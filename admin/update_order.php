<?php
/**
 * Adda Collection - Delivery Status Management
 */

// Session start
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// 1. Centralized Database Connection (SSL included)
// Ensure path points to your common configuration correctly
require_once __DIR__ . '/../common/config.php';

// Auth check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

if (!isset($_GET['id'])) {
    header("Location: delivery.php");
    exit();
}

$order_id = $_GET['id'];

try {
    // 2. Order details fetch karna
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found!");
    }

    // 3. Status update logic
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_status = $_POST['status'];
        
        $update = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $update->execute([$new_status, $order_id]);
        
        header("Location: delivery.php?success=1");
        exit();
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #1a1a1a; color: #e5e5e5; }
        .bg-card { background-color: #262626; }
    </style>
</head>
<body class="p-8">

<div class="max-w-2xl mx-auto">
    <a href="delivery.php" class="text-orange-500 hover:underline mb-6 block">← Back to Requests</a>
    
    <div class="bg-card p-8 rounded-3xl border border-gray-700">
        <h2 class="text-2xl font-bold mb-6">Manage Order #<?= $order['id'] ?></h2>
        
        <div class="mb-6 p-4 bg-black rounded-xl border border-gray-800 text-gray-400">
            <p>Customer: <span class="text-white"><?= htmlspecialchars($order['name'] ?? 'N/A') ?></span></p>
            <p>Total: <span class="text-white">₹<?= number_format($order['total_amount'] ?? 0, 2) ?></span></p>
        </div>

        <form method="POST">
            <label class="block text-xs uppercase text-gray-400 mb-2">Update Order Status</label>
            <select name="status" class="w-full p-3 bg-black rounded-xl border border-gray-700 outline-none mb-6">
                <option value="Pending" <?= $order['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Shipped" <?= $order['order_status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                <option value="Completed" <?= $order['order_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option value="Cancelled" <?= $order['order_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                <option value="Refunded" class="text-red-400 font-bold" <?= $order['order_status'] == 'Refunded' ? 'selected' : '' ?>>Refunded</option>
            </select>
            
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 p-4 rounded-xl font-bold transition">Update Status</button>
        </form>
    </div>
</div>

</body>
</html>