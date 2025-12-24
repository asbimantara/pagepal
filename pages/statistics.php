<?php
ob_start();
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = $database->users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
$books = $user->books ?? [];

// Hitung statistik
$totalBooks = count($books);
$booksCompleted = 0;
$totalPages = 0;
$pagesRead = 0;
$booksInProgress = 0;

foreach ($books as $book) {
    $totalPages += $book->total_pages;
    $pagesRead += $book->current_page;

    if ($book->status === 'selesai') {
        $booksCompleted++;
    } elseif ($book->status === 'sedang_dibaca') {
        $booksInProgress++;
    }
}

// Tambahkan setelah perhitungan statistik yang ada
$achievement = '';
$nextLevel = '';
$booksToNext = 0;

if ($booksCompleted <= 15) {
    $achievement = 'Pembaca Pemula';
    $nextLevel = 'Pembaca Antusias';
    $booksToNext = 16 - $booksCompleted;
} elseif ($booksCompleted <= 40) {
    $achievement = 'Pembaca Antusias';
    $nextLevel = 'Petualang Literasi';
    $booksToNext = 41 - $booksCompleted;
} elseif ($booksCompleted <= 75) {
    $achievement = 'Petualang Literasi';
    $nextLevel = 'Penjelajah Kata';
    $booksToNext = 76 - $booksCompleted;
} elseif ($booksCompleted <= 120) {
    $achievement = 'Penjelajah Kata';
    $nextLevel = 'Cendekiawan Buku';
    $booksToNext = 121 - $booksCompleted;
} elseif ($booksCompleted <= 175) {
    $achievement = 'Cendekiawan Buku';
    $nextLevel = 'Sarjana Pustaka';
    $booksToNext = 176 - $booksCompleted;
} elseif ($booksCompleted <= 250) {
    $achievement = 'Sarjana Pustaka';
    $nextLevel = 'Guru Literasi';
    $booksToNext = 251 - $booksCompleted;
} elseif ($booksCompleted <= 350) {
    $achievement = 'Guru Literasi';
    $nextLevel = 'Maestro Buku';
    $booksToNext = 351 - $booksCompleted;
} elseif ($booksCompleted <= 500) {
    $achievement = 'Maestro Buku';
    $nextLevel = 'Filsuf Pustaka';
    $booksToNext = 501 - $booksCompleted;
} elseif ($booksCompleted <= 750) {
    $achievement = 'Filsuf Pustaka';
    $nextLevel = 'Legenda Literasi';
    $booksToNext = 751 - $booksCompleted;
} else {
    $achievement = 'Legenda Literasi';
    $nextLevel = '';
    $booksToNext = 0;
}

// Tambahkan fungsi untuk menghitung progress
function calculateProgress($booksCompleted)
{
    // Level Pembaca Pemula (0-15 buku)
    if ($booksCompleted <= 15) {
        return ($booksCompleted / 15) * 100;
    }
    // Level Pembaca Antusias (16-40 buku)
    else if ($booksCompleted <= 40) {
        return (($booksCompleted - 15) / (40 - 15)) * 100;
    }
    // Level Petualang Literasi (41-75 buku)
    else if ($booksCompleted <= 75) {
        return (($booksCompleted - 40) / (75 - 40)) * 100;
    }
    // Level Penjelajah Kata (76-120 buku)
    else if ($booksCompleted <= 120) {
        return (($booksCompleted - 75) / (120 - 75)) * 100;
    }
    return 100;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PagePal - Statistik</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/statistics.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <?php include '../layouts/header.php'; ?>

    <div class="container">
        <div class="statistics-wrapper">
            <div class="statistics-card">
                <h1>Statistik Membaca</h1>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-item">
                        <i class="fas fa-book-reader"></i>
                        <div class="stat-info">
                            <h3>Sedang Dibaca</h3>
                            <p><?php echo $booksInProgress; ?></p>
                        </div>
                    </div>

                    <div class="stat-item">
                        <i class="fas fa-check-circle"></i>
                        <div class="stat-info">
                            <h3>Buku Selesai</h3>
                            <p><?php echo $booksCompleted; ?></p>
                        </div>
                    </div>

                    <div class="stat-item">
                        <i class="fas fa-bookmark"></i>
                        <div class="stat-info">
                            <h3>Halaman Dibaca</h3>
                            <p><span class="current-pages"><?php echo $pagesRead; ?></span> / <?php echo $totalPages; ?>
                            </p>
                        </div>
                    </div>

                    <div class="stat-item">
                        <i class="fas fa-book-open"></i>
                        <div class="stat-info">
                            <h3>Total Buku</h3>
                            <p><?php echo $totalBooks; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Achievement Section -->
                <div class="achievement-section">
                    <h2>Pencapaianmu</h2>
                    <div class="achievement-card">
                        <div class="achievement-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="achievement-info">
                            <h3>Level Saat Ini</h3>
                            <p class="achievement-title"><?php echo $achievement; ?></p>
                            <?php if ($nextLevel): ?>
                                <p class="achievement-next">
                                    <span class="books-remaining"><?php echo $booksToNext; ?></span> buku lagi untuk
                                    mencapai level <?php echo $nextLevel; ?>
                                </p>
                                <div class="achievement-progress">
                                    <div class="achievement-progress-bar"
                                        style="width: <?php echo calculateProgress($booksCompleted); ?>%"></div>
                                </div>
                                <p class="progress-text"><?php echo round(calculateProgress($booksCompleted)); ?>% menuju
                                    level berikutnya</p>
                            <?php else: ?>
                                <p class="achievement-next">Selamat! Kamu sudah mencapai level tertinggi! 🎉</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Levels List -->
                    <div class="levels-list">
                        <h3>Perjalanan Level Membaca</h3>
                        <div class="level-items">
                            <div class="level-item <?php echo $booksCompleted <= 15 ? 'current' : 'completed'; ?>">
                                <div class="level-info">
                                    <i class="fas fa-seedling"></i>
                                    <span class="level-name">Pembaca Pemula</span>
                                </div>
                                <span class="level-books">0-15 buku</span>
                            </div>
                            <div
                                class="level-item <?php echo $booksCompleted > 15 && $booksCompleted <= 40 ? 'current' : ($booksCompleted > 40 ? 'completed' : ''); ?>">
                                <div class="level-info">
                                    <i class="fas fa-book-reader"></i>
                                    <span class="level-name">Pembaca Antusias</span>
                                </div>
                                <span class="level-books">16-40 buku</span>
                            </div>
                            <div
                                class="level-item <?php echo $booksCompleted > 40 && $booksCompleted <= 75 ? 'current' : ($booksCompleted > 75 ? 'completed' : ''); ?>">
                                <div class="level-info">
                                    <i class="fas fa-compass"></i>
                                    <span class="level-name">Petualang Literasi</span>
                                </div>
                                <span class="level-books">41-75 buku</span>
                            </div>
                            <div
                                class="level-item <?php echo $booksCompleted > 75 && $booksCompleted <= 120 ? 'current' : ($booksCompleted > 120 ? 'completed' : ''); ?>">
                                <div class="level-info">
                                    <i class="fas fa-feather-alt"></i>
                                    <span class="level-name">Penjelajah Kata</span>
                                </div>
                                <span class="level-books">76-120 buku</span>
                            </div>
                            <div
                                class="level-item <?php echo $booksCompleted > 120 && $booksCompleted <= 175 ? 'current' : ($booksCompleted > 175 ? 'completed' : ''); ?>">
                                <div class="level-info">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span class="level-name">Cendekiawan Buku</span>
                                </div>
                                <span class="level-books">121-175 buku</span>
                            </div>
                            <div
                                class="level-item <?php echo $booksCompleted > 175 && $booksCompleted <= 250 ? 'current' : ($booksCompleted > 250 ? 'completed' : ''); ?>">
                                <div class="level-info">
                                    <i class="fas fa-university"></i>
                                    <span class="level-name">Sarjana Pustaka</span>
                                </div>
                                <span class="level-books">176-250 buku</span>
                            </div>
                            <div
                                class="level-item <?php echo $booksCompleted > 250 && $booksCompleted <= 350 ? 'current' : ($booksCompleted > 350 ? 'completed' : ''); ?>">
                                <div class="level-info">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <span class="level-name">Guru Literasi</span>
                                </div>
                                <span class="level-books">251-350 buku</span>
                            </div>
                            <div
                                class="level-item <?php echo $booksCompleted > 350 && $booksCompleted <= 500 ? 'current' : ($booksCompleted > 500 ? 'completed' : ''); ?>">
                                <div class="level-info">
                                    <i class="fas fa-crown"></i>
                                    <span class="level-name">Maestro Buku</span>
                                </div>
                                <span class="level-books">351-500 buku</span>
                            </div>
                            <div
                                class="level-item <?php echo $booksCompleted > 500 && $booksCompleted <= 750 ? 'current' : ($booksCompleted > 750 ? 'completed' : ''); ?>">
                                <div class="level-info">
                                    <i class="fas fa-brain"></i>
                                    <span class="level-name">Filsuf Pustaka</span>
                                </div>
                                <span class="level-books">501-750 buku</span>
                            </div>
                            <div class="level-item <?php echo $booksCompleted > 750 ? 'current' : ''; ?>">
                                <div class="level-info">
                                    <i class="fas fa-star"></i>
                                    <span class="level-name">Legenda Literasi</span>
                                </div>
                                <span class="level-books">>750 buku</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../layouts/footer.php'; ?>
</body>

</html>