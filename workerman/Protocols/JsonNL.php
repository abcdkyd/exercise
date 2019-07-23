<?php
namespace Protocols;

class JsonNL
{
    public static function input($buffer)
    {
        $pos = strpos($buffer, "\n");

        if ($pos === false) {
            return 0;
        }

        return $pos + 1;
    }

    public static function encode($buffer)
    {
        return json_encode($buffer) . "\n";
    }

    public static function decode($buffer)
    {
        return json_decode(trim($buffer), true) . "\n";
    }
}
