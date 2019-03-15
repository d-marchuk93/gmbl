<?php

namespace App\Config;

class Config implements ConnectBDConfigGetInterface
{

    private static $instance;

    /** @var string*/
    private $connectionHost = '';
    /** @var int */
    private $connectionPort = 3306;
    /** @var string */
    private $connectionUser = '';
    /** @var string*/
    private $connectionPassword = '';
    /** @var string */
    private $connectionDataBase = '';
    /** @var string */
    private $tableName = '';

    public function __construct()
    {
        $this->connectionHost = (string)getenv('GUMBALL_MYSQL_CONNECTION_HOST');
        $this->connectionPort = (int)getenv('GUMBALL_MYSQL_CONNECTION_PORT');
        $this->connectionUser = (string)getenv('GUMBALL_MYSQL_CONNECTION_USER');
        $this->connectionPassword = (string)getenv('GUMBALL_MYSQL_CONNECTION_PASSWORD');
        $this->connectionDataBase = (string)getenv('GUMBALL_MYSQL_CONNECTION_DB');

        $this->tableName = (string)getenv('GUMBALL_MYSQL_TABLE_DEFAULT');
    }

    /**
     * @return Config
     */
    public static function create(): Config
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * @return string
     */
    public function getConnectionHost(): string
    {
        return $this->connectionHost;
    }

    /**
     * @param string $connectionHost
     */
    public function setConnectionHost(string $connectionHost)
    {
        $this->connectionHost = $connectionHost;
    }

    /**
     * @return int
     */
    public function getConnectionPort(): int
    {
        return $this->connectionPort;
    }

    /**
     * @param int $connectionPort
     */
    public function setConnectionPort(int $connectionPort)
    {
        $this->connectionPort = $connectionPort;
    }

    /**
     * @return string
     */
    public function getConnectionUser(): string
    {
        return $this->connectionUser;
    }

    /**
     * @param string $connectionUser
     */
    public function setConnectionUser(string $connectionUser)
    {
        $this->connectionUser = $connectionUser;
    }

    /**
     * @return string
     */
    public function getConnectionPassword(): string
    {
        return $this->connectionPassword;
    }

    /**
     * @param string $connectionPassword
     */
    public function setConnectionPassword(string $connectionPassword)
    {
        $this->connectionPassword = $connectionPassword;
    }

    /**
     * @return string
     */
    public function getConnectionDataBase(): string
    {
        return $this->connectionDataBase;
    }

    /**
     * @param string $connectionDataBase
     */
    public function setConnectionDataBase(string $connectionDataBase)
    {
        $this->connectionDataBase = $connectionDataBase;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;
    }
}