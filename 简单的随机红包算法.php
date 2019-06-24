<?php

function getRedPackage($amount, $count)
{
    // 用了随机的钱先减去每个的1分
    $rand_money = ($amount * 100) - $count;

    if ($rand_money < 0 || $count <= 0) return [];

    $cur_money = $rand_money;
    $send_money = 0;
    $count_left = $count;

    $result = [];

    for ($i = 1; $i <= $count; $i++) {

        // 判断是否最后一个
        if ($i == $count) {
            $item = $cur_money;
        } elseif($cur_money == 0) {
            $item = 0;
        } else {
            // 期望值是平均值的2倍
            $expect = floor($cur_money / $count_left) * 2;

            // 红包金额在1分到期望值之间
            $item = rand(1, $expect);
        }

        // 插入数组
        $result[$i - 1] = ($item + 1) / 100;

        // 计算剩余金额
        $cur_money -= $item;

        // 剩余人数-1
        --$count_left;

        // 记录发送金额
        $send_money += $item + 1;
    }

    return $send_money == round(array_sum($result), 2) * 100 ? $result : [];
}

echo '<pre>';
var_dump(getRedPackage(300, 20));
echo '</pre>';
