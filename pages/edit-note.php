<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['book_index']) || !isset($_POST['note_index'])) {
    header("Location: my-books.php");
    exit();
}

$bookIndex = intval($_POST['book_index']);
$noteIndex = intval($_POST['note_index']);
$content = filter_var($_POST['content'], FILTER_SANITIZE_STRING);
$page = intval($_POST['page']);

try {
    // Ambil data user dan buku
    $user = $database->users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
    if (!isset($user->books[$bookIndex]) || !isset($user->books[$bookIndex]->notes[$noteIndex])) {
        header("Location: my-books.php");
        exit();
    }

    // Update catatan
    $updatePath = "books.$bookIndex.notes.$noteIndex";
    $result = $database->users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$set' => [
            "$updatePath.content" => $content,
            "$updatePath.page" => $page,
            "$updatePath.updated_at" => new MongoDB\BSON\UTCDateTime()
        ]]
    );

    if ($result->getModifiedCount() > 0) {
        header("Location: book-detail.php?index=" . $bookIndex . "&success=edit");
    } else {
        header("Location: book-detail.php?index=" . $bookIndex . "&error=edit");
    }
} catch (Exception $e) {
    header("Location: book-detail.php?index=" . $bookIndex . "&error=edit");
}
exit();