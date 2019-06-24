<?php

$data = [
    array('id'=>1  , 'address'=>'安徽' , 'parent_id' => 0, [2,'sd']),
    array('id'=>2  , 'address'=>'江苏' , 'parent_id' => 0),
    array('id'=>3  , 'address'=>'合肥' , 'parent_id' => 1),
    array('id'=>4  , 'address'=>'庐阳区' , 'parent_id' => 3),
    array('id'=>5  , 'address'=>'大杨镇' , 'parent_id' => 4),
    array('id'=>6  , 'address'=>'南京' , 'parent_id' => 2),
    array('id'=>7  , 'address'=>'玄武区' , 'parent_id' => 6),
    array('id'=>8  , 'address'=>'梅园新村街道', 'parent_id' => 7),
    array('id'=>9  , 'address'=>'上海' , 'parent_id' => 0),
    array('id'=>10 , 'address'=>'黄浦区' , 'parent_id' => 9),
    array('id'=>11 , 'address'=>'外滩' , 'parent_id' => 10),
    array('id'=>12 , 'address'=>'安庆' , 'parent_id' => 1)
];

function tree1($data, $pid = 0) {
    $result = [];
    foreach ($data as $val) {
        if ($pid == $val['parent_id']) {
            $child = tree1($data, $val['id']);
            if (!empty($child)) $val['child'] = $child;
            $result[] = $val;
        }
    }
    return $result;
}

function tree2($data) {
    $tmp_data = [];
    foreach ($data as $d) $tmp_data[$d['id']] = $d;
    foreach ($tmp_data as $tmp_d) $tmp_data[$tmp_d['parent_id']]['child'][] = &$tmp_data[$tmp_d['id']];
    return $tmp_data[0]['child'] ?? [];
}

// echo '<pre>';
// print_r(tree1($data));
// echo '</pre>';

// $pre = preg_match('/^[a-zA-Z0-9]+([-_.]*[a-zA-Z0-9]+)*@[a-zA-Z0-9]+([-_.]*[a-zA-Z0-9]+)*(\.[a-zA-Z0-9]+)+$/', $mail);
// print_r($pre);

function MulitarraytoSingle($array){
    $temp=array();
    if(is_array($array)){
     foreach ($array as $key=>$value )
     {
      if(is_array($value)){
        $temp[$key] = MulitarraytoSingle($value);
      }
      else{
       $temp[$key]= is_numeric($value) ? 0 : $value;
      }
     }
    }
    return $temp;
  }

  echo '<pre>';
print_r(MulitarraytoSingle($data));
echo '</pre>';
