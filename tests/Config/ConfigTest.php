<?php

namespace Config;


use App\Config\Config;
use App\Config\ConnectBDConfigGetInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

class ConfigTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $env = new Dotenv();
        $env->load(__DIR__ . '/.env.test');
        parent::__construct($name, $data, $dataName);
    }

    public function testCreateConfig()
    {
        $config = Config::create();
        $this->assertTrue($config instanceof ConnectBDConfigGetInterface);
    }

    public function testConfigValues()
    {
        $config = Config::create();
        $this->assertEquals($config->getTableName(), 'testTable');
        $this->assertEquals($config->getConnectionPort(), 3306);
        $this->assertEquals($config->getConnectionDataBase(), 'testDB');
        $this->assertEquals($config->getConnectionPassword(), 'testPassword');
        $this->assertEquals($config->getConnectionUser(), 'testUser');
        $this->assertEquals($config->getConnectionHost(), 'testHost');
    }

    public function testSingleConfig()
    {
        $env = new Dotenv();
        $env->load(__DIR__ . '/.env.test');
        $config = Config::create();
        unset($config);
        $config = Config::create();
        $this->assertEquals($config->getTableName(), 'testTable');
        $this->assertEquals($config->getConnectionPort(), 3306);
        $this->assertEquals($config->getConnectionDataBase(), 'testDB');
        $this->assertEquals($config->getConnectionPassword(), 'testPassword');
        $this->assertEquals($config->getConnectionUser(), 'testUser');
        $this->assertEquals($config->getConnectionHost(), 'testHost');

    }
}