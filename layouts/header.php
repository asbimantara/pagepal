<?php
$isInPagesDir = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
$baseUrl = $isInPagesDir ? '..' : '.';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>PagePal - Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/assets/css/style.css">
    <script src="<?php echo $baseUrl; ?>/assets/js/main.js" defer></script>
</head>
<body>
    <nav class="main-nav">
        <div class="logo">
            <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" 
                 alt="PagePal Logo" 
                 style="max-height: 40px;">
        </div>
        <ul class="nav-links">
            <li><a href="<?php echo $baseUrl; ?>/dashboard.php">Beranda</a></li>
            <li><a href="<?php echo $baseUrl; ?>/pages/my-books.php">Koleksi Saya</a></li>
            <li><a href="<?php echo $baseUrl; ?>/pages/statistics.php">Statistik</a></li>
            <li><a href="<?php echo $baseUrl; ?>/pages/profile.php">Profil</a></li>
            <li><a href="<?php echo $baseUrl; ?>/pages/logout.php">Keluar</a></li>
        </ul>
        <button class="mobile-menu-btn">
            <i class="fas fa-bars"></i>
        </button>
    </nav>
</body>
</html> 