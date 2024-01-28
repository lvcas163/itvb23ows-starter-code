<?php

function getState()
{
    return serialize([$_SESSION['hand'], $_SESSION['board'], $_SESSION['player']]);
}

function setState($state)
{
    list($a, $b, $c) = unserialize($state);
    $_SESSION['hand'] = $a;
    $_SESSION['board'] = $b;
    $_SESSION['player'] = $c;
}

$db_host = getenv('DB_HOST') ?: 'db';
$db_user = getenv('DB_USER') ?: 'user';
$db_password = getenv('DB_PASSWORD') ?: 'password';
$db_name = getenv('DB_NAME') ?: 'hive';

$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

return $mysqli;

?>
