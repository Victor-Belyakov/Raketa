<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Raketa\BackendTestTask\Infrastructure\Http\JsonResponse;
use Psr\Log\LoggerInterface;
use Raketa\BackendTestTask\Infrastructure\Service\ProductService;

readonly class ProductsController
{
    public function __construct(
        private LoggerInterface $logger,
        private ProductService $productService
    ) {
    }

    public function get(RequestInterface $request): ResponseInterface
    {
        $statusCode = 200;
        $data = [];
        try {
            $rawRequest = json_decode($request->getBody()->getContents(), true);
            $data = $this->productService->get($rawRequest);
        } catch (\Exception $exception) {
            $statusCode = 500;
            $data['error'] = $exception->getMessage();
            $this->logger->error('Ошибка при получении товаров по категории: ' . $exception->getMessage());
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
