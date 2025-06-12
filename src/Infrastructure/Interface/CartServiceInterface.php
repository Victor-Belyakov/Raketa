<?php

namespace Raketa\BackendTestTask\Infrastructure\Interface;


interface CartServiceInterface
{
    /**
     * @param array $request
     * @return array|null
     */
    public function addProduct(array $request): ?array;

    /**
     * @return array|null
     */
    public function getCart(): ?array;

    /**
     * @param array $request
     * @return array|null
     */
    public function createCart(array $request): ?array;
}
