<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Board;
use Lucas\Hive\Database;
use Lucas\Hive\Hand;
use Lucas\Hive\Hive;
use Lucas\Hive\HiveException;
use PHPUnit\Framework\TestCase;

class HiveTest extends TestCase
{
    public function testFromSession()
    {
        $hand = ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];
        $session = [
            'board' => [],
            'hand' => [$hand, $hand],
            'player' => 0,
            'game_id' => 1
        ];
        $hive = Hive::fromSession($session);
        $hands = $hive->getHands();

        $this->assertEquals($hand, $hands[0]->getHand());
        $this->assertEquals($hand, $hands[1]->getHand());
        $this->assertEquals(0, $hive->getPlayer());
        $this->assertEquals(1, $hive->getGameId());
        $this->assertEquals([], $hive->getBoard()->getBoard());
    }

    public function testGetOtherPlayer()
    {
        $hive = new Hive(player: 0);
        $this->assertEquals(1, $hive->getOtherPlayer());
        $hive = new Hive(player: 1);
        $this->assertEquals(0, $hive->getOtherPlayer());
    }

    public function testGetHands()
    {
        $hand = new Hand(["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]);
        $hands = [$hand, $hand];
        $hive = new Hive(hands: $hands);

        $this->assertEquals($hands, $hive->getHands());
    }

    public function testGetPlayerHand()
    {
        $hand = new Hand(["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]);
        $hand1 = new Hand(["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]);
        $hive = new Hive(player: 0, hands: [$hand, $hand1]);

        $this->assertEquals($hand, $hive->getPlayerHand());
    }

    public function testGetGameId()
    {
        $hive = new Hive(gameId: 1);
        $this->assertEquals(1, $hive->getGameId());
    }

    public function testGetPlayer()
    {
        $hive = new Hive(player: 1);
        $this->assertEquals(1, $hive->getPlayer());
    }

    public function testGetBoard()
    {
        $board = new Board([]);
        $hive = new Hive(board: $board);

        $this->assertEquals($board, $hive->getBoard());
    }

    public function testGetValidPositions()
    {
        $emptyBoard = new Hive(new Board([]), player: 0);
        $this->assertEquals([], $emptyBoard->getValidPositionsPlay());

        $nonEmptyCheck = new Hive(new Board(['0,0' => [[0, 'Q']]]), player: 1);
        $nonEmptyTiles = ['0,1', '0,-1', '1,0', '-1,0', '-1,1', '1,-1'];
        $this->assertEquals($nonEmptyTiles, $nonEmptyCheck->getValidPositionsPlay());

        $neighboursBoard = new Board(['0,0' => [[0, 'Q']], '1,0' => [[1, 'Q']]]);
        $hands = [new Hand(['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3,]), new Hand(['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3])];
        $neighboursCheck = new Hive($neighboursBoard, player: 0, hands: $hands);
        $validTiles = ['0,-1', '-1,0', '-1,1'];
        $this->assertEquals($validTiles, $neighboursCheck->getValidPositionsPlay());
    }

    public function testMoveFourQueenBee()
    {
        $board = new Board(array(
            '0,0' => array(0 => 0, 1 => 'B'),
            '1,0' => array(0 => 1, 1 => 'B'),
            '0,-1' => array(0 => 0, 1 => 'S'),
            '1,1' => array(0 => 1, 1 => 'B'),
            '1,-2' => array(0 => 0, 1 => 'S'),
            '1,2' => array(0 => 1, 1 => 'S'),
        ));
        $hands = [new Hand([
            'Q' => 1,
            'B' => 1,
            'S' => 0,
            'A' => 3,
            'G' => 3,
        ]), new Hand([
            'Q' => 1,
            'B' => 0,
            'S' => 1,
            'A' => 3,
            'G' => 3,
        ])];

        $hive = new Hive($board, gameId: 0, player: 0, hands: $hands);

        $reflectionClass = new ReflectionClass($hive);
        $playRulesHand = $reflectionClass->getMethod('playRulesHand');
        $playRulesHand->setAccessible(true);

        $playRulesHand->invoke($hive, 'Q');


        $this->expectException(HiveException::class);

        $playRulesHand->invoke($hive, 'B');
    }

    public function testValidMoveTwoQueens()
    {
        $board1 = new Board(array(
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']]
        ));
        $hands1 = [new Hand(["Q" => 0, "B" => 2, "S" => 2, "A" => 3, "G" => 3]), new Hand(["Q" => 0, "B" => 2, "S" => 2, "A" => 3, "G" => 3])];

        $hive = new Hive($board1, gameId: -1, player: 0, hands: $hands1);

        $moveId = $hive->move('0,0', '0,1');
        $this->assertIsInt($moveId);

        $board2 = new Board(array(
            '0,0' => [[0, 'B']],
            '1,0' => [[1, 'B']]
        ));

        $hands2 = [new Hand([
            'Q' => 1,
            'B' => 0,
            'S' => 0,
            'A' => 3,
            'G' => 3,
        ]), new Hand([
            'Q' => 1,
            'B' => 0,
            'S' => 0,
            'A' => 3,
            'G' => 3,
        ])];

        $this->expectException(HiveException::class);

        $hive = new Hive($board2, gameId: -1, player: 0, hands: $hands2);
        $hive->move('0,0', '0,1');
    }

    public function testLostCondition()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '0,-1' => [[0, 'B']],
            '-1,-1' => [[0, 'S']],
            '-1,0' => [[0, 'B']],
            '-1,1' => [[0, 'S']],
            '0,1' => [[1, 'B']],
            '1,-1' => [[1, 'B']],
        ]);
        $hive = new Hive($board);

        $this->assertTrue($hive->hasLost(0));
    }

    public function testNoLoseCondition()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '0,-1' => [[0, 'B']],
            '-1,-1' => [[0, 'S']],
            '2,-1' => [[1, 'B']],
            '-1,0' => [[0, 'B']],
            '-1,1' => [[0, 'S']],
            '0,1' => [[1, 'B']],
        ]);
        $hive = new Hive($board);
        $this->assertFalse($hive->hasLost(0));
    }

    public function testDrawCondition()
    {
        $board = new Board([
            '0,0' => [[0, 'Q']],
            '1,0' => [[1, 'Q']],
            '0,-1' => [[0, 'B']],
            '-1,-1' => [[0, 'S']],
            '-1,0' => [[0, 'B']],
            '-1,1' => [[0, 'S']],
            '0,1' => [[1, 'B']],
            '1,-1' => [[1, 'B']],
            '2,-1' => [[1, 'S']],
            '1,1' => [[1, 'S']],
            '2,0' => [[1, 'A']],
        ]);
        $hive = new Hive($board);
        $this->assertTrue($hive->hasLost(0));
        $this->assertTrue($hive->hasLost(1));
    }

    public function testPassPossible()
    {
        $board = new Board([
            "0,0" => [[0, "A"]]
        ]);
        $hand = [new Hand([]), new Hand([])];
        $hive = new Hive($board, player: 0, hands: $hand);
        $this->assertTrue($hive->canPass());
    }

    public function testPassPossibleMove()
    {
        $board = new Board([
            "0,0" => [[0, 'Q']],
            "0,1" => [[1, 'A']],
            "0,-1" => [[1, 'A']],
            "-1,0" => [[1, 'A']],
            "-1,1" => [[1, 'A']]
        ]);
        $hand = [new Hand(["Q" => 0, "B" => 0, "S" => 0, "A" => 0, "G" => 0]), new Hand(["Q" => 0, "B" => 0, "S" => 0, "A" => 0, "G" => 0])];
        $hive = new Hive($board, player: 0, hands: $hand);
        $this->expectException(HiveException::class);
        $hive->canPass();
    }

    public function testPassPossiblePlay()
    {
        $board = new Board(["0,0" => [[0, 'Q']]]);
        $hand = [new Hand(["Q" => 1, "B" => 2, "S" => 2, "A" => 2, "G" => 2]), new Hand(["Q" => 1, "B" => 2, "S" => 2, "A" => 2, "G" => 2])];
        $hive = new Hive($board, player: 0, hands: $hand);
        $this->expectException(HiveException::class);
        $hive->canPass();
    }

    public function testPassEmptyBoard()
    {
        $board = new Board([]);
        $hive = new Hive($board);

        $this->expectException(HiveException::class);
        $hive->undo();
    }

    public function testUndo()
    {
        $double = Mockery::mock('overload:' . Database::class); // this fails when running all classes at once

        // Set up the expectations for the mocked methods
        $double->shouldReceive('getMove')->with(756)->andReturn([
            'id' => 756,
            'game_id' => 591,
            'type' => 'play',
            'move_from' => 'Q',
            'move_to' => '0,1',
            'previous_id' => 755,
            'state' => 'a:3:{i:0;a:2:{i:0;a:5:{s:1:"Q";i:0;s:1:"B";i:2;s:1:"S";i:2;s:1:"A";i:3;s:1:"G";i:3;}i:1;a:5:{s:1:"Q";i:1;s:1:"B";i:2;s:1:"S";i:2;s:1:"A";i:3;s:1:"G";i:3;}}i:1;a:1:{s:3:"0,0";a:1:{i:0;a:2:{i:0;i:0;i:1;s:1:"Q";}}}i:2;i:1;}',
        ])->once();

        $double->shouldReceive('deleteMove')->with(756)->andReturn(true)->once();

        $_SESSION = [];

        $board = new Board([
            '0,0' => [[0, 'Q']],
            '0,1' => [[1, 'Q']],
        ]);
        $hands = [
            new Hand([['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3]]),
            new Hand(['Q' => 0, 'B' => 2, 'S' => 2, 'A' => 3, 'G' => 3])
        ];
        $hive = new Hive($board, 1, 0, $hands, 756);

        $this->assertEquals(755, $hive->undo());
    }
}
