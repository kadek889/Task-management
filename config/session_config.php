<?php
// Set waktu timeout session (3 jam dalam detik)
ini_set('session.gc_maxlifetime', 10800); // 3 * 60 * 60 = 10800 detik
session_set_cookie_params(10800);

// Mulai session
// Pastikan session_start() dipanggil setelah pengaturan session tetapi sebelum cek LAST_ACTIVITY
session_start(); // Memindahkan session_start() ke sini

// Cek apakah pengguna sudah login dan ambil peran pengguna
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? null;

// Cek apakah ada last activity time
if (isset($_SESSION['LAST_ACTIVITY'])) {
    // Hitung selisih waktu sejak aktivitas terakhir
    $inactive_time = time() - $_SESSION['LAST_ACTIVITY'];
    
    // Jika tidak aktif selama 3 jam (10800 detik)
    if ($inactive_time >= 10800) {
        // Hapus semua data session
        session_unset();
        session_destroy();
        
        // Redirect ke halaman login
        header("Location: /login.php");
        exit();
    }
}

// Update waktu aktivitas terakhir
$_SESSION['LAST_ACTIVITY'] = time();
?> 