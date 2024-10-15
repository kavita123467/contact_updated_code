<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Function to generate CAPTCHA code
function generateCaptchaCode($length = 6): string {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $captcha_code = '';
    for ($i = 0; $i < $length; $i++) {
        $captcha_code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $captcha_code;
}

// Function to create CAPTCHA image
function createCaptchaImage($code) {
    $image = imagecreatetruecolor(200, 50);
    $background_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 0, 0);
    imagefilledrectangle($image, 0, 0, 200, 50, $background_color);
    
    // Add some noise to the image
    for ($i = 0; $i < 1000; $i++) {
        $color = imagecolorallocate($image, rand(200, 255), rand(200, 255), rand(200, 255));
        imagesetpixel($image, rand(0, 199), rand(0, 49), $color);
    }
    
    // Add the text
    $font = 5; // Built-in font (1-5)
    $x = 30;
    for ($i = 0; $i < strlen($code); $i++) {
        $y = rand(15, 35);
        imagechar($image, $font, $x, $y, $code[$i], $text_color);
        $x += 25;
    }
    
    return $image;
}

// Generate new CAPTCHA
function generateNewCaptcha() {
    $captcha_code = generateCaptchaCode();
    $_SESSION['captcha'] = $captcha_code;
    $_SESSION['captcha_expires'] = time() + 120; // 2 minutes expiration
    
    return $captcha_code;
}

// Generate and display a new CAPTCHA image
$captcha_code = generateNewCaptcha();
$image = createCaptchaImage($captcha_code);
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
