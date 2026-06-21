<?php
session_start();
require_once 'common/config.php';

if (!isset($_SESSION['user_id'])) { header("Location: auth.php"); exit; }

// 1. Auto-Delete 30 days
$pdo->exec("DELETE FROM notifications WHERE created_at < NOW() - INTERVAL 30 DAY");

// 2. Mark ALL existing notifications as READ for this user
$stmt = $pdo->prepare("
    INSERT IGNORE INTO notification_read (user_id, notification_id)
    SELECT ?, id FROM notifications 
    WHERE id NOT IN (SELECT notification_id FROM notification_read WHERE user_id = ?)
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);

// 3. Delete Logic
if (isset($_GET['del_id'])) {
    $pdo->prepare("INSERT IGNORE INTO user_notification_status (user_id, notification_id) VALUES (?, ?)")
        ->execute([$_SESSION['user_id'], (int)$_GET['del_id']]);
    header("Location: notifications.php");
    exit;
}

// 4. Fetch Data
$notices = $pdo->prepare("
    SELECT n.* FROM notifications n 
    WHERE n.id NOT IN (SELECT notification_id FROM user_notification_status WHERE user_id = ?) 
    ORDER BY n.id DESC
");
$notices->execute([$_SESSION['user_id']]);
$notices = $notices->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Updates</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap'); body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#FCFAF6] min-h-screen text-[#2C1A11] pb-10">

    <header class="sticky top-0 bg-[#FCFAF6]/95 backdrop-blur-sm z-50 border-b border-[#EBE2D3]/60 px-6 py-4 flex items-center gap-4">
        <a href="index.php" class="w-10 h-10 flex items-center justify-center rounded-full bg-[#F3ECE0]/50 text-[#634832] active:bg-[#F3ECE0]">
            <i class="fa-solid fa-chevron-left text-sm"></i>
        </a>
        <h1 class="text-sm font-bold uppercase tracking-[0.2em]">Updates</h1>
    </header>

    <main class="px-5 pt-6">
        <?php if(empty($notices)): ?>
            <div class="flex flex-col items-center justify-center h-[60vh] text-[#8C6D58]">
                <i class="fa-solid fa-bell-slash text-4xl mb-4 opacity-30"></i>
                <p class="text-xs uppercase tracking-widest">No new updates yet</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach($notices as $notice): ?>
                <div class="bg-white border border-[#EBE2D3]/60 p-4 rounded-2xl shadow-sm relative">
                    <?php if(!empty($notice['image'])): ?>
                        <div class="mb-3">
                            <img src="uploads/notifications/<?php echo $notice['image']; ?>" class="rounded-xl w-full h-40 object-cover border border-[#EBE2D3]/20">
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex gap-3">
                        <div class="w-8 h-8 mt-0.5 rounded-full bg-[#F3ECE0] flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-bell text-[#634832] text-[10px]"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-[#2C1A11] leading-relaxed pr-6"><?php echo nl2br(htmlspecialchars($notice['message'])); ?></p>
                            <p class="text-[9px] text-[#8C6D58] mt-2 font-bold uppercase tracking-widest">
                                <?php echo date('M d, H:i', strtotime($notice['created_at'])); ?>
                            </p>
                        </div>
                        <a href="notifications.php?del_id=<?php echo $notice['id']; ?>" class="absolute top-4 right-4 text-[#8C6D58] hover:text-rose-500 transition-all">
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>