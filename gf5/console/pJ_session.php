<?php
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
if($killsession){
	$_SESSION=array();
	header('Location: /');
}else{
	if($PW!=$MASTER_PASSWORD)prn('You do not have access to this information',1);
	prn($_SESSION);
}
?>