<?php

namespace InventorySystem\repository;

use PDO;

class ProductRepository
{
    private const MAX_STOCK = 2147483647;

    public function __construct(private PDO $connection)
    {

    }

    public function all(): array
    {
        $stmt = $this->connection->query('SELECT id, name, price, stock FROM products ORDER BY name');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $name, string $price, int $stock): void
    {
        $stmt = $this->connection->prepare('INSERT INTO products (name, price, stock) VALUES (:name, :price, :stock)');

        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':stock', $stock);
        $stmt->execute();
    }

    public function restock(int $productId, int $quantity): bool
    {
        $stmt = $this->connection->prepare('UPDATE products SET stock = stock + :quantity WHERE id = :product_id AND stock <= :max_stock - :quantity');
        $stmt->bindValue(':quantity', $quantity);
        $stmt->bindValue(':product_id', $productId);
        $stmt->bindValue(':max_stock', self::MAX_STOCK, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
