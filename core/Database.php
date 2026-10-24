<?php
class Database {

    private static ?PDO $instance = null;

    public static function getInstance(): PDO {

        if (self::$instance === null) {
            $cfg = require __DIR__ . '/../config/database.php';
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s', 
                $cfg['host'], 
                $cfg['port'], 
                $cfg['dbname'], 
                $cfg['charset']
                );
            self::$instance = new PDO(
                $dsn, 
                $cfg['username'], 
                $cfg['password'], 
                $cfg['options']
                );
        }
        return self::$instance;
    }
    private function __construct() {}
    private function __clone() {}
}
