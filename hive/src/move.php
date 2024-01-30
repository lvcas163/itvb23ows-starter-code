<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Hive;
use Lucas\Hive\HiveException;

$gameId = $_SESSION['game_id'];
$player = $_SESSION['player'];

$from = $_POST['from'];
$to = $_POST['to'];

$hive = Hive::fromSession($_SESSION);

unset($_SESSION['error']);

try {
    $moveId = $hive->move($from, $to);

    $_SESSION['player'] = $hive->getOtherPlayer();
    $_SESSION['last_move'] = $moveId;
    $_SESSION['board'] = $board->getBoard();
} catch (HiveException $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: index.php');
