<?php
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');

$a=q("SHOW TABLES IN cpm180_hmr", O_ARRAY);
foreach($a as $n=>$v){
	//if(!preg_match('/^_v_/',$v['Tables_in_cpm180_hmr']))continue;
	$b=q("SHOW CREATE ".(preg_match('/^_v_/',$v['Tables_in_cpm180_hmr']) ? 'VIEW':'TABLE')." `".$v['Tables_in_cpm180_hmr']."`", O_ROW);
	echo($b['Create '.(preg_match('/^_v_/',$v['Tables_in_cpm180_hmr']) ? 'View':'Table')]);
	echo "\n\n";
}
?>