<?php
$uri=urldecode($_SERVER['REQUEST_URI']);
$path=realpath(__DIR__.'/../../../');
$path_original=$path.str_replace('/webps/','/wp-content/',$uri);
$path_completed=$path.str_replace('/webps/','/wp-content/webpcache/',$uri);

//var_dump($path_original);
//var_dump($path_completed);
$folder=pathinfo($path_completed,PATHINFO_DIRNAME );
if(!is_dir($folder)){
	mkdir($folder,0777,true);
}

/*
function imageCreateFromAny($filepath) {
    $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
    $allowedTypes = array(
        1,  // [] gif
        2,  // [] jpg
        3,  // [] png
        6   // [] bmp
    );
    if (!in_array($type, $allowedTypes)) {
        return false;
    }
    switch ($type) {
        case 1 :
            $im = imageCreateFromGif($filepath);
        break;
        case 2 :
            $im = imageCreateFromJpeg($filepath);
        break;
        case 3 :
            $im = imageCreateFromPng($filepath);
			imagepalettetotruecolor($im);
        break;
        case 6 :
            $im = imageCreateFromBmp($filepath);
        break;
    }   
    return $im; 
}

$image=imageCreateFromAny($path_original);


$s=imagewebp($image, $path_completed);
//var_dump($s);

imagewebp($image);
*/

header('Content-type: image/webp');
$image = new Imagick($path_original);

$image->setImageFormat('webp');
//$image->setOption('webp:lossless', 'true');
$image->writeImage($path_completed.'.webp');
rename($path_completed.'.webp',$path_completed);
echo file_get_contents($path_completed);


?>