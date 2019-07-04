<?php

if (!function_exists('dd')) {
    function dd()
    {
        echo '<pre>';
        $args_arr = func_get_args();
        foreach ($args_arr as $arg) {
            var_dump($arg);
            echo "\n";
        }
        echo '</pre>';
        exit(0);
    }
}

if (!function_exists('dump')) {
    function dump()
    {
        echo '<pre>';
        $args_arr = func_get_args();
        foreach ($args_arr as $arg) {
            var_dump($arg);
            echo "\n";
        }
        echo '</pre>';
    }
}

if (!function_exists('show_func')) {
    function show_func($func)
    {
        $func_name = array_column($func, 0);

        if (isset($_GET['func'])) {
            $func_index = array_search($_GET['func'], $func_name);
            if ($func_index !== false) {
                echo '<p><a href="' . $_SERVER['PHP_SELF'] . '">返回</a></p>';
                call_user_func_array($_GET['func'], $func[$func_index][2] ?? []);
            }
        } else {
            foreach ($func as $item) {
                echo '<p><a href="?func=' . $item[0] . '">' . $item[1] . '</a></p>';
            }
        }
    }
}
