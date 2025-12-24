<?php
ob_start();
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['index'])) {
    header("Location: my-books.php");
    exit();
}

$bookIndex = intval($_POST['index']);

try {
    // Hapus buku dari array books
    $result = $database->users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$unset' => ["books.{$bookIndex}" => 1]]
    );

    // Reindex array untuk menghilangkan gap
    $database->users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$pull' => ['books' => null]]
    );

    header("Location: my-books.php");
} catch (Exception $e) {
    header("Location: my-books.php?error=delete_failed");
}
exit();