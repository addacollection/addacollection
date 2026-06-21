<?php
// Aiven Database Configuration
$host = 'mysql-7efca4b-addacollection.i.aivencloud.com';
$dbname = 'defaultdb';
$user = 'avnadmin';
$pass = 'AVNS_h0ihm4NmXYmZcJ8ISQM';
$port = 13574;

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Agar nayi image upload ki gayi hai
    if (!empty($_FILES['new_image']['name'])) {
        $img = time() . "_" . $_FILES['new_image']['name'];
        if (move_uploaded_file($_FILES['new_image']['tmp_name'], "../uploads/products/" . $img)) {
            $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, description=?, image_url=? WHERE id=?");
            $stmt->execute([$name, $price, $description, $img, $id]);
        }
    } else {
        // Agar image update nahi karni, to sirf baaki details update karo
        $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, description=? WHERE id=?");
        $stmt->execute([$name, $price, $description, $id]);
    }

    header("Location: edit_product.php?id=$id");
    exit();
}
?>