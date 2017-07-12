<?php

function dd() {

    echo '<pre>';
    $args_arr = func_get_args();
    foreach($args_arr as $arg) {
        var_dump($arg);
        echo "\n";
    }
    echo '</pre>';
}

$file = 'http://saishi.zgzcw.com/soccer/league/31/2016-2017/ssb';

$data = file_get_contents($file);

$data = preg_match('/<div class=\"right\">(.*)<\/div>/ism',$data, $arr);

$html = preg_replace("/[\t\n\r]+/","",$arr[0]);

preg_match_all('/<tbody>([\s\S]*?)<\/tbody>/',$html,$tbody_arr);

//$test_str = $tbody_arr[0][1];

$result = [];

$call_back = function($v, $k) use (&$result) {
    preg_match_all('/<tr>[\s\S]*?<\/tr>/',$v,$tr_arr);

    $result_item = [];
    //dd($tr_arr[0]);die;

    foreach($tr_arr[0] as $v) {

        //|(?<=>)[^>]*?(?=<\/a>)
        //(?<=<td>)[^>]*?(?=<\/td>)
        preg_match_all('/(?<=\>)[^>]*?(?=<\/a>)|(?<=<td>)[^>]*?(?=<\/td>)/',$v,$td_arr);

        $td_arr = $td_arr[0];

//        if(!isset($td_arr[7])) {
//            print_r($td_arr);die;
//        }

        $result_item[] = [
            'id' => $td_arr[0],
            'team_name' => $td_arr[1],
            'player_name' => $td_arr[2],
            'country' => $td_arr[3],
            'goals' => $td_arr[4],
            'penalty' => $td_arr[5],
            'home_goals' => $td_arr[6],
            'away_goals' => $td_arr[7]
        ];

    }
    $result[] = $result_item;
};

array_walk($tbody_arr[0], $call_back);

dd($result);


