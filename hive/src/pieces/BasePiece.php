<?php

namespace Lucas\Hive\pieces;

use Lucas\Hive\Board;
use Lucas\Hive\Hive;
use Lucas\Hive\HiveException;

abstract class BasePiece
{
    protected Hive $hive;

    public function __construct(Hive $hive)
    {
        $this->hive = $hive;
    }

    abstract function validateMove(string $from, string $to): bool;

    public static function fromType(string $type, Hive $hive)
    {
        return match ($type) {
            'G' => new GrassHopperPiece($hive),
            'Q' => new QueenBeePiece($hive),
            'B' => new BeetlePiece($hive),
            'S' => new SpiderPiece($hive),
            'A' => new SoldierAntPiece($hive),
            default => throw new HiveException('Type not in base pieces'),
        };
    }
}