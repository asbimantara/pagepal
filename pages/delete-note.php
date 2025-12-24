<?php
ob_start();
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['book_index']) || !isset($_POST['note_index'])) {
    header("Location: my-books.php");
    exit();
}

$bookIndex = intval($_POST['book_index']);
$noteIndex = intval($_POST['note_index']);

try {
    // Ambil data user dan buku
    $user = $database->users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
    if (!isset($user->books[$bookIndex]) || !isset($user->books[$bookIndex]->notes[$noteIndex])) {
        header("Location: my-books.php");
        exit();
    }

    // Hapus catatan menggunakan $pull
    $updatePath = "books.$bookIndex.notes";
    $result = $database->users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$unset' => ["$updatePath.$noteIndex" => 1]]
    );

    // Reindex array untuk menghilangkan gap
    $database->users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$pull' => ["$updatePath" => null]]
    );

    if ($result->getModifiedCount() > 0) {
        header("Location: book-detail.php?index=" . $bookIndex . "&success=delete");
    } else {
        header("Location: book-detail.php?index=" . $bookIndex . "&error=delete");
    }
} catch (Exception $e) {
    header("Location: book-detail.php?index=" . $bookIndex . "&error=delete");
}
exit();