<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Database;
use Lucas\Hive\Util;
use Lucas\Hive\Board;

$from = $_POST['from'];
$to = $_POST['to'];

$player = $_SESSION['player'];
$board = new Board($_SESSION['board']);
$hand = $_SESSION['hand'][$player];
unset($_SESSION['error']);

if ($board->emptyTile($from)) {
    $_SESSION['error'] = 'Board position is empty';
} elseif ($board->getLastTile($from)[0] != $player) {
    $_SESSION['error'] = "Tile is not owned by player";
} elseif ($hand['Q']) {
    $_SESSION['error'] = "Queen bee is not played";
} else {
    $tile = $board->popTile($from);
    if (!$board->hasNeighBour($to)) {
        $_SESSION['error'] = "Move would split hive";
    } else {
        $all = $board->allTiles();
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
            $_SESSION['error'] = "Move would split hive";
        } else {
            if ($from == $to) {
                $_SESSION['error'] = 'Tile must move';
            } elseif (!$board->emptyTile($to) && $tile[1] != "B") {
                $_SESSION['error'] = 'Tile not empty';
            } elseif ($tile[1] == "Q" || $tile[1] == "B") {
                if (!$board->slide($from, $to)) {
                    $_SESSION['error'] = 'Tile must slide';
                }
            }
        }
    }
    if (isset($_SESSION['error'])) {
        if(!$board->emptyTile($from)) {
            $board->pushTile($from, $tile[0], $tile[1]);
        } else {
            $board->setTile($from, $tile[0], $tile[1]);
        }
    } else {
        if(!$board->emptyTile($to)) {
            $board->pushTile($to, $tile[0], $tile[1]);
        } else {
            $board->setTile($to, $tile[0], $tile[1]);
        }
        $_SESSION['player'] = 1 - $_SESSION['player'];
        $insertId = Database::addNormalMove($_SESSION['game_id'], $from, $to, $_SESSION['last_move'], Util::getState());
        $_SESSION['last_move'] = $insertId;
    }
    $_SESSION['board'] = $board;
}

header('Location: index.php');
