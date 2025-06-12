<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure\Redis;

use Psr\Log\LoggerInterface;
use Redis;
use RedisException;

class ConnectorFacade
{
    protected ?Connector $connector;

    public function __construct(
        private string $host,
        private int $port,
        private string $password,
        private int $dbindex,
        private LoggerInterface $logger
    ) {
    }

    protected function build(): void
    {
        $redis = new Redis();
        try {
            $redis->connect($this->host, $this->port);

            if ($this->password) {
                $redis->auth($this->password);
            }

            $redis->select($this->dbindex);

            $this->connector = new Connector($redis);
        } catch (RedisException $e) {
            $this->logger->error('Ошибка при подключении к редису' . $e->getMessage());
            $this->connector = null;
        }
    }
}
