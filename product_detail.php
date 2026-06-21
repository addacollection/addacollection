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
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection Failed: " . $e->getMessage());
}

$product = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['name']) : 'Product'; ?> | ADDA COLLECTION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-[#FCFAF6] text-[#2C1A11] min-h-screen">
<?php if ($product): 
    $img = !empty($product['image_url']) ? 'uploads/products/' . $product['image_url'] : 'https://via.placeholder.com/400';
?>
    <header class="p-5"><a href="products.php" class="text-sm font-bold uppercase"><i class="fa-solid fa-chevron-left mr-2"></i> Back</a></header>
    <main class="max-w-sm mx-auto px-4 pb-10">
        <div class="w-full aspect-square bg-white p-2 rounded-[2rem] shadow-sm mb-6 border border-[#EFE9DE]">
            <img src="<?php echo $img; ?>" class="w-full h-full object-cover rounded-[1.5rem]">
        </div>
        <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($product['name']); ?></h1>
        <p class="text-lg font-medium mt-1">₹<?php echo number_format((float)$product['price'], 2); ?></p>

        <div class="mt-6 flex flex-col gap-3">
            <form action="cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="quantity" value="1"> <button type="submit" class="w-full py-4 border-2 border-[#2C1A11] rounded-2xl font-bold hover:bg-[#2C1A11] hover:text-white transition">Add to Cart</button>
            </form>

            <form action="buy.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="action" value="buy_now">
                <input type="hidden" name="quantity" value="1"> <button type="submit" class="w-full py-4 bg-[#2C1A11] text-white rounded-2xl font-bold hover:bg-[#4a3225] transition">Buy Now</button>
            </form>
        </div>

        <div class="mt-8 pt-6 border-t border-[#EFE9DE]">
            <h3 class="font-bold uppercase text-xs mb-2 text-gray-500">Description</h3>
            <p class="text-sm text-gray-700 leading-relaxed">
                <?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available.')); ?>
            </p>
        </div>
    </main>
<?php endif; ?>
</body>
</html>