<?php

namespace Lucas\Hive\pieces;

use Lucas\Hive\Board;
use Lucas\Hive\HiveException;

class SoldierAntPiece extends BasePiece
{
    public function validateMove(string $from, string $to): bool
    {
        if (!$this->hive->getBoard()->emptyTile($to)) {
            throw new HiveException('Tile not empty');
        }
        if (!$this->validateSlide($from, $to)) {
            throw new HiveException('Tile must slide');
        }
        return true;
    }

    public function validateSlide($from, $to): bool
    {
        $board = $this->hive->getBoard();
        if (!$board->hasNeighBour($to))
        {
            return false;
        }

        $visited = [];
        $tiles = [$from];

        while (!empty($tiles)) {
            $currentTile = array_shift($tiles);

            if (!in_array($currentTile, $visited)) {
                $visited[] = $currentTile;
            }

            $b = explode(',', $currentTile);

            foreach (Board::$OFFSETS as $pq) {
                $p = $b[0] + $pq[0];
                $q = $b[1] + $pq[1];

                $position = $p . "," . $q;

                if (
                    !in_array($position, $visited) &&
                    $board->emptyTile($position) &&
                    $board->hasNeighbour($position)
                ) {
                    if ($position == $to) {
                        return true;
                    }
                    $tiles[] = $position;
                }
            }
        }

        return false;
    }
}