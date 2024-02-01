<?php

namespace Lucas\Hive\pieces;

use Lucas\Hive\HiveException;

class GrassHopperPiece extends BasePiece
{

    public function validateMove(string $from, string $to): bool
    {
        if (!$this->hive->getBoard()->emptyTile($to)) {
            throw new HiveException('Tile not empty');
        }
        if (!$this->checkJumpTiles($from, $to)) {
            throw new HiveException('Grasshopper needs to move at least 1 tile');
        }
        if (!$this->isStraight($from, $to)) {
            throw new HiveException('Move is not straight');
        }
        if (!$this->noEmptyPositions($from, $to)) {
            throw new HiveException('Grasshopper cant jump over empty positions');
        }
        return true;
    }

    private function isStraight(string $from, string $to): bool
    {
        [$fromX, $fromY] = explode(',', $from);
        [$toX, $toY] = explode(',', $to);

        if ($this->isOnSameAxis($fromX, $fromY, $toX, $toY)) {
            return true;
        }

        if ($this->isOnDiagonal($fromX, $fromY, $toX, $toY)) {
            return true;
        }

        return false;
    }

    private function isOnSameAxis(int $fromX, int $fromY, int $toX, int $toY): bool
    {
        return $fromX == $toX || $fromY == $toY;
    }

    private function isOnDiagonal(int $fromX, int $fromY, int $toX, int $toY): bool
    {
        return abs($fromX - $toX) == abs($fromY - $toY);
    }

    private function noEmptyPositions(string $from, string $to): bool
    {
        [$startX, $startY] = explode(',', $from);
        [$endX, $endY] = explode(',', $to);


        if ($this->isHorizontalMove($startY, $endY)) {
            $positions = $this->generateCoordinatesHorizontal($startX, $endX, $startY);
        } elseif ($this->isVerticalMove($startX, $endX)) {
            $positions = $this->generateCoordinatesVertical($startY, $endY, $startX);
        } else {
            return false;
        }

        foreach ($positions as $pos) {
            if ($this->hive->getBoard()->emptyTile($pos)) {
                return false;
            }
        }

        return true;
    }

    private function isHorizontalMove(int $startY, int $endY): bool
    {
        return $startY == $endY;
    }

    private function isVerticalMove(int $startX, int $endX): bool
    {
        return $startX == $endX;
    }

    private function generateCoordinatesHorizontal(int $startX, int $endX, int $y): array
    {
        $coordinates = [];
        for ($x = min($startX, $endX) + 1; $x < max($startX, $endX); $x++) {
            $coordinates[] = "$x,$y";
        }
        return $coordinates;
    }

    private function generateCoordinatesVertical(int $startY, int $endY, int $x): array
    {
        $coordinates = [];
        for ($y = min($startY, $endY) + 1; $y < max($startY, $endY); $y++) {
            $coordinates[] = "$x,$y";
        }
        return $coordinates;
    }


    private function checkJumpTiles(string $from, string $to): bool
    {
        [$startX, $startY] = explode(',', $from);
        [$endX, $endY] = explode(',', $to);

        if ($this->isHorizontalMove($startY, $endY)) {
            $positions = $this->generateCoordinatesHorizontal($startX, $endX, $startY);
        } else {
            $positions = $this->generateCoordinatesVertical($startY, $endY, $startX);
        }

        return count($positions) > 0;
    }
}