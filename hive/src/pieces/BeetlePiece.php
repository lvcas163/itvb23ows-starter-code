<?php

namespace Lucas\Hive\pieces;

use Lucas\Hive\HiveException;

class BeetlePiece extends BasePiece
{
    public function validateMove(string $from, string $to): bool
    {
        if (!$this->board->slide($from, $to)) {
            throw new HiveException('Tile must slide');
        }
        return true;
    }
}