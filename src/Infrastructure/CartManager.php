<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Exception;
use Exception\ConnectorException;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Domain\Entity\Cart;
use Raketa\BackendTestTask\Infrastructure\Redis\ConnectorFacade;

class CartManager extends ConnectorFacade
{
    public function __construct(
        private LoggerInterface $logger,
    )
    {
        parent::__construct(
            $_ENV['CART_HOST'],
            $_ENV['CART_PORT'],
            $_ENV['CART_PASS'],
            1,
            $logger
        );
        parent::build();
    }

    /**
     * @param Cart $cart
     * @param int $ttl
     * @return void
     * @throws ConnectorException
     */
    public function saveCart(Cart $cart): void
    {
        try {
            $this->connector->set(session_id(), $cart);
        } catch (Exception $e) {
            $this->logger->error('Ошибка при сохранении корзины: ' . $e->getMessage());
        }
    }

    /**
     * @return Cart|null
     */
    public function getCart(): ?Cart
    {
        try {
            return $this->connector->get(session_id());
        } catch (Exception $e) {
            $this->logger->error('Ошибка при получении корзины: ' . $e->getMessage());
        }
    }
}
