<?php
//auth_i2_v100.php

/**
2010-08-01: moved registeredComponents array here
Note 2005-06-26 by Sam: I hadn't thought to say this but this file only checks for appropriate access, comparing the localSys script ID's with the login backmap to authorized components.  It also sets a few login-specific variables cu and cuName.  So, this page could be omitted for a non-login section without consequence.

this page for the admin section will do the following
1. determine the script id.  IF not there it shuts down the page
	(NOTE: get the version also)
2. determine the admin and see if they have access to this script
3. determine the user (applicant) and see if there is a relationship for this admin to this user in this script
**/
if(!in_array( str_replace('auth_i2_v100.php','config.php',__FILE__), get_included_files() )){
	mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
	if($scriptDisposition=='imagereader'){
		header('Accept-Ranges: bytes');
		header('Content-Length: '.filesize($_SERVER['DOCUMENT_ROOT'].'/images/assets/aberror.jpg'));
		header('Content-Type: image/jpg');
		readfile($_SERVER['DOCUMENT_ROOT'].'/images/assets/aberror.jpg');
		$assumeErrorState=false;
		$suppressNormalIframeShutdownJS=true;
		exit;
	}
	exit('auth_i2_v100.php requires config.php to run');
}
//script ID and Version
if(!$localSys['scriptID'] || !$localSys['scriptVersion']){
	exit('Script ID and version not declared');
}
if(!$_SESSION['admin']['identity'] || !sun()){
	//handle non-logged in
	if($scriptDisposition=='imagereader'){
		header('Accept-Ranges: bytes');
		header('Content-Length: '.filesize($_SERVER['DOCUMENT_ROOT'].'/images/assets/notsignedin.jpg'));
		header('Content-Type: image/jpg');
		readfile($_SERVER['DOCUMENT_ROOT'].'/images/assets/notsignedin.jpg');
		$assumeErrorState=false;
		$suppressNormalIframeShutdownJS=true;
		exit;
	}
	echo 'not logged in';
	//handle notification inside iframes
	?><script language="javascript" type="text/javascript">
	/***
	THIS SCRIPT IS NOT COMPATIBLE WITH ALL BROWSERS - window.opener is having some problems being identified
	***/
	//javascript:window.location='/gf5/console/login/index.php?src='+escape(window.location+'')+'&hideHeader=1'
	var popupparent='';
	try{ popupparent=window.opener.self.name; } catch(e){ }
	if(popupparent!=window.self.name){
		var x=window.location+'';
		if(!x.match(/UN=[^&]+/))alert('Your session has expired');
		window.location='/gf5/console/login/index.php?src='+escape(x)+'&hideHeader=1';
	}else if(window.parent.name!=self.name){
		alert('The action you wanted to perform cannot be performed because your login has timed out.\nLog in again in the main window, and retry.  When you have signed in, you should be able to re-submit this.\n\nNOTE: if you just submitted progress or other report information, it has been stored for recovery.  Please contact $AcctCompanyName for assistance or developer Samuel Fullman at (310)701-3129');
		window.parent.focus();
		window.parent.window.opener.focus();
	}else if(typeof window.opener=='object'){
		//this is an l1 window
		alert('Not logged in, close window if necessary and re-Log in');
		var x=window.location+'';
		window.location='/gf5/console/login/index.php?src='+escape(x);
	}else{
		var x=window.location+'';
		window.location='/gf5/console/login/index.php?src='+escape(x);
	}
	</script><?php
	//exit process
	$assumeErrorState=false;
	exit;
}else{
	if(!$_SESSION['admin']['processes'][$localSys['scriptID']]){
		mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals($err='This page '.$localSys['scriptID']. ' is not properly set up, or does not match your permissions; a database administrator has been notified'),$fromHdrBugs);

		//how to close the page
		if($localSys['pageType']=='Executable Page'){
			error_alert($err);
		}else if($localSys['pageType']=='Properties Window'){
			?><h1>Error in Access</h1><?php
			echo $err;
			?><br />
			<input type="button" name="submit" value="Close this Window.." onclick="window.close();" /><?php
		}else{
			?><h1>Error in Access</h1><?php
			echo $err;
		}
		$assumeErrorState=false;
		exit;
	}
}

//-----------------------  user settings - added 2008-04-20  ------------------------------
//submitted, pending, correction needed etc.
if($scriptDisposition!=='imagereader'){
	$defaultUserSettings=array(
		'hideInactiveFosterhomes'=>1,
		'hideInactiveStaff'=>1,
		'hideInactiveParents'=>1,
		'hideInactiveChildren'=>1,
		'hideInactiveTherapists'=>1
		/* 'arraynode:key'=>'value' this is an example */
	);
	if($_SESSION['userSettings'] && !$refreshUserSettings){
		//OK
	}else{
		//set initial values
		$a=q("SELECT CONCAT( varnode, IF(varkey!='',':',''), varkey), varvalue FROM bais_settings WHERE UserName='".sun()."' ORDER BY varnode, varkey", O_COL_ASSOC);
		$_SESSION['userSettings']=@array_merge(
			is_array($defaultUserSettings) ? $defaultUserSettings : array(), 
			$a ? $a : array()
		);
	}
}

$trainingTypeLabels=array(
	'Pre'=>'Pre-service',
	'In-service'=>'In-service (Core)',
	'Annual'=>'Annual'
);

$localRoles=$_SESSION['admin']['roles'];

$registeredComponents['propertiesResults']=	$COMPONENT_ROOT.'/comp_91_searchresult_v100.php'; #2010-12-19
$registeredComponents['paymentsGUI']=	$COMPONENT_ROOT.'/comp_70_payments_v100.php'; #2010-12-19
$registeredComponents['InvoiceList']=	$COMPONENT_ROOT.'/comp_250_invoices.php'; #2010-12-19
$registeredComponents['InvoiceListAgent']=	$COMPONENT_ROOT.'/comp_255_agentinvoices.php'; #2010-12-19
$registeredComponents['invoiceList']=	$COMPONENT_ROOT.'/comp_invoices_251_(dataset_from_scratch_03).php'; #2010-12-19
$registeredComponents['staffList']=	$COMPONENT_ROOT.'/comp_60_staff_list_v110.php'; #2012-04-11
$registeredComponents['propertyImages']=	$COMPONENT_ROOT.'/comp_301_propertyimages.php'; #2012-04-18
$registeredComponents['propertyFiles']=	$COMPONENT_ROOT.'/comp_321_propertyfiles.php'; #2012-04-18
$registeredComponents['ticketsList']=	$COMPONENT_ROOT.'/comp_911_tickets.php'; #2012-05-09


if($getStates)$states=q("SELECT st_code, st_name FROM aux_states",O_COL_ASSOC, $public_cnx);
if($getCountries)$countries=q("SELECT ct_code, ct_name FROM aux_countries",O_COL_ASSOC, $public_cnx);

//2013-11-30 pull exportTypes from database now
if($a=q("SELECT e.*, LCASE(v.ClientName) AS vendor FROM hbs_exporttypes e JOIN finan_vendors v ON e.Vendors_ID=v.ID ORDER BY v.ClientName", O_ARRAY)){
	$regex='/^createdate|creator|editdate|editor|vendors_id$/i';
	foreach($a as $v){
		foreach($v as $o=>$w){
			if($o=='sort'){
				$v[$o]=explode(',',$w);
			}else if(preg_match($regex,$o))unset($v[$o]);
		}
		$exportTypes[$v['vendor']][]=$v;
	}
}

//accounts
$InvoiceAccounts_ID=901; //AccountsReceivableAccounts_ID
$RentalLocatingItemAccounts_ID=904; //Income
$UndepositedFundsAccounts_ID=905;

//classes
$InvoiceClasses_ID=902;

//items
$RentalLocatingItems_ID=903;

//system variables and defaults
$apSettings['defaultState']='TX';


?>