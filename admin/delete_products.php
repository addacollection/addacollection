<?php
// Database Connection
$pdo = new PDO("mysql:host=localhost;dbname=adda_collection;charset=utf8mb4", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Handle Deletion
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    $message = "PRODUCT REMOVED FROM DATABASE CLUSTER.";
}

$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>SECURE NODE | DELETE PRODS</title>
</head>
<body class="bg-[#241c19] text-[#e0d6c8] font-sans">

    <header class="border-b border-[#3e342f] px-8 py-6 flex items-center justify-between bg-[#2d2421]">
        <div class="flex items-center gap-4">
        <a href="index.php" class="text-[#a89a84] hover:text-white transition">
    <i class="fa-solid fa-arrow-left"></i> BACK TO ENGINE
</a>
            <h1 class="text-lg font-bold tracking-widest text-[#e0d6c8]">SECURE NODE // DELETE PRODS</h1>
        </div>
        <div class="px-4 py-1 border border-[#5c4d47] text-[10px] uppercase font-bold text-[#a89a84]">
            Engine Active
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-8">
        <?php if ($message): ?>
            <div class="mb-6 p-4 bg-[#3e342f] border border-[#5c4d47] text-[#e0d6c8] text-xs uppercase tracking-widest text-center shadow-lg">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-[#2d2421] rounded-lg border border-[#3e342f] overflow-hidden shadow-2xl">
            <table class="w-full text-left">
                <thead class="bg-[#241c19] border-b border-[#3e342f]">
                    <tr>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest">Product ID</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest">Name</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest">Price</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-widest text-right">Delete Engine</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#3e342f]">
                    <?php foreach ($products as $product): ?>
                        <tr class="hover:bg-[#362d2a] transition">
                            <td class="px-6 py-4 text-xs font-mono text-[#a89a84]">#<?php echo $product['id']; ?></td>
                            <td class="px-6 py-4 text-sm font-bold"><?php echo htmlspecialchars($product['name']); ?></td>
                            <td class="px-6 py-4 text-sm text-[#a89a84]">₹<?php echo number_format($product['price'], 2); ?></td>
                            <td class="px-6 py-4 text-right">
                                <form action="delete_products.php" method="POST" onsubmit="return confirm('CONFIRM DELETION FROM DATABASE?');">
                                    <input type="hidden" name="delete_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="text-[#e74c3c] hover:text-white transition uppercase text-[10px] font-bold tracking-widest flex items-center justify-end gap-2">
                                        <i class="fa-solid fa-trash"></i> Initiate
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>