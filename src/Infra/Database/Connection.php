<?php

namespace App\Infra\Database;

use App\Infra\Env\Env;

class Connection
{
    private static ?\PDO $connection = null;

    public static function getInstance(): \PDO
    {
        if (self::$connection === null) {
            $host = Env::getString('DB_HOST') ?? 'localhost';
            $user = Env::getString('DB_USER') ?? 'root';
            $password = Env::getString('DB_PASSWORD') ?? '';
            $database = Env::getString('DB_NAME') ?? 'blog_db';
            $port = Env::getString('DB_PORT') ?? 3306;

            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

            try {
                self::$connection = new \PDO($dsn, $user, $password, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (\PDOException $e) {
                throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
