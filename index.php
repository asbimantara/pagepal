<?php
require_once 'config/session.php';
initSession();

if (isset($_SESSION['user_id'])) {
    checkSessionTimeout();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PagePal - Your Reading Companion</title>
    <link rel="stylesheet" href="assets/css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body>
    <nav class="landing-nav">
        <div class="logo">
            <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" alt="PagePal Logo">
            <span>PagePal</span>
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="nav-cta">Dashboard</a>
        <?php else: ?>
            <a href="pages/login.php" class="nav-cta">Masuk</a>
        <?php endif; ?>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Teman Setia Perjalanan Membacamu</h1>
            <p>PagePal akan menemani setiap halaman yang kamu baca dengan mencatat progress, membuat catatan, dan menampilkan statistik membacamu</p>
            <div class="cta-buttons">
                <a href="pages/register.php" class="cta-primary">Mulai Sekarang</a>
                <a href="pages/login.php" class="cta-secondary">Sudah Punya Akun?</a>
            </div>
        </div>
        <div class="hero-image">
            <lottie-player 
                src="https://assets7.lottiefiles.com/packages/lf20_1a8dx7zj.json"
                background="transparent"
                speed="1"
                style="width: 100%; height: 400px;"
                loop
                autoplay>
            </lottie-player>
        </div>
    </header>

    <section class="features">
        <h2>Fitur Utama</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-book-reader"></i>
                <h3>Track Progress</h3>
                <p>Catat halaman yang sudah dibaca dan lihat progressmu</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-sticky-note"></i>
                <h3>Catatan Membaca</h3>
                <p>Buat catatan untuk setiap buku yang kamu baca</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-chart-line"></i>
                <h3>Statistik</h3>
                <p>Lihat statistik membacamu secara detail</p>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <h2>Siap Mulai Petualangan?</h2>
        <p>Bergabung sekarang dan biarkan PagePal menemani perjalanan membacamu</p>
        <a href="pages/register.php" class="cta-primary">Daftar Gratis</a>
    </section>

    <footer class="landing-footer">
        <p>&copy; <?php echo date('Y'); ?> PagePal. All rights reserved.</p>
    </footer>
</body>
</html> 