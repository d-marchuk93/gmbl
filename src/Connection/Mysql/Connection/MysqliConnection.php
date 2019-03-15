<?php

namespace App\Connection\Mysql\Connection;

use App\Config\ConnectBDConfigGetInterface;

class MysqliConnection implements ConnectionInterface
{
    /** @var \mysqli */
    private $connection;
    /** @var ConnectBDConfigGetInterface */
    private $config;

    public function __construct(ConnectBDConfigGetInterface $config)
    {
        $this->connection = new \mysqli(
          $config->getConnectionHost(),
          $config->getConnectionUser(),
          $config->getConnectionPassword(),
          $config->getConnectionDataBase(),
          $config->getConnectionPort()
        );

        $this->config = $config;
    }

    public function getConnection(): \mysqli
    {
        return $this->connection;
    }

    public function closeConnection()
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function execute(string $sql)
    {
        mysqli_escape_string($this->connection, $sql);

        return $this->connection->query($sql);
    }

    public function getErrorCode(): int
    {
        return (int)$this->connection->errno;
    }

    public function getErrorMessage(): string
    {
        return (string)$this->connection->error;
    }

    public function getConfig(): ConnectBDConfigGetInterface
    {
        return $this->config;
    }
}