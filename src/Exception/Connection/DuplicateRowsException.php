<?php

namespace App\Exception\Connection;

class DuplicateRowsException extends ConnectException
{
    protected $message = "Duplicate rows exception";

    protected $code = 1062;
}