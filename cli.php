<?php
//$file = fopen('/tmp/test.txt', 'w+');
//if(flock($file, LOCK_EX)) {
//
//    fwrite($file, 'write something');
//
//    flock($file, LOCK_UN);
//
//} else {
//    return 'you can not lock the file';
//}
//
fclose($file);
substr(strrchr($file, '.'), 1);
substr($file, strpos($file, '.')+1);
end(explode('.', $file));

$info = pathinfo($file); return $info['extension'];
pathinfo($file, PATHINFO_EXTENSION);

