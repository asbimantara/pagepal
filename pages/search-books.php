<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['query'])) {
    exit(json_encode(['results' => []]));
}

$searchText = $_GET['query'];

try {
    $userId = new MongoDB\BSON\ObjectId($_SESSION['user_id']);
    
    $pipeline = [
        [
            '$match' => ['_id' => $userId]
        ],
        [
            '$lookup' => [
                'from' => 'books',
                'localField' => 'book_ids',
                'foreignField' => '_id',
                'as' => 'books'
            ]
        ],
        [
            '$unwind' => '$books'
        ],
        [
            '$match' => [
                '$or' => [
                    ['books.title' => new MongoDB\BSON\Regex($searchText, 'i')],
                    ['books.author' => new MongoDB\BSON\Regex($searchText, 'i')],
                    ['books.status' => new MongoDB\BSON\Regex($searchText, 'i')]
                ]
            ]
        ],
        [
            '$project' => [
                'index' => '$books._id',
                'title' => '$books.title',
                'author' => '$books.author',
                'status' => '$books.status',
                'cover_url' => '$books.cover_url',
                'progress' => [
                    '$multiply' => [
                        ['$divide' => ['$books.current_page', '$books.total_pages']],
                        100
                    ]
                ]
            ]
        ]
    ];

    $results = $database->users->aggregate($pipeline)->toArray();

    echo json_encode(['results' => $results]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Terjadi kesalahan saat mencari buku: ' . $e->getMessage()]);
}