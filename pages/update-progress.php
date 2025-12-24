<?php
ob_start();
session_start();
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPage = intval($_POST['current_page']);

    // Cek apakah ini adalah konfirmasi dari warning
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'true') {
        // Proses update langsung karena sudah dikonfirmasi
        try {
            // Update status buku
            $status = 'belum_mulai';
            if ($currentPage > 0 && $currentPage < $book->total_pages) {
                $status = 'sedang_dibaca';
            } else if ($currentPage == $book->total_pages) {
                $status = 'selesai';
            }

            $result = $database->users->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
                [
                    '$set' => [
                        "books.$bookIndex.current_page" => $currentPage,
                        "books.$bookIndex.status" => $status,
                        "books.$bookIndex.last_read" => new MongoDB\BSON\UTCDateTime()
                    ]
                ]
            );

            header("Location: book-detail.php?index=" . $bookIndex);
            exit();
        } catch (Exception $e) {
            $error = "Gagal mengupdate progress";
        }
    }
    // Jika bukan konfirmasi, cek apakah perlu warning
    else if ($currentPage < $book->current_page) {
        $error = "Perhatian: Halaman yang Anda masukkan (" . $currentPage . ") lebih kecil dari progress sebelumnya (" . $book->current_page . "). 
                 Apakah Anda yakin ingin mengubah progress membaca?";
        $warning = true;
    } else if ($currentPage >= 0 && $currentPage <= $book->total_pages) {
        // Proses normal untuk update yang tidak memerlukan konfirmasi
        try {
            $status = 'belum_mulai';
            if ($currentPage > 0 && $currentPage < $book->total_pages) {
                $status = 'sedang_dibaca';
            } else if ($currentPage == $book->total_pages) {
                $status = 'selesai';
            }

            $result = $database->users->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
                [
                    '$set' => [
                        "books.$bookIndex.current_page" => $currentPage,
                        "books.$bookIndex.status" => $status,
                        "books.$bookIndex.last_read" => new MongoDB\BSON\UTCDateTime()
                    ]
                ]
            );

            header("Location: book-detail.php?index=" . $bookIndex);
            exit();
        } catch (Exception $e) {
            $error = "Gagal mengupdate progress";
        }
    } else {
        $error = "Halaman tidak valid";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Progress - <?php echo htmlspecialchars($book->title); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/form.css">
</head>

<body>
    <?php include '../layouts/header.php'; ?>

    <div class="container">
        <div class="form-card">
            <h1>Update Progress Membaca</h1>
            <h2 class="book-title"><?php echo htmlspecialchars($book->title); ?></h2>

            <?php if (isset($error)): ?>
                <div class="alert <?php echo isset($warning) ? 'alert-warning' : 'alert-error'; ?>">
                    <?php echo $error; ?>
                    <?php if (isset($warning)): ?>
                        <form method="POST" class="confirm-form">
                            <input type="hidden" name="current_page" value="<?php echo $currentPage; ?>">
                            <input type="hidden" name="confirm" value="true">
                            <div class="form-actions warning-actions">
                                <button type="button" onclick="history.back()" class="btn-secondary">Kembali</button>
                                <button type="submit" class="btn-warning">Ya, Saya Yakin</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!isset($warning)): ?>
                <form method="POST" class="progress-form">
                    <div class="form-group">
                        <label for="current_page">Halaman Saat Ini</label>
                        <div class="progress-container">
                            <input type="number" id="current_page" name="current_page" required min="0"
                                max="<?php echo $book->total_pages; ?>" value="<?php echo $book->current_page; ?>"
                                oninput="validateProgress(this)">
                            <small class="form-text">dari <?php echo $book->total_pages; ?> halaman</small>
                        </div>
                        <small class="form-text text-info">Progress terakhir: Halaman
                            <?php echo $book->current_page; ?></small>
                    </div>

                    <div class="form-actions">
                        <a href="book-detail.php?index=<?php echo $bookIndex; ?>" class="btn-secondary">Batal</a>
                        <button type="submit" class="btn-primary">Simpan Progress</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../layouts/footer.php'; ?>

    <script>
        function validateProgress(input) {
            const max = parseInt(input.getAttribute('max'));
            const value = input.value;

            // Hapus karakter non-digit
            input.value = value.replace(/[^\d]/g, '');

            // Batasi panjang input sesuai dengan max
            if (parseInt(value) > max) {
                input.value = max;
            }

            // Mencegah input negatif
            if (parseInt(value) < 0) {
                input.value = 0;
            }
        }

        // Mencegah input karakter e, E, +, -
        document.getElementById('current_page').addEventListener('keydown', function (e) {
            if (['e', 'E', '+', '-'].includes(e.key)) {
                e.preventDefault();
            }
        });

        // Mencegah paste teks yang tidak valid
        document.getElementById('current_page').addEventListener('paste', function (e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            if (/^\d+$/.test(pastedText)) {
                const value = parseInt(pastedText);
                const max = parseInt(this.getAttribute('max'));
                this.value = Math.min(value, max);
            }
        });
    </script>
</body>

</html>