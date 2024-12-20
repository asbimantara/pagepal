<?php
session_start();
require_once '../config/database.php';

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

    // Buat direktori jika belum ada
    $uploadDir = '../uploads/covers/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate nama file unik
    $fileName = uniqid() . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Update cover_url di database dengan path relatif yang benar
        $coverUrl = '../uploads/covers/' . $fileName;  // Ubah path ini
        
        $result = $database->users->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
            ['$set' => ["books.$bookIndex.cover_url" => $coverUrl]]
        );

        if ($result->getModifiedCount() > 0) {
            echo json_encode([
                'success' => true, 
                'cover_url' => $coverUrl,
                'message' => 'Cover berhasil diupdate'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 