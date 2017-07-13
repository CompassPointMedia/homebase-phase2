<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='generic';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');

/*
2012-05-11
this requires a file in a specific folder and with specific names and allows the processed results to show remotely

*/

if(false){ ?><script language="javascript" type="text/javascript"><?php }

parse_str($_SERVER['QUERY_STRING'],$a);
foreach($a as $n=>$v){
	unset($a[$n]);
	$a[strtolower($n)]=$v;
}
extract($a);

if(file_exists('components/comp_public_'.strtolower($form).'.php')){
	//get file contents
	ob_start();
	require('components/comp_public_'.strtolower($form).'.php');
	$out=ob_get_contents();
	ob_end_clean();
	$out=str_replace('\'','\\\'',$out);
	$out=preg_replace('/\r/','',$out);
	$out=str_replace("\n",'\\n',$out);
}else{
	$out='the form you are requesting does not exist!';
}
?>
document.write('<?php echo $out;?>');
<?php

if(false){ ?></script><?php }

?>