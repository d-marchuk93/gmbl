<?php

namespace App\Handler;

use App\Handler\Repository\KeyRepositoryInterface;
use App\Handler\Repository\KeyTagRepositoryInterface;
use App\Handler\VO\KeyDTO;
use App\Handler\VO\KeyValueVO;

class FetchService implements FetchServiceInterface
{
    /** @var KeyRepositoryInterface */
    private $keyRepository;
    /** @var KeyTagRepositoryInterface */
    private $keyTagRepository;

    /**
     * FetchService constructor.
     * @param KeyRepositoryInterface $keyRepository
     * @param KeyTagRepositoryInterface $keyTagRepository
     */
    public function __construct(
        KeyRepositoryInterface $keyRepository,
        KeyTagRepositoryInterface $keyTagRepository
    ) {
        $this->keyRepository = $keyRepository;
        $this->keyTagRepository = $keyTagRepository;
    }

    /**
     * @param string $key
     * @return KeyValueVO|null
     */
    public function fetchByKey(string $key):?KeyValueVO
    {
        $vo = $this->keyRepository->findOne(sha1($key));
        if ($vo !== null) {
            $tags = $this->keyTagRepository->findTagsByKey(sha1($key));
            $vo->setTags($tags);
        }

        return $vo;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function fetchByKeys(array $keys): array
    {
        $searchKeys = array_map(function($key) {
            return sha1($key);
        }, $keys);

        return $this->keyRepository->findMany($searchKeys, 'key', 1000, 0);
    }

    /**
     * @param string $key
     * @param string $type
     * @param $data
     * @return KeyValueVO|null
     */
    public function modifyData(string $key, string $type, $data):?KeyValueVO
    {
        $searchKey = $key . $type;

        $keyValueVO = $this->fetchByKey($searchKey);
        if (!$keyValueVO) {
            return null;
        }

        $keyDTO = KeyDTO::create($key, $type, $data);
        return $this->keyRepository->change(sha1($searchKey), $keyDTO);
    }

    /**
     * @param string $type
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function fetchByType(string $type, int $limit = 12, $offset  = 0): array
    {
        return $this->keyRepository->findmany($type, 'type', $limit, $offset);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function fetchTypes(int $limit, $offset = 0): array
    {
        return $this->keyRepository->findGroupedTypes($limit, $offset);
    }

    /**
     * @param string $key
     * @param string $type
     * @param $data
     * @return KeyValueVO
     */
    public function saveData(string $key, string $type, $data): KeyValueVO
    {
        return $this->keyRepository->create(KeyDTO::create($key, $type, $data));
    }

    /**
     * @param string $type
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function paginate(string $type, int $limit = 12, $offset = 0): array
    {
        return $this->keyRepository->paginate($type, 'type', $limit, $offset);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function removeByKey(string $key): bool
    {
        return $this->keyRepository->remove(sha1($key));
    }

    /**
     * @param string $tag
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function fetchByTag(string $tag, int $limit = 12, $offset = 0): array
    {
        $tagsVOCollection = $this->keyTagRepository->findKeysByTag($tag);
        $keys = [];

        foreach ($tagsVOCollection as $tagKeyVO) {
            $keys[] = $tagKeyVO->getKey();
        }
        $keysVOCollection = $this->keyRepository->findMany($keys, 'key', $limit, $offset);

        return $keysVOCollection;
    }

    /**
     * @param string $type
     * @param string $tag
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \App\Exception\Fetcher\InvalidData
     */
    public function fetchByTypeAndTag(string $type, string $tag, int $limit = 12, $offset = 0): array
    {
        $collection = $this->keyRepository->searchByTypeAndTag($type, $tag, $limit, $offset);
        $keys = [];
        foreach ($collection as $item) {
            $keys[] = KeyValueVO::createForFetch($item);
        }

        return $keys;
    }
}