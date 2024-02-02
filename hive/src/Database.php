<?php

namespace Lucas\Hive;

use mysqli;

class Database
{
    private static $db = null;

    private function __construct()
    {
        // Private because there is always just one database conn
    }

    public static function getInstance()
    {
        if (self::$db === null) {
            $db_host = getenv('DB_HOST') ?: '0.0.0.0';
            $db_user = getenv('DB_USER') ?: 'user';
            $db_password = getenv('DB_PASSWORD') ?: 'password';
            $db_name = getenv('DB_NAME') ?: 'hive';

            self::$db = new mysqli($db_host, $db_user, $db_password, $db_name);

            if (self::$db->connect_error) {
                die('Connect Error (' . self::$db->connect_errno . ') ' . self::$db->connect_error);
            }
        }
        return self::$db;
    }

    private static function addMove($gameId, $from, $to, $lastMove, $state, $move)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state)
         values (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isssis', $gameId, $move, $from, $to, $lastMove, $state);
        $stmt->execute();

        return $db->insert_id;
    }

    public static function addNormalMove($gameId, $from, $to, $lastMove, $state)
    {
        return Database::addMove($gameId, $from, $to, $lastMove, $state, 'move');
    }

    public static function addPlayMove($gameId, $from, $to, $lastMove, $state)
    {
        return Database::addMove($gameId, $from, $to, $lastMove, $state, 'play');
    }

    public static function addPassMove($gameId, $lastMove, $state)
    {
        return Database::addMove($gameId, null, null, $lastMove, $state, "pass");
    }

    public static function getMoves(string $gameId)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM moves WHERE game_id = ?');
        $stmt->bind_param('s', $gameId);
        $stmt->execute();
        $result = $stmt->get_result();
        $moves = array();
        while ($row = $result->fetch_assoc()) {
            $moves[] = $row;
        }
        return $moves;
    }

    public static function getMove(string $moveId)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM moves WHERE id = ?');
        $stmt->bind_param('s', $moveId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function deleteMove(string $moveId)
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM moves WHERE id = ?');
        $stmt->bind_param('s', $moveId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public static function newGame()
    {
        $db = Database::getInstance();
        $db->prepare('INSERT INTO games VALUES ()')->execute();
        return $db->insert_id;
    }
}
