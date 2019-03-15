<?php
namespace App\Exception\Connection;

class ConnectException extends \Exception
{
    protected $message = "Error Connection  refused";

    protected $code = 1;
}