<?php
session_start();

function generateCaptcha() {
    // Generate random CAPTCHA code
    $captcha_code = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);
    $_SESSION['captcha_code'] = $captcha_code;

    // Create CAPTCHA image
    $image = imagecreatetruecolor(200, 50);
    $background_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 0, 0);
    imagefilledrectangle($image, 0, 0, 200, 50, $background_color);
    imagestring($image, 5, 50, 15, $captcha_code, $text_color);
    
    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
}

if (isset($_GET['refresh'])) {
    generateCaptcha();
    exit; // Stop further processing
}

// If not refreshing, just serve the CAPTCHA image
generateCaptcha();
