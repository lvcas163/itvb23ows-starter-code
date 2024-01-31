<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Util;

use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    public function testSetState()
    {
        // Arrange
        $state = serialize(['a', 'b', 'c']);
        // Act
        Util::setState($state);
        // Assert
        $this->assertEquals('a', $_SESSION['hand']);
        $this->assertEquals('b', $_SESSION['board']);
        $this->assertEquals('c', $_SESSION['player']);
    }
}