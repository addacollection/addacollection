<?php
/**
 * Adda Collection - Ultra Premium Ivory & Rich Espresso Brown Edit
 */

// 1. Centralized Database Connection (SSL included)
require_once __DIR__ . '/common/config.php';

// Session start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$cart_count = 0;
$new_count = 0; 

// $pdo ab common/config.php se automatically global variable ban kar aa raha hai
if (isset($_SESSION['user_id']) && $pdo) {
    // Fetch Cart Count
    try {
        $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $cart_count = (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        $cart_count = 0;
    }

    // Notification Count Logic
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM notifications n
            WHERE n.id NOT IN (SELECT notification_id FROM notification_read WHERE user_id = ?)
            AND n.id NOT IN (SELECT notification_id FROM user_notification_status WHERE user_id = ?)
        ");
        $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
        $new_count = (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        $new_count = 0;
    }
}

// Fetch Categories & Products
$categories = [];
$featured_products = [];

if ($pdo) {
    try {
        $categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC LIMIT 6")->fetchAll();
        $featured_products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC LIMIT 8")->fetchAll();
    } catch (PDOException $e) {}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ADDA COLLECTION — Quiet Luxury Maison</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-user-select: none; user-select: none; }
        .font-luxury { font-family: 'Cormorant Garamond', serif; }
        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#FCFAF6] text-[#2C1A11] antialiased min-h-screen flex flex-col justify-between">

<header class="sticky top-0 z-50 bg-[#FCFAF6]/90 backdrop-blur-xl border-b border-[#EFE9DE]/50 shadow-[0_2px_24px_rgba(44,26,17,0.02)]">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between gap-6">
        
        <a href="index.php" class="font-luxury text-lg font-medium tracking-[0.3em] uppercase text-[#2C1A11] transition-opacity hover:opacity-80">
            ADDA <span class="font-light text-[#8C6D58]">COLLECTION</span>
        </a>

        <form action="products.php" method="GET" class="flex-1 max-w-sm hidden md:block">
            <div class="relative">
                <input type="text" name="search" placeholder="Search our signature aesthetics..." 
                       class="w-full bg-[#F3ECE0]/40 text-xs font-light text-[#2C1A11] border border-[#EBE2D3]/60 rounded-full pl-5 pr-12 py-3 focus:outline-none focus:border-[#634832] focus:bg-[#FCFAF6] placeholder-[#A89A84] shadow-sm transition-all duration-300">
                <button type="submit" class="absolute right-4 top-3.5 text-[#8C6D58] hover:text-[#2C1A11] transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </button>
            </div>
        </form>

        <div class="flex items-center gap-4">
            <!-- New Notification Icon with Indicator -->
            <a href="notifications.php" class="relative w-11 h-11 rounded-full bg-[#F3ECE0]/40 flex items-center justify-center border border-[#EBE2D3]/30 hover:bg-[#F3ECE0]/80 text-[#634832] hover:text-[#2C1A11] transition-all duration-300 shadow-sm">
                <i class="fa-regular fa-bell text-sm"></i>
                <?php if($new_count > 0): ?>
                    <span class="absolute top-2 right-2 flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                    </span>
                <?php endif; ?>
            </a>
            <a href="profile.php" class="w-11 h-11 rounded-full bg-[#F3ECE0]/40 flex items-center justify-center border border-[#EBE2D3]/30 hover:bg-[#F3ECE0]/80 text-[#634832] hover:text-[#2C1A11] transition-all duration-300 shadow-sm" title="Manage Account Profile">
                <i class="fa-regular fa-user text-sm"></i>
            </a>
        </div>
    </div>
</header>

<main class="flex-grow pb-32">

<section class="max-w-7xl mx-auto px-4 sm:px-6 pt-4">
            <div class="relative bg-[#EBE2D3] overflow-hidden md:h-[560px] h-[420px] rounded-[2.5rem] shadow-[0_15px_45px_rgba(44,26,17,0.06)] border border-[#EFE9DE]">
                <div class="absolute inset-0 bg-gradient-to-r from-[#2C1A11]/60 via-[#2C1A11]/20 to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1485968579580-b6d095142e6e?auto=format&fit=crop&w=1200&q=80" 
                     alt="Editorial Lookbook" 
                     class="w-full h-full object-cover object-center absolute inset-0 transform scale-100 hover:scale-103 transition-transform duration-[2000ms]">
                
                <div class="relative z-20 max-w-2xl h-full flex flex-col justify-center px-8 md:px-16 text-[#FCFAF6]">
                    <span class="text-[10px] font-semibold tracking-[0.3em] uppercase text-[#F3ECE0]/90 mb-4 block">The Premium Collective</span>
                    <h1 class="font-luxury text-4xl md:text-7xl font-light italic tracking-tight mb-5 leading-[1.1]">
                        Effortless Luxury,<br><span class="not-italic font-medium text-white">Soft Silhouettes.</span>
                    </h1>
                    <p class="max-w-xs text-xs md:text-sm font-light text-[#F3ECE0]/80 mb-10 leading-relaxed tracking-wide">
                        Experience clothing structured with meticulous geometry, earthy shades, and unmatched fluid textures.
                    </p>
                    <div>
                        <a href="products.php" class="inline-flex items-center justify-center bg-[#FCFAF6] text-[#2C1A11] font-medium tracking-widest text-[10px] uppercase px-8 py-4 rounded-full shadow-lg hover:bg-[#F3ECE0] hover:scale-102 transition-all duration-300">
                            Discover The Edit <i class="fa-solid fa-arrow-right-long ml-3 text-[10px] text-[#8C6D58]"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-7xl mx-auto px-6 py-4"> 
    <div class="flex flex-col md:flex-row items-center gap-4">
        
        <div class="w-full md:w-1/2">
            <div class="h-64 overflow-hidden rounded-[2rem] shadow-md">
                <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=80" 
                     alt="Banner" 
                     class="w-full h-full object-cover">
            </div>
        </div>

        <div class="w-full md:w-1/2 flex flex-col gap-2">
            <span class="text-[9px] uppercase tracking-[0.2em] text-[#8C6D58] font-bold">Signature Curation</span>
            <h2 class="font-luxury text-3xl text-[#2C1A11]">Timeless Elegance</h2>
            <p class="text-sm text-[#634832] leading-relaxed">
                More than just a design—an experience. Fusing minimalist aesthetics with premium quality.
            </p>
            <a href="products.php" class="mt-1 text-[9px] uppercase tracking-widest border-b border-[#2C1A11] w-fit">Explore Now</a>
        </div>
    </div>
</section>

        <section class="max-w-7xl mx-auto px-6 mt-16">
    <div class="flex items-baseline justify-between mb-8 border-b border-[#EFE9DE] pb-4">
        <h2 class="text-xs font-semibold uppercase tracking-[0.25em] text-[#2C1A11]">Editorial Maison</h2>
        <span class="text-[11px] font-light italic text-[#8C6D58]">Visual Storytelling</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="group relative aspect-[16/10] overflow-hidden rounded-[2rem] bg-[#EFE9DE]">
            <img src="https://picsum.photos/seed/fashion1/800/600" 
                 alt="The Ivory Edit" 
                 class="w-full h-full object-cover transition-transform duration-[2000ms] group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-[#2C1A11]/70 to-transparent p-8 flex flex-col justify-end">
                <h3 class="font-luxury text-2xl text-white mb-1">The Ivory Edit</h3>
                <p class="text-[11px] text-[#EBE2D3]/90 font-light tracking-wide">
                    Minimalist silhouettes meeting urban elegance.
                </p>
            </div>
        </div>

        <div class="group relative aspect-[16/10] overflow-hidden rounded-[2rem] bg-[#EFE9DE]">
            <img src="https://picsum.photos/seed/fashion2/800/600" 
                 alt="Raw Texture" 
                 class="w-full h-full object-cover transition-transform duration-[2000ms] group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-[#2C1A11]/70 to-transparent p-8 flex flex-col justify-end">
                <h3 class="font-luxury text-2xl text-white mb-1">Raw Texture</h3>
                <p class="text-[11px] text-[#EBE2D3]/90 font-light tracking-wide">
                    Crafting narratives through fabric and form.
                </p>
            </div>
        </div>

    </div>
</section>

    </main>

    <footer class="bg-[#F3ECE0]/50 border-t border-[#EBE2D3] pb-28 pt-12 text-[#634832] text-xs">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="text-center md:text-left">
                <h4 class="text-[10px] font-semibold tracking-widest uppercase text-[#2C1A11] mb-4">ADDA COLLECTION</h4>
                <p class="font-light text-[#8C6D58] leading-relaxed text-[11px] max-w-sm">Premium apparel engineered around rich minimalist aesthetics, clean architectures, and organic dynamic luxury elements.</p>
            </div>
            
            <div class="text-center md:text-left space-y-2">
                <h4 class="text-[10px] font-semibold tracking-widest uppercase text-[#2C1A11] mb-4">Client Concierge</h4>
                <div class="flex items-center justify-center md:justify-start gap-2.5 text-[11px] font-light text-[#8C6D58]">
                    <i class="fa-regular fa-envelope text-xs text-[#2C1A11]"></i>
                    <a href="mailto:dvvvrrb@gmail.com" class="hover:text-[#2C1A11] transition-colors underline decoration-[#EBE2D3]">dvvvrrb@gmail.com</a>
                </div>
                <div class="flex items-center justify-center md:justify-start gap-2.5 text-[11px] font-light text-[#8C6D58]">
                    <i class="fa-brands fa-whatsapp text-xs text-emerald-600"></i>
                    <a href="https://wa.me/919058915072" target="_blank" class="hover:text-[#2C1A11] transition-colors font-medium">WhatsApp Support</a>
                </div>
            </div>
            
            <div class="text-center md:text-left">
                <h4 class="text-[10px] font-semibold tracking-widest uppercase text-[#2C1A11] mb-4">Secure Network</h4>
                <p class="font-light text-[#8C6D58] text-[11px] leading-relaxed">Optimized architectural stack targeting high-availability cloud cluster distributions.</p>
            </div>
        </div>
        <div class="text-center mt-12 pt-6 border-t border-[#EBE2D3]/60 text-[9px] uppercase tracking-[0.25em] text-[#A89A84]">
            &copy; <?php echo date('Y'); ?> ADDA COLLECTION. All Rights Reserved.
        </div>
    </footer>

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
                if (e.key === 'a' || e.key === 'A' || e.key === '+' || e.key === '=' || e.key === '-' || e.key === '_' || e.key === 'u' || e.key === 'U') {
                    e.preventDefault();
                }
            }
        }, false);
        document.addEventListener('gesturestart', function(e) { e.preventDefault(); });
    </script>
</body>
</html>