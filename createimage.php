<?php
//header("Content-type: image/png");
$im = @imagecreate(600, 480)
   or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 255, 255, 255);
$text_color = imagecolorallocate($im, 233, 14, 91);
$red = array(219, 204, 170, 0,0,0,0);
$green = array(0,0,0,0, 128, 170, 219);
$strings = array(">=8", "5", "2", "0", "-2", "-5", "<=-8");
//imagestring($im, 1, 5, 5,  " Text String2", $text_color);
$trans_x = 210; $trans_y = 25;
$squaresize = 10;
for($sq = 0; $sq < 7; $sq++){
	$rect_color = imagecolorallocate($im, $red[$sq], $green[$sq], 0);
	$y = $trans_y + ($sq * $squaresize);
	imagefilledrectangle($im, $trans_x,$y,$trans_x + $squaresize,$y + $squaresize,  $rect_color);
	imagestring($im, 2, $trans_x + $squaresize + 10, $y-2, $strings[$sq], $text_color);
}
// The text to draw
$text = 'Testing...';
// Replace path by your own font path
//echo "$text";
$font = 'arial.ttf';

// Add some shadow to the text
//imagettftext($im, 20, 0, 11, 21, $grey, $font, $text);
imagepng($im);
imagepng($im, "image.png");
imagedestroy($im);
?>