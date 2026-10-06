<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * PDO data layer with explicit parameter binding (prepare → bindValue → execute).
 * Never concatenates user input into SQL. Never exposes SQL/path details to clients.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = (string) config('database.host');
        $port = (int) config('database.port');
        $dbname = (string) config('database.database');
        $username = (string) config('database.username');
        $password = (string) config('database.password');
        $socket = (string) config('database.socket');
        $charset = (string) config('database.charset', 'utf8mb4');

        if ($username === '' || strtolower($username) === 'root') {
            ErrorHandler::log('Refusing database connection as root or empty username.');
            throw new RuntimeException('Database configuration error. Please try again later.');
        }

        if ($socket !== '') {
            $dsn = sprintf('mysql:unix_socket=%s;dbname=%s;charset=%s', $socket, $dbname, $charset);
        } else {
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbname, $charset);
        }

        try {
            self::$pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            self::$pdo->exec('SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci');
        } catch (PDOException $e) {
            // Full detail stays in server logs only — never returned to the client.
            ErrorHandler::log('Database connection failed: ' . $e->getMessage());
            throw new RuntimeException('Database connection failed. Please try again later.');
        }

        return self::$pdo;
    }

    /**
     * Prepare SQL, bind each parameter with typed bindValue(), then execute.
     * Equivalent safety model to MySQLi prepare() + bind_param() + execute().
     *
     * @param array<int|string, mixed> $params
     */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = self::connection()->prepare($sql);
            self::bindParams($stmt, $params);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            ErrorHandler::log('Database query failed: ' . $e->getMessage());
            throw new RuntimeException('A database error occurred. Please try again later.');
        }
    }

    /**
     * Bind parameters explicitly (PDO bindValue — same role as mysqli_stmt::bind_param).
     *
     * @param array<int|string, mixed> $params
     */
    private static function bindParams(PDOStatement $stmt, array $params): void
    {
        if ($params === []) {
            return;
        }

        $isList = array_is_list($params);
        $index = 1;

        foreach ($params as $key => $value) {
            $name = $isList ? $index : (is_int($key) ? $key + 1 : (str_starts_with((string) $key, ':') ? (string) $key : ':' . $key));
            $stmt->bindValue($name, $value, self::pdoType($value));
            $index++;
        }
    }

    private static function pdoType(mixed $value): int
    {
        return match (true) {
            $value === null => PDO::PARAM_NULL,
            is_bool($value) => PDO::PARAM_BOOL,
            is_int($value) => PDO::PARAM_INT,
            default => PDO::PARAM_STR,
        };
    }

    /**
     * @param array<int|string, mixed> $params
     * @return array<string, mixed>|null
     */
    public static function fetch(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /**
     * @param array<int|string, mixed> $params
     * @return list<array<string, mixed>>
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * @param array<int|string, mixed> $params
     */
    public static function execute(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }

    public static function lastInsertId(): string
    {
        return self::connection()->lastInsertId();
    }

    public static function beginTransaction(): void
    {
        self::connection()->beginTransaction();
    }

    public static function commit(): void
    {
        self::connection()->commit();
    }

    public static function rollBack(): void
    {
        $pdo = self::connection();
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }

    public static function ping(): bool
    {
        try {
            self::connection()->query('SELECT 1');
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
