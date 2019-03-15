<?php

namespace Connection\Connect;


use App\Connection\ConnectInterface;
use App\Connection\Mysql\Connect;
use App\Connection\Mysql\Connection\ConnectionInterface;
use App\Exception\Connection\ConnectException;
use PHPUnit\Framework\TestCase;

class ConnectTest extends TestCase
{
    /** @var ConnectionInterface */
    private $connection;

    public function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->connection = $this->createMock(ConnectionInterface::class);
    }

    public function testFailingCreating()
    {
        $this->connection->method('getErrorCode')
            ->willReturn(2);
        $this->expectException(ConnectException::class);
        Connect::getConnection($this->connection);
    }

    public function testCreatingInstance()
    {
        $connect = Connect::getConnection($this->connection);
        $this->assertTrue($connect instanceof ConnectInterface);
    }

}