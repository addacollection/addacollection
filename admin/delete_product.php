<?php
session_start();
require_once '../common/config.php';

// Security: Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth.php");
    exit;
}

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // 1. Pehle image file ka path nikalte hain taaki server se delete kar saken (Optional but recommended)
    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product && !empty($product['image_url'])) {
        $imagePath = "../uploads/products/" . $product['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Yeh server se image delete kar dega
        }
    }

    // 2. Database se record delete karte hain
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    // 3. Success message ke saath wapas bhej do
    header("Location: products.php?deleted=true");
    exit;
} else {
    // Agar bina ID ke aaya, toh seedha wapas bhej do
    header("Location: products.php");
    exit;
}
?>