<?php
use InventorySystem\controller\ProductController;
use InventorySystem\database\Database;
use InventorySystem\repository\ProductRepository;
use InventorySystem\repository\SaleRepository;

require __DIR__ . '/../vendor/autoload.php';

ini_set('session.use_strict_mode', '1');
$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; form-action 'self'; base-uri 'self'; frame-ancestors 'none'; object-src 'none'");

if(!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
$errors = [];
$products = [];

try {
    $config = require __DIR__ . '/../config/database.php';

    $database = new Database($config);
    $connection = $database->getConnection();

    $productRepository = new ProductRepository($connection);
    $saleRepository = new SaleRepository($connection);
    $controller = new ProductController($productRepository, $saleRepository);

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';

        if(!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $errors[] = 'Invalid request. Please reload the page and try again.';
        }else {
            $action = $_POST['action'] ?? '';

            if(!is_string($action)) {
                $action = '';
            }

            $errors = $controller->handle($action, $_POST);
        }
    }

    $products = $productRepository->all();
}catch(Throwable $e) {
    error_log($e->getMessage());
    http_response_code(503);
    $errors[] = 'The system is temporarily unavailable. Please try again later.';
}

require __DIR__ . '/../views/home.php';
