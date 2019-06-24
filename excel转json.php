<?php
require __DIR__.'/vendor/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

// 转换宠物资料为json
function exchange1()
{
    $file_path = '/Users/vin/Desktop/宠物.xlsx';

    $reader = ReaderEntityFactory::createXLSXReader();

    $reader->setShouldFormatDates(true);
    $reader->open($file_path);

    $rows = [];

    foreach ($reader->getSheetIterator() as $sheet) {
        if ($sheet->isActive()) {
            foreach($sheet->getRowIterator() as $key => $val) {
                if ($key == 1) continue;
                $rows[] = $val->toArray();
            }
            break;
        }
    }

    $series_col = array_values(array_filter(array_unique(array_column($rows, 0))));

    $json = [];

    foreach ($series_col as $s) {

        $tmp_series = [];
        $tmp_type = [];

        foreach ($rows as $r_key => $r_val) {
            if ($r_val[0] == $s) {

                list($ground, $water, $fire, $wind) = array_map(function($value) {
                    return intval(explode('-', $value)[1]);
                }, preg_split('/,|，/', $r_val[3]));

                $tmp_type[] = [
                    'name' => $r_val[1],
                    'color' => $r_val[2],
                    'attribute' => compact('ground', 'water', 'fire', 'wind'),
                    'payment' => $r_val[4],
                    'key' => $r_val[5],
                    'desc' => $r_val[6],
                    'img_url' => $r_val[7],
                ];
            }
        }

        $tmp_series['series'] = $s;
        $tmp_series['type'] = $tmp_type;

        array_push($json, $tmp_series);
    }

    $explode_file_path = '/Users/vin/Desktop/pet.json';

    if (file_put_contents($explode_file_path, json_encode($json, 256))) {
        echo '转换成功';
        return;
    }

    echo '转换失败';
    return;
}


exchange1();
