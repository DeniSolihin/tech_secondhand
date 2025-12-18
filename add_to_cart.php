<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Harus login dulu.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Produk tidak valid.']);
    exit;
}

// Cek apakah produk sudah ada di keranjang
$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$existing = $stmt->fetch();

if ($existing) {
    // Update quantity
    $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?")->execute([$existing['id']]);
} else {
    // Tambah baru
    $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)")->execute([$user_id, $product_id]);
}

echo json_encode(['success' => true, 'message' => 'Ditambahkan ke keranjang.']);
?>