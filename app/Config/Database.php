<?php
namespace Adityawangsaa\LoginPhpManagementV1\Config;

class Database {
    private static ?\PDO $pdo = NULL;

    public static function getConnection(string $env = 'test')
    {
        if (self::$pdo == NULL) {
            require_once __DIR__ . '/../../config/Database.php';
            $config = getConfigDatabase();
            self::$pdo = new \PDO (
                $config['database'][$env]['url'],
                $config['database'][$env]['username'],
                $config['database'][$env]['password']
            );

            // Disarankan tambahkan opsi error mode
            self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }

    public static function beginTransaction()
    {
        self::$pdo->beginTransaction();
    }

    public static function commitTransaction()
    {
        self::$pdo->commit();
    }

    public static function rollbackTransaction()
    {
        self::$pdo->rollBack();
    }
}