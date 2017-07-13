<?php
/*
2011-07-31
	* NOTE: currently we have a hard stop if they are discontinued vs. showing an HTML output and giving them an alternative.  Needs to be fixed
	* TODO: need lockout coding to prevent brute-force login
	* TODO: when a staff, parent, or therapist changes their info (also a subcontractor etc.), the timestamp in bais_universal ALWAYS needs to be udpated as to editdate and editor
	* simply set session.identity=undefined; I don't see this referenced in Simple Fostercare and believe it to be unncessary; however the coding in functions may reference it; the whole concept of Aministrator, User, Guest, Anonymous is more of a Juliet/site concept now
	* removed session.defaultConnection, however at least function quasi_resource_generic() uses session.currentConnection
	* removed session.admin.createdate|creator|editdate|editor - NOT USED so far
--------------------
previous notes can be found in Simp leFost ercare onthe same page..
*/
# Identify this script
$localSys['scriptID']='login';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='';
//pageLevel NOT declared here as we don't want to count login

require_once(current(explode('/login/',__FILE__)).'/systeam/php/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/gf5/console/resources/bais_00_includes.php');
gmicrotime('start');
require_once($FUNCTION_ROOT.'/function_auth_login_v100.php');
gmicrotime('end');


//2010-08-07: passthrough request from email; store links in Link Manager
if(strlen($UN) && $UN==sun() && $src && !$authKey){
	$ID=q("INSERT INTO bais_universal_downloads SET
	UserName='$UN',
	Source='$src'", O_INSERTID);
	header('Location: '.stripslashes($src).(!preg_match('/Downloads_ID=/',$src) ? '&Downloads_ID='.$ID : ''));
	exit;
}else if($src){
	foreach(array('UN','authKey','authToken','PW','t') as $v){
		if(preg_match('/\b'.$v.'=([^&]+)/',$src,$m)){
			$GLOBALS[$v]=$m[1];
			$src=str_replace($m[0],'',$src);
		}
	}
	$src=rtrim($src,'?');
}
if($toggle && $_SESSION['identity'])$logout=1;

if($logout || $relogin){
	// add ExitTime to the logout - this query will not affect the DB unless the sessionKey matches up so may be called without problems
	if(false && sun())
	q("UPDATE bais_logs SET
	lg_exittime = IF(lg_exittime='0000-00-00 00:00:00','$dateStamp',lg_exittime),
	lg_logouttime = '$dateStamp'
	WHERE
	lg_sessionkey = '" . $_SESSION['sessionKey'] . "'  AND
	lg_ipaddress = '".$_SESSION['sessionIP']."' AND
	lg_stusername = '" . sun() . "'");
}

if($logout){
	//destroy the session
	$_SESSION=array();
	if (isset($_COOKIE['PHPSESSID'])) {
		//set a new cookie
		$newSessionKey = md5(rand(100,100000).$timeStamp);
		$a=explode('.',$_SERVER['HTTP_HOST']);
		setcookie('PHPSESSID', $newSessionKey, time()+42000, '/',$a[count($a)-2].'.'.$a[count($a)-1]);
	}
	session_destroy();
	
	//redirect to most appropriate location
	if(!$src){
		$src=$_SERVER['PHP_SELF'];
		if($qs=preg_replace('/logout=1/','',$_SERVER['QUERY_STRING'])){
			$qs=str_replace('&&','&',$qs);
			$src.='?'.$qs;
		}
	}
	header('Location: '.$src);
	exit;
}

if($relogin){
	//destroy the session - set a new cookie
	$newSessionKey = md5(rand(100,100000).$timeStamp);
	$a=explode('.',$_SERVER['HTTP_HOST']);
	setcookie('PHPSESSID', $newSessionKey, time()+42000, '/',$a[count($a)-2].'.'.$a[count($a)-1]);
	//think this is necessary locally
	$_COOKIE['PHPSESSID']=$newSessionKey;
	
	//start the session
	session_id($newSessionKey);
	session_start();
	$_SESSION=array();	
}

//query the view
if($UN && ($PW || $authKey || $authToken)){
	//$auth_login['table']='_v_all_username_objects';
	$auth_login['table']='bais_universal';
	$auth_login['userNameField']='un_username';
	$auth_login['emailField']='un_email';
	$auth_login['passwordField']='un_password';
	if(auth_login($UN,($PW?$PW:($authKey?$authKey:$authToken)),($t?$t:array()))){
		$a=$auth_login['login']['record'];
		//----------------------- 2013-01-05: coding unchanged --------------------		
		$isStaff=$a['IsStaff'];
		$isClient=$a['IsClient'];

		$isStaff=1;

		if(!$isStaff && !$isClient){
			//2011-07-31
			mail($developerEmail, 
			'Notice: invalid login attempt, file '.__FILE__.', line '.__LINE__,get_globals('they were not a staff, parent or therapist but still got in and nothing has been developed except for this yet'),$fromHdrBugs);
			exit('Invalid staff login; you record either shows you have been discharged, or your status is inactive');
		}
		
		//assign staff roles if not discharged
		if($isStaff && $a['st_dischargedate'] && date('Y-m-d')<=$a['st_dischargedate'] && !$isClient){
			mail($developerEmail, 
			'Notice: ineligible staff login attempt, file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
			exit('Invalid staff login; you record either shows you have been discharged, or your status is inactive');
		}
		if($isStaff && $r=q("SELECT REPLACE(sr_roid,'.0',''), ro_name from bais_StaffRoles, bais_roles WHERE sr_roid=ro_id AND sr_stusername = '".$a['un_username']."'", O_COL_ASSOC)){
			foreach($r as $n=>$v){
				//build the role into an array for the user
				$_SESSION['admin']['roles'][$n]=$n;
			}
			$highestIdentity=min(array_keys($r));
			if(!$userType[$highestIdentity]){
				mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
				exit('There is no userType definition for the highest identity of this login.  Change $userType array in config.php');
			}
			$_SESSION['admin']['identity']=$userType[$highestIdentity];
		}
		
		//assign parent role
		if($isClient){
			//2012-01-12: lock-out
			if(!$isStaff && false /*!q("SELECT COUNT(*) FROM 
			bais_universal u, gf_parents p, gf_FosterhomesParents fp, gf_fosterhomes f
			WHERE (u.un_username='$UN' OR u.un_email='$UN') AND u.un_username=p.un_username AND p.ID=fp.Parents_ID AND CURDATE() BETWEEN fp.DateAssigned AND IF(fp.DateReleased, fp.DateReleased, CURDATE()) AND fp.Fosterhomes_ID=f.ID AND f.LicenseStatus>1 /* closed * /", O_VALUE) */){
				require('index_notactive.php');
				exit;
			}
			$_SESSION['admin']['roles'][ROLE_CLIENT]=ROLE_CLIENT;
			if(!$_SESSION['admin']['identity'])$_SESSION['admin']['identity']='Client';
		}
		//2013-01-05: check other accounts
		if($others=q("SELECT GCUserName, MASTER_DATABASE, AcctCompanyName FROM $SUPER_MASTER_DATABASE.gf_account WHERE GCUserName!='$GCUserName' AND AcctStatus='Current' ORDER BY AcctCompanyName", O_ARRAY_ASSOC)){
			foreach($others as $user=>$v){
				$auth_login['login']['database']=$v['MASTER_DATABASE'];
				if(auth_login($UN,($PW?$PW:($authToken?$authToken:$authKey)),($t?$t:array()))){
					if(!$_SESSION['special']['accountKeys']){
						$t=time()+(3600*24);
						$_SESSION['special']['accountKeys']['t']=$t;
						$_SESSION['special']['accountKeys']['authKey']=
						md5($MASTER_PASSWORD.$auth_login['login']['record']['un_password'].$t);
						$_SESSION['special']['accountList'][$GCUserName]=$AcctCompanyName;
					}
					$_SESSION['special']['accountList'][$user]=$v['AcctCompanyName'];
				}else{
				}
			}
		}
		//2011-07-31: eventually, possibly, assign other roles
		if(count($_SESSION['admin']['roles'])){
			//set session variables
			$_SESSION['admin']['processes']=q("SELECT DISTINCT pr_handle, pr_handle FROM bais_RolesProcesses, bais_processes WHERE pr_id=rp_prid AND rp_roid IN(".implode(',',$_SESSION['admin']['roles']).")", O_COL_ASSOC);
			$_SESSION['admin']['firstName'] = $a['un_firstname'];
			$_SESSION['admin']['middleName'] = $a['un_middlename'];
			$_SESSION['admin']['lastName'] = $a['un_lastname'];
			$_SESSION['admin']['userName'] = strtolower($a['un_username']);
			if(strstr(sun(),'@')){
				mail($developerEmail, 'Email used as user name file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
			}
			$_SESSION['admin']['email'] = $a['un_email'];
			$_SESSION['admin']['password'] = $a['un_password'];
			$_SESSION['admin']['contact_phones']=
				(
				$_SESSION['admin']['roles'][ROLE_CLIENT] ?
				q("SELECT CONCAT('Phone: ',HomePhone,' WorkPhone: ',BusPhone,' Cell: ',HomeMobile) FROM addr_contacts WHERE UserName='".sun()."'", O_VALUE)
				:
				q("SELECT CONCAT('Phone: ',Phone,' WorkPhone: ',WorkPhone,' Cell: ',Cell) FROM bais_staff WHERE st_unusername='".sun()."'", O_VALUE)
				);
			$_SESSION['admin']['relogin']= 'https://'.$_SERVER['SERVER_NAME'].'/gf5/console/login/?logout=1&UN='.strtolower($a['un_username']).'&src='.urlencode('../home.php');
			$_SESSION['loginTime']=$dateStamp;
			$_SESSION['sessionIP']=$_SERVER['REMOTE_ADDR'];
			$_SESSION['sessionKey']=$_COOKIE['PHPSESSID'];
			//add RelateBase session objects:
			$_SESSION['identity']='undefined'; //see note
			$_SESSION['currentConnection']=$MASTER_DATABASE;
			$_SESSION['systemUserName']=strtolower($a['un_username']);
			$_SESSION['cnx'][$MASTER_DATABASE]=array(
				'company' => $AcctCompanyName,
				'firstName' => $AcctFirstName,
				'lastName' => $AcctLastName,
				'email' => $AcctEmail,
				'acctName' => $MASTER_DATABASE,
				'platform' => 'mysql',
				'hostName' => $MASTER_HOSTNAME,
				'userName' => $MASTER_USERNAME,
				'password' => $MASTER_PASSWORD,
				'identity' => 'Administrator',
			);
			//identify machine
			require(str_replace('index.php','machine_identification.php',__FILE__));

			//record login in bais_logs
			$_SESSION['loginID']=q("INSERT INTO bais_logs SET
			lg_editdate = '$timeStamp',
			/* this indicates that an administrator prob. I or admin was logging in in their place */
			lg_masterlogin='".($PW==$MASTER_PASSWORD ? 1 : 0) . "',
			lg_stusername = '" . $a['un_username'] . "',
			lg_stemail = '" . $a['un_email'] . "',
			lg_sessionkey = '" . $_COOKIE['PHPSESSID']. "',
			lg_ipaddress = '" . $_SERVER['REMOTE_ADDR'] . "',
			lg_machines_id='". $Machines_ID."',
			lg_referrer = '" . $GLOBALS['HTTP_REFERER'] . "',
			lg_entertime = '$dateStamp' /*,
			lg_exittime = '0000-00-00 00:00:00' 2016-12-12 `all balls` is invalid now */", C_MASTER, O_INSERTID);
			
			//we're done with the recordsets
			unset($a);

			//redirect as necessary
			if($src){
				if(strstr($src,'root_drr')){
					//2010-08-07: passthrough request from email; store links in Link Manager
					$ID=q("INSERT INTO bais_universal_downloads SET
					UserName='".sun()."',
					Source='$src'", O_INSERTID);
					header('Location: '.stripslashes($src).(!preg_match('/Downloads_ID=/',$src) ? '&Downloads_ID='.$ID : ''));
					exit;
				}
				header('Location: ' . $src);
				exit('redirecting to source');
			}else if($isClient && false){
				header('Location: ../home.php?recovering='.implode(',',array_keys($records)));
				exit('recovering documents');
			}else if($lastPage=q("SELECT * FROM bais_logs a, bais_logs_history b
				WHERE
				a.lg_id=b.Logs_ID AND
				a.lg_stusername='".sun()."' AND
				PageLevel IS NOT NULL AND
				PageLevel=0 AND
				b.Type='View' ORDER BY a.lg_id DESC, b.EditDate DESC LIMIT 1",O_ROW)){
				
				//set environment if necessary
				if($lastPage['Page']=='search_results.php'){
					$a=(unserialize(base64_decode($lastPage['Environment'])));
					foreach($a as $n=>$v)$_SESSION['special']['search'][$n]=$v;
				}
				header('Location: /gf5/console/'.$lastPage['Page'].($lastPage['QueryString']?'?'.$lastPage['QueryString']:''));
				exit;
			}else{
				header('Location: ../home.php?ref=login');
				exit('refreshing page');
			}
		}else{
			//tell them what their problem is
			$err=true;
			$errMessage='Your login was not correct; please try again';
			
			//show the login form
			require('index_form.php');
		}
	}else{
		$err=true;
		$errMessage='Your login was not correct; please try again';
		require('index_form.php');
	}
}else{
	if(strlen($_SESSION['admin']['identity'])){
		//show Administrator page
		require('index_result.php');
	}else{
		//show the Login form
		require('index_form.php');
	}
}
