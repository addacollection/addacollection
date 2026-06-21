<?php
/**
 * Adda Collection — User Intelligence & Diagnostic Profile Console
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Strict Security Gate
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// 1. Centralized Database Connection (SSL included)
require_once __DIR__ . '/../common/config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$userId = (int)$_GET['id'];

$orders_success = 0; $orders_rejected = 0; $orders_refund = 0; $orders_pending = 0;
$total_orders = 0; $total_investment = 0; $order_history = [];

try {
    // User details fetch
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        die("System Alert: Request profile not found.");
    }

    // Safety checks for columns
    $checkStatusCol = $pdo->query("SHOW COLUMNS FROM `orders` LIKE 'status'")->fetch();
    $checkPriceCol = $pdo->query("SHOW COLUMNS FROM `orders` LIKE 'total_price'")->fetch();
    
    // Metrics
    $stmt = $pdo->prepare("SELECT COUNT(id) FROM orders WHERE user_id = ?");
    $stmt->execute([$userId]);
    $total_orders = $stmt->fetchColumn() ?: 0;

    if ($checkStatusCol) {
        $stats = ['delivered', 'cancelled', 'refunded', 'pending'];
        foreach ($stats as $s) {
            $stmt = $pdo->prepare("SELECT COUNT(id) FROM orders WHERE user_id = ? AND status = ?");
            $stmt->execute([$userId, $s]);
            $count = $stmt->fetchColumn() ?: 0;
            
            // Assign to variables
            if ($s == 'delivered') $orders_success = $count;
            elseif ($s == 'cancelled') $orders_rejected = $count;
            elseif ($s == 'refunded') $orders_refund = $count;
            elseif ($s == 'pending') $orders_pending = $count;
        }

        if ($checkPriceCol) {
            $stmt = $pdo->prepare("SELECT SUM(total_price) FROM orders WHERE user_id = ? AND status = 'delivered'");
            $stmt->execute([$userId]);
            $total_investment = $stmt->fetchColumn() ?: 0;
        }
    }

    $queryStr = "SELECT id, created_at";
    if ($checkPriceCol) { $queryStr .= ", total_price"; }
    if ($checkStatusCol) { $queryStr .= ", status"; }
    $queryStr .= " FROM orders WHERE user_id = ? ORDER BY id DESC";

    $stmt = $pdo->prepare($queryStr);
    $stmt->execute([$userId]);
    $order_history = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NODE INTEL — PROFILE #<?php echo $user['id']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            body { background: #ffffff !important; color: #000000 !important; }
            .no-print { display: none !important; }
            .print-card { background: #ffffff !important; border: 1px solid #e5e7eb !important; color: #000000 !important; box-shadow: none !important; border-radius: 0.5rem !important; }
            .text-white { color: #000000 !important; }
            .text-gray-400, .text-[#8C6D58] { color: #4b5563 !important; }
            table { border-collapse: collapse !important; }
            th { background: #f3f4f6 !important; color: #000000 !important; }
            td, th { border: 1px solid #e5e7eb !important; padding: 8px !important; }
        }
    </style>
</head>

<script>
function refreshStats() {
    fetch('get_user_stats.php?id=<?php echo $userId; ?>')
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll('.total-requests-val').forEach(el => el.innerText = data.total || 0);
            document.querySelectorAll('.success-val').forEach(el => el.innerText = data.success || 0);
            document.querySelectorAll('.pending-val').forEach(el => el.innerText = data.pending || 0);
            document.querySelectorAll('.rejected-val').forEach(el => el.innerText = data.rejected || 0);
            document.querySelectorAll('.refund-val').forEach(el => el.innerText = data.refund || 0);
            document.querySelector('.total-invest-val').innerText = '₹' + new Intl.NumberFormat().format(data.total_invest || 0);
        });
}
// Har 3 second mein auto-sync
setInterval(refreshStats, 3000);
</script>
<body class="bg-[#2C1A11] text-[#FCFAF6] antialiased min-h-screen flex flex-col">

    <header class="h-16 border-b border-[#EFE9DE]/10 bg-[#352116] px-6 flex items-center justify-between sticky top-0 z-50 shadow-md no-print">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></div>
            <span class="text-xs uppercase tracking-widest font-bold font-mono text-white">SECURE NODE // USER DIAGNOSTICS MODULE</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print();" class="bg-amber-600 hover:bg-amber-500 text-white text-[10px] uppercase tracking-widest font-bold px-5 py-2 rounded-xl transition-all shadow-sm">
                <i class="fa-solid fa-file-pdf mr-1.5"></i> Download PDF
            </button>
            <a href="users.php" class="bg-white hover:bg-stone-200 text-[#2C1A11] text-[10px] uppercase tracking-widest font-bold px-5 py-2 rounded-xl transition-all">
                BACK TO LIST
            </a>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        
        <aside class="w-80 bg-[#352116] border-r border-[#EFE9DE]/10 flex flex-col justify-between p-4 space-y-4 shrink-0 select-none no-print">
            <div class="grid grid-cols-2 gap-2 overflow-y-auto pr-1">
                <a href="users.php" class="flex flex-col items-center justify-center p-3 text-center bg-[#2C1A11] border border-amber-500/40 rounded-xl transition group">
                    <div class="text-amber-400 mb-1.5"><i class="fa-solid fa-users text-sm"></i></div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-white">Manage Users</span>
                </a>
                <a href="ban.php" class="flex flex-col items-center justify-center p-3 text-center bg-[#2C1A11] border border-[#EFE9DE]/5 hover:border-rose-500/30 rounded-xl transition group">
                    <div class="text-[#8C6D58] group-hover:text-rose-400 mb-1.5 transition"><i class="fa-solid fa-user-slash text-sm"></i></div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-300 group-hover:text-white">Ban Users</span>
                </a>
                <a href="notifications.php" class="flex flex-col items-center justify-center p-3 text-center bg-[#2C1A11] border border-[#EFE9DE]/5 hover:border-cyan-500/30 rounded-xl transition group col-span-2">
                    <div class="text-[#8C6D58] group-hover:text-cyan-400 mb-1 transition"><i class="fa-solid fa-paper-plane text-xs"></i></div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-300 group-hover:text-white mt-1">Sent Notifications</span>
                </a>
                <a href="products.php" class="flex flex-col items-center justify-center p-3 text-center bg-[#2C1A11] border border-[#EFE9DE]/5 hover:border-emerald-500/30 rounded-xl transition group">
                    <div class="text-[#8C6D58] group-hover:text-emerald-400 mb-1.5 transition"><i class="fa-solid fa-box text-sm"></i></div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-300 group-hover:text-white">Manage Prods</span>
                </a>
                <a href="delete_products.php" class="flex flex-col items-center justify-center p-3 text-center bg-[#2C1A11] border border-[#EFE9DE]/5 hover:border-rose-500/30 rounded-xl transition group">
                    <div class="text-[#8C6D58] group-hover:text-rose-400 mb-1.5 transition"><i class="fa-solid fa-box-open text-sm"></i></div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-300 group-hover:text-white">Delete Prods</span>
                </a>
                <a href="stock.php" class="flex flex-col items-center justify-center p-3 text-center bg-[#2C1A11] border border-[#EFE9DE]/5 hover:border-amber-500/30 rounded-xl transition group">
                    <div class="text-[#8C6D58] group-hover:text-amber-400 mb-1.5 transition"><i class="fa-solid fa-cubes text-sm"></i></div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-300 group-hover:text-white">Stock Prods</span>
                </a>
                <a href="offers.php" class="flex flex-col items-center justify-center p-3 text-center bg-[#2C1A11] border border-[#EFE9DE]/5 hover:border-purple-500/30 rounded-xl transition group">
                    <div class="text-[#8C6D58] group-hover:text-purple-400 mb-1.5 transition"><i class="fa-solid fa-tags text-sm"></i></div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-300 group-hover:text-white">Add Offers</span>
                </a>
                <a href="delivery.php" class="flex flex-col items-center justify-center p-3 text-center bg-[#2C1A11] border border-[#EFE9DE]/5 hover:border-indigo-500/30 rounded-xl transition group col-span-2">
                    <div class="text-[#8C6D58] group-hover:text-indigo-400 mb-1 transition"><i class="fa-solid fa-truck text-xs"></i></div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-300 group-hover:text-white mt-1">Delivery Request</span>
                </a>
                <a href="completed.php" class="flex flex-col items-center justify-center p-3 text-center bg-[#2C1A11] border border-[#EFE9DE]/5 hover:border-emerald-500/30 rounded-xl transition group col-span-2">
                    <div class="text-[#8C6D58] group-hover:text-emerald-400 mb-1 transition"><i class="fa-solid fa-clipboard-check text-xs"></i></div>
                    <span class="text-[9px] font-bold uppercase tracking-wider text-gray-300 group-hover:text-white mt-1">Completed Deliveries</span>
                </a>
            </div>
        </aside>

        <main class="flex-grow p-6 overflow-y-auto space-y-6 print:p-0">
            
            <?php if (!$checkStatusCol || !$checkPriceCol): ?>
                <div class="no-print bg-amber-950/40 border border-amber-500/30 text-amber-300 text-xs p-4 rounded-xl font-mono">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> <strong>System Debug Notice:</strong> 
                    <?php 
                    $missing = [];
                    if(!$checkStatusCol) $missing[] = "'status'";
                    if(!$checkPriceCol) $missing[] = "'total_price'";
                    echo implode(' & ', $missing) . " column(s) not detected inside your `orders` table. System is running in Safe Compatibility Mode.";
                    ?>
                </div>
            <?php endif; ?>

            <div class="bg-[#352116] border border-[#EBE2D3]/10 p-6 rounded-[2rem] shadow-md print-card">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-[#2C1A11] border border-[#EFE9DE]/10 rounded-2xl flex items-center justify-center text-xl text-amber-400">
                            <i class="fa-solid fa-terminal"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-mono text-[#8C6D58] uppercase tracking-widest block">System Identity Signature</span>
                            <h2 class="text-xl font-bold text-white uppercase tracking-tight"><?php echo htmlspecialchars($user['name']); ?></h2>
                            <p class="text-xs text-gray-400 font-mono mt-0.5"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-left md:items-end font-mono">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs uppercase font-bold text-emerald-400">STATUS: ACTIVE LOCK</span>
                        </div>
                        <span class="text-[10px] text-[#8C6D58] mt-1.5">Last Sync: <?php echo date('Y-m-d H:i:s'); ?></span>
                        <span class="text-[10px] text-gray-500">Joined Node: <?php echo htmlspecialchars($user['created_at'] ?? date('Y-m-d')); ?></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-[#352116] border border-[#EBE2D3]/5 p-4 rounded-2xl text-center print-card shadow-inner">
                    <p class="text-[9px] uppercase tracking-wider text-[#8C6D58] mb-1 font-mono">Total Requests</p>
                    <p class="text-xl font-bold text-white font-mono"><?php echo $total_orders; ?></p>
                </div>
                <div class="bg-[#352116] border border-emerald-900/20 bg-emerald-950/10 p-4 rounded-2xl text-center print-card shadow-inner">
                    <p class="text-[9px] uppercase tracking-wider text-emerald-400 mb-1 font-mono">Success Metrics</p>
                    <p class="text-xl font-bold text-emerald-400 font-mono"><?php echo $orders_success; ?></p>
                </div>
                <div class="bg-[#352116] border border-amber-900/20 bg-amber-950/10 p-4 rounded-2xl text-center print-card shadow-inner">
                    <p class="text-[9px] uppercase tracking-wider text-amber-400 mb-1 font-mono">Pending Stack</p>
                    <p class="text-xl font-bold text-amber-400 font-mono"><?php echo $orders_pending; ?></p>
                </div>
                <div class="bg-[#352116] border border-rose-900/20 bg-rose-950/10 p-4 rounded-2xl text-center print-card shadow-inner">
                    <p class="text-[9px] uppercase tracking-wider text-rose-400 mb-1 font-mono">Rejected Aborts</p>
                    <p class="text-xl font-bold text-rose-400 font-mono"><?php echo $orders_rejected; ?></p>
                </div>
                <div class="bg-[#352116] border border-purple-900/20 bg-purple-950/10 p-4 rounded-2xl text-center print-card shadow-inner col-span-2 lg:col-span-1">
                    <p class="text-[9px] uppercase tracking-wider text-purple-400 mb-1 font-mono">Refund Reversals</p>
                    <p class="text-xl font-bold text-purple-400 font-mono"><?php echo $orders_refund; ?></p>
                </div>
            </div>

            <div class="bg-[#352116] border border-[#EBE2D3]/5 p-4 rounded-2xl flex justify-between items-center print-card">
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#8C6D58]">Total Value Extracted</span>
                <span class="text-lg font-bold text-emerald-400 font-mono">₹<?php echo number_format($total_investment); ?></span>
            </div>

            <div class="bg-[#352116] border border-[#EBE2D3]/10 rounded-[2rem] overflow-hidden print-card shadow-lg">
                <div class="p-4 border-b border-[#EFE9DE]/10 bg-[#3d271b] print:bg-gray-100 flex justify-between items-center">
                    <span class="text-[10px] uppercase tracking-widest font-bold text-white print:text-black">Complete Purchase Execution History Ledger</span>
                    <span class="text-[10px] font-mono text-gray-400"><?php echo count($order_history); ?> Rows Logged</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs font-light">
                        <thead class="bg-[#2C1A11] print:bg-gray-200 text-[9px] uppercase tracking-widest text-[#8C6D58] print:text-black border-b border-[#EFE9DE]/5">
                            <tr>
                                <th class="p-4 pl-6 font-mono">Order HASH</th>
                                <th class="p-4">Transaction Date</th>
                                <th class="p-4">Billing Price</th>
                                <?php if ($checkStatusCol): ?><th class="p-4 text-right pr-6">Execution State</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFE9DE]/5 text-gray-300 print:text-black">
                            <?php if(empty($order_history)): ?>
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-[#8C6D58] text-[10px] uppercase tracking-widest">
                                        Zero historical transaction hashes found for this cluster block.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($order_history as $order): ?>
                                <tr class="hover:bg-[#2C1A11]/30 transition">
                                    <td class="p-4 pl-6 font-mono text-[#8C6D58] print:text-gray-700">#<?php echo $order['id']; ?></td>
                                    <td class="p-4 text-xs font-mono text-gray-400 print:text-black"><?php echo htmlspecialchars($order['created_at'] ?? 'N/A'); ?></td>
                                    <td class="p-4 font-medium text-white print:text-black">₹<?php echo isset($order['total_price']) ? number_format($order['total_price']) : '0'; ?></td>
                                    
                                    <?php if ($checkStatusCol): ?>
                                    <td class="p-4 text-right pr-6">
                                        <?php 
                                        $st = strtolower($order['status'] ?? '');
                                        if($st === 'delivered'): ?>
                                            <span class="bg-emerald-950/60 text-emerald-400 print:bg-emerald-100 print:text-emerald-800 text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border border-emerald-900/30">Success</span>
                                        <?php elseif($st === 'cancelled'): ?>
                                            <span class="bg-rose-950/60 text-rose-400 print:bg-rose-100 print:text-rose-800 text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border border-rose-900/30">Rejected</span>
                                        <?php elseif($st === 'refunded'): ?>
                                            <span class="bg-purple-950/60 text-purple-400 print:bg-purple-100 print:text-purple-800 text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border border-purple-900/30">Refunded</span>
                                        <?php else: ?>
                                            <span class="bg-amber-950/60 text-amber-400 print:bg-amber-100 print:text-amber-800 text-[9px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border border-amber-900/30">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>