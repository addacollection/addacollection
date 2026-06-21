<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function check_user_access() {
    // Strict Check: Agar user_id set nahi hai YA empty hai
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        
        // Target page URL save karo redirection ke liye
        $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Authentication Required | ADDA COLLECTION</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Inter:wght@300;400;600&display=swap');
                body { background-color: #FDFBF7; color: #3E2723; font-family: 'Inter', sans-serif; }
                .brand-font { font-family: 'Playfair Display', serif; }
            </style>
        </head>
        <body class="flex items-center justify-center min-h-screen px-6">
            
            <div class="w-full max-w-md bg-white p-8 rounded-[2.5rem] shadow-xl text-center border border-[#3E2723]/5">
                <div class="w-20 h-20 bg-[#3E2723]/5 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-lock text-[#3E2723] text-2xl"></i>
                </div>
                
                <h2 class="brand-font text-2xl font-bold italic mb-2">Adda Collection</h2>
                <p class="text-xs opacity-60 uppercase tracking-widest mb-4">Hold On!</p>
                
                <p class="text-sm text-gray-500 mb-8 px-4 leading-relaxed">
                    You haven't logged in yet. Please sign up or login to your account to manage your custom premium bag, profile details, and orders.
                </p>
                
                <a href="auth.php" class="block w-full bg-[#3E2723] text-white text-xs uppercase tracking-widest font-bold py-4 rounded-full transition-all active:scale-95 shadow-md mb-3">
                    Continue to Login
                </a>
                
                <a href="index.php" class="block text-[10px] uppercase tracking-widest font-bold opacity-40 hover:opacity-100 transition">
                    Back to Home
                </a>
            </div>

        </body>
        </html>
        <?php
        exit(); // strict block: iske niche ka koi code browser read nahi karega
    }
}
?>