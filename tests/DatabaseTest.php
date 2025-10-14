<?php
use Adityawangsaa\LoginPhpManagementV1\Config\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testDatabaseConnection()
    {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();
        self::assertEquals($connection1, $connection2);
    }

    public function testDatabaseConnectionSingletone()
    {
        $connection = Database::getConnection();
        self::assertNotNull($connection); 
    }
}