<?php

use App\Config\Config;
use App\Connection\Builder\MysqlBuilder;
use App\Connection\Mysql\Connect;
use App\Connection\Mysql\Connection\MysqliConnection;
use App\Exception\Connection\DuplicateRowsException;
use App\Handler\FetchService;
use App\Handler\FetchServiceInterface;
use App\Handler\Repository\KeyRepository;
use App\Handler\Repository\KeyTagRepository;
use App\Handler\VO\KeyValueVO;

if (!function_exists('gumballCreateFetcherService')) {
    function gumballCreateFetcherService(): FetchServiceInterface
    {
        $config = Config::create();
        $connection = new MysqliConnection($config);
        $connect = Connect::getConnection($connection);
        $builder = new MysqlBuilder($config);
        $keyRepository = new KeyRepository($connect, $builder);
        $keyTagRepository = new KeyTagRepository($connect, $builder);

        return new FetchService($keyRepository, $keyTagRepository);
    }
}

if (!function_exists('gumballGetValue')) {
    function gumballGetValue(string $key, string $type)
    {
        $fetcher = gumballCreateFetcherService();
        return $fetcher->fetchByKey($key . $type);
    }
}

if (!function_exists('gumballGetValues')) {
    function gumballGetValues(array $keys, string $type)
    {
        $fetcher = gumballCreateFetcherService();
        $keys = array_map(function(string $key) use ($type) {
           return $key . $type;
        }, $keys);

        return $fetcher->fetchByKeys($keys);
    }
}

if (!function_exists('gumballChangeValue')) {
    function gumballChangeValue(string $key, string $type, $data = null): ?KeyValueVO
    {
        $putter = gumballCreateFetcherService();
        return $putter->modifyData($key, $type, $data);
    }
}

if (!function_exists('gumballPutValue')) {
    function gumballPutValue(string $key, string $type, $data, bool $waitingException = true):?KeyValueVO
    {
        $putter = gumballCreateFetcherService();
        try {
            return $putter->saveData($key, $type, $data);
        } catch (DuplicateRowsException $duplicateRowsException) {
            if ($waitingException) {
                throw $duplicateRowsException;
            }
            return null;
        }
    }
}

if (!function_exists('gumballRemoveValue')) {
    function gumballRemoveValue(string $key, string $type)
    {
        $fetcher = gumballCreateFetcherService();
        return $fetcher->removeByKey($key.$type);
    }
}

if (!function_exists('gumballTypeTagSearch')) {
    function gumballTypeTagSearch(string $type, string $tag, int $limit = 12, int $offset = 0)
    {
        $fetcher = gumballCreateFetcherService();
        return $fetcher->fetchByTypeAndTag($type, $tag, $limit, $offset);
    }
}

if (!function_exists('gumballTypeSearch')) {
    function gumballTypeSearch(string $type, int $limit = 12, int $offset = 0) {
        $fetcher = gumballCreateFetcherService();
        return $fetcher->fetchByType($type, $limit, $offset);
    }
}

if (!function_exists('gumballGetTypeList')) {
    function gumballGetTypeList(int $limit = 12, int $offset = 0)
    {
        $fetcher = gumballCreateFetcherService();
        return $fetcher->fetchTypes($limit, $offset);
    }
}

if (!function_exists('gumballPaginate')) {
    function gumballPaginate(string $type, int $limit = 12, int $offset = 0)
    {
        $fetcher = gumballCreateFetcherService();
        return $fetcher->paginate($type, $limit, $offset);
    }
}

