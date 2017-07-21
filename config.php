<?php
/* 2013-01-13: extremely irritating refusal to change things from the server management, these are workarounds */
$bugger=array(
	'subdomain'=>'hmr',
);

ini_set('error_reporting',7);

/* ---------
*	config.php
this file is not used in any of the gf5/gf6 folders, only for root pages promoting Great Locations

---------- */
if(!$suppressSessionStart){
	if(strlen($sessionid)) session_id($sessionid);
	session_start();
	$sessionid ? '' : $sessionid = session_id();
}
//standardize the date and time stamps for long or repeated queries
function gmicrotime($n=''){
	#version 1.1, 2007-05-09
	//store array of all calls
	global $mT;
	list($usec, $sec) = explode(' ',microtime());
	$t=round((float)$usec + (float)$sec,6);
	$mT['all'][]=$t;
	if($n)$mT[$n][]=$t;
	//return elapsed since last call (in the local array)
	$u=($n?$mT[$n]:$mT['all']);
	if(count($u)>1)return round(1000*($u[count($u)-1]-$u[count($u)-2]),6);
}
gmicrotime('initial');
$ctime=time();
$dateStamp=date('Y-m-d H:i:s');
$timeStamp=preg_replace('/[^0-9]*/','',$dateStamp);

$appEnv = getenv('AppEnv');

if(!function_exists('config_get')){
    /**
     * config_get: return defined variables for multiple config(.php) files in order called.  File paths must be readable as-is.
     *
     * @created = 2017-07-13
     * @author = Sam Fullman <sam-git@compasspointmedia.com>
     * @param $__files
     * @param array $__config (Note: this position is reserved if needed)
     * @param array $__args
     * @return array
     */
    function config_get($__files, $__config = [], $__args = []){
        /*
         * Example of use:
         * ---------------
         * $files = ['../private/config.php', '../private/qa/config.php'];
         * print_r(config_get($files, [], ['foo'=>'bar']));
         */

        // File input list must be valid
        if(empty($__files) || !is_array($__files)) return $__args;

        // Accept only valid readable files
        foreach($__files as $__n=>$__v){
            unset($__files[$__n]);
            if(!is_readable($__v) || !is_file($__v)) continue;

            // Read the file
            require($__v);
            break;
        }

        // Collect defined vars in config file, or array if none present
        $__working = get_defined_vars();
        foreach(['__files', '__config', '__args', '__n', '__v'] as $__unset) unset($__working[$__unset]);
        $__args = array_merge($__args, $__working);

        return config_get($__files, $__config, $__args);

    }
}

// Get config files by precedence
$config = [ $_SERVER['DOCUMENT_ROOT'] . '/../private/config.php' ];
if($appEnv){
    $config[] = str_replace('/private/config.php', '/private/'.$appEnv.'/config.php', $config[0]);
}
$config = config_get($config);
extract($config);

//emails - vars moved to private directory
/**
 * siteRootEmailAccount, siteDomain, fromHdrNormal, fromHdrNotices, fromHdrBugs, developerEmail, superadminEmail, adminEmail
 */

//primary company information - vars moved to private directory
/**
 * companyName, companyAddress, companyCity, companyState, companyZip, companyPhone, companyFax, siteName, siteURL, siteURLSecure, printHeaderAddress
 */

if($a=$_SESSION['special']['requestSetCookie']){
	foreach($a as $n=>$v){
		continue; //not used
		setcookie($n,$v,time()+(3600*24*180*(is_null($v)?-1:1)),'/' /*,preg_replace('/^www\./i','',$_SERVER['HTTP_HOST'])*/);
		$_COOKIE[$n]=$v;
	}
	$_SESSION['special']['requestSetCookie']=array();
}

//THESE ARE TEMPORARY until we have a login system front and back
if(!$_SESSION['sessionKey'])$_SESSION['sessionKey']=md5(time().rand(100,1000000));
if(!$_SESSION['systemUserName'] && $GLOBALS['PHP_AUTH_USER']){
	$_SESSION['systemUserName']=$GLOBALS['PHP_AUTH_USER'];
}

if(substr($GLOBALS['REQUEST_URI'],0,strlen($_SERVER['PHP_SELF']))==$_SERVER['PHP_SELF']){
	//previous page/folder method
	if(!strlen($thispage) || !isset($thisfolder)){
		$a=preg_split('/\\\|\//',$_SERVER['PHP_SELF']);
		$thispage=$a[count($a)-1];
		if(count($a)>2){
			$thisfolder=$a[count($a)-2];
		}else{
			$thisfolder='';
		}
	}
}else{
	//2009-04-24, new method: presumed 404 page masquerading as other page, get page from REQUEST_URI
	$a=explode('?',$GLOBALS['REQUEST_URI']);
	$_qs_=$a[1];
	$a=preg_split('/\\\|\//',$a[0]);
	$thispage=$a[count($a)-1];
	if(count($a)>2){
		$thisfolder=$a[count($a)-2];
	}else{
		$thisfolder='';
	}
	if($_qs_){
		//globalize query string
		$a=explode('&',trim($_qs_,'&'));
		foreach($a as $pair){
			if(!stristr($pair,'='))continue;
			//safest most reliable way
			if(stristr($pair,'_SESSION') || stristr($pair,'HTTP_SESSION_VARS') || stristr($pair,'_SERVER') || stristr($pair,'HTTP_SERVER_VARS') || stristr($pair,'PHP_AUTH_USER') || stristr($pair,'PHP_AUTH_PW') || stristr($pair,'_ENV') || stristr($pair,'HTTP_ENV_VARS'))continue;

			$var=substr($pair,0,strpos($pair,'='));
			$var=str_replace('[','[\'', str_replace(']','\']',$var));
			$value=substr($pair,strpos($pair,'=')+1);
			$value=(is_numeric($value)?'':'\'') . str_replace("'","\'",urldecode($value)) . (is_numeric($value)?'':'\'');
			@eval('global $'.preg_replace('/\[.+/','',$var).';');
			@eval('$'.$var.'='.$value.';');
		}
	}
}

//test mode C will show the iframes
//test mode D will allow certain actions in testing which would not be allowed otherwise
foreach(array('C','D') as $v){
	if($_GET['testMode'.$v] || $_GET['testmode'.strtolower($v)]){
		$_SESSION['special']['testMode'.$v]=1;
	}
	if($_SESSION['special']['testMode'.$v])$GLOBALS['testMode'.$v]=1;
}

//wholesale access
if($_SESSION['cnx'][$MASTER_DATABASE]['wholesaleAccess']>=8 /* WHOLESALE_APPROVED */){
	$wholesale=true;
}else{
	$wholesale=false;
}

//browser detect
if(preg_match('/^Mozilla\/4/i',$_SERVER['HTTP_USER_AGENT'])){
	$browser='IE';
}else if(preg_match('/^Mozilla\/5/i',$_SERVER['HTTP_USER_AGENT'])){
	$browser='Moz';
}else if(!stristr($_SERVER['HTTP_USER_AGENT'],'Gigabot') && !stristr($_SERVER['HTTP_USER_AGENT'],'msnbot')){
	//mail($developerEmail,'Unknown browser type','Called page: '.$_SERVER['PHP_SELF']."\nUser agent: ".$_SERVER['HTTP_USER_AGENT'],$fromHdrBugs);
	$browser='Moz'; #assume
}

//session.special.[account name].adminMode is set separately
if(isset($adminMode) && $adminMode==='0'){
	unset($_SESSION['special'][$MASTER_DATABASE]['adminMode']);
	$adminMode=false;
}else if($_SESSION['special'][$MASTER_DATABASE]['adminMode']){
	$adminMode=true;
}else{
	$adminMode=false;
}

//very basic layout variables
$centerPage=true;

//global variables and arrays
$canada = array('AB','BC','MB','NB','NF','NS','NT','ON','PE','PQ','QC','SK','YT');
$militaryPOs = array('AA','AE','AP');
$normalTitles = array('Mr.','Mrs.','Dr.','Rev.','Ms.');

//wholesale access
if($_SESSION['cnx'][$MASTER_DATABASE]['wholesaleAccess']>=8 /* WHOLESALE_APPROVED */){
	$wholesale=true;
}else{
	$wholesale=false;
}
//relatebase links
$RelateBaseAuthKey=''; //for automatic login
$RelateBaseUFTLink=''; //where to go for the UFT interface
$RelateBaseViewID['items'][0]=0;
$RelateBaseViewID['categories'][0]=0;
$RelateBaseViewID['contacts'][0]=0;
$RelateBaseViewID['invoices'][0]=0;

//shopping cart account (username)
$cartAcct=preg_replace('/_[a-z0-9]+$/i','',$MASTER_DATABASE);
$mid='';  //module ID
//location of shopping cart
$shoppingCartURL = 'https://www.relatebase.com/c/cart/en/v300/index.php?sessionid='.($sessionid ? $sessionid : $GLOBALS['PHPSESSID']).'&acct='.$cartAcct.'&mid='.$mid;
$useIframesForAdd=true;

$FUNCTION_ROOT=preg_replace('/config\.php$/','',__FILE__).'functions';
$COMPONENT_ROOT=preg_replace('/config\.php$/','',__FILE__).'components';
$EMAIL_ROOT=preg_replace('/config\.php$/','',__FILE__).'emails';
$PROTOCOL_ROOT='/home/rbase/lib/devteam/php/protocols';
$SNIPPET_ROOT='/home/rbase/lib/devteam/php/snippets';
$SQL_ROOT='/home/rbase/lib/devteam/php/sql';

if(!function_exists('CMSB'))
require($FUNCTION_ROOT.'/function_CMSB_v120.php');
if(!function_exists('enhanced_mail'))
require($FUNCTION_ROOT.'/function_enhanced_mail_v211.php');
if(!function_exists('enhanced_parse_url'))
require($FUNCTION_ROOT.'/function_enhanced_parse_url_v100.php');
if(!function_exists('get_file_assets'))
require($FUNCTION_ROOT.'/function_get_file_assets_v100.php');
if(!function_exists('image_dims'))
require($FUNCTION_ROOT.'/function_image_dims_v100.php');
if(!function_exists('js_email_encryptor'))
require($FUNCTION_ROOT.'/function_js_email_encryptor_v100.php');
if(!function_exists('metatags_i1'))
require($FUNCTION_ROOT.'/function_metatags_i1_v101.php');
if(!function_exists('pk_encode'))
require($FUNCTION_ROOT.'/function_pk_encode_decode.php');
if(!function_exists('prn'))
require($FUNCTION_ROOT.'/function_prn.php');
if(!function_exists('q'))
require($FUNCTION_ROOT.'/function_q_v130.php');
if(!function_exists('site_track'))
require($FUNCTION_ROOT.'/function_site_track_v101.php');
if(!function_exists('sql_insert_update_generic'))
require($FUNCTION_ROOT.'/function_sql_insert_update_generic_v100.php');
if(!function_exists('stats_collection'))
require($FUNCTION_ROOT.'/function_stats_collection_v110.php');
if(!function_exists('function_sql_autoinc_text'))
require($FUNCTION_ROOT.'/function_sql_autoinc_text_v232.php');
if(!function_exists('subkey_sort'))
require($FUNCTION_ROOT.'/function_array_subkey_sort_v203.php');

$qx['defCnxMethod']=C_MASTER;

//Q connection
$thumb_width=135; //width of thumbnail images in products.php
$large_width=300; //width of large images in single.php

//event constants:
define(ONFIRSTVISIT,1);
define(ONAPPLICATIONSUBMIT,2);
define(ONCARTADD,3);
define(ONCARTSUBMIT,4);
define(ONORDERPLACE,5);
define(ONORDERAPPROVED,6);

//these are standard as of shopping cart v2.01 - and the only ones that will be used anywhere - taxableness is gotten from the db
$productTable[1]='SKU';
$productTable[2]='Name';
$productTable[3]='Weight'; //in ounces
$productTable[4]='Description';
$productTable[5]='LongDescription';
$_settings['retailPriceField']='UnitPrice';
$_settings['salePriceField']='UnitPrice2';
$_settings['wholesalePriceField']='WholesalePrice';

$pageHandles['categoryPage']='categories.php';
$pageHandles['subCategoryPage']='subcategories.php';
$pageHandles['productsPage']='products.php';
$pageHandles['singlePage']='single.php';
$pageHandles['newsList']='CPM-Org-News-Press-List.php';
$pageHandles['newsFocus']='CPM-Organization-News-Press.php';
$pageHandles['calList']='EventCalendar.php';
$pageHandles['calFocus']='EventCalendarItem.php';


//Set Overture/Yahoo or Google AdWords term is present - string format is:
//c=1&s=o&t=1 - campaign=1, source=overture, term=1 (must know what that is)


function get_globals($msg=''){
	ob_start();
	//snapshot of globals
	$a=$GLOBALS;
	//unset redundant nodes
	unset($a['HTTP_SERVER_VARS'], $a['HTTP_ENV_VARS'], $a['HTTP_GET_VARS'], $a['HTTP_COOKIE_VARS'], $a['HTTP_SESSION_VARS'], $a['HTTP_POST_FILES'], $a['GLOBALS']);
	print_r($a);
	unset($a);
	$out=ob_get_contents();
	ob_end_clean();
	return $msg . "\n\n" . $out;
}
function error_alert($x,$continue=false){
	global $assumeErrorState;
	?><script language="javascript" type="text/javascript">
	alert('<?php echo $x?>');
	</script><?php
	if(!$continue){
		$assumeErrorState=false;
		exit;
	}
}
function valid_email($x){
	if(preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/',$x))return true;
	return false;
}
function page_end(){
	//2007-01-16: add stats collection version 1.00
	return false;
	global $excludePageFromStats, $bufferBodyForEditableRegion, $SUPER_MASTER_HOSTNAME;
	if(!$excludePageFromStats) stats_collection(false,array('cnx'=>$SUPER_MASTER_HOSTNAME));

	//clear out the page if isolating editable regions
	if($bufferBodyForEditableRegion)ob_end_clean();
}
function h($x){
	return htmlentities($x);
}
//shutdown functions
/* we eval this so any code optimizer doesn't rename the function */
function iframe_shutdown(){
	/*
	version 1.01 2007-03-21 @6:21AM - cleaned things up and started depending on external fctns like get_globals(); this version was used in jboyce.com
	*/
	global $store_html_output, $assumeErrorState, $parentUnSubControl, $suppressNormalIframeShutdownJS, $developerEmail,$fromHdrBugs;
	if(!$suppressNormalIframeShutdownJS){
		?><script>
		//notify the waiting parent of success, prevent timeout call of function
		window.parent.submitting=false;
		try{
			if(<?php echo $parentUnSubControl ? 'true' : 'false' ?>){
				eval('<?php echo $parentUnSubControl?>');
			}else{
				window.parent.document.getElementById('SubmitApplication').disabled=false;
				window.parent.document.getElementById('SubmitStatus1').innerHTML=' ';
			}
		}catch(e){ }
		/*** optional: ***/
		//window.parent.window.body.cursor.style='pointer';
		</script><?php
	}
	if(!$assumeErrorState){
		flush();
		return false; //that's all, folks
	}

	//handle errors
	?><script>
	//for the end user - you can improve this rather scary-sounding message
	try{
		window.parent.g('ctrlSection').style.display='block';
	}catch(e){ }
	alert('We are sorry but there has been an abnormal error while submitting your information, and staff have been emailed.  Please try refreshing the page and entering your information again');
	</script><?php

	//we also mail that this has happened
	$mail='File: '.__FILE__."\n".'Line: '.__LINE__."\n";
	$mail.="There has been an abnormal shutdown in this page.  Attached are the environment variables:\n\n";
	$mail.=get_globals();
	//Page Output - normally we print out results after each SQL query for example
	if($store_html_output){
		$mail.=$store_html_output . "\n\n";
	}
	//Globals - you may find this unnecessary if your process outputting was good
	$printGlobals=true;
	
	//send email notification
	mail($developerEmail,'Abnormal shutdown', $mail, $fromHdrBugs);
	return true;
}
function store_html_output($buffer){
	//PHP sends the output buffer before shutting down (error or otherwise).  This catches the buffer prior to shutdown
	global $store_html_output;
	$store_html_output=$buffer;
	return $buffer;
}

function stripslashes_deep($value){
	$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
	return $value;
}
