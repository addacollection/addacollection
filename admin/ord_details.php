<?php
// Session start
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// 1. Centralized Database Connection (SSL included)
// Ensure path points to your common configuration
require_once __DIR__ . '/../common/config.php';

$order_id = $_GET['id'] ?? 0;

try {
    // 2. Order Details fetch karo
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found!");
    }

    // 3. Order Items fetch karo
    $items_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $items_stmt->execute([$order_id]);
    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="p-8">

<div class="max-w-4xl mx-auto">
    <div class="no-print mb-6 flex justify-between">
        <a href="delivery.php" class="text-orange-500 hover:underline">← Back to Delivery</a>
        <button onclick="window.print()" class="bg-blue-600 px-6 py-2 rounded-xl text-sm font-bold shadow-lg hover:bg-blue-500">Download/Print Invoice</button>
    </div>

    <div class="bg-card p-8 rounded-3xl border border-gray-700">
        <div class="flex justify-between items-start mb-8 border-b border-gray-700 pb-8">
            <div>
                <h1 class="text-3xl font-bold">Order #<?= htmlspecialchars($order['id']) ?></h1>
                <p class="text-gray-400 text-sm mt-1">Placed on: <?= htmlspecialchars($order['created_at']) ?></p>
            </div>
            <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-orange-900 text-orange-200 uppercase">
                <?= htmlspecialchars($order['order_status'] ?? 'Pending') ?>
            </span>
        </div>
        
        <div class="grid grid-cols-3 gap-8 mb-8">
            <div>
                <h3 class="text-gray-500 text-[10px] uppercase font-bold mb-2">Shipping Address</h3>
                <p class="text-sm"><?= nl2br(htmlspecialchars($order['shipping_address'] ?? 'No Address')) ?></p>
            </div>
            <div>
                <h3 class="text-gray-500 text-[10px] uppercase font-bold mb-2">Customer Info</h3>
                <p class="text-sm font-bold"><?= htmlspecialchars($order['name'] ?? 'N/A') ?></p>
                <p class="text-sm text-gray-400"><?= htmlspecialchars($order['email'] ?? 'N/A') ?></p>
                <p class="text-sm text-gray-400"><?= htmlspecialchars($order['phone'] ?? 'N/A') ?></p>
            </div>
            <div>
                <h3 class="text-gray-500 text-[10px] uppercase font-bold mb-2">Payment Details</h3>
                <p class="text-sm">Method: <span class="text-white font-bold">
                    <?= ($order['payment_method'] == 'cod') ? 'Cash On Delivery' : ucfirst($order['payment_method']) ?>
                </span></p>
                <p class="text-sm mt-1">UTR: <span class="text-green-400 font-mono"><?= htmlspecialchars($order['utr_number'] ?? 'N/A') ?></span></p>
            </div>
        </div>

        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-500 text-[10px] uppercase border-b border-gray-700">
                    <th class="pb-4">Image</th>
                    <th class="pb-4">Product</th>
                    <th class="pb-4 text-center">Qty</th>
                    <th class="pb-4 text-right">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr class="border-b border-gray-800">
                    <td class="py-4">
                        <img src="../uploads/products/<?= htmlspecialchars($item['product_image'] ?? 'default.jpg') ?>" 
                             onerror="this.src='../uploads/default.jpg'" 
                             class="w-16 h-16 object-cover rounded-lg border border-gray-600">
                    </td>
                    <td class="py-4 font-medium text-white"><?= htmlspecialchars($item['product_name'] ?? 'Unknown Product') ?></td>
                    <td class="py-4 text-center"><?= $item['quantity'] ?></td>
                    <td class="py-4 text-right font-mono text-white">₹<?= number_format($item['price'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-8 flex justify-end">
            <div class="text-xl font-bold bg-black px-6 py-3 rounded-2xl border border-gray-700">
                Grand Total: <span class="text-orange-500">₹<?= number_format($order['total_amount'] ?? 0, 2) ?></span>
            </div>
        </div>
    </div>
</div>

</body>
</html>