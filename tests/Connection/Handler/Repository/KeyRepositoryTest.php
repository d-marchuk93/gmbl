<?php
/**
 * Created by PhpStorm.
 * User: yuriiastahov
 * Date: 10.12.18
 * Time: 16:02
 */

namespace Connection\Handler\Repository;


use App\Connection\Builder\BuilderInterface;
use App\Connection\ConnectInterface;
use App\Exception\Connection\DataNotCreatedException;
use App\Exception\Fetcher\InvalidData;
use App\Handler\Repository\KeyRepository;
use App\Handler\Repository\KeyRepositoryInterface;
use App\Handler\VO\KeyDTO;
use App\Handler\VO\KeyValueVO;
use PHPUnit\Framework\TestCase;

class KeyRepositoryTest extends TestCase
{
    private $builder;
    private $connect;

    public function setUp()
    {
        $this->connect = $this->createMock(ConnectInterface::class);
        $this->builder = $this->createMock(BuilderInterface::class);
    }

    public function testCreateInstance()
    {
        $repository = new KeyRepository($this->connect, $this->builder);
        $this->assertTrue($repository instanceof KeyRepositoryInterface);
    }

    public function testFindOne()
    {
        $shKey = sha1('key');
        $data = [
            'key' => $shKey,
            'type' => 'type',
            'data' => json_encode([
                'data' => 'data',
                'key' => 'key'
            ])
        ];
        $this->connect->method('simpleFetchOne')
            ->willReturn($data);
        $repository = new KeyRepository($this->connect, $this->builder);
        $vo = $repository->findOne('condition');
        $this->assertTrue($vo instanceof KeyValueVO);
        $this->assertEquals($vo->getType(), 'type');
        $this->assertEquals($vo->getData(), 'data');
        $this->assertEquals($vo->getKey(), $shKey);
    }

    public function fieldsProvider()
    {
        return [['key'], ['type'], ['data']];
    }

    /**
     * @dataProvider fieldsProvider
     */
    public function testExceptionWhileDataDoesNotExists($declineField)
    {
        $shKey = sha1('key');
        $data = [
            'key' => $shKey,
            'type' => 'type',
            'data' => 'data'
        ];
        unset($data[$declineField]);
        $this->expectException(InvalidData::class);
        $this->connect->method('simpleFetchOne')
            ->willReturn($data);
        $repository = new KeyRepository($this->connect, $this->builder);
        $repository->findOne('condition');
    }

    public function testExceptionWhileInvalidData()
    {
        $shKey = sha1('key');
        $data = [
            'key' => $shKey,
            'type' => 'type',
            'data' => 'data'
        ];
        $this->expectException(InvalidData::class);
        $this->connect->method('simpleFetchOne')
            ->willReturn($data);
        $repository = new KeyRepository($this->connect, $this->builder);
        $repository->findOne('condition');
    }

    public function testFindMany()
    {
        $shKey = sha1('key');
        $data = [
            'key' => $shKey,
            'type' => 'type',
            'data' => json_encode([
                'data' => 'data',
                'key' => 'key'
            ])
        ];
        $this->connect->method('simpleFetchMany')
            ->willReturn([$data]);
        $repository = new KeyRepository($this->connect, $this->builder);
        $collection = $repository->findMany('condition');
        $this->assertTrue(is_array($collection));
        $item = array_shift($collection);
        $this->assertTrue($item instanceof KeyValueVO);
        $this->assertEquals($item->getType(), 'type');
        $this->assertEquals($item->getData(), 'data');
        $this->assertEquals($item->getKey(), $shKey);
    }

    /**
     * @dataProvider fieldsProvider
     */
    public function testExceptionWhileInvalidDataOnFetchAll($excludeField)
    {
        $shKey = sha1('key');
        $data = [
            'key' => $shKey,
            'type' => 'type',
            'data' => 'data'
        ];
        unset($data[$excludeField]);
        $this->connect->method('simpleFetchMany')
            ->willReturn([$data]);
        $repository = new KeyRepository($this->connect, $this->builder);
        $this->expectException(InvalidData::class);
        $repository->findMany('condition');
    }

    public function testCreating()
    {
        $shKey = sha1('key');
        $data = [
            'key' => $shKey,
            'type' => 'type',
            'data' => json_encode([
                'data' => 'data',
                'key' => 'key'
            ])
        ];
        $this->connect->method('simpleFetchOne')
            ->willReturn($data);

        $repository = new KeyRepository($this->connect, $this->builder);
        $vo = $repository->create(KeyDTO::create('', '', []));
        $this->assertEquals($vo->getKey(), $shKey);
        $this->assertEquals($vo->getType(), 'type');
        $this->assertEquals($vo->getData(), 'data');
    }

    public function testFailingCreating()
    {
        $this->connect->method('simpleFetchOne')
            ->willReturn([]);
        $this->expectException(DataNotCreatedException::class);
        $repository = new KeyRepository($this->connect, $this->builder);
        $repository->create(KeyDTO::create('', '', []));
    }

    public function testChange()
    {
        $shKey = sha1('key');
        $data = [
            'key' => $shKey,
            'type' => 'type',
            'data' => json_encode([
                'data' => 'data',
                'key' => 'key'
            ])
        ];
        $this->connect->method('simpleFetchOne')->willReturn($data);
        $this->connect->method('simpleChange')->willReturn($shKey);

        $repository = new KeyRepository($this->connect, $this->builder);
        $vo = $repository->change('key', KeyDTO::create('', '', []));
        $this->assertEquals($vo->getKey(), $shKey);
        $this->assertEquals($vo->getType(), 'type');
        $this->assertEquals($vo->getData(), 'data');
    }

    public function testChangeReturnsNullWhileNotFoundResult()
    {
        $this->connect->method('simpleChange')->willReturn(null);
        $repository = new KeyRepository($this->connect, $this->builder);
        $vo = $repository->change('key',KeyDTO::create('', '', []));
        $this->assertNull($vo);
    }
}