<?php
namespace App\Models\Repositories;

use PDO;
use PDOException;

/**
 * Singleton poskytující sdílené PDO připojení k databázi.
 * Odděluje správu připojení od repository implementací.
 */
class Databaze
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';charset=utf8',
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                error_log('Chyba připojení k DB: ' . $e->getMessage());
                throw new \RuntimeException('Databáze není dostupná.');
            }
        }
        return self::$instance;
    }
}
