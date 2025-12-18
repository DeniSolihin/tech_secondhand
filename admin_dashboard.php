<?php
session_start();
require 'config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Hitung statistik
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$user_count = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php">Admin Panel</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="admin_products.php">Kelola Produk</a>
                <a class="nav-link" href="admin_users.php">Kelola Pengguna</a>
                <a class="nav-link" href="admin_orders.php">Kelola Pesanan</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Dashboard Admin</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Jumlah Produk</h5>
                        <p><?php echo $product_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Jumlah Pengguna</h5>
                        <p><?php echo $user_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Jumlah Pesanan</h5>
                        <p><?php echo $order_count; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>