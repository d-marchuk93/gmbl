<?php

namespace Connection\Handler;

use App\Exception\Connection\DataNotCreatedException;
use App\Handler\FetchService;
use App\Handler\Repository\KeyRepositoryInterface;
use App\Handler\Repository\KeyTagRepositoryInterface;
use App\Handler\VO\KeyDTO;
use App\Handler\VO\KeyValueVO;
use PHPUnit\Framework\TestCase;

class FetcherTest extends TestCase
{
    private $repository;
    private $tagRepository;

    public function setUp()
    {
        $this->repository = $this->createMock(KeyRepositoryInterface::class);
        $this->tagRepository = $this->createMock(KeyTagRepositoryInterface::class);
    }

    public function testFetchByKey()
    {
        $key = sha1('key');
        $vo = new KeyValueVO();
        $vo->setKey($key);
        $vo->setType('type');
        $vo->setData('data');
        $this->repository->method('findOne')
            ->willReturn($vo);
        $fetcher = new FetchService($this->repository, $this->tagRepository);
        $keyVO = $fetcher->fetchByKey('key');
        $this->assertTrue($keyVO instanceof KeyValueVO);
        $this->assertEquals($keyVO->getData(), $vo->getData());
        $this->assertEquals($keyVO->getType(), $vo->getType());
        $this->assertEquals($keyVO->getKey(), $vo->getKey());
    }

    public function testEmptyFetchByKey()
    {
        $this->repository->method('findOne')
            ->willReturn(null);
        $fetcher = new FetchService($this->repository, $this->tagRepository);
        $keyVO = $fetcher->fetchByKey('key');
        $this->assertNull($keyVO);
    }

    public function testFetchManyByType()
    {
        $this->repository->method('findMany')
            ->willReturn([]);
        $fetcher = new FetchService($this->repository, $this->tagRepository);
        $collection = $fetcher->fetchByType('type');
        $this->assertTrue(is_array($collection));
    }

    public function testFetchManyByTypeWithData()
    {
        $key = sha1('key');
        $vo = new KeyValueVO();
        $vo->setKey($key);
        $vo->setType('type');
        $vo->setData('data');
        $this->repository->method('findMany')
            ->willReturn([$vo]);
        $fetcher = new FetchService($this->repository, $this->tagRepository);
        $collection = $fetcher->fetchByType('type');
        $this->assertFalse(empty($collection));
        $item = array_shift($collection);
        $this->assertTrue($item instanceof KeyValueVO);
        $this->assertEquals($item->getKey(), $vo->getKey());
        $this->assertEquals($item->getType(), $vo->getType());
        $this->assertEquals($item->getData(), $vo->getData());
    }

    public function testFetchingTypes()
    {
        $this->repository->method('findGroupedTypes')
            ->willReturn(['type1', 'type2']);
        $fetcher = new FetchService($this->repository, $this->tagRepository);
        $types = $fetcher->fetchTypes(12, 0);
        $this->assertIsArray($types);
        $this->assertEquals(count($types), 2);
    }

    public function testSavingNewData()
    {
        $key = sha1('key');
        $vo = new KeyValueVO();
        $vo->setKey($key);
        $vo->setType('type');
        $vo->setData('data');
        $this->repository->method('create')->willReturn($vo);
        $fetcher = new FetchService($this->repository, $this->tagRepository);
        $keyVO = $fetcher->saveData('key', 'type', []);
        $this->assertTrue($keyVO instanceof KeyValueVO);
        $this->assertEquals($keyVO->getKey(),$vo->getKey());
        $this->assertEquals($keyVO->getData(),$vo->getData());
        $this->assertEquals($keyVO->getType(),$vo->getType());
    }

    public function testFailWhileSavingNewData()
    {
        $this->repository->method('create')->will($this->throwException(new DataNotCreatedException()));
        $fetcher = new FetchService($this->repository, $this->tagRepository);
        $this->expectException(DataNotCreatedException::class);
        $fetcher->saveData('key', 'type', []);
    }

    public function testModifyData()
    {
        $key = sha1('key');
        $vo = new KeyValueVO();
        $vo->setKey($key);
        $vo->setType('type');
        $vo->setData('data');
        $this->repository->method('findOne')->willReturn($vo);
        $newVo = clone $vo;
        $vo->setData('newData');
        $this->repository->method('change')->willReturn($newVo);
        $fetcher = new FetchService($this->repository, $this->tagRepository);
        $keyVO = $fetcher->modifyData('key','newData','type');
        $this->assertTrue($keyVO instanceof KeyValueVO);
        $this->assertEquals($keyVO->getKey(),$vo->getKey());
        $this->assertEquals($keyVO->getData(),$newVo->getData());
        $this->assertEquals($keyVO->getType(),$vo->getType());
    }

    public function testModifyDataWhenEmptyResult()
    {
        $this->repository->method('findOne')->willReturn(null);
        $fetcher = new FetchService($this->repository, $this->tagRepository);
        $keyVO = $fetcher->modifyData('key','newData','type');
        $this->assertNull($keyVO);
    }
}