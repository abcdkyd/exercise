<?php
/**
 * Created by PhpStorm.
 * User: vin
 * Date: 17/3/8
 * Time: 15:56
 */

$myDir = "./dir1";

function getFile($myDir) {
    $handle = opendir($myDir);
    $myFile = array();
    while (($file = readdir($handle)) !== false) {
        if($file !== '.' && $file !== '..') {
            if(is_dir($myDir.DIRECTORY_SEPARATOR.$file)) {
                $myFile[] = [
                    'name' => $file,
                    'type' => 'dir',
                    'child' => getFile($myDir.DIRECTORY_SEPARATOR.$file)
                ];

            } else {
                $myFile[] = [
                    'name' => $file,
                    'type' => 'file'
                ];
            }
        }
    }

    return $myFile;
}
//
echo '<pre>';
print_r(getFile($myDir));
echo '</pre>';
//die;

$files = getFile($myDir);

function showDir($arr, $sep = '----', $level = 0) {

    foreach($arr as $val) {
        if($val['type'] == 'file') {
            echo str_repeat($sep,$level).$val['name']."<br/>";
        } else if($val['type'] == 'dir') {
            echo str_repeat($sep,$level).$val['name']."<br/>";
            showDir($val['child'],$sep, $level+1);
        }
    }

}

showDir($files);


