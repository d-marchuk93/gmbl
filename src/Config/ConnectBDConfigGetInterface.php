<?php

namespace App\Config;

interface ConnectBDConfigGetInterface
{
    public function getConnectionHost(): string;

    public function getConnectionPort(): int;

    public function getConnectionUser(): string;

    public function getConnectionPassword(): string;

    public function getConnectionDataBase(): string;

    public function getTableName(): string;
}