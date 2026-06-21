<?php
// get_user_stats.php
$pdo = new PDO("mysql:host=127.0.0.1;dbname=adda_collection;charset=utf8mb4", "root", "");
$userId = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT 
    (SELECT COUNT(*) FROM orders WHERE user_id = ?) as total,
    (SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'delivered') as success,
    (SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'pending') as pending,
    (SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'cancelled') as rejected,
    (SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'refunded') as refunded,
    (SELECT SUM(total_price) FROM orders WHERE user_id = ? AND status = 'delivered') as total_invest");
$stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
?>