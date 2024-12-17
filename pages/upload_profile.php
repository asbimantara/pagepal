<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Tipe file tidak didukung']);
        exit();
    }
    
    // Buat direktori uploads jika belum ada
    $uploadDir = '../uploads/profile_pictures/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate nama file unik
    $fileName = $_SESSION['user_id'] . '_' . time() . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Update path gambar di database
        try {
            $database->users->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
                ['$set' => ['profile_picture' => 'uploads/profile_pictures/' . $fileName]]
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Foto profil berhasil diupload',
                'path' => 'uploads/profile_pictures/' . $fileName
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate database']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupload file']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
} 