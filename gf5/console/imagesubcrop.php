<?php
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';

require('systeam/php/config.php');
require('resources/bais_00_includes.php');

$imgPath='images/documentation/hmr/TXZZ0018.jpg';


/*imgMag] => .95
imgTop] => -231
imgLeft] => -261
thumbTop] => 50
thumbLeft] => 50
thumbWidth] => 420
thumbHeight] => 575
*/
//these need converted to these parameters:
#Wx, l1, t1, l2, t2
//get image size
$Tree_ID=tree_build_path($imgPath);
$path=tree_id_to_path($Tree_ID);

//this is the translation section for the variables the function requires
if($g=@getimagesize($_SERVER['DOCUMENT_ROOT'].($s=preg_replace('/\/([^/]+)\.(gif|jpg|jpeg|png|svg)$/i','/.thumbs.dbr/$1{orig}.$2',$path)))){
	//OK 
	$path=$s;
	$g_sub=getimagesize($_SERVER['DOCUMENT_ROOT'].$path);
	$imgMag *= ($g_sub[0]/$g[0]);
	$w_x = round($imgMag * $g[0],0);
	//need to work on this
	exit;
}else if($g=getimagesize($_SERVER['DOCUMENT_ROOT'].$path)){
	//imgMag stays the same as requested
	if(!$imgMag)$imgMag=1.0;
	if(!$imgLeft)$imgLeft=0;
	if(!$imgTop)$imgTop=0;
	if(!$thumbLeft)$thumbLeft=0;
	if(!$thumbTop)$thumbTop=0;
	if(!$thumbWidth)$thumbWidth=round($g[0]*$imgMag,0)-$thumbLeft;
	if(!$thumbHeight)$thumbHeight=round($g[1]*$imgMag,0)-$thumbTop;

	$w_x = round($imgMag * $g[0],0);
	$el_1= $thumbLeft - $imgLeft;
	$tee_1 = $thumbTop - $imgTop;
	$el_2= $el_1 + $thumbWidth;
	$tee_2= $tee_1 + $thumbHeight;
	//prn("w_x:$w_x  el_1:$el_1 tee_1:$tee_1 el_2:$el_2 tee_2:$tee_2",1);
}

//2012-08-07 new function - also to be integrated into reader.php
if(!function_exists('create_thumbnail'))require($FUNCTION_ROOT.'/function_create_thumbnail_v201.php');
// works: echo create_thumbnail_scalecrop($path,$g,$w_h,$el_1,$tee_1,$el_2,$tee_2,array('location'=>$location));


$resource=create_thumbnail($_SERVER['DOCUMENT_ROOT'].$path, $shrink=$imgMag, $crop='', $location='returnresource', $options=array());
$left=$thumbLeft-$imgLeft;
$top=$thumbTop-$imgTop;
create_thumbnail($resource, '', array($left, $top, $left+$thumbWidth, $top+$thumbHeight), 'hello4.jpg');


exit;
?>