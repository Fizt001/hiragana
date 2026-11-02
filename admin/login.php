<?php
session_start();

$settingsPath = __DIR__ . '/../data/settings.json';
$settings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [];
$brandName = $settings['brand_name'] ?? 'Hiragana Footwear';

// If already logged in, redirect to admin dashboard
if (!empty($_SESSION['hf_admin_logged_in'])) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $password = $_POST['password'] ?? '';
    $hash = $settings['admin_password_hash'] ?? '$2y$12$nToZ6MSLD4/lnVjwyKKMteqZfY2gaUYoJLG9NuMpFIzKDz5FWBzgy';

    if (password_verify($password, $hash)) {
        $_SESSION['hf_admin_logged_in'] = true;
        $_SESSION['hf_admin_user'] = 'Admin';
        header('Location: /admin/index.php');
        exit;
    } else {
        $error = 'Kata sandi yang Anda masukkan salah. Default: admin123';
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pengelola - <?= htmlspecialchars($brandName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-4 selection:bg-indigo-600 selection:text-white">

    <div class="max-w-md w-full space-y-6">
        
        <!-- Brand Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-3xl bg-indigo-600 text-white flex items-center justify-center font-black text-2xl mx-auto shadow-lg shadow-indigo-500/30">
                HF
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Admin Portal</h1>
            <p class="text-xs text-slate-400">Pengaturan Informasi & Produk <?= htmlspecialchars($brandName) ?></p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md space-y-6">
            
            <?php if (!empty($error)): ?>
                <div class="p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-300 text-xs font-semibold flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="/admin/login.php" class="space-y-4">
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Kata Sandi Admin
                    </label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        <input type="password" id="password" name="password" required autofocus placeholder="Masukkan kata sandi..." class="w-full pl-10 pr-10 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition placeholder:text-slate-600">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition">
                            <i data-lucide="eye" id="eyeIcon" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-500 pt-1">
                        🔑 <em>Kata sandi bawaan awal:</em> <strong class="text-slate-300 font-mono">admin123</strong>
                    </p>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2 cursor-pointer">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk ke Panel Pengelola</span>
                </button>
            </form>

            <div class="pt-4 border-t border-slate-700/60 text-center">
                <a href="../index.php" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-indigo-400 transition">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Kembali ke Website Utama</span>
                </a>
            </div>

        </div>

        <p class="text-center text-[11px] text-slate-600">
            Akses Lokal: <code>http://hiraganafootwear.test/admin</code>
        </p>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });

        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                pwd.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            if (window.lucide) lucide.createIcons();
        }
    </script>
</body>
</html>

