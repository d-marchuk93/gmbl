<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 14.03.19
 * Time: 10:02
 */

namespace App\Connection\Mysql;

use App\Connection\ConnectInterface;
use App\Connection\Mysql\Connection\ConnectionInterface;
use App\Exception\Connection\ConnectException;
use App\Exception\Connection\DuplicateRowsException;
use App\Exception\Connection\InvalidSQLSyntaxException;

class Connect implements ConnectInterface
{
    /** @var Connect */
    private static $instance;
    /** @var ConnectionInterface */
    private $connection;

    /**
     * Connect constructor.
     * @param ConnectionInterface $connection
     * @throws ConnectException
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        if ($this->connection->getErrorCode()) {
            throw new ConnectException(
                $this->connection->getErrorMessage(),
                $this->connection->getErrorCode()
            );
        }
    }

    /**
     * @param ConnectionInterface $connection
     * @return ConnectInterface
     * @throws ConnectException
     */
    public static function getConnection(ConnectionInterface $connection): ConnectInterface
    {
        if (null === static::$instance) {
            static::$instance = new self($connection);
        }

        return static::$instance;
    }

    /**
     * @param array $data
     * @return bool
     * @throws DuplicateRowsException
     * @throws InvalidSQLSyntaxException
     */
    public function simplePut(array $data): bool
    {
        $columns = [];
        $values = [];
        foreach ($data as $column => $value) {
            $columns[] = "`$column`";
            if (is_array($value)) {
                $value = json_decode($value);
            }
            $value = mysqli_escape_string($this->connection->getConnection(), $value);
            $values[] = "'{$value}'";
        }

        $sql = "INSERT INTO `{$this->connection->getConfig()->getTableName()}` (" . implode(", ", $columns) . ") " .
            " VALUES (" . implode(", ", $values). ");";
        $this->executeQuery($sql);
    }

    /**
     * @param string $key
     * @param array $data
     * @return string|null
     * @throws DuplicateRowsException
     * @throws InvalidSQLSyntaxException
     */
    public function simpleChange(string $key, array $data): ?string
    {
        $conditions = [];
        foreach ($data as $column => $value) {
            if ($column === 'key') {
                continue;
            }
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $value = mysqli_escape_string($this->connection->getConnection(), $value);
            $conditions[] = " `{$column}` = '{$value}' ";
        }
        if (empty($conditions)) {
            return null;
        }
        $sql = "UPDATE `{$this->connection->getConfig()->getTableName()}`" .
            " SET " . implode(" , ", $conditions) .
            " WHERE `key`={$key};";
        $this->executeQuery($sql);
        return $key;
    }

    /**
     * @param string $sql
     * @return array
     * @throws DuplicateRowsException
     * @throws InvalidSQLSyntaxException
     */
    public function simpleFetchOne(string $sql): array
    {
        $result = $this->executeQuery($sql);
        if ($result->num_rows === 0) {
            return [];
        }
        return $result->fetch_assoc();
    }

    /**
     * @param string $sql
     * @return array
     * @throws DuplicateRowsException
     * @throws InvalidSQLSyntaxException
     */
    public function simpleFetchMany(string $sql): array
    {
        $result = $this->executeQuery($sql);
        if ($result->num_rows === 0) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @param string $sql
     * @return mixed
     * @throws DuplicateRowsException
     * @throws InvalidSQLSyntaxException
     */
    private function executeQuery(string $sql)
    {
        $result = $this->connection->execute($sql);
        if ($this->connection->getErrorCode() === 1062) {
            throw new DuplicateRowsException($this->connection->getErrorMessage());
        }

        if (!$result) {
            throw new InvalidSQLSyntaxException($this->connection->getErrorMessage(), $this->connection->getErrorCode());
        }

        return $result;
    }

    /**
     * @param string $key
     * @return bool
     * @throws DuplicateRowsException
     * @throws InvalidSQLSyntaxException
     */
    public function simpleRemove(string $key): bool
    {
        $sql = "DELETE FROM `{$this->connection->getConfig()->getTableName()}` WHERE `key`='{$key}'";
        $this->executeQuery($sql);
        return true;
    }

    /**
     * @param string $key
     * @param string $tag
     * @return string
     * @throws DuplicateRowsException
     * @throws InvalidSQLSyntaxException
     */
    public function simpleAddTag(string $key, string $tag): string
    {
        $sql = "INSERT INTO `propertiesTags` (`property_key`, `tag`) " .
            " VALUES ('{$key}', '{$tag}') ON DUPLICATE KEY UPDATE `property` = '{$key}', `tag` = '$tag'";
        $this->executeQuery($sql);

        return $key;
    }

    public function __destruct()
    {
        if ($this->connection !== null) {
            $this->connection->closeConnection();
        }
    }
}