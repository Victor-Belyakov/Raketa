<?php

namespace Raketa\BackendTestTask\Infrastructure\Service;

use Raketa\BackendTestTask\Domain\Repository\ProductRepository;
use Raketa\BackendTestTask\Infrastructure\Interface\ProductServiceInterface;
use Raketa\BackendTestTask\View\ProductsView;

readonly class ProductService implements ProductServiceInterface
{
    public function __construct(
        private ProductsView $productsView,
        private ProductRepository $productRepository,
    ) {
    }

    /**
     * @param array $request
     * @return array
     */
    public function get(array $request): array
    {
        $products = $this->productRepository->getByCategory($request['category']);
        return $this->productsView->toArray($products);
    }
}
