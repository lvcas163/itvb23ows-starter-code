<?php


use Lucas\Hive\Board;
use Lucas\Hive\Hive;
use Lucas\Hive\HiveException;

use Lucas\Hive\pieces\SoldierAntPiece;
use Lucas\Hive\pieces\SpiderPiece;
use PHPUnit\Framework\TestCase;

class TestSpiderPiece extends TestCase
{

    public function testMoveThreeTiles()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '0,-1' => [[0, 'S']],
            '2,0' => [[1, 'B']],
            '3,0' => [[1, 'B']],
        ]);
        $hive = new Hive($board);
        $spiderPiece = new SpiderPiece($hive);
        $this->assertTrue($spiderPiece->validateMove('0,-1', '3,-1'));
    }

    public function testMoveLessThanThreeTiles()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '0,-1' => [[0, 'S']],
            '2,0' => [[1, 'B']],
            '3,0' => [[1, 'B']],
        ]);
        $hive = new Hive($board);
        $spiderPiece = new SpiderPiece($hive);
        $this->expectException(HiveException::class);
        $this->assertTrue($spiderPiece->validateMove('0,-1', '2,-1'));
    }

    public function testMoveMoreThanThreeTiles()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '0,-1' => [[0, 'S']],
            '2,0' => [[1, 'B']],
            '3,0' => [[1, 'B']],
        ]);
        $hive = new Hive($board);
        $spiderPiece = new SpiderPiece($hive);
        $this->expectException(HiveException::class);
        $this->assertTrue($spiderPiece->validateMove('0,-1', '4,-1'));
    }

    public function testMoveToNonEmptyTile()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '0,-1' => [[0, 'S']],
            '2,0' => [[1, 'B']],
            '3,0' => [[1, 'B']],
        ]);
        $hive = new Hive($board);
        $ant = new SoldierAntPiece($hive);
        $this->expectException(HiveException::class);
        $this->assertTrue($ant->validateMove('0,-1', '2,0'));
    }
}
