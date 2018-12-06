<?php
/**
 * Created by PhpStorm.
 * User: vin
 * Date: 2018/12/6
 * Time: 下午3:56
 */

$arr = ['41', '411101000000', '410200000000', '410201000000', '411301000000', '410401000000', '410601000000'];
$arr1 = $arr[0];

$arr_tmp = array_map(function($arr_item) {
    return substr($arr_item, 0, 4);
}, $arr);

$arr_count = array_count_values($arr_tmp);
arsort($arr_count);
$arr_count_max = reset($arr_count);
$arr_count_max_key = strval(key($arr_count));

if ($arr_count_max >=2) {
    $arr = array_filter($arr, function($a) use($arr_count_max_key) {
        return strpos($a, $arr_count_max_key) === 0;
    });
    array_unshift($arr, $arr1);
}

var_dump($arr);