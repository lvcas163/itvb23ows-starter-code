<?php

namespace Lucas\Hive;

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Board;

class Hive
{
    private Board $board;
    private int $gameId;

    public function __construct(Board $board, int $gameId)
    {
        $this->board = $board;
        $this->gameId = $gameId;
    }

    public static function getState()
    {
        return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
    }

    public static function setState($state)
    {
        list($a, $b, $c) = unserialize($state);
        $_SESSION['hand'] = $a;
        $_SESSION['board'] = $b;
        $_SESSION['player'] = $c;
    }

    public function setMove(string $from, string $to)
    {

    }

}