<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Redis;

use Raketa\BackendTestTask\Domain\Entity\Cart;
use Raketa\BackendTestTask\Infrastructure\Exception\ConnectorException;
use Redis;
use RedisException;

readonly class Connector
{

    public function __construct(
        private Redis $redis
    ) {
    }

    /**
     * @param string $key
     * @return Cart|null
     * @throws ConnectorException
     */
    public function get(string $key): ?Cart
    {
        try {
            $data = $this->redis->get($key);
            if (false === $data) {
                return null;
            }
            return unserialize($data);
        } catch (RedisException $e) {
            throw new ConnectorException('Ошибка при получении записи в редис: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $key
     * @param Cart $value
     * @param int $ttl
     * @return void
     * @throws ConnectorException
     */
    public function set(string $key, Cart $value, int $ttl = 86400): void
    {
        try {
            $this->redis->setex($key, $ttl, serialize($value));
        } catch (RedisException $e) {
            throw new ConnectorException('Ошибка при записи в редис: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $key
     * @return bool
     * @throws RedisException
     */
    public function has(string $key): bool
    {
        return $this->redis->exists($key) > 0;
    }
}
