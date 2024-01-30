<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Database;
use Lucas\Hive\Util;

$insertId = Database::addPassMove($_SESSION['game_id'], $_SESSION['last_move'], Util::getState());
$_SESSION['last_move'] = $insertId;
$_SESSION['player'] = 1 - $_SESSION['player'];

header('Location: index.php');
