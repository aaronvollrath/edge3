<?php
header("Content-type: image/png");
$file = $_GET['file'];
$file = "./IMAGES/image$file";
//$file = "svg154.svg";
$depth = array();
$x1 = 0;
$y1 = 0;

global $scale;
global $rotatetext;
function ImageRectangleWithRoundedCorners(&$im, $x1, $y1, $x2, $y2, $radius, $color) {
// draw rectangle without corners
imagerectangle($im, $x1+$radius, $y1, $x2-$radius, $y2, $color);
imagerectangle($im, $x1, $y1+$radius, $x2, $y2-$radius, $color);
// draw circled corners
imageellipse($im, $x1+$radius, $y1+$radius, $radius*2, $radius*2, $color);
imageellipse($im, $x2-$radius, $y1+$radius, $radius*2, $radius*2, $color);
imageellipse($im, $x1+$radius, $y2-$radius, $radius*2, $radius*2, $color);
imageellipse($im, $x2-$radius, $y2-$radius, $radius*2, $radius*2, $color);
}
function startElement($parser, $name, $attrs)
{
   global $depth;
   global $im3;
  global $translate_x;
   global $translate_y;
   global $rect_color;
   global $x1;
   global $y1;
   global $text_color;
   global $scale;
   global $rotatetext;
   for ($i = 0; $i < $depth[$parser]; $i++) {
       ////echo "  ";
   }

   if($name == "SVG"){
   	////echo "<hr>$name<br>";
	// Create image....
	$width = 0;
	$height = 0;
	foreach( $attrs as $attname => $value )
	{
	//$value = $this->_cleanString( $value );
	//$object->$attname = $value;
		if($attname == "WIDTH"){
			$width = $value;
		}
		if($attname == "HEIGHT"){
			$height = $value;
		}
		if($attname == "VIEWBOX"){
			////echo "$attname: $value<br>";
			// GET THE VALUES....
			$vals = explode(" ",$value);
			$count = 0;
			foreach($vals as $val){
				////echo "$val<br>";
				if($count == 2){
					$width = $val;
				}
				if($count == 3){
					$height = $val;
				}
				$count++;
			}
			////echo "W = $width<br>H = $height<br>";
		}

	}
	$im3 = @imagecreatetruecolor($width, $height)
   		or die("Cannot Initialize new GD image stream");
		////echo "creating image...";
		//$text_color = imagecolorallocate($im3, 233, 14, 91);
		//$background_color = imagecolorallocate($im3, 255, 255, 255);
		$bgColor = imagecolorallocate($im3, 255,255,255);
		imagefill($im3 , 0,0 , $bgColor);
		$text_color = imagecolorallocate($im3, 0, 0, 0);
   }
   	// Check to see if we have a <G> element...
	if($name == "G"){
		////echo "<hr>$name<br>";
		//if($scale == 1){

			//$scale = 0;
		//}
		foreach( $attrs as $attname => $value )
		{
			////echo "$attname:$value<br>";
			if($attname == "TRANSFORM"){
				// Parse the value string to get the transformation parameters...
				// translate(0,0): need to get out the 0 and 0 values...
				if(strlen($value) > 23){
					////echo "scaling taking place...<br>";
					////echo "$value<br>";
					$scale = 1;
					if($scale==1){
						////echo "scale is true...<br>";
					}
					$strings = substr($value, 0,12);
					////echo "***$strings**** <br>";
					$value = substr($value, 13, strlen($value));
					////echo "***$strings****<br>";
					$vals = str_replace("translate(", "",$value);
				$vals = str_replace(")", "",$vals);
				////echo "$vals<br>";
				$vals = explode(",", $vals);
				$translate_x = $vals[0];
				$translate_y = $vals[1];
				if($translate_y < 0){
					//$translate_y *= -1;
					$translate_y = $translate_y * -1;
				}
				////echo "Translate_x = $translate_x<br>";
				////echo "Translate_y = $translate_y<br>";

				}
				else{
					////echo "no scaling<br>";
				$vals = str_replace("translate(", "",$value);
				$vals = str_replace(")", "",$vals);
				////echo "$vals<br>";
				$vals = explode(",", $vals);
				$translate_x = $vals[0];
				$translate_y = $vals[1];
				////echo "Translate_x = $translate_x<br>";
				////echo "Translate_y = $translate_y<br>";
				}
			}
			if($attname == "ID"){
				if($value == "chemdendrogram"){
					////echo "CHEMDENDROGRAM*****************<BR>";
				}
				if($value == "treatments"){
					////echo "TRXS*********************<BR>";
					$rotatetext = 1;
				}

			}

		}
	}
	if($name == "RECT"){
		////echo "<hr>$name<br>";
		//<rect x="0" y="18" width="10" height="6" style="fill: rgb(213,0,0);"/>
		foreach( $attrs as $attname => $value )
		{
			////echo "$attname:$value<br>";
			if($attname == "X"){
				$x = $value;
			}
			if($attname == "Y"){
				$y = $value;
			}
			if($attname == "WIDTH"){
				$width = $value;
			}
			if($attname == "HEIGHT"){
				$height = $value;
			}
			if($attname == "STYLE"){

				// Need to check to see if fill is 'none'
				if(strlen($value) > 23){
					$x1 = $x + $translate_x;
					$y1 = $y + $translate_y;
					$x2 = $x1 + $width;
					$y2 = $y1 + $height;
					////echo "imagefilledrectangle($im3, $x1, $y1 ,$x2, $y2, $rect_color)<br>";
					$rect_color2 = imagecolorallocate($im3, 0,0,0);
					imagerectangle($im3, $x1, $y1 ,$x2,$y2,$rect_color2);
					//ImageRectangleWithRoundedCorners($im3, $x1, $y1, $x2, $y2, 3, $rect_color2);

				}else{
					$rgb = str_replace("fill: rgb(","",$value);
					$rgb = str_replace(");", "", $rgb);
					$rgb = explode(",", $rgb);
					$count = 0;
		foreach ($rgb as $val){

			if(is_numeric($val)){
				////echo "$val is a number<br>";
			}
			if($count == 0){

			$red = $val;
			}
			if($count == 1){
				$green = $val;
			}
			if($count == 2){
				$blue = $val;
			}
			$count++;

		}

		$rect_color = imagecolorallocate($im3, $red, $green, $blue);

		/////echo "rect_color = imagecolorallocate($im3, $red, $green, $blue)<br>";
		//allocatecolor($colorvar, $image, $red, $green, $blue);
		//$rect_color = imagecolorallocate($im3, 240, 0, 0);
		//$y = $translate_y + $y;
		//$translate_x += $x;
		$x1 = $x + $translate_x;
		$y1 = $y + $translate_y;
		$x2 = $x1 + $width;
		$y2 = $y1 + $height;
		////echo "imagefilledrectangle($im3, $x1, $y1 ,$x2, $y2, $rect_color)<br>";
		imagefilledrectangle($im3, $x1, $y1 ,$x2,$y2,$rect_color);
				}


				////echo "rect_color = imagecolorallocate($im3, $rgb[0], $rgb[1], $rgb[2])";

			}
		}


	}

	if($name == "TEXT"){
		////echo "<hr>$name<br>";
		//<text x="15" y="23" style="stroke: black; font-family: arial; font-size: 6pt;">6</text>
		foreach( $attrs as $attname => $value )
		{
			////echo "$attname:$value<br>";
			if($attname == "X"){
				$x = $value;
			}
			if($attname == "Y"){
				$y = $value;
			}
		}
		$x1 = $x + $translate_x;
		$y1 = $y + $translate_y - 10;
		$textcolor = imagecolorallocate($im3, 0, 0, 255);




	}

	if($name == "LINE"){
		////echo "<hr>$name<br>";
		//<text x="15" y="23" style="stroke: black; font-family: arial; font-size: 6pt;">6</text>
		foreach( $attrs as $attname => $value )
		{
			////echo "$attname:$value<br>";
			if($attname == "X1"){
				$x1a = $value;
			}
			if($attname == "Y1"){
				$y1a = $value;
			}
			if($attname == "X2"){
				$x2a = $value;
			}
			if($attname == "Y2"){
				$y2a = $value;
			}




		}

		$x1 = $x1a + $translate_x;

		$x2 = $x2a + $translate_x;

		////echo "*********************scale is: $scale<br>";
		if($scale==1){
		$y1 = -$y1a + $translate_y;
		$y2 = -$y2a + $translate_y;
			//echo "scale is true...<br>";
			/*$xtemp = $x1;
			$x1=$x2;
			$x2 = $xtemp;
			$ytemp = $y1;
			$y1 = $y2;
			$y2 = $ytemp;*/
			$line_color = imagecolorallocate($im3, 0, 0, 0);
		imageline($im3, $x1, $y1, $x2, $y2, $line_color);
		//echo "imageline($im3, $x1, $y1, $x2, $y2, $line_color)";

		}
		else{
		$y1 = $y1a + $translate_y;
		$y2 = $y2a + $translate_y;
		$line_color = imagecolorallocate($im3, 0, 0, 0);
		imageline($im3, $x1, $y1, $x2, $y2, $line_color);
		////echo "imageline($im3, $x1, $y1, $x2, $y2, $line_color)";
		}


	}


   $depth[$parser]++;
}

function endElement($parser, $name)
{
   global $depth;
   global $scale;
   ////echo "/$name<br>";
   $depth[$parser]--;
}


function characterData($parser, $data) {
	global $im3;
	global $x1, $y1, $text_color;
	global $scale;
	global $rotatetext;
	$data = trim($data);
	if($data != ""){
		$strings = substr($data, 0,8);
		////echo "strings = $strings<br>";
		if(substr($data, 0, 8)=="function"){
			////echo "this is a function...<br>";
		}
		elseif(substr($data,0,3)=="));"){
			////echo "this is crap...<br>";
		}else{
			////echo "data = $data<br>";
			///$text_color = imagecolorallocate($im3, 233, 14, 91);
			////echo "imagecolorallocate($im3, 233, 14, 91)<br>";
			$data = str_replace(",", "_",$data);
			if($rotatetext == 1){
				$x1 -= 10;
				////echo "imagestringup($im3, 3, $x1, $y1, $data, $text_color)";
				imagestringup($im3, 3, $x1, $y1, $data, $text_color);
			}else{
				////echo "imagestring($im3, 3, $x1, $y1, $data, $text_color)";
				imagestring($im3, 3, $x1, $y1, $data, $text_color);
			}
		}
	}

}

$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startElement", "endElement");
xml_set_character_data_handler($xml_parser, "characterData");



if (!($fp = fopen($file, "r"))) {
   die("could not open XML input");
}

while ($data = fread($fp, 4096)) {
   if (!xml_parse($xml_parser, $data, feof($fp))) {
       die(sprintf("XML error: %s at line %d",
                   xml_error_string(xml_get_error_code($xml_parser)),
                   xml_get_current_line_number($xml_parser)));
   }
}
xml_parser_free($xml_parser);
imagepng($im3);
imagepng($im3, "image.png");
imagedestroy($im3);
?>
