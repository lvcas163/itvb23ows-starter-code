<?php

namespace Lucas\Hive;

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Board;
use Lucas\Hive\HiveException;

class Hive
{
    private Board $board;
    private int $gameId;
    private int $player;
    private array $hands;
    private int|null $lastMove;

    public function __construct(Board $board = null, int $gameId = null, int $player = 0, array $hands = null, int $lastMove = null)
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
        if (isset($session['hands'])) {
            $hands = array_map(function (array $hand) {
                return new Hand($hand);
            }, $session['hands']);
        }

        return new Hive(
            new Board($session['board']),
            $session['game_id'],
            $session['player'],
            $hands,
            $session['last_move']
        );
    }

    public function getOtherPlayer()
    {
        return 1 - $this->player;
    }

    public function getMoves()
    {
        $result = Database::getMoves($this->gameId);
        return $result->fetch_array();
    }

    public function getHands()
    {
        return $this->hands;
    }

    public function getPlayerHand()
    {
        return $this->hands[$this->player];
    }

    public function getOpponentHand()
    {
        return $this->hands[$this->getOtherPlayer()];
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

    public function move(string $from, string $to)
    {
        try {
            if ($this->board->emptyTile($from)) {
                throw new HiveException('Board position is empty');
            } elseif ($this->board->getLastTile($from)[0] != $this->player) {
                throw new HiveException("Tile is not owned by player");
            } elseif ($this->getPlayerHand()->hasPiece('Q')) {
                throw new HiveException("Queen bee is not played");
            }

            $tile = $this->board->popTile($from);
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

            if ($from == $to) {
                throw new HiveException('Tile must move');
            } elseif (!$this->board->emptyTile($to) && $tile[1] != "B") {
                throw new HiveException('Tile not empty');
            } elseif ($tile[1] == "Q" || $tile[1] == "B") {
                if (!$this->board->slide($from, $to)) {
                    throw new HiveException('Tile must slide');
                }
            }
        } catch (HiveException $e) {
            if (!$this->board->emptyTile($from)) {
                $this->board->pushTile($from, $tile[0], $tile[1]);
            } else {
                $this->board->setTile($from, $tile[0], $tile[1]);
            }

            throw $e;
        }

        if (!$this->board->emptyTile($to)) {
            $this->board->pushTile($to, $tile[0], $tile[1]);
        } else {
            $this->board->setTile($to, $tile[0], $tile[1]);
        }

        return Database::addNormalMove($this->gameId, $from, $to, $this->lastMove, Util::getState());
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
        return Database::addPassMove($this->gameId, $this->lastMove, Util::getState());
    }

    public function play(string $to, string $piece)
    {
        $hand = $this->getPlayerHand();
        $board = $this->getBoard();

        if (!$hand->hasPiece($piece)) {
            throw new HiveException("Player does not have tile");
        } elseif ($board->emptyTile($to)) {
            throw new HiveException('Board position is not empty');
        } elseif ($board->boardCount() && !$board->hasNeighBour($to)) {
            throw new HiveException("board position has no neighbour");
        } elseif ($hand->sum() < 11 && !$board->neighboursAreSameColor($this->getPlayer(), $to)) {
            throw new HiveException("Board position has opposing neighbour");
        } elseif ($hand->sum() <= 8 && $hand['Q']) {
            throw new HiveException('Must play queen bee');
        }

        $this->getBoard()->setTile($to, $piece, $this->getPlayer());
        $this->getPlayerHand()->removePiece($piece);

        return Database::addPlayMove($this->gameId, $piece, $to, $this->lastMove, Util::getState());
    }
}
