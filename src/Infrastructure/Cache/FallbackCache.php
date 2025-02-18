<?php

namespace App\Infrastructure\Cache;

use App\Domain\Cache\CacheInterface;
use Predis\Client;
use Exception;

class FallbackCache implements CacheInterface
{
    private CacheInterface $primaryCache;
    private array $memoryCache = [];
    private bool $useMemory = false;

    public function __construct(Client $redis)
    {
        try {
            $this->primaryCache = new RedisCache($redis);
            $redis->ping();
        } catch (Exception $e) {
            $this->useMemory = true;
        }
    }

    public function get(string $key): ?string
    {
        if ($this->useMemory) {
            return $this->memoryCache[$key] ?? null;
        }

        try {
            return $this->primaryCache->get($key);
        } catch (Exception $e) {
            $this->useMemory = true;
            return $this->memoryCache[$key] ?? null;
        }
    }

    public function set(string $key, string $value, int $ttl = 3600): void
    {
        if ($this->useMemory) {
            $this->memoryCache[$key] = $value;
            return;
        }

        try {
            $this->primaryCache->set($key, $value, $ttl);
        } catch (Exception $e) {
            $this->useMemory = true;
            $this->memoryCache[$key] = $value;
        }
    }

    public function delete(string $key): void
    {
        if ($this->useMemory) {
            unset($this->memoryCache[$key]);
            return;
        }

        try {
            $this->primaryCache->delete($key);
        } catch (Exception $e) {
            $this->useMemory = true;
            unset($this->memoryCache[$key]);
        }
    }
}
