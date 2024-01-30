<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Lucas\Hive\Database;


$_SESSION['board'] = [];
$_SESSION['hand'] = [
    0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3],
    1
    => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]
];
$_SESSION['player'] = 0;

$_SESSION['game_id'] = Database::newGame();

header('Location: index.php');
