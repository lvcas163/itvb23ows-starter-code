<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Board;

use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{
    public function testIsNeighbour()
    {
        $this->assertTrue(Board::isNeighbour('0,1', '0,2'));
        $this->assertFalse(Board::isNeighbour('0,0', '2,2'));
    }

    public function testHasNeighbour()
    {
        $board = new Board(['0,1' => true, '1,0' => false]);
        $this->assertTrue($board->hasNeighBour('0,0'));
        $this->assertFalse($board->hasNeighBour('2,2'));
    }

    public function testNeighboursAreSameColor()
    {
        $board = new Board([
            '0,0' => ['Q', '0'],
            '0,1' => ['Q', '0'],
            '1,0' => ['Q', '0']
        ]);
        $this->assertTrue($board->neighboursAreSameColor(0, '0,1'));
        $this->assertFalse($board->neighboursAreSameColor(1, '0,1'));
    }

    public function testLen()
    {
        $this->assertEquals(0, Board::len(null));
        $this->assertEquals(3, Board::len([1, 2, 3]));
    }

    public function testGetPlayerTiles()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']],
            '1,0' => [[1, 'B']]
        ]);

        $this->assertEquals(['0,0'], $board->getPlayerTiles(0));
    }

    public function testPopTile()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']]
        ]);

        $newBoard = [
            '0,0' => [[0, 'Q']]
        ];

        $board->popTile('0,1');

        $this->assertEquals($newBoard, $board->getBoard());

        $board = new Board([
            '0,0' => [[0, 'Q'], [1, 'B']],
            '0,1' => [[1, 'Q']]
        ]);
        $newBoard = [
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']]
        ];

        $board->popTile('0,0');
        $this->assertEquals($newBoard, $board->getBoard());
    }
}
