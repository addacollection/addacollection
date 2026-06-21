<?php
session_start();
require_once '../common/config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth.php"); exit;
}

$upload_dir = "../uploads/notifications/";
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// 1. Handle Broadcast Sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notice'])) {
    $msg = trim($_POST['message']);
    $user_id = ($_POST['target_user'] === 'all') ? null : (int)$_POST['target_user'];
    $imagePath = null;

    if (!empty($_FILES['image']['name'])) {
        $imagePath = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $imagePath);
    }

    $stmt = $pdo->prepare("INSERT INTO notifications (message, image, user_id) VALUES (?, ?, ?)");
    $stmt->execute([$msg, $imagePath, $user_id]);
}

// 2. Handle Deletions
if (isset($_GET['del_id'])) {
    $pdo->prepare("DELETE FROM notifications WHERE id = ?")->execute([$_GET['del_id']]);
}
if (isset($_GET['delete_all'])) {
    $pdo->exec("DELETE FROM notifications");
}

// 3. Fetch Data
$users = $pdo->query("SELECT id, name FROM users ORDER BY name ASC")->fetchAll();
$search = $_GET['search'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE message LIKE ? ORDER BY id DESC");
$stmt->execute(["%$search%"]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Notification Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#1a1512] min-h-screen text-[#EBE2D3]">

    <div class="max-w-xl mx-auto py-10 px-6">
        <div class="flex items-center justify-between mb-10">
            <a href="index.php" class="text-[10px] uppercase tracking-widest text-[#8C6D58] hover:text-white flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
            <div class="text-[10px] uppercase tracking-[0.2em] font-bold text-cyan-500">Admin Mode</div>
        </div>

        <div class="bg-[#261d19] border border-[#EBE2D3]/5 p-8 rounded-[2rem] shadow-2xl mb-10">
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <textarea name="message" required class="w-full bg-[#1a1512] border border-[#EBE2D3]/5 rounded-2xl p-4 text-white text-[12px]" rows="3" placeholder="Enter message..."></textarea>
                
                <select name="target_user" class="w-full bg-[#1a1512] border border-[#EBE2D3]/5 rounded-2xl p-4 text-white text-[12px]">
                    <option value="all">Broadcast to: ALL USERS</option>
                    <?php foreach($users as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?> (ID: <?= $user['id'] ?>)</option>
                    <?php endforeach; ?>
                </select>

                <input type="file" name="image" accept="image/*" class="w-full text-[10px] text-[#8C6D58] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:bg-[#3d322c] file:text-white">
                
                <button type="submit" name="send_notice" class="w-full bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-[10px] uppercase py-4 rounded-2xl">Deploy Broadcast</button>
            </form>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-white font-bold uppercase text-xs">History</h2>
            <a href="?delete_all=1" class="text-[9px] text-rose-500 hover:text-rose-300">CLEAR ALL</a>
        </div>
        
        <form method="GET" class="mb-6">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search messages..." class="w-full bg-[#261d19] border border-[#EBE2D3]/5 rounded-xl p-3 text-[11px] text-white">
        </form>

        <div class="space-y-3">
            <?php foreach($notifications as $row): ?>
            <div class="bg-[#261d19] p-4 rounded-2xl flex justify-between items-center border border-[#EBE2D3]/5">
                <div class="truncate mr-4">
                    <p class="text-[11px] text-white truncate"><?= htmlspecialchars($row['message']) ?></p>
                    <p class="text-[9px] text-[#8C6D58]">To: <?= $row['user_id'] ? 'User #'.$row['user_id'] : 'Everyone' ?></p>
                </div>
                <a href="?del_id=<?= $row['id'] ?>" class="text-[#8C6D58] hover:text-rose-500"><i class="fa-solid fa-trash"></i></a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>