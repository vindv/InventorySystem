<?php

namespace InventorySystem\controller;

use InventorySystem\repository\ProductRepository;
use InventorySystem\repository\SaleRepository;
use PDOException;
use RuntimeException;
use Throwable;

class ProductController
{

    public function __construct(
        private ProductRepository $productRepository,
        private SaleRepository    $saleRepository
    ){}

    public function handle(string $action, array $input): array
    {
        return match ($action) {
            'save_product' => $this->saveProduct($input),
            'restock' => $this->restockProduct($input),
            'sell' => $this->sellProduct($input),
            default => ['Unknown action please try again'],
        };
    }

    private function saveProduct(array $input): array
    {
        $errors = [];

        $name = trim(is_string($input['name'] ?? null) ? $input['name'] : '');
        $price = $input['price'] ?? '';
        $stock = $input['stock'] ?? '';

        if($name === '') {
            $errors[] = 'The name is required';
        }
        if(mb_strlen($name, 'UTF-8') > 120) {
            $errors[] = 'The name must be 120 characters or fewer';
        }
        if(!is_string($price) || !preg_match('/^\d{1,8}(\.\d{1,2})?$/', $price)) {
            $errors[] = 'The price must be a number between 0 and 99999999.99, with up to 2 decimals';
        }
        if(filter_var($stock, FILTER_VALIDATE_INT) === false || $stock < 0 || $stock > 2147483647) {
            $errors[] = 'The stock must be a whole number between 0 and 2147483647';
        }

        if($errors === []) {
            try {
                $this->productRepository->create($name, $price, (int)$stock);
                $_SESSION['message'] = 'Product saved successfully';
                header('Location: index.php');
                exit;

            }catch(PDOException $e) {
                if(($e->errorInfo[1] ?? null) === 1062) {
                    $errors[] = 'A product with that name already exists';
                }else {
                    error_log($e->getMessage());
                    $errors[] = 'The product could not be saved. Please try again.';
                }

            }catch(Throwable $e) {
                error_log($e->getMessage());
                $errors[] = 'The product could not be saved. Please try again.';
            }
        }

        return $errors;
    }

    private function restockProduct(array $input): array
    {
        $errors = $this->validateItemRequest($input);

        if($errors === []) {
            $productId = (int)$input['product_id'];
            $quantity = (int)$input['quantity'];

            try {
                if($this->productRepository->restock($productId, $quantity)) {
                    $_SESSION['message'] = 'Stock updated successfully';
                    header('Location: index.php');
                    exit;
                }
                $errors[] = 'The product could not be restocked';

            }catch(PDOException $e) {
                error_log($e->getMessage());
                $errors[] = 'The product could not be restocked. Please try again';

            }catch(Throwable $e) {
                error_log($e->getMessage());
                $errors[] = 'The product could not be restocked. Please try again';
            }
        }

        return $errors;
    }

    private function sellProduct(array $input): array
    {
        $errors = $this->validateItemRequest($input);

        if($errors === []) {
            $productId = (int)$input['product_id'];
            $quantity = (int)$input['quantity'];

            try {
                $this->saleRepository->register($productId, $quantity);
                $_SESSION['message'] = 'Sale registered successfully';
                header('Location: index.php');
                exit;

            }catch(PDOException $e) {
                error_log($e->getMessage());
                $errors[] = 'The sale could not be registered. Please try again';

            }catch(RuntimeException $e) {
                $errors[] = $e->getMessage();

            }catch(Throwable $e) {
                error_log($e->getMessage());
                $errors[] = 'The sale could not be registered. Please try again';
            }
        }

        return $errors;
    }

    private function validateItemRequest(array $input): array
    {
        $errors = [];

        $productId = $input['product_id'] ?? '';
        $quantity = $input['quantity'] ?? '';

        if(filter_var($productId, FILTER_VALIDATE_INT) === false || $productId < 1) {
            $errors[] = 'Please select a valid product';
        }
        if(filter_var($quantity, FILTER_VALIDATE_INT) === false || $quantity < 1) {
            $errors[] = 'The quantity must be a whole number greater than zero';
        }

        return $errors;
    }

}
