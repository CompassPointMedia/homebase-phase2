<?php
//make sure cron echos
//get page_end in place
//figure out how to stay- on index page and ping
$localSys['scriptID']='bullshit';
$localSys['scriptVersion']='bullshit';

$file = __FILE__;
$config = explode('/console/system/', $file);
$left = explode('/', $config[0]);
$gf = array_pop($left);
$config = implode('/', $left) . '/'. $gf . '/console/systeam/php/config.php';
require_once($config);
if(count($argv)>1){
	for($i=1;$i<count($argv);$i++){
		$a=explode('=',$argv[$i]);
		$_GET[$a[0]]=$a[1];
	}
}
if($_GET['token']!==$cronToken){
	mail($developerEmail,'Cron report file '.$thispage.', line '.__LINE__,get_globals(),$fromHdrNormal);
	exit('Cron not executed - improper cron token passed');
}

$Scans_ID=q("INSERT INTO bais_scans SET 
CronPage='".__FILE__."',
Version='$cronVer'", O_INSERTID);

$startTime=time();
//get a list of users in the system, and see if they have the ability to log in
if(!($logins=q("SELECT 
un_username, un_password, IF(p.ID IS NOT NULL,1,0) AS Parent, IF(st_status IS NULL,'no', IF(st_status = 0,'inactive','active')) AS Staff
FROM 
bais_universal
LEFT JOIN gf_parents p ON un_username=pn_unusername
LEFT JOIN bais_staff ON un_username=st_unusername
WHERE
p.ID IS NOT NULL OR st_status > 0
GROUP BY un_username", O_ARRAY)))mail($developerEmail,'Cron report: no Great Locations Staff/Users file '.$thispage.', line '.__LINE__,get_globals(),$fromHdrNormal);
foreach($a as $v){
	extract($v);
	//set their "sessionid"
	$mysessionid=md5(time().$un_username);
	$authToken=md5($MASTER_PASSWORD.$un_password);
	
	//we do a curl call and see how long it takes and if we're signed in
	$start=time();
	$page='/console/login/index.php';
	$queryString="UN=$un_username&authToken=$authToken&ping=1";
	$out=`curl -b "PHPSESSID=$mysessionid;" "http://www.fantasticshop.com/gf5$page?$queryString"`;
	$stop=time();
	if(preg_match('/-ping-([0-9]+)$/i',$out,$end)){
		if(preg_match_all('/<b>Warning<\/b>:.*? on line <b>([0-9]+)<\/b>/i',$out,$warn)){
			//errors being declared on the page somewhere
			for($i=0; $i<=count($warn[0]); $i++){
				//log these errors
				q("INSERT INTO bais_scans_errors SET
				Scans_ID=$Scans_ID,
				UserName='$un_username',
				Type='Warning',
				ProcessTime='".($end[1]-$start)."',
				Content='".addslashes($warn[0][$i])."',
				Page='$page',
				QueryString='$queryString'");
			}
		}else{
			//page worked according to call
			q("INSERT INTO bais_scans_logs SET
			Scans_ID=$Scans_ID,
			UserName='$un_username',
			Type='OK Page Call',
			ProcessTime='".($end[1]-$start)."',
			Content='".($logEntirePage? $out : strlen($out).' characters received')."'");
		}
	}else{
		//page failure (didn't get to ping)
		q("INSERT INTO bais_scans_errors SET
		Scans_ID=$Scans_ID,
		UserName='$un_username',
		Type='Page Failure',
		ProcessTime='".($stop-$start)."',
		Content='".addslashes($out)."',
		Page='$page',
		QueryString='$queryString'");
	}
	
	
	//we do a curl call and see if we're redirected plus the ping
	
	//log the successful signins and times
	
	//based on their permissions, perform a list of view-only tasks
	
	//based on their permissions, open a prognote and submit it
	
	
}



mail($developerEmail,'Cron report file '.$thispage.', line '.__LINE__,get_globals(),$fromHdrNormal);
?>