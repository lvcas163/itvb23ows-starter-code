<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\AIPlayer;

use PHPUnit\Framework\TestCase;

class TestAIPlayer extends TestCase
{
    public function testSetState()
    {
        $double = Mockery::mock(AIPlayer::class);

        $double->shouldReceive('move')->andReturn(['play', 1])->once();

        $this->assertEquals(['play', 1], $double->move());
    }
}
