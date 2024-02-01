<?php

namespace Lucas\Hive\pieces;

use Exception;
use Lucas\Hive\Board;
use Lucas\Hive\HiveException;

abstract class BasePiece
{
    protected Board $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    abstract function validateMove(string $from, string $to): bool;

    public static function fromType(string $type, Board $board)
    {
        return match ($type) {
            'G' => new GrassHopperPiece($board),
            'Q' => new QueenBeePiece($board),
            'B' => new BeetlePiece($board),
            default => throw new HiveException('Type not in base pieces'),
        };
    }
}