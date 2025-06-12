<?php

namespace Raketa\BackendTestTask\Infrastructure\Service;

use Cassandra\Uuid;
use Raketa\BackendTestTask\Domain\Entity\Cart;
use Raketa\BackendTestTask\Domain\Entity\CartItem;
use Raketa\BackendTestTask\Domain\Repository\ProductRepository;
use Raketa\BackendTestTask\Infrastructure\CartManager;
use Raketa\BackendTestTask\Infrastructure\Interface\CartServiceInterface;
use Raketa\BackendTestTask\View\CartView;

readonly class CartService implements CartServiceInterface
{
    public function __construct(
        private CartView $cartView,
        private CartManager $cartManager,
        private ProductRepository $productRepository
    ) {
    }

    /**
     * @param array $request
     * @return array
     * @throws \Exception
     */
    public function addProduct(array $request): array
    {
        $product = $this->productRepository->getByUuid($request['productUuid']);

        $cart = $this->cartManager->getCart();
        $cart->addItem(new CartItem(
            Uuid::uuid4()->toString(),
            $product->getUuid(),
            $product->getPrice(),
            $request['quantity'],
        ));

        return $this->cartView->toArray($cart);
    }

    /**
     * @return array|null
     */
    public function getCart(): ?array
    {
        $cart = $this->cartManager->getCart();

        if (!$cart) {
            return null;
        }

        return $this->cartView->toArray($cart);
    }

    /**
     * @param array $request
     * @return array
     */
    public function createCart(array $request): array
    {
        $cart = new Cart(
            Uuid::uuid4()->toString(),
            $request['customer'],
            $request['paymentMethod'],
            $request['items']
        );
        $this->cartManager->saveCart($cart);

        return $this->cartView->toArray($cart);
    }
}
