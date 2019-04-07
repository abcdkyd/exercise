<?php

$file = fopen('/Users/vin/Desktop/20171125.csv','r');
while ($data = fgetcsv($file)) { //每次读取CSV里面的一行内容
    //print_r($data); //此为一个数组，要获得每一个数据，访问数组下标即可
    $goods_list[] = $data;
}
//print_r($goods_list);
//$str = 'RFHa2kXIiGo5pGZE+wR5XPnNu6dLRMyfW5y5xW++YtFXRYQ6iaBrYb4+lmra6RFJ';
//echo openssl_decrypt($str, 'AES-256-ECB', '');

$str = '"手机号", "姓名", "注册日期", "身份证地址", "贷款信息地址"' . "\n";
foreach($goods_list as &$val) {
    $val[4] = openssl_decrypt($val[4], 'AES-256-ECB', '');
    $values = array_values($val);
    $column = '"' . implode('","', $values) . '"';
    $str .= $column . "\n";
}

file_put_contents('20171125212124.csv', $str);

fclose($file);

var_dump($goods_list);