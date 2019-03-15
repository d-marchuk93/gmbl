<?php

namespace App\Exception\Connection;

class InvalidSQLSyntaxException extends ConnectException
{
    protected $message = 'Incorrect sql syntax';

    protected $code = 2;
}