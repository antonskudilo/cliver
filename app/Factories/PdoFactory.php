<?php

namespace App\Factories;

use PDO;

final class PdoFactory
{
    /**
     * @param string $host
     * @param string $port
     * @param string $database
     * @param string $user
     * @param string $password
     * @param string $charset
     * @return PDO
     */
    public static function make(
        string $host,
        string $port,
        string $database,
        string $user,
        string $password,
        string $charset = 'utf8mb4'
    ): PDO {
        return new PDO(
            dsn: "mysql:host={$host};port={$port};dbname={$database};charset={$charset}",
            username: $user,
            password: $password,
            options: [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
}
