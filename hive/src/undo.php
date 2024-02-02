<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Hive;
use Lucas\Hive\HiveException;

$hive = Hive::fromSession($_SESSION);
try {
    $moveIdBefore = $hive->undo();
    $_SESSION['last_move'] = $moveIdBefore;
} catch (HiveException $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: index.php');
