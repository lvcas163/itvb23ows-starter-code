<?php


use Lucas\Hive\Board;
use Lucas\Hive\Hive;
use Lucas\Hive\HiveException;
use Lucas\Hive\pieces\BeetlePiece;
use Lucas\Hive\pieces\GrassHopperPiece;

use PHPUnit\Framework\TestCase;

class TestBeetlePiece extends TestCase
{

    public function testMoveOneTile()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '0,-1' => [[0, 'B']],
            '0,-2' => [[0, 'B']],
        ]);
        $hive = new Hive($board);
        $beetle = new BeetlePiece($hive);
        $this->assertTrue($beetle->validateMove('0,-2', '1,-2'));
    }

    public function testMoveMultipleTiles()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '2,0' => [[1, 'B']],
            '0,-1' => [[0, 'B']],
        ]);
        $hive = new Hive($board);
        $beetle = new BeetlePiece($hive);
        $this->expectException(HiveException::class);
        $beetle->validateMove('0,-1', '2,-1');
    }

    public function testMoveToNonEmptyTile()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '2,0' => [[1, 'B']],
            '0,-1' => [[0, 'B']],
        ]);
        $hive = new Hive($board);
        $beetle = new BeetlePiece($hive);
        $this->assertTrue($beetle->validateMove('0,-1', '0,0'));
    }
}
