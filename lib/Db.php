<?php

defined('DB_NAME') || define('DB_NAME', 'ticket');

class Db
{
    protected static $db;

    public static function getDb()
    {
        if (!self::$db) {
            $pdo = new PDO('mysql:host=localhost;port=3306;dbname=' . DB_NAME . ';charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db = $pdo;
        }
        
        return self::$db;
    }
}
