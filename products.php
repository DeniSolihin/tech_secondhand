<?php
session_start();
require 'config.php';

$category = $_GET['category'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM products WHERE category = ?");
$stmt->execute([$category]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Produk <?php echo $category; ?></title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Produk <?php echo $category; ?></h1>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?php echo $product['image']; ?>" class="card-img-top">
                        <div class="card-body">
                            <h5><?php echo $product['name']; ?></h5>
                            <p>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="btn btn-success add-to-cart" data-product-id="<?php echo $product['id']; ?>">Tambah ke Keranjang</button>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-warning">Login untuk Beli</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>