<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy — ADDA COLLECTION</title>
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
        <div class="w-10"></div>
    </header>

    <main class="max-w-3xl mx-auto px-6 py-12 pb-32">
        <h1 class="font-luxury text-4xl font-medium italic mb-2 text-[#2C1A11]">Privacy Policy</h1>
        <p class="text-[10px] uppercase tracking-widest text-[#8C6D58] mb-8">Secure Data Architecture Node</p>

        <div class="space-y-8 text-xs leading-relaxed text-gray-600 font-light">
            
            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-[#2C1A11] mb-3">1. Data We Harvest</h2>
                <p>When you interact with the Adda Studio platform, we collect metrics necessary to power premium operations. This includes personal markers like Full Name, validated Email Address, delivery address inputs, and session configuration scripts. Password records are immediately injected into BCRYPT one-way security cryptographic arrays to protect your raw traces from cleartext leaks.</p>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-[#2C1A11] mb-3">2. How Data is Utilized</h2>
                <p>Your harvested telemetry is used exclusively to: maintain your active curated shopping bag system, execute order dispatches, run security profiling, clear database lookup configurations during authentication, and deliver tailored notification drops regarding new inventory drops.</p>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-[#2C1A11] mb-3">3. Session Storage & Cookies</h2>
                <p>Our monolithic PHP framework deploys system session tokens to remember who you are. These secure cookies stay local inside your web client to maintain shopping cart variables intact. Disabling browser cookie engines will structurally disrupt authentication gates and user profile panel access.</p>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-[#2C1A11] mb-3">4. Information Isolation Policy</h2>
                <p>We operate under strict data isolation. ADDA COLLECTION does not trade, pitch, lease, or mirror user profile metrics with external marketing conglomerates. Third-party infrastructure pipelines only view your information to finalize credit check clearings or fulfill order cargo handling.</p>
            </section>

            <section>
                <h2 class="text-sm font-semibold uppercase tracking-wider text-[#2C1A11] mb-3">5. User Cryptographic Freedom</h2>
                <p>Under local security parameters, you retain the complete right to request a full database purge. If you decide to flush your user profile and cancel system footprint variables, please connect with our administrative concierge node directly.</p>
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