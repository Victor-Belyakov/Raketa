<?php

namespace Raketa\BackendTestTask\Infrastructure\Interface;

interface ProductServiceInterface
{
    /**
     * @return array|null
     */
    public function get(array $request): ?array;
}
