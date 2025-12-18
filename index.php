<?php
session_start();
require 'config.php';

// Ambil produk terbaru
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tech Secondhand</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Tech Secondhand</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="products.php?category=Hp">Hp</a>
                <a class="nav-link" href="products.php?category=Laptop">Laptop</a>
                <a class="nav-link" href="products.php?category=Kamera">Kamera</a>
                <?php if (isAdmin()): ?>
    <a class="nav-link" href="admin_dashboard.php">Admin Panel</a>
<?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="cart.php">Keranjang</a>
                    <a class="nav-link" href="orders.php">Riwayat</a>
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
<div class="bg-dark text-white py-5 text-center">
    <div class="container">
        <h1 class="display-5 fw-bold">Tech Secondhand</h1>
        <p class="lead">
            Marketplace perangkat teknologi bekas berkualitas & terpercaya
        </p>
        <a href="products.php?category=Hp" class="btn btn-primary me-2">
            Mulai Belanja
        </a>
        <a href="register.php" class="btn btn-outline-light">
            Daftar
        </a>
    </div>
</div>
    <div class="container mt-4">
        <h1>Selamat Datang di Tech Secondhand</h1>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                        <div class="card-body">
                            <h5><?php echo $product['name']; ?></h5>
                            <p>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                            <a href="products.php?category=<?php echo $product['category']; ?>" class="btn btn-primary">Lihat Kategori</a>
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