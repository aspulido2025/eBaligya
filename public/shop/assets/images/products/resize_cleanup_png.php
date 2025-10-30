<?php
$dir = __DIR__ . '/';
$images = glob($dir . '*.png');
foreach ($images as $img) {
    $data = @imagecreatefrompng($img);
    if ($data) {
        imagepng($data, $img, 9);
        imagedestroy($data);
        echo "Cleaned: " . basename($img) . "\n";
    } else {
        echo "Skipped (corrupt): " . basename($img) . "\n";
    }
}
