<?php

namespace Lucas\Hive;

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Board;
use Lucas\Hive\HiveException;
use Lucas\Hive\pieces\BasePiece;

class Hive
{
    private Board $board;
    private int $gameId;
    private int $player;
    private array $hands;
    private int|null $lastMove;

    public function __construct(Board $board = null, int $gameId = null, int $player = 0, array $hands = null,
                                int   $lastMove = null)
    {
        $this->board = $board ?? new Board();
        $this->gameId = $gameId ?? Database::newGame();
        $this->player = $player;
        $this->hands = $hands ?? [0 => new Hand(), 1 => new Hand()];
        $this->lastMove = $lastMove ?? null;
    }

    public static function fromSession(array $session)
    {
        $hands = null;
        if (isset($session['hand'])) {
            $hands = array_map(function (array $hand) {
                return new Hand($hand);
            }, $session['hand']);
        }

        $last_move = null;
        if (isset($session['last_move'])) {
            $last_move = $session['last_move'];
        }

        return new Hive(
            new Board($session['board']),
            $session['game_id'],
            $session['player'],
            $hands,
            $last_move
        );
    }

    public function getOtherPlayer()
    {
        return 1 - $this->player;
    }

    public function getMoves()
    {
        return Database::getMoves($this->gameId);
    }

    public function getHands()
    {
        return $this->hands;
    }

    public function getPlayerHand()
    {
        return $this->hands[$this->player];
    }

    public function getGameId()
    {
        return $this->gameId;
    }

    public function getPlayer()
    {
        return $this->player;
    }

    public function getBoard()
    {
        return $this->board;
    }

    private function checkTileMove(string $from)
    {
        if ($this->board->emptyTile($from)) {
            throw new HiveException('Board position is empty');
        } elseif ($this->board->getLastTile($from)[0] != $this->player) {
            throw new HiveException("Tile is not owned by player");
        } elseif ($this->getPlayerHand()->hasPiece('Q')) {
            throw new HiveException("Queen bee is not played");
        }
    }

    private function checkHive(string $to)
    {
        if (!$this->board->hasNeighBour($to)) {
            throw new HiveException("Move would split hive");
        }

        $all = $this->board->allTiles();
        $queue = [array_shift($all)];
        while ($queue) {
            $next = explode(',', array_shift($queue));
            foreach (Board::$OFFSETS as $pq) {
                list($p, $q) = $pq;
                $p += $next[0];
                $q += $next[1];
                if (in_array("$p,$q", $all)) {
                    $queue[] = "$p,$q";
                    $all = array_diff($all, ["$p,$q"]);
                }
            }
        }
        if ($all) {
            throw new HiveException("Move would split hive");
        }
    }

    private function checkDestination(string $from, string $to, string $type)
    {
        if ($from == $to) {
            throw new HiveException('Tile must move');
        }

        $piece = BasePiece::fromType($type, $this);
        $piece->validateMove($from, $to);
    }

    private function moveTile(string $position, array $tile)
    {
        if (!$this->board->emptyTile($position)) {
            $this->board->pushTile($position, $tile[1], $tile[0]);
        } else {
            $this->board->setTile($position, $tile[1], $tile[0]);
        }
    }

    public function move(string $from, string $to)
    {
        $tile = null;
        try {
            $this->checkTileMove($from);
            $tile = $this->board->popTile($from);
            $this->checkHive($from);
            $this->checkDestination($from, $to, $tile[1]);

            $this->moveTile($to, $tile);
        } catch (HiveException $e) {
            if ($tile) {
                if (!$this->board->emptyTile($from)) {
                    $this->board->pushTile($from, $tile[1], $tile[0]);
                } else {
                    $this->board->setTile($from, $tile[1], $tile[0]);
                }
            }

            throw $e;
        }

        return Database::addNormalMove($this->gameId, $from, $to, $this->lastMove, $this->getState());
    }

    public function undo()
    {
        $result = Database::getMove($this->lastMove)->fetch_array();
        Util::setState($result[6]);

        $moveIdBefore = $result[5];
        $this->lastMove = $moveIdBefore;

        return $moveIdBefore;
    }

    public function pass()
    {
        return Database::addPassMove($this->gameId, $this->lastMove, $this->getState());
    }

    public function getState()
    {
        $hands = array_map(function (Hand $hand) {
            return $hand->getHand();
        }, $this->getHands());

        return serialize([$hands, $this->getBoard()->getBoard(), $this->getPlayer()]);
    }

    private function playRulesHand(string $piece)
    {
        $hand = $this->getPlayerHand();

        if (!$hand->hasPiece($piece)) {
            throw new HiveException("Player does not have tile");
        }
        if ($hand->sum() <= 8 && $this->getPlayerHand()->hasPiece('Q') && $piece != 'Q') {
            throw new HiveException('Must play queen bee');
        }
    }

    public function play(string $to, string $piece)
    {
        $this->playRulesHand($piece);
        $this->checkPlayRules($to);

        $this->getBoard()->setTile($to, $piece, $this->getPlayer());
        $this->getPlayerHand()->removePiece($piece);

        return Database::addPlayMove($this->gameId, $piece, $to, $this->lastMove, $this->getState());
    }

    public function checkPlayRules($to): void
    {
        if (!$this->board->emptyTile($to)) {
            throw new HiveException('Board position is not empty');
        } elseif ($this->board->boardCount() && !$this->board->hasNeighBour($to)) {
            throw new HiveException("board position has no neighbour");
        } elseif ($this->getPlayerHand()->sum() < 11 && !$this->board->neighboursAreSameColor($this->getPlayer(), $to)) {
            throw new HiveException("Board position has opposing neighbour");
        }
    }

    public function getValidPositionsPlay(): array
    {
        $to = [];
        $offsets = Board::$OFFSETS;
        foreach ($offsets as $pq) {
            $positions = array_keys($this->board->getBoard());
            foreach ($positions as $pos) {
                $pq2 = explode(',', $pos);
                $result = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
                try {
                    $this->checkPlayRules($result);
                } catch (HiveException) {
                    continue;
                }
                $to[] = $result;
            }
        }
        $to = array_unique($to);
        if (!count($to)) {
            $to[] = '0,0';
        }

        return $to;
    }

    public function getValidPositionsMove(): array
    {
        $to = [];
        $offsets = Board::$OFFSETS;
        foreach ($offsets as $pq) {
            $positions = array_keys($this->board->getBoard());
            foreach ($positions as $pos) {
                $pq2 = explode(',', $pos);
                $result = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);
                $to[] = $result;
            }
        }
        $to = array_unique($to);
        if (!count($to)) {
            $to[] = '0,0';
        }

        return $to;
    }
}
