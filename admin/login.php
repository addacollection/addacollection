<?php
/**
 * Adda Collection - Secure Admin Portal Access Node
 * Location: /admin/login.php
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fixed Admin Credentials Configuration
define('ADMIN_USER', 'Addacollection_admin');
define('ADMIN_PASS', 'Addacollection0001');

// Agar admin pehle se logged in hai, toh seedhe admin dashboard (admin/index.php) par bhejo
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header("Location: index.php");
    exit;
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_msg = "Please fill in all mandatory access paths.";
    } elseif ($username === ADMIN_USER && $password === ADMIN_PASS) {
        // Secure admin session token deployment
        $_SESSION['user_id'] = 'ADMIN_NODE_01';
        $_SESSION['user_name'] = 'Adda Admin';
        $_SESSION['user_role'] = 'admin';

        // Yeh usi folder ke andar index.php (Admin Dashboard) par le jayega
        header("Location: index.php");
        exit;
    } else {
        $error_msg = "Invalid administrative secure signature trace.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Admin Portal Gate — ADDA COLLECTION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-luxury { font-family: 'Cormorant Garamond', serif; }
    </style>
</head>
<body class="bg-[#2C1A11] text-[#FCFAF6] antialiased min-h-screen flex flex-col justify-between">

    <!-- Premium Luxury Header -->
    <header class="h-20 flex items-center justify-center border-b border-[#EFE9DE]/10 bg-[#2C1A11]">
        <div class="font-luxury text-xl font-medium tracking-[0.3em] uppercase text-[#FCFAF6]">
            ADDA <span class="font-light text-[#8C6D58]">STUDIO CONTROL</span>
        </div>
    </header>

    <!-- Main Administrative Access Node -->
    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full bg-[#352116] border border-[#EBE2D3]/10 rounded-[2rem] shadow-2xl p-6 sm:p-8">
            
            <div class="text-center mb-6">
                <div class="w-12 h-12 bg-[#8C6D58]/20 rounded-full flex items-center justify-center mx-auto mb-3 text-[#8C6D58]">
                    <i class="fa-solid fa-shield-halved text-xl"></i>
                </div>
                <h2 class="font-luxury text-xl italic text-white tracking-wide">Secure Authorization Node</h2>
                <p class="text-[9px] uppercase tracking-widest text-[#8C6D58] mt-1">Authorized Personnel Only</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="bg-rose-950/40 border border-rose-800/60 text-rose-300 text-xs px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-[11px] text-rose-400"></i>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-4">
                
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-[#8C6D58] mb-1.5">Admin Identifier</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#8C6D58]/60">
                            <i class="fa-solid fa-user text-[11px]"></i>
                        </span>
                        <input type="text" name="username" required placeholder="Admin Username"
                               class="w-full bg-[#2C1A11] text-xs text-white border border-[#EBE2D3]/10 rounded-xl pl-10 pr-4 py-3.5 focus:outline-none focus:border-[#8C6D58] transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-[#8C6D58] mb-1.5">Secret Secure Token</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#8C6D58]/60">
                            <i class="fa-solid fa-key text-[11px]"></i>
                        </span>
                        <input type="password" name="password" required placeholder="••••••••••••"
                               class="w-full bg-[#2C1A11] text-xs text-white border border-[#EBE2D3]/10 rounded-xl pl-10 pr-4 py-3.5 focus:outline-none focus:border-[#8C6D58] transition-all">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-[#8C6D58] hover:bg-[#A8846B] text-white font-semibold text-xs uppercase tracking-widest py-4 rounded-xl shadow-lg transition-all active:scale-[0.99]">
                        Verify Credentials
                    </button>
                </div>
            </form>

        </div>
    </main>

    <!-- Minimalist Footer -->
    <footer class="pb-8 text-center text-[10px] text-[#8C6D58] font-light">
        &copy; <?php echo date('Y'); ?> ADDA COLLECTION • CORE INTERFACE
    </footer>

</body>
</html>