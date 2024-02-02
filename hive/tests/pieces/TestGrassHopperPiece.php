<?php


use Lucas\Hive\Board;
use Lucas\Hive\Hive;
use Lucas\Hive\HiveException;
use Lucas\Hive\pieces\GrassHopperPiece;

use PHPUnit\Framework\TestCase;

class TestGrassHopperPiece extends TestCase
{
    public function testGrassHopperJump()
    {
        $board = new Board(['0,0' => [[0, 'Q']], '1,0' => [[1, 'Q']], '-1,0' => [[0, 'G']]]);
        $hive = new Hive($board);
        $grassHopper = new GrassHopperPiece($hive);
        $this->assertTrue($grassHopper->validateMove('-1,0', '2,0'));
    }

    public function testGrassHopperJumpNonEmptyTileHorizontal()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '-1,0' => [[0, 'G']],
            '2,0' => [[1, 'B']],
        ]);

        $hive = new Hive($board);
        $grassHopper = new GrassHopperPiece($hive);
        $this->expectException(HiveException::class);
        $grassHopper->validateMove('-1,0', '2,0');
    }

    public function testGrassHopperJumpNonEmptyTileVertical()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']],
            '0,-1' => [[0, 'G']],
        ]);

        $hive = new Hive($board);
        $grassHopper = new GrassHopperPiece($hive);
        $this->assertTrue($grassHopper->validateMove('0,-1', '0,2'));
    }

    public function testGrassHopperJumpDiagonal()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '-1,0' => [[0, 'G']],
            '2,0' => [[1, 'B']],
        ]);

        $hive = new Hive($board);
        $grassHopper = new GrassHopperPiece($hive);
        $this->expectException(HiveException::class);
        $grassHopper->validateMove('-1,0', '1,1');
    }

    public function testGrassHopperEmptyTileJump(){
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']],
            '-1,0' => [[0, 'G']],
            '0,2' => [[1, 'B']],
            '1,1' => [[1, 'B']],
            '2,0' => [[1, 'S']],
        ]);

        $hive = new Hive($board);
        $grassHopper = new GrassHopperPiece($hive);
        $this->expectException(HiveException::class);
        $grassHopper->validateMove('-1,0', '3,0');
    }

    public function testGrassHopperOneTileJump()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '0,-1' => [[0, 'G']]]);

        $hive = new Hive($board);
        $grassHopper = new GrassHopperPiece($hive);
        $this->expectException(HiveException::class);
        $grassHopper->validateMove('0,-1', '1,-1');
    }
}
