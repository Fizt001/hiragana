<?php
/**
 * HIRAGANA FOOTWEAR — Men's Casual & Distro Footwear
 * Location: /Users/admin/Herd/hiraganafootwear (http://hiraganafootwear.test)
 */

// Load Store & Contact Configuration
$settingsPath = __DIR__ . '/data/settings.json';
$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];

$brand = [
    'name' => $settings['brand_name'] ?? 'HIRAGANA FOOTWEAR',
    'logo_url' => $settings['logo_url'] ?? '',
    'japanese_tagline' => $settings['japanese_tagline'] ?? 'HIRAGANA • MEN\'S CASUAL & DISTRO FOOTWEAR',
    'headline' => $settings['headline'] ?? 'TANGGUH DI JALAN, NYAMAN DI KAKI.',
    'sub_headline' => $settings['sub_headline'] ?? 'Sandal kasual & distro pria handmade pengrajin lokal Bekasi. Dibuat dengan bantalan Phylon anti-kempes, kulit sintetis tebal yang lentur, dan sol karet anti-slip untuk gaya santai cowok yang maskulin.',
    'location' => $settings['location'] ?? 'Kabupaten Bekasi, Jawa Barat, Indonesia',
    'whatsapp' => $settings['whatsapp'] ?? '6281234567890',
    'whatsapp_display' => $settings['whatsapp_display'] ?? '+62 812-3456-7890',
    'cs_hours' => $settings['cs_hours'] ?? 'Senin - Sabtu (08.00 - 21.00 WIB)',
    'instagram' => $settings['instagram'] ?? 'hiragana.footwear',
    'tiktok' => $settings['tiktok'] ?? 'hiragana.0',
    'tiktok_url' => !empty($settings['tiktok']) ? (str_starts_with($settings['tiktok'], 'http') ? $settings['tiktok'] : 'https://www.tiktok.com/@' . ltrim($settings['tiktok'], '@')) : 'https://www.tiktok.com/@hiragana.0',
    'shopee_url' => $settings['shopee_url'] ?? 'https://shopee.co.id/search?keyword=hiragana%20footwear',
    'tokopedia_url' => $settings['tokopedia_url'] ?? 'https://www.tokopedia.com/search?st=product&q=hiragana%20footwear',
    'rating' => $settings['rating'] ?? '4.9',
    'total_sold' => $settings['total_sold'] ?? '10.000+',
    'banner_announcement' => $settings['banner_announcement'] ?? 'PENGIRIMAN BISA COD SE-INDONESIA • GARANSI TUKAR SIZE JIKA KEKECILAN • 100% ORIGINAL DISTRO',
];

// Load Products
$productsPath = __DIR__ . '/data/products.json';
$catalog = file_exists($productsPath) ? json_decode(file_get_contents($productsPath), true) : [];
$products = $catalog['products'] ?? [];

// Load Feature Flags & Keunggulan Toko
$features = $settings['features'] ?? [
    'checkout_wa' => true,
    'varian_produk' => true,
    'qris_payment' => true,
    'banner_promo' => true,
    'seo_google' => true,
    'multi_outlet' => false,
];

$keunggulan = $settings['keunggulan'] ?? [
    'eceran_grosir' => true,
    'garansi_mutu' => true,
    'siap_kirim_indonesia' => true,
    'bisa_cod' => true,
    'garansi_tukar_size' => true,
    'faktur_pajak' => false,
];

/**
 * Normalizes Google Drive, Dropbox, and external image URLs to direct high-speed image streams.
 */
function normalizeImageUrl(?string $url): string {
    if (empty($url)) {
        return '';
    }
    $url = trim($url);

    // Google Drive share / view link conversions:
    if (preg_match('#drive\.google\.com/(?:file/d/|open\?id=|uc\?(?:export=view&)?id=)([a-zA-Z0-9_-]+)#', $url, $matches)) {
        return "https://lh3.googleusercontent.com/d/{$matches[1]}";
    }

    if (str_contains($url, 'drive.google.com/uc?export=view&id=')) {
        if (preg_match('#id=([a-zA-Z0-9_-]+)#', $url, $matches)) {
            return "https://lh3.googleusercontent.com/d/{$matches[1]}";
        }
    }

    // Dropbox link conversion (dl=0 -> raw=1)
    if (str_contains($url, 'dropbox.com')) {
        if (str_contains($url, 'dl=0')) {
            return str_replace('dl=0', 'raw=1', $url);
        }
        if (!str_contains($url, 'raw=1') && !str_contains($url, 'dl=1')) {
            return $url . (str_contains($url, '?') ? '&raw=1' : '?raw=1');
        }
    }

    return $url;
}

function rupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth bg-[#09090b] text-[#fafafa] overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($brand['name']) ?> — Sandal Kasual & Distro Pria Asli Bekasi</title>
    <meta name="description" content="Sandal distro & kasual pria Hiragana Footwear. Busa Phylon tebal anti-pegal, strap kulit sintetis awet, sol karet anti-slip. 100% buatan lokal Bekasi, bisa COD & garansi tukar size.">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: {
                            950: '#09090b',
                            900: '#121214',
                            850: '#18181b',
                            800: '#27272a',
                            700: '#3f3f46'
                        },
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706'
                        },
                        orange: {
                            500: '#f97316',
                            600: '#ea580c'
                        }
                    },
                    fontFamily: {
                        condensed: ['Barlow Condensed', 'sans-serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts: Barlow Condensed (Bold Masculine Distro) + Plus Jakarta Sans + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,600;0,700;0,800;0,900;1,700;1,900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Instant Theme Detection (Prevents FOUC) -->
    <script>
        (function() {
            try {
                if (localStorage.getItem('hiragana_theme') === 'light') {
                    document.documentElement.classList.add('light-mode');
                }
            } catch(e) {}
        })();
    </script>

    <style>
        .font-distro { font-family: 'Barlow Condensed', sans-serif; }
        .font-tech { font-family: 'JetBrains Mono', monospace; }
        
        /* Dark Carbon / Asphalt Background */
        .carbon-bg {
            background-color: #0c0c0e;
            background-image: radial-gradient(rgba(245, 158, 11, 0.08) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .stripe-banner {
            background: repeating-linear-gradient(
                -45deg,
                #f59e0b,
                #f59e0b 10px,
                #18181b 10px,
                #18181b 20px
            );
        }

        /* ========================================================
           LIGHT MODE STYLING (Crisp, High-End Distro Streetwear)
           ======================================================== */
        html.light-mode body {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        html.light-mode .carbon-bg {
            background-color: #f8fafc !important;
            background-image: radial-gradient(rgba(15, 23, 42, 0.08) 1px, transparent 1px) !important;
        }

        html.light-mode header {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-bottom-color: #e2e8f0 !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05) !important;
        }

        html.light-mode #mobileMenu {
            background-color: #ffffff !important;
            border-bottom-color: #e2e8f0 !important;
        }

        html.light-mode #mobileMenu a {
            color: #334155 !important;
        }

        html.light-mode #mobileMenu a:hover {
            color: #f59e0b !important;
        }

        /* Surfaces & Cards */
        html.light-mode .bg-\[\#18181b\] {
            background-color: #ffffff !important;
        }

        html.light-mode .bg-\[\#121214\] {
            background-color: #f1f5f9 !important;
        }

        html.light-mode .bg-\[\#09090b\] {
            background-color: #f8fafc !important;
        }

        html.light-mode .bg-\[\#27272a\] {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        html.light-mode .border-\[\#27272a\] {
            border-color: #e2e8f0 !important;
        }

        html.light-mode .border-\[\#3f3f46\] {
            border-color: #cbd5e1 !important;
        }

        /* Texts */
        html.light-mode .text-white,
        html.light-mode .text-\[\#fafafa\],
        html.light-mode .text-\[\#d4d4d8\],
        html.light-mode .text-slate-300 {
            color: #0f172a !important;
        }

        html.light-mode .text-\[\#a1a1aa\],
        html.light-mode .text-slate-400 {
            color: #64748b !important;
        }

        html.light-mode .distro-card {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.06) !important;
        }

        html.light-mode .distro-card:hover {
            border-color: #f59e0b !important;
            box-shadow: 0 12px 24px -4px rgba(245, 158, 11, 0.15) !important;
        }


        html.light-mode header button {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        html.light-mode header button:hover {
            background-color: #e2e8f0 !important;
            color: #f59e0b !important;
        }

        html.light-mode #themeToggleBtn {
            color: #d97706 !important;
        }

        /* Top Announcement Bar remains Dark Charcoal for premium contrast */
        html.light-mode aside[aria-label="Announcement"] {
            background-color: #0f172a !important;
            border-bottom-color: #1e293b !important;
        }

        html.light-mode aside[aria-label="Announcement"] .text-white {
            color: #ffffff !important;
        }

        html.light-mode aside[aria-label="Announcement"] .text-\[\#a1a1aa\] {
            color: #94a3b8 !important;
        }

        /* Hero Featured Card */
        html.light-mode .group\/heroCard {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.08) !important;
        }

        /* Size Chart Table */
        html.light-mode table thead {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        html.light-mode table tbody tr:hover {
            background-color: rgba(241, 245, 249, 0.8) !important;
        }

        /* Modal */
        html.light-mode #distroModal .bg-\[\#18181b\] {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }

        html.light-mode .size-choice {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        html.light-mode .size-choice.bg-amber-500 {
            background-color: #f59e0b !important;
            color: #000000 !important;
            border-color: #f59e0b !important;
        }

        /* Search input */
        html.light-mode #distroSearch {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        html.light-mode #distroSearch::placeholder {
            color: #94a3b8 !important;
        }

        /* Quick filters */
        html.light-mode .distro-btn {
            border-color: #cbd5e1 !important;
        }

        /* Smooth horizontal scroll without scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="carbon-bg font-sans antialiased text-[#fafafa] selection:bg-amber-500 selection:text-black overflow-x-hidden">

    <!-- TOP INDUSTRIAL UTILITY STRIP -->
    <?php if (!empty($features['banner_promo'])): ?>
    <aside aria-label="Announcement" class="hidden md:block bg-[#18181b] text-[#a1a1aa] text-[11px] font-tech py-2 px-4 border-b border-[#27272a] uppercase tracking-wider">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span class="text-white font-bold tracking-widest">[HIRAGANA BEKASI WORKSHOP]</span>
                <span class="text-[#71717a] hidden sm:inline">•</span>
                <span class="text-amber-400 font-bold hidden sm:inline"><?= htmlspecialchars($brand['banner_announcement']) ?></span>
            </div>
            <div class="flex items-center gap-4 text-[10px] text-[#a1a1aa] font-bold">
                <?php if (!empty($keunggulan['garansi_tukar_size'])): ?>
                    <span class="flex items-center gap-1 text-emerald-400"><i data-lucide="shield-check" class="w-3.5 h-3.5"></i> 100% GARANSI TUKAR SIZE</span>
                <?php endif; ?>
                <?php if (!empty($keunggulan['bisa_cod'])): ?>
                    <span class="hidden md:flex items-center gap-1 text-white"><i data-lucide="truck" class="w-3.5 h-3.5 text-amber-500"></i> BISA BAYAR DI TEMPAT (COD)</span>
                <?php endif; ?>
                <?php if (!empty($keunggulan['siap_kirim_indonesia'])): ?>
                    <span class="hidden xl:flex items-center gap-1 text-[#d4d4d8]"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-blue-400"></i> PENGIRIMAN SE-INDONESIA</span>
                <?php endif; ?>
            </div>
        </div>
    </aside>
    <?php endif; ?>

    <!-- MAIN NAVBAR -->
    <header class="sticky top-0 z-40 bg-[#121214]/95 backdrop-blur-md border-b border-[#27272a] shadow-lg">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between gap-1.5 sm:gap-4">
            
            <!-- Logo Brand Cowok (Hanya Logo yang Berbentuk Rounded Kotak) -->
            <a href="#" class="flex items-center gap-2.5 sm:gap-3 group shrink min-w-0">
                <?php if (!empty($brand['logo_url'])): ?>
                    <div class="h-9 w-9 sm:h-11 sm:w-11 rounded-xl bg-white p-1 flex items-center justify-center shrink-0 border border-slate-200/80 shadow-sm overflow-hidden group-hover:scale-105 transition-transform duration-200">
                        <img src="<?= htmlspecialchars($brand['logo_url']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>" class="max-h-full max-w-full object-contain">
                    </div>
                <?php else: ?>
                    <div class="w-9 h-9 sm:w-11 sm:h-11 bg-amber-500 text-black flex items-center justify-center font-distro font-black text-lg sm:text-2xl tracking-tighter rounded-xl group-hover:bg-amber-400 transition transform group-hover:-translate-y-0.5 shrink-0 shadow-sm">
                        HF
                    </div>
                <?php endif; ?>
                <div class="min-w-0">
                    <span class="font-distro font-black text-lg sm:text-2xl tracking-tight text-white block leading-none group-hover:text-amber-400 transition truncate">
                        <?= htmlspecialchars($brand['name']) ?><span class="text-amber-500">.</span>
                    </span>
                    <span class="text-[9px] sm:text-[10px] font-tech font-bold uppercase tracking-[0.12em] sm:tracking-[0.18em] text-[#a1a1aa] block pt-1 truncate">
                        MEN'S CASUAL FOOTWEAR
                    </span>
                </div>
            </a>

            <!-- Nav Links Cowok (Concise, Clean & Collision-Free) -->
            <nav class="hidden lg:flex items-center gap-4 xl:gap-6 text-xs xl:text-sm font-distro uppercase tracking-wider font-bold text-[#d4d4d8]">
                <a href="#katalog" class="text-white hover:text-amber-400 transition flex items-center gap-1.5 whitespace-nowrap">
                    <span class="text-amber-500">⚡</span>
                    <span>KATALOG</span>
                    <span class="px-1.5 py-0.5 bg-amber-500/10 text-amber-400 text-[10px] rounded font-tech border border-amber-500/30">15 SERI</span>
                </a>
                <a href="#material" class="hover:text-amber-400 transition whitespace-nowrap">MATERIAL</a>
                <a href="#size-chart" class="hover:text-amber-400 transition whitespace-nowrap">SIZE CHART</a>
                <?php if (!empty($keunggulan['eceran_grosir'])): ?>
                    <a href="#grosir" class="hover:text-amber-400 transition whitespace-nowrap">GROSIR <span class="hidden xl:inline">& RESELLER</span></a>
                <?php endif; ?>
                <a href="#testimoni" class="hover:text-amber-400 transition whitespace-nowrap">TESTIMONI</a>
            </nav>

            <!-- Action Buttons (shrink-0 with safe mobile spacing) -->
            <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0">
                <!-- Dark / Light Mode Toggle Button -->
                <button id="themeToggleBtn" onclick="toggleTheme()" type="button" class="w-8 h-8 sm:w-auto sm:h-auto p-1.5 sm:px-2.5 sm:py-2 rounded-lg sm:rounded-xl bg-[#27272a] hover:bg-amber-500 hover:text-black text-amber-400 transition flex items-center justify-center border border-[#3f3f46] shrink-0" title="Ganti Mode Gelap / Terang" aria-label="Toggle Dark/Light Mode">
                    <i id="themeIcon" data-lucide="sun" class="w-4 h-4"></i>
                    <span id="themeText" class="hidden sm:inline font-tech text-[10px] font-bold uppercase ml-1.5 tracking-wider">LIGHT</span>
                </button>

                <?php if (!empty($features['checkout_wa'])): ?>
                    <a href="https://wa.me/<?= $brand['whatsapp'] ?>?text=Halo%20Admin%20Hiragana,%20mau%20tanya%20stok%20sandal%20cowok%20dong" target="_blank" class="hidden md:inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-wider transition shadow-sm shrink-0">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        <span>ORDER VIA WA</span>
                    </a>
                <?php endif; ?>
                
                <a href="#katalog" class="px-2.5 sm:px-5 py-1.5 sm:py-2.5 bg-amber-500 hover:bg-amber-400 text-black font-distro font-black text-xs sm:text-sm uppercase tracking-wider rounded-lg sm:rounded-xl transition shadow-md shadow-amber-500/20 shrink-0 whitespace-nowrap">
                    PILIH SANDAL
                </a>
                
                <button onclick="toggleMobileMenu()" class="lg:hidden w-8 h-8 sm:w-9 sm:h-9 p-1.5 sm:p-2 rounded-lg sm:rounded-xl bg-[#18181b] hover:bg-[#27272a] border border-[#27272a] text-white hover:text-amber-400 transition flex items-center justify-center shrink-0" aria-label="Menu">
                    <i data-lucide="menu" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </button>
            </div>

        </div>

        <!-- Mobile Menu Drawer -->
        <div id="mobileMenu" class="hidden lg:hidden bg-[#18181b] border-b border-[#27272a] px-5 py-5 space-y-3 font-distro text-base uppercase tracking-wider">
            <a href="#katalog" onclick="toggleMobileMenu()" class="block text-amber-400 font-bold py-1.5 flex items-center justify-between">
                <span>Katalog Sandal Cowok (15 Seri)</span>
                <span class="text-xs font-tech bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded">READY</span>
            </a>
            <a href="#material" onclick="toggleMobileMenu()" class="block text-slate-300 hover:text-white py-1">Material & Ketahanan</a>
            <a href="#size-chart" onclick="toggleMobileMenu()" class="block text-slate-300 hover:text-white py-1">Panduan Ukuran Kaki (Size 39-44)</a>
            <a href="#grosir" onclick="toggleMobileMenu()" class="block text-slate-300 hover:text-white py-1">Grosir & Reseller Distro</a>
            <a href="#testimoni" onclick="toggleMobileMenu()" class="block text-slate-300 hover:text-white py-1">Testimoni Pembeli</a>
            <div class="pt-3 border-t border-[#27272a] space-y-2">
                <button onclick="toggleTheme()" type="button" class="w-full py-2.5 px-4 rounded-xl bg-[#27272a] text-amber-400 font-tech text-xs font-bold uppercase tracking-wider flex items-center justify-between border border-[#3f3f46]">
                    <span class="flex items-center gap-2">
                        <i data-lucide="sun-moon" class="w-4 h-4"></i>
                        <span>Mode Tampilan</span>
                    </span>
                    <span id="mobileThemeLabel" class="text-[10px] bg-black/40 px-2 py-0.5 rounded font-mono">DARK</span>
                </button>
                <a href="https://wa.me/<?= $brand['whatsapp'] ?>" target="_blank" class="block w-full py-3 bg-emerald-600 text-white text-center font-bold text-xs uppercase tracking-wider rounded-xl">
                    Chat WhatsApp Admin
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION (BOLD, MASCULINE & CASUAL COWOK) -->
    <section class="relative pt-12 pb-20 sm:pt-20 sm:pb-28 border-b border-[#27272a] overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
                
                <!-- Left: Bold Headline & Distro Statement (Centered on Mobile, Left on Desktop) -->
                <div class="lg:col-span-7 space-y-4 sm:space-y-6 text-center lg:text-left">
                    
                    <div class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-400 font-tech text-[11px] sm:text-xs uppercase tracking-widest font-bold mx-auto lg:mx-0">
                        <span>⚡</span>
                        <span>ORIGINAL STREETWEAR & CASUAL FOOTWEAR</span>
                    </div>

                    <h1 class="font-distro font-black text-4xl sm:text-7xl lg:text-8xl tracking-tight text-white leading-[0.95] uppercase">
                        TANGGUH DI JALAN, <br>
                        <span class="text-amber-500">NYAMAN DI KAKI.</span>
                    </h1>

                    <p class="text-xs sm:text-base text-[#a1a1aa] max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                        Sandal kasual & distro pria <strong>handmade pengrajin lokal Bekasi</strong>. Menggabungkan bantalan <strong>Phylon tebal anti-kempes (ga bikin kaki lecet)</strong>, strap kulit sintetis tebal yang lentur, dan sol karet anti-slip untuk gaya santai cowok yang maskulin dan berkarakter.
                    </p>

                    <!-- Dual Tough Action Buttons (Kiri & Kanan Berdampingan di Mobile) -->
                    <div class="pt-2 grid grid-cols-2 gap-2.5 sm:gap-3.5 sm:flex sm:flex-wrap sm:items-center sm:justify-start">
                        <a href="#katalog" class="px-3 sm:px-7 py-3 sm:py-3.5 bg-amber-500 hover:bg-amber-400 text-black font-distro font-black text-xs sm:text-base uppercase tracking-wider rounded-xl transition duration-200 flex items-center justify-center gap-1.5 sm:gap-2.5 shadow-lg shadow-amber-500/20 text-center">
                            <span class="sm:hidden">KATALOG</span>
                            <span class="hidden sm:inline">LIHAT KATALOG LENGKAP</span>
                            <i data-lucide="arrow-down-circle" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                        </a>
                        <a href="https://wa.me/<?= $brand['whatsapp'] ?>?text=Halo%20Admin%20Hiragana,%20mau%20order%20sandal%20cowok%20dong" target="_blank" class="px-3 sm:px-7 py-3 sm:py-3.5 bg-[#18181b] hover:bg-[#27272a] text-white border border-[#3f3f46] font-distro font-bold text-xs sm:text-base uppercase tracking-wider rounded-xl transition flex items-center justify-center gap-1.5 sm:gap-2 text-center">
                            <i data-lucide="message-circle" class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-400"></i>
                            <span class="sm:hidden">ORDER WA</span>
                            <span class="hidden sm:inline">ORDER VIA WHATSAPP</span>
                        </a>
                    </div>

                    <!-- Marketplace Official Badges (Di bawah label Toko Resmi) -->
                    <div class="pt-2 sm:pt-4 space-y-2 text-center lg:text-left">
                        <span class="block text-[11px] sm:text-xs font-tech uppercase tracking-widest text-[#a1a1aa] font-bold">
                            Toko Resmi:
                        </span>
                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 sm:gap-3 text-[11px] sm:text-xs font-tech">
                            <a href="<?= $brand['shopee_url'] ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#27272a] hover:bg-orange-600 hover:text-white text-orange-400 font-bold transition shadow-xs">
                                <span>Shopee Official</span>
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            </a>
                            <a href="<?= $brand['tokopedia_url'] ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#27272a] hover:bg-emerald-600 hover:text-white text-emerald-400 font-bold transition shadow-xs">
                                <span>Tokopedia Store</span>
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            </a>
                            <a href="<?= $brand['tiktok_url'] ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#27272a] hover:bg-slate-900 hover:text-white text-sky-400 font-bold transition shadow-xs">
                                <span>TikTok Shop</span>
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>

                </div>

                <!-- Right: High-Impact Rugged Featured Card (Mirrors Catalog Product 0 & Auto-Slides) -->
                <?php
                $featured = !empty($products) ? $products[0] : null;
                $featuredImages = [];
                if ($featured) {
                    if (!empty($featured['images']) && is_array($featured['images'])) {
                        $featuredImages = array_values(array_filter(array_map('normalizeImageUrl', $featured['images'])));
                    } elseif (!empty($featured['image_url'])) {
                        $featuredImages = [normalizeImageUrl($featured['image_url'])];
                    }
                }
                if (empty($featuredImages)) {
                    $featuredImages = ['https://lh3.googleusercontent.com/d/1M7t0bvc9j906MjANcCwCrZOxqI8nhMp_'];
                }
                $featuredHasMultiple = count($featuredImages) > 1;
                $featuredImagesJson = htmlspecialchars(json_encode($featuredImages), ENT_QUOTES, 'UTF-8');
                $featName = $featured['name'] ?? 'Sandal Toshima Series';
                $featPrice = (int)($featured['price'] ?? 145000);
                $featNormalPrice = (int)($featured['normal_price'] ?? ($featPrice + 90000));
                $featDesc = $featured['description'] ?? 'Insole Phylon empuk tanpa sambungan kasar. Tali jepit dilapisi kanvas tebal sehingga nyaman dipakai jalan jauh, nongkrong, atau riding santai.';
                $featSizes = $featured['sizes'] ?? [39, 40, 41, 42, 43, 44];
                $featTag = $featured['tag'] ?? 'BEST SELLER COWOK';
                $featCode = $featured['code'] ?? 'HF-TSM-01';
                $featColors = !empty($featured['colors']) && is_array($featured['colors']) 
                    ? implode(' / ', array_map(fn($c) => is_array($c) ? ($c['name'] ?? '') : $c, $featured['colors']))
                    : 'BLACK / DARK BROWN / TAN / NAVY';
                ?>
                <div class="lg:col-span-5">
                    <div class="bg-[#18181b] border-2 border-[#27272a] hover:border-amber-500/50 transition duration-300 rounded-3xl p-6 sm:p-7 shadow-2xl space-y-5 relative group/heroCard">
                        
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 bg-amber-500 text-black font-distro font-black text-xs uppercase tracking-wider rounded-md shadow-sm">
                                <?= htmlspecialchars($featTag) ?>
                            </span>
                            <span class="font-tech text-xs text-[#a1a1aa] font-bold flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                SERI <?= strtoupper(str_replace(['Sandal ', ' Series', ' series'], '', $featName)) ?>
                            </span>
                        </div>

                        <!-- Product Image Frame with Multi-Image Carousel & Auto-Slide -->
                        <div class="relative aspect-[4/3] rounded-2xl overflow-hidden bg-[#121214] border border-[#27272a] group/heroPhoto cursor-pointer"
                             data-slider-id="hero"
                             data-images="<?= $featuredImagesJson ?>"
                             onclick="openDistroModal(<?= htmlspecialchars(json_encode($featured), ENT_QUOTES, 'UTF-8') ?>)"
                             title="Klik untuk lihat detail <?= htmlspecialchars($featName) ?>">
                            
                            <img id="slider_hero_img" src="<?= htmlspecialchars($featuredImages[0]) ?>" alt="<?= htmlspecialchars($featName) ?>" class="w-full h-full object-cover group-hover/heroPhoto:scale-105 transition-all duration-500">
                            
                            <!-- Badges Top Right -->
                            <div class="absolute top-3 right-3 z-10 flex items-center gap-1.5 pointer-events-none">
                                <?php if ($featuredHasMultiple): ?>
                                    <span id="slider_hero_counter" class="bg-black/85 text-amber-400 font-tech text-[10px] uppercase font-bold px-2 py-0.5 rounded border border-[#3f3f46] shadow-sm">
                                        1/<?= count($featuredImages) ?>
                                    </span>
                                <?php endif; ?>
                                <span class="bg-black/80 text-white font-tech text-[10px] uppercase font-bold px-2 py-0.5 rounded border border-[#3f3f46]">
                                    <?= htmlspecialchars($featCode) ?>
                                </span>
                            </div>

                            <!-- Color Palette Strip Bottom Left -->
                            <div class="absolute bottom-3 left-3 bg-black/85 text-amber-300 font-tech text-[10px] uppercase font-bold px-3 py-1 rounded-md border border-[#3f3f46] z-10 pointer-events-none shadow">
                                <?= htmlspecialchars($featColors) ?>
                            </div>

                            <!-- Carousel Navigation Arrows (< & >) -->
                            <?php if ($featuredHasMultiple): ?>
                                <button type="button" onclick="prevCardSlider('hero', event)" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/85 hover:bg-amber-500 hover:text-black text-white flex items-center justify-center transition border border-[#3f3f46] z-20 opacity-0 group-hover/heroCard:opacity-100 shadow-lg cursor-pointer text-sm font-bold" title="Foto Sebelumnya">
                                    ❮
                                </button>
                                <button type="button" onclick="nextCardSlider('hero', event)" class="absolute right-2.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/85 hover:bg-amber-500 hover:text-black text-white flex items-center justify-center transition border border-[#3f3f46] z-20 opacity-0 group-hover/heroCard:opacity-100 shadow-lg cursor-pointer text-sm font-bold" title="Foto Selanjutnya">
                                    ❯
                                </button>

                                <!-- Bottom Dots Indicator -->
                                <div class="absolute bottom-2.5 inset-x-0 flex items-center justify-center gap-1.5 z-20 pointer-events-auto">
                                    <?php foreach ($featuredImages as $dIdx => $dImg): ?>
                                        <button type="button" onclick="setCardSliderIdx('hero', <?= $dIdx ?>, event)" class="slider-dot-hero h-1.5 rounded-full transition-all duration-300 <?= $dIdx === 0 ? 'w-5 bg-amber-500' : 'w-1.5 bg-white/60 hover:bg-white' ?> cursor-pointer" aria-label="Foto <?= $dIdx + 1 ?>"></button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-baseline justify-between gap-3">
                                <h3 class="font-distro font-black text-2xl sm:text-3xl text-white uppercase group-hover/heroCard:text-amber-400 transition">
                                    <?= htmlspecialchars($featName) ?>
                                </h3>
                                <div class="text-right shrink-0">
                                    <span class="font-tech text-xl sm:text-2xl font-black text-amber-400 block"><?= rupiah($featPrice) ?></span>
                                    <span class="font-tech text-xs text-[#71717a] line-through block"><?= rupiah($featNormalPrice) ?></span>
                                </div>
                            </div>
                            <p class="text-xs text-[#a1a1aa] leading-relaxed line-clamp-2">
                                <?= htmlspecialchars($featDesc) ?>
                            </p>
                        </div>

                        <div class="pt-3 border-t border-[#27272a] flex items-center justify-between text-xs font-tech text-[#a1a1aa]">
                            <span class="text-emerald-400 font-bold flex items-center gap-1">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> Ready Size <?= min($featSizes) ?> - <?= max($featSizes) ?>
                            </span>
                            <button type="button" onclick="openDistroModal(<?= htmlspecialchars(json_encode($featured), ENT_QUOTES, 'UTF-8') ?>)" class="text-amber-400 font-bold hover:text-amber-300 flex items-center gap-1 group/btn transition cursor-pointer">
                                <span>DETAIL & ORDER</span>
                                <span class="group-hover/btn:translate-x-0.5 transition transform">→</span>
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- TRUST COUNTERS (DISTRO COWOK) -->
    <section class="bg-[#121214] border-b border-[#27272a] py-7">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center md:divide-x md:divide-[#27272a]">
                <div class="border-t border-[#27272a] md:border-t-0 pt-3 md:pt-0 space-y-0.5 py-1">
                    <span class="font-distro font-black text-3xl sm:text-4xl text-white tracking-wider">10.000+</span>
                    <p class="font-tech text-[11px] text-[#a1a1aa] font-bold uppercase tracking-wider">Pasang Terjual</p>
                </div>
                <div class="border-t border-[#27272a] md:border-t-0 pt-3 md:pt-0 space-y-0.5 py-1">
                    <span class="font-distro font-black text-3xl sm:text-4xl text-amber-500 tracking-wider">15 SERI</span>
                    <p class="font-tech text-[11px] text-[#a1a1aa] font-bold uppercase tracking-wider">Pilihan Sandal Pria</p>
                </div>
                <div class="border-t border-[#27272a] md:border-t-0 pt-3 md:pt-0 space-y-0.5 py-1">
                    <span class="font-distro font-black text-3xl sm:text-4xl text-white tracking-wider">4.9 / 5.0</span>
                    <p class="font-tech text-[11px] text-[#a1a1aa] font-bold uppercase tracking-wider">Rating Toko Online</p>
                </div>
                <div class="border-t border-[#27272a] md:border-t-0 pt-3 md:pt-0 space-y-0.5 py-1">
                    <?php if (!empty($keunggulan['garansi_tukar_size'])): ?>
                        <span class="font-distro font-black text-3xl sm:text-4xl text-emerald-400 tracking-wider">BISA RETUR</span>
                        <p class="font-tech text-[11px] text-[#a1a1aa] font-bold uppercase tracking-wider">Garansi Tukar Size</p>
                    <?php elseif (!empty($keunggulan['bisa_cod'])): ?>
                        <span class="font-distro font-black text-3xl sm:text-4xl text-amber-500 tracking-wider">BISA COD</span>
                        <p class="font-tech text-[11px] text-[#a1a1aa] font-bold uppercase tracking-wider">Bayar di Tempat</p>
                    <?php else: ?>
                        <span class="font-distro font-black text-3xl sm:text-4xl text-white tracking-wider">100% ASLI</span>
                        <p class="font-tech text-[11px] text-[#a1a1aa] font-bold uppercase tracking-wider">Pengrajin Bekasi</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- KEUNGGULAN MATERIAL & KEKUATAN (RUGGED CRAFTSMANSHIP) -->
    <section id="material" class="py-20 border-b border-[#27272a]">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-8 sm:space-y-12">
            
            <div class="max-w-2xl space-y-2">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-[2px] bg-amber-500"></span>
                    <span class="font-tech text-xs uppercase tracking-widest text-amber-400 font-bold">MATERIAL & KEKUATAN PRODUK</span>
                </div>
                <h2 class="font-distro font-black text-3xl sm:text-5xl text-white uppercase tracking-tight">
                    DIRANCANG KUAT BUAT COWOK AKTIF.
                </h2>
                <p class="text-xs sm:text-sm text-[#a1a1aa] leading-relaxed">
                    Kami tahu sandal cowok harus siap dipakai ke mana saja: nongkrong di warkop, jalan-jalan santai, kehujanan, sampai touring tipis. Empat alasan kenapa Hiragana Footwear beda dari sandal biasa:
                </p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-6">
                
                <div class="bg-[#18181b] p-3.5 sm:p-6 lg:p-7 rounded-xl sm:rounded-2xl border border-[#27272a] hover:border-amber-500 transition space-y-1.5 sm:space-y-3 flex flex-col">
                    <div class="font-tech text-amber-500 font-black text-base sm:text-xl">01.</div>
                    <h3 class="font-distro font-bold text-xs sm:text-lg lg:text-xl text-white uppercase leading-tight sm:leading-snug">Insole Phylon Anti-Kempes</h3>
                    <p class="text-[11px] sm:text-xs text-[#a1a1aa] leading-relaxed">
                        Bantalan phylon tebal yang dicetak mengikuti lekuk telapak kaki pria. Sangat empuk, meredam benturan saat melangkah, dan ga gampang tipis/kempes biarpun dipakai cowok berbadan besar.
                    </p>
                </div>

                <div class="bg-[#18181b] p-3.5 sm:p-6 lg:p-7 rounded-xl sm:rounded-2xl border border-[#27272a] hover:border-amber-500 transition space-y-1.5 sm:space-y-3 flex flex-col">
                    <div class="font-tech text-amber-500 font-black text-base sm:text-xl">02.</div>
                    <h3 class="font-distro font-bold text-xs sm:text-lg lg:text-xl text-white uppercase leading-tight sm:leading-snug">Kulit Sintetis Tebal Grade A</h3>
                    <p class="text-[11px] sm:text-xs text-[#a1a1aa] leading-relaxed">
                        Bahan strap kulit sintetis premium tebal yang lentur di kaki dan tahan cipratan air. Gampang dibersihkan saat kena tanah becek, ga kaku, dan ga gampang retak atau terkelupas.
                    </p>
                </div>

                <div class="bg-[#18181b] p-3.5 sm:p-6 lg:p-7 rounded-xl sm:rounded-2xl border border-[#27272a] hover:border-amber-500 transition space-y-1.5 sm:space-y-3 flex flex-col">
                    <div class="font-tech text-amber-500 font-black text-base sm:text-xl">03.</div>
                    <h3 class="font-distro font-bold text-xs sm:text-lg lg:text-xl text-white uppercase leading-tight sm:leading-snug">Sol Karet Mentah Anti-Slip</h3>
                    <p class="text-[11px] sm:text-xs text-[#a1a1aa] leading-relaxed">
                        Bagian bawah menggunakan karet mentah vulkanisir dengan ulir tapak bergerigi. Mencengkeram kuat di lantai keramik basah, tangga masjid, maupun jalanan aspal berpasir.
                    </p>
                </div>

                <div class="bg-[#18181b] p-3.5 sm:p-6 lg:p-7 rounded-xl sm:rounded-2xl border border-[#27272a] hover:border-amber-500 transition space-y-1.5 sm:space-y-3 flex flex-col">
                    <div class="font-tech text-amber-500 font-black text-base sm:text-xl">04.</div>
                    <h3 class="font-distro font-bold text-xs sm:text-lg lg:text-xl text-white uppercase leading-tight sm:leading-snug">Tali Kuat & Anti-Lecet</h3>
                    <p class="text-[11px] sm:text-xs text-[#a1a1aa] leading-relaxed">
                        Bagian bawah tali jepit dilapisi kain kanvas lembut, dan penahan jepit ditanam kuat ke dalam sol dengan jahitan ganda. Ga bikin sela jari merah atau lecet saat dipakai jalan jauh.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- KATALOG SANDAL COWOK (SHOWCASE 15 SERI) -->
    <section id="katalog" class="py-20 bg-[#121214] border-b border-[#27272a]">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-6 sm:space-y-10">
            
            <!-- Section Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-4 border-b border-[#27272a]">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-[2px] bg-amber-500"></span>
                        <span class="font-tech text-xs uppercase tracking-widest text-amber-400 font-bold">KOLEKSI RESMI 2026</span>
                    </div>
                    <h2 class="font-distro font-black text-4xl sm:text-5xl text-white uppercase tracking-tight">
                        KATALOG LENGKAP SANDAL COWOK
                    </h2>
                </div>
                <div class="text-xs font-tech text-[#a1a1aa]">
                    PILIH SERI FAVORITMU • <span class="text-amber-400 font-bold">15 PRODUK READY</span>
                </div>
            </div>

            <!-- Filter Buttons & Search -->
            <div class="bg-[#18181b] p-4 rounded-2xl border border-[#27272a] flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                
                <!-- Category Tabs (Horizontal Scrollable Chips di Mobile, Wrap di Desktop) -->
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth -mx-1 px-1 py-1 md:py-0 md:flex-wrap" id="distroTabs">
                    <button onclick="setDistroFilter('ALL')" class="distro-btn active px-3.5 sm:px-4 py-2 rounded-xl text-xs font-distro uppercase tracking-wider font-bold bg-amber-500 text-black transition whitespace-nowrap shrink-0">
                        Semua Seri (15)
                    </button>
                    <button onclick="setDistroFilter('SANDAL JEPIT DISTRO')" class="distro-btn px-3.5 sm:px-4 py-2 rounded-xl text-xs font-distro uppercase tracking-wider font-bold bg-[#27272a] text-[#a1a1aa] hover:text-white transition whitespace-nowrap shrink-0">
                        Sandal Jepit (6)
                    </button>
                    <button onclick="setDistroFilter('SANDAL SLOP & KOKOP')" class="distro-btn px-3.5 sm:px-4 py-2 rounded-xl text-xs font-distro uppercase tracking-wider font-bold bg-[#27272a] text-[#a1a1aa] hover:text-white transition whitespace-nowrap shrink-0">
                        Sandal Slop / Kokop (6)
                    </button>
                    <button onclick="setDistroFilter('SEPATU SAFETY')" class="distro-btn px-3.5 sm:px-4 py-2 rounded-xl text-xs font-distro uppercase tracking-wider font-bold bg-[#27272a] text-[#a1a1aa] hover:text-white transition whitespace-nowrap shrink-0">
                        Sepatu Safety Kerja (2)
                    </button>
                    <button onclick="setDistroFilter('SEPATU KASUAL')" class="distro-btn px-3.5 sm:px-4 py-2 rounded-xl text-xs font-distro uppercase tracking-wider font-bold bg-[#27272a] text-[#a1a1aa] hover:text-white transition whitespace-nowrap shrink-0">
                        Sneakers (1)
                    </button>
                </div>

                <!-- Search Input -->
                <div class="relative w-full md:w-72">
                    <i data-lucide="search" class="w-4 h-4 text-[#71717a] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input type="text" id="distroSearch" oninput="filterDistro()" placeholder="Cari nama seri (Toshima, Osaka)..." class="w-full pl-9 pr-4 py-2 bg-[#121214] border border-[#27272a] rounded-xl text-xs font-tech text-white focus:outline-none focus:border-amber-500 transition">
                </div>

            </div>

            <!-- Products Grid (2 Kolom di Mobile / 50% Kiri Kanan, 3 Kolom di Desktop) -->
            <div id="distroGrid" class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
                <?php foreach ($products as $idx => $prod): 
                    $cat = htmlspecialchars($prod['category'] ?? 'UMUM');
                    $subCat = htmlspecialchars($prod['sub_category'] ?? '');
                    $name = htmlspecialchars($prod['name'] ?? '');
                    $code = htmlspecialchars($prod['code'] ?? 'HF-' . str_pad($idx+1, 2, '0', STR_PAD_LEFT));
                    $tag = htmlspecialchars($prod['tag'] ?? 'POPULER');
                    $price = (int) ($prod['price'] ?? 145000);
                    $normalPrice = (int) ($prod['normal_price'] ?? ($price + 80000));
                    
                    // Normalize up to 3 images
                    $allImages = !empty($prod['images']) && is_array($prod['images'])
                        ? array_values(array_filter(array_map('normalizeImageUrl', $prod['images'])))
                        : (!empty($prod['image_url']) ? [normalizeImageUrl($prod['image_url'])] : []);
                    if (empty($allImages)) {
                        $allImages = ['https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=800&q=80'];
                    }
                    $img = htmlspecialchars($allImages[0]);
                    $prod['images'] = $allImages;
                    $prod['image_url'] = $allImages[0];
                    $hasMultiple = count($allImages) > 1;
                    $allImagesJson = htmlspecialchars(json_encode($allImages), ENT_QUOTES, 'UTF-8');

                    $colors = $prod['colors'] ?? [];
                    $sizes = $prod['sizes'] ?? [39, 40, 41, 42, 43];
                    $desc = htmlspecialchars($prod['description'] ?? '');
                ?>
                <div class="distro-card bg-[#18181b] border-2 border-[#27272a] hover:border-amber-500/80 rounded-xl sm:rounded-2xl p-2.5 sm:p-5 flex flex-col justify-between transition duration-200 group shadow-md"
                     data-category="<?= $cat ?>"
                     data-name="<?= strtolower($name) ?> <?= strtolower($subCat) ?> <?= strtolower($code) ?>">
                    
                    <div class="space-y-2 sm:space-y-3.5">
                        
                        <!-- Product Visual Frame with Carousel & Auto-Slide -->
                        <div class="relative aspect-[4/3] rounded-lg sm:rounded-xl overflow-hidden bg-[#121214] border border-[#27272a] group/cardPhoto cursor-pointer"
                             data-slider-id="card_<?= $idx ?>"
                             data-images="<?= $allImagesJson ?>"
                             onclick="openDistroModal(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES, 'UTF-8') ?>)">
                            <img id="slider_card_<?= $idx ?>_img" src="<?= $img ?>" alt="<?= $name ?>" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                            
                            <div class="absolute top-1.5 left-1.5 sm:top-2.5 sm:left-2.5 z-10 pointer-events-none">
                                <span class="bg-amber-500 text-black font-distro font-black text-[9px] sm:text-[11px] uppercase tracking-wider px-1.5 sm:px-2.5 py-0.5 rounded shadow">
                                    <?= $tag ?>
                                </span>
                            </div>

                            <div class="absolute top-1.5 right-1.5 sm:top-2.5 sm:right-2.5 z-10 flex items-center gap-1 sm:gap-1.5 pointer-events-none">
                                <?php if ($hasMultiple): ?>
                                    <span id="slider_card_<?= $idx ?>_counter" class="bg-black/85 text-amber-400 font-tech text-[8px] sm:text-[10px] uppercase font-bold px-1.5 sm:px-2 py-0.5 rounded border border-[#3f3f46] shadow-sm">
                                        1/<?= count($allImages) ?>
                                    </span>
                                <?php endif; ?>
                                <span class="bg-black/80 text-white font-tech text-[8px] sm:text-[10px] uppercase font-bold px-1.5 sm:px-2 py-0.5 rounded border border-[#3f3f46]">
                                    <?= $code ?>
                                </span>
                            </div>

                            <!-- Carousel Navigation Arrows (< & >) -->
                            <?php if ($hasMultiple): ?>
                                <button type="button" onclick="prevCardSlider('card_<?= $idx ?>', event)" class="absolute left-1 sm:left-2 top-1/2 -translate-y-1/2 w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-black/80 hover:bg-amber-500 hover:text-black text-white flex items-center justify-center transition border border-[#3f3f46] z-20 opacity-0 group-hover/cardPhoto:opacity-100 shadow-md cursor-pointer text-[10px] sm:text-xs font-bold" title="Foto Sebelumnya">
                                    ❮
                                </button>
                                <button type="button" onclick="nextCardSlider('card_<?= $idx ?>', event)" class="absolute right-1 sm:right-2 top-1/2 -translate-y-1/2 w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-black/80 hover:bg-amber-500 hover:text-black text-white flex items-center justify-center transition border border-[#3f3f46] z-20 opacity-0 group-hover/cardPhoto:opacity-100 shadow-md cursor-pointer text-[10px] sm:text-xs font-bold" title="Foto Selanjutnya">
                                    ❯
                                </button>

                                <!-- Bottom Dots Indicator -->
                                <div class="absolute bottom-1.5 sm:bottom-2 inset-x-0 flex items-center justify-center gap-1 sm:gap-1.5 z-20 pointer-events-auto">
                                    <?php foreach ($allImages as $dIdx => $dImg): ?>
                                        <button type="button" onclick="setCardSliderIdx('card_<?= $idx ?>', <?= $dIdx ?>, event)" class="slider-dot-card_<?= $idx ?> card-dot-<?= $idx ?> h-1 sm:h-1.5 rounded-full transition-all duration-300 <?= $dIdx === 0 ? 'w-4 sm:w-5 bg-amber-500' : 'w-1 sm:w-1.5 bg-white/60 hover:bg-white' ?> cursor-pointer" aria-label="Foto <?= $dIdx + 1 ?>"></button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Title & Category -->
                        <div class="space-y-0.5 sm:space-y-1">
                            <span class="font-tech text-[9px] sm:text-[10px] uppercase tracking-wider text-amber-400 font-bold block truncate">
                                <?= $subCat ?: $cat ?>
                            </span>
                            <h3 class="font-distro font-black text-base sm:text-2xl text-white uppercase group-hover:text-amber-400 transition leading-tight line-clamp-2">
                                <?= $name ?>
                            </h3>
                        </div>

                        <!-- Price Tag -->
                        <div class="flex items-baseline gap-1.5 sm:gap-2 pt-0.5">
                            <span class="font-tech text-sm sm:text-xl font-black text-amber-400"><?= rupiah($price) ?></span>
                            <span class="font-tech text-[10px] sm:text-xs text-[#71717a] line-through font-bold"><?= rupiah($normalPrice) ?></span>
                        </div>

                        <!-- Size Pills -->
                        <div class="space-y-0.5 sm:space-y-1 pt-0.5 sm:pt-1">
                            <span class="text-[9px] sm:text-[10px] font-tech text-[#a1a1aa] uppercase block">Ukuran:</span>
                            <div class="flex flex-wrap gap-1">
                                <?php foreach ($sizes as $s): ?>
                                    <span class="px-1.5 sm:px-2 py-0.5 bg-[#27272a] text-white text-[9px] sm:text-[11px] font-tech font-bold rounded border border-[#3f3f46]">
                                        <?= $s ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Short Description (Desktop) -->
                        <p class="text-[10px] sm:text-xs text-[#a1a1aa] leading-relaxed line-clamp-2 pt-0.5 sm:pt-1 hidden sm:block">
                            <?= $desc ?>
                        </p>

                    </div>

                    <!-- Card Actions -->
                    <div class="pt-2.5 sm:pt-4 mt-2.5 sm:mt-4 border-t border-[#27272a] flex flex-col sm:flex-row items-stretch sm:items-center gap-1.5 sm:gap-2">
                        <button onclick="openDistroModal(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES, 'UTF-8') ?>)" class="flex-1 py-1.5 sm:py-2.5 px-2 sm:px-3 bg-[#27272a] hover:bg-[#3f3f46] text-white text-[10px] sm:text-xs font-distro font-bold uppercase tracking-wider rounded-lg sm:rounded-xl transition text-center flex items-center justify-center gap-1">
                            <i data-lucide="info" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-400"></i>
                            <span class="sm:hidden">Spek</span>
                            <span class="hidden sm:inline">Detail Spek</span>
                        </button>

                        <?php 
                        $waText = urlencode("Bro Admin Hiragana, mau order *{$name}* ({$code}) harga promo " . rupiah($price) . ". Ukuran ready apa aja?");
                        ?>
                        <a href="https://wa.me/<?= $brand['whatsapp'] ?>?text=<?= $waText ?>" target="_blank" class="flex-1 py-1.5 sm:py-2.5 px-2 sm:px-3 bg-amber-500 hover:bg-amber-400 text-black text-[10px] sm:text-xs font-distro font-black uppercase tracking-wider rounded-lg sm:rounded-xl transition text-center flex items-center justify-center gap-1 shadow-md">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                            <span>Beli WA</span>
                        </a>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <!-- Empty Filter State -->
            <div id="noDistroResults" class="hidden text-center py-16 bg-[#18181b] border border-[#27272a] rounded-2xl space-y-2">
                <span class="text-3xl">🔍</span>
                <h3 class="font-distro font-bold text-xl text-white uppercase">Seri Sandal Tidak Ditemukan</h3>
                <p class="text-xs font-tech text-[#a1a1aa]">Coba cari dengan kata kunci lain seperti "Toshima", "Osaka", atau "Safety".</p>
            </div>

        </div>
    </section>

    <!-- SIZE CHART COWOK (STANDAR INSOLE LOKAL PRIA 39 - 44) -->
    <section id="size-chart" class="py-20 border-b border-[#27272a]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <div class="text-center space-y-2">
                <div class="inline-flex items-center gap-2">
                    <span class="w-6 h-[2px] bg-amber-500"></span>
                    <span class="font-tech text-xs uppercase tracking-widest text-amber-400 font-bold">STANDAR UKURAN KAKI COWOK</span>
                    <span class="w-6 h-[2px] bg-amber-500"></span>
                </div>
                <h2 class="font-distro font-black text-3xl sm:text-5xl text-white uppercase tracking-tight">
                    PANDUAN SIZE CHART (TRUE TO SIZE)
                </h2>
                <p class="text-xs sm:text-sm text-[#a1a1aa] max-w-xl mx-auto">
                    Hiragana Footwear menggunakan ukuran standar cowok Indonesia. Jangan sampai salah ukuran, ukur panjang kaki dari tumit ke ujung jempol pakai penggaris:
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                
                <!-- Tips Pengukuran -->
                <div class="md:col-span-5 bg-[#18181b] p-6 sm:p-7 rounded-2xl border border-[#27272a] space-y-4">
                    <h3 class="font-distro font-bold text-xl text-white uppercase flex items-center gap-2">
                        <i data-lucide="ruler" class="w-5 h-5 text-amber-400"></i>
                        <span>Cara Ukur Kaki Cowok:</span>
                    </h3>
                    <ol class="text-xs text-[#a1a1aa] space-y-2.5 list-decimal list-inside leading-relaxed">
                        <li>Pijakkan tumit rapat ke tembok di atas kertas putih.</li>
                        <li>Garis ujung jari kaki terpanjang Anda dengan pensil.</li>
                        <li>Ukur panjang dari tembok ke garis tadi (cm).</li>
                        <li>Cocokkan dengan tabel insole di sebelah kanan.</li>
                    </ol>
                    <div class="p-3.5 bg-amber-500/10 border border-amber-500/30 rounded-xl text-xs text-amber-300 font-bold leading-relaxed">
                        ⚠️ <strong>TIPS COWOK:</strong> Kalo punggung kakimu cenderung tebal atau lebar ke samping, sangat disarankan pilih <strong>1 size lebih besar (upsize)</strong> biar makin longgar dan leluasa.
                    </div>
                </div>

                <!-- Tabel Insole -->
                <div class="md:col-span-7 bg-[#18181b] rounded-2xl border border-[#27272a] overflow-hidden">
                    <table class="w-full text-left font-tech text-xs">
                        <thead class="bg-[#27272a] text-white uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="p-3.5 font-bold">Ukuran (Size)</th>
                                <th class="p-3.5 font-bold">Panjang Insole</th>
                                <th class="p-3.5 font-bold">Panjang Kaki Cowok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#27272a] text-[#fafafa]">
                            <tr class="hover:bg-[#27272a]/50 transition"><td class="p-3.5 font-bold text-amber-400">Size 39</td><td class="p-3.5">26.0 cm</td><td class="p-3.5 text-[#a1a1aa]">± 25.0 cm</td></tr>
                            <tr class="hover:bg-[#27272a]/50 transition"><td class="p-3.5 font-bold text-amber-400">Size 40</td><td class="p-3.5">26.5 cm</td><td class="p-3.5 text-[#a1a1aa]">± 25.5 cm</td></tr>
                            <tr class="hover:bg-[#27272a]/50 transition"><td class="p-3.5 font-bold text-amber-400">Size 41</td><td class="p-3.5">27.0 cm</td><td class="p-3.5 text-[#a1a1aa]">± 26.0 cm</td></tr>
                            <tr class="hover:bg-[#27272a]/50 transition"><td class="p-3.5 font-bold text-amber-400">Size 42</td><td class="p-3.5">27.5 cm</td><td class="p-3.5 text-[#a1a1aa]">± 26.5 cm</td></tr>
                            <tr class="hover:bg-[#27272a]/50 transition"><td class="p-3.5 font-bold text-amber-400">Size 43</td><td class="p-3.5">28.0 cm</td><td class="p-3.5 text-[#a1a1aa]">± 27.0 cm</td></tr>
                            <tr class="hover:bg-[#27272a]/50 transition"><td class="p-3.5 font-bold text-amber-400">Size 44</td><td class="p-3.5">29.0 cm</td><td class="p-3.5 text-[#a1a1aa]">± 28.0 cm</td></tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </section>

    <!-- RESELLER & GROSIR DISTRO (B2B) -->
    <?php if (!empty($keunggulan['eceran_grosir'])): ?>
    <section id="grosir" class="py-12 sm:py-20 bg-[#121214] border-b border-[#27272a]">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-8 sm:space-y-12">
            
            <div class="max-w-2xl space-y-2">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-[2px] bg-amber-500"></span>
                    <span class="font-tech text-xs uppercase tracking-widest text-amber-400 font-bold">PELUANG BISNIS DISTRO</span>
                </div>
                <h2 class="font-distro font-black text-2xl sm:text-5xl text-white uppercase tracking-tight">
                    KERJASAMA GROSIR & RESELLER TOKO SEPATU
                </h2>
                <p class="text-xs sm:text-sm text-[#a1a1aa] leading-relaxed">
                    Punya toko distro pakaian pria, toko sepatu, atau mau jualan online? Kami sediakan harga grosir langsung dari pabrik pengrajin di Bekasi untuk dijual kembali dengan margin tinggi.
                </p>
            </div>

            <?php if (!empty($keunggulan['faktur_pajak'])): ?>
                <div class="p-3.5 sm:p-4 bg-amber-500/10 border border-amber-500/30 rounded-xl sm:rounded-2xl flex items-center gap-3 text-xs font-tech text-amber-300">
                    <i data-lucide="file-check-2" class="w-5 h-5 text-amber-400 shrink-0"></i>
                    <span><strong>Faktur Pajak & SPH Resmi Siap:</strong> Kami melayani tender dan pengadaan resmi perusahaan, kantor, serta instansi B2B dengan faktur legal.</span>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-6">
                
                <div class="bg-[#18181b] p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-[#27272a] hover:border-amber-500 transition space-y-2 flex flex-col justify-between">
                    <div class="space-y-1.5 sm:space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xl sm:text-2xl font-tech text-amber-400 font-black">35% - 45%</span>
                            <span class="text-[9px] sm:text-[10px] font-tech text-amber-400 bg-amber-500/10 border border-amber-500/30 px-2 py-0.5 rounded uppercase font-bold tracking-wider">LABA BERSIH</span>
                        </div>
                        <h3 class="font-distro font-bold text-base sm:text-xl text-white uppercase leading-snug">Margin Keuntungan Bersih</h3>
                        <p class="text-xs text-[#a1a1aa] leading-relaxed">Selisih laba tebal per pasang dengan skema potongan harga bertingkat mulai dari order 1 kodi (20 pasang).</p>
                    </div>
                </div>

                <div class="bg-[#18181b] p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-[#27272a] hover:border-amber-500 transition space-y-2 flex flex-col justify-between">
                    <div class="space-y-1.5 sm:space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xl sm:text-2xl font-tech text-amber-400 font-black">FOTO & VIDEO HD</span>
                            <span class="text-[9px] sm:text-[10px] font-tech text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded uppercase font-bold tracking-wider">SIAP JUAL</span>
                        </div>
                        <h3 class="font-distro font-bold text-base sm:text-xl text-white uppercase leading-snug">Aset Promosi Siap Posting</h3>
                        <p class="text-xs text-[#a1a1aa] leading-relaxed">Disediakan foto produk studio, video unboxing, dan banner promosi tanpa watermark siap pakai jualan di IG/TikTok.</p>
                    </div>
                </div>

                <div class="bg-[#18181b] p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-[#27272a] hover:border-amber-500 transition space-y-2 flex flex-col justify-between">
                    <div class="space-y-1.5 sm:space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xl sm:text-2xl font-tech text-amber-400 font-black">KARGO MURAH</span>
                            <span class="text-[9px] sm:text-[10px] font-tech text-blue-400 bg-blue-500/10 border border-blue-500/30 px-2 py-0.5 rounded uppercase font-bold tracking-wider">HEMAT ONGKIR</span>
                        </div>
                        <h3 class="font-distro font-bold text-base sm:text-xl text-white uppercase leading-snug">Kirim Kargo Seluruh Kota</h3>
                        <p class="text-xs text-[#a1a1aa] leading-relaxed">Ekspedisi kargo hemat (Indah Kargo, J&T Kargo, Dakota) langsung dikirim dari workshop Bekasi ke kotamu.</p>
                    </div>
                </div>

            </div>

            <div class="pt-2">
                <a href="https://wa.me/<?= $brand['whatsapp'] ?>?text=Halo%20Admin%20Hiragana,%20saya%20tertarik%20ambil%20grosir%20untuk%20toko/distro%20saya" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 sm:px-8 py-3.5 sm:py-4 bg-amber-500 hover:bg-amber-400 text-black font-distro font-black text-xs sm:text-base uppercase tracking-wider rounded-xl transition shadow-lg shadow-amber-500/20 text-center">
                    <i data-lucide="message-square" class="w-4 h-4 sm:w-5 sm:h-5 shrink-0"></i>
                    <span>TANYA PRICE LIST GROSIR VIA WHATSAPP</span>
                </a>
            </div>

        </div>
    </section>
    <?php endif; ?>

    <!-- TESTIMONI PEMBELI COWOK -->
    <section id="testimoni" class="py-20 border-b border-[#27272a]">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-6 sm:space-y-10">
            
            <div class="space-y-1">
                <span class="font-tech text-xs uppercase tracking-widest text-amber-400 font-bold">ULASAN TERVERIFIKASI</span>
                <h2 class="font-distro font-black text-3xl sm:text-4xl text-white uppercase tracking-tight">
                    KATA PARA COWOK YANG UDAH PAKAI
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-[#18181b] p-6 rounded-2xl border border-[#27272a] space-y-3 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex text-amber-400 text-sm">★★★★★</div>
                        <p class="text-xs text-[#d4d4d8] leading-relaxed">
                            "Seri Toshima mantul abis bro! Busa phylonnya beneran tebel dan empuk, ga gampang kempes. Buat nongkrong dan dipake harian enak banget, jepitannya ga bikin sela jari merah."
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#27272a] flex items-center justify-between text-xs font-tech text-[#a1a1aa]">
                        <span class="font-bold text-white">Rizky • Jakarta</span>
                        <span class="text-amber-400">Toshima Black</span>
                    </div>
                </div>

                <div class="bg-[#18181b] p-6 rounded-2xl border border-[#27272a] space-y-3 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex text-amber-400 text-sm">★★★★★</div>
                        <p class="text-xs text-[#d4d4d8] leading-relaxed">
                            "Gue ambil yang model slop Osaka. Kulitnya doff macho banget, dalemnya ada busa alus jadi ga bikin lecet punggung kaki. Sol bawah tebel ga licin pas ubin basah."
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#27272a] flex items-center justify-between text-xs font-tech text-[#a1a1aa]">
                        <span class="font-bold text-white">Dimas • Bandung</span>
                        <span class="text-amber-400">Osaka Slop</span>
                    </div>
                </div>

                <div class="bg-[#18181b] p-6 rounded-2xl border border-[#27272a] space-y-3 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex text-amber-400 text-sm">★★★★★</div>
                        <p class="text-xs text-[#d4d4d8] leading-relaxed">
                            "Sepatu safety low-cut nya kokoh buat kerja di bengkel bubut. Besi depannya tebel tapi sepatunya ga kaku pas jalan. Pengiriman cepet, bisa COD lagi."
                        </p>
                    </div>
                    <div class="pt-3 border-t border-[#27272a] flex items-center justify-between text-xs font-tech text-[#a1a1aa]">
                        <span class="font-bold text-white">Hendra • Cikarang</span>
                        <span class="text-amber-400">Safety Low-Cut</span>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- FOOTER MASKULIN (Ringkas & Proporsional di Mobile) -->
    <footer class="bg-[#09090b] text-[#a1a1aa] py-8 sm:py-16 text-xs border-t border-[#27272a]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-12">
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5 sm:gap-8">
                
                <div class="md:col-span-5 space-y-2 sm:space-y-3">
                    <div class="inline-flex items-center gap-2.5 sm:gap-3">
                        <?php if (!empty($brand['logo_url'])): ?>
                            <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg sm:rounded-xl bg-white p-0.5 sm:p-1 flex items-center justify-center shrink-0 border border-slate-200/80 shadow-sm overflow-hidden">
                                <img src="<?= htmlspecialchars($brand['logo_url']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>" class="max-h-full max-w-full object-contain">
                            </div>
                        <?php else: ?>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-amber-500 text-black flex items-center justify-center font-distro font-black text-base sm:text-xl rounded-lg sm:rounded-xl shadow-sm">
                                HF
                            </div>
                        <?php endif; ?>
                        <div>
                            <span class="font-distro font-black text-lg sm:text-2xl text-white tracking-wider block leading-none"><?= htmlspecialchars($brand['name']) ?><span class="text-amber-500">.</span></span>
                            <span class="text-[8px] sm:text-[9px] font-tech font-bold uppercase tracking-[0.14em] sm:tracking-[0.16em] text-[#a1a1aa] block pt-0.5">MEN'S CASUAL FOOTWEAR</span>
                        </div>
                    </div>
                    <p class="text-[11px] sm:text-xs text-[#a1a1aa] leading-relaxed max-w-sm">
                        Alas kaki distro & kasual pria buatan tangan pengrajin lokal Bekasi. Kuat di jalan, empuk di kaki.
                    </p>
                    <p class="text-[10px] sm:text-[11px] font-tech text-[#71717a]">
                        📍 <?= htmlspecialchars($brand['location']) ?>
                    </p>
                </div>

                <div class="md:col-span-3 space-y-1.5 sm:space-y-2 font-distro uppercase tracking-wider text-xs sm:text-sm">
                    <h4 class="font-bold text-white text-xs sm:text-sm">Koleksi Produk</h4>
                    <ul class="grid grid-cols-2 md:grid-cols-1 gap-x-2 gap-y-1 md:space-y-1.5 text-[11px] sm:text-xs font-normal text-[#a1a1aa]">
                        <li><a href="#katalog" class="hover:text-amber-400 transition truncate block">Sandal Jepit</a></li>
                        <li><a href="#katalog" class="hover:text-amber-400 transition truncate block">Sandal Slop & Kokop</a></li>
                        <li><a href="#katalog" class="hover:text-amber-400 transition truncate block">Sepatu Safety Kerja</a></li>
                        <li><a href="#katalog" class="hover:text-amber-400 transition truncate block">Sneakers Kasual</a></li>
                        <li><a href="#size-chart" class="hover:text-amber-400 transition truncate block">Panduan Ukuran</a></li>
                    </ul>
                </div>

                <div class="md:col-span-4 space-y-2 sm:space-y-3 font-tech text-xs">
                    <h4 class="font-distro font-bold text-white text-xs sm:text-base uppercase tracking-wider">Kontak & Pemesanan</h4>
                    <p class="text-[#71717a] text-[10px] sm:text-xs">Jam Kerja: <?= htmlspecialchars($brand['cs_hours']) ?></p>
                    <div class="pt-0.5 space-y-1.5 sm:space-y-2">
                        <a href="https://wa.me/<?= $brand['whatsapp'] ?>" target="_blank" class="flex items-center gap-2 text-white font-bold hover:text-amber-400 transition text-xs sm:text-sm">
                            <i data-lucide="phone" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-500"></i>
                            <span><?= htmlspecialchars($brand['whatsapp_display']) ?></span>
                        </a>
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 pt-1 text-[9px] sm:text-[10px] font-distro uppercase tracking-wider font-bold">
                            <a href="<?= $brand['shopee_url'] ?>" target="_blank" class="px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg bg-[#18181b] border border-[#27272a] text-orange-400 hover:border-orange-500 transition">Shopee Official</a>
                            <a href="<?= $brand['tokopedia_url'] ?>" target="_blank" class="px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg bg-[#18181b] border border-[#27272a] text-emerald-400 hover:border-emerald-500 transition">Tokopedia Store</a>
                            <a href="<?= $brand['tiktok_url'] ?>" target="_blank" class="px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg bg-[#18181b] border border-[#27272a] text-sky-400 hover:border-sky-500 transition">TikTok Shop</a>
                            <a href="https://instagram.com/<?= $brand['instagram'] ?>" target="_blank" class="px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg bg-[#18181b] border border-[#27272a] text-pink-400 hover:border-pink-500 transition">Instagram</a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="pt-5 sm:pt-8 border-t border-[#27272a] flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 text-[10px] sm:text-[11px] font-tech text-[#71717a]">
                <p>&copy; <?= date('Y') ?> HIRAGANA FOOTWEAR. All rights reserved.</p>
                <div class="flex items-center gap-4 sm:gap-5">
                    <a href="#katalog" class="hover:text-white transition">Katalog</a>
                    <a href="#size-chart" class="hover:text-white transition">Size Chart</a>
                    <a href="#grosir" class="hover:text-white transition">Grosir</a>
                    <span class="text-[#3f3f46]">•</span>
                    <a href="/admin/login.php" class="text-amber-400 hover:text-white font-bold transition flex items-center gap-1">
                        <i data-lucide="lock" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
                        <span>Admin Portal</span>
                    </a>
                </div>
            </div>

        </div>
    </footer>

    <!-- INTERACTIVE DISTRO DETAIL MODAL -->
    <div id="distroModal" class="fixed inset-0 bg-black/80 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-[#18181b] rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border-2 border-[#27272a] space-y-5 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-start justify-between gap-4 pb-4 border-b border-[#27272a]">
                <div>
                    <span id="modalDistroCode" class="font-tech text-xs text-amber-400 font-bold uppercase"></span>
                    <h3 id="modalDistroName" class="font-distro font-black text-3xl text-white uppercase mt-0.5"></h3>
                    <div class="flex items-baseline gap-2 pt-1">
                        <span id="modalDistroPrice" class="font-tech text-2xl font-black text-amber-400"></span>
                        <span id="modalDistroStrike" class="font-tech text-xs text-[#71717a] line-through font-bold"></span>
                    </div>
                </div>
                <button onclick="closeDistroModal()" class="w-8 h-8 rounded-full bg-[#27272a] hover:bg-[#3f3f46] text-white flex items-center justify-center transition">✕</button>
            </div>

            <!-- Product Visual in Modal with Arrows & Counter -->
            <div class="space-y-3">
                <div class="relative aspect-[16/9] rounded-2xl overflow-hidden bg-[#121214] border-2 border-[#27272a] group/modalImg">
                    <img id="modalDistroImg" src="" alt="Foto Produk" class="w-full h-full object-cover transition-opacity duration-300">
                    
                    <!-- Prev / Next Arrows -->
                    <button id="modalPrevBtn" type="button" onclick="slideModalPhoto(-1)" class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/80 hover:bg-amber-500 hover:text-black text-white flex items-center justify-center transition border border-[#3f3f46] z-20 shadow-xl cursor-pointer font-bold text-sm" title="Foto Sebelumnya">❮</button>
                    <button id="modalNextBtn" type="button" onclick="slideModalPhoto(1)" class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/80 hover:bg-amber-500 hover:text-black text-white flex items-center justify-center transition border border-[#3f3f46] z-20 shadow-xl cursor-pointer font-bold text-sm" title="Foto Selanjutnya">❯</button>
                    
                    <!-- Counter on Modal Image -->
                    <span id="modalPhotoCounter" class="absolute top-3 right-3 px-2.5 py-1 bg-black/85 backdrop-blur-xs text-amber-400 text-[11px] font-tech font-bold rounded-lg border border-[#3f3f46]">
                        1 / 3
                    </span>
                </div>

                <!-- 3 Mini Thumbnails Strip (Like StoreOnline Pro) -->
                <div id="modalThumbStrip" class="flex items-center justify-center gap-3 pt-0.5">
                    <!-- Generated via JavaScript -->
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <span class="font-tech text-[10px] uppercase text-[#a1a1aa] font-bold block">Rincian & Ketahanan:</span>
                <p id="modalDistroDesc" class="text-xs text-[#d4d4d8] leading-relaxed"></p>
            </div>

            <!-- Technical Specs Grid -->
            <div class="space-y-2 pt-1">
                <span class="font-tech text-[10px] uppercase text-[#a1a1aa] font-bold block">Spesifikasi Material:</span>
                <div id="modalDistroSpecs" class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-tech"></div>
            </div>

            <!-- Size Selector -->
            <div class="space-y-2 pt-1">
                <span class="font-tech text-[10px] uppercase text-[#a1a1aa] font-bold block">Pilih Ukuran Kaki (Size):</span>
                <div id="modalDistroSizes" class="flex flex-wrap gap-1.5"></div>
            </div>

            <!-- Order Buttons -->
            <div class="pt-4 border-t border-[#27272a] flex flex-col sm:flex-row gap-2.5">
                <a id="modalDistroWa" href="#" target="_blank" class="flex-1 py-3 px-5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black font-distro font-black text-base uppercase tracking-wider text-center flex items-center justify-center gap-2 transition shadow-lg shadow-amber-500/20">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    <span>Order via WhatsApp</span>
                </a>
                <button onclick="closeDistroModal()" class="py-3 px-5 rounded-xl bg-[#27272a] hover:bg-[#3f3f46] text-white font-distro font-bold text-sm uppercase tracking-wider transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        function normalizeClientUrl(url) {
            if (!url) return '';
            const match = url.match(/drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?(?:export=view&)?id=)([a-zA-Z0-9_-]+)/);
            if (match && match[1]) {
                return 'https://lh3.googleusercontent.com/d/' + match[1];
            }
            return url;
        }

        // ==========================================
        // AUTO-SLIDING ENGINE (3.5s + Pause on Hover)
        // Supports Hero Featured Card & Catalog Cards
        // ==========================================
        const autoSliders = {};

        function initAutoSliders() {
            document.querySelectorAll('[data-slider-id]').forEach(el => {
                const sliderId = el.getAttribute('data-slider-id');
                const rawJson = el.getAttribute('data-images');
                if (!sliderId || !rawJson) return;

                try {
                    const rawImgs = JSON.parse(rawJson);
                    const images = rawImgs.map(normalizeClientUrl).filter(Boolean);
                    if (images.length <= 1) return; // No auto-slide needed if only 1 image

                    // If existing slider had a timer, clear it
                    if (autoSliders[sliderId] && autoSliders[sliderId].timer) {
                        clearInterval(autoSliders[sliderId].timer);
                    }

                    autoSliders[sliderId] = {
                        id: sliderId,
                        images: images,
                        active: 0,
                        timer: null,
                        isHovered: false,
                        intervalMs: 3000
                    };

                    // Pause auto-sliding on hover, resume on mouse leave
                    el.addEventListener('mouseenter', () => pauseSlider(sliderId));
                    el.addEventListener('mouseleave', () => resumeSlider(sliderId));

                    // Start automatic rotation
                    startSliderTimer(sliderId);
                } catch (e) {
                    console.error('Slider parse error for ' + sliderId, e);
                }
            });
        }

        function startSliderTimer(sliderId) {
            const s = autoSliders[sliderId];
            if (!s || s.timer || s.images.length <= 1) return;
            s.timer = setInterval(() => {
                if (!s.isHovered) {
                    nextCardSlider(sliderId, null, true);
                }
            }, s.intervalMs);
        }

        function pauseSlider(sliderId) {
            const s = autoSliders[sliderId];
            if (!s) return;
            s.isHovered = true;
            if (s.timer) {
                clearInterval(s.timer);
                s.timer = null;
            }
        }

        function resumeSlider(sliderId) {
            const s = autoSliders[sliderId];
            if (!s) return;
            s.isHovered = false;
            if (!s.timer) {
                startSliderTimer(sliderId);
            }
        }

        function restartSliderTimer(sliderId) {
            const s = autoSliders[sliderId];
            if (!s) return;
            if (s.timer) {
                clearInterval(s.timer);
                s.timer = null;
            }
            if (!s.isHovered) {
                startSliderTimer(sliderId);
            }
        }

        function nextCardSlider(sliderId, e, automated = false) {
            if (e) e.stopPropagation();
            const s = autoSliders[sliderId];
            if (!s || s.images.length <= 1) return;
            s.active = (s.active + 1) % s.images.length;
            renderSliderUI(sliderId);
            if (!automated) restartSliderTimer(sliderId);
        }

        function prevCardSlider(sliderId, e) {
            if (e) e.stopPropagation();
            const s = autoSliders[sliderId];
            if (!s || s.images.length <= 1) return;
            s.active = (s.active - 1 + s.images.length) % s.images.length;
            renderSliderUI(sliderId);
            restartSliderTimer(sliderId);
        }

        function setCardSliderIdx(sliderId, idx, e) {
            if (e) e.stopPropagation();
            const s = autoSliders[sliderId];
            if (!s || !s.images[idx]) return;
            s.active = idx;
            renderSliderUI(sliderId);
            restartSliderTimer(sliderId);
        }

        function renderSliderUI(sliderId) {
            const s = autoSliders[sliderId];
            if (!s) return;

            // Target image element (supports slider_<id>_img or cardImg_<idx>)
            const imgEl = document.getElementById('slider_' + sliderId + '_img') || 
                          document.getElementById('cardImg_' + sliderId.replace('card_', ''));
            const counterEl = document.getElementById('slider_' + sliderId + '_counter') || 
                              document.getElementById('cardCounter_' + sliderId.replace('card_', ''));

            if (imgEl) {
                imgEl.style.opacity = '0.6';
                setTimeout(() => {
                    imgEl.src = s.images[s.active];
                    imgEl.style.opacity = '1';
                }, 80);
            }

            if (counterEl) {
                counterEl.innerText = (s.active + 1) + '/' + s.images.length;
            }

            // Update dots
            const dots = document.querySelectorAll('.slider-dot-' + sliderId + ', .card-dot-' + sliderId.replace('card_', ''));
            dots.forEach((dot, dIdx) => {
                if (dIdx === s.active) {
                    dot.classList.remove('w-1.5', 'bg-white/60');
                    dot.classList.add('w-5', 'bg-amber-500');
                } else {
                    dot.classList.remove('w-5', 'bg-amber-500');
                    dot.classList.add('w-1.5', 'bg-white/60');
                }
            });
        }

        // Backward compatibility
        function slideCardPhoto(cardIdx, dir, e) {
            if (dir > 0) nextCardSlider('card_' + cardIdx, e);
            else prevCardSlider('card_' + cardIdx, e);
        }
        function setCardPhoto(cardIdx, photoIdx, e) {
            setCardSliderIdx('card_' + cardIdx, photoIdx, e);
        }
        function applyCardPhoto(cardIdx) {
            renderSliderUI('card_' + cardIdx);
        }

        // ==========================================
        // DARK / LIGHT THEME CONTROLLER
        // ==========================================
        function getSavedTheme() {
            try {
                return localStorage.getItem('hiragana_theme') || 'dark';
            } catch(e) {
                return 'dark';
            }
        }

        function applyTheme(theme) {
            const htmlEl = document.documentElement;
            const themeIcon = document.getElementById('themeIcon');
            const themeText = document.getElementById('themeText');
            const mobileLabel = document.getElementById('mobileThemeLabel');

            if (theme === 'light') {
                htmlEl.classList.add('light-mode');
                if (themeIcon) themeIcon.setAttribute('data-lucide', 'moon');
                if (themeText) themeText.innerText = 'DARK';
                if (mobileLabel) mobileLabel.innerText = 'LIGHT (AKTIF)';
            } else {
                htmlEl.classList.remove('light-mode');
                if (themeIcon) themeIcon.setAttribute('data-lucide', 'sun');
                if (themeText) themeText.innerText = 'LIGHT';
                if (mobileLabel) mobileLabel.innerText = 'DARK (AKTIF)';
            }

            if (window.lucide) lucide.createIcons();
            try {
                localStorage.setItem('hiragana_theme', theme);
            } catch(e) {}
        }

        function toggleTheme() {
            const current = document.documentElement.classList.contains('light-mode') ? 'light' : 'dark';
            const next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
        }

        function startApp() {
            applyTheme(getSavedTheme());
            if (window.lucide) lucide.createIcons();
            initAutoSliders();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startApp);
        } else {
            startApp();
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        // Filter Logic
        let currentDistroCat = 'ALL';
        function setDistroFilter(cat) {
            currentDistroCat = cat;

            document.querySelectorAll('.distro-btn').forEach(btn => {
                btn.classList.remove('bg-amber-500', 'text-black');
                btn.classList.add('bg-[#27272a]', 'text-[#a1a1aa]');
            });
            const target = event.currentTarget || (event.target ? event.target.closest('.distro-btn') : null) || event.target;
            if (target) {
                target.classList.remove('bg-[#27272a]', 'text-[#a1a1aa]');
                target.classList.add('bg-amber-500', 'text-black');
                if (typeof target.scrollIntoView === 'function') {
                    target.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            }

            filterDistro();
        }

        function filterDistro() {
            const query = (document.getElementById('distroSearch').value || '').toLowerCase().trim();
            const cards = document.querySelectorAll('.distro-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                const cardName = card.getAttribute('data-name');

                const catMatch = (currentDistroCat === 'ALL' || cardCat === currentDistroCat);
                const queryMatch = query === '' || cardName.includes(query);

                if (catMatch && queryMatch) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('noDistroResults').classList.toggle('hidden', visibleCount > 0);
        }

        // Modal Logic (3 Photos Strip & Controls)
        let activeDistroItem = null;
        let selectedDistroSize = '41';
        let modalPhotos = [];
        let modalActiveIndex = 0;

        function openDistroModal(prod) {
            activeDistroItem = prod;
            const price = parseInt(prod.price) || 145000;
            const normal = parseInt(prod.normal_price) || (price + 80000);

            // Multi-photo initialization in modal
            let rawImgs = prod.images && Array.isArray(prod.images) && prod.images.length > 0 
                ? prod.images 
                : (prod.image_url ? [prod.image_url] : []);
            modalPhotos = rawImgs.map(normalizeClientUrl);
            modalActiveIndex = 0;
            updateModalPhotosUI();

            document.getElementById('modalDistroCode').innerText = (prod.code || 'HF-SERIES') + ' • ' + (prod.sub_category || prod.category);
            document.getElementById('modalDistroName').innerText = prod.name;
            document.getElementById('modalDistroPrice').innerText = 'Rp ' + price.toLocaleString('id-ID');
            document.getElementById('modalDistroStrike').innerText = 'Rp ' + normal.toLocaleString('id-ID');
            document.getElementById('modalDistroDesc').innerText = prod.description || '';

            // Specs
            const specsContainer = document.getElementById('modalDistroSpecs');
            specsContainer.innerHTML = '';
            const specs = prod.specs || {};
            for (const [key, val] of Object.entries(specs)) {
                const item = document.createElement('div');
                item.className = 'p-2.5 bg-[#121214] rounded-lg border border-[#27272a]';
                item.innerHTML = `<span class="text-[#71717a] block text-[10px] uppercase font-bold">${key}</span><span class="text-white font-bold text-xs">${val}</span>`;
                specsContainer.appendChild(item);
            }

            // Sizes
            const sizesContainer = document.getElementById('modalDistroSizes');
            sizesContainer.innerHTML = '';
            const sizes = prod.sizes || [39, 40, 41, 42, 43];
            selectedDistroSize = sizes[0] || '40';

            sizes.forEach((s, idx) => {
                const btn = document.createElement('button');
                btn.className = `size-choice px-3 py-1.5 rounded-lg text-xs font-tech font-bold transition border ${idx === 0 ? 'bg-amber-500 text-black border-amber-500' : 'bg-[#121214] text-white border-[#27272a] hover:border-amber-500'}`;
                btn.innerText = 'Size ' + s;
                btn.onclick = () => {
                    document.querySelectorAll('.size-choice').forEach(b => {
                        b.classList.remove('bg-amber-500', 'text-black', 'border-amber-500');
                        b.classList.add('bg-[#121214]', 'text-white', 'border-[#27272a]');
                    });
                    btn.classList.remove('bg-[#121214]', 'text-white', 'border-[#27272a]');
                    btn.classList.add('bg-amber-500', 'text-black', 'border-amber-500');
                    selectedDistroSize = s;
                    updateDistroWaLink();
                };
                sizesContainer.appendChild(btn);
            });

            updateDistroWaLink();
            document.getElementById('distroModal').classList.remove('hidden');
        }

        function slideModalPhoto(dir) {
            if (modalPhotos.length <= 1) return;
            modalActiveIndex = (modalActiveIndex + dir + modalPhotos.length) % modalPhotos.length;
            updateModalPhotosUI();
        }

        function setModalPhoto(idx) {
            if (!modalPhotos[idx]) return;
            modalActiveIndex = idx;
            updateModalPhotosUI();
        }

        function updateModalPhotosUI() {
            const mainImg = document.getElementById('modalDistroImg');
            const counter = document.getElementById('modalPhotoCounter');
            const prevBtn = document.getElementById('modalPrevBtn');
            const nextBtn = document.getElementById('modalNextBtn');
            const strip = document.getElementById('modalThumbStrip');
            
            if (modalPhotos.length > 0) {
                mainImg.src = modalPhotos[modalActiveIndex];
                counter.innerText = (modalActiveIndex + 1) + ' / ' + modalPhotos.length;
                counter.classList.toggle('hidden', modalPhotos.length <= 1);
                prevBtn.classList.toggle('hidden', modalPhotos.length <= 1);
                nextBtn.classList.toggle('hidden', modalPhotos.length <= 1);
            } else {
                mainImg.src = '';
                counter.classList.add('hidden');
                prevBtn.classList.add('hidden');
                nextBtn.classList.add('hidden');
            }
            
            // Render 3 Mini Thumbnails Strip (Like StoreOnline Pro)
            strip.innerHTML = '';
            if (modalPhotos.length > 1) {
                modalPhotos.forEach((img, idx) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border-2 transition-all duration-200 cursor-pointer p-0.5 ${idx === modalActiveIndex ? 'border-amber-500 scale-105 shadow-md shadow-amber-500/20' : 'border-[#27272a] hover:border-[#3f3f46] opacity-60 hover:opacity-100'}`;
                    btn.innerHTML = `<img src="${img}" class="w-full h-full object-cover rounded-lg">`;
                    btn.onclick = () => setModalPhoto(idx);
                    strip.appendChild(btn);
                });
            }
        }

        function updateDistroWaLink() {
            if (!activeDistroItem) return;
            const price = parseInt(activeDistroItem.price) || 145000;
            const msg = `Bro Admin Hiragana Footwear, mau order *${activeDistroItem.name}* (${activeDistroItem.code || ''}), Ukuran: Size ${selectedDistroSize}, harga promo Rp ${price.toLocaleString('id-ID')}. Masih ready stok?`;
            document.getElementById('modalDistroWa').href = `https://wa.me/<?= $brand['whatsapp'] ?>?text=${encodeURIComponent(msg)}`;
        }

        function closeDistroModal() {
            document.getElementById('distroModal').classList.add('hidden');
            activeDistroItem = null;
        }

        window.addEventListener('click', (e) => {
            const m = document.getElementById('distroModal');
            if (e.target === m) closeDistroModal();
        });
    </script>
</body>
</html>
