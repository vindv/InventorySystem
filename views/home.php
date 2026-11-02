<?php
/**
 * @var string $message
 * @var string[] $errors
 * @var array $products
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inventory and Sales</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/theme.js" defer></script>
</head>
<body>
<main class="container">
    <header class="page-header">
        <div>
            <h1>Inventory and Sales System</h1>
            <p class="subtitle">Manage products, stock and sales</p>
        </div>
        <button class="theme-toggle" id="theme-toggle" type="button" aria-pressed="false">
            Switch to dark theme
        </button>
    </header>


    <?php if($message !== ''): ?>
        <p class="alert alert-success" role="status"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php foreach($errors as $error): ?>
        <p class="alert alert-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endforeach; ?>

    <div class="forms">
        <section class="card">
            <h2>New product</h2>
            <form method="post">
                <input type="hidden" name="action" value="save_product">
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" maxlength="120" required>

                <label for="price">Price</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>

                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" step="1" min="0" required>

                <button type="submit">Save product</button>
            </form>
        </section>

        <section class="card">
            <h2>Restock product</h2>
            <form method="post">
                <input type="hidden" name="action" value="restock">
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <label for="product_id">Product</label>
                <select id="product_id" name="product_id" required>
                    <?php foreach($products as $product): ?>
                        <option value="<?= $product['id'] ?>">
                            <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" step="1" min="1" required>

                <button type="submit">Restock</button>
            </form>
        </section>

        <section class="card">
            <h2>New Sale</h2>
            <form method="post">
                <input type="hidden" name="action" value="sell">
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <label for="sale_product_id">Product</label>
                <select id="sale_product_id" name="product_id" required>
                    <?php foreach($products as $product): ?>
                        <option value="<?= $product['id'] ?>">
                            <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="sale_quantity">Quantity</label>
                <input type="number" id="sale_quantity" name="quantity" step="1" min="1" required>

                <button type="submit">Sell</button>
            </form>
        </section>
    </div>

    <section class="card">
        <h2>Products</h2>
        <div class="table-wrap">
        <table>
            <caption>Current product inventory</caption>
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th class="num" scope="col">Price</th>
                <th class="num" scope="col">Stock</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="num"><?= $product['price'] ?></td>
                    <td class="num"><?= $product['stock'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </section>
</main>
</body>
</html>
