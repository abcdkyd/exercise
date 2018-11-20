<?php

class cart {

    const PRICE_MILK = 3.50;
    const PRICE_APPLE = 5.50;
    const PRICE_EGG = 1.0;

    private $cart_list = array();

    function add($list, $quantity) {
        $this -> cart_list[$list] = $quantity;
    }

    function total($tax) {

        $total = 0.00;

        $callback = function($num, $p) use ($tax, &$total) {
            $price = constant(__CLASS__.'::PRICE_'.strtoupper($p));
            $total += $num * $price * ($tax + 1.0) ;
        };

        array_walk($this -> cart_list, $callback);
        return round($total, 2);
    }
}

$cart = new cart();
$cart -> add('milk', 1);
$cart -> add('apple', 1);
$cart -> add('egg', 1);

print $cart -> total(0.05);

