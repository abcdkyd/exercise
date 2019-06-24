<?php

if (!function_exists('dd')) {
    function dd() {

        echo '<pre>';
        $args_arr = func_get_args();
        foreach($args_arr as $arg) {
            var_dump($arg);
            echo "\n";
        }
        echo '</pre>';
        exit(0);
    }
}
