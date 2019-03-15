<?php

namespace App\Handler\Repository;

use App\Connection\Builder\BuilderInterface;
use App\Connection\ConnectInterface;
use App\Exception\Connection\DataNotCreatedException;
use App\Handler\VO\KeyDTO;
use App\Handler\VO\KeyValueVO;

class KeyRepository implements KeyRepositoryInterface
{
    /** @var ConnectInterface */
    private $connect;
    /** @var BuilderInterface */
    private $builder;

    /**
     * KeyRepository constructor.
     * @param ConnectInterface $connect
     * @param BuilderInterface $builder
     */
    public function __construct(ConnectInterface $connect, BuilderInterface $builder)
    {
        $this->connect = $connect;
        $this->builder = $builder;
    }

    /**
     * @param string $condition
     * @param string $key
     * @return KeyValueVO|null
     * @throws \App\Exception\Fetcher\InvalidData
     */
    public function findOne(string $condition, string $key = 'key'): ?KeyValueVO
    {
        $sqlQuery = $this->builder
            ->createBuilder()
            ->where([$key => $condition])
            ->getSQLQuery();
        $result = $this->connect->simpleFetchOne($sqlQuery);
        if (empty($result)) {
            return null;
        }
        $keyValueVO = KeyValueVO::createForFetch($result);
        return $keyValueVO;
    }

    /**
     * @param $condition
     * @param string $key
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \App\Exception\Fetcher\InvalidData
     */
    public function findMany($condition, string $key = 'type', int $limit = 12, int $offset = 0): array
    {
        $where = " WHERE `{$key}` = `{$condition}` ";
        if (is_array($condition) && !empty($condition)) {
            $condition = array_map(function($item) {
                return "'{$item}'";
            }, $condition);
            $where = " WHERE `{$key}` IN (" . implode(', ', $condition) . ")";
        }

        $sqlQuery = "SELECT properties.*, GROUP_CONCAT(propertiesTags.tag) as tag
            FROM properties
               LEFT JOIN propertiesTags ON propertiesTags.property_key = properties.`key`
               {$where}
               GROUP BY properties.key LIMIT {$limit} OFFSET {$offset};";
        $collection = $this->connect->simpleFetchMany($sqlQuery);
        $result = [];
        foreach ($collection as $item) {
            $result[] = KeyValueVO::createForFetch($item);
        }

        return $result;
    }

    /**
     * @param KeyDTO $keyDTO
     * @return KeyValueVO
     * @throws DataNotCreatedException
     * @throws \App\Exception\Fetcher\InvalidData
     */
    public function create(KeyDTO $keyDTO): KeyValueVO
    {
        $this->connect->simplePut($keyDTO->toArray());
        if ($keyDTO->getTag()) {
            $this->connect->simpleAddTag($keyDTO->getKey(), $keyDTO->getTag());
        }
        $key = $this->findOne($keyDTO->getKey());
        if (!$key) {
            throw new DataNotCreatedException();
        }
        return $key;
    }

    /**
     * @param string $key
     * @param KeyDTO $keyDTO
     * @return KeyValueVO|null
     * @throws \App\Exception\Fetcher\InvalidData
     */
    public function change(string $key, KeyDTO $keyDTO): ?KeyValueVO
    {
        $key = $this->connect->simpleChange($key, $keyDTO->toArray());
        if (!$key) {
            return null;
        }
        if ($keyDTO->getTag()) {
            $this->connect->simpleAddTag($keyDTO->getKey(), $keyDTO->getTag());
        }

        return $this->findOne($key);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findGroupedTypes(int $limit = 12, int $offset = 0): array
    {
        $sqlQuery = $this->builder->createBuilder()
            ->select('type')
            ->group('type')
            ->limit($limit, $offset)
            ->getSQLQuery();

        $collection = $this->connect->simpleFetchMany($sqlQuery);
        $result = [];
        foreach ($collection as $item) {
            $result[] = $item['type'];
        }

        return $result;
    }

    /**
     * @param string $condition
     * @param string $key
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \App\Exception\Fetcher\InvalidData
     */
    public function paginate(string $condition, string $key = 'type', int $limit = 12, int $offset = 0): array
    {
        $sqlQuery = $this->builder
            ->createBuilder()
            ->select('count(`key`) as total')
            ->where([$key => $condition])
            ->getSQLQuery();

        $result = $this->connect->simpleFetchOne($sqlQuery);
        $count = $result['total'];

        return [
            'total' => $count,
            'data' => $this->findMany($condition, $key, $limit, $offset)
        ];
    }

    /**
     * @param string $condition
     * @return bool
     */
    public function remove(string $condition): bool
    {
        return $this->connect->simpleRemove($condition);
    }

    /**
     * @param string $type
     * @param string $tag
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchByTypeAndTag(string $type, string $tag, int $limit = 12, int $offset = 0): array
    {
        $sqlQuery = $this->builder
            ->createBuilder()
            ->select('DISTINCT p.*')
            ->from('properties as p')
            ->addJoin('JOIN propertiesTags AS pt ON pt.property_key = p.key')
            ->where([
                'type' => $type,
                'tag' => $tag
            ])
            ->limit($limit, $offset)
            ->getSQLQuery();

        return $this->connect->simpleFetchMany($sqlQuery);
    }
}