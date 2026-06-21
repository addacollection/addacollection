<?php
// Database connection
$pdo = new PDO("mysql:host=127.0.0.1;dbname=adda_collection;charset=utf8mb4", "root", "");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Agar nayi image upload ki gayi hai
    if (!empty($_FILES['new_image']['name'])) {
        $img = time() . "_" . $_FILES['new_image']['name'];
        // Path check karo (Agar admin folder mein ho to ../ use karo)
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