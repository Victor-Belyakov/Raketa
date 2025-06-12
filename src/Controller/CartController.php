<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Infrastructure\Http\JsonResponse;
use Raketa\BackendTestTask\Infrastructure\Interface\CartServiceInterface;

readonly class CartController
{
    public function __construct(
        private LoggerInterface $logger,
        private CartServiceInterface $cartService
    ) {
    }

    public function get(): JsonResponse
    {
        $statusCode = 200;
        $data = [];
        try {
            $data = $this->cartService->getCart();

            if (!$data) {
                $statusCode = 404;
                $data = ['message' => 'Корзина не найдена'];
            }
        } catch (\Exception $exception) {
            $statusCode = 500;
            $data['error'] = $exception->getMessage();
            $this->logger->error('Ошибка при получении корзины: ' . $exception->getMessage());
        }

        $response = new JsonResponse();
        $response->getBody()->write(
            json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($statusCode);
    }

    public function add(RequestInterface $request): JsonResponse
    {
        $statusCode = 200;
        $data = [];
        try {
            $rawRequest = json_decode($request->getBody()->getContents(), true);
            $data['cart'] = $this->cartService->addProduct($rawRequest);
            $data['status'] = 'success';
        } catch (\Exception $exception) {
            $statusCode = 500;
            $data['status'] = 'error';
            $data['error'] = $exception->getMessage();
            $this->logger->error('Ошибка при добавлении товара в коризну: ' . $exception->getMessage());
        }

        $response = new JsonResponse();
        $response->getBody()->write(
            json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($statusCode);
    }

    public function create(RequestInterface $request): JsonResponse
    {
        $statusCode = 201;
        $data = [];
        try {
            $rawRequest = json_decode($request->getBody()->getContents(), true);
            $data = $this->cartService->createCart($rawRequest);
        } catch (\Exception $exception) {
            $statusCode = 500;
            $data['status'] = 'error';
            $data['error'] = $exception->getMessage();
            $this->logger->error('Ошибка при добавлении товара в коризну: ' . $exception->getMessage());
        }

        $response = new JsonResponse();
        $response->getBody()->write(
            json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($statusCode);
    }
}
