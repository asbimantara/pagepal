<?php
ob_start();
session_start();
require_once '../config/database.php';

// Try to load Cloudinary, but have fallback for local storage
$useCloudinary = false;
try {
    require_once '../config/cloudinary.php';
    $useCloudinary = true;
} catch (Exception $e) {
    $useCloudinary = false;
}

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
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];

    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload JPG, PNG or WEBP']);
        exit();
    }

    $coverUrl = '';

    // Try Cloudinary first
    if ($useCloudinary && function_exists('uploadToCloudinary')) {
        $uploadResult = uploadToCloudinary($file['tmp_name'], 'covers');

        if ($uploadResult['success']) {
            $coverUrl = $uploadResult['url'];
        }
    }

    // Fallback to local storage if Cloudinary fails or not available
    if (empty($coverUrl)) {
        $uploadDir = '../uploads/covers/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($file['name']));
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $coverUrl = 'uploads/covers/' . $fileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save file']);
            exit();
        }
    }

    // Save to database
    $result = $database->users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$set' => ["books.$bookIndex.cover_url" => $coverUrl]]
    );

    echo json_encode([
        'success' => true,
        'cover_url' => $coverUrl,
        'message' => 'Cover berhasil diupdate'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>