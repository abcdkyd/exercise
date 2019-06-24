<?php
/**
 * Created by PhpStorm.
 * User: vin
 * Date: 17/3/7
 * Time: 13:33
 */

$url = 'name=jxie&account=abc&psw=111&nick=vin';

$url_arr = explode('&', $url);
$arr = [];

foreach($url_arr as $val) {
    $k_v = explode('=', $val);
    $arr[$k_v[0]] = $k_v[1];
}

dd($arr,$url_arr, 'abc');
