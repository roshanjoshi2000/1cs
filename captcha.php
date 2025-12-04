<?php
session_start();

header('Content-Type: image/png');

$code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
$_SESSION['captcha_code'] = $code;

$image = imagecreatetruecolor(120, 40);
$bg = imagecolorallocate($image, 240, 240, 240);
$txt = imagecolorallocate($image, 0, 0, 0);

imagefilledrectangle($image, 0, 0, 200, 50, $bg);
imagestring($image, 5, 20, 10, $code, $txt);

imagepng($image);
imagedestroy($image);
