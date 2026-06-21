<?php
/**
 * Adda Collection - Admin Edit Product Logic
 */

// Session check
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// 1. Centralized Database Connection (SSL included)
require_once __DIR__ . '/../common/config.php';

// Auth check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    try {
        // Agar nayi image upload ki gayi hai
        if (!empty($_FILES['new_image']['name'])) {
            $upload_dir = "../uploads/products/";
            $img = time() . "_" . basename($_FILES['new_image']['name']);
            
            if (move_uploaded_file($_FILES['new_image']['tmp_name'], $upload_dir . $img)) {
                $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, description=?, image_url=? WHERE id=?");
                $stmt->execute([$name, $price, $description, $img, $id]);
            }
        } else {
            // Agar image update nahi karni, sirf baaki details
            $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, description=? WHERE id=?");
            $stmt->execute([$name, $price, $description, $id]);
        }

        header("Location: edit_product.php?id=$id&status=success");
        exit();
    } catch (PDOException $e) {
        die("Update Failed: " . $e->getMessage());
    }
}
?>