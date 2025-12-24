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

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    if (!isset($_FILES['profile_picture'])) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        exit();
    }

    $file = $_FILES['profile_picture'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];

    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload an image']);
        exit();
    }

    $imageUrl = '';

    // Try Cloudinary first
    if ($useCloudinary && function_exists('uploadToCloudinary')) {
        $uploadResult = uploadToCloudinary($file['tmp_name'], 'profiles');

        if ($uploadResult['success']) {
            $imageUrl = $uploadResult['url'];
        }
    }

    // Fallback to local storage if Cloudinary fails or not available
    if (empty($imageUrl)) {
        $uploadDir = '../uploads/profiles/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($file['name']));
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $imageUrl = 'uploads/profiles/' . $fileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save file']);
            exit();
        }
    }

    // Save to database
    $result = $database->users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$set' => ['profile_picture' => $imageUrl]]
    );

    echo json_encode([
        'success' => true,
        'image_url' => $imageUrl,
        'message' => 'Profile picture updated successfully'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>