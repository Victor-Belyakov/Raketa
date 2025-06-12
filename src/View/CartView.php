<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\View;

use Raketa\BackendTestTask\Domain\Entity\Cart;
use Raketa\BackendTestTask\Domain\Repository\ProductRepository;

readonly class CartView
{
    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function toArray(Cart $cart): array
    {
        $data = [
            'uuid' => $cart->getUuid(),
            'customer' => [
                'id' => $cart->getCustomer()->getId(),
                'name' => implode(' ', [
                    $cart->getCustomer()->getLastName(),
                    $cart->getCustomer()->getFirstName(),
                    $cart->getCustomer()->getMiddleName(),
                ]),
                'email' => $cart->getCustomer()->getEmail(),
            ],
            'payment_method' => $cart->getPaymentMethod(),
        ];

        $productUuids = array_map(
            fn($item) => $item->getProductUuid(),
            $cart->getItems()
        );

        $products = $this->productRepository->getByUuids($productUuids);

        $productsMap = [];
        foreach ($products as $product) {
            $productsMap[$product->getUuid()] = $product;
        }

        $total = 0;
        $data['items'] = [];

        foreach ($cart->getItems() as $item) {
            $total += $item->getPrice() * $item->getQuantity();
            $product = $productsMap[$item->getProductUuid()] ?? null;

            if (null === $product) {
                continue;
            }

            $data['items'][] = [
                'uuid' => $item->getUuid(),
                'price' => $item->getPrice(),
                'total' => $total,
                'quantity' => $item->getQuantity(),
                'product' => [
                    'id' => $product->getId(),
                    'uuid' => $product->getUuid(),
                    'name' => $product->getName(),
                    'thumbnail' => $product->getThumbnail(),
                    'price' => $product->getPrice(),
                ],
            ];
        }
        $data['total'] = $total;

        return $data;
    }
}
