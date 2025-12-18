<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Harus login dulu.']);
    exit;
}

$cart_id = $_POST['cart_id'] ?? null;

if (!$cart_id) {
    echo json_encode(['success' => false, 'message' => 'Item tidak valid.']);
    exit;
}

// Hapus dari keranjang
$pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?")->execute([$cart_id, $_SESSION['user_id']]);

echo json_encode(['success' => true, 'message' => 'Dihapus dari keranjang.']);
?>