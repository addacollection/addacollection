<?php
/**
 * Adda Collection - Professional Amazon-Inspired Search Hub
 */

// Database connection ko include kar lo (path check kar lena)
require_once __DIR__ . '/common/config.php';

// Session management
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// 13 Most searched trending apparel categories
$trending_tags = [
    'Oversized T-Shirts', 'Premium Shirts', 'Summer Dresses', 
    'Denim Jeans', 'Kurtis', 'Formal Blazers', 'Chinos', 
    'Co-ord Sets', 'Hoodies', 'Cargo Pants', 'Skirts', 'Winter Sweaters'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Search Wardrobe — ADDA COLLECTION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-user-select: none; user-select: none; }
        .font-luxury { font-family: 'Cormorant Garamond', serif; }
    </style>
</head>
<body class="bg-[#FCFAF6] text-[#2C1A11] antialiased min-h-screen flex flex-col justify-between">

    <header class="sticky top-0 z-50 bg-[#FCFAF6] border-b border-[#EFE9DE]/80 px-4 py-3 flex items-center gap-3">
        <a href="index.php" class="text-[#634832] text-sm px-2"><i class="fa-solid fa-arrow-left"></i></a>
        
        <form action="products.php" method="GET" class="flex-grow">
            <div class="relative w-full">
                <input type="text" name="search" autofocus autocomplete="off" placeholder="Search for dresses, shirts, brands..." 
                       class="w-full bg-[#F3ECE0]/40 text-xs text-[#2C1A11] border border-[#EBE2D3] rounded-xl pl-4 pr-10 py-3 focus:outline-none focus:border-[#634832] focus:bg-white transition-all placeholder-[#A89A84]">
                <button type="submit" class="absolute right-3 top-2.5 text-[#634832] p-0.5"><i class="fa-solid fa-magnifying-glass text-xs"></i></button>
            </div>
        </form>
    </header>

    <main class="flex-grow max-w-xl w-full mx-auto px-5 py-6 pb-28">
        
        <div class="space-y-4">
            <div class="flex items-center gap-2 text-[#8C6D58]">
                <i class="fa-solid fa-arrow-trend-up text-[10px]"></i>
                <h2 class="text-[10px] font-bold tracking-widest uppercase">Popular Trending Apparel</h2>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <?php foreach ($trending_tags as $tag): ?>
                    <a href="products.php?search=<?php echo urlencode($tag); ?>" 
                       class="bg-white border border-[#EBE2D3]/60 text-[#2C1A11] text-xs px-3.5 py-2 rounded-xl transition-all hover:border-[#8C6D58] shadow-[0_2px_6px_rgba(0,0,0,0.02)]">
                        <?php echo htmlspecialchars($tag); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-12 p-4 bg-[#F3ECE0]/20 border border-[#EBE2D3]/40 rounded-xl flex gap-3 items-start">
            <i class="fa-solid fa-circle-info text-[#8C6D58] mt-0.5 text-xs"></i>
            <p class="text-[11px] leading-relaxed font-light text-[#8C6D58]">
                Type clothing terms like <span class="font-medium text-[#2C1A11]">"Linen"</span>, <span class="font-medium text-[#2C1A11]">"Oversized T-Shirts"</span>, or specific item matches to query our active premium stock catalog lines.
            </p>
        </div>
    </main>

    <nav class="fixed bottom-4 left-4 right-4 z-50 bg-[#FCFAF6]/90 backdrop-blur-xl border border-[#EFE9DE]/80 flex items-center justify-around h-20 px-3 rounded-2xl shadow-[0_10px_30px_rgba(44,26,17,0.08)]">
        <a href="index.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-house text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Home</span></a>
        <a href="categories.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-border-all text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Categories</span></a>
        <a href="search.php" class="flex flex-col items-center gap-1 w-14 text-[#2C1A11]"><div class="w-8 h-8 rounded-full bg-[#F3ECE0]/80 flex items-center justify-center shadow-sm"><i class="fa-solid fa-magnifying-glass text-xs"></i></div><span class="text-[9px] font-semibold uppercase tracking-wider">Search</span></a>
        <a href="cart.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-bag-shopping text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Bag</span></a>
        <a href="profile.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-user text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Profile</span></a>
    </nav>

    <script>
        document.addEventListener('contextmenu', function(e) { e.preventDefault(); }, false);
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                if (e.key === 'a' || e.key === 'A' || e.key === 'u' || e.key === 'U') { e.preventDefault(); }
            }
        }, false);
    </script>
</body>
</html>