<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
    $database = $mongoClient->bookTracker;
} catch (Exception $e) {
    die("Error koneksi ke MongoDB: " . $e->getMessage());
}
?> 