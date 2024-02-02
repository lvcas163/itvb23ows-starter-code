<?php


use Lucas\Hive\Board;
use Lucas\Hive\Hive;
use Lucas\Hive\HiveException;

use Lucas\Hive\pieces\SoldierAntPiece;
use PHPUnit\Framework\TestCase;

class TestSoldierAntPiece extends TestCase
{

    public function testMoveTiles()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '1,1' => [[1, 'B']],
            '2,0' => [[0, 'A']],
        ]);
        $hive = new Hive($board);
        $ant = new SoldierAntPiece($hive);
        $this->assertTrue($ant->validateMove('1,-1', '0,1'));
    }

    public function testMoveToNonEmptyTile()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '1,1' => [[1, 'B']],
            '2,0' => [[0, 'A']],
        ]);
        $hive = new Hive($board);
        $ant = new SoldierAntPiece($hive);
        $this->expectException(HiveException::class);
        $this->assertTrue($ant->validateMove('1,-1', '1,0'));
    }
}
