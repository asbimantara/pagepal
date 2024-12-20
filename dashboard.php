<?php
require_once 'config/session.php';
initSession();

if (!isset($_SESSION['user_id'])) {
    header("Location: pages/login.php");
    exit();
}

checkSessionTimeout();

require_once 'config/database.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: pages/login.php");
    exit();
}

try {
    // Ambil data user dengan error handling
    $user = $database->users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
    if (!$user) {
        session_destroy();
        header("Location: pages/login.php");
        exit();
    }

    // Konversi books ke array dengan pengecekan null
    $books = iterator_to_array($user->books ?? new ArrayObject([]));

    // Hitung statistik dengan type casting
    $totalBooks = count($books);
    $booksReading = array_filter((array)$books, function($book) {
        return isset($book->status) && $book->status === 'sedang_dibaca';
    });
    $booksFinished = array_filter((array)$books, function($book) {
        return isset($book->status) && $book->status === 'selesai';
    });
    $totalPagesRead = array_reduce((array)$books, function($sum, $book) {
        return $sum + (isset($book->current_page) ? (int)$book->current_page : 0);
    }, 0);

} catch (Exception $e) {
    error_log("Error in dashboard: " . $e->getMessage());
    header("Location: pages/error.php");
    exit();
}

include 'layouts/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PagePal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="hero-content">
                <h1>Selamat Datang, <?php echo htmlspecialchars($user->name ?? 'User'); ?>!</h1>
                <p class="hero-subtitle">Catat dan lacak perjalanan membacamu di sini</p>
                <a href="pages/add-book.php" class="cta-button">
                    <i class="fas fa-plus"></i> Mulai Membaca
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-book-open"></i>
                <h3><?php echo count($booksReading); ?></h3>
                <p>Sedang Dibaca</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3><?php echo count($booksFinished); ?></h3>
                <p>Buku Selesai</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-file-alt"></i>
                <h3><?php echo $totalPagesRead; ?></h3>
                <p>Halaman Dibaca</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-book"></i>
                <h3><?php echo $totalBooks; ?></h3>
                <p>Total Buku</p>
            </div>
        </div>

        <!-- Quote Slider -->
        <div class="quote-slider-container">
            <div class="quote-slider" id="quoteSlider">
                <div class="quotes">
                    <div class="quote active">
                        <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                        <p class="quote-text">"Buku adalah teman yang tidak pernah mengkhianati."</p>
                        <div class="quote-author">
                            <span class="author-name">- Soekarno</span>
                        </div>
                    </div>
                    <div class="quote">
                        <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                        <p class="quote-text">"Membaca buku seperti melihat dunia dengan mata orang lain."</p>
                        <div class="quote-author">
                            <span class="author-name">- Pramoedya Ananta Toer</span>
                        </div>
                    </div>
                    <div class="quote">
                        <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                        <p class="quote-text">"Ilmu itu diperoleh dari lidah yang gemar bertanya serta akal yang suka berpikir."</p>
                        <div class="quote-author">
                            <span class="author-name">- Abdullah bin Abbas</span>
                        </div>
                    </div>
                </div>
                <div class="quote-nav">
                    <button class="quote-nav-btn prev-quote"><i class="fas fa-chevron-left"></i></button>
                    <div class="quote-dots"></div>
                    <button class="quote-nav-btn next-quote"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layouts/footer.php'; ?>
</body>
</html>