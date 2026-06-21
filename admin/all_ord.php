<?php
// Session start
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// 1. Centralized Database Connection (SSL included)
// Agar ye file admin folder mein hai toh path sahi adjust karna: ../common/config.php
require_once __DIR__ . '/../common/config.php';

// Auth check (Ensure only admin can update)
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}

// 2. STATUS UPDATE LOGIC
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    try {
        // $pdo ab central config se aa raha hai
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        
        header("Location: all_ord.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating status: " . $e->getMessage());
    }
}

$statuses = ['Processing', 'Shipped', 'Completed', 'Rejected'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Outfit', sans-serif; background: #0f0f0f; color: #fff; }</style>
</head>
<body class="p-6">

<div class="mb-6">
    <a href="index.php" class="flex items-center text-gray-400 hover:text-white transition">
        <i class="fa-solid fa-arrow-left mr-2"></i> Back to Dashboard
    </a>
</div>

<h1 class="text-2xl font-bold mb-6">Order Management Board</h1>

<div class="grid grid-cols-4 gap-4">
    <?php foreach ($statuses as $status): ?>
        <div class="bg-[#1a1a1a] p-4 rounded-2xl border border-gray-800 min-h-[500px]">
            <h2 class="font-bold text-sm uppercase text-gray-500 mb-4"><?= $status ?></h2>
            
            <div class="space-y-3">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_status = ? ORDER BY created_at DESC");
                $stmt->execute([$status]);
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($orders as $order):
                ?>
                    <div class="bg-[#262626] p-4 rounded-xl border border-gray-700">
                        <a href="ord_details.php?id=<?= $order['id'] ?>" class="block">
                            <div class="flex justify-between text-xs mb-2 text-gray-400">
                                <span>#<?= $order['id'] ?></span>
                                <span>₹<?= number_format($order['total_amount']) ?></span>
                            </div>
                            <p class="font-bold text-sm"><?= htmlspecialchars($order['name']) ?></p>
                        </a>
                        
                        <form method="POST" class="mt-3">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="new_status" onchange="this.form.submit()" class="w-full bg-[#333] text-[10px] p-2 rounded-lg border border-gray-600 uppercase font-bold cursor-pointer hover:bg-gray-700">
                                <option disabled selected>Move to...</option>
                                <?php foreach($statuses as $s): if($s != $status): ?>
                                    <option value="<?= $s ?>"><?= $s ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>