<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil pesanan pengguna
$stmt = $pdo->prepare("SELECT o.*, oi.product_id, oi.quantity, oi.price, p.name FROM orders o 
                       JOIN order_items oi ON o.id = oi.order_id 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE o.user_id = ? ORDER BY o.created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kelompokkan berdasarkan order_id
$grouped_orders = [];
foreach ($orders as $order) {
    $grouped_orders[$order['id']][] = $order;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pemesanan</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Tech Secondhand</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="products.php?category=Hp">Hp</a>
                <a class="nav-link" href="products.php?category=Laptop">Laptop</a>
                <a class="nav-link" href="products.php?category=Kamera">Kamera</a>
                <a class="nav-link" href="cart.php">Keranjang</a>
                <a class="nav-link" href="orders.php">Riwayat</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Riwayat Pemesanan</h1>
        <?php if (empty($grouped_orders)): ?>
            <p>Belum ada pesanan.</p>
        <?php else: ?>
            <?php foreach ($grouped_orders as $order_id => $items): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Pesanan #<?php echo $order_id; ?> - Status: <?php echo $items[0]['status']; ?> - Total: Rp <?php echo number_format($items[0]['total'], 0, ',', '.'); ?></h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($items as $item): ?>
                                <li class="list-group-item">
                                    <?php echo $item['name']; ?> - Jumlah: <?php echo $item['quantity']; ?> - Harga: Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>