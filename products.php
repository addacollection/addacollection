<?php
/**
 * Adda Collection - Main Products Stream Interface (100% Live DB Only)
 * Premium grid feed with NO fake products. Completely driven by Admin Panel uploads.
 */

// Aiven Database Configuration
define('DB_HOST', 'mysql-7efca4b-addacollection.i.aivencloud.com');
define('DB_USER', 'avnadmin');
define('DB_PASS', 'AVNS_h0ihm4NmXYmZcJ8ISQM');
define('DB_NAME', 'defaultdb');
define('DB_PORT', '13574');

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    $pdo = null;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$products = [];
$status_msg = '';

// INTERCEPT FORM SUBMISSION ACTION: ADD ITEM TO USER CART
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = 1; 
    }
   
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = 1;
   
    if ($pdo && $product_id > 0) {
        try {
            $check = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $check->execute([$_SESSION['user_id'], $product_id]);
            $existing = $check->fetch();
           
            if ($existing) {
                $up = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
                $up->execute([$existing['id']]);
            } else {
                $ins = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $ins->execute([$_SESSION['user_id'], $product_id, $quantity]);
            }
            $status_msg = "Apparel added to your shopping bag!";
        } catch (PDOException $e) {
            $status_msg = "Error updating shopping cart stack.";
        }
    }
}

// FETCH CLOTHES SYSTEM SCHEMATICS
if ($pdo) {
    try {
        $sql = "SELECT p.* FROM products p WHERE 1=1";
        $params = [];
       
        if (!empty($search_query)) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.category LIKE ?)";
            $bind_term = "%{$search_query}%";
            $params[] = $bind_term;
            $params[] = $bind_term;
            $params[] = $bind_term;
        }
       
        if ($category_filter > 0) {
            $stmt_cat = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
            $stmt_cat->execute([$category_filter]);
            $cat_name = $stmt_cat->fetchColumn();
           
            $sql .= " AND (p.category_id = ? OR p.category = ?)";
            $params[] = $category_filter;
            $params[] = $cat_name;
        }
       
        $sql .= " ORDER BY p.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
       
    } catch (PDOException $e) {
        $products = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Browse Apparel — ADDA COLLECTION</title>
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

    <?php if (!empty($status_msg)): ?>
        <div class="bg-[#2C1A11] text-[#FCFAF6] text-[11px] font-medium tracking-wider uppercase text-center py-2.5 px-4 sticky top-0 z-50 flex items-center justify-center gap-2">
            <i class="fa-solid fa-circle-check text-[10px]"></i> <span><?php echo htmlspecialchars($status_msg); ?></span>
        </div>
    <?php endif; ?>

    <header class="bg-[#FCFAF6] border-b border-[#EFE9DE]/60 h-20 flex items-center justify-between px-6 sticky top-0 z-40 backdrop-blur-md bg-white/90">
        <a href="index.php" class="font-luxury text-lg font-medium tracking-[0.3em] uppercase text-[#2C1A11]">
            ADDA <span class="font-light text-[#8C6D58]">COLLECTION</span>
        </a>
        <a href="search.php" class="w-10 h-10 rounded-full bg-[#F3ECE0]/30 border border-[#EBE2D3]/40 flex items-center justify-center text-[#634832]">
            <i class="fa-solid fa-magnifying-glass text-xs"></i>
        </a>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 py-8 pb-32">
       
        <?php if (!empty($search_query)): ?>
            <div class="mb-6 pl-1 flex items-center gap-2 text-xs text-[#8C6D58]">
                <span>Search results for:</span>
                <span class="font-semibold text-[#2C1A11] bg-[#F3ECE0]/50 border border-[#EBE2D3]/40 px-2.5 py-1 rounded-lg">"<?php echo htmlspecialchars($search_query); ?>"</span>
                <a href="products.php" class="text-rose-700 font-medium ml-1 underline">Clear</a>
            </div>
        <?php endif; ?>

        <?php if (!empty($products)): ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($products as $p): ?>
    <?php 
        // SIRF YAHAN PATH CHANGE KIYA HAI
        $img = !empty($p['image_url']) ? 'uploads/products/' . $p['image_url'] : 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=500&q=80';
        $price = isset($p['price']) ? number_format((float)$p['price'], 2) : '0.00';
    ?>
    <div class="group bg-white border border-[#EBE2D3]/40 rounded-3xl p-2.5 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between">
        
        <a href="product_detail.php?id=<?php echo $p['id']; ?>" class="block relative w-full aspect-[3/4] bg-[#F3ECE0]/20 rounded-2xl overflow-hidden">
            <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" 
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-103">
        </a>

        <div class="mt-3 px-1.5 pb-2 flex-grow flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-medium text-[#2C1A11] tracking-tight group-hover:text-[#8C6D58] transition-colors line-clamp-1">
                    <?php echo htmlspecialchars($p['name']); ?>
                </h3>
                <p class="text-[10px] text-[#8C6D58] font-light tracking-tight mt-0.5 line-clamp-1">
                    <?php echo !empty($p['description']) ? htmlspecialchars($p['description']) : 'Premium live curated apparel design stock.'; ?>
                </p>
            </div>
            
            <div class="mt-3 pt-2 border-t border-[#F3ECE0]/60 flex items-center justify-between gap-2">
                <span class="text-xs font-semibold text-[#2C1A11] font-mono">₹<?php echo $price; ?></span>
                
                <form action="products.php?search=<?php echo urlencode($search_query); ?>&category=<?php echo $category_filter; ?>" method="POST">
                    <input type="hidden" name="action" value="add_to_cart">
                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                    <button type="submit" class="bg-[#2C1A11] hover:bg-[#8C6D58] text-[#FCFAF6] text-[10px] font-medium uppercase tracking-wider px-3 py-2 rounded-xl transition-colors shadow-sm">
                        + Bag
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
            </div>
        <?php else: ?>
           
            <div class="max-w-md mx-auto text-center py-16 px-4 bg-white border border-[#EBE2D3]/60 rounded-[2.5rem] my-8 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-[#F3ECE0]/40 text-[#8C6D58] border border-[#EBE2D3]/40 flex items-center justify-center mx-auto mb-5">
                    <i class="fa-solid fa-box-open text-xl font-light"></i>
                </div>
               
                <h2 class="font-luxury text-2xl italic font-medium text-[#2C1A11] tracking-tight">No Clothes Available</h2>
                <p class="text-xs font-light text-[#8C6D58] max-w-xs mx-auto mt-2 leading-relaxed">
                    Currently there are no direct clothing styles cataloged here. New arrivals added by admin will instantly reflect here.
                </p>

                <div class="mt-8 pt-6 border-t border-[#F3ECE0]/80 flex flex-col gap-2">
                    <a href="categories.php" class="bg-[#2C1A11] hover:bg-[#634832] text-white font-medium text-[10px] uppercase tracking-widest py-3.5 px-6 rounded-xl transition-all shadow-md">
                        Explore Departments
                    </a>
                    <a href="search.php" class="bg-[#F3ECE0]/30 hover:bg-[#F3ECE0]/70 border border-[#EBE2D3]/60 text-[#634832] font-medium text-[10px] uppercase tracking-widest py-3 rounded-xl transition-all">
                        Try Another Search
                    </a>
                </div>
            </div>

        <?php endif; ?>
    </main>

    <nav class="fixed bottom-4 left-4 right-4 z-50 bg-[#FCFAF6]/90 backdrop-blur-xl border border-[#EFE9DE]/80 flex items-center justify-around h-20 px-3 rounded-2xl shadow-[0_10px_30px_rgba(44,26,17,0.08)]">
        <a href="index.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-house text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Home</span></a>
        <a href="categories.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-border-all text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Categories</span></a>
        <a href="search.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-magnifying-glass text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Search</span></a>
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
