<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user dan buku-bukunya
$user = $database->users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
$books = $user->books ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PagePal - My Books</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/books.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="../assets/js/books.js" defer></script>
</head>
<body>
    <?php include '../layouts/header.php'; ?>

    <div class="container">
        <div class="books-header">
            <div class="books-header-top">
                <h1>Koleksi Buku Saya</h1>
                <a href="add-book.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Tambah Buku
                </a>
            </div>
        </div>

        <?php if (empty($books)): ?>
            <div class="empty-state">
                <img src="../assets/images/empty-books.svg" alt="Tidak ada buku">
                <h2>Belum Ada Buku</h2>
                <p>Mulai tambahkan buku yang ingin Anda baca</p>
            </div>
        <?php else: ?>
            <div class="books-wrapper">
                <div class="search-and-filter">
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchBook" placeholder="Cari judul buku atau penulis...">
                    </div>
                    <div class="filter-container">
                        <button id="filterButton" class="filter-btn">
                            <i class="fas fa-sliders-h"></i>
                            <span>Filter</span>
                        </button>
                        <div id="filterDropdown" class="filter-dropdown">
                            <div class="filter-option" data-status="all">
                                <i class="fas fa-books"></i> Semua Buku
                            </div>
                            <div class="filter-option" data-status="belum_mulai">
                                <i class="fas fa-hourglass-start"></i> Belum Mulai
                            </div>
                            <div class="filter-option" data-status="sedang_dibaca">
                                <i class="fas fa-book-reader"></i> Sedang Dibaca
                            </div>
                            <div class="filter-option" data-status="selesai">
                                <i class="fas fa-check-circle"></i> Selesai
                            </div>
                        </div>
                    </div>
                </div>
                <div class="books-grid">
                    <?php foreach ($books as $index => $book): ?>
                        <div class="book-card active" data-status="<?php echo htmlspecialchars($book->status); ?>">
                            <div class="book-content">
                                <div class="book-cover">
                                    <img src="<?php echo htmlspecialchars($book->cover_url); ?>" 
                                         alt="<?php echo htmlspecialchars($book->title); ?>">
                                    <div class="book-progress">
                                        <?php 
                                        $progress = ($book->current_page / $book->total_pages) * 100;
                                        echo round($progress) . '%';
                                        ?>
                                    </div>
                                </div>
                                <div class="book-info">
                                    <h3><?php echo htmlspecialchars($book->title); ?></h3>
                                    <p class="author"><?php echo htmlspecialchars($book->author); ?></p>
                                    <div class="status-badge <?php echo $book->status; ?>">
                                        <?php 
                                        $status_text = [
                                            'belum_mulai' => 'Belum Mulai',
                                            'sedang_dibaca' => 'Sedang Dibaca',
                                            'selesai' => 'Selesai'
                                        ];
                                        echo $status_text[$book->status];
                                        ?>
                                    </div>
                                </div>
                                <div class="book-actions">
                                    <a href="book-detail.php?index=<?php echo $index; ?>" class="btn-secondary">
                                        Update
                                    </a>
                                    <button onclick="deleteBook(<?php echo $index; ?>)" class="btn-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../layouts/footer.php'; ?>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Konfirmasi Hapus</h2>
            <p>Apakah Anda yakin ingin menghapus buku ini?</p>
            <form id="deleteForm" method="POST" action="delete-book.php">
                <input type="hidden" name="index" id="deleteIndex">
                <div class="form-actions">
                    <button type="button" onclick="closeDeleteModal()" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function searchBooks() {
        const searchText = document.getElementById('searchBook').value.toLowerCase();
        const bookCards = document.querySelectorAll('.book-card');
        
        bookCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const author = card.querySelector('.author').textContent.toLowerCase();
            const status = card.querySelector('.status-badge').textContent.toLowerCase();
            
            if (title.includes(searchText) || 
                author.includes(searchText) || 
                status.includes(searchText)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    const deleteModal = document.getElementById('deleteModal');
    
    function deleteBook(index) {
        document.getElementById('deleteIndex').value = index;
        deleteModal.style.display = 'block';
    }
    
    function closeDeleteModal() {
        deleteModal.style.display = 'none';
    }
    
    // Tutup modal jika user klik di luar modal
    window.onclick = function(event) {
        if (event.target == deleteModal) {
            closeDeleteModal();
        }
    }
    </script>
</body>
</html>