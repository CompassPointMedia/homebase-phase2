<?php 
/* -------------------------------------
2013-03-28:
* upgraded error_alert() - best yet but takes reading to use right
2010-11-20
----------
Home Base; cpm180. Developed from G iocosaC are with hopes of the whole concept of roles, processes, offices and etc. being worthy enough to serve as a base for future applications such as event planners and the like.  Functions and coding reduced pretty much to essentials.

----------------------------------------- */

//this needs to be contextual on prod vs. qa env - send to apache
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);


//this line added 2012-04-12: AJAX posts do not have session but we can pull it this way (within same domain)
if(isset($_POST['PHPSESSID'])) $sessionid=$_POST['PHPSESSID'];
if(@strlen($sessionid)) session_id($sessionid);
session_start();
@$sessionid ? '' : $sessionid = session_id();

//--------------- 2013-01-09 workarounds on globals and magic_gpc_quotes -----------------
if(!function_exists('needslashes_wrong')){
	function needslashes_wrong($str, $escape='\\\'"'){
		/*
		2013-01-09: this function is wrong, but it WILL keep a string from failing an SQL query.  For example, if a string has \\ in it, needslashes_wrong in this version sees it as legal, so does not add slashes.  However, the string that goes into MySQL becomes simply \ - I am not worried about this at this point, more irritated at losing ability to set php_value magic_quotes_gpc 
		*/
		$rand=md5(rand(1,1000000));
		//double-backs are good :)
		$str=str_replace('\\\\',$rand,$str);
		for($i=0;$i<strlen($str);$i++){
			$current=substr($str,$i,1);
			if(strstr($escape,$current) && $buffer!='\\'){
				$fail=true;
				break;
			}
			$buffer=$current;
		}
		$str=str_replace($rand,'\\\\',$str);
		if(@$fail)$str=addslashes($str);
		return $str;
	}
	function needslashes_wrong_deep($value){
		$value = is_array($value) ?
		array_map('needslashes_wrong_deep', $value) :
		needslashes_wrong($value);
		return $value;
	}
}

//2013-01-08
if(!empty($_GET)){
	$_GET=needslashes_wrong_deep($_GET);
	foreach($_GET as $n=>$v){
		$$n=$GLOBALS[$n]=$v;
	}
}
if(!empty($_POST)){
	$_POST=needslashes_wrong_deep($_POST);
	foreach($_POST as $n=>$v){
		$$n=$GLOBALS[$n]=$v;
	}
}
if(count($_SERVER)<10){
	$_hack_=true;
	exit('problems reading $_SERVER - PROBABLY A PHP.INI READ ERROR');
}
//------------------------------ end workarounds -----------------------------
$cronToken='3f6c1fe9db86dd1525d08fbe2d0738f0';
$applicationVersion='5.0';
$ctime=time();
//this is the system configuration file, created by user systeam 
if(!isset($localSys['scriptID']) || !isset($localSys['scriptVersion']))exit('CONFIG.PHP: Script ID (handle) and version not declared for '.$_SERVER['PHP_SELF'].', componentID is optional');

$enhanced_mail['logmail']=false;

$bugger=array(
	/*'subdomain'=>'hmr',*/
);
if(@$bugger['subdomain']){
	//2013-01-24 MIVA server tech support IT Christmas present; no longer used
	if($GCUserName=$_SESSION['setAcct']){
		//request switch account
		$a=explode('.',$_SERVER['HTTP_HOST']);
		setcookie('_acct_',strtolower($_SESSION['setAcct']),time()+3600*24*60,'/','.'.$a[count($a)-2].'.'.$a[count($a)-1]);
		$_COOKIE['_acct_']=$GCUserName;
		unset($_SESSION['setAcct']);		
	}else if($GCUserName=strtolower($_REQUEST['acct'])){
		//request switch account
		$a=explode('.',$_SERVER['HTTP_HOST']);
		setcookie('_acct_',strtolower($_REQUEST['acct']),time()+3600*24*60,'/','.'.$a[count($a)-2].'.'.$a[count($a)-1]);
		$_COOKIE['_acct_']=$GCUserName;
	}else if($GCUserName=$_COOKIE['_acct_']){
		//OK - have cookie
	}else{
		//fail
		header('Location: /');
		exit('no request or cookie present');
	}
}else{
	$GCUserName=explode('.',$_SERVER['HTTP_HOST']);
	array_pop($GCUserName);
	array_pop($GCUserName);
	$GCUserName=end($GCUserName);
}

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

//2010-02-06 this gets overwritten by ap settings
$bookkeeperEmail='bookkeeper@'.$_SERVER['SERVER_NAME'];

//2010-02-06 roots moved above config.master
$general_root=str_replace('gf5/console/systeam/php/config.php','',__FILE__);
$FUNCTION_ROOT = $general_root.'functions';
$SNIPPET_ROOT  = $general_root.'gf5/console/snippets';
$COMPONENT_ROOT= $general_root.'gf5/console/components';
$MASTER_COMPONENT_ROOT=$general_root.'components';
$globalSetCtrlFields=true;

define("ROLE_DBADMIN",'1');
define("ROLE_ADMIN",'2');
define("ROLE_MANAGER",'3');
define("ROLE_AGENT",'10');
define("ROLE_CLIENT",'25');

$userType[ROLE_DBADMIN]='DB Administrator'; //required
$userType[ROLE_ADMIN]='Administrative'; //required
$userType[ROLE_MANAGER]='Manager'; //required
$userType[ROLE_AGENT]='Data Entry';
$userType[ROLE_CLIENT]='Client';
//gets MASTER_DATABASE
if(false)$defaultApSettings=array(
	/******* not used on Home Base just yet *******/
	'implementRegionalDirector'=>false,
	'implementVendorID'=>false, /* this is for foster home */
	'implementClientID'=>false, /* this is for children */
	'implementRTC'=>false, /* Residential Treatment Center */
	/* I used this until the certificate is installed */
	'secureProtocolPresent'=>false,
	'emails'=>array(
		'newFosterParentEmail'=>'email_1070_newfosterparent.php'
	),
);
$defaultAdminSettings=array(
	ROLE_DBADMIN=>array(
		/* 2010-02-06: these are settings a db admin would set such as backup location, ftp, mail headers and hooks into advanced features such as how SQL queries for reports are interpreted, even future API's.. */
		'registeredTemplates'=>array(
			'New Map'=>array(
				'file'=>'email_1000_item_created.php',
			),
			'New Export'=>array(
				'file'=>'email_2000_new_export.php',
			),
		),
		'sendObjects'=>array(
			'new_map'=>array(
				'label'=>'New Map Added',
				'when'=> 'a new map is added when an uploaded file is completely processed with descriptions and category/subcategory',
				'permissions'=>16 /*PERM_FINANCIAL*/ + 8 /*PERM_CLERICAL*/ + 4 /*PERM_THERAPEUTIC*/,
				'objectTable'=>'finan_items',
				
				/* -- user controlled settings (dbadmin) -- */
				'notifyOfficeLevel'=>false,
				'notifyUpOneLevel'=>false,
				'specificSends'=>array('bais_staff:ric','bais_staff:sam'),
				'template'=>'New Map',
			),
			'new_export'=>array(
				'label'=>'New Export Performed',
				'when'=> 'a new export is performed when a vendor and to-be-exported records are selected, usually for a specific vendor and via an export profile',
				'permissions'=>16 /*PERM_FINANCIAL*/ + 8 /*PERM_CLERICAL*/ + 4 /*PERM_THERAPEUTIC*/,
				'objectTable'=>'gen_batches',
				
				/* -- user controlled settings (dbadmin) -- */
				'notifyOfficeLevel'=>false,
				'notifyUpOneLevel'=>false,
				'specificSends'=>array('bais_staff:ric','bais_staff:sam'),
				'template'=>'New Export',
			),
		),
	),
	ROLE_ADMIN=>array(
		/* these are overall settings for the site which a senior or foundation director can control */
		'wordFoundationDirector'=>'Foundation Director',
		'wordShortFoundationDirector'=>'Foundation Director',
		'wordAcroFoundationDirector'=>'FD',
		
		'wordRegionalDirector'=>'Regional Director',
		'wordShortRegionalDirector'=>'Reg. Dir.',
		'wordAcroRegionalDirector'=>'RD',
		
		'wordProgramDirector'=>'Program Director',
		'wordShortProgramDirector'=>'Prog. Dir.',
		'wordAcroProgramDirector'=>'PD',
		
		'wordCaseManager'=>'Case Manager',
		'wordShortCaseManager'=>'Case Mgr.',
		'wordAcroCaseManager'=>'CM'
	),
	'offices'=>array(
		/* ordered by bais_universal.un_id joined to bais_orgaliases */
		1=>array(
		
		),
		2=>array(
		
		)
	)
);
require($_SERVER['DOCUMENT_ROOT'].'/config.master.php');
$DOCUMENTATION_IMAGE_ROOT=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName;

//public used for states and countries, generic data
if(@$_hbs_scheme_=='controlled'){
	$public_cnx=array('localhost','cpm180_public','readonly','cpm180');
}else{
	$public_cnx=array($MASTER_HOSTNAME,$MASTER_USERNAME,$MASTER_PASSWORD,$MASTER_DATABASE);
}
//system-level emails
$developerEmail='samuelf@compasspoint-sw.com';
$developerEmailNotice='developer.cpm180@compasspoint-sw.com';
$developerEmailWarning='developer.cpm180@compasspoint-sw.com';
$developerEmailFatalError='fatalerrors@compasspoint-sw.com';

$fromHdrBugs='From: bugreports@'.$_SERVER['HTTP_HOST'];
$fromHdrNotices='From: notices@'.$_SERVER['HTTP_HOST'];
$doNotReplyEmail='From: do-not-reply@'.$_SERVER['HTTP_HOST'];

$siteURL='http://'.$_SERVER['HTTP_HOST'];

if(!function_exists('q'))
require($FUNCTION_ROOT.'/function_q_v130.php');
$qx['defCnxMethod']=C_MASTER;
$qx['slowQueryThreshold']=1.5;

$tabLibraryPath='../../';

//things that need abstracted to the individual acct databases
if(!preg_match('/^[a-f0-9]{32}/',@$MASTER_DBADMIN_PASSWORD))$MASTER_DBADMIN_PASSWORD='c52dd5e4abac566effee7eca8548eb46';
$minPasswordLength=4;
$maxPasswordLength=20;

//name of this page
$a=preg_split('/\\\|\//',$_SERVER['PHP_SELF']);
$thispage=$a[count($a)-1];
if(count($a)>2){
	$thisfolder=$a[count($a)-2];
}else{
	$thisfolder='';
}

//browser detection
if(preg_match('/^Mozilla\/4/i',$_SERVER['HTTP_USER_AGENT'])){
	//Internet Explorer current versions
	$browser='IE';
}else if(preg_match('/^Mozilla\/5/i',$_SERVER['HTTP_USER_AGENT'])){
	//Firefox, Mozilla
	$browser='Moz';
}else if(!preg_match('/gigabot|msnbot/i',$_SERVER['HTTP_USER_AGENT'])){
	//mail($developerEmail,'Unknown browser type',$_SERVER['HTTP_USER_AGENT'].', called from file '. $thisfolder . '/'. $thispage,$fromHdrBugs);
	$browser='Moz'; #assume
}

//standardize the date and time stamps for long or repeated queries
function gmicrotime($n){
	//store array of all calls
	global $gmicrotime, $mT;
	list($usec, $sec) = explode(' ',microtime());
	$mT[]=$gmicrotime[$n]=round((float)$usec + (float)$sec,6);
	//return elapsed since last call
	if(count($mT)>1)return round(1000*($mT[count($mT)-1]-$mT[count($mT)-2]),6);
}
gmicrotime('initial');
//for long queries, standardize the date and time stamps for the action
$dateStamp=date('Y-m-d H:i:s');
$timeStamp=date('YmdHis',strtotime($dateStamp));
define("_1900", '1900-01-01 00:00:00');
define("OPEN_ENDED", '0000-00-00 00:00:00');
$_1900=_1900;
$INFINITY_DATE='9999-12-31';
$OPEN_ENDED=OPEN_ENDED;

//added 2012-04-27
$settingsNodes=array(
	'settings.agent'=>array(
		'bulletins','search',
	),
);

//colors
$settings['restraint']['bgColor']='#F4DCDB';
$settings['incident']['bgColor']='#FFF8D2';
$settings['prognote']['bgColor']='#D7F0CE';
$settings['restraint']['bgColorLight']='#F9ECEC';
$settings['incident']['bgColorLight']='#FFFCEB';
$settings['prognote']['bgColorLight']='#EFF9EC';

$logStatuses[1]=array('#cccccc','#333'); //light grey
$logStatuses[2]=array('#90ee90','#333'); //light green
$logStatuses[5]=array('#fffceb','#000'); //light cream
$logStatuses[10]=array('#c8ba6a','#FFF'); //light olive
$logStatuses[15]=array('#cd3834','#FFF'); //brighter red
$logStatuses[20]=array('#e0c1c1','#FFF'); //light rose red
$logStatuses[25]=array('#6f4891','#FFF'); //purple
$logStatuses[30]=array('#ea9515','#FFF'); //light gold

//dataset constants
define('COL_VISIBLE',16,false);
define('COL_AVAILABLE',8,false);
define('COL_SYSTEM',4,false);
define('COL_HIDDEN',2,false);
define('COL_RESTRICTED',1,false);

//administrative permissions constants - added 2010-05-10
define('PERM_MEDICAL',1);
define('PERM_CLINICAL',2);
define('PERM_ADMINISTRATIVE',4);
define('PERM_CLERICAL',8);
define('PERM_FINANCIAL',16);
define('PERM_ALL',31);

//for report_deletability
define('DELETE_YES',16);
define('DELETE_NOTIFYCM',12);
define('DELETE_NOTIFYAUTHOR',11);
define('DELETE_REQUEST',4);
define('DELETE_NO',0);

//utilities

define('PRINT_BASIC',0.0478,true);
define('PRINT_LAMINATED',0.071,true);
define('PRINT_GICLEE',.10868,true);
define('PRINT_CANVAS',.13767,true);

define('PRINT_BASIC_MARKUP',2.00,true);
define('PRINT_LAMINATED_MARKUP',0.0,true);
define('PRINT_GICLEE_MARKUP',0.0,true);
define('PRINT_CANVAS_MARKUP',0.0,true);

define('PROD_BASIC',1);
define('PROD_LAMINATED',2);
define('PROD_GICLEE',3);
define('PROD_CANVAS',4);
define('PROD_DIGITAL',9);

//global variables and arrays
$canada 				= array('AB','BC','MB','NB','NF','NS','NT','ON','PE','PQ','QC','SK','YT');
$militaryPOs			= array('AA','AE','AP');
$normalTitles 			= array('Mr.','Mrs.','Dr.','Rev.','Ms.');
$blankFills = array(
	'un_username'=>' User Name',
	'Email'=>' Email',
	'FirstName'=>' First Name',
	'MiddleName'=>' M.N.',
	'LastName'=>' Last Name',
	'Address'=>' Address',
	'City'=>' City',
	'Phone'=>' Phone',
	'WorkPhone'=>' Work Phone',
	'Fax'=>' Fax',
	'Zip'=>' Zip',
	'PagerVoice'=>' Pager/Voice',
	'Notes'=>' Notes',
	'MisctextFosterhomenotes'=>' Notes',
	'MisctextParentnotes'=>' Notes',
	'MisctextStaffnotes'=>' Notes',
	'DateReleased'=>' (N/A)'
);
$genders=array(
	'M'=>'Male',
	'F'=>'Female',
	'm'=>'Male',
	'f'=>'Female'
);
$persistPostNewValues=true;
if(count($_POST))
foreach($_POST as $n=>$v){
	if(strlen($_POST[$n.'_RBADDNEW']) && $_POST[$n.'_RBADDNEWMODIFICATION']=='distinct'){
		if($_POST[$n]=='{RBADDNEW}'){
			//as it should be
			unset($$n,$_POST[$n]);
			$$n=$_POST[$n]=$_POST[$n.'_RBADDNEW'];
			if(!$persistPostNewValues)unset($GLOBALS[$n.'_RBADDNEW'], $_POST[$n.'_RBADDNEW'], $GLOBALS[$n.'_RBADDNEWMODIFICATION'], $_POST[$n.'_RBADDNEWMODIFICATION']);
		}else{
			//js error - should not happen
			mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
		}
	}
}
$modApType='embedded';
$modApHandle='first';
$tabVersion=3;


$finan_itemsFields=array(
	'AMAZON'=>array(
		'listingid' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'Quantity' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'OpenDate' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'itemismarketplace' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'productidtype' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'itemcondition' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'asin1' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'Text1' => array(
			'description'=>'',
			'type'=>'calculated',
			'fieldtype'=>'textarea',
			'cols'=>80,
			'rows'=>6,
		),
		'Text2' => array(
			'description'=>'',
			'type'=>'calculated',
			'fieldtype'=>'textarea',
			'cols'=>65,
			'rows'=>5,
		),
	),
	'EBAY'=>array(
		'ItemID' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'QuantityAvailable' => array(
			'description'=>'',
			'type'=>'calculated',
			'attributes'=>array(
				'size'=>5,
			),
			'default'=>10,
		),
		'Purchases' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'Bids' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'Type' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'CategoryLeafName' => array(
			'description'=>'',
			'type'=>'calculated',
		),
		'CategoryNumber' => array(
			'description'=>'',
			'type'=>'calculated',
		),
	),
	'MIVA'=>array(
	),
);

$itemsFieldsUse=array(
	'category' => 1,
	'createdate' => 1,
	'creator' => 1,
	'description' => 1,
	'metakeywords' => 1,
	'metatitle' => 1,
	'name' => 1,
	'subcategory' => 1,
	'subtheme' => 1,
	'id' => 1,
	'pk' => 0,
	'um' => 0,
	'accounts_id' => 0,
	'active' => 0,
	'amazon_asin1' => 0,
	'amazon_itemcondition' => 0,
	'amazon_itemismarketplace' => 0,
	'amazon_listingid' => 0,
	'amazon_opendate' => 0,
	'amazon_productidtype' => 0,
	'amazon_quantity' => 0,
	'amazon_text1' => 0,
	'amazon_text2' => 0,
	'amazon_tobeexported' => 1,
	'assetaccounts_id' => 0,
	'brand' => 1,
	'breakprice' => 0,
	'caption' => 1,
	'category_codes' => 1,
	'classes_id' => 0,
	'cogsaccounts_id' => 0,
	'del' => 0,
	'depth' => 1,
	'dpi1' => 1,
	'ebay_bids' => 0,
	'ebay_categoryleafname' => 0,
	'ebay_categorynumber' => 0,
	'ebay_itemid' => 0,
	'ebay_purchases' => 0,
	'ebay_quantityavailable' => 0,
	'ebay_tobeexported' => 1,
	'ebay_type' => 0,
	'editdate' => 1,
	'editdatebuffer' => 0,
	'edition' => 0,
	'editor' => 1,
	'expirationdate' => 0,
	'exportdate' => 0,
	'exporter' => 0,
	'featured' => 1,
	'filesize' => 1,
	'function' => 1,
	'grouping' => 1,
	'groupleader' => 0,
	'hbs_originalfilename' => 0,
	'hbs_year' => 1,
	'hbs_yearestimated' => 1,
	'height1' => 1,
	'hmr_oldsku' => 0,
	'hmr_price1' => 1,
	'hmr_price2' => 1,
	'hmr_price3' => 1,
	'hmr_price4' => 1,
	'hmr_upccheckdigit' => 1,
	'ignorefile' => 0,
	'instock' => 0,
	'islocked' => 0,
	'ispassedthrough' => 0,
	'itemfootnote' => 0,
	'items_id' => 0,
	'keywords' => 1,
	'lat1' => 1,
	'lat2' => 1,
	'lat3' => 1,
	'lat4' => 1,
	'lat5' => 0,
	'lat6' => 0,
	'lat7' => 0,
	'length' => 0,
	'listprice' => 0,
	'longdescription' => 1,
	'manufacturer' => 1,
	'manufacturersku' => 0,
	'manufacturers_id' => 0,
	'metadescription' => 1,
	'miva_tobeexported' => 1,
	'model' => 0,
	'new_subcategory' => 0,
	'notes' => 0,
	'ourgroup_id' => 0,
	'outofstock' => 0,
	'overflowtype' => 0,
	'primarycountry' => 1,
	'primaryregion' => 1,
	'priority' => 0,
	'product_code' => 0,
	'purchaseprice' => 0,
	'realtime' => 0,
	'refnbr' => 0,
	'reorderpt' => 0,
	'resourcetoken' => 0,
	'resourcetype' => 0,
	'rwb' => 0,
	'schemas_id' => 0,
	'scom_ldsku' => 0,
	'scom_liid' => 0,
	'seo_archivefilepath' => 0,
	'seo_filename' => 0,
	'sessionkey' => 0,
	'shippers_id' => 0,
	'sku' => 1,
	'source' => 0,
	'special' => 1,
	'subfunction' => 1,
	'supersku' => 0,
	'taxable' => 0,
	'taxable2' => 0,
	'theme' => 0,
	'thumbdata' => 0,
	'tobeexported' => 0,
	'type' => 0,
	'unitprice' => 0,
	'unitprice2' => 0,
	'upc' => 1,
	'vat' => 0,
	'vendor' => 0,
	'vendors_id' => 0,
	'weight' => 0,
	'wholesaleprice' => 0,
	'width' => 0,
	'width1' => 1,
);

$vendors=array(
	/* added 2012-09-26 not sure what data goes in just yet */
	'settings'=>array(
		'MIVA'=>array(),
		'AMAZON'=>array(),
		'EBAY'=>array(),
	),
);
//added 2013-05-08
$array_to_csv['always_trim']=true;

//------------------ functions only from here ----------------------
function searchtype($options=array()){
	/* created 2012-08-26 pulled from comp_itemsquery_v100.php so that this would become a generic system including from the dataset query which is held in session; basically this is the protocol for ALL search eventually, with input=what I am searching for and output=various */
	extract($options);
	if(!$searchtype)global $searchtype;
	if(!$q)global $q;
	if($searchtype=='SKU1'){
		$a=q("SELECT ID, CreateDate, Creator, Category, SKU, SEO_Filename, Name, Description, LongDescription, Featured FROM finan_items WHERE ResourceType IS NOT NULL AND SKU LIKE '".substr($q,0,4)."%' ORDER BY SKU", O_ARRAY);
		$title='Products Matching \''.substr($q,0,4).'%\'';
		$subtitle='('.count($a).' matches)';
	}else if($searchtype=='batch'){
		$a=q("SELECT i.ID, i.CreateDate, i.Creator, i.Category, i.SKU, i.SEO_Filename, i.Name, i.Description, i.LongDescription, i.Featured FROM finan_items i, gen_batches_entries e WHERE i.ID=e.Objects_ID AND e.Batches_ID=$q ORDER BY SKU", O_ARRAY);
		$title='Products in batch number '. $q;
		$subtitle='('.count($a).' matches)';
	}else if($searchtype=='merchanttobeexported'){
		$a=q("SELECT ID, CreateDate, Creator, Category, SKU, SEO_Filename, Name, Description, LongDescription, Featured FROM finan_items WHERE ResourceType IS NOT NULL AND ".$q."_ToBeExported >0 ORDER BY SKU", O_ARRAY);
		$title='Products to be exported to '. $q;
		$subtitle='('.count($a).' matches)';
	}else if($searchtype=='categorysubcategory'){
		$q=explode('|',$q);
		$a=q("SELECT ID, CreateDate, Creator, Category, SKU, SEO_Filename, Name, Description, LongDescription, Featured FROM finan_items WHERE ResourceType IS NOT NULL AND Category='".$q[0]."' AND SubCategory='".$q[1]."' ORDER BY SKU", O_ARRAY);
		$title='Category: '.stripslashes($q[0]).' - Subcategory: '.stripslashes($q[1]);
		$subtitle='('.count($a).' matches)';
	}else if($searchtype=='generic'){
		$a=q("SELECT ID, CreateDate, Creator, Category, SKU, SEO_Filename, Name, Description, LongDescription, Featured FROM finan_items WHERE ".stripslashes($q), O_ARRAY);
		$title='General Search: '.stripslashes($q);
		$subtitle='generic SQL query, '.count($a).' matches';
	}else if($searchtype=='grouping'){
		$a=q("SELECT ID, CreateDate, Creator, Category, SKU, SEO_Filename, Name, Description, LongDescription, Featured FROM finan_items WHERE Grouping=".$q, O_ARRAY);
		$title='Grouping Search: '.stripslashes($q);
		$subtitle='generic SQL query, '.count($a).' matches';
	}else if($searchtype=='location'){
		global $toggle;
		$a=q("SELECT ID, CreateDate, Creator, Category, SKU, SEO_Filename, Name, Description, LongDescription, Featured FROM finan_items WHERE ResourceType IS NOT NULL AND (".($toggle==='0' ? 'Lat1=\'\' OR Lat1 IS NULL': 'Lat1<>\'\'').") ORDER BY SKU", O_ARRAY);
		$title='Polygon Search';
		$subtitle='All maps that have a <u>polygon location</u> specified; '.count($a).' matches';
	}else{
		exit('variable $searchtype not passed properly');
	}
	return array(
		'title'=>$title,
		'subtitle'=>$subtitle,
		'records'=>$a,
	);
}
function minroles(){
	return @min($_SESSION['admin']['roles']);
}
function sun($n=''){
	@extract($_SESSION['admin']);
	switch($n){
		case 'e': return $email;
		case 'fl': return $firstName . ' '. $lastName;
		case 'lf': return $lastName . ', '.$firstName;
		case 'lfi': return $lastName.', '.$firstName.($middleName?' '.substr($middleName,0,1).'.':'');
		default: return $userName;
	}
}
function eOK($l=''){
	global $assumeErrorState,$suppressPrintEnv;
	$assumeErrorState=false;
	exit($suppressPrintEnv || !$l ? '' : 'exit line '.$l);
}
function list_chain_above(){

}
function list_chain_below(){

}
function list_peers(){

}
function list_offices($mode='keys', $userName='', $roles=''){
	/*
	2008-01-28 - returns a query for everything in bais_universal, bais_orgaliases, or keys as associative username->oa_businessname.  This also gives the office when the only role is that of a parent; this is not a permission to that office or the CMs/FH's below that office so be careful
	*/
	global $fl, $ln, $qr;
	global $list_offices;
	global $globTest;
	//default user
	if(!$userName){
		$userName=sun();
		$roles=$_SESSION['admin']['roles'];
	}else{
		//get roles - this must be EXPLICITLY PASSED IN THE CASE OF A FOSTER PARENT
		if(!$roles)$roles=q("SELECT REPLACE(sr_roid,'.0',''), REPLACE(sr_roid,'.0','') FROM bais_StaffRoles WHERE sr_stusername='$userName'", O_COL_ASSOC);
	}
	if($roles[ROLE_ADMIN]){
		//keys
		return q("SELECT oa_unusername FROM bais_orgaliases ORDER BY oa_businessname", O_COL);
	}else{
		//modified 2010-06-02 use native offices of foster homes
		if($roles[ROLE_AGENT]){
			$b=q("SELECT os_unusername FROM bais_OfficesStaff WHERE os_stusername='$userName'", O_COL);
		}
		if($roles[ROLE_MANAGER]){
			$c=q("SELECT so_unusername FROM bais_StaffOffices WHERE so_stusername='$userName'", O_COL);
		}
		if($b || $c){
			$int=array('b','c');
			ob_start();
			foreach($int as $v){
				foreach($$v as $w){
					$e[$w]=true;
				}
			}
			ob_end_clean();
			return array_keys($e);
		}
	}
}
function h($v){
	return htmlentities($v);
}
function stripslashes_deep($value){
	$value = is_array($value) ?
	array_map('stripslashes_deep', $value) :
	stripslashes($value);
	return $value;
}
function addslashes_deep($value){
	$value = is_array($value) ?
	array_map('addslashes_deep', $value) :
	addslashes($value);
	return $value;
}
if(!function_exists('get_globals')){
	function get_globals($msg=''){
		ob_start();
		//snapshot of globals
		$a=$GLOBALS;
		//unset redundant nodes
		unset($a['HTTP_SERVER_VARS'], $a['HTTP_ENV_VARS'], $a['HTTP_GET_VARS'], $a['HTTP_COOKIE_VARS'], $a['HTTP_SESSION_VARS'], $a['HTTP_POST_FILES'],$a['HTTP_POST_VARS']);
		print_r($a);
		unset($a);
		$out=ob_get_contents();
		$out=str_replace('[GLOBALS] => Array
 *RECURSION*
  ','',$out);
		
		ob_end_clean();
		return $msg.$out;
	}
}
function error_alert($str,$options=array()){
	/* 2013-03-28 universal function from all the ones I had
	options:
		continue - original var, don't exit the script
		script - javascript code, happens before the alert
		focusField - field to focus on (id) after the alert
		callback - just another script variable; happens before the alert
		runAfter - flag, moves callback AFTER the alert
		unset - unsets that field from GLOBALS
		die - simply stop

		storeErrorAlert = md5(MASTER_PASSWORD) - this will store error messages in an array

	*/
	if(is_bool($options) || $options===1 || $options===0){
		$continue=$options;
	}else if(is_string($options)){
		$unset=$options;
	}else if(!empty($options)){
		extract($options);
	}
	global $assumeErrorState,$error_alert;
	
	//-------------------------------------
	
	//based on sql_insert_update_generic using GLOBALS as the collection
	if($unset){
		$unset=explode(',',$unset);
		foreach($unset as $v)unset($GLOBALS[$v]);
		return 'unset';
	}
	if($die){
		$assumeErrorState=false;
		exit;
	}
	//this applies to scripts that do import or other mulitple processes
	if(strlen($error_alert['storeErrorAlert']) && $error_alert['storeErrorAlert']==md5($GLOBALS['MASTER_PASSWORD'])){
		$error_alert['errors'][]=$x;
		return;
	}
	echo "\n".'<!-- error_alert() called -->'."\n";
	?><script language="javascript" type="text/javascript"><?php 
	if($script){
		echo "\n//script\n";
		echo trim($script);
	}
	if($callback && !$runAfter){
		echo "\n//callback\n";
		echo trim($callback);
	}
	echo "\n";
	if($focusField){
		echo "\n//focusField\n";
		?>window.parent.g('<?php echo $focusField?>').focus();<?php 
		echo "\n";
		?>window.parent.g('<?php echo $focusField?>').select();<?php
	}
	?>alert('<?php echo str_replace('\'','\\\'',$str);?>');<?php 
	if($callback && $runAfter){
		echo "\n//callback\n";
		echo trim($callback);
	}
	echo "\n";
	?></script><?php
	if(!$continue){
		$assumeErrorState=false;
		exit;
	}
}
function error_alert_focus($x,$focus){
	global $assumeErrorState;
	?><script defer>alert('<?php echo $x?>');
	window.parent.g('<?php echo $focus?>').focus();
	window.parent.g('<?php echo $focus?>').select();
	</script><?php
	$assumeErrorState=false;
	exit;
}
function valid_email($x){
	if(preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/',$x))return true;
	return false;
}
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
	alert('We\'re sorry but there has been an abnormal error while submitting your information, and staff have been emailed.  Please try refreshing the page and entering your information again');
	</script><?php

	//we also mail that this has happened
	global $fl,$ln;
	$mail='File: '.($fl?$fl:__FILE__)."\n".'Line: '.($ln?$ln:__LINE__)."\n";
	$mail.="There has been an abnormal shutdown in this page.  Attached are the environment variables:\n\n";
	$mail.=get_globals();
	if(strlen(serialize($mail))>1024*1024)return true;
	
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
function page_end(){
	global $Logs_history_ID, $developerEmail, $fl, $ln, $fromHdrBugs, $thispage, $thisfolder, $PageTitle, $localSys;
	/* created 2009-11-16 by Samuel
	This stores user-tangible page visits (not component refreshes) in bais_logs_history; the primary objective is to show that page as closely to what it really was (see todo list), e.g.:
		List Foster Children : Austin and Hutto, Moderate and above
		Foster Child Record: Steven Hopkins
		etc.
	There should be a way to "*" a record that has been modified
	*/
	if(!strlen($PageTitle))mail('joey@compasspoint-sw.com','Great Locations: untitled Page '.$thisfolder.'/'.$thispage,get_globals(),$fromHdrBugs);
	
	if($thispage=='search_results.php'){
		global $key;
		$search[$key]=$_SESSION['special']['search'][$key];
		$Environment=base64_encode(serialize($search));
	}
	
	//list this page and query string
	$Logs_history_ID=q("INSERT INTO bais_logs_history SET
	Logs_ID='".$_SESSION['loginID']."',
	Type='".($thispage=='bais_01_exe.php' ? 'Exe' : 'View')."',
	Page='".addslashes($thispage)."',
	".(isset($localSys['pageLevel']) ? "PageLevel='".$localSys['pageLevel']."'," : '')."
	PageTitle='".addslashes($PageTitle)."',
	Environment='".$Environment."',
	QueryString='".addslashes($_SERVER['QUERY_STRING'])."'", O_INSERTID);
}
function in_date_compare($date,$from,$to,$beforeEnd=false){
	if($date>=$from &&
	($beforeEnd && $date < $to) || (!$beforeEnd && $date <= $to))return true;
	return false;
}
function in_date_valid($date){
	if($date==_1900){
		$date=-99999999999;
	}else if($date==OPEN_ENDED){
		$date=99999999999;
	}else if(is_int($date)){
		//OK
	}else if(($date=strtotime($date))!==-1){
		//OK
	}else{
		return false;
	}
	return $date;
}
function in_date($date,$dateRange,$dateEnd='',$beforeEnd=true){
	global $in_date, $developerEmail, $fromHdrBugs;
	$in_date['err']='';
	
	if(is_int($date)){
		//OK
	}else if(($date=strtotime($date))!==-1){
		//OK
	}else{
		$in_date['err']='Invalid comparison date';
		if($in_date['debug'])mail($developerEmail,$in_date['err'],get_globals(),$fromHdrBugs);
		return false;
	}
	//parse the date range
	if(!is_array($dateRange) && strlen($dateEnd)){
		//from,to method
		#from -----------
		if(!($from=in_date_valid($dateRange))){
			$in_date['err']='Invalid from date, two-parameter method';
			if($in_date['debug'])mail($developerEmail,$in_date['err'],get_globals(),$fromHdrBugs);
			return false;
		}
		#to -----------
		if(!($from=in_date_valid($dateEnd))){
			$in_date['err']='Invalid to date, two-parameter method';
			if($in_date['debug'])mail($developerEmail,$in_date['err'],get_globals(),$fromHdrBugs);
			return false;
		}
		if(in_date_compare($date,$from,$to,$beforeEnd))return true;
		return false;
	}else{
		//recursive - this time we don't worry about invalid date strings
		foreach($dateRange as $v){
			if(strlen($v['DateAssigned']) && $from=in_date_valid($v['DateAssigned']) && strlen($v['DateReleased']) && $to=in_date_valid($v['DateReleased'])){
				//OK
				if(in_date_compare($date,$from,$to,$beforeEnd))return true;
			}else{
				//strings are not valid
			}
		}
		return false;
	}
}
function ifround($str,$round=-1){
	//number may be a string or an int
	if(preg_match('/\.0*[1-9]+/',$str)){
		if($round==-1){
			//best fit
			return preg_replace('/0*$/','',$str);
		}else{
			return round($str,$round);
		}
	}else{
		//this is an integer
		return preg_replace('/\.0*$/','',$str);
	}
}
function js_userSettings($options=array()){
	//declare js for user settings - created 2008-04-20
	extract($options);
	if($bookends)echo '<script language="javascript" type="text/javascript" id="jsUserSettings">'."\n";
	echo '//js user settings generated by PHP '."\n";
	foreach($_SESSION['userSettings'] as $n=>$v){
		if($filter && !preg_match('/'.$filter.'/i',$n))continue;
		$x=(!strlen($v) ? "''" : (is_numeric($v) ? $v : ("'".str_replace("'","\'",$v)."'")));
		if(strstr($n,':')){
			$n=explode(':',$n);
			
			if(is_numeric($n[0]))continue;
			
			if(!$called[$n[0]]){
				$called[$n[0]]=true;
				echo 'var '.$n[0].'= new Array();'."\n";
			}
			echo $n[0].'[\''.$n[1].'\']= '.$x.';'."\n";
		}else{
			if(is_numeric($n))continue;

			echo 'var '.$n . '='.$x.';'."\n";
		}
	}
	if($bookends)echo '</script>'."\n";
}
function get_object_parameters($node,$key=''){
	/*
	2009-11-14
	 this contains legacy coding on file names, id calls etc. */
	global $url, $window, $objectLabel, $size, $owString;
	switch($node){
		case 'therapists':
			$url='therapists.php?'.($key ? 'Therapists_ID='.$key : '');
			$window='l1_therapists';
			$objectLabel='Therapist Record';
			$size='700,700';
		break;
		case 'staff':
			$url='staff.php?'.($key ? 'un_username='.$key : '');
			$window='l1_pds';
			$objectLabel='Staff Record';
			$size='600,700';
		break;
		case 'locs':
			$url='loc_assign.php?'.($key ? 'Locs_ID='.$key : '');
			$window='l1_locassign';
			$objectLabel='Child Record';
			$size='700,700';
		break;
		case 'children';
			$url='children.php?'.($key ? 'Children_ID='.$key : '');
			$window='l1_children';
			$objectLabel='Child Record';
			$size='700,700';
		break;
		case 'parents';
			$url='parents.php?'.($key ? 'Parents_ID='.$key : '');
			$window='l1_parents';
			$objectLabel='Parent Record';
			$size='700,700';
		break;
		case 'fosterhomes';
			$url='homes.php?'.($key ? 'Fosterhomes_ID='.$key : '');
			$window='l1_fosterhomes';
			$objectLabel='Fosterhome Record';
			$size='700,700';
		break;
		case 'timesheets';
			$url='timesheets.php?'.($key ? 'Timesheets_ID='.$key : '');
			$window='l1_timesheets';
			$size='950,450';
			$objectLabel='Timesheet';
		break;
		case 'bulletins';
			$url='read_bulletins.php?'.($key ? 'Bulletins_ID='.$key : '');
			$window='l1_reader';
			$objectLabel='Bulletin';
			$size='700,700';
		break;
		case 'prognotes';
			$url='progress_reports.php?'.($key ? 'ID='.$key : '');
			$window='l1_progressnotes';
			$objectLabel='Progress Note';
			$size='850,650';
		break;
		case 'increports';
			$url='focus_incident_reports.php?'.($key ? 'ID='.$key : '');
			$window='l1_incident';
			$objectLabel='Incident Report';
			$size='700,700';
		break;
		case 'restreports';
			$url='focus_restraint_reports.php?'.($key ? 'ID='.$key : '');
			$window='l1_restraint';
			$objectLabel='Restraint Report';
			$size='700,700';
		break;
		case 'cmlogs';
			$url='casemanager.php?'.($key ? 'Logs_ID='.$key : '');
			$window='l1_casemanager';
			$objectLabel='Case Management Log';
			$size='700,700';
		break;
		case 'therapynotes';
			$url='clinical.php?'.($key ? 'Logs_ID='.$key : '');
			$window='l1_therapynotes';
			$objectLabel='Therapy Note';
			$size='700,700';
		break;
		default:
			error_alert('unknown object node '.$node.' - cannot get parameters');
	}
	$owString="return ow(this.href,'$window','$size');";
}
function seed_sequence($count, $seed){
	$seed=md5(strtolower($seed));
	for($i=1;$i<=$count;$i++)$a[$i]=1;
	while(true){
		$k++;
		#echo 'loop '.$k . "\n";
		if(!count($a))break;
		
		while(true){
			#echo $seed . "\n";
			for($j=1;$j<=32;$j++){
				$key=substr($seed,$j,1);
				if(!is_numeric($key))continue;
				#echo $key."\n";
				if($a[$key]){
					#echo 'found '.$key . "\n";
					$b[count($b)+1]=$key;
					unset($a[$key]);
					$seed=md5($seed);
					break;
				}
			}
			$seed=md5($seed);
			break;
		}
	}
	return $b;
}
function DHTMLmenu_evaluate($o,$k){
	global $apSettings, $_dshow_,$_dk_;
	switch($o){
		case 'Add Residential Treatment Ctr.':
			return ($apSettings['implementRTC'] ? 1 : 0);
		case 'Quickbooks Exporting':
			return ($apSettings['implementQuickBooks'] ? 1 : 0);
		case 'Reports':
			for($i=1;$i<=7;$i++){
				$_dk_++;
				$_dshow_[$_dk_]=(minroles()<ROLE_CLIENT && count($_SESSION['admin']['roles'])?1:0);
			}
			return (minroles()<ROLE_CLIENT && count($_SESSION['admin']['roles']) ? 1 : 0);
		break;
		default:
			mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
			return 1;
	}
}
function DHTMLmenu($roles=''){
	//this must get changed with the DHTML Menu Generator (sothink)
	//smallest array index is PERMISSION_LEVEL 
	/*
	2012-12-25: this has been changed so each account can have its own DHTML menu
	*/
	global $apSettings,$thispage, $_dshow_,$_dk_;
	if(!$roles)$roles=$_SESSION['admin']['roles'];
	
	global $GCUserName;
	//----------- this is cumbersome for now but is safe ----------------
	//-------------- (menus started off the same 12/25/12) --------------
	if($GCUserName=='hmr'){
		$DHTMLmenu=array(
			'(settings)'=>array( /* settings */ ),
			'Home'=>array(
				'(settings)'=>array( /* settings */ ),
				'My Home Page'=>array(false, 'home.php'),
				'Calendar'=>array(ROLE_AGENT, 'calendar.php'),
				'Instant Messaging'=>array(ROLE_AGENT, 'im.php'),
				'Notifications'=>array(false, 'notifications.php'),
				'Preferences'=>array(false, 'preferences.php'),
			),
			'Products'=>array(
				'(settings)'=>array(ROLE_AGENT /* settings */ ),
				'Add Product'=>array(false, 'products.php'),
				'List Products'=>array(false, 'root_products.php'),
				'Show Unassigned Maps'=>array(false, 'root_products.php'),
				'Show Google API Map'=>array(false, 'root_products.php'),
				'Auxiliary Tables'=>array(false, 'root_aux.php'),
				'Product number gaps'=>array(false, 'root_aux.php'),
			),
			'Reports'=>array(
				'(settings)'=>array(ROLE_AGENT/* settings */ ),
				'Categories and Subcategories'=>array(false,'report_generic.php'),
				'Activity by Date'=>array(false,'report_generic.php'),
				'Polygon Report'=>array(false,'report_generic.php'),
				'Sales Outlet Online Status'=>array(false,'report_generic.php'),
				'Grouping Report'=>array(false,'report_generic.php'),
			),
			'Utilities'=>array(
				'(settings)'=>array(ROLE_AGENT /* settings */ ),
				'Import and Export'=>array(false, 'unspecified.php'),
				'Document Library'=>array(false, 'unspecified.php'),
				'List Staff'=>array(false, 'unspecified.php'),
				'Add Staff'=>array(false, 'unspecified.php'),
				'County List'=>array(false, 'unspecified.php'),
				'UPS Check Digit'=>array(false, 'unspecified.php'),
			),
			'Help'=>array(
				'(settings)'=>array( /* settings */ ),
				'With This Page..'=>array(false, 'unspecified.php'),
				'Main Documentation'=>array(false, 'unspecified.php'),
				'Support and Contact'=>array(false, 'unspecified.php'),
				'Submit a Ticket'=>array(false, 'unspecified.php'),
				'About Home Base'=>array(ROLE_AGENT, 'unspecified.php'),
			),
		);
		$_dshow_[1]=1; //beginSTM
		$_dshow_[2]=1; //beginSTMB
		$_dk_=2;
		foreach($DHTMLmenu as $n=>$col){
			if($n=='(settings)')continue;
			
			//determine showPrimary
			if(!$roles && !in_array($n,array('Home','Help'))){
				$showPrimary=0;
			}else if($str=$col['(settings)'][0]){
				//---------------------------------------------
				if(is_numeric($str)){
					$showPrimary=(@min($roles) > $str ? 0 : 1); 
				}else if(is_array($str)){
					foreach($str as $g=>$h){
						if($roles[$h]){
							$showPrimary=1;
							break;
						}
						$showPrimary=0;
					}
				}else{
					$showPrimary=$str($n,$_dk_);
				}
				//---------------------------------------------
			}else{
				$showPrimary=1;
			}
			//appendSTMI
			$_dk_++;
			$_dshow_[$_dk_]=$showPrimary;
			if(count($col)>1){//NOTE!! we assume now that the first node HAS to be settings
				//beginSTMB
				$_dk_++;
				$_dshow_[$_dk_]=$showPrimary;
				foreach($col as $o=>$w){
					if($o=='(settings)')continue;
					if($w[0]){
						//--------------------------------
						if(is_numeric($w[0])){
							$showSecondary=(@min($roles) > $w[0] ? 0 : 1);
						}else if(is_array($w[0])){
							foreach($w[0] as $g=>$h){
								if($roles[$h]){
									$showSecondary=1;
									break;
								}
								$showSecondary=0;
							}
						}else{
							$function=$w[0];
							$showSecondary=$function($o,$_dk_);
						}
						//--------------------------------
					}else{
						$showSecondary=1;
					}
					//appendSTMI - ADD AN ELEMENT
					$_dk_++;
					$_dshow_[$_dk_]=($showSecondary && $showPrimary? 1 : 0);
				}
				//endSTMB
				$_dk_++;
				$_dshow_[$_dk_]=$showPrimary;
			}	
		}
		$_dk_++;
		$_dshow_[$_dk_]=true;
		$_dk_++;
		$_dshow_[$_dk_]=true;
	}else if($GCUserName=='art'){
		$DHTMLmenu=array(
			'(settings)'=>array( /* settings */ ),
			'Home'=>array(
				'(settings)'=>array( /* settings */ ),
				'My Home Page'=>array(false, 'home.php'),
				'Calendar'=>array(ROLE_AGENT, 'calendar.php'),
				'Instant Messaging'=>array(ROLE_AGENT, 'im.php'),
				'Notifications'=>array(false, 'notifications.php'),
				'Preferences'=>array(false, 'preferences.php'),
			),
			'Products'=>array(
				'(settings)'=>array(ROLE_AGENT /* settings */ ),
				'Add Product'=>array(false, 'products.php'),
				'List Products'=>array(false, 'root_products.php'),
				'Show Unassigned Maps'=>array(false, 'root_products.php'),
				'Show Google API Map'=>array(false, 'root_products.php'),
				'Auxiliary Tables'=>array(false, 'root_aux.php'),
			),
			'Reports'=>array(
				'(settings)'=>array(ROLE_AGENT/* settings */ ),
				'Categories and Subcategories'=>array(false,'report_generic.php'),
				'Activity by Date'=>array(false,'report_generic.php'),
				'Polygon Report'=>array(false,'report_generic.php'),
				'Sales Outlet Online Status'=>array(false,'report_generic.php'),
				'Grouping Report'=>array(false,'report_generic.php'),
			),
			'Utilities'=>array(
				'(settings)'=>array(ROLE_AGENT /* settings */ ),
				'Import and Export'=>array(false, 'unspecified.php'),
				'Document Library'=>array(false, 'unspecified.php'),
				'List Staff'=>array(false, 'unspecified.php'),
				'Add Staff'=>array(false, 'unspecified.php'),
				'County List'=>array(false, 'unspecified.php'),
				'UPS Check Digit'=>array(false, 'unspecified.php'),
			),
			'Help'=>array(
				'(settings)'=>array( /* settings */ ),
				'With This Page..'=>array(false, 'unspecified.php'),
				'Main Documentation'=>array(false, 'unspecified.php'),
				'Support and Contact'=>array(false, 'unspecified.php'),
				'Submit a Ticket'=>array(false, 'unspecified.php'),
				'About Home Base'=>array(ROLE_AGENT, 'unspecified.php'),
			),
		);
		$_dshow_[1]=1; //beginSTM
		$_dshow_[2]=1; //beginSTMB
		$_dk_=2;
		foreach($DHTMLmenu as $n=>$col){
			if($n=='(settings)')continue;
			
			//determine showPrimary
			if(!$roles && !in_array($n,array('Home','Help'))){
				$showPrimary=0;
			}else if($str=$col['(settings)'][0]){
				//---------------------------------------------
				if(is_numeric($str)){
					$showPrimary=(@min($roles) > $str ? 0 : 1); 
				}else if(is_array($str)){
					foreach($str as $g=>$h){
						if($roles[$h]){
							$showPrimary=1;
							break;
						}
						$showPrimary=0;
					}
				}else{
					$showPrimary=$str($n,$_dk_);
				}
				//---------------------------------------------
			}else{
				$showPrimary=1;
			}
			//appendSTMI
			$_dk_++;
			$_dshow_[$_dk_]=$showPrimary;
			if(count($col)>1){//NOTE!! we assume now that the first node HAS to be settings
				//beginSTMB
				$_dk_++;
				$_dshow_[$_dk_]=$showPrimary;
				foreach($col as $o=>$w){
					if($o=='(settings)')continue;
					if($w[0]){
						//--------------------------------
						if(is_numeric($w[0])){
							$showSecondary=(@min($roles) > $w[0] ? 0 : 1);
						}else if(is_array($w[0])){
							foreach($w[0] as $g=>$h){
								if($roles[$h]){
									$showSecondary=1;
									break;
								}
								$showSecondary=0;
							}
						}else{
							$function=$w[0];
							$showSecondary=$function($o,$_dk_);
						}
						//--------------------------------
					}else{
						$showSecondary=1;
					}
					//appendSTMI - ADD AN ELEMENT
					$_dk_++;
					$_dshow_[$_dk_]=($showSecondary && $showPrimary? 1 : 0);
				}
				//endSTMB
				$_dk_++;
				$_dshow_[$_dk_]=$showPrimary;
			}	
		}
		$_dk_++;
		$_dshow_[$_dk_]=true;
		$_dk_++;
		$_dshow_[$_dk_]=true;
	}else{
		mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='DHTML Menu for this account has not been set up'),$fromHdrBugs);
		exit($err.', developer has been notified');
	}
	return $_dshow_;
}
function global_extractor($__file__,$options=array()){
	//from juliet
	/*2012-04-21 SF - allows a file be be run inside this function as if it was public
	options
		return = string; use so calling function can also globalize
	*/
	extract($options);
	$a=array(
		'GLOBALS','_ENV','HTTP_ENV_VARS','AUTH_TYPE','HTTP_COOKIE','PHP_AUTH_PW','PHP_AUTH_USER','argv','_POST','HTTP_POST_VARS','_GET','HTTP_GET_VARS','_COOKIE','HTTP_COOKIE_VARS','_SERVER','HTTP_SERVER_VARS','_FILES','HTTP_POST_FILES','_REQUEST','SUPER_MASTER_USERNAME','SUPER_MASTER_PASSWORD','SUPER_MASTER_HOSTNAME','SUPER_MASTER_DATABASE','MASTER_DATABASE','MASTER_USERNAME','MASTER_HOSTNAME','MASTER_PASSWORD','a','n','v','HTTP_SESSION_VARS','_SESSION',
	);
	$str='global $';
	foreach($GLOBALS as $n=>$v){
		if(in_array($n,$a))continue;
		$str.=$n.',$';
	}
	$str=rtrim($str,',$').';';
	if($return=='string'){
		return $str;
	}else{
		eval($str);
		require($__file__);
	}
}
function history($SubObjectName){
	/* 2012-04-24 
	may need to return if in insertMode..
	*/
	if(minroles()>ROLE_AGENT)return;
	global $propertiesFields, $unitsFields, $Units_ID, $Properties_ID, $ID;
	$ObjectName=(in_array($SubObjectName,$propertiesFields) ? 'gl_properties' : 'gl_properties_units');
	if($ObjectName=='gl_properties'){
		$id=($Properties_ID ? $Properties_ID : $ID);
	}else{
		$id=($Units_ID ? $Units_ID : $ID);
	}
	if(q("SELECT COUNT(*) FROM gf_modifications WHERE Objects_ID='$id' AND SubObjectName='$SubObjectName'", O_VALUE)){
		?>[<a href="modifications.php?Objects_ID=<?php echo $id;?>&ObjectName=<?php echo $ObjectName;?>&SubObjectName=<?php echo $SubObjectName;?>" title="View changes to this field" onclick="return ow(this.href,'l2_mods','550,400');">H</a>]<?php
	}
}
function dynamic_title($static=true){
	global $CustomTitle;
	$url=$_SERVER['SCRIPT_NAME'].($_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:'');
	$return=q("SELECT VarKey FROM bais_settings WHERE UserName='".sun()."' AND VarGroup='Custom Report' AND VarValue='$url'",O_VALUE);
	if($return>''){
		$CustomTitle=$return;
		return($return);
		q("UPDATE bais_settings SET EditDate=NOW() WHERE UserName='".sun()."' AND VarGroup='Custom Report' AND VarValue='$url'");
	}else{
		return($static);
	}
}
function UPC_checkdigit($str,$options=array()){
	/*
	2012-06-14
	options
		return -> digit (default is full string)
	*/
	extract($options);
	if(!preg_match('/^[0-9]{11,12}$/',$str))return;
	for($i=0; $i<=10; $i++){
		//multiply first, third, fifth etc. digits by 3, others by 1, and sum this
		$digit+= $str[$i] * (!fmod($i,2) ? 3 : 1);
	}
	//500-sum, take last number as check digit
	$digit=substr(500-$digit,-1);
	if(strlen($str)==12){
		//rarely expect to use this
		return $digit==substr($str,-1);
	}else if($return=='digit'){
		return $digit;
	}else{
		return $str.$digit;
	}
}
function sql_table_relationships($options=array()){
	/* created 2012-10-15: this was in form_field_presenter() but is atomic enough to pull out */
	global $sql_table_relationships, $refreshRelations;
	if($sql_table_relationships['relations'])return $sql_table_relationships['relations'];
	extract($options);
	if(!$megaTableSize)$megaTableSize=10000;
	//see when tables were touched
	if(!$db){
		global $GCUserName;
		$db=$GCUserName;
	}
	foreach($a=q("SHOW TABLES", O_ARRAY) as $v){
		$t=current($v);
		if(substr($t,0,1)=='_')continue; //speed it up 
		$h=md5(preg_replace('/AUTO_INCREMENT=[0-9]+/','',end(q("SHOW CREATE TABLE ".current($v), O_ROW))));
		$hash.=$h;
	}
	$hash=md5($hash);
	if($_SESSION['relations_hash']!=$hash || $refreshRelations){
		$_SESSION['relations_hash']=$hash;
		?><div id="getting">getting database table information..</div><?php
		flush();
		foreach(q("SHOW TABLES", O_ARRAY) as $v){
			$n=current($v);
			$compCognate=strtolower(current(explode('_',$n)));
			if(substr($n,0,1)=='_')continue;
			if(strstr($n,'_bk'))continue;
			if(q("SELECT COUNT(*) FROM $n", O_VALUE)>$megaTableSize)continue;
			$_fields=q("EXPLAIN $n", O_ARRAY);
			$primary='';
			$cog=strtolower(end(explode('_',$n)));
			$label=false;
			foreach($_fields as $w){
				if($w['Key']=='PRI'){
					$primary=strtolower($w['Field']);
					if($relations[$cog.'_'.$primary]){
						if($compCognate!=$cognate){
							//this one is not any better
							break;
						}else{
							#prn('replacing relation table with '.$n);
						}
					}
					$relations[$cog.'_'.$primary]=array(
						'table'=>$n,
					);
				}
				if($primary && !$label && preg_match('/char|varchar/',$w['Type']) && !preg_match('/creator|editor/i',$w['Field']) && (q("SELECT COUNT(DISTINCT ".$w['Field'].")/COUNT(*) FROM $n WHERE ".$w['Field']."!='' AND ".$w['Field']." IS NOT NULL", O_VALUE)==1 || !q("SELECT COUNT(*) FROM $n WHERE ".$w['Field']."!='' AND ".$w['Field']." IS NOT NULL",O_VALUE))){
					$label=true;
					$relations[$cog.'_'.$primary]['label']=$w['Field'];
				}
			}
			/* 2012-11-01 this is not being used at this time */
			if(false)
			$sql_table_relationships['tables'][strtolower($n)]=$_fields;
		}
		$_SESSION['relations']=/* not used! $sql_table_relationships['relations']=*/$relations;
		?><script language="javascript" type="text/javascript">g('getting').style.display='none';</script><?php
		return $relations;
	}else{
		return $_SESSION['relations'];
	}
}

