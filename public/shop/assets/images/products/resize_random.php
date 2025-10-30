<?php
/**
 * Random image resizer with caching.
 * Picks a random product image and random allowed size.
 * Folder: shop/assets/images/products/
 */

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

// 🚫 Never start output buffering unless needed
if (ob_get_length()) ob_end_clean();

$path = __DIR__ . '/';

// --- Step 1: Pick a random image ---
$images = glob($path . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);
if (!$images) {
    header('Content-Type: image/png');
    readfile(__DIR__ . '/default.png');
    exit;
}

$src  = $images[array_rand($images)];
$file = basename($src);

// --- Step 2: Pick a random size ---
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
[$width, $height] = $allowed_sizes[array_rand($allowed_sizes)];

// --- Step 3: Cache ---
$cache_dir = $path . 'cache/';
if (!is_dir($cache_dir)) mkdir($cache_dir, 0755, true);

$ext = pathinfo($file, PATHINFO_EXTENSION);
$cache_file = sprintf('%s%s_%dx%d.%s',
    $cache_dir,
    pathinfo($file, PATHINFO_FILENAME),
    $width, $height,
    strtolower($ext)
);

if (file_exists($cache_file)) {
    // Serve cached version
    header('Content-Type: image/' . ($ext === 'jpg' ? 'jpeg' : $ext));
    readfile($cache_file);
    exit;
}

// --- Step 4: Resize ---
[$orig_w, $orig_h, $type] = getimagesize($src);
switch ($type) {
    case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($src); break;
    case IMAGETYPE_PNG:  $image = imagecreatefrompng($src);  break;
    case IMAGETYPE_WEBP: $image = imagecreatefromwebp($src); break;
    default:
        http_response_code(415);
        exit('Unsupported image type');
}

$image_p = imagecreatetruecolor($width, $height);
if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
    imagealphablending($image_p, false);
    imagesavealpha($image_p, true);
    $transparent = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
    imagefilledrectangle($image_p, 0, 0, $width, $height, $transparent);
}

imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $orig_w, $orig_h);

// --- Step 5: Save + Output ---
switch ($type) {
    case IMAGETYPE_JPEG:
        header('Content-Type: image/jpeg');
        imagejpeg($image_p, $cache_file, 90);
        imagejpeg($image_p, null, 90);
        break;
    case IMAGETYPE_PNG:
        header('Content-Type: image/png');
        imagepng($image_p, $cache_file);
        imagepng($image_p);
        break;
    case IMAGETYPE_WEBP:
        header('Content-Type: image/webp');
        imagewebp($image_p, $cache_file, 90);
        imagewebp($image_p, null, 90);
        break;
}

imagedestroy($image_p);
imagedestroy($image);
exit;
