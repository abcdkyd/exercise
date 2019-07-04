<?php
require __DIR__.'/vendor/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// 转换宠物资料为json
function exchange1()
{
    $file_path = '/Users/vin/Desktop/宠物.xlsx';

    $reader = ReaderEntityFactory::createXLSXReader();

    $reader->setShouldFormatDates(true);
    $reader->open($file_path);

    $tmp_data = [];

    foreach ($reader->getSheetIterator() as $sheet) {
        if ($sheet->isActive()) {
            foreach($sheet->getRowIterator() as $key => $val) {
                if ($key == 1) continue;
                $tmp_val = $val->toArray();
                array_shift($tmp_val);
                $tmp_data[] = $tmp_val;
            }
            break;
        }
    }

    $series_col = array_values(array_filter(array_unique(array_column($tmp_data, 0))));

    $list = [];
    $data = [];

    foreach ($series_col as $s) {

        $tmp_series = [];
        $tmp_type = [];

        foreach ($tmp_data as $r_key => $r_val) {
            if ($r_val[0] == $s) {

                list($ground, $water, $fire, $wind) = array_map(function($value) {
                    return intval(preg_replace('/[^\d]/', '', $value));
                }, preg_split('/,|，/', $r_val[4]));

                $tmp_type[] = [
                    'name' => $r_val[1],
                    'color' => $r_val[2],
                    'attribute' => compact('ground', 'water', 'fire', 'wind'),
                    'payment' => $r_val[5],
                    'key' => $r_val[6],
                    'desc' => $r_val[7],
                    'pet_img' => $r_val[8],
                    'pet_icon' => $r_val[9],
                    'pet_icon_unact' => $r_val[10],
                    'pet_color_icon' => $r_val[11],
                ];
            }
        }

        $tmp_series['series'] = $s;
        $tmp_series['child'] = $tmp_type;

        array_push($list, [
            'series' => $s,
            'pet_icon' => $tmp_series['child'][0]['pet_icon'],
            'pet_icon_unact' => $tmp_series['child'][0]['pet_icon_unact'],
        ]);
        array_push($data, $tmp_series);
    }

    $json = ['list' => $list, 'data' => $data];
    $explode_file_path = '/Users/vin/Desktop/pet.json';

    if (file_put_contents($explode_file_path, json_encode($json, 256))) {
        echo '转换成功';
        return;
    }

    echo '转换失败';
    return;
}

function exchange2()
{
    $file_path = '/Users/vin/Desktop/宠物.xlsx';
    $extension = IOFactory::identify($file_path);
    $reader = IOFactory::createReader($extension);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($file_path);

    $workSheet = $spreadsheet->getSheetByName('Sheet1');

    $highestRow = $workSheet->getHighestRow();
    $highestCol = $workSheet->getHighestColumn();

    // 把列的索引字母转为数字 从1开始 这里返回的是最大列的索引
    $highestColumnIndex = Coordinate::columnIndexFromString($highestCol);

    $tmp_data = [];

    for($row = 2; $row <= $highestRow; $row++) {
        $tmp_row = [];
        for($col = 1; $col <= $highestColumnIndex; $col++) {
            array_push($tmp_row, $workSheet->getCellByColumnAndRow($col, $row)->getValue());
        }
        array_push($tmp_data, $tmp_row);

    }

    $series_col = array_values(array_filter(array_unique(array_column($tmp_data, 0))));

    $list = [];
    $data = [];

    foreach ($series_col as $s) {

        $tmp_series = [];
        $tmp_type = [];

        foreach ($tmp_data as $r_key => $r_val) {
            if ($r_val[0] == $s) {

                list($ground, $water, $fire, $wind) = array_map(function($value) {
                    return intval(preg_replace('/[^\d]/', '', $value));
                }, preg_split('/,|，/', $r_val[4]));

                $tmp_type[] = [
                    'name' => $r_val[1],
                    'color' => $r_val[2],
                    'attribute' => compact('ground', 'water', 'fire', 'wind'),
                    'payment' => $r_val[5],
                    'key' => $r_val[6],
                    'desc' => $r_val[7],
                    'pet_img' => $r_val[8],
                    'pet_icon' => $r_val[9],
                    'pet_icon_unact' => $r_val[10],
                    'pet_color_icon' => $r_val[11],
                ];
            }
        }

        $tmp_series['series'] = $s;
        $tmp_series['child'] = $tmp_type;

        array_push($list, [
            'series' => $s,
            'pet_icon' => $tmp_series['child'][0]['pet_icon'],
            'pet_icon_unact' => $tmp_series['child'][0]['pet_icon_unact'],
        ]);
        array_push($data, $tmp_series);
    }

    $json = ['list' => $list, 'data' => $data];
    $explode_file_path = '/Users/vin/Desktop/pet2.json';

    if (file_put_contents($explode_file_path, json_encode($json, 256))) {
        echo '转换成功';
        return;
    }

    echo '转换失败';
    return;
}

show_func([
   ['exchange1', 'excel转json(box/spout库)-不支持xls'],
   ['exchange2', 'excel转json(PhpSpreadsheet库)'],
]);

