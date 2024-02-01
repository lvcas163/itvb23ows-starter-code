<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Hive;
use Lucas\Hive\Hand;

$hive = new Hive();

$_SESSION['board'] = $hive->getBoard()->getBoard();
$_SESSION['hand'] = array_map(function (Hand $hand) {
    return $hand->getHand();
}, $hive->getHands());
$_SESSION['player'] = $hive->getPlayer();
$_SESSION['game_id'] = $hive->getGameId();

header('Location: index.php');
