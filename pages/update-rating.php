<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['book_index']) || !isset($_POST['rating'])) {
    echo json_encode(['success' => false]);
    exit();
}

$bookIndex = intval($_POST['book_index']);
$rating = intval($_POST['rating']);

// Validasi rating
if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false]);
    exit();
}

try {
    $result = $database->users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$set' => ["books.$bookIndex.rating" => $rating]]
    );

    echo json_encode([
        'success' => $result->getModifiedCount() > 0,
        'rating' => $rating
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false]);
} 