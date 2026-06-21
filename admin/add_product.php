<?php
// Session check
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// 1. Centralized Database Connection (SSL included)
// Note: Path adjust kar lena agar admin folder deeper hai
require_once __DIR__ . '/../common/config.php';

// Admin Auth check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth.php"); 
    exit;
}

$upload_dir = "../uploads/products/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Categories fetch kar sakte ho, ya hardcoded rakho
$categories = ['Womens Wear', 'Mens Wear', 'Kids Wear', 'T-Shirts', 'Shirts', 'Jeans & Denim', 'Winter Clothes', 'Sarees & Kurtis', 'Trousers & Pants'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $cat = $_POST['category'];

    $img = time() . "_" . $_FILES['image']['name'];
    
    // 2. PDO $pdo ab config.php se mil raha hai
    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $img)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, description, category, image_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $price, $desc, $cat, $img]);
            echo "<script>alert('Product added successfully!'); window.location='products.php';</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Database Error: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('Failed to upload image. Check folder permissions.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#1a1512] min-h-screen text-[#EBE2D3] p-10">
    <div class="max-w-xl mx-auto bg-[#261d19] p-8 rounded-[2rem] border border-white/5">
        <h2 class="text-white font-bold mb-6 uppercase tracking-widest">Add New Product</h2>
        <?php if(isset($msg)) echo "<p class='text-cyan-500 text-[10px] mb-4'>$msg</p>"; ?>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="name" placeholder="Product Name" required class="w-full p-4 bg-[#1a1512] rounded-xl border border-white/5">
            <input type="number" name="price" placeholder="Price" required class="w-full p-4 bg-[#1a1512] rounded-xl border border-white/5">
            <textarea name="description" placeholder="Description" class="w-full p-4 bg-[#1a1512] rounded-xl border border-white/5"></textarea>
            
            <select name="category" class="w-full p-4 bg-[#1a1512] rounded-xl border border-white/5">
                <?php foreach($categories as $c): ?> <option value="<?= $c ?>"><?= $c ?></option> <?php endforeach; ?>
            </select>

            <input type="file" name="image" required class="w-full p-4 bg-[#1a1512] rounded-xl border border-white/5 text-[10px]">
            
            <button type="submit" class="w-full bg-cyan-600 p-4 rounded-xl font-bold uppercase text-[10px] tracking-widest">Publish Product</button>
            <a href="products.php" class="block text-center mt-4 text-[#8C6D58] text-[9px] uppercase">Cancel</a>
        </form>
    </div>
</body>
</html>