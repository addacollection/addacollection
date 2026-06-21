<?php
session_start();

// Aiven Database Configuration
$host = 'mysql-7efca4b-addacollection.i.aivencloud.com';
$dbname = 'defaultdb';
$user = 'avnadmin';
$pass = 'AVNS_h0ihm4NmXYmZcJ8ISQM';
$port = 13574;

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// LOGIC: Sirf active orders dikhao (Pending aur Shipped)
$orders = $pdo->query("SELECT * FROM orders 
                       WHERE order_status NOT IN ('Completed', 'Cancelled', 'Refunded') 
                       ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #1a1a1a; color: #e5e5e5; }
        .bg-card { background-color: #262626; }
    </style>
</head>
<body class="p-8">

<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="index.php" class="text-orange-500 hover:underline">← Back to Dashboard</a>
            <h1 class="text-3xl font-bold mt-2">Active Delivery Requests</h1>
        </div>
        <a href="all_ord.php" class="bg-gray-800 hover:bg-gray-700 px-5 py-3 rounded-xl border border-gray-600 text-sm font-bold transition">
            View History →
        </a>
    </div>

    <div class="bg-card rounded-3xl border border-gray-700 overflow-hidden shadow-xl">
    <table class="w-full text-left">
    <thead class="bg-black text-gray-400 text-xs uppercase tracking-wider">
        <tr>
            <th class="px-6 py-4">Order ID</th>
            <th class="px-6 py-4">Customer</th>
            <th class="px-6 py-4">Status</th>
            <th class="px-6 py-4">Amount</th>
            <th class="px-6 py-4 text-center">Action</th> </tr>
    </thead>
    <tbody class="divide-y divide-gray-700">
        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
            <tr class="hover:bg-gray-800 transition">
                <td class="px-6 py-4 font-bold text-orange-500">#<?= htmlspecialchars($order['id']) ?></td>
                <td class="px-6 py-4">
                    <div class="font-medium"><?= htmlspecialchars($order['name'] ?? 'Guest') ?></div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-xs bg-yellow-900 text-yellow-300 border border-yellow-700">
                        <?= htmlspecialchars($order['order_status'] ?? 'Pending') ?>
                    </span>
                </td>
                <td class="px-6 py-4 font-mono">₹<?= number_format($order['total_amount'] ?? 0, 2) ?></td>
                
                <td class="px-6 py-4 text-center space-x-2">
                    <a href="ord_details.php?id=<?= $order['id'] ?>" class="text-blue-400 hover:text-blue-300 font-bold text-sm underline">Details</a>
                    <span class="text-gray-600">|</span>
                    <a href="update_order.php?id=<?= $order['id'] ?>" class="text-orange-400 hover:text-orange-300 font-bold text-sm underline">Manage</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">No active delivery requests.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
    </div>
</div>

</body>
</html>