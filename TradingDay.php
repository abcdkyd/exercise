<?php

function is_weekend($date)
{
    //获取当前日期星期几
    $date_w = date('w', strtotime($date));
    if ($date_w == 0 || $date_w == 6) {
        return true;
    }
    return false;
}

function is_holiday($date)
{
    $holiday_arr = [
        '2017-01-01', '2017-05-01', '2017-10-01', '2017-10-02', '2017-10-03', '2017-10-04', '2017-10-05', '2017-10-06', '2017-10-09'
    ];

    if (in_array($date, $holiday_arr)) {
        return true;
    }

    return false;
}

function getNextTradingDay($date = null)
{
    if(!$date || !isset($date) || empty($date)) {
        $date = date('Y-m-d', time());
    }

    $next_day = date('Y-m-d', strtotime($date . '+1 day'));

    if(is_holiday($next_day)) {
        $next_day = getNextTradingDay($next_day);
    }

    if(is_weekend($next_day)) {
        $next_day = getNextTradingDay($next_day);
    }

    return $next_day;
}

function result($date)
{
    $result = getNextTradingDay($date);
    echo $result . ' ' . date("l", strtotime($result));

}

result('2017-10-04');