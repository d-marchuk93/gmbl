<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 14.03.19
 * Time: 13:30
 */

namespace App\Handler;

use App\Handler\VO\KeyValueVO;

interface FetchServiceInterface
{
    public function fetchByKey(string $key): ?KeyValueVO;

    public function fetchByKeys(array $keys): array;

    public function fetchByType(string $type, int $limit = 12, $offset = 0): array;

    public function saveData(string $key, string $type, $data): KeyValueVO;

    public function modifyData(string $key, string $type, $data): ?KeyValueVO;

    public function fetchTypes(int $limit, $offset = 0);

    public function paginate(string $type, int $limit = 12, int $offset = 0): array;

    public function removeByKey(string $key): bool;

    public function fetchByTag(string $tag, int $limit = 12, $offset = 0): array;

    public function fetchByTypeAndTag(string $type, string $tag, int $limit = 12, $offset = 0): array;
}