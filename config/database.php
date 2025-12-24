<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables only if .env file exists (local development)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

try {
    // Gunakan environment variable (from .env or from server environment like Render)
    $mongoUri = getenv('MONGODB_URI') ?: $_ENV['MONGODB_URI'] ?? 'mongodb://localhost:27017';
    $dbName = getenv('DB_NAME') ?: $_ENV['DB_NAME'] ?? 'bookTracker';

    $mongoClient = new MongoDB\Client($mongoUri);
    $database = $mongoClient->$dbName;
} catch (Exception $e) {
    throw new Exception("Error koneksi ke MongoDB: " . $e->getMessage());
}
?>