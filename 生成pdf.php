<?php
/**
 * Created by PhpStorm.
 * User: vin
 * Date: 2019/4/9
 * Time: 下午3:52
 */

// use Knp\Snappy\Pdf;
// use ZipArchive;

function exportFarmerCertificate()
{
    $user = \Auth::guard('api')->user();

    if (!$user) {
        return $this->withCode(401)->withCustom(['message' => 'unauthorized']);
    }

    $order_id = $this->request->input('order_id', '');
    if (empty($order_id)) return $this->withCode(422)->withCustom(['message' => '缺少订单id']);

    $orderNo = LoansOrder::query()->where('id', $order_id)->value('order_no');

    $file_name = $orderNo . '.pdf';
    $bucket = 'pht-userprofile';
    $bucket_uri = 'farmer-cer';
    $object = $bucket_uri . '/' . $file_name;
    $upload_path = 'uploads/product/loans/farmerCer/';

    if (app('AliyunOSS')->doesObjectExist($bucket, $object)) {
        return $this->withCode(200)
            ->withCustom(['file_path' => url('api/product/third_party/government/farmer_certificate_pdf/' . $orderNo)]);
    }

    $cerBuilder = GovernmentFarmerCertificate::query()
        ->select([
            'members.realname as member_name',
            'sdm_government_organizations.name as organization_name',
            'sdm_government_farmer_certificate.created_at'
        ])
        ->leftJoin('members', 'sdm_government_farmer_certificate.member_id', '=', 'members.id')
        ->leftJoin('member_sdm_government_meta', 'sdm_government_farmer_certificate.created_by'
            , '=', 'member_sdm_government_meta.member_id')
        ->leftJoin('sdm_government_organizations', 'member_sdm_government_meta.organization_id'
            , '=', 'sdm_government_organizations.id')
        ->where('order_id', $order_id)->first();

    if (!$cerBuilder) {
        return $this->withCode(403)->withCustom(['message' => '农户证明信息有误']);
    }

    $cerData = $cerBuilder->toArray();

    $htmlFile = app_path('modules/product/resources/dataFiles/farmer-cel.html');
    $htmlContent = file_get_contents($htmlFile);

    $searchDataArr = [
        '{-cer_content-}',
        '{-cer_content_suffix-}',
        '{-cer_organization-}',
        '{-cer_date-}',
    ];

    $replaceDataArr = [
        'cer_content' => "目前尚未发现我村村民[RED]{$cerData['member_name']}[/RED]有犯罪记录或不良嗜好，日常信誉良好。",
        'cer_content_suffix' => '此证明仅用作银行参考。',
        'cer_organization' => $cerData['organization_name'],
        'cer_date' => date('Y年m月d日', strtotime($cerData['created_at']))
    ];

    $content = str_replace($searchDataArr, $replaceDataArr, $htmlContent);

    try {
        if (!is_dir(static_path($upload_path))) {
            mkdir(static_path($upload_path), 0755);
        }
        $file_path = static_path($upload_path . $file_name);

        if (!file_exists($file_path)) {
//                $snappy = new Pdf('/usr/local/bin/wkhtmltopdf.sh');     // 线上
            $snappy = new Pdf('/usr/local/bin/wkhtmltopdf', [], ['LANG' => 'zh_CN.utf8']);      // 本地
            $snappy->generateFromHtml($content, $file_path);
        }

        $data = !config('AliyunOSS.debug')
            ? app('AliyunOSS')->uploadFile($bucket, $bucket_uri . '/' . $file_name, $file_path)
            : [];

        if (!isset($data['info'])) {
            return $this->withCode(422)->withCustom(['message' => '证明上传阿里云失败']);
        }

        unlink($file_path);

        return $this->withCode(200)
            ->withCustom(['file_path' => url('api/product/third_party/government/farmer_certificate_pdf/' . $orderNo)]);

    } catch (\Exception $e) {
        return $this->withCode(500)
            ->withCustom(['message' => '系统异常']);
    }
}

function getFarmerCertificatePDF()
{
    $user = \Auth::guard('api')->user();

    if (!$user) {
        return $this->withCode(401)->withCustom(['message' => 'unauthorized']);
    }

    $order_no = $this->request->route('order_no', '');

    if (empty($order_no)) {
        return $this->withCode(422)
            ->withCustom(['message' => '缺少订单号']);
    }

    $file_name = $order_no . '.pdf';
    $bucket = 'pht-userprofile';
    $bucket_uri = 'farmer-cer';
    $object = $bucket_uri . '/' . $file_name;

    if (!app('AliyunOSS')->doesObjectExist($bucket, $object)) {
        return $this->withCode(403)
            ->withCustom(['message' => '系统异常[NOTFOUND]']);
    }

    $file_path = app('AliyunOSS')->signUrl($bucket, $object, 300, 'GET');

    try {
        header('Content-Description: ' . $order_no);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
//            header('Content-Length: ' . filesize($file_path));
        ob_clean();
        flush();
        readfile($file_path);
        exit;
    } catch (\Exception $e) {
        dd($e->getTrace());
        return $this->withCode(500)
            ->withCustom(['message' => '系统异常[ERROR]']);
    }
}

function exportAllFarmerCertificatePDF()
{
    $this->zip = new ZipArchive();

    $user = \Auth::guard('api')->user();

    if (!$user) {
        return $this->withCode(401)
            ->withCustom(['message' => 'unauthorized']);
    }

    $group_id = MemberGroupRelation::query()
        ->where('member_id', $user->id)
        ->value('group_id');

    if ($group_id != 1) {
        return $this->withCode(422)
            ->withCustom(['message' => '该操作需管理员权限']);
    }

    $select_list = $this->request->input('select_list', '[]');

    $select_list = json_decode($select_list, true);

    $file_mark = 'PHSX';

    $query = LoansOrder::query()
        ->select(['user_id', 'order_no',])
        ->where('p_id', 17);

    if (is_array($select_list) && !empty($select_list)) {
        $query->whereIn('id', $select_list);
        $file_mark = strtolower($file_mark) . '_';
    }

    $orders = $query->get()->toArray();

    if (!file_exists(app_path('files/product/loans/microcredit/zip/'))) {
        mkdir(app_path('files/product/loans/microcredit/zip/'));
    }

    $file_folder = $file_mark . date('Ymd', time());
    $file_dir = app_path('files/product/loans/microcredit/');
    $zip_path = app_path('files/product/loans/microcredit/zip/' . $file_folder . '.zip');

    if (empty($orders)) {
        return $this->withCode(500)
            ->withCustom(['message' => '导出失败，没有普惠授信相关资料！']);
    }

    $start_time = time();

    if (true === ($this->zip->open($zip_path, ZipArchive::OVERWRITE | ZipArchive::CREATE))) {

        $this->zip->addFile($file_dir . 'README.md', './README.md');

        foreach ($orders as $info) {

            $user_id = array_get($info, 'user_id');
            $order_no = array_get($info, 'order_no');
            $file_name = $order_no . '.pdf';
            $file_path = $file_dir . $file_name;

            if (!file_exists($file_path)) {
                ++$this->error_num;
                array_push($this->error_log, 'emptyError：用户（' . $user_id .'）普惠授信信息为空');
                continue;
            }

            try {
                if (true === $this->zip->addFile($file_path, $file_name)) {
                    ++$this->success_num;
                } else {
                    ++$this->error_num;
                    array_push($this->error_log, 'zipError：用户（' . $user_id . '）普惠授信信息添加压缩失败');
                    continue;
                }
            } catch (\Exception $e) {
                ++$this->error_num;
                array_push($this->error_log, 'zipError：用户（' . $user_id .'）普惠授信信息添加压缩失败');
                continue;
            }
        }

        $this->zip->close();

        // 后台添加导出日志
        $log_data = [
            'success' => $this->getSuccessNum(),
            'error' => $this->getErrorNum(),
            'error_log' => $this->error_log,
        ];
        Log::info('=========== 普惠授信信息批量导出日志 ===========');
        Log::info('普惠授信信息批量导出结果：', $log_data);
        $end_time = time();
        Log::info('用时：' . ($end_time - $start_time) . 's');

        // 输出下载
        try {
            header("Cache-Control: max-age=0");
            header("Content-Description: File Transfer");
            header('Content-disposition: attachment; filename=' . $file_folder . '.zip'); // 文件名
            header("Content-Type: application/zip"); // zip格式的
            header('Expires: 0');
            header('content-description: ' . $file_folder);
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header("Content-Transfer-Encoding: binary"); // 告诉浏览器，这是二进制文件
            header('Content-Length: ' . filesize($zip_path)); // 告诉浏览器，文件大小
            ob_clean();
            flush();
            @readfile($zip_path);//输出文件;
            exit;
        } catch (\Exception $e) {
            return $this->withCode(422)
                ->withCustom(['message' => '系统异常[NOTFOUND]']);
        }
    }

    return $this->withCode(500)
        ->withCustom(['message' => '导出失败！']);
}

// js
$js1 = <<<js1
microcreditExport() {
    const self = this;
    const select_list = fetchData(LOCALSTORAGE_KEY);
    let lists = [];
    self.microcreditExportLoading = true;
    select_list.forEach(item => {
        if (Object.prototype.toString.call(item) == '[object Array]') {
            lists = lists.concat(item);
        }
    });

    self.$http.get(`${window.api}/product/loans/microcredit/info/export`, {
        responseType: 'blob',
        params: {
            select_list: JSON.stringify(lists),
        }
    }).then(file => {
        console.log(file);
        self.save(file.data, file.headers['content-description'] + '.zip', 'application/zip');
        window.localStorage.removeItem(LOCALSTORAGE_KEY);
        self.paginator(self.current_page);
        self.$message.info('导出成功');
        self.microcreditExportLoading = false;
    }).catch(error => {
        self.$message.error('导出失败[BLOB]');
    });
},
js1;

$js2 = <<<js2
save(code, name, type = 'application/pdf') {
    const URL = window.URL || window.webkitURL || window.mozURL || window.msURL
    navigator.saveBlob = navigator.saveBlob || navigator.msSaveBlob || navigator.mozSaveBlob || navigator.webkitSaveBlob
    window.saveAs = window.saveAs || window.webkitSaveAs || window.mozSaveAs || window.msSaveAs
    let blob = new Blob([code], { type: type })
    if (window.saveAs) {
        window.saveAs(blob, name)
    } else if (navigator.saveBlob) {
        navigator.saveBlob(blob, name)
    } else {
        let url = URL.createObjectURL(blob)
        let link = window.document.createElement('a')
        link.setAttribute('href', url)
        link.setAttribute('download', name)
        let event = window.document.createEvent('MouseEvents')
        event.initMouseEvent('click', true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null)
        link.dispatchEvent(event)
    }
},
js2;

$js3 = <<<js3
exportInfo(order_id) {
    const self = this;
    self.$http.get(`${window.api}/product/loans/microcredit/info/export/${order_id}`).then(response => {
        self.$http.get(response.data.file_path, {
            responseType: 'blob'
        }).then(file => {
            self.save(file.data, file.headers['content-description'] + '.pdf');
        }).catch(error => {
            self.$message.error('导出失败[BLOB]');
        });
        self.$message.info('导出成功');
    }).catch(error => {
        self.$message.error('导出失败[API]');
    });
},
js3;


