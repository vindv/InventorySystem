<?php

namespace InventorySystem\repository;

use PDO;
use RuntimeException;
use Throwable;

class SaleRepository
{

    public function __construct(private PDO $connection)
    {
    }

    /**
     * @throws Throwable
     */
    public function register(int $productId, int $quantity): void
    {
        if($quantity < 1) {
            throw new \InvalidArgumentException('Quantity must be a positive integer');
        }
        if($productId < 1) {
            throw new \InvalidArgumentException('Product ID must be a positive integer');
        }

        $this->connection->beginTransaction();

        try {
            $select = $this->connection->prepare('SELECT price, price * :quantity AS total FROM products WHERE id = :product_id');
            $select->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $select->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $select->execute();

            $product = $select->fetch(PDO::FETCH_ASSOC);
            if($product === false) {
                throw new RuntimeException('The selected product does not exist');
            }

            $update = $this->connection->prepare('UPDATE products SET stock = stock - :quantity WHERE id = :product_id AND stock >= :min_stock');
            $update->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $update->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $update->bindValue(':min_stock', $quantity, PDO::PARAM_INT);
            $update->execute();

            if($update->rowCount() === 0) {
                throw new RuntimeException('Not enough stock for this sale');
            }

            $insert = $this->connection->prepare('INSERT INTO sales (total) VALUES (:total)');
            $insert->bindValue(':total', $product['total']);
            $insert->execute();

            $saleId = (int)$this->connection->lastInsertId();

            $detail = $this->connection->prepare('INSERT INTO sale_details (sale_id, product_id, quantity, unit_price) VALUES (:sale_id, :product_id, :quantity, :unit_price)');
            $detail->bindValue(':sale_id', $saleId, PDO::PARAM_INT);
            $detail->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $detail->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $detail->bindValue(':unit_price', $product['price']);
            $detail->execute();

            $this->connection->commit();
        }catch(Throwable $e) {
            if($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $e;
        }
    }

}
