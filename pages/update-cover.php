<?php
ob_start();
session_start();
require_once '../config/database.php';
require_once '../config/cloudinary.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['book_index'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $bookIndex = intval($_POST['book_index']);

    if (!isset($_FILES['cover'])) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        exit();
    }

    $file = $_FILES['cover'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload JPG or PNG']);
        exit();
    }

    // Upload to Cloudinary
    $uploadResult = uploadToCloudinary($file['tmp_name'], 'covers');

    if ($uploadResult['success']) {
        $coverUrl = $uploadResult['url'];

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
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload: ' . $uploadResult['error']]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>