<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// Configure Cloudinary using environment variables
$cloudName = getenv('CLOUDINARY_CLOUD_NAME') ?: $_ENV['CLOUDINARY_CLOUD_NAME'] ?? 'dn5mavjts';
$apiKey = getenv('CLOUDINARY_API_KEY') ?: $_ENV['CLOUDINARY_API_KEY'] ?? '933595762292283';
$apiSecret = getenv('CLOUDINARY_API_SECRET') ?: $_ENV['CLOUDINARY_API_SECRET'] ?? '5b52qteNA9wIAicbTVloIMeI73g';

Configuration::instance([
    'cloud' => [
        'cloud_name' => $cloudName,
        'api_key' => $apiKey,
        'api_secret' => $apiSecret
    ],
    'url' => [
        'secure' => true
    ]
]);

/**
 * Upload image to Cloudinary
 * @param string $filePath - Local file path or URL
 * @param string $folder - Folder in Cloudinary (e.g., 'profiles', 'covers')
 * @return array - ['success' => bool, 'url' => string, 'public_id' => string, 'error' => string]
 */
function uploadToCloudinary($filePath, $folder = 'pagepal')
{
    try {
        $upload = new UploadApi();
        $result = $upload->upload($filePath, [
            'folder' => 'pagepal/' . $folder,
            'resource_type' => 'image',
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ]
        ]);

        return [
            'success' => true,
            'url' => $result['secure_url'],
            'public_id' => $result['public_id']
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
?>