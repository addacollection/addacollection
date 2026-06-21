<?php
// Session start
if (session_status() == PHP_SESSION_NONE) session_start();

// 1. Centralized Database Connection (SSL included from common/config.php)
require_once __DIR__ . '/common/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in.");
}

// 2. Fetch Cart Subtotal
try {
    $stmt = $pdo->prepare("SELECT SUM(products.price * cart.quantity) as subtotal 
                           FROM cart 
                           JOIN products ON cart.product_id = products.id 
                           WHERE cart.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $subtotal = $cart_data['subtotal'] ?? 0;

    $delivery_charge = 50; // Default COD
    $total_payable = $subtotal + $delivery_charge;
    
} catch (PDOException $e) {
    $subtotal = 0;
    $total_payable = 0;
    error_log("Cart calculation error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | ADDA COLLECTION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-[#FCFAF6] text-[#2C1A11] min-h-screen">

    <div class="max-w-sm mx-auto min-h-screen p-6">
        <header class="mb-8">
            <a href="cart.php" class="text-sm font-bold uppercase"><i class="fa-solid fa-chevron-left mr-2"></i> Back</a>
            <h1 class="text-3xl font-bold mt-4">Checkout</h1>
        </header>

        <form action="process_order.php" method="POST" id="checkoutForm" class="space-y-4">
            <input type="text" name="name" placeholder="Full Name" class="w-full p-4 bg-white border border-[#EFE9DE] rounded-2xl" required>
            <input type="email" name="email" placeholder="Email Address" class="w-full p-4 bg-white border border-[#EFE9DE] rounded-2xl" required>
            <input type="tel" name="phone" placeholder="Phone Number" class="w-full p-4 bg-white border border-[#EFE9DE] rounded-2xl" required>
            <textarea name="address" placeholder="Delivery Address" class="w-full p-4 bg-white border border-[#EFE9DE] rounded-2xl h-24" required></textarea>

            <div class="bg-white p-5 rounded-2xl border border-[#EFE9DE]">
                <h3 class="font-bold mb-3 uppercase text-xs text-gray-500">Payment Method</h3>
                <label class="flex items-center gap-3 p-3 bg-[#FCFAF6] rounded-xl mb-2 cursor-pointer">
                    <input type="radio" name="payment" value="cod" checked onchange="updateTotal(50, false)"> 
                    <span class="font-medium">Cash on Delivery (₹50)</span>
                </label>
                <label class="flex items-center gap-3 p-3 bg-[#FCFAF6] rounded-xl cursor-pointer">
                    <input type="radio" name="payment" value="online" onchange="updateTotal(150, true)"> 
                    <span class="font-medium">Online (QR Pay) (₹150)</span>
                </label>
            </div>

            <div id="qr-section" class="hidden space-y-4 text-center p-6 bg-white rounded-2xl border border-[#EFE9DE]">
                <img src="assets/qr_order.jpg" class="w-48 mx-auto rounded-xl">
                <p class="text-xs opacity-60">Scan QR to pay</p>
                <input type="text" name="utr_number" placeholder="Enter 12-digit UTR/Ref Number" class="w-full p-4 bg-[#FCFAF6] border border-[#EFE9DE] rounded-xl text-sm">
            </div>

            <div class="bg-white p-6 rounded-2xl border border-[#EFE9DE] space-y-3">
                <div class="flex justify-between text-sm"><span>Subtotal</span> <span>₹<?= number_format($subtotal, 2) ?></span></div>
                <div class="flex justify-between text-sm"><span>Delivery</span> <span id="del-display">₹50.00</span></div>
                <div class="flex justify-between font-bold text-lg border-t pt-3"><span>Total</span> <span id="total-display">₹<?= number_format($total_payable, 2) ?></span></div>
            </div>

            <input type="hidden" name="final_amount" id="final_amount" value="<?= $total_payable ?>">
            
            <button type="button" onclick="confirmOrder()" class="w-full bg-[#2C1A11] text-white py-5 rounded-2xl font-bold uppercase tracking-widest hover:bg-black transition-all">
                Proceed to Order
            </button>
        </form>
    </div>

    <script>
        function updateTotal(fee, showQR) {
            let subtotal = <?= $subtotal ?>;
            document.getElementById('qr-section').classList.toggle('hidden', !showQR);
            document.getElementById('del-display').innerText = '₹' + fee.toFixed(2);
            document.getElementById('total-display').innerText = '₹' + (subtotal + fee).toFixed(2);
            document.getElementById('final_amount').value = (subtotal + fee);
        }

        function confirmOrder() {
            if (confirm("Are you sure you want to place this order?")) {
                document.getElementById('checkoutForm').submit();
            }
        }
    </script>
</body>
</html>