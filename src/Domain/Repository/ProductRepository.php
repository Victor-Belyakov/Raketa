<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Domain\Repository;

use Doctrine\DBAL\Connection;
use Exception;
use Raketa\BackendTestTask\Domain\Entity\Product;

class ProductRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getByUuid(string $uuid): Product
    {
        $row = $this->connection->fetchAssociative(
            "SELECT * FROM products WHERE uuid = ?",
            [$uuid]
        );

        if (empty($row)) {
            throw new Exception('Продукт не найден');
        }

        return $this->make($row);
    }

    /**
     * @param string[] $uuids
     * @return Product[]
     */
    public function getByUuids(array $uuids): array
    {
        $placeholders = implode(',', array_fill(0, count($uuids), '?'));

        $sql = "SELECT * FROM products WHERE uuid IN ($placeholders)";
        $rows = $this->connection->fetchAllAssociative($sql, $uuids);

        return array_map(fn(array $row) => $this->make($row), $rows);
    }

    public function getByCategory(string $category): array
    {
        return array_map(
            static fn (array $row): Product => $this->make($row),
            $this->connection->fetchAllAssociative(
                "SELECT * FROM products WHERE is_active = 1 AND category = ?", [$category],
            )
        );
    }

    public function make(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uuid'],
            $row['is_active'],
            $row['category'],
            $row['name'],
            $row['description'],
            $row['thumbnail'],
            $row['price'],
        );
    }
}
