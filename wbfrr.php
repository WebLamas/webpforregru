<?php 
/**
 * Plugin Name: WebP generation for reg ru
 * Description: change all images to webp.
 * Version: 1
 * Author: weblamas
 *
 */
 
//1. вешаем фильтр на вот эту функцию.
//2. встраиваем фильтр в нашу customPost::query;
//https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
//3. wp_enqueue_style - сделать закешированную версию. Все стили сжать.
 
//preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $row->introtext, $matches);

//1. КАртинки тупо в css. надо их просто разок переформатировать..
//2. получаемм с помощь custompost::QUery
//3. контент?) хотелось бы его подменять все таки.
/*
<IfModule mod_rewrite.c>
RewriteEngine On
# если не понимает webp - отдаем обычную картинку.
RewriteCond %{HTTP_ACCEPT} !image/webp
RewriteCond %{REQUEST_URI} ^/webps*
RewriteRule ^webps(?i)(.*)(\.jpe?g|\.png)$ /wp-content$1$2 [L]

RewriteCond %{HTTP_ACCEPT} image/webp
RewriteCond %{REQUEST_URI} ^/webps*
RewriteRule ^webps(?i)(.*)(\.jpe?g|\.png)$ /wp-content/webpcache$1$2  [L]

RewriteCond %{REQUEST_URI} ^/wp-content/webpcache*
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule (?i).*(\.jpe?g|\.png)$ /wp-content/plugins/webpforregru/webp-on-demand.php [L]


</IfModule>
*/

function wbfr_fixurl($image){
	$imagepath=parse_url($image);
	
	$siteurl=parse_url(get_site_url().'/');
	
	if($imagepath['host']!=$siteurl['host'])return $image;
	
	$imagepath=$imagepath['path'];
	$extenstion=strtolower(pathinfo($imagepath,PATHINFO_EXTENSION ));
	if(!in_array($extenstion,['jpg','png']))return $image;
	$image=str_replace('/wp-content/','/webps/',$imagepath);;
	return $image;
}
add_filter('wp_get_attachment_image_src',function($image,$attach_id,$size,$icon){
	$image[0]=wbfr_fixurl($image[0]);
	return $image;
},10,4);

add_filter('wp_get_attachment_url',function($url,$attach_id){
	return wbfr_fixurl($url);
},10,2);



register_activation_hook(__FILE__,function(){
	$htaccess = get_home_path().".htaccess";

	$string='
	<IfModule mod_rewrite.c>
RewriteEngine On
# если не понимает webp - отдаем обычную картинку.
RewriteCond %{HTTP_ACCEPT} !image/webp
RewriteCond %{REQUEST_URI} ^/webps*
RewriteRule ^webps(?i)(.*)(\.jpe?g|\.png)$ /wp-content$1$2 [L,E=webpimage:1]

RewriteCond %{HTTP_ACCEPT} image/webp
RewriteCond %{REQUEST_URI} ^/webps*
RewriteRule ^webps(?i)(.*)(\.jpe?g|\.png)$ /wp-content/webpcache$1$2  [L,E=webpimage:1]

RewriteCond %{REQUEST_URI} ^/wp-content/webpcache*
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule (?i).*(\.jpe?g|\.png)$ /wp-content/plugins/webpforregru/webp-on-demand.php [L,E=webpimage:1]
</IfModule>
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresDefault "access plus 12 month"
	ExpiresByType application/vnd.oasis.opendocument.formula-templatel "access plus 12 month"
</IfModule>
	';

	insert_with_markers($htaccess, "WebPForRegru", $string);	
});
/*
header("Cache-Control: max-age=86400"); 
header("Pragma: cache"); 
header("Expires: ". date(DATE_RFC2822, time() + 86400)); 

$imgname = $_GET["id"];
   header("Content-type: image/jpeg");
   readfile("./img/".$imgname.".JPG")
   */