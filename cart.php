<?php
// Session start
if (session_status() == PHP_SESSION_NONE) session_start();

// 1. Centralized Database Connection (SSL included)
require_once __DIR__ . '/common/config.php';

// 2. SESSION CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 3. ADD TO CART LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_to_cart') {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $product_id, $quantity]);
    
    header("Location: cart.php");
    exit();
}

// 4. DELETE LOGIC
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([intval($_GET['id']), $user_id]);
    header("Location: cart.php");
    exit();
}

// 5. FETCH CART DATA
$stmt = $pdo->prepare("SELECT cart.id as cart_id, products.* FROM cart 
                       JOIN products ON cart.product_id = products.id 
                       WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Bag | ADDA COLLECTION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Inter:wght@300;400;600&display=swap');
        body { background-color: #FDFBF7; color: #3E2723; font-family: 'Inter', sans-serif; }
        .brand-font { font-family: 'Playfair Display', serif; }
        .card-shadow { box-shadow: 0 8px 25px -5px rgba(62, 39, 35, 0.06); }
    </style>
</head>
<body class="pb-32">

    <header class="px-8 py-8 flex justify-between items-center border-b border-[#3E2723]/5 bg-white/50 backdrop-blur-md sticky top-0 z-40">
        <h1 class="brand-font text-2xl font-bold italic">Shopping Bag</h1>
        <span class="text-xs font-semibold opacity-60">(<?= count($cart_items) ?>)</span>
    </header>

    <main class="px-6 mt-8 space-y-6">
        <?php if (empty($cart_items)): ?>
            <div class="text-center py-20 bg-white rounded-[2.5rem] p-8 border border-[#3E2723]/5 card-shadow">
                <div class="w-16 h-16 bg-[#3E2723]/5 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-bag-shopping text-xl opacity-40"></i>
                </div>
                <p class="brand-font text-lg font-bold italic mb-2">Your Bag is Empty</p>
                <p class="text-xs opacity-60 mb-8 max-w-xs mx-auto">Looks like you haven't added any premium curated pieces to your collection yet.</p>
                <a href="products.php" class="inline-block bg-[#3E2723] text-white text-[10px] uppercase tracking-widest font-bold px-8 py-4 rounded-full shadow-md active:scale-95 transition-all">Explore Drops</a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($cart_items as $item): $total += $item['price']; ?>
                    <div class="flex items-center gap-5 bg-white p-4 rounded-3xl border border-[#3E2723]/5 card-shadow relative">
                        <div class="w-24 h-28 rounded-2xl overflow-hidden bg-stone-100 flex-shrink-0">
                            <img src="uploads/products/<?= htmlspecialchars($item['image'] ?? $item['image_url']) ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0 pr-6">
                            <h3 class="text-xs font-bold uppercase tracking-wide truncate text-[#3E2723]"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="text-[10px] opacity-50 mt-0.5">Size: Standard</p>
                            <p class="text-sm font-semibold text-[#3E2723] mt-4">INR <?= number_format($item['price'], 2) ?></p>
                        </div>
                        <a href="cart.php?action=delete&id=<?= $item['cart_id'] ?>" class="absolute top-5 right-5 text-stone-300 hover:text-red-600 transition">
                            <i class="fa-regular fa-trash-can text-sm"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white p-6 rounded-[2.5rem] border border-[#3E2723]/5 card-shadow space-y-4 mt-8">
                <h4 class="brand-font text-base font-bold italic border-b border-stone-100 pb-3">Price Details</h4>
                <div class="flex justify-between text-xs opacity-70"><span>Cart Subtotal</span><span>INR <?= number_format($total, 2) ?></span></div>
                <div class="flex justify-between text-xs opacity-70"><span>Delivery Charges</span><span class="text-green-600 font-semibold tracking-wide uppercase text-[10px]">Free Express</span></div>
                <div class="border-t border-dashed border-stone-200 pt-4 flex justify-between text-sm font-bold text-[#3E2723]">
                    <span>Total Pay</span>
                    <span>INR <?= number_format($total, 2) ?></span>
                </div>
                <form action="buy.php" method="POST">
    <input type="hidden" name="action" value="proceed_to_checkout">
    
    <button type="submit" class="w-full bg-[#3E2723] text-white text-xs uppercase tracking-widest font-bold py-4 rounded-full mt-2 shadow-md active:scale-95 transition-all">
        Proceed to Place Order
    </button>
</form>
            </div>
        <?php endif; ?>
    </main>

    <nav class="fixed bottom-4 left-4 right-4 z-50 bg-[#FCFAF6]/90 backdrop-blur-xl border border-[#EFE9DE]/80 flex items-center justify-around h-20 px-3 rounded-2xl shadow-[0_10px_30px_rgba(44,26,17,0.08)]">
        <a href="index.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-house text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Home</span></a>
        <a href="categories.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-border-all text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Categories</span></a>
        <a href="search.php" class="flex flex-col items-center gap-1 w-14 text-[#2C1A11]"><div class="w-8 h-8 rounded-full bg-[#F3ECE0]/80 flex items-center justify-center shadow-sm"><i class="fa-solid fa-magnifying-glass text-xs"></i></div><span class="text-[9px] font-semibold uppercase tracking-wider">Search</span></a>
        <a href="cart.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-bag-shopping text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Bag</span></a>
        <a href="profile.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-user text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Profile</span></a>
    </nav>
</body>
</html>