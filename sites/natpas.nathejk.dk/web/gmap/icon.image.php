<?php
header ('Content-Type: image/png');
$im = imagecreatetruecolor(21, 22);

$black = imagecolorallocate($im,0,0,0);
// Make the background transparent
imagecolortransparent($im, $black);

$colors = array('purple' => '8c4eb8', 'pink' => 'c259b6', 'lightred' => 'f34648', 'darkred' => 'c03639', 'lightblue' => '3875d7', 'darkblue' => '265bb2', 'cyan' => '5ec8bd', 'darkgreen' => '128e4e', 'lightgreen' => '67c547', 'yellow' => 'ffc01f', 'oramge' => 'ff8922', 'brown' => '9d7050', 'grey' => 'a8a8a8');
$colorcode = isset($_GET['color'], $colors[$_GET['color']]) ? $colors[$_GET['color']] : array_pop($colors);
list($r, $g, $b) = str_split($colorcode, 2);
$blue = imagecolorallocate($im, hexdec("0x$r"), hexdec("0x$g"), hexdec("0x$b"));

$values = array(1, 1, 19, 1, 19, 17, 15, 17, 11, 21, 7, 17, 1, 17);

// draw a polygon
imagefilledpolygon($im, $values, 7, $blue);


$src = imagecreatefrompng('border.png');
imagealphablending($im, true);
//imagealphablending( $src, false );
imagesavealpha( $src, true );


//imagecopymerge ($im , $src, 0 ,0 , 0, 0, 21, 22, 100 );
imagecopyresampled ($im , $src, 0 ,0 , 0, 0, 21, 22, 21, 22 );

//imagepalettecopy ( $im , $src );

//      $text_color = imagecolorallocate($im, 233, 14, 91);
//      imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

//$black = imagecolorallocate($src,0,0,0);
//imagecolortransparent($src, $black);


$white = imagecolorallocate($im, 255, 255, 255);
$font = __DIR__ . '/17095_GillSansMTExtCondensedBold.ttf';
$font = __DIR__ . '/09389_ContextRepriseCondensedSSiBoldCondensed.ttf';
$size = 10.5;
$text = isset($_GET['label']) ? $_GET['label'] : '103';
list($llx, $lly, $lrx) = imagettfbbox ($size , 0, $font, $text );
$width = $lrx - $llx;
$center = (strlen($text) < 3) ? 13 : 12;
// Add some shadow to the text
imagettftext($im, $size, 0, $center-($width/2), 14, $white, $font, $text);


imagepng($im);
//imagedestroy($im);
