<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions — ADDA COLLECTION</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-luxury { font-family: 'Cormorant Garamond', serif; }
    </style>
</head>
<body class="bg-[#FCFAF6] text-[#2C1A11] antialiased min-h-screen flex flex-col justify-between">

    <header class="h-20 flex items-center justify-between px-6 border-b border-[#EFE9DE]/60 bg-[#FCFAF6] sticky top-0 z-40 backdrop-blur-md bg-opacity-90">
        <a href="auth.php" class="text-xs uppercase tracking-widest font-semibold text-[#8C6D58] hover:text-[#2C1A11] transition-all">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back
        </a>
        <a href="index.php" class="font-luxury text-xl font-medium tracking-[0.3em] uppercase text-[#2C1A11]">
            ADDA <span class="font-light text-[#8C6D58]">COLLECTION</span>
        </a>
        <div class="w-10"></div> </header>

    <main class="max-w-3xl mx-auto px-6 py-12 pb-32">
        <h1 class="font-luxury text-4xl font-medium italic mb-2 text-[#2C1A11]">Terms & Conditions</h1>
        <p class="text-[10px] uppercase tracking-widest text-[#8C6D58] mb-8">Last Updated: June 2026</p>

        <div class="space-y-8 text-xs leading-relaxed text-gray-600 font-light">
            
            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-[#2C1A11] mb-3">1. Agreement to Terms</h2>
                <p>Welcome to ADDA COLLECTION ("we," "our," "us"). By accessing or using our website, digital nodes, or premium checkout networks, you agree to bound yourself strictly by these Terms & Conditions. If you do not accept these operational parameters, you must cease authorization immediately.</p>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-[#2C1A11] mb-3">2. Digital Account Protocols</h2>
                <p>When you create an account in our system via secure signup authentication, you are fully responsible for maintaining the privacy of your session tokens, password structures, and active login state. Any user activity registered under your token credentials will be recognized as explicitly verified by you. We reserve absolute rights to terminate membership parameters or flush database entries at our independent discretion without warning.</p>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-[#2C1A11] mb-3">3. Curated Inventory & Orders</h2>
                <p>All minimalist apparel variants, pricing metrics, and drops displayed on our interface are subject to sudden inventory adjustments. While we attempt extreme accuracy in showcasing item imagery and colors, variances may appear due to your display hardware configurations. We reserve the absolute right to limit sales metrics, cancel order requests, or reject premium distribution pathways to any region or user cluster.</p>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-[#2C1A11] mb-3">4. Intellectual Property Rights</h2>
                <p>The visual framework, source architecture, monolithic templates, imagery assets, font integrations, and textual layouts are the exclusive property of ADDA COLLECTION. No part of this node may be replicated, mirrored, cached, or extracted via web scrapers without signed administrative confirmation.</p>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-[#2C1A11] mb-3">5. Delivery & Global Distribution</h2>
                <p>Express distribution timelines are estimations handled by secondary logistical partners. ADDA COLLECTION does not secure operational liabilities regarding delays triggered by external shipping disruptions, custom clearances, or regional server down-states.</p>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-[#2C1A11] mb-3">6. Limitation of Liability</h2>
                <p>In no transaction or database breakdown shall ADDA COLLECTION be held liable for indirect, incidental, punitive, or system-wide computational damages arising from your usage or absolute inability to fetch items via our platform.</p>
            </section>

        </div>
    </main>

    <nav class="fixed bottom-4 left-4 right-4 z-50 bg-[#FCFAF6]/90 backdrop-blur-xl border border-[#EFE9DE]/80 flex items-center justify-around h-20 px-3 rounded-2xl shadow-[0_10px_30px_rgba(44,26,17,0.08)]">
        <a href="index.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-house text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Home</span></a>
        <a href="categories.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-border-all text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Categories</span></a>
        <a href="search.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-magnifying-glass text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Search</span></a>
        <a href="cart.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-bag-shopping text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Bag</span></a>
        <a href="profile.php" class="flex flex-col items-center gap-1 w-14 text-[#8C6D58]"><i class="fa-solid fa-user text-xs"></i><span class="text-[9px] font-medium uppercase tracking-wider">Profile</span></a>
    </nav>

</body>
</html>