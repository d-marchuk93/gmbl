<?php

namespace Connection\Builder;

use App\Config\ConnectBDConfigGetInterface;
use App\Connection\Builder\BuilderInterface;
use App\Connection\Builder\MysqlBuilder;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    /** @var ConnectBDConfigGetInterface */
    private $config;

    protected function setUp()
    {
        /** @var ConnectBDConfigGetInterface $config */
        $this->config = $this->createMock(ConnectBDConfigGetInterface::class);
        $this->config->method('getTableName')
            ->willReturn('table');
    }

    public function testCreatingBuilder()
    {
        $build = new MysqlBuilder($this->config);
        $this->assertTrue($build instanceof BuilderInterface);
    }

    public function testCreatingSQLQuuery()
    {
        $build = new MysqlBuilder($this->config);
        $sql = $build->createBuilder()
            ->limit(10, 0)
            ->getSQLQuery();
        $this->assertEquals('SELECT * FROM table LIMIT 10 OFFSET 0;', $sql);
    }

    public function testWhereCondition()
    {
        $build = new MysqlBuilder($this->config);
        $sql = $build->createBuilder()
            ->where(['field' => 'condition'])
            ->limit(10, 0)
            ->getSQLQuery();
        $this->assertEquals('SELECT * FROM table WHERE `field` = \'condition\' LIMIT 10 OFFSET 0;', $sql);
    }

    public function testLimitCondition()
    {
        $build = new MysqlBuilder($this->config);
        $sql = $build->createBuilder()
            ->limit(100, 40)
            ->getSQLQuery();
        $this->assertEquals('SELECT * FROM table LIMIT 100 OFFSET 40;', $sql);
    }

    public function testSelectCondition()
    {
        $build = new MysqlBuilder($this->config);
        $sql = $build->createBuilder()
            ->select('field')
            ->limit(10, 0)
            ->getSQLQuery();
        $this->assertEquals('SELECT field FROM table LIMIT 10 OFFSET 0;', $sql);
    }
}