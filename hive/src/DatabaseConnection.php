<?php
namespace Lucas\Hive;

use mysqli;

class DatabaseConnection {
    private static $instance = null;

    private function __construct() {
        // Private because there is always just one database conn
    }

    public static function getInstance() {
        if (self::$instance === null) {
            $db_host = getenv('DB_HOST') ?: 'db';
            $db_user = getenv('DB_USER') ?: 'user';
            $db_password = getenv('DB_PASSWORD') ?: 'password';
            $db_name = getenv('DB_NAME') ?: 'hive';

            self::$instance = new mysqli($db_host, $db_user, $db_password, $db_name);

            if (self::$instance->connect_error) {
                die('Connect Error (' . self::$instance->connect_errno . ') ' . self::$instance->connect_error);
            }
        }
        return self::$instance;
    }
}
