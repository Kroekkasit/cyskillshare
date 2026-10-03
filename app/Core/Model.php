<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base model — all queries go through the shared Database PDO layer.
 */
abstract class Model
{
    /**
     * @param array<int|string, mixed> $params
     * @return array<string, mixed>|null
     */
    protected static function fetch(string $sql, array $params = []): ?array
    {
        return Database::fetch($sql, $params);
    }

    /**
     * @param array<int|string, mixed> $params
     * @return list<array<string, mixed>>
     */
    protected static function fetchAll(string $sql, array $params = []): array
    {
        return Database::fetchAll($sql, $params);
    }

    /**
     * @param array<int|string, mixed> $params
     */
    protected static function execute(string $sql, array $params = []): int
    {
        return Database::execute($sql, $params);
    }

    protected static function lastInsertId(): string
    {
        return Database::lastInsertId();
    }
}
