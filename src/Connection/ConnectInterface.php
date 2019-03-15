<?php

namespace App\Connection;

use App\Connection\Mysql\Connection\ConnectionInterface;

interface ConnectInterface
{
    public static function getConnection(ConnectionInterface $connection): ConnectInterface;

    public function simplePut(array $data): bool;

    public function simpleChange(string $key, array $data): ?string;

    public function simpleFetchOne(string $sql): array;

    public function simpleFetchMany(string $sql): array;

    public function simpleRemove(string $key): bool;

    public function simpleAddTag(string $key, string $tag): string;
}