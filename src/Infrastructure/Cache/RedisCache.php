<?php

namespace App\Infrastructure\Cache;

use App\Domain\Cache\CacheInterface;
use Predis\Client;

class RedisCache implements CacheInterface
{
    private Client $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    public function get(string $key): ?string
    {
        return $this->redis->get($key);
    }

    public function set(string $key, string $value, int $ttl = 3600): void
    {
        $this->redis->setex($key, $ttl, $value);
    }

    public function delete(string $key): void
    {
        $this->redis->del($key);
    }
}
