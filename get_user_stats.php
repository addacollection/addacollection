<?php
// get_user_stats.php

// Aiven Database Configuration
$host = 'mysql-7efca4b-addacollection.i.aivencloud.com';
$db   = 'defaultdb';
$user = 'avnadmin';
$pass = 'AVNS_h0ihm4NmXYmZcJ8ISQM';
$port = 13574;

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed']));
}

$userId = (int)$_GET['id'];

// Using the same userId for all placeholders
$stmt = $pdo->prepare("SELECT 
    (SELECT COUNT(*) FROM orders WHERE user_id = :uid) as total,
    (SELECT COUNT(*) FROM orders WHERE user_id = :uid AND status = 'delivered') as success,
    (SELECT COUNT(*) FROM orders WHERE user_id = :uid AND status = 'pending') as pending,
    (SELECT COUNT(*) FROM orders WHERE user_id = :uid AND status = 'cancelled') as rejected,
    (SELECT COUNT(*) FROM orders WHERE user_id = :uid AND status = 'refunded') as refunded,
    (SELECT SUM(total_price) FROM orders WHERE user_id = :uid AND status = 'delivered') as total_invest");

$stmt->execute(['uid' => $userId]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
?>