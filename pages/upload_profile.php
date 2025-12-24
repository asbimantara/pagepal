<?php
ob_start();
session_start();
require_once '../config/database.php';

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
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Please upload JPG or PNG']);
        exit();
    }

    $uploadDir = '../uploads/profiles/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $imageUrl = 'uploads/profiles/' . $fileName;

        $result = $database->users->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
            ['$set' => ['profile_picture' => $imageUrl]]
        );

        if ($result->getModifiedCount() > 0) {
            echo json_encode([
                'success' => true,
                'image_url' => $imageUrl,
                'message' => 'Profile picture updated successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
    } else {
        $uploadError = error_get_last();
        echo json_encode([
            'success' => false,
            'message' => 'Gagal upload file: ' . $uploadError['message']
        ]);
        exit();
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>