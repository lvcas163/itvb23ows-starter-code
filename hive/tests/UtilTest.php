<?php

use PHPUnit\Framework\TestCase;
use Lucas\Hive\Util;

class UtilTest extends TestCase
{
    public function testIsNeighbour()
    {
        $this->assertTrue(Util::isNeighbour('0,1', '0,2'));
        $this->assertFalse(Util::isNeighbour('0,0', '2,2'));
    }

    public function testHasNeighBour()
    {
        $board = ['0,1' => true, '1,0' => false];
        $this->assertTrue(Util::hasNeighBour('0,0', $board));
        $this->assertFalse(Util::hasNeighBour('2,2', $board));
    }

    public function testNeighboursAreSameColor()
    {
        $board = ['0,1' => [['white']], '1,0' => [['black']]];
        $this->assertTrue(Util::neighboursAreSameColor('white', '0,0', $board));
        $this->assertFalse(Util::neighboursAreSameColor('black', '0,0', $board));
    }

    public function testLen()
    {
        $this->assertEquals(0, Util::len(null));
        $this->assertEquals(3, Util::len([1, 2, 3]));
    }

    public function testSlide()
    {
        $board = ['0,1' => true, '1,0' => false];
        $this->assertTrue(Util::slide($board, '0,0', '0,1'));
        $this->assertFalse(Util::slide($board, '0,0', '2,2'));
    }

    public function testGetSetState()
    {
        $_SESSION = ['hand' => 'value1', 'board' => 'value2', 'player' => 'value3'];
        $state = Util::getState();
        Util::setState($state);
        $this->assertEquals('value1', $_SESSION['hand']);
        $this->assertEquals('value2', $_SESSION['board']);
        $this->assertEquals('value3', $_SESSION['player']);
    }
}
