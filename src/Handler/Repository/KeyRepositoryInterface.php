<?php

namespace App\Handler\Repository;

use App\Handler\VO\KeyDTO;
use App\Handler\VO\KeyValueVO;

interface KeyRepositoryInterface
{
    public function findOne(string $condition, string $key = 'key'): ?KeyValueVO;

    public function findMany($condition, string $key = 'type', int $limit = 12, int $offset = 0): array;

    public function create(KeyDTO $keyDTO): KeyValueVO;

    public function change(string $key, KeyDTO $keyDTO): ?KeyValueVO;

    public function findGroupedTypes(int $limit = 12, int $offset = 0): array;

    public function paginate(string $condition, string $key = 'type', int $limit = 12, int $offset = 0): array;

    public function remove(string $condition): bool;

    public function searchByTypeAndTag(string $type, string $tag, int $limit = 12, int $offset = 0): array;
}