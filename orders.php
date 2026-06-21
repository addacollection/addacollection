<?php
// Session start
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// 1. Centralized Database Connection (SSL included)
require_once __DIR__ . '/common/config.php';

// Auth check
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// 2. Orders fetch karo ($pdo ab config.php se aa raha hai)
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | ADDA COLLECTION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-[#FCFAF6] text-[#2C1A11] min-h-screen p-6">

    <div class="max-w-sm mx-auto">
        <header class="mb-8">
            <a href="index.php" class="text-sm font-bold uppercase"><i class="fa-solid fa-chevron-left mr-2"></i> Back</a>
            <h1 class="text-3xl font-bold mt-4">My Orders</h1>
        </header>

        <div class="space-y-4">
            <?php if (empty($orders)): ?>
                <p class="text-center text-gray-500 mt-10">No orders placed yet.</p>
            <?php else: ?>
                <?php foreach ($orders as $order): 
                    // Items fetch query
                    $items_stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.image_url as image 
                                                FROM order_items oi 
                                                JOIN products p ON oi.product_id = p.id 
                                                WHERE oi.order_id = ?");
                    $items_stmt->execute([$order['id']]);
                    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                    <div class="bg-white p-5 rounded-2xl border border-[#EFE9DE] shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-[10px] uppercase tracking-widest font-bold bg-[#FCFAF6] px-2 py-1 rounded">#ORD<?= $order['id'] ?></span>
                            <span class="text-xs font-bold text-emerald-600"><?= strtoupper($order['order_status'] ?? 'Processing') ?></span>
                        </div>

                        <div class="space-y-3 mb-4">
    <?php foreach ($items as $item): ?>
        <div class="flex gap-4 items-center">
            <img src="uploads/products/<?= htmlspecialchars($item['image']) ?>" 
                 onerror="this.onerror=null; this.src='https://via.placeholder.com/60?text=No+Img'; console.log('Path error: uploads/products/<?= $item['image'] ?>');" 
                 class="w-14 h-14 rounded-xl object-cover border">
            <div>
                <h4 class="font-bold text-sm"><?= htmlspecialchars($item['product_name']) ?></h4>
                <p class="text-[10px] text-gray-500">Qty: <?= $item['quantity'] ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

                        <div class="border-t pt-3 flex justify-between items-center">
                            <h3 class="font-bold text-sm">Total: ₹<?= number_format($order['total_amount'], 2) ?></h3>
                            <p class="text-[10px] text-gray-500"><?= date('d M, Y', strtotime($order['created_at'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>