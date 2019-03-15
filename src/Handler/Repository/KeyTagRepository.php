<?php

namespace App\Handler\Repository;

use App\Connection\Builder\BuilderInterface;
use App\Connection\ConnectInterface;
use App\Connection\Mysql\Connect;
use App\Handler\VO\KeyTagVO;

class KeyTagRepository implements KeyTagRepositoryInterface
{

    /** @var ConnectInterface */
    private $connect;
    /** @var BuilderInterface */
    private $builder;

    /**
     * KeyTagRepository constructor.
     * @param ConnectInterface $connect
     * @param BuilderInterface $builder
     */
    public function __construct(
        ConnectInterface $connect,
        BuilderInterface $builder
    ) {
        $this->connect = $connect;
        $this->builder = $builder;
    }

    /**
     * @param string $key
     * @return array
     */
    public function findTagsByKey(string $key): array
    {
        $sql = $this->builder->createBuilder()
            ->from('propertiesTags')
            ->where([
                'property_key' => $key
            ])->getSQLQuery();
        $collection = $this->connect->simpleFetchMany($sql);
        $result = [];
        foreach ($collection as $item) {
            $result[] = KeyTagVO::create($item);
        }

        return $result;
    }

    /**
     * @param string $tag
     * @return array
     */
    public function findKeysByTag(string $tag): array
    {
        $sql = $this->builder->createBuilder()
            ->from('propertiesTags')
            ->where([
                'tag' => $tag
            ])->getSQLQuery();
        $collection = $this->connect->simpleFetchMany($sql);
        $result = [];
        foreach ($collection as $item) {
            $result[] = KeyTagVO::create($item);
        }

        return $result;
    }
}