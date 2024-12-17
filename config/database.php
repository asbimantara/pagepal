<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    // Gunakan environment variable
    $mongoUri = getenv('MONGODB_URI') ?: 'mongodb://localhost:27017';
    $dbName = getenv('DB_NAME') ?: 'bookTracker';
    
    $mongoClient = new MongoDB\Client($mongoUri);
    $database = $mongoClient->$dbName;
} catch (Exception $e) {
    die("Error koneksi ke MongoDB: " . $e->getMessage());
}
?> 