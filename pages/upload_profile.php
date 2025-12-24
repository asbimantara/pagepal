<?php
ob_start();
session_start();
require_once '../config/database.php';
require_once '../config/cloudinary.php';

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

    // Upload to Cloudinary
    $uploadResult = uploadToCloudinary($file['tmp_name'], 'profiles');

    if ($uploadResult['success']) {
        $imageUrl = $uploadResult['url'];

        $result = $database->users->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
            ['$set' => ['profile_picture' => $imageUrl]]
        );

        if ($result->getModifiedCount() > 0 || $result->getMatchedCount() > 0) {
            echo json_encode([
                'success' => true,
                'image_url' => $imageUrl,
                'message' => 'Profile picture updated successfully'
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