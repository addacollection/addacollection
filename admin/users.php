<?php
/**
 * Adda Collection — User Cluster Management Console (With Live Search)
 * Location: /admin/users.php
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Strict Security Gate
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Aiven Database Configuration
$host = 'mysql-7efca4b-addacollection.i.aivencloud.com';
$dbname = 'defaultdb';
$user = 'avnadmin';
$pass = 'AVNS_h0ihm4NmXYmZcJ8ISQM';
$port = 13574;

$search_query = '';
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Search Parameter Sanitization & Execution
    if (isset($_GET['search']) && trim($_GET['search']) !== '') {
        $search_query = trim($_GET['search']);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'user' AND (name LIKE ? OR email LIKE ?) ORDER BY id DESC");
        $stmt->execute(["%$search_query%", "%$search_query%"]);
        $users = $stmt->fetchAll();
    } else {
        // Default View: All active users
        $users = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY id DESC")->fetchAll();
    }

} catch (PDOException $e) {
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SECURE NODE — SEARCH USERS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #2C1A11; }
        ::-webkit-scrollbar-thumb { background: rgba(140, 109, 88, 0.3); border-radius: 4px; }
    </style>
</head>
<body class="bg-[#2C1A11] text-[#FCFAF6] antialiased min-h-screen flex flex-col">

    <header class="h-16 border-b border-[#EFE9DE]/10 bg-[#352116] px-6 flex items-center justify-between sticky top-0 z-50 shadow-md">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
            <span class="text-xs uppercase tracking-widest font-bold font-mono text-white">SECURE NODE // USER PROFILES CORE</span>
        </div>
        <a href="index.php" class="bg-white hover:bg-stone-200 text-[#2C1A11] text-[10px] uppercase tracking-widest font-bold px-5 py-2 rounded transition-all">
            BACK TO DASHBOARD
        </a>
    </header>

    <div class="flex flex-1 overflow-hidden">
        
        <aside class="w-80 bg-[#352116] border-r border-[#EFE9DE]/10 flex flex-col justify-between p-4 space-y-4 shrink-0 select-none">
            
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

            <div class="bg-[#2C1A11] p-4 rounded-2xl border border-[#EFE9DE]/5 text-center text-[10px] text-gray-400 font-mono">
                Active Cluster Connection Established
            </div>
        </aside>

        <main class="flex-grow p-6 overflow-y-auto space-y-6">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-white uppercase">User Registry Database</h1>
                    <p class="text-xs text-[#8C6D58]">
                        <?php echo $search_query ? "Search results for '".htmlspecialchars($search_query)."': " : "Active user profile nodes tracked: "; ?> 
                        <span class="text-white font-bold font-mono"><?php echo count($users); ?></span>
                    </p>
                </div>
                
                <form action="users.php" method="GET" class="w-full sm:w-80 flex items-center bg-[#352116] border border-[#EFE9DE]/10 rounded-xl p-1">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search by name or email..." class="w-full bg-transparent text-xs text-white placeholder-gray-500 px-3 py-2 outline-none">
                    <?php if($search_query): ?>
                        <a href="users.php" class="text-gray-400 hover:text-white px-2 text-xs"><i class="fa-solid fa-xmark"></i></a>
                    <?php endif; ?>
                    <button type="submit" class="bg-[#2C1A11] hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>

            <div class="bg-[#352116] border border-[#EBE2D3]/10 rounded-[2rem] overflow-hidden shadow-lg">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs font-light">
                        <thead class="bg-[#2C1A11] text-[9px] uppercase tracking-widest text-[#8C6D58] border-b border-[#EFE9DE]/5">
                            <tr>
                                <th class="p-4 pl-6 w-24">ID</th>
                                <th class="p-4">Customer Details</th>
                                <th class="p-4 text-center w-48">Operational Logs</th>
                                <th class="p-4 text-right pr-6 w-48">System Node Privilege</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFE9DE]/5 text-gray-300">
                            <?php if(empty($users)): ?>
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-[#8C6D58] text-[10px] uppercase tracking-widest">
                                        No active users match the explicit search parameters.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                <tr class="hover:bg-[#2C1A11]/30 transition">
                                    <td class="p-4 pl-6 font-mono text-[#8C6D58]">#<?php echo $user['id']; ?></td>
                                    <td class="p-4">
                                        <div class="font-medium text-white text-sm"><?php echo htmlspecialchars($user['name']); ?></div>
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5"><?php echo htmlspecialchars($user['email'] ?? 'no-email-logged'); ?></div>
                                    </td>
                                    
                                    <td class="p-4 text-center">
                                        <a href="user_details.php?id=<?php echo $user['id']; ?>" class="inline-block bg-[#2C1A11] hover:bg-white hover:text-[#2C1A11] border border-[#EFE9DE]/10 text-gray-300 text-[10px] font-bold uppercase tracking-widest px-4 py-2 rounded-xl transition duration-200 shadow-sm">
                                            <i class="fa-solid fa-terminal text-[9px] mr-1.5 opacity-80"></i> See Details
                                        </a>
                                    </td>

                                    <td class="p-4 text-right pr-6">
                                        <span class="bg-emerald-950/50 text-emerald-400 text-[9px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-md border border-emerald-900/30">
                                            Active User
                                        </span>
                                    </td>
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