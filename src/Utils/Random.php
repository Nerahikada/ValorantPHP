<?php

namespace Nerahikada\ValorantPHP\Utils;

class Random
{
    public static function string(int $length): string
    {
        $str = "";
        for ($i = 0; $i < $length; ++$i) {
            $str .= "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"[random_int(0, 61)];
        }
        return $str;
    }
}