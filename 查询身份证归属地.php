<?php
/**
 * Created by PhpStorm.
 * User: vin
 * Date: 2018/12/4
 * Time: 上午9:58
 */


// 调用库的github地址：https://github.com/douyasi/identity-card

$idcard_no = '412328196803181254';
$ID = new \Douyasi\IdentityCard\ID();
$result = $ID->getArea($idcard_no);


if (isset($result['status']) && $result['status'] == true) {

    $member_area_ = [
        $result['province'],
        $result['city'],
        $result['county']
    ];

    if ($result['using'] == 0 || empty($member_area_[1]) || empty($member_area_[2])) {
        if (isset($member_area_[0]) && $member_area_[0] == '内蒙古') {
            $change_arr = [
                '内蒙古' => '内蒙古自治区'
            ];

            if (isset($change_arr[$member_area_[0]])) $member_area_[0] = $change_arr[$member_area_[0]];
        }

        if (isset($member_area_[1])) {
            if (empty($member_area_[1])) {

                if (!empty($member_area_[2]) && mb_strpos($member_area_[2], '县') !== false) {
                    $member_area_[1] = '县';
                } else if (!empty($member_area_[2]) && mb_strpos($member_area_[2], '区') !== false) {
                    $member_area_[1] = '市辖区';
                }
            }

            if (mb_strpos($member_area_[1], '地区') !== false) {
                $member_area_1 = mb_ereg_replace('地区', '', $member_area_[1]);
                $member_area_2 = mb_ereg_replace('市', '', $member_area_[2]);

                $member_area_[1] = $member_area_1 . '市';
                if ($member_area_1 == $member_area_2) $member_area_[2] = '市辖区';
            }

            $change_arr = [
                '周口地区' => '周口市',
                '保山地区' => '保山市'
            ];
            if (isset($change_arr[$member_area_[1]])) $member_area_[1] = $change_arr[$member_area_[1]];
        }

        if (isset($member_area_[2])) {

            if (empty($member_area_[2])) $member_area_[2] = '市辖区';

            // 第三级的市降区
            if (!in_array($member_area_[2], ['吴川市'])) {
                $member_area_[2] = mb_ereg_replace('市$', '区', $member_area_[2]);
            }

            // 已变更的地名
            $change_arr = [
                '望城县' => '望城区',
                '保山市' => '市辖区',
                '郾城县' => '郾城区',
                '项城区' => '项城市',
                '济源区' => '济源市',
                '长葛县' => '长葛市',
                '获鹿县' => '鹿泉区',
                '栾城县' => '栾城区',
                '丰润县' => '丰润区',
                '丰南县' => '丰南区',
                '唐海县' => '曹妃甸区',
                '抚宁县' => '抚宁区',
                '满城县' => '满城区',
                '清苑县' => '清苑区',
                '徐水县' => '徐水区',
                '冀州区' => '冀州市',
                '南宫区' => '南宫市',
                '辛集区' => '辛集市',
                '新乐县' => '新乐市',
                '新密区' => '新密市',
                '开封县' => '祥符区',
                '偃师县' => '偃师市',
                '舞钢区' => '舞钢市',
                '沁阳县' => '沁阳市',
                '灵宝县' => '灵宝市',
                '永城县' => '永城市',
                '项城县' => '项城市',
                '邓州区' => '邓州市',
                '信阳县' => '平桥区',
                '荥阳县' => '荥阳市',
                '新郑县' => '新郑市',
                '登封县' => '登封市',
                '临汝县' => '汝州市',
                '济源县' => '济源市',
                '许昌县' => '建安区',
                '周口地区' => '市辖区',
            ];

            if (isset($change_arr[$member_area_[2]])) $member_area_[2] = $change_arr[$member_area_[2]];

            // 归属更改
            if ($member_area_[2] == '济源市') $member_area_[1] = '省直辖县级行政区划';
        }
    }
}

return $member_area_;