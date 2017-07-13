<?php
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');

if(!$token || md5($Expires . $MASTER_PASSWORD)!=$token)exit('Improper page call');
if($Expires<time())exit('This page has expired');

echo q("SELECT Output FROM gl_searches WHERE ID=$Searches_ID", O_VALUE);
?>