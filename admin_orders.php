<?php
session_start();
require 'config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Ambil semua pesanan
$stmt = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle update status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$status, $order_id]);
    header('Location: admin_orders.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan</title>
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
        <h1>Kelola Pesanan</h1>
        <table class="table">
            <thead>
                <tr><th>ID Pesanan</th><th>Pengguna</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['username']; ?></td>
                        <td>Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                        <td><?php echo $order['status']; ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" class="form-select form-select-sm d-inline w-auto">
                                    <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Paid" <?php if ($order['status'] == 'Paid') echo 'selected'; ?>>Paid</option>
                                    <option value="Shipped" <?php if ($order['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                    <option value="Delivered" <?php if ($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>