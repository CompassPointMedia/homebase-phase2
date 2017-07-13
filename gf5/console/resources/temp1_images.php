<?php

imgMag] => .95
imgTop] => -231
imgLeft] => -261
thumbTop] => 50
thumbLeft] => 50
thumbWidth] => 420
thumbHeight] => 575
//these need converted to these parameters:
Wx, l1, t1, l2, t2
//get image size
$path=tree_id_to_path($Tree_ID);
if($g=getimagesize($_SERVER['DOCUMENT_ROOT'].($src=('/'.ltrim(preg_replace('/\/([^/]+)\.(gif|jpg|jpeg|png|svg)$/i','/.thumbs.dbr/$1{orig}.$2',$path)),'/')))){
	//OK 
	$g_sub=getimagesize($_SERVER['DOCUMENT_ROOT'].$path);
	$imgMag *= ($g_sub[0]/$g[0]);
	$w_x = round($imgMag * $g[0],0);
}else if($g=getimagesize($_SERVER['DOCUMENT_ROOT'].$path)){
	$src=$path;
	//imgMag stays the same
	$w_x = round($imgMag * $g[0],0);
	$el_1= $thumbLeft - $imgLeft;
	$tee_1 = $thumbTop - $imgTop;
	$el_2= $el_1 + $thumbWidth;
	$tee_2= $tee_1 + $thumbHeight;
}

function image_scale_subcrop($src,$g,$w_x,$el_1,$tee_1,$el_2,$tee_2){
	$w=min($w_x,$g[0]);
	
	$scaled = imagecreatetruecolor($w, $g[1]); 
	$source = imagecreatefromjpeg($src); 
	imagecopyresized($scaled, $source, 0, 0, 0, 0, $w, $origImgHeight, $origImgWidth, $origImgHeight);
	$cropped = imagecreatetruecolor($_GET['Lsub2'] - $_GET['Lsub1'], $_GET['Tsub2'] - $_GET['Tsub1']);
	imagecopy($cropped, $scaled, 0, 0, $_GET['Lsub1'], $_GET['Tsub1'], $_GET['Lsub2'], $_GET['Tsub2']);
	header('Content-Type: image/jpeg'); 
	imagejpeg($cropped); 
	exit; 
}


?>