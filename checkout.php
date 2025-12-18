<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil item keranjang untuk preview
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];
    $address = $_POST['address'] ?? ''; // Untuk COD

    if (empty($cart_items)) {
        $error = "Keranjang kosong.";
    } elseif (!$payment_method) {
        $error = "Pilih metode payment.";
    } else {
        $pdo->beginTransaction();
        try {
            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, payment_method, status) VALUES (?, ?, ?, 'Pending')");
            $stmt->execute([$user_id, $total, $payment_method]);
            $order_id = $pdo->lastInsertId();

            // Insert order items
            foreach ($cart_items as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
            }

            // Kosongkan keranjang
            $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

            $pdo->commit();
            header('Location: orders.php?success=1'); // Redirect dengan pesan sukses
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
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
        <h1>Checkout</h1>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if (isset($_GET['success'])) echo "<div class='alert alert-success'>Pesanan berhasil dibuat! Lihat riwayat untuk detail.</div>"; ?>

        <div class="row">
            <div class="col-md-8">
                <h3>Ringkasan Pesanan</h3>
                <?php if (empty($cart_items)): ?>
                    <p>Keranjang kosong. <a href="index.php">Kembali ke beranda</a></p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Total</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td><?php echo $item['name']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                    <td>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <h4>Total: Rp <?php echo number_format($total, 0, ',', '.'); ?></h4>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <h3>Metode Payment</h3>
                <form method="POST" id="checkoutForm">
                    <div class="mb-3">
                        <label>Metode Payment</label>
                        <select name="payment_method" id="paymentMethod" class="form-control" required>
                            <option value="">Pilih Metode</option>
                            <option value="COD">COD (Cash on Delivery)</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                        </select>
                    </div>

                    <!-- Detail untuk COD -->
                    <div id="codDetails" style="display: none;">
                        <div class="mb-3">
                            <label>Alamat Pengiriman</label>
                            <textarea name="address" class="form-control" placeholder="Masukkan alamat lengkap" required></textarea>
                        </div>
                        <p class="text-muted">Bayar saat barang diterima di alamat Anda.</p>
                    </div>

                    <!-- Detail untuk Transfer Bank -->
                    <div id="transferDetails" style="display: none;">
                        <p class="text-muted">Transfer ke rekening berikut:</p>
                        <ul>
                            <li>Bank BCA: 1234567890 a/n Tech Secondhand</li>
                            <li>Bank Mandiri: 0987654321 a/n Tech Secondhand</li>
                        </ul>
                        <p class="text-muted">Upload bukti transfer setelah checkout (simulasi).</p>
                    </div>

                    <button type="submit" class="btn btn-primary" <?php if (empty($cart_items)) echo 'disabled'; ?>>Konfirmasi Pesanan</button>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle detail payment berdasarkan pilihan
        document.getElementById('paymentMethod').addEventListener('change', function() {
            const method = this.value;
            document.getElementById('codDetails').style.display = method === 'COD' ? 'block' : 'none';
            document.getElementById('transferDetails').style.display = method === 'Transfer Bank' ? 'block' : 'none';
        });

        // Konfirmasi sebelum submit
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin melanjutkan pembayaran?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>