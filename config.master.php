<?php
/*
2009-08-21: Master Connection coding - pulls the account based on the subdomain

*/

if(!$GCUserName)exit('variable $GCUserName not declared');

unset($rd);
$super_cnx=mysqli_connect($MASTER_HOSTNAME,$MASTER_USERNAME,$MASTER_PASSWORD) or die(mysqli_error($super_cnx));
mysqli_select_db($super_cnx, $SUPER_MASTER_DATABASE) or die(mysqli_error($super_cnx));
$sql = "SELECT MASTER_DATABASE, AcctStatus, AcctCompanyName, AcctCompanyAbbr, AcctLicenseType, AcctEmail, AcctBookkeeperEmail, 
AcctWebsite, AcctAddress, AcctCity, AcctState, AcctZip, AcctPhone, AcctFax, ApplicationSettings, AdministratorSettings FROM gf_account WHERE GCUserName='$GCUserName'";

$resource=mysqli_query($super_cnx, $sql) or die(mysqli_error($super_cnx));
$rd=mysqli_fetch_array($resource, MYSQLI_ASSOC) or die(mysqli_error($super_cnx));

if(preg_match('#/gf[.0-9]+/#i',$_SERVER['PHP_SELF']) || $configMasterCall){
	if(!$rd)exit('unable to find account '.$GCUserName);
	if($rd['AcctStatus']!=='Current')exit('You account is not current.  Contact Great Locations staff');


	/*
	 * 2017-07-22 SF; bugfix
	 * We are already very complex on the sequence to get settings.  We already HAVE MASTER_DATABASE, and it can't be defined in the db since we need it to vary based on environment for testing
	 */
	if(!empty($MASTER_DATABASE)) unset($rd['MASTER_DATABASE']);


	extract($rd);
	if(!function_exists('array_merge_accurate'))require($FUNCTION_ROOT.'/function_array_merge_accurate_v100.php');
	if($ApplicationSettings){
		$apSettings=unserialize(base64_decode($ApplicationSettings));
		unset($ApplicationSettings);
	}
	$apSettings=@array_merge_accurate($defaultApSettings, $apSettings);
	if($AdministratorSettings){
		if($adminSettings=unserialize(base64_decode($AdministratorSettings))){
			//2010-07-13: bug in array_merge_accurate will not alllow an array node of null to be overwritten
			foreach($adminSettings as $n=>$v){
				if(is_null($v))unset($adminSettings[$n]);
			}
		}
		unset($AdministratorSettings);
	}
	$adminSettings=array_merge_accurate($defaultAdminSettings, is_array($adminSettings) ? $adminSettings : array());
	if(@$adminSettings['foundation'])
	foreach($adminSettings['foundation'] as $n=>$v){
		if(substr($n,0,4)=='word')$$n=$v;
	}
	mysqli_close($super_cnx);
}else{
	//it is assumed that they are either in public root files or possibly cgi
}

