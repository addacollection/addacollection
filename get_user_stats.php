<?php
// get_user_stats.php

// 1. Centralized Database Connection (SSL included)
require_once __DIR__ . '/common/config.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    die(json_encode(['error' => 'User ID missing']));
}

$userId = (int)$_GET['id'];

try {
    // 2. Ab $pdo yahan directly available hai
    $stmt = $pdo->prepare("SELECT 
        (SELECT COUNT(*) FROM orders WHERE user_id = :uid) as total,
        (SELECT COUNT(*) FROM orders WHERE user_id = :uid AND order_status = 'delivered') as success,
        (SELECT COUNT(*) FROM orders WHERE user_id = :uid AND order_status = 'Processing') as pending,
        (SELECT COUNT(*) FROM orders WHERE user_id = :uid AND order_status = 'cancelled') as rejected,
        (SELECT COUNT(*) FROM orders WHERE user_id = :uid AND order_status = 'refunded') as refunded,
        (SELECT SUM(total_amount) FROM orders WHERE user_id = :uid AND order_status = 'delivered') as total_invest");

    $stmt->execute(['uid' => $userId]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>