<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Hive;

function undo() {
    $hive = Hive::fromSession($_SESSION);

    $moveIdBefore = $hive->undo();
    $_SESSION['last_move'] = $moveIdBefore;
    header('Location: index.php');
}

undo();
