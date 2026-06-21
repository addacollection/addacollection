<?php
// Aiven Database Configuration
$host = 'mysql-7efca4b-addacollection.i.aivencloud.com';
$dbname = 'defaultdb';
$user = 'avnadmin';
$pass = 'AVNS_h0ihm4NmXYmZcJ8ISQM';
$port = 13574;

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle Offer Update
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_offer'])) {
    $product_id = (int)$_POST['product_id'];
    $discount_percent = (int)$_POST['discount_percent'];
    
    $stmt = $pdo->prepare("UPDATE products SET discount_percent = ? WHERE id = ?");
    $stmt->execute([$discount_percent, $product_id]);
    $message = "SYSTEM UPDATE: OFFER CONFIGURATION SAVED FOR ID #$product_id";
}

$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>SECURE NODE | OFFER ENGINE</title>
</head>
<body class="bg-[#241c19] text-[#e0d6c8] font-sans">

    <header class="border-b border-[#3e342f] px-8 py-6 flex items-center justify-between bg-[#2d2421]">
        <div class="flex items-center gap-4">
            <a href="index.php" class="text-[#a89a84] hover:text-white transition">
                <i class="fa-solid fa-arrow-left"></i> BACK TO ENGINE
            </a>
            <h1 class="text-lg font-bold tracking-widest text-[#e0d6c8]">SECURE NODE // OFFER MANAGEMENT</h1>
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

        <div class="space-y-4">
            <?php foreach ($products as $product): 
                $mrp = (float)$product['price'];
                $off = (int)$product['discount_percent'];
                $offer_price = $mrp - ($mrp * $off / 100);
            ?>
            <div class="bg-[#2d2421] p-5 rounded-lg border border-[#3e342f] flex items-center justify-between hover:border-[#5c4d47] transition shadow-xl">
                <div class="flex flex-col">
                    <span class="text-[10px] text-[#a89a84] uppercase tracking-widest">Item #<?php echo $product['id']; ?></span>
                    <span class="text-sm font-bold"><?php echo htmlspecialchars($product['name']); ?></span>
                    
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-md font-bold text-[#2ecc71]">₹<?php echo number_format($offer_price, 2); ?></span>
                        
                        <?php if($off > 0): ?>
                            <span class="text-xs text-[#a89a84] line-through">₹<?php echo number_format($mrp, 2); ?></span>
                            <span class="text-[9px] bg-[#e74c3c] text-white px-2 py-0.5 rounded uppercase font-bold"><?php echo $off; ?>% OFF</span>
                        <?php endif; ?>
                    </div>
                </div>

                <form action="offers.php" method="POST" class="flex items-center gap-4">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="flex flex-col items-end">
                        <label class="text-[9px] uppercase text-[#a89a84] mb-1">Set Discount (%)</label>
                        <input type="number" name="discount_percent" value="<?php echo $off; ?>" 
                               class="w-20 bg-[#241c19] border border-[#3e342f] p-2 text-center text-sm focus:border-[#a89a84] outline-none">
                    </div>
                    
                    <button type="submit" name="apply_offer" class="bg-[#3e342f] px-6 py-3 text-[10px] uppercase font-bold hover:bg-[#a89a84] hover:text-[#241c19] transition">
                        Sync
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>