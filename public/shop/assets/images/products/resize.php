<?php
/**
 * Dynamic image resizer with caching.
 * Generates multiple sizes from one source image.
 * Folder: shop/assets/images/products/
 */

$path = __DIR__ . '/shop/assets/images/products/';
$file = $_GET['file'] ?? 'default.jpg';
$width = (int)($_GET['w'] ?? 800);
$height = (int)($_GET['h'] ?? 500);

$src = realpath($path . basename($file));
if (!$src || !file_exists($src)) {
    http_response_code(404);
    exit('File not found');
}

// Allowed sizes for security & consistency
$allowed_sizes = [
    [100, 100],
    [180, 180],
    [220, 160],
    [335, 335],
    [370, 250],
    [510, 600],
    [620, 340],
    [730, 310],
    [800, 500],
];

$allowed = false;
foreach ($allowed_sizes as [$w, $h]) {
    if ($width === $w && $height === $h) {
        $allowed = true;
        break;
    }
}

if (!$allowed) {
    http_response_code(400);
    exit('Invalid size requested');
}

// Cache filename
$cache_dir = $path . 'cache/';
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0755, true);
}
$cache_file = $cache_dir . pathinfo($file, PATHINFO_FILENAME) . "_{$width}x{$height}.jpg";

// Serve cached version if it exists
if (file_exists($cache_file)) {
    header('Content-Type: image/jpeg');
    readfile($cache_file);
    exit;
}

// Resize process
list($orig_width, $orig_height, $type) = getimagesize($src);

switch ($type) {
    case IMAGETYPE_JPEG:
        $image = imagecreatefromjpeg($src);
        break;
    case IMAGETYPE_PNG:
        $image = imagecreatefrompng($src);
        break;
    case IMAGETYPE_WEBP:
        $image = imagecreatefromwebp($src);
        break;
    default:
        http_response_code(415);
        exit('Unsupported image type');
}

$image_p = imagecreatetruecolor($width, $height);

// Preserve transparency for PNG/WebP
if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
    imagealphablending($image_p, false);
    imagesavealpha($image_p, true);
    $transparent = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
    imagefilledrectangle($image_p, 0, 0, $width, $height, $transparent);
}

// Resize
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);

// Save cached version
imagejpeg($image_p, $cache_file, 90);

// Output to browser
header('Content-Type: image/jpeg');
imagejpeg($image_p, null, 90);

// Cleanup
imagedestroy($image_p);
imagedestroy($image);
?>
