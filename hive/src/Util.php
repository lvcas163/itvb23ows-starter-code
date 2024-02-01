<?php

namespace Lucas\Hive;

class Util
{
    public static function setState($state): void
    {
        list($a, $b, $c) = unserialize($state);
        $_SESSION['hand'] = $a;
        $_SESSION['board'] = $b;
        $_SESSION['player'] = $c;
    }
}
