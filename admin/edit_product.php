<?php
// Session start
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// 1. Centralized Database Connection (SSL included)
// Path ko apni file location ke hisaab se adjust kar lena
require_once __DIR__ . '/common/config.php';

// 2. Fetch Products
try {
    // $pdo object ab central config se aa raha hai
    $products = $pdo->query("SELECT * FROM products")->fetchAll();

    $selected_p = null;
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $selected_p = $stmt->fetch();
    }
} catch (PDOException $e) {
    $products = [];
    error_log("Error fetching product data: " . $e->getMessage());
}
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
    <a href="products.php" class="text-orange-500 hover:underline mb-6 block">← Back to Inventory</a>
    <h1 class="text-3xl font-bold mb-8">Edit Product Inventory</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="md:col-span-1">
            <input type="text" id="searchInput" placeholder="Search product..." 
                   class="w-full p-3 mb-4 bg-black rounded-xl border border-gray-700 outline-none">
            
            <div id="productGrid" class="space-y-2 max-h-[600px] overflow-y-auto pr-2">
                <?php foreach ($products as $p): ?>
                    <a href="?id=<?= $p['id'] ?>" class="flex items-center gap-3 bg-card p-3 rounded-xl border border-gray-700 hover:border-orange-500 transition">
                        <img src="../uploads/products/<?= htmlspecialchars($p['image_url']) ?>" class="w-12 h-12 object-cover rounded-lg">
                        <span class="text-sm truncate"><?= htmlspecialchars($p['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="md:col-span-2">
            <?php if ($selected_p): ?>
                <form action="update_logic.php" method="POST" enctype="multipart/form-data" class="bg-card p-8 rounded-3xl border border-gray-700">
                    <input type="hidden" name="id" value="<?= $selected_p['id'] ?>">
                    
                    <div class="flex gap-6 mb-6">
                        <img src="../uploads/products/<?= htmlspecialchars($selected_p['image_url']) ?>" class="w-32 h-32 object-cover rounded-2xl border border-gray-600">
                        <div class="flex-1">
                            <label class="block text-xs uppercase text-gray-400 mb-2">Change Image</label>
                            <input type="file" name="new_image" class="w-full bg-black p-2 rounded-lg border border-gray-700">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="name" value="<?= htmlspecialchars($selected_p['name']) ?>" class="p-3 bg-black rounded-xl border border-gray-700 w-full" placeholder="Title">
                        <input type="number" name="price" value="<?= htmlspecialchars($selected_p['price']) ?>" class="p-3 bg-black rounded-xl border border-gray-700 w-full" placeholder="MRP">
                    </div>
                    <textarea name="description" rows="4" class="w-full mt-4 p-3 bg-black rounded-xl border border-gray-700"><?= htmlspecialchars($selected_p['description']) ?></textarea>
                    
                    <button type="submit" class="w-full mt-6 bg-orange-600 hover:bg-orange-700 p-4 rounded-xl font-bold transition">Update Details</button>
                </form>
            <?php else: ?>
                <div class="h-64 flex items-center justify-center text-gray-500 border-2 border-dashed border-gray-700 rounded-3xl">
                    Select a product from the list to edit
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let val = this.value.toLowerCase();
    document.querySelectorAll('#productGrid a').forEach(item => {
        item.style.display = item.innerText.toLowerCase().includes(val) ? "" : "none";
    });
});
</script>
</body>
</html>