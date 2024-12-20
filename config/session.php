<?php
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        // Jika tidak ada aktivitas selama 30 menit
        if (time() - $_SESSION['last_activity'] > 1800) {
            session_unset();     // Hapus semua variable session
            session_destroy();    // Destroy session
            header("Location: pages/login.php?message=timeout"); // Redirect ke login
            exit();
        }
    }
    $_SESSION['last_activity'] = time(); // Update waktu aktivitas terakhir
}

// Pengaturan session lifetime
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.gc_maxlifetime', 1800); // 30 menit
        session_set_cookie_params(1800); // 30 menit
        session_start();
    }
}
?> 