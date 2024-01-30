<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\HiveException;
use Lucas\Hive\Hive;

$piece = $_POST['piece'];
$to = $_POST['to'];

$hive = Hive::fromSession($_SESSION);

try {
    $hive = Hive::fromSession($_SESSION);

    $moveId = $hive->play($to, $piece);
    $_SESSION['board'] = $hive->getBoard()->getBoard();
    $_SESSION['player'] = $hive->getOtherPlayer();
    $_SESSION['last_move'] = $moveId;
    $_SESSION['hand'] = $hive->getPlayerHand()->getHand();
} catch (HiveException $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: index.php');
