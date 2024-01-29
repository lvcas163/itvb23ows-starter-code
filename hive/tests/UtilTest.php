<?php

use PHPUnit\Framework\TestCase;
use Lucas\Hive\Util;

class UtilTest extends TestCase
{
    /**
     * @covers ::isNeighbour
     */
    public function testIsNeighbour()
    {
        $this->assertTrue(Util::isNeighbour('0,1', '0,2'));
        $this->assertFalse(Util::isNeighbour('0,0', '2,2'));
    }

    /**
     * @covers ::hasNeighBour
     */
    public function testHasNeighBour()
    {
        $board = ['0,1' => true, '1,0' => false];
        $this->assertTrue(Util::hasNeighBour('0,0', $board));
        $this->assertFalse(Util::hasNeighBour('2,2', $board));
    }

    /**
     * @covers ::neighboursAreSameColor
     */
    public function testNeighboursAreSameColor()
    {
        $board = [
            '0,0' => ['Q', '0'],
            '0,1' => ['Q', '0'],
            '1,0' => ['Q', '0']
        ];
        $this->assertTrue(Util::neighboursAreSameColor(0, '0,1', $board));
        $this->assertFalse(Util::neighboursAreSameColor(1, '0,1', $board));
    }

    /**
     * @covers ::len
     */
    public function testLen()
    {
        $this->assertEquals(0, Util::len(null));
        $this->assertEquals(3, Util::len([1, 2, 3]));
    }

    /**
     * @covers ::slide
     */
    public function testSlide()
    {
        $board = [
            '0,0' => [['white']],
            '0,1' => [['black']],
            '1,0' => null,
            '1,1' => null
        ];
        $this->assertTrue(Util::slide($board, '0,0', '1,0'));
        $this->assertFalse(Util::slide($board, '0,0', '2,0'));
    }

    /**
     * @covers ::getState
     * @covers ::setState
     */
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
