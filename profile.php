<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// --- DIRECT DATABASE CONNECTION (No config.php needed) ---
$host = '127.0.0.1';
$dbname = 'adda_collection';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
// ---------------------------------------------------------

// Auth check file validation
if (file_exists(__DIR__ . '/auth_check.php')) {
    require_once(__DIR__ . '/auth_check.php');
} else {
    die("Error: 'auth_check.php' missing in adda_collection root.");
}

// Locks the page if not logged in
check_user_access();

$user_id = $_SESSION['user_id'];

// Sign Out logic
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Fetch Active Logged-in User Data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $user = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | ADDA COLLECTION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Inter:wght@300;400;600&display=swap');
        body { background-color: #FDFBF7; color: #3E2723; font-family: 'Inter', sans-serif; }
        .brand-font { font-family: 'Playfair Display', serif; }
        .nav-text { font-size: 8px; text-transform: uppercase; letter-spacing: 0.15em; margin-top: 4px; font-weight: 600; }
        .card-shadow { box-shadow: 0 8px 25px -5px rgba(62, 39, 35, 0.06); }
    </style>
</head>
<body class="pb-32">

    <header class="px-8 pt-14 pb-10 text-center bg-white rounded-b-[3rem] border-b border-[#3E2723]/5 card-shadow">
        <div class="w-20 h-20 bg-[#3E2723] text-[#FDFBF7] rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4 tracking-tighter shadow-inner uppercase">
            <?= strtoupper(substr(htmlspecialchars($user['name'] ?? 'A'), 0, 1)) ?>
        </div>
        <h2 class="brand-font text-2xl font-bold italic text-[#3E2723]"><?= htmlspecialchars($user['name'] ?? 'Studio Member') ?></h2>
        <p class="text-[11px] opacity-50 font-medium tracking-wide mt-1"><?= htmlspecialchars($user['email'] ?? 'member@addacollection.com') ?></p>
    </header>

    <main class="px-6 mt-10 space-y-4">
        
    <a href="orders.php" class="block bg-white p-5 rounded-3xl border border-[#3E2723]/5 card-shadow active:bg-stone-50 transition">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 bg-[#3E2723]/5 rounded-full flex items-center justify-center text-[#3E2723]">
                        <i class="fa-solid fa-cube text-sm"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider">My Orders</span>
                </div>
                <i class="fa-solid fa-angle-right text-xs opacity-40"></i>
            </div>
        </a>

        <!-- Direct WhatsApp Support Card -->
        <a href="https://wa.me/919058915072?text=Hello%20Adda%20Collection%2C%20I%20need%20support%20with%20my%20account." target="_blank" class="block bg-white p-5 rounded-3xl border border-[#3E2723]/5 card-shadow active:bg-stone-50 transition">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-600">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider">WhatsApp Concierge</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[9px] uppercase tracking-wider bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full font-medium">Online</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] opacity-30"></i>
                </div>
            </div>
        </a>

        <div class="pt-6">
            <a href="profile.php?action=logout" class="block w-full text-center border border-red-100 text-red-600 bg-red-50/20 text-xs uppercase tracking-widest font-bold py-4 rounded-full shadow-sm active:bg-red-50 transition">
                Sign Out
            </a>
        </div>
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