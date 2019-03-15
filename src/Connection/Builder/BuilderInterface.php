<?php

namespace App\Connection\Builder;

interface BuilderInterface
{
    public function createBuilder(): BuilderInterface;

    public function select(string $select): BuilderInterface;

    public function from(string $from): BuilderInterface;

    public function where(array $conditions): BuilderInterface;

    public function limit(int $limit, int $offset = 0): BuilderInterface;

    public function group(string $group): BuilderInterface;

    public function addJoin(string $join): BuilderInterface;

    public function getSQLQuery(): string;
}