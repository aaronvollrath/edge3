<?php
header ('Content-Type: image/png');
$im = @imagecreatetruecolor(120, 20)
      or die('Cannot Initialize new GD image stream');
$text_color = imagecolorallocate($im, 233, 14, 91);
imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);
//imagettftext($im3, 8, 90, $x1, $y1, $text_color, $font, $data);
imagepng($im);
imagedestroy($im);
?>