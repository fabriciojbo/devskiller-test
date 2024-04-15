<?php

namespace App\Services;


use Illuminate\Redis\Connections\Connection;

class ViewCounter
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function increment(string $pageId): void
    {
        $key = "page-views:{$pageId}";
        $this->connection->incr($key);
    }

    public function reset(string $pageId): void
    {
        $key = "page-views:{$pageId}";
        $this->connection->set($key, 0);
    }

    public function count(string $pageId): int
    {
        $key = "page-views:{$pageId}";
        return $this->connection->get($key) ?? 0;
    }
}
