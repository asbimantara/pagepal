<?php
session_start();
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
</div>

<?php include 'layouts/footer.php'; ?> 