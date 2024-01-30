<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Hive;

$hive = Hive::fromSession($_SESSION);

$moveId = $hive->pass();
$_SESSION['last_move'] = $moveId;
$_SESSION['player'] = $hive->getOtherPlayer();

header('Location: index.php');
