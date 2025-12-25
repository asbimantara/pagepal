<?php
// Pengaturan session lifetime
function initSession()
{
    // Start output buffering to prevent "headers already sent" errors
    if (!ob_get_level()) {
        ob_start();
    }

    // Only configure and start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        // Check if headers have NOT been sent before setting ini
        if (!headers_sent()) {
            ini_set('session.gc_maxlifetime', 1800); // 30 menit
            session_set_cookie_params(1800); // 30 menit
        }
        session_start();
    }
}

function checkSessionTimeout()
{
    if (isset($_SESSION['last_activity'])) {
        // Jika tidak ada aktivitas selama 30 menit
        if (time() - $_SESSION['last_activity'] > 1800) {
            session_unset();     // Hapus semua variable session
            session_destroy();    // Destroy session
            header("Location: pages/login.php?message=timeout");
            exit();
        }
    }
    $_SESSION['last_activity'] = time(); // Update waktu aktivitas terakhir
}
?>