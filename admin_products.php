<?php
session_start();
require 'config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Ambil semua produk
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle tambah/edit produk
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $image = $_POST['image']; // Simulasi, sebenarnya upload file

    if (isset($_POST['id'])) {
        // Edit
        $stmt = $pdo->prepare("UPDATE products SET name=?, category=?, price=?, description=?, stock=?, image=? WHERE id=?");
        $stmt->execute([$name, $category, $price, $description, $stock, $image, $_POST['id']]);
    } else {
        // Tambah
        $stmt = $pdo->prepare("INSERT INTO products (name, category, price, description, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $price, $description, $stock, $image]);
    }
    header('Location: admin_products.php');
}

// Handle hapus
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$_GET['delete']]);
    header('Location: admin_products.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk</title>
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
        <h1>Kelola Produk</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#productModal">Tambah Produk</button>
        <table class="table">
            <thead>
                <tr><th>Nama</th><th>Kategori</th><th>Harga</th><th>Stock</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['category']; ?></td>
                        <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $product['id']; ?>" data-name="<?php echo $product['name']; ?>" data-category="<?php echo $product['category']; ?>" data-price="<?php echo $product['price']; ?>" data-description="<?php echo $product['description']; ?>" data-stock="<?php echo $product['stock']; ?>" data-image="<?php echo $product['image']; ?>" data-bs-toggle="modal" data-bs-target="#productModal">Edit</button>
                            <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal untuk Tambah/Edit -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah/Edit Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="productId">
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="name" id="productName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Kategori</label>
                            <select name="category" id="productCategory" class="form-control" required>
                                <option value="Hp">Hp</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Kamera">Kamera</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Harga</label>
                            <input type="number" name="price" id="productPrice" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea name="description" id="productDescription" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Stock</label>
                            <input type="number" name="stock" id="productStock" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Gambar (URL)</label>
                            <input type="text" name="image" id="productImage" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk edit
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('productId').value = btn.dataset.id;
                document.getElementById('productName').value = btn.dataset.name;
                document.getElementById('productCategory').value = btn.dataset.category;
                document.getElementById('productPrice').value = btn.dataset.price;
                document.getElementById('productDescription').value = btn.dataset.description;
                document.getElementById('productStock').value = btn.dataset.stock;
                document.getElementById('productImage').value = btn.dataset.image;
            });
        });
    </script>
</body>
</html>