<?php

namespace Lucas\Hive\pieces;

use Lucas\Hive\Board;
use Lucas\Hive\HiveException;

class SpiderPiece extends BasePiece
{
    public function validateMove(string $from, string $to): bool
    {
        if (!$this->hive->getBoard()->emptyTile($to)) {
            throw new HiveException('Tile not empty');
        }
        if (!$this->isThreeSteps($from, $to)) {
            throw new HiveException('Move needs to be exactly three steps');
        }
        if (!$this->validateSlide($from, $to)) {
            throw new HiveException('Tile must slide');
        }
        return true;
    }

    public function validateSlide($from, $to): bool
    {
        $board = $this->hive->getBoard();

        $visited = [];
        $tiles = [$from];
        $tiles[] = null;
        $tileBefore = null;

        if (!$board->hasNeighBour($to)) {
            return false;
        }

        while (!empty($tiles)) {
            $currentTile = array_shift($tiles);

            if ($currentTile == null) {
                $tiles[] = null;

                if (reset($tiles) == null) {
                    break;
                } else {
                    continue;
                }
            }

            if (!in_array($currentTile, $visited)) {
                $visited[] = $currentTile;
            }

            $b = explode(',', $currentTile);

            foreach (Board::$OFFSETS as $pq) {
                $p = $b[0] + $pq[0];
                $q = $b[1] + $pq[1];

                $position = $p . "," . $q;

                if (
                    $position != $tileBefore &&
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

            $tileBefore = $currentTile;
        }

        return false;
    }

    private function isThreeSteps($from, $to)
    {

        [$fromX, $fromY] = explode(',', $from);
        [$toX, $toY] = explode(',', $to);

        $xDiff = abs($toX - $fromX);
        $yDiff = abs($fromY - $toY);

        $totalDistance = $xDiff + $yDiff;

        return $totalDistance == 3;
    }
}