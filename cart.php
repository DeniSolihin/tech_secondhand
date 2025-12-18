<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Keranjang Belanja</h1>
        <?php if (empty($cart_items)): ?>
            <p>Keranjang kosong.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Total</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php $total = 0; foreach ($cart_items as $item): $total += $item['price'] * $item['quantity']; ?>
                        <tr>
                            <td><?php echo $item['name']; ?></td>
                            <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td>
                            <td><button class="btn btn-danger remove-from-cart" data-cart-id="<?php echo $item['id']; ?>">Hapus</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Total: Rp <?php echo number_format($total, 0, ',', '.'); ?></h3>
            <a href="checkout.php" class="btn btn-primary">Checkout</a>
        <?php endif; ?>
    </div>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>