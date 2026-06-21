<?php
session_start();
require_once '../common/config.php';

$cat = $_GET['cat'] ?? null;
if (!$cat) { header("Location: products.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY id DESC");
$stmt->execute([$cat]);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#1a1512] min-h-screen text-[#EBE2D3] p-10">
    <div class="max-w-4xl mx-auto">
        <a href="products.php" class="text-xs text-[#8C6D58] uppercase mb-6 block">← Back to Categories</a>
        <h2 class="text-2xl font-bold uppercase mb-8"><?= htmlspecialchars($cat) ?></h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach($products as $prod): ?>
            <div class="bg-[#261d19] p-4 rounded-3xl border border-white/5">
                <img src="../uploads/products/<?= htmlspecialchars($prod['image_url']) ?>" class="w-full h-40 object-cover rounded-2xl mb-3">
                <h3 class="text-sm font-bold"><?= htmlspecialchars($prod['name']) ?></h3>
                <p class="text-cyan-500">₹<?= htmlspecialchars($prod['price']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>