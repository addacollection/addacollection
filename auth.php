<?php
/**
 * Adda Collection - Unified Auth System (Login & Signup with Security Guard)
 * Monolithic architecture with secure password hashing, session tracking, and ban filtering.
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

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already authenticated
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $redirect = $_SESSION['redirect_to'] ?? 'index.php';
    unset($_SESSION['redirect_to']);
    header("Location: " . $redirect);
    exit;
}

$error_msg = '';
$success_msg = '';
$active_tab = 'login'; // Controls UI toggle state on submission error
$account_banned_trigger = false; // Controls Ban Alert Modal Trigger State

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$pdo) {
        $error_msg = "Database Connection Failed. Please ensure MySQL is active in XAMPP.";
    } else {
        $action = $_POST['action'] ?? '';

        // LOGIN LOGIC
        if ($action === 'login') {
            $active_tab = 'login';
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error_msg = "Please fill in all input fields.";
            } else {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();

                    if ($user && password_verify($password, $user['password'])) {
                        
                        // CRITICAL PROTECTION BLOCK: Check if User has been Blacklisted/Banned
                        if (isset($user['is_banned']) && $user['is_banned'] == 1) {
                            $account_banned_trigger = true; // Opens the Modal popup visually
                            $error_msg = "Security Restriction: This account node has been banned by management.";
                        } else {
                            // Normal Login Proceed Handler
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['user_name'] = $user['name'];
                            $_SESSION['user_role'] = $user['role'] ?? 'user';
                            
                            // Intelligent routing architecture
                            $redirect = $_SESSION['redirect_to'] ?? (($_SESSION['user_role'] === 'admin') ? 'admin/index.php' : 'index.php');
                            unset($_SESSION['redirect_to']);
                            
                            header("Location: " . $redirect);
                            exit;
                        }
                    } else {
                        $error_msg = "Invalid email address or incorrect password security trace.";
                    }
                } catch (PDOException $e) {
                    $error_msg = "Database lookup authentication failure: " . $e->getMessage();
                }
            }
        }

        // SIGNUP LOGIC
        if ($action === 'signup') {
            $active_tab = 'signup';
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($name) || empty($email) || empty($password)) {
                $error_msg = "All registration input fields are mandatory.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_msg = "Please provide a valid structured email address.";
            } elseif (strlen($password) < 6) {
                $error_msg = "Password length must be at least 6 characters long.";
            } else {
                try {
                    // Verify unique account availability
                    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                    $check->execute([$email]);
                    
                    if ($check->fetch()) {
                        $error_msg = "This email identifier is already registered in our node.";
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                        
                        // Set default role as 'user' and is_banned as 0
                        $ins = $pdo->prepare("INSERT INTO users (name, email, password, role, is_banned) VALUES (?, ?, ?, 'user', 0)");
                        $ins->execute([$name, $email, $hashed_password]);
                        
                        $success_msg = "Account created successfully! Please sign in now.";
                        $active_tab = 'login'; // Send user to login tab automatically
                    }
                } catch (PDOException $e) {
                    $error_msg = "Database Error: " . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Account Gate — ADDA COLLECTION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-luxury { font-family: 'Cormorant Garamond', serif; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body class="bg-[#FCFAF6] text-[#2C1A11] antialiased min-h-screen flex flex-col justify-between">

    <?php if ($account_banned_trigger): ?>
    <div id="banModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all animate-fadeIn">
        <div class="bg-white border-2 border-rose-600 w-full max-w-md rounded-[2.5rem] p-6 shadow-2xl text-center relative overflow-hidden transform scale-100 transition-all">
            <div class="absolute top-0 left-0 w-full h-2 bg-rose-600"></div>
            
            <div class="w-16 h-16 bg-rose-50 border border-rose-200 text-rose-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 animate-bounce mt-2">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            
            <h3 class="font-luxury text-2xl font-bold tracking-wide text-rose-700 uppercase">Access Restrict Protocol</h3>
            <p class="text-xs text-[#2C1A11] font-medium tracking-tight mt-2 px-2">
            Your account has been suspended by the administrator. Access is currently restricted due to a violation of our security and usage policies.
            </p>
            
            <div class="bg-stone-50 border border-stone-200/60 rounded-xl p-3 my-4 text-left font-mono text-[10px] text-gray-500 space-y-1">
                <div><span class="font-bold text-[#8C6D58]">GATE IDENTIFIER:</span> SECURITY_TRACE_LOCKED</div>
                <div><span class="font-bold text-[#8C6D58]">ACTION REQUIRED:</span> Contact customer help support desk.</div>
            </div>

            <button type="button" onclick="document.getElementById('banModal').remove();" class="w-full bg-rose-600 hover:bg-rose-700 text-white text-xs uppercase font-bold tracking-widest py-3.5 rounded-xl shadow-md transition">
                Acknowledge Restrictions
            </button>
        </div>
    </div>
    <?php endif; ?>

    <header class="h-20 flex items-center justify-center border-b border-[#EFE9DE]/60 bg-[#FCFAF6]">
        <a href="index.php" class="font-luxury text-xl font-medium tracking-[0.3em] uppercase text-[#2C1A11]">
            ADDA <span class="font-light text-[#8C6D58]">COLLECTION</span>
        </a>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 py-12 pb-32">
        <div class="max-w-md w-full bg-white border border-[#EBE2D3]/60 rounded-[2rem] shadow-sm p-6 sm:p-8">
            
            <?php if (!empty($error_msg)): ?>
                <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-[11px]"></i>
                    <span><?php echo htmlspecialchars($error_msg); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_msg)): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-[11px]"></i>
                    <span><?php echo htmlspecialchars($success_msg); ?></span>
                </div>
            <?php endif; ?>

            <div class="flex border-b border-[#F3ECE0] mb-6">
                <button type="button" onclick="switchTab('login')" id="btn-login" 
                        class="flex-1 pb-3 text-xs uppercase tracking-wider font-semibold border-b-2 transition-all text-center">
                    Sign In
                </button>
                <button type="button" onclick="switchTab('signup')" id="btn-signup" 
                        class="flex-1 pb-3 text-xs uppercase tracking-wider font-semibold border-b-2 transition-all text-center">
                    Register
                </button>
            </div>

            <div id="content-login" class="tab-content">
                <form action="auth.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="login">
                    
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[#8C6D58] mb-1.5">Email Address</label>
                        <input type="email" name="email" required placeholder="name@domain.com"
                               value="<?php echo isset($_POST['email']) && $active_tab === 'login' ? htmlspecialchars($_POST['email']) : ''; ?>"
                               class="w-full bg-[#F3ECE0]/20 text-xs text-[#2C1A11] border border-[#EBE2D3] rounded-xl px-4 py-3 focus:outline-none focus:border-[#634832] focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[#8C6D58] mb-1.5">Password</label>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full bg-[#F3ECE0]/20 text-xs text-[#2C1A11] border border-[#EBE2D3] rounded-xl px-4 py-3 focus:outline-none focus:border-[#634832] focus:bg-white transition-all">
                    </div>

                    <button type="submit" class="w-full bg-[#2C1A11] hover:bg-[#634832] text-white font-medium text-xs uppercase tracking-widest py-4 rounded-xl shadow-md transition-all">
                        Secure Sign In
                    </button>
                </form>
            </div>

            <div id="content-signup" class="tab-content">
                <form action="auth.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="signup">
                    
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[#8C6D58] mb-1.5">Full Name</label>
                        <input type="text" name="name" required placeholder="John Doe"
                               value="<?php echo isset($_POST['name']) && $active_tab === 'signup' ? htmlspecialchars($_POST['name']) : ''; ?>"
                               class="w-full bg-[#F3ECE0]/20 text-xs text-[#2C1A11] border border-[#EBE2D3] rounded-xl px-4 py-3 focus:outline-none focus:border-[#634832] focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[#8C6D58] mb-1.5">Email Address</label>
                        <input type="email" name="email" required placeholder="name@domain.com"
                               value="<?php echo isset($_POST['email']) && $active_tab === 'signup' ? htmlspecialchars($_POST['email']) : ''; ?>"
                               class="w-full bg-[#F3ECE0]/20 text-xs text-[#2C1A11] border border-[#EBE2D3] rounded-xl px-4 py-3 focus:outline-none focus:border-[#634832] focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-[#8C6D58] mb-1.5">Create Password</label>
                        <input type="password" name="password" required placeholder="Min 6 characters"
                               class="w-full bg-[#F3ECE0]/20 text-xs text-[#2C1A11] border border-[#EBE2D3] rounded-xl px-4 py-3 focus:outline-none focus:border-[#634832] focus:bg-white transition-all">
                    </div>

                    <p class="text-[10px] text-[#8C6D58] font-light leading-relaxed pt-1">
                        By creating an account, you agree to access curated minimalist apparel variants matching our inventory policy.
                    </p>

                    <button type="submit" class="w-full bg-[#2C1A11] hover:bg-[#634832] text-white font-medium text-xs uppercase tracking-widest py-4 rounded-xl shadow-md transition-all">
                        Agree & Register
                    </button>
                </form>
            </div>

        </div>
    </main>

    <footer class="pb-28 text-center text-[11px] text-[#8C6D58] font-light space-y-2">
        <div>
            &copy; <?php echo date('Y'); ?> ADDA COLLECTION. Secure Authorization Node.
        </div>
        <div class="flex justify-center items-center gap-3 opacity-60 hover:opacity-100 transition-all">
            <a href="terms.php" class="hover:underline tracking-wider uppercase text-[9px] font-medium">Terms & Conditions</a>
            <span class="text-[8px] opacity-40">•</span>
            <a href="privacy.php" class="hover:underline tracking-wider uppercase text-[9px] font-medium">Privacy Policy</a>
        </div>
    </footer>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            const btnLogin = document.getElementById('btn-login');
            const btnSignup = document.getElementById('btn-signup');
            
            if (tab === 'login') {
                document.getElementById('content-login').classList.add('active');
                btnLogin.className = "flex-1 pb-3 text-xs uppercase tracking-wider font-semibold border-b-2 text-[#2C1A11] border-[#2C1A11]";
                btnSignup.className = "flex-1 pb-3 text-xs uppercase tracking-wider font-medium border-b-2 text-[#A89A84] border-transparent";
            } else {
                document.getElementById('content-signup').classList.add('active');
                btnSignup.className = "flex-1 pb-3 text-xs uppercase tracking-wider font-semibold border-b-2 text-[#2C1A11] border-[#2C1A11]";
                btnLogin.className = "flex-1 pb-3 text-xs uppercase tracking-wider font-medium border-b-2 text-[#A89A84] border-transparent";
            }
        }

        window.addEventListener('DOMContentLoaded', (event) => {
            switchTab('<?php echo $active_tab; ?>');
        });
    </script>
</body>
</html>