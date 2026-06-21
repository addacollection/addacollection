<?php
/**
 * Adda Collection - Client Categories View
 * Amazon App Style Compact Boxes
 */

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'adda_collection');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    $pdo = null;
}

if (session_status() == PHP_SESSION_NONE) { session_start(); }

// 1. Fetch Categories from Database
$categories_list = [];
if ($pdo) {
    try {
        // Ensure you have a column named 'img' in your database table
        $stmt = $pdo->query("SELECT id, name, img FROM categories ORDER BY id ASC");
        $categories_list = $stmt->fetchAll();
    } catch (PDOException $e) {
        $categories_list = [];
    }
}

// 2. Fallback Categories (if DB is empty)
$default_categories = [
    ['id' => 1, 'name' => 'Womens Wear', 'img' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=200&q=80'],
    ['id' => 2, 'name' => 'Mens Wear', 'img' => 'https://images.unsplash.com/photo-1617137968427-85924c800a22?auto=format&fit=crop&w=200&q=80'],
    ['id' => 3, 'name' => 'Kids Wear', 'img' => 'https://images.unsplash.com/photo-1519457431-44ccd64a579b?auto=format&fit=crop&w=200&q=80'],
    ['id' => 4, 'name' => 'T-Shirts', 'img' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=200&q=80'],
    ['id' => 5, 'name' => 'Shirts', 'img' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=200&q=80'],
    ['id' => 6, 'name' => 'Jeans & Denim', 'img' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=200&q=80'],
    ['id' => 7, 'name' => 'Winter Clothes', 'img' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?auto=format&fit=crop&w=200&q=80'],
    ['id' => 8, 'name' => 'Sarees & Kurtis', 'img' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?auto=format&fit=crop&w=200&q=80'],
    ['id' => 9, 'name' => 'Trousers & Pants', 'img' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?auto=format&fit=crop&w=200&q=80'],
    ['id' => 11, 'name' => 'Ethnic & Kurtis', 'img' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?auto=format&fit=crop&w=200&q=80'],
    ['id' => 12, 'name' => 'Hoodies & Sweaters', 'img' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=200&q=80'],
    ['id' => 13, 'name' => 'Suits & Blazers', 'img' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&w=200&q=80']
];

if (empty($categories_list)) {
    $categories_list = $default_categories;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Shop Clothes — ADDA COLLECTION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-user-select: none; user-select: none; }
        .font-luxury { font-family: 'Cormorant Garamond', serif; }
    </style>
</head>
<body class="bg-[#FCFAF6] text-[#2C1A11] antialiased min-h-screen flex flex-col justify-between">

    <header class="sticky top-0 z-50 bg-[#FCFAF6]/90 backdrop-blur-xl border-b border-[#EFE9DE]/60 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between gap-6">
            <a href="index.php" class="font-luxury text-lg font-medium tracking-[0.3em] uppercase text-[#2C1A11]">
                ADDA <span class="font-light text-[#8C6D58]">COLLECTION</span>
            </a>
            <div class="flex items-center gap-4">
                <a href="profile.php" class="w-11 h-11 rounded-full bg-[#F3ECE0]/40 flex items-center justify-center border border-[#EBE2D3]/30 text-[#634832]"><i class="fa-regular fa-user text-sm"></i></a>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-5xl w-full mx-auto px-4 py-8 pb-32">
        <div class="mb-8 pl-2">
            <h1 class="font-luxury text-3xl italic font-light tracking-tight text-[#2C1A11]">Apparel Catalog</h1>
            <p class="text-xs text-[#8C6D58] font-light">Select a clothes category to view curated apparel variants.</p>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-5 gap-y-8 gap-x-4 justify-items-center">
            <?php foreach ($categories_list as $cat): 
                // Logic: Check if DB has img, otherwise use default
                $img_url = !empty($cat['img']) ? $cat['img'] : 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=200&q=80';
            ?>
                <a href="products.php?category=<?php echo $cat['id']; ?>" class="group flex flex-col items-center text-center max-w-[100px]">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden bg-[#F3ECE0]/50 p-1 border border-[#EBE2D3]/60 shadow-sm group-hover:shadow-md group-hover:border-[#8C6D58]/60 transition-all duration-300">
                        <div class="w-full h-full rounded-xl overflow-hidden bg-white">
                            <img src="<?php echo htmlspecialchars($img_url); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>
                    <span class="text-[11px] font-medium text-[#2C1A11] mt-2.5 tracking-tight group-hover:text-[#8C6D58] transition-colors line-clamp-1 w-full">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="bg-[#F3ECE0]/50 border-t border-[#EBE2D3] pb-28 pt-8 text-[#634832] text-xs text-center">
        <p class="font-light text-[#8C6D58] text-[11px]">&copy; <?php echo date('Y'); ?> ADDA COLLECTION. All Rights Reserved.</p>
    </footer>

    <nav class="fixed bottom-4 left-4 right-4 z-50 bg-[#FCFAF6]/90 backdrop-blur-xl border border-[#EFE9DE]/80 flex items-center justify-around h-20 px-3 rounded-2xl shadow-[0_10px_30px_rgba(44,26,17,0.08)]">
        <a href="index.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-house text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Home</span></a>
        <a href="categories.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-border-all text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Categories</span></a>
        <a href="search.php" class="flex flex-col items-center gap-1 w-14 text-[#2C1A11]"><div class="w-8 h-8 rounded-full bg-[#F3ECE0]/80 flex items-center justify-center shadow-sm"><i class="fa-solid fa-magnifying-glass text-xs"></i></div><span class="text-[9px] font-semibold uppercase tracking-wider">Search</span></a>
        <a href="cart.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-bag-shopping text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Bag</span></a>
        <a href="profile.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-user text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Profile</span></a>
    </nav>
</body>
</html>