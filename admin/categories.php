<?php
/**
 * Adda Collection - Admin Category Management
 * Simple core segments management console.
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
    die("Database connection failed: " . $e->getMessage());
}

$message = '';
$error = '';

// ACTION: SEED SIMPLE DEPARMENTS
if (isset($_POST['action']) && $_POST['action'] === 'inject_defaults') {
    $simple_categories = [
        ['name' => 'Women Wear', 'description' => 'Elegant dresses, luxury tops, and timeless ethnic ensembles.'],
        ['name' => 'Men Wear', 'description' => 'Premium tailored shirts, structured blazers, and luxury basics.'],
        ['name' => 'Kids & Child', 'description' => 'Comfortable cotton casuals and vibrant festive edits for little ones.'],
        ['name' => 'Footwear & Accessories', 'description' => 'Handcrafted leather bags, statement belts, and designer footwear.']
    ];

    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, description) VALUES (?, ?)");
        $count = 0;
        foreach ($simple_categories as $cat) {
            $stmt->execute([$cat['name'], $cat['description']]);
            if ($stmt->rowCount() > 0) $count++;
        }
        $message = "Successfully created {$count} simple target segments!";
    } catch (PDOException $e) { $error = $e->getMessage(); }
}

// ACTION: ADD CUSTOM CATEGORY
if (isset($_POST['action']) && $_POST['action'] === 'add_category') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([$name, $description]);
            $message = "Category '{$name}' created!";
        } catch (PDOException $e) { $error = "Name already exists."; }
    }
}

// ACTION: DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        $message = "Category segment removed.";
    } catch (PDOException $e) { $error = "Active products are currently mapped to this classification."; }
}

$categories = $pdo->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Control Console — Departments</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; } .font-luxury { font-family: 'Cormorant Garamond', serif; }</style>
</head>
<body class="bg-[#FCFAF6] text-[#2C1A11] p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-[#EFE9DE] pb-6 mb-6">
            <div>
                <span class="text-[10px] font-bold tracking-widest uppercase text-[#8C6D58]">Dashboard Control</span>
                <h1 class="font-luxury text-3xl text-[#2C1A11]">Store Departments</h1>
            </div>
            <form action="categories.php" method="POST">
                <input type="hidden" name="action" value="inject_defaults">
                <button type="submit" class="bg-[#8C6D58] hover:bg-[#634832] text-white text-xs uppercase font-medium px-4 py-2.5 rounded-xl transition-all shadow-sm">
                    <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Setup Simple Core Departments
                </button>
            </form>
        </div>

        <?php if($message): ?><div class="p-3 bg-emerald-50 text-emerald-800 text-xs rounded-xl mb-4 border border-emerald-200"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="p-3 bg-rose-50 text-rose-800 text-xs rounded-xl mb-4 border border-rose-200"><?php echo $error; ?></div><?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-[#F3ECE0]/30 border border-[#EBE2D3]/60 rounded-2xl p-5">
                <h2 class="font-luxury text-lg font-medium mb-3">Add Custom Line</h2>
                <form action="categories.php" method="POST" class="space-y-3">
                    <input type="hidden" name="action" value="add_category">
                    <div>
                        <label class="block text-[10px] uppercase font-semibold text-[#634832] mb-1">Name</label>
                        <input type="text" name="name" required class="w-full bg-white text-xs border border-[#EBE2D3] rounded-xl px-3 py-2.5 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-semibold text-[#634832] mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full bg-white text-xs border border-[#EBE2D3] rounded-xl px-3 py-2.5 focus:outline-none resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-[#2C1A11] hover:bg-[#634832] text-white font-medium text-[10px] uppercase py-3 rounded-xl shadow-md transition-all">Save Segment</button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white border border-[#EBE2D3]/60 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#F3ECE0]/10 border-b border-[#EFE9DE] text-[10px] uppercase text-[#634832] font-semibold">
                            <th class="p-3 pl-4">ID</th>
                            <th class="p-3">Department Name</th>
                            <th class="p-3 text-center">Live Stock</th>
                            <th class="p-3 text-right pr-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EFE9DE]/40">
                        <?php foreach($categories as $cat): ?>
                        <tr class="hover:bg-[#F3ECE0]/5">
                            <td class="p-3 pl-4 text-[#8C6D58] font-mono">#<?php echo $cat['id']; ?></td>
                            <td class="p-3 font-luxury text-sm font-medium"><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td class="p-3 text-center"><span class="bg-[#F3ECE0]/60 text-[#2C1A11] px-2 py-0.5 rounded-full text-[10px]"><?php echo $cat['product_count']; ?> clothes</span></td>
                            <td class="p-3 text-right pr-4"><a href="categories.php?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Remove Segment?');" class="w-7 h-7 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg inline-flex items-center justify-center border border-rose-100"><i class="fa-regular fa-trash-can text-xs"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>