<?php
ob_start();
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['book_index']) || !isset($_POST['cover_url'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

try {
    $bookIndex = intval($_POST['book_index']);
    $coverUrl = $_POST['cover_url'];

    // Validate cover URL (must be a relative path to assets or a valid URL)
    if (empty($coverUrl)) {
        echo json_encode(['success' => false, 'message' => 'Cover URL is empty']);
        exit();
    }

    $result = $database->users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$set' => ["books.$bookIndex.cover_url" => $coverUrl]]
    );

    if ($result->getModifiedCount() > 0 || $result->getMatchedCount() > 0) {
        echo json_encode([
            'success' => true,
            'cover_url' => $coverUrl,
            'message' => 'Cover berhasil diupdate'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update database']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>