<?php

namespace App\Connection\Mysql\Connection;

use App\Config\ConnectBDConfigGetInterface;

interface ConnectionInterface
{
    public function getConnection();

    public function closeConnection();

    public function execute(string $sql);

    public function getErrorCode(): int;

    public function getErrorMessage(): string;

    public function getConfig(): ConnectBDConfigGetInterface;
}