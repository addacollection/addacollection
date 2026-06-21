<?php
session_start();
require_once '../common/config.php';

$categories = ['Womens Wear', 'Mens Wear', 'Kids Wear', 'T-Shirts', 'Shirts', 'Jeans & Denim', 'Winter Clothes', 'Sarees & Kurtis', 'Trousers & Pants'];
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#1a1512] min-h-screen text-[#EBE2D3] p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <a href="index.php" class="text-[10px] uppercase tracking-widest text-[#8C6D58] hover:text-white"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
            <a href="add_product.php" class="bg-cyan-600 px-6 py-3 rounded-2xl text-[10px] uppercase font-bold tracking-widest">Upload Clothes +</a>
        </div>

        <div class="mb-12">
            <h2 class="text-[9px] uppercase tracking-widest text-[#8C6D58] mb-4">Jump to Category</h2>
            <div class="grid grid-cols-4 md:grid-cols-9 gap-2">
                <?php foreach($categories as $c): ?>
                    <a href="view_category.php?cat=<?= urlencode($c) ?>" class="bg-[#261d19] p-3 rounded-xl border border-white/5 text-[9px] text-center uppercase hover:border-cyan-500 transition-all"><?= $c ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <h2 class="text-[9px] uppercase tracking-widest text-[#8C6D58] mb-6">All Products (Inventory)</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php foreach($products as $prod): ?>
            <div class="bg-[#261d19] p-4 rounded-3xl border border-white/5">
                <img src="../uploads/products/<?= htmlspecialchars($prod['image_url']) ?>" class="w-full h-32 object-cover rounded-2xl mb-3">
                <h3 class="text-[11px] font-bold truncate"><?= $prod['name'] ?></h3>
                <p class="text-[10px] text-cyan-500">₹<?= $prod['price'] ?></p>
                <div class="mt-3 flex gap-2">
                    <a href="edit_product.php?id=<?= $prod['id'] ?>" class="text-[9px] text-[#8C6D58] underline">Edit</a>
                    <a href="delete_product.php?id=<?= $prod['id'] ?>" class="text-[9px] text-red-500 underline">Delete</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>