<?php

$width = $height = 1024;
$im = imagecreatetruecolor($width, $height);
if (!$im) {
	echo 'Init image failed!', PHP_EOL;
	exit;
}

$color = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, $width, $height, $color);
imagecolordeallocate($im, $color);

for ($x = 0; $x < $width; $x++) {
	for ($y = 0; $y < $height; $y++) {
		list($r, $g, $b, $a) = get_color_at($x, $y);
		$color = imagecolorallocatealpha($im, $r, $g, $b, $a);
		imagesetpixel($im, $x, $y, $color);
	}
}

/*
// save file
*/
$imageFile = dirname(__FILE__).'/code1024.jpg';
imagejpeg($im, $imageFile, 90);
echo 'image saved: ', $imageFile, PHP_EOL;

/*
// output
header('content-type: image/jpeg');
imagejpeg($im, null, 90);
*/

/**
 * 获取指定点的颜色值： r,g,b,alpha
 * 坐标范围： (0, 0) => (1023, 1023)
 * 颜色值范围：均为 0 - 255
 * 透明度： 0 - 255， 值越大越透明
 */
function get_color_at($x, $y) {
	$r = $g = $b = $a = 0;

	// {{code start}}
	// your code here

	// {{code end}}

	return array($r, $g, $b, $a);
}