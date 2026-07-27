<?php

namespace EventLab\Database\Helpers;

use PDO;
use PDOStatement;

class QueryHelper
{
    public function __construct(
        private PDO $pdo
    ) {
    }

    /**
     * Prepares and executes any raw SQL query.
     */
    public function raw(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * Executes an INSERT query.
     *
     * @return int|string returns the last inserted ID, or 0 if none
     */
    public function insert(string $table, array $data): int|string
    {
        $columns      = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        $this->raw($sql, $data);

        return $this->pdo->lastInsertId();
    }

    /**
     * Executes an UPDATE query.
     *
     * @return int the number of affected rows
     */
    public function update(string $table, array $data, array $where): int
    {
        $setParts = [];
        $params   = [];

        foreach ($data as $column => $value) {
            $setParts[]              = "{$column} = :set_{$column}";
            $params["set_{$column}"] = $value;
        }

        $whereParts = [];
        foreach ($where as $column => $value) {
            $whereParts[]              = "{$column} = :where_{$column}";
            $params["where_{$column}"] = $value;
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $setParts),
            implode(' AND ', $whereParts)
        );

        return $this->raw($sql, $params)->rowCount();
    }

    /**
     * Executes a DELETE query.
     *
     * @return int the number of affected rows
     */
    public function delete(string $table, array $where): int
    {
        $whereParts = [];
        $params     = [];

        foreach ($where as $column => $value) {
            $whereParts[]              = "{$column} = :where_{$column}";
            $params["where_{$column}"] = $value;
        }

        $sql = sprintf('DELETE FROM %s WHERE %s', $table, implode(' AND ', $whereParts));

        return $this->raw($sql, $params)->rowCount();
    }

    /**
     * Fetches a single row as an associative array.
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->raw($sql, $params)->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Fetches all matching rows as an array.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->raw($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}
