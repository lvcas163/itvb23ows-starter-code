<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\AIPlayer;
use Lucas\Hive\Hand;
use Lucas\Hive\Hive;
use Lucas\Hive\HiveException;

$hive = Hive::fromSession($_SESSION);

$moves = $hive->getMoves();
$aiPlayer = new AIPlayer(count($moves), $hive);
try {
    [$type, $moveId] = $aiPlayer->move();
    if ($type == 'move' || $type == 'play') {
        $_SESSION['board'] = $hive->getBoard()->getBoard();
        $_SESSION['player'] = $hive->getOtherPlayer();
        $_SESSION['last_move'] = $moveId;
        $_SESSION['hand'] = array_map(function (Hand $hand) {
            return $hand->getHand();
        }, $hive->getHands());
    } else {
        $_SESSION['last_move'] = $moveId;
        $_SESSION['player'] = $hive->getOtherPlayer();
    }
} catch (HiveException $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: index.php');
