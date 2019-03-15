<?php

namespace App\Handler\Repository;

interface KeyTagRepositoryInterface
{
    public function findTagsByKey(string $key): array;

    public function findKeysByTag(string $tag): array;
}