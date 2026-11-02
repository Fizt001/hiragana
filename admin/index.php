<?php
session_start();

// Auth check
if (empty($_SESSION['hf_admin_logged_in'])) {
    header('Location: /admin/login.php');
    exit;
}

$settingsPath = __DIR__ . '/../data/settings.json';
$productsPath = __DIR__ . '/../data/products.json';

/**
 * Normalizes Google Drive, Dropbox, and external image URLs to direct high-speed image streams.
 */
function normalizeImageUrl(?string $url): string {
    if (empty($url)) {
        return '';
    }
    $url = trim($url);

    // Google Drive share/view link conversions:
    // https://drive.google.com/file/d/1X5abc12345/view?usp=sharing
    // https://drive.google.com/open?id=1X5abc12345
    // https://drive.google.com/uc?id=1X5abc12345
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

$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];
$catalog = file_exists($productsPath) ? json_decode(file_get_contents($productsPath), true) : [];
$products = $catalog['products'] ?? [];

$successMsg = '';
$errorMsg = '';
$activeTab = 'tab-general';

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

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. SAVE GENERAL SETTINGS & CONTACT
    if ($action === 'save_settings') {
        $activeTab = 'tab-general';
        $settings['brand_name'] = trim($_POST['brand_name'] ?? 'Hiragana Footwear');
        $settings['japanese_tagline'] = trim($_POST['japanese_tagline'] ?? '');
        $settings['headline'] = trim($_POST['headline'] ?? '');
        $settings['sub_headline'] = trim($_POST['sub_headline'] ?? '');
        $settings['location'] = trim($_POST['location'] ?? '');
        $settings['whatsapp'] = preg_replace('/[^0-9]/', '', $_POST['whatsapp'] ?? '');
        $settings['whatsapp_display'] = trim($_POST['whatsapp_display'] ?? '');
        $settings['cs_hours'] = trim($_POST['cs_hours'] ?? '');
        $settings['banner_announcement'] = trim($_POST['banner_announcement'] ?? '');
        $settings['rating'] = trim($_POST['rating'] ?? '4.9');
        $settings['total_sold'] = trim($_POST['total_sold'] ?? '10.000+');

        // Handle Logo Removal
        if (!empty($_POST['remove_logo'])) {
            $settings['logo_url'] = '';
        }
        // Handle File Upload
        elseif (!empty($_FILES['logo_file']['name']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['logo_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
            if (in_array($ext, $allowed, true)) {
                $uploadDir = __DIR__ . '/../uploads';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = 'logo_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
                    $settings['logo_url'] = '/uploads/' . $filename;
                }
            } else {
                $errorMsg = 'Format file logo harus berupa PNG, JPG, JPEG, WEBP, atau SVG.';
            }
        }
        // Handle URL Input if provided
        elseif (isset($_POST['logo_url']) && trim($_POST['logo_url']) !== '') {
            $settings['logo_url'] = normalizeImageUrl($_POST['logo_url']);
        }

        file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        if (empty($errorMsg)) {
            $successMsg = 'Informasi toko, logo & kontak berhasil diperbarui!';
        }
    }

    // 2. SAVE FEATURE FLAGS & KEUNGGULAN TOKO (SESUAI FOTO 2)
    elseif ($action === 'save_features') {
        $activeTab = 'tab-features';
        $settings['features'] = [
            'checkout_wa' => !empty($_POST['feature_checkout_wa']),
            'varian_produk' => !empty($_POST['feature_varian_produk']),
            'qris_payment' => !empty($_POST['feature_qris_payment']),
            'banner_promo' => !empty($_POST['feature_banner_promo']),
            'seo_google' => !empty($_POST['feature_seo_google']),
            'multi_outlet' => !empty($_POST['feature_multi_outlet']),
        ];

        $settings['keunggulan'] = [
            'eceran_grosir' => !empty($_POST['keunggulan_eceran_grosir']),
            'garansi_mutu' => !empty($_POST['keunggulan_garansi_mutu']),
            'siap_kirim_indonesia' => !empty($_POST['keunggulan_siap_kirim_indonesia']),
            'bisa_cod' => !empty($_POST['keunggulan_bisa_cod']),
            'garansi_tukar_size' => !empty($_POST['keunggulan_garansi_tukar_size']),
            'faktur_pajak' => !empty($_POST['keunggulan_faktur_pajak']),
        ];

        file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $features = $settings['features'];
        $keunggulan = $settings['keunggulan'];
        $successMsg = 'Pembaruan Limit, Fitur & Profil Toko berhasil disimpan!';
    }

    // 3. SAVE E-COMMERCE & SOCIAL MEDIA LINKS
    elseif ($action === 'save_socials') {
        $activeTab = 'tab-socials';
        $settings['shopee_url'] = trim($_POST['shopee_url'] ?? '');
        $settings['tokopedia_url'] = trim($_POST['tokopedia_url'] ?? '');
        $settings['instagram'] = trim($_POST['instagram'] ?? '');
        $settings['tiktok'] = trim($_POST['tiktok'] ?? '');

        file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $successMsg = 'Tautan e-commerce dan sosial media berhasil diperbarui!';
    }

    // 4. UPDATE PRODUCT (FOTO HINGGA 3 GAMBAR VIA UPLOAD / GOOGLE DRIVE & HARGA)
    elseif ($action === 'update_product' || $action === 'update_product_price') {
        $activeTab = 'tab-products';
        $prodIndex = (int) ($_POST['product_index'] ?? -1);

        if (isset($products[$prodIndex])) {
            // Update Price if supplied
            if (isset($_POST['price'])) {
                $newPrice = (int) preg_replace('/[^0-9]/', '', $_POST['price']);
                $products[$prodIndex]['price'] = $newPrice;
                if (!empty($products[$prodIndex]['variants'])) {
                    foreach ($products[$prodIndex]['variants'] as &$v) {
                        $v['price'] = $newPrice;
                    }
                }
            }

            // Existing images array or fallback to single image_url
            $currentImages = !empty($products[$prodIndex]['images']) && is_array($products[$prodIndex]['images'])
                ? $products[$prodIndex]['images']
                : (!empty($products[$prodIndex]['image_url']) ? [$products[$prodIndex]['image_url']] : []);

            $updatedImages = [];

            // Process 3 Image Slots
            for ($i = 1; $i <= 3; $i++) {
                $fileKey = "product_image_{$i}";
                $urlKey = "image_url_{$i}";
                $deleteKey = "delete_image_{$i}";

                // If user checked delete this image slot
                if (!empty($_POST[$deleteKey])) {
                    continue;
                }

                // A. Check File Upload for Slot $i
                if (!empty($_FILES[$fileKey]['name']) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES[$fileKey];
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
                    if (in_array($ext, $allowed, true)) {
                        $uploadDir = __DIR__ . '/../uploads/products';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        $filename = 'prod_' . ($prodIndex + 1) . '_slot' . $i . '_' . time() . '.' . $ext;
                        if (move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
                            $updatedImages[] = '/uploads/products/' . $filename;
                            continue;
                        }
                    } else {
                        $errorMsg = "Format file gambar slot {$i} harus PNG, JPG, JPEG, WEBP, atau SVG.";
                    }
                }

                // B. Check URL / Google Drive link for Slot $i
                if (isset($_POST[$urlKey]) && trim($_POST[$urlKey]) !== '') {
                    $norm = normalizeImageUrl(trim($_POST[$urlKey]));
                    if (!empty($norm)) {
                        $updatedImages[] = $norm;
                        continue;
                    }
                }

                // C. Fallback: Keep existing image if slot was already filled and not modified
                $slotIdx = $i - 1;
                if (isset($currentImages[$slotIdx]) && !empty($currentImages[$slotIdx])) {
                    $updatedImages[] = $currentImages[$slotIdx];
                }
            }

            // Legacy single inputs fallback
            if (empty($updatedImages)) {
                if (!empty($_FILES['product_image']['name']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['product_image'];
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
                    if (in_array($ext, $allowed, true)) {
                        $uploadDir = __DIR__ . '/../uploads/products';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                        $filename = 'prod_' . ($prodIndex + 1) . '_main_' . time() . '.' . $ext;
                        if (move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename)) {
                            $updatedImages[] = '/uploads/products/' . $filename;
                        }
                    }
                } elseif (isset($_POST['image_url']) && trim($_POST['image_url']) !== '') {
                    $updatedImages[] = normalizeImageUrl(trim($_POST['image_url']));
                }
            }

            // Save up to 3 images
            $finalImages = array_values(array_slice(array_unique(array_filter($updatedImages)), 0, 3));
            $products[$prodIndex]['images'] = $finalImages;
            $products[$prodIndex]['image_url'] = !empty($finalImages) ? $finalImages[0] : '';

            $catalog['products'] = $products;
            file_put_contents($productsPath, json_encode($catalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            if (empty($errorMsg)) {
                $imgCount = count($finalImages);
                $successMsg = 'Produk "' . htmlspecialchars($products[$prodIndex]['name']) . '" berhasil disimpan (' . $imgCount . ' foto aktif)!';
            }
        } else {
            $errorMsg = 'Produk tidak ditemukan.';
        }
    }

    // 5. CHANGE ADMIN PASSWORD
    elseif ($action === 'change_password') {
        $activeTab = 'tab-security';
        $oldPwd = $_POST['old_password'] ?? '';
        $newPwd = $_POST['new_password'] ?? '';
        $confirmPwd = $_POST['confirm_password'] ?? '';
        $currentHash = $settings['admin_password_hash'] ?? '$2y$12$nToZ6MSLD4/lnVjwyKKMteqZfY2gaUYoJLG9NuMpFIzKDz5FWBzgy';

        if (!password_verify($oldPwd, $currentHash)) {
            $errorMsg = 'Kata sandi lama yang Anda masukkan tidak sesuai.';
        } elseif (strlen($newPwd) < 6) {
            $errorMsg = 'Kata sandi baru minimal harus terdiri dari 6 karakter.';
        } elseif ($newPwd !== $confirmPwd) {
            $errorMsg = 'Konfirmasi kata sandi baru tidak cocok.';
        } else {
            $settings['admin_password_hash'] = password_hash($newPwd, PASSWORD_BCRYPT);
            file_put_contents($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $successMsg = 'Kata sandi pengelola berhasil diubah!';
        }
    }
}

// Reload updated data
$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];
$catalog = file_exists($productsPath) ? json_decode(file_get_contents($productsPath), true) : [];
$products = $catalog['products'] ?? [];
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Pengelola - <?= htmlspecialchars($settings['brand_name'] ?? 'Hiragana Footwear') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full antialiased text-slate-800 selection:bg-indigo-600 selection:text-white">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-sm">
                    HF
                </div>
                <div>
                    <span class="font-black text-slate-900 text-sm tracking-tight block">
                        <?= htmlspecialchars($settings['brand_name'] ?? 'Hiragana Footwear') ?>
                    </span>
                    <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">
                        Portal Pengelola Toko
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="../index.php" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Buka Website Utama</span>
                </a>
                <a href="logout.php" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center gap-1.5">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-indigo-400 bg-indigo-500/10 px-2.5 py-0.5 rounded-full border border-indigo-400/20">
                        Pusat Kontrol Lokal
                    </span>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
                        http://hiraganafootwear.test/admin
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-white">Kelola Informasi, Kontak & Katalog Produk</h1>
                <p class="text-xs text-slate-300">Setiap perubahan yang Anda simpan di halaman ini akan langsung otomatis tampil di website utama.</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="../katalog.html" target="_blank" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Master File Katalog</span>
                </a>
            </div>
        </div>

        <!-- Feedback Alert Messages -->
        <?php if (!empty($successMsg)): ?>
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-2xs">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span><?= htmlspecialchars($successMsg) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2 shadow-2xs">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
                <span><?= htmlspecialchars($errorMsg) ?></span>
            </div>
        <?php endif; ?>

        <!-- Tabs Navigation -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-2" id="adminTabs">
            <button onclick="switchTab('tab-general', this)" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-black transition <?= $activeTab === 'tab-general' ? 'active bg-indigo-600 text-white shadow-xs' : 'bg-white hover:bg-slate-200 text-slate-700 border border-slate-200' ?>">
                🏢 Informasi Toko & Kontak
            </button>
            <button onclick="switchTab('tab-features', this)" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-black transition <?= $activeTab === 'tab-features' ? 'active bg-indigo-600 text-white shadow-xs' : 'bg-white hover:bg-slate-200 text-slate-700 border border-slate-200' ?>">
                ⚡ Izin Fitur & Keunggulan Toko
            </button>
            <button onclick="switchTab('tab-socials', this)" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $activeTab === 'tab-socials' ? 'active bg-indigo-600 text-white shadow-xs' : 'bg-white hover:bg-slate-200 text-slate-700 border border-slate-200' ?>">
                🌐 E-Commerce & Sosial Media
            </button>
            <button onclick="switchTab('tab-products', this)" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $activeTab === 'tab-products' ? 'active bg-indigo-600 text-white shadow-xs' : 'bg-white hover:bg-slate-200 text-slate-700 border border-slate-200' ?>">
                📦 Kelola Harga & Katalog (<?= count($products) ?> Produk)
            </button>
            <button onclick="switchTab('tab-security', this)" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition <?= $activeTab === 'tab-security' ? 'active bg-indigo-600 text-white shadow-xs' : 'bg-white hover:bg-slate-200 text-slate-700 border border-slate-200' ?>">
                🔒 Keamanan & Sandi
            </button>
        </div>

        <!-- ============================================== -->
        <!-- TAB 1: INFORMASI TOKO & KONTAK -->
        <!-- ============================================== -->
        <div id="tab-general" class="tab-content <?= $activeTab === 'tab-general' ? '' : 'hidden' ?> space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                <div>
                    <h3 class="font-black text-slate-900 text-lg">Data Pokok Toko & Kontak Penjualan</h3>
                    <p class="text-xs text-slate-500">Nomor WhatsApp di bawah ini digunakan untuk menerima pemesanan otomatis dari pengunjung website.</p>
                </div>

                <form method="POST" action="/admin/index.php" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="action" value="save_settings">

                    <!-- Logo Brand / Toko Upload & Settings -->
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">Logo Brand / Toko</label>
                                <p class="text-[11px] text-slate-500">Logo ini akan otomatis tampil di Navbar atas dan Footer website.</p>
                            </div>
                            <span class="text-[10px] font-bold px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg">Format: PNG, JPG, WEBP, SVG</span>
                        </div>

                        <?php if (!empty($settings['logo_url'])): ?>
                            <div class="flex flex-wrap items-center gap-4 p-4 bg-white rounded-xl border border-slate-200">
                                <div class="w-20 h-16 bg-[#121214] rounded-xl flex items-center justify-center p-2 border border-slate-300">
                                    <img src="<?= htmlspecialchars($settings['logo_url']) ?>" alt="Logo Toko" class="max-h-full max-w-full object-contain">
                                </div>
                                <div class="space-y-1">
                                    <span class="text-xs font-bold text-slate-800 block">Logo Saat Ini Aktif</span>
                                    <p class="text-[11px] text-slate-400 font-mono"><?= htmlspecialchars($settings['logo_url']) ?></p>
                                    <label class="inline-flex items-center gap-2 text-xs text-rose-600 font-bold cursor-pointer pt-1">
                                        <input type="checkbox" name="remove_logo" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                                        <span>Centang ini jika ingin menghapus logo (kembali ke ikon bawaan)</span>
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase">Opsi 1: Upload File Gambar Baru</label>
                                <input type="file" name="logo_file" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                                <p class="text-[10px] text-slate-400">Paling bagus gunakan logo berformat PNG transparan.</p>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-700 uppercase">Opsi 2: Atau Masukkan Link / URL Gambar</label>
                                <input type="text" name="logo_url" placeholder="https://... atau /uploads/nama-logo.png" value="<?= htmlspecialchars($settings['logo_url'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 outline-none">
                                <p class="text-[10px] text-slate-400">Bisa masukkan link gambar online atau path lokal.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Brand / Toko</label>
                            <input type="text" name="brand_name" required value="<?= htmlspecialchars($settings['brand_name'] ?? 'Hiragana Footwear') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tagline Jepang / Slogan</label>
                            <input type="text" name="japanese_tagline" value="<?= htmlspecialchars($settings['japanese_tagline'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nomor WhatsApp Penerima Order (Hanya Angka)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">📱</span>
                                <input type="text" name="whatsapp" required placeholder="6281234567890" value="<?= htmlspecialchars($settings['whatsapp'] ?? '') ?>" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                            </div>
                            <p class="text-[11px] text-slate-500">Gunakan format kode negara tanpa spasi atau tanda plus (contoh: <strong>6281234567890</strong>).</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Format WhatsApp Tampilan</label>
                            <input type="text" name="whatsapp_display" placeholder="+62 812-3456-7890" value="<?= htmlspecialchars($settings['whatsapp_display'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                            <p class="text-[11px] text-slate-500">Teks nomor yang ditampilkan rapi pada footer website.</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Jam Operasional CS</label>
                            <input type="text" name="cs_hours" value="<?= htmlspecialchars($settings['cs_hours'] ?? 'Senin - Sabtu (08.00 - 21.00 WIB)') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Lokasi Workshop / Pengiriman</label>
                            <input type="text" name="location" value="<?= htmlspecialchars($settings['location'] ?? 'Kabupaten Bekasi, Jawa Barat, Indonesia') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Teks Banner Pengumuman Atas (Top Bar)</label>
                        <input type="text" name="banner_announcement" value="<?= htmlspecialchars($settings['banner_announcement'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Ringkasan Narasi Hero (Headline Tambahan)</label>
                        <textarea name="sub_headline" rows="2" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none"><?= htmlspecialchars($settings['sub_headline'] ?? '') ?></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan Informasi Toko</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB: FITUR & KEUNGGULAN TOKO (PERSIS FOTO 2) -->
        <!-- ============================================== -->
        <div id="tab-features" class="tab-content <?= $activeTab === 'tab-features' ? '' : 'hidden' ?> space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-8">
                
                <form method="POST" action="/admin/index.php" class="space-y-8">
                    <input type="hidden" name="action" value="save_features">

                    <!-- SECTION 1: Izin Fitur Sistem (Feature Flags) -->
                    <div class="space-y-3">
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Izin Fitur Sistem (Feature Flags):</h3>
                            <p class="text-xs text-slate-500">Fitur fungsional toko yang dikirimkan via API ke sistem storefront.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                            <!-- Feature 1: Checkout WhatsApp -->
                            <label class="flex items-center gap-3 p-3.5 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/20 cursor-pointer transition">
                                <input type="checkbox" name="feature_checkout_wa" value="1" <?= !empty($features['checkout_wa']) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <span class="text-xs font-bold text-slate-800">Checkout WhatsApp</span>
                            </label>

                            <!-- Feature 2: Varian Produk -->
                            <label class="flex items-center gap-3 p-3.5 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/20 cursor-pointer transition">
                                <input type="checkbox" name="feature_varian_produk" value="1" <?= !empty($features['varian_produk']) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <span class="text-xs font-bold text-slate-800">Varian Produk (Ukuran/Rasa)</span>
                            </label>

                            <!-- Feature 3: QRIS & Rekening Toko -->
                            <label class="flex items-center gap-3 p-3.5 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/20 cursor-pointer transition">
                                <input type="checkbox" name="feature_qris_payment" value="1" <?= !empty($features['qris_payment']) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <span class="text-xs font-bold text-slate-800">QRIS & Rekening Toko</span>
                            </label>

                            <!-- Feature 4: Banner & Slider Promo -->
                            <label class="flex items-center gap-3 p-3.5 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/20 cursor-pointer transition">
                                <input type="checkbox" name="feature_banner_promo" value="1" <?= !empty($features['banner_promo']) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <span class="text-xs font-bold text-slate-800">Banner & Slider Promo</span>
                            </label>

                            <!-- Feature 5: SEO Google & Maps -->
                            <label class="flex items-center gap-3 p-3.5 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/20 cursor-pointer transition">
                                <input type="checkbox" name="feature_seo_google" value="1" <?= !empty($features['seo_google']) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <span class="text-xs font-bold text-slate-800">SEO Google & Maps</span>
                            </label>

                            <!-- Feature 6: Multi Outlet / Cabang -->
                            <label class="flex items-center gap-3 p-3.5 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/20 cursor-pointer transition">
                                <input type="checkbox" name="feature_multi_outlet" value="1" <?= !empty($features['multi_outlet']) ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <span class="text-xs font-bold text-slate-800">Multi Outlet / Cabang</span>
                            </label>
                        </div>
                    </div>

                    <!-- SECTION 2: Opsi Ceklis Keunggulan Toko ("Tentang Usaha Kami") (PERSIS FOTO 2) -->
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm">Opsi Ceklis Keunggulan Toko ("Tentang Usaha Kami"):</h3>
                                <p class="text-xs text-slate-500">Pilih ceklis yang sesuai profil toko. Deskripsi "Tentang Usaha Kami" akan otomatis menyesuaikan pilihan ini.</p>
                            </div>
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[11px] font-bold rounded-full border border-emerald-200">
                                Dinamis & Otomatis
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Card 1: Melayani Eceran & Grosir -->
                            <label class="flex items-start gap-3 p-4 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/10 cursor-pointer transition">
                                <input type="checkbox" name="keunggulan_eceran_grosir" value="1" <?= !empty($keunggulan['eceran_grosir']) ? 'checked' : '' ?> class="mt-0.5 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Melayani Eceran & Grosir</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Cocok untuk toko retail, sembako, fashion, kuliner & elektronik</div>
                                </div>
                            </label>

                            <!-- Card 2: Garansi Mutu Terpercaya & Original -->
                            <label class="flex items-start gap-3 p-4 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/10 cursor-pointer transition">
                                <input type="checkbox" name="keunggulan_garansi_mutu" value="1" <?= !empty($keunggulan['garansi_mutu']) ? 'checked' : '' ?> class="mt-0.5 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Garansi Mutu Terpercaya & Original</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Memberi rasa aman & keyakinan pada calon pembeli</div>
                                </div>
                            </label>

                            <!-- Card 3: Siap Kirim ke Seluruh Indonesia -->
                            <label class="flex items-start gap-3 p-4 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/10 cursor-pointer transition">
                                <input type="checkbox" name="keunggulan_siap_kirim_indonesia" value="1" <?= !empty($keunggulan['siap_kirim_indonesia']) ? 'checked' : '' ?> class="mt-0.5 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Siap Kirim ke Seluruh Indonesia</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Jika tidak dicentang, otomatis menampilkan 'Siap Kirim Area [Kota]'</div>
                                </div>
                            </label>

                            <!-- Card 4: Bisa Bayar di Tempat (COD) - DARI FOTO 1 -->
                            <label class="flex items-start gap-3 p-4 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/10 cursor-pointer transition">
                                <input type="checkbox" name="keunggulan_bisa_cod" value="1" <?= !empty($keunggulan['bisa_cod']) ? 'checked' : '' ?> class="mt-0.5 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Bisa Bayar di Tempat (COD)</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Jika tidak dicentang, badge toko tidak akan memuat kata-kata COD</div>
                                </div>
                            </label>

                            <!-- Card 5: 100% Garansi Tukar Size - DARI FOTO 1 -->
                            <label class="flex items-start gap-3 p-4 bg-white border border-slate-200 rounded-xl hover:border-blue-400 hover:bg-blue-50/10 cursor-pointer transition">
                                <input type="checkbox" name="keunggulan_garansi_tukar_size" value="1" <?= !empty($keunggulan['garansi_tukar_size']) ? 'checked' : '' ?> class="mt-0.5 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">100% Garansi Tukar Size</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Tampilkan badge jaminan retur/tukar ukuran jika sepatu/sandal kekecilan</div>
                                </div>
                            </label>

                            <!-- Card 6: Faktur Pajak & SPH Resmi (B2B) -->
                            <label class="flex items-start gap-3 p-4 bg-amber-50/40 border border-amber-300 rounded-xl hover:border-amber-400 cursor-pointer transition">
                                <input type="checkbox" name="keunggulan_faktur_pajak" value="1" <?= !empty($keunggulan['faktur_pajak']) ? 'checked' : '' ?> class="mt-0.5 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Faktur Pajak & SPH Resmi Siap (B2B / Pengadaan)</div>
                                    <div class="text-[11px] text-amber-700 mt-0.5">⚠️ Hanya aktifkan jika toko melayani tender/pengadaan kantor resmi. Biarkan nonaktif untuk UMKM mikro biasa agar aman & tidak berisiko.</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Tombol Simpan Biru Sesuai Foto 2 -->
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer">
                            <span>Simpan Pembaruan Limit, Fitur & Profil Toko</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 2: E-COMMERCE & SOSIAL MEDIA -->
        <!-- ============================================== -->
        <div id="tab-socials" class="tab-content <?= $activeTab === 'tab-socials' ? '' : 'hidden' ?> space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                <div>
                    <h3 class="font-black text-slate-900 text-lg">Tautan Toko E-Commerce & Akun Media Sosial</h3>
                    <p class="text-xs text-slate-500">Tautan ini akan langsung terhubung ke badge toko Shopee, Tokopedia, dan akun Instagram/TikTok Anda.</p>
                </div>

                <form method="POST" action="/admin/index.php" class="space-y-6">
                    <input type="hidden" name="action" value="save_socials">

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded bg-orange-100 text-orange-800 text-[10px]">Shopee</span>
                                <span>Tautan Toko Shopee Official</span>
                            </label>
                            <input type="url" name="shopee_url" value="<?= htmlspecialchars($settings['shopee_url'] ?? '') ?>" placeholder="https://shopee.co.id/nama_toko" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px]">Tokopedia</span>
                                <span>Tautan Toko Tokopedia Official</span>
                            </label>
                            <input type="url" name="tokopedia_url" value="<?= htmlspecialchars($settings['tokopedia_url'] ?? '') ?>" placeholder="https://www.tokopedia.com/nama_toko" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Username Instagram</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">@</span>
                                    <input type="text" name="instagram" value="<?= htmlspecialchars($settings['instagram'] ?? 'hiragana.footwear') ?>" class="w-full pl-8 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Username TikTok</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">@</span>
                                    <input type="text" name="tiktok" value="<?= htmlspecialchars($settings['tiktok'] ?? 'hiragana.0') ?>" class="w-full pl-8 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan Tautan E-Commerce</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 3: KELOLA HARGA & KATALOG PRODUK -->
        <!-- ============================================== -->
        <div id="tab-products" class="tab-content <?= $activeTab === 'tab-products' ? '' : 'hidden' ?> space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-black text-slate-900 text-lg">Kelola Foto & Harga Jual Produk</h3>
                        <p class="text-xs text-slate-500">Upload foto langsung dari komputer atau tempel link Google Drive / URL gambar untuk setiap produk.</p>
                    </div>
                    <div class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl">
                        Total: <?= count($products) ?> Produk Terdaftar
                    </div>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-900 text-white font-black uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="p-4 w-12">No</th>
                                <th class="p-4">Foto & Seri Produk (Hingga 3 Foto)</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4">Harga Promo (Rp)</th>
                                <th class="p-4 text-center">Aksi Foto & Harga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            <?php foreach ($products as $idx => $p): 
                                $price = (int) ($p['price'] ?? 145000);
                                $imgs = !empty($p['images']) && is_array($p['images'])
                                    ? array_values(array_filter(array_map('normalizeImageUrl', $p['images'])))
                                    : (!empty($p['image_url']) ? [normalizeImageUrl($p['image_url'])] : []);
                                $code = htmlspecialchars($p['code'] ?? 'HF-' . str_pad($idx+1, 2, '0', STR_PAD_LEFT));
                                $imgsJson = htmlspecialchars(json_encode($imgs), ENT_QUOTES, 'UTF-8');
                            ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 font-bold text-slate-400"><?= $idx + 1 ?></td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3.5">
                                        <!-- Multi-photo Stack Preview -->
                                        <div class="flex items-center -space-x-4 hover:space-x-1.5 transition-all duration-200">
                                            <?php if (!empty($imgs)): ?>
                                                <?php foreach ($imgs as $k => $im): ?>
                                                    <div class="w-12 h-12 rounded-xl bg-slate-900 overflow-hidden flex-shrink-0 border-2 border-white shadow-xs relative" title="Foto <?= $k+1 ?>">
                                                        <img src="<?= htmlspecialchars($im) ?>" alt="Foto <?= $k+1 ?>" class="w-full h-full object-cover">
                                                        <span class="absolute bottom-0.5 right-0.5 px-1 bg-black/80 text-[8px] font-bold text-white rounded"><?= $k+1 ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-[9px] font-bold text-slate-400">
                                                    NO FOTO
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <strong class="font-black text-slate-900 text-xs block"><?= htmlspecialchars($p['name']) ?></strong>
                                            <span class="text-[11px] text-slate-400 font-mono"><?= $code ?> • <?= htmlspecialchars($p['sub_category'] ?? '') ?></span>
                                            <div class="flex items-center gap-1 text-[10px] font-bold text-indigo-700 mt-1">
                                                <span class="px-1.5 py-0.5 rounded bg-indigo-50 border border-indigo-200">📷 <?= count($imgs) ?> / 3 Foto Aktif</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-bold text-[10px] rounded-lg">
                                        <?= htmlspecialchars($p['category']) ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="font-bold text-indigo-600 font-mono text-xs">Rp <?= number_format($price, 0, ',', '.') ?></span>
                                </td>
                                <td class="p-4 text-center">
                                    <button type="button" onclick="openProductModal(<?= $idx ?>, '<?= htmlspecialchars(addslashes($p['name'])) ?>', '<?= htmlspecialchars(addslashes($code)) ?>', <?= $price ?>, <?= $imgsJson ?>)" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white font-bold rounded-xl text-xs transition border border-indigo-200 shadow-2xs cursor-pointer">
                                        <i data-lucide="images" class="w-3.5 h-3.5"></i>
                                        <span>Kelola 3 Foto & Harga</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MODAL EDIT PRODUK (UPLOAD FOTO HINGGA 3 GAMBAR & LINK GOOGLE DRIVE) -->
        <div id="productEditModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5 animate-in fade-in duration-200 max-h-[92vh] overflow-y-auto">
                
                <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded bg-amber-100 text-amber-800">FITUR STOREONLINE PRO</span>
                            <span class="text-[10px] font-bold text-slate-400">Mendukung 3 Gambar & Ganti-ganti Foto</span>
                        </div>
                        <h3 id="modalProdTitle" class="font-black text-slate-900 text-base mt-1">Nama Produk</h3>
                    </div>
                    <button type="button" onclick="closeProductModal()" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form method="POST" action="/admin/index.php" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="action" value="update_product">
                    <input type="hidden" name="product_index" id="modalProdIndex" value="0">

                    <!-- Info Box Google Drive -->
                    <div class="p-3 bg-blue-50/70 border border-blue-200 rounded-2xl text-xs text-blue-900 flex items-start gap-2.5">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
                        <p class="leading-relaxed text-[11px]">
                            <strong>Tips 3 Foto:</strong> Anda bisa mengunggah file dari komputer/HP ATAU menempelkan link Google Drive untuk setiap slot foto di bawah. Pembeli di web bisa menggeser (&lt; &amp; &gt;) dan klik thumbnail untuk berganti foto!
                        </p>
                    </div>

                    <!-- 3 Image Slots -->
                    <div class="space-y-4">
                        
                        <!-- SLOT 1: FOTO UTAMA -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-xs text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px]">1</span>
                                    <span>Foto 1: Foto Utama (Depan / Sudut 45°)</span>
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">Wajib Ada</span>
                            </div>

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <div class="w-16 h-16 rounded-xl bg-slate-900 border border-slate-300 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                    <img id="modalProdPreview1" src="" alt="Slot 1" class="w-full h-full object-cover hidden">
                                    <span id="modalProdPlaceholder1" class="text-[9px] text-slate-400 font-bold">KOSONG</span>
                                </div>
                                <div class="flex-1 w-full space-y-2">
                                    <input type="text" name="image_url_1" id="modalProdUrl1" placeholder="Tempel Link Google Drive / URL foto 1..." class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                                    <div class="flex items-center gap-2">
                                        <input type="file" name="product_image_1" accept="image/*" class="text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-indigo-600 file:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SLOT 2: FOTO SAMPING / DETAIL -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-xs text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-slate-700 text-white flex items-center justify-center text-[10px]">2</span>
                                    <span>Foto 2: Tampak Samping / Detail Upper</span>
                                </span>
                                <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-bold cursor-pointer">
                                    <input type="checkbox" name="delete_image_2" value="1" class="rounded text-rose-600">
                                    <span>Kosongkan Slot 2</span>
                                </label>
                            </div>

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <div class="w-16 h-16 rounded-xl bg-slate-900 border border-slate-300 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                    <img id="modalProdPreview2" src="" alt="Slot 2" class="w-full h-full object-cover hidden">
                                    <span id="modalProdPlaceholder2" class="text-[9px] text-slate-400 font-bold">KOSONG</span>
                                </div>
                                <div class="flex-1 w-full space-y-2">
                                    <input type="text" name="image_url_2" id="modalProdUrl2" placeholder="Tempel Link Google Drive / URL foto 2..." class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                                    <div class="flex items-center gap-2">
                                        <input type="file" name="product_image_2" accept="image/*" class="text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-700 file:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SLOT 3: FOTO SOL BAWAH / BELAKANG -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-xs text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-slate-700 text-white flex items-center justify-center text-[10px]">3</span>
                                    <span>Foto 3: Tampak Sol Bawah / Belakang</span>
                                </span>
                                <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-bold cursor-pointer">
                                    <input type="checkbox" name="delete_image_3" value="1" class="rounded text-rose-600">
                                    <span>Kosongkan Slot 3</span>
                                </label>
                            </div>

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <div class="w-16 h-16 rounded-xl bg-slate-900 border border-slate-300 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                    <img id="modalProdPreview3" src="" alt="Slot 3" class="w-full h-full object-cover hidden">
                                    <span id="modalProdPlaceholder3" class="text-[9px] text-slate-400 font-bold">KOSONG</span>
                                </div>
                                <div class="flex-1 w-full space-y-2">
                                    <input type="text" name="image_url_3" id="modalProdUrl3" placeholder="Tempel Link Google Drive / URL foto 3..." class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-indigo-500">
                                    <div class="flex items-center gap-2">
                                        <input type="file" name="product_image_3" accept="image/*" class="text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-700 file:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Input Harga Jual Promo (Rp) -->
                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Harga Jual Promo (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="price" id="modalProdPrice" required step="1000" class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-black text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <button type="button" onclick="closeProductModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black transition shadow-md flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Simpan Perubahan (3 Foto & Harga)</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 4: KEAMANAN & SANDI -->
        <!-- ============================================== -->
        <div id="tab-security" class="tab-content <?= $activeTab === 'tab-security' ? '' : 'hidden' ?> space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6 max-w-xl">
                <div>
                    <h3 class="font-black text-slate-900 text-lg">Ubah Kata Sandi Admin</h3>
                    <p class="text-xs text-slate-500">Perbarui kata sandi login untuk menjaga keamanan akses panel pengelola toko Anda.</p>
                </div>

                <form method="POST" action="/admin/index.php" class="space-y-4">
                    <input type="hidden" name="action" value="change_password">

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi Saat Ini</label>
                        <input type="password" name="old_password" required placeholder="Masukkan kata sandi lama..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi Baru</label>
                        <input type="password" name="new_password" required minlength="6" placeholder="Minimal 6 karakter..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="confirm_password" required minlength="6" placeholder="Ulangi kata sandi baru..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs shadow-md transition flex items-center gap-2 cursor-pointer">
                            <i data-lucide="key" class="w-4 h-4"></i>
                            <span>Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });

        function switchTab(tabId, btn) {
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            
            // Reset tab button styles
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'bg-indigo-600', 'text-white', 'shadow-xs');
                b.classList.add('bg-white', 'text-slate-700', 'border', 'border-slate-200');
            });

            // Show selected tab
            const target = document.getElementById(tabId);
            if (target) target.classList.remove('hidden');
            
            // Activate clicked button
            const el = btn || (typeof event !== 'undefined' && event ? (event.currentTarget || event.target.closest('.tab-btn')) : null);
            if (el) {
                el.classList.remove('bg-white', 'text-slate-700', 'border', 'border-slate-200');
                el.classList.add('active', 'bg-indigo-600', 'text-white', 'shadow-xs');
            }
        }

        // Product Modal Logic for 3 Images
        function openProductModal(idx, name, code, price, images) {
            document.getElementById('modalProdIndex').value = idx;
            document.getElementById('modalProdTitle').innerText = name + (code ? ' (' + code + ')' : '');
            document.getElementById('modalProdPrice').value = price;
            
            const imgs = Array.isArray(images) ? images : (images ? [images] : []);
            
            for (let i = 1; i <= 3; i++) {
                const rawUrl = imgs[i - 1] || '';
                const urlInput = document.getElementById('modalProdUrl' + i);
                if (urlInput) urlInput.value = rawUrl;
                
                const preview = document.getElementById('modalProdPreview' + i);
                const placeholder = document.getElementById('modalProdPlaceholder' + i);
                
                if (rawUrl) {
                    let previewUrl = rawUrl;
                    const match = rawUrl.match(/drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?(?:export=view&)?id=)([a-zA-Z0-9_-]+)/);
                    if (match && match[1]) {
                        previewUrl = 'https://lh3.googleusercontent.com/d/' + match[1];
                    }
                    if (preview) {
                        preview.src = previewUrl;
                        preview.classList.remove('hidden');
                    }
                    if (placeholder) placeholder.classList.add('hidden');
                } else {
                    if (preview) {
                        preview.src = '';
                        preview.classList.add('hidden');
                    }
                    if (placeholder) placeholder.classList.remove('hidden');
                }
            }

            document.getElementById('productEditModal').classList.remove('hidden');
            if (window.lucide) lucide.createIcons();
        }

        function closeProductModal() {
            document.getElementById('productEditModal').classList.add('hidden');
        }

        window.addEventListener('click', (e) => {
            const m = document.getElementById('productEditModal');
            if (e.target === m) closeProductModal();
        });
    </script>
</body>
</html>

