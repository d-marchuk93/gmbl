<?php

namespace App\Connection\Builder;

use App\Config\ConnectBDConfigGetInterface;

class MysqlBuilder implements BuilderInterface
{
    /** @var string */
    private $select = '*';
    /** @var string */
    private $from = '';
    /** @var array */
    private $conditions = [];
    /** @var int */
    private $limit = 12;
    /** @var int */
    private $offset = 0;
    /** @var string */
    private $group = '';
    /** @var ConnectBDConfigGetInterface */
    private $config;
    /** @var array */
    private $join = [];

    /**
     * MysqlBuilder constructor.
     * @param ConnectBDConfigGetInterface $config
     */
    public function __construct(ConnectBDConfigGetInterface $config)
    {
        $this->config = $config;
        $this->from = $config->getTableName();
        $this->cleanUp();
    }

    public function __clone()
    {
        $this->cleanUp();
    }

    /**
     * @return BuilderInterface
     */
    public function cleanUp(): BuilderInterface
    {
        $this->select = '*';
        $this->conditions = [];
        $this->from = $this->config->getTableName();
        $this->limit = 12;
        $this->offset = 0;
        $this->group = '';
        $this->join = [];

        return $this;
    }

    /**
     * @return BuilderInterface
     */
    public function createBuilder(): BuilderInterface
    {
        return $this->cleanUp();
    }

    /**
     * @param string $select
     * @return BuilderInterface
     */
    public function select(string $select): BuilderInterface
    {
        $this->select = $select;

        return $this;
    }

    /**
     * @param string $from
     * @return BuilderInterface
     */
    public function from(string $from): BuilderInterface
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param array $conditions
     * @return BuilderInterface
     */
    public function where(array $conditions): BuilderInterface
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return BuilderInterface
     */
    public function limit(int $limit, int $offset = 0): BuilderInterface
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param string $group
     * @return BuilderInterface
     */
    public function group(string $group): BuilderInterface
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @param string $join
     * @return BuilderInterface
     */
    public function addJoin(string $join): BuilderInterface
    {
        $this->join[] = $join;

        return $this;
    }

    public function getSQLQuery(): string
    {
        $conditionsString = '';
        if (!empty($this->conditions)) {
            $conditionsArray = [];

            foreach ($this->conditions as $field => $value) {
                if (is_array($value)) {
                    $values = [];
                    foreach ($value as $item) {
                        $values[] = "'{$item}'";
                    }
                    $conditionsArray[] = "`{$field}` IN (" . implode(',', $values) . ")";
                    unset($values);
                    continue;
                }
                $conditionsArray[] = "`$field` = '{$value}'";
            }

            if (!empty($conditionsArray)) {
                $conditionsString = ' WHERE ' . implode(' AND ', $conditionsArray);
            }
        }
        $group = '';
        if ($this->group) {
            $group = " GROUP BY {$this->group}";
        }

        $joinString = '';
        if (!empty($this->join)) {
            $joinString = implode(' ', $this->join);
        }

        $sql = "SELECT {$this->select} " .
            " FROM {$this->from} " .
            " {$joinString} " .
            " {$conditionsString} " .
            " {$group} " .
            " LIMIT {$this->limit} " .
            " OFFSET {$this->offset};";
        $sql = preg_replace('|\s+|', ' ', $sql);

        return $sql;
    }
}