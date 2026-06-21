<?php
// Session start
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// 1. Centralized Database Connection (SSL included)
require_once __DIR__ . '/common/config.php';

// Order processing logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $payment_mode = $_POST['payment'];
    $utr_number = $_POST['utr_number'];

    try {
        // Transaction start karo taaki agar beech mein error aaye toh data kharab na ho
        $pdo->beginTransaction();

        // 1. Order insert karo
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, email, phone, shipping_address, payment_method, utr_number, total_amount, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Processing')");
        $stmt->execute([$user_id, $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address'], $payment_mode, $utr_number, $_POST['final_amount']]);
        
        $order_id = $pdo->lastInsertId();

        // 2. Cart items ko process karo
        $cart_query = $pdo->prepare("SELECT c.*, p.name, p.image_url, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
        $cart_query->execute([$user_id]);
        $cart_items = $cart_query->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($cart_items as $item) {
            $ins = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, product_name, product_image, price) VALUES (?, ?, ?, ?, ?, ?)");
            $ins->execute([$order_id, $item['product_id'], $item['quantity'], $item['name'], $item['image_url'], $item['price']]);
        }

        // 3. Cart khali karo
        $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);

        $pdo->commit(); // Sab sahi raha toh save kar do

        echo "<script>alert('Order Placed Successfully!'); window.location.href='orders.php';</script>";
        exit();

    } catch (Exception $e) {
        $pdo->rollBack(); // Error aane par purana data restore kar do
        die("Error processing your order: " . $e->getMessage());
    }
}
?>