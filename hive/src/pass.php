<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Hive;
use Lucas\Hive\HiveException;

$hive = Hive::fromSession($_SESSION);
try {
    $moveId = $hive->pass();

    $_SESSION['last_move'] = $moveId;
    $_SESSION['player'] = $hive->getOtherPlayer();
} catch(HiveException $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: index.php');
