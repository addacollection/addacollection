<?php
// Database Connection
$pdo = new PDO("mysql:host=localhost;dbname=adda_collection;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Handle Stock Update
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $product_id = (int)$_POST['product_id'];
    $new_stock = (int)$_POST['stock_qty'];
    
    $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
    $stmt->execute([$new_stock, $product_id]);
    $message = "SYSTEM ALERT: STOCK LEVEL SYNCHRONIZED FOR ID #$product_id";
}

// Fetch all products
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>SECURE NODE | STOCK CONTROL</title>
</head>
<body class="bg-[#241c19] text-[#e0d6c8] font-sans">

    <header class="border-b border-[#3e342f] px-8 py-6 flex items-center justify-between bg-[#2d2421]">
        <div class="flex items-center gap-4">
            <a href="index.php" class="text-[#a89a84] hover:text-white transition">
                <i class="fa-solid fa-arrow-left"></i> BACK TO ENGINE
            </a>
            <h1 class="text-lg font-bold tracking-widest text-[#e0d6c8]">SECURE NODE // STOCK CONTROL</h1>
        </div>
        <div class="px-4 py-1 border border-[#5c4d47] text-[10px] uppercase font-bold text-[#a89a84]">
            Engine Active
        </div>
    </header>

    <main class="max-w-4xl mx-auto p-8">
        <?php if ($message): ?>
            <div class="mb-6 p-4 bg-[#3e342f] border border-[#5c4d47] text-[#e0d6c8] text-xs uppercase tracking-widest text-center shadow-lg">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-[#2d2421] rounded-lg border border-[#3e342f] shadow-2xl p-6">
            <div class="space-y-4">
                <?php foreach ($products as $product): ?>
                    <form action="stock.php" method="POST" class="flex items-center justify-between p-4 bg-[#241c19] rounded border border-[#3e342f] hover:border-[#5c4d47] transition">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <div class="flex flex-col">
                            <span class="text-[10px] text-[#a89a84] uppercase tracking-widest">Item #<?php echo $product['id']; ?></span>
                            <span class="text-sm font-bold"><?php echo htmlspecialchars($product['name']); ?></span>
                            
                            <span class="text-[9px] uppercase mt-1 <?php echo ($product['stock'] > 0) ? 'text-green-500' : 'text-red-500'; ?>">
                                <?php echo ($product['stock'] > 0) ? '● Status: In Stock' : '● Status: Out of Stock'; ?>
                            </span>
                        </div>

                        <div class="flex items-center gap-4">
                            <input type="number" name="stock_qty" value="<?php echo (int)$product['stock']; ?>" 
                                   min="0"
                                   class="w-20 bg-[#2d2421] border border-[#3e342f] p-2 text-center text-sm focus:outline-none focus:border-[#a89a84]">
                            
                            <button type="submit" name="update_stock" class="text-[10px] bg-[#3e342f] px-4 py-2 hover:bg-[#a89a84] hover:text-[#241c19] transition uppercase font-bold tracking-widest">
                                Update
                            </button>
                        </div>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

</body>
</html>