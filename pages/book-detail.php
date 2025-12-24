<?php
ob_start();
session_start();
date_default_timezone_set('Asia/Jakarta');
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$bookIndex = isset($_GET['index']) ? intval($_GET['index']) : -1;

// Ambil data buku
$user = $database->users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
if (!isset($user->books[$bookIndex])) {
    header("Location: my-books.php");
    exit();
}

$book = $user->books[$bookIndex];

// Tambahkan pengecekan jumlah catatan
$maxNotes = 3;
$currentNoteCount = isset($book->notes) ? count($book->notes) : 0;

// Handle penambahan catatan baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['note'])) {
    if ($currentNoteCount >= $maxNotes) {
        header("Location: book-detail.php?index=" . $bookIndex . "&error=max_notes");
        exit();
    }

    $newNote = [
        'content' => filter_var($_POST['note'], FILTER_SANITIZE_STRING),
        'page' => intval($_POST['page']),
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];

    $database->users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$push' => ["books.$bookIndex.notes" => $newNote]]
    );

    header("Location: book-detail.php?index=" . $bookIndex);
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book->title); ?> - Book Tracker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/book-detail.css">
</head>

<body>
    <?php include '../layouts/header.php'; ?>

    <div class="container">
        <?php if (isset($_GET['cover_success'])): ?>
            <div class="alert alert-success alert-dismissible">
                <i class="fas fa-check-circle"></i>
                Cover buku berhasil diperbarui!
                <button type="button" class="close-alert" onclick="this.parentElement.remove();">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['cover_error'])): ?>
            <div class="alert alert-error alert-dismissible">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_GET['cover_error']); ?>
                <button type="button" class="close-alert" onclick="this.parentElement.remove();">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <div class="book-detail">
            <div class="book-header">
                <div class="book-cover" onclick="document.getElementById('coverInput').click()">
                    <img src="<?php echo htmlspecialchars($book->cover_url); ?>"
                        alt="<?php echo htmlspecialchars($book->title); ?>">
                    <div class="cover-edit-overlay">
                        <button class="edit-cover-btn">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <input type="file" id="coverInput" accept="image/jpeg,image/png,image/jpg"
                        onchange="updateCover(this)" style="display: none;">
                    <small style="display: block; text-align: center; color: #666; margin-top: 8px; font-size: 0.8rem;">
                        <i class="fas fa-info-circle"></i> Klik untuk ganti cover (Maks. 5MB)
                    </small>
                </div>
                <div class="book-info">
                    <h1><?php echo htmlspecialchars($book->title); ?></h1>
                    <p class="author">oleh <?php echo htmlspecialchars($book->author); ?></p>

                    <p class="book-date">
                        <i class="far fa-clock"></i>
                        <?php
                        $date = $book->added_date->toDateTime();
                        echo $date->format('d M Y H:i');
                        ?>
                    </p>

                    <div class="progress-info">
                        <div class="progress-bar">
                            <div class="progress"
                                style="width: <?php echo ($book->current_page / $book->total_pages) * 100; ?>%"></div>
                        </div>
                        <p>Halaman <?php echo $book->current_page; ?> dari <?php echo $book->total_pages; ?></p>

                        <?php if ($book->current_page == $book->total_pages): ?>
                            <?php
                            $startDate = $book->added_date->toDateTime();
                            $finishDate = isset($book->last_read) ? $book->last_read->toDateTime() : new DateTime();
                            $interval = $startDate->diff($finishDate);
                            ?>
                            <div class="completion-info">
                                <i class="far fa-clock"></i>
                                <?php
                                if ($interval->days == 0) {
                                    echo "Diselesaikan hari ini";
                                } else if ($interval->days == 1) {
                                    echo "Diselesaikan dalam 1 hari pada tanggal " . $finishDate->format('d M Y');
                                } else {
                                    echo "Diselesaikan dalam " . $interval->days . " hari pada tanggal " . $finishDate->format('d M Y');
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button onclick="window.location.href='update-progress.php?index=<?php echo $bookIndex; ?>'"
                        class="btn-primary">
                        Update Progress
                    </button>
                </div>
            </div>

            <div class="book-notes">
                <h2>Catatan Membaca</h2>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php
                        if ($_GET['success'] === 'edit') {
                            echo "Catatan berhasil diperbarui!";
                        } else if ($_GET['success'] === 'delete') {
                            echo "Catatan berhasil dihapus!";
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error">
                        <?php
                        if ($_GET['error'] === 'edit') {
                            echo "Gagal memperbarui catatan. Silakan coba lagi.";
                        } else if ($_GET['error'] === 'delete') {
                            echo "Gagal menghapus catatan. Silakan coba lagi.";
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error']) && $_GET['error'] === 'max_notes'): ?>
                    <div class="alert alert-error">
                        Anda telah mencapai batas maksimal 3 catatan. Silakan hapus catatan lama untuk menambah catatan
                        baru.
                    </div>
                <?php endif; ?>

                <div class="notes-counter">
                    Catatan: <?php echo $currentNoteCount; ?>/<?php echo $maxNotes; ?>
                </div>

                <?php if ($currentNoteCount < $maxNotes): ?>
                    <form method="POST" class="note-form">
                        <div class="form-group">
                            <label for="note">Tambah Catatan Baru</label>
                            <textarea id="note" name="note" required maxlength="500" oninput="updateCharacterCount(this)"
                                placeholder="Tulis catatan Anda di sini..."></textarea>
                            <small class="character-count">0/500 karakter</small>
                        </div>
                        <div class="form-group">
                            <label for="page">Halaman</label>
                            <div class="page-input-container">
                                <input type="number" id="page" name="page" required min="1"
                                    max="<?php echo $book->total_pages; ?>" oninput="validatePageNumber(this)" value="1">
                                <small class="page-info">dari <?php echo $book->total_pages; ?> halaman</small>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Simpan Catatan</button>
                    </form>
                <?php else: ?>
                    <div class="max-notes-warning">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Anda telah mencapai batas maksimal catatan. Silakan hapus catatan yang ada untuk menambah catatan
                            baru.</p>
                    </div>
                <?php endif; ?>

                <div class="notes-list">
                    <?php if (!empty($book->notes)): ?>
                        <?php foreach ($book->notes as $noteIndex => $note): ?>
                            <div class="note-card">
                                <div class="note-header">
                                    <span class="page-number">Halaman <?php echo $note->page; ?></span>
                                    <div class="note-actions">
                                        <button
                                            onclick="editNote(<?php echo $noteIndex; ?>, '<?php echo htmlspecialchars(addslashes($note->content)); ?>', <?php echo $note->page; ?>)"
                                            class="btn-icon" title="Edit catatan">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteNote(<?php echo $noteIndex; ?>)" class="btn-icon btn-icon-danger"
                                            title="Hapus catatan">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="note-content"><?php echo nl2br(htmlspecialchars($note->content)); ?></div>
                                <?php if (strlen($note->content) > 200): ?>
                                    <button class="note-content-toggle" onclick="toggleNoteContent(this)">Lihat
                                        Selengkapnya</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-notes">Belum ada catatan untuk buku ini</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="editNoteModal" class="modal">
        <div class="modal-content">
            <h2>Edit Catatan</h2>
            <form id="editNoteForm" method="POST" action="edit-note.php">
                <input type="hidden" name="book_index" value="<?php echo $bookIndex; ?>">
                <input type="hidden" name="note_index" id="editNoteIndex">
                <div class="form-group">
                    <label for="editNoteContent">Catatan</label>
                    <textarea id="editNoteContent" name="content" required></textarea>
                </div>
                <div class="form-group">
                    <label for="editNotePage">Halaman</label>
                    <input type="number" id="editNotePage" name="page" required min="1"
                        max="<?php echo $book->total_pages; ?>">
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeEditModal()" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteNoteModal" class="modal">
        <div class="modal-content">
            <h2>Hapus Catatan</h2>
            <p>Apakah Anda yakin ingin menghapus catatan ini?</p>
            <form id="deleteNoteForm" method="POST" action="delete-note.php">
                <input type="hidden" name="book_index" value="<?php echo $bookIndex; ?>">
                <input type="hidden" name="note_index" id="deleteNoteIndex">
                <div class="form-actions">
                    <button type="button" onclick="closeDeleteModal()" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editNote(noteIndex, content, page) {
            document.getElementById('editNoteIndex').value = noteIndex;
            document.getElementById('editNoteContent').value = content;
            document.getElementById('editNotePage').value = page;
            document.getElementById('editNoteModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editNoteModal').style.display = 'none';
        }

        function deleteNote(noteIndex) {
            document.getElementById('deleteNoteIndex').value = noteIndex;
            document.getElementById('deleteNoteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteNoteModal').style.display = 'none';
        }

        // Tutup modal jika user klik di luar modal
        window.onclick = function (event) {
            const editModal = document.getElementById('editNoteModal');
            const deleteModal = document.getElementById('deleteNoteModal');
            if (event.target == editModal) {
                closeEditModal();
            }
            if (event.target == deleteModal) {
                closeDeleteModal();
            }
        }

        function updateCharacterCount(textarea) {
            const maxLength = textarea.getAttribute('maxlength');
            const currentLength = textarea.value.length;
            const counter = textarea.nextElementSibling;

            counter.textContent = `${currentLength}/${maxLength} karakter`;

            if (currentLength >= maxLength) {
                counter.style.color = '#dc3545';
            } else {
                counter.style.color = '#666';
            }
        }

        function validatePageNumber(input) {
            const max = parseInt(input.getAttribute('max'));
            const min = parseInt(input.getAttribute('min'));
            let value = parseInt(input.value);

            // Hapus karakter non-digit
            input.value = input.value.replace(/[^\d]/g, '');

            // Validasi nilai
            if (isNaN(value)) value = min;
            if (value > max) input.value = max;
            if (value < min) input.value = min;
        }

        // Mencegah input karakter e, E, +, - di input number
        document.getElementById('page').addEventListener('keydown', function (e) {
            if (['e', 'E', '+', '-'].includes(e.key)) {
                e.preventDefault();
            }
        });

        // Mencegah paste teks yang tidak valid
        document.getElementById('page').addEventListener('paste', function (e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (/^\d+$/.test(pastedText)) {
                const value = Math.min(parseInt(pastedText), parseInt(this.getAttribute('max')));
                this.value = value;
            }
        });

        function toggleNoteContent(button) {
            const content = button.previousElementSibling;
            content.classList.toggle('expanded');
            button.textContent = content.classList.contains('expanded') ? 'Lihat Lebih Sedikit' : 'Lihat Selengkapnya';
        }

        // Tambahkan fungsi untuk mengecek apakah konten perlu tombol toggle
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.note-content').forEach(content => {
                if (content.scrollHeight > content.clientHeight) {
                    const button = content.nextElementSibling;
                    if (button && button.classList.contains('note-content-toggle')) {
                        button.style.display = 'block';
                    }
                }
            });
        });

        function updateCover(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validasi ukuran file (max 5MB)
                const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                if (file.size > maxSize) {
                    const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    alert(`File terlalu besar (${sizeMB}MB). Maksimal 5MB!\n\nSilahkan kompres gambar terlebih dahulu.`);
                    input.value = '';
                    return;
                }

                // Validasi tipe file
                if (!file.type.startsWith('image/')) {
                    alert('Mohon upload file gambar (JPG, PNG)');
                    input.value = '';
                    return;
                }

                const formData = new FormData();
                formData.append('cover', file);
                formData.append('book_index', '<?php echo $bookIndex; ?>');

                // Tampilkan loading state
                const coverImg = input.parentElement.querySelector('img');
                coverImg.style.opacity = '0.5';

                fetch('update-cover.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Refresh gambar cover
                            coverImg.src = data.cover_url + '?t=' + new Date().getTime();
                            coverImg.style.opacity = '1';

                            // Redirect dengan parameter success
                            window.location.href = `book-detail.php?index=<?php echo $bookIndex; ?>&cover_success=1`;
                        } else {
                            // Redirect dengan parameter error
                            coverImg.style.opacity = '1';
                            window.location.href = `book-detail.php?index=<?php echo $bookIndex; ?>&cover_error=${encodeURIComponent(data.message)}`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        coverImg.style.opacity = '1';
                        window.location.href = `book-detail.php?index=<?php echo $bookIndex; ?>&cover_error=Terjadi kesalahan saat mengupdate cover`;
                    });
            }
        }

        // Otomatis hilangkan alert setelah beberapa detik
        document.addEventListener('DOMContentLoaded', function () {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
    </script>

    <?php include '../layouts/footer.php'; ?>
</body>

</html>