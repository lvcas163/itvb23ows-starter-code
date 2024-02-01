<?php

use PHPUnit\Framework\TestCase;
use Lucas\Hive\Hand;

class HandTest extends TestCase
{
    public function testDefaultHand()
    {
        $hand = new Hand();
        $defaultHand = ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];

        $this->assertEquals($defaultHand, $hand->getHand());
    }

    public function testCustomHand()
    {
        $customHand = ["Q" => 2, "B" => 1, "S" => 1, "A" => 2, "G" => 1];
        $hand = new Hand($customHand);

        $this->assertEquals($customHand, $hand->getHand());
    }

    public function testHasPiece()
    {
        $hand = new Hand();

        $this->assertTrue($hand->hasPiece("Q"));
        $this->assertTrue($hand->hasPiece("B"));
        $this->assertTrue($hand->hasPiece("S"));
        $this->assertTrue($hand->hasPiece("A"));
        $this->assertTrue($hand->hasPiece("G"));

        $hand->removePiece("Q");
        $this->assertFalse($hand->hasPiece("Q"));
    }

    public function testRemovePiece()
    {
        $hand = new Hand();

        $this->assertTrue($hand->hasPiece("Q"));

        $hand->removePiece("Q");
        $this->assertFalse($hand->hasPiece("Q"));
    }

    public function testSum()
    {
        $hand = new Hand(["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]);
        $this->assertEquals(11, $hand->sum());

        $hand->removePiece("Q");
        $this->assertEquals(10, $hand->sum());
    }
}
