<?php 
//functions that are developed for various views
function lib_nextHeaderNumber(){
	$a=q("SELECT HeaderNumber FROM finan_headers", O_COL);
	if(!$a)return 1;
	foreach($a as $n=>$v)$a[$n]=preg_replace('/[^-0-9]*/','',$v);
	foreach($a as $n=>$v)$a[$n]=(int) end(explode('-',$v));
	return max($a)+1;
}
function systementry_parse_vars($n){
	preg_match_all('/\{\{([_0-9a-zA-Z]+)\}\}/',$n,$m);
	for($i=0;$i<count($m[1]);$i++){
		if($m[1][$i]=='MASTER_PASSWORD' || $m[1][$i]=='SUPER_MASTER_PASSWORD')continue;
		$n=str_replace('{{'.$m[1][$i].'}}',$GLOBALS[$m[1][$i]],$n);
	}
	return $n;
}
function lib_calculate($f,$a){
	//2012-12-25
	if($f=='total'){
		return array_sum($a);
	}else if($f=='average'){
		return array_sum($a)/count($a);
	}else if($f=='count'){
		return count($a);
	}else if($f=='median'){
		sort($a);
		return $a[floor(count($a)/2)];
	}
}
require(str_replace('.php','.assets.php',__FILE__));
/*
*/
@$f=($GLOBALS['REDIRECT_URL'] ? $GLOBALS['REDIRECT_URL'] : $SCRIPT_FILENAME);
if(end(explode('/',$f))==end(explode('/',__FILE__))){
	//identify this script/GUI
	$localSys['scriptGroup']='';
	$localSys['scriptID']='gen_access1';
	$localSys['scriptVersion']='1.0';
	$localSys['pageType']='Properties Window';
	
	require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');
}


//------------------------ codeblock 1015037 ----------------------------------------
/*
2012-12-30: also see codeblock 1230052 which is improved from this
*/
//a few security items [2012-12-19 this may not be necessary since I have consolidated coding]
unset($tableSettings,$profileSettings,$raw);
//2012-12-12 do this for now, as this is passed and conflicting; any old systems like callback() use this?
unset($recordPKField);

//2012-12-16 a little translation here
if($_Profiles_ID_){
	//get $object and $identifier
	if(!($systemProfile=q("SELECT * FROM system_profiles WHERE ID=$_Profiles_ID_", O_ROW))){
		exit('Unable to locate Profile ID '.$_Profiles_ID_);
	}
	if(!($systemTable=q("SELECT * FROM system_tables WHERE ID='".$systemProfile['Tables_ID']."'", O_ROW))){
		exit('Unable to locate Table ID '.$systemProfile['Tables_ID']);
	}
	$object=$systemTable['SystemName'];
	$identifier=$systemProfile['Identifier'];
}else if($object){
	if(!$identifier)$identifier='default';
	if($systemTable=q("SELECT * FROM system_tables WHERE SystemName='$object'", O_ROW)){
		//OK
	}else{
		//do we have any call to register table settings for this identifier->object?
		#add query and settings here
		$Settings='';
		
		//by calling this script we are registering the table
		$n=q("INSERT INTO system_tables SET
		SystemName='$object',
		Name='$object',
		Settings='$Settings',
		Description='Table registered by systementry',
		Type='table'",O_INSERTID);
		
		//after edgar
		$updatePKFieldValue[$object]=true;
		$systemTable=array(
			'ID'=>$n,
			'SystemName'=>$object,
			'Name'=>$object,
			'Settings'=>$Settings,
		);
	}
	if($systemProfile=q("SELECT * FROM system_profiles WHERE Tables_ID='".$systemTable['ID']."' AND Identifier='$identifier'", O_ROW)){
		$_Profiles_ID_=$systemProfile['ID'];
	}else{
		//do we have any call to register profile settings for this identifier->object?
		#add query and settings here
		$Settings='';

		//for now, we are not creating settings for this view - although theoretically at some point the rec. pymts window itself could be completely defined by settings, and this page (rfm_payments.php) would just be systementry.php called with the _Profiles_ID_
		$_Profiles_ID_=q("INSERT INTO system_profiles SET 
		Tables_ID='".$systemTable['ID']."',
		Identifier='$identifier',
		Type='Data View',
		Settings='$Settings',
		CreateDate=NOW(),
		Creator='".sun()."'", O_INSERTID);
		$systemProfile=array(
			'ID'=>$n,
			'Identifier'=>$identifier,
			'Settings'=>$Settings,
		);
	}
}else{
	exit('Improper call of systementry.php');
}
//handle settings
if(strlen($systemTable['Settings'])>7)$tableSettings=unserialize(base64_decode($systemTable['Settings']));
if(strlen($systemProfile['Settings'])>7)$profileSettings=unserialize(base64_decode($systemProfile['Settings']));

//---------- begin edgar -------------
$objectFields=q("EXPLAIN $object", O_ARRAY);
foreach($objectFields as $n=>$v){
	if($v['Key']=='PRI')$recordPKField[]=$v['Field'];
	//first non-numeric field = default sorter
	if(!$sorter && preg_match('/(char|varchar)/',$v['Type']) && !preg_match('/createdate|creator|editdate|editor/i',$v['Field']))$sorter=$v['Field'];
	if(preg_match('/resourcetype/i',$v['Field']) && $v['Null']=='YES')$quasiResourceTypeField=$v['Field'];
	if(preg_match('/resourcetoken/i',$v['Field']))$quasiResourceTokenField=$v['Field'];
	if(preg_match('/sessionkey/i',$v['Field']))$sessionKeyField=$v['Field'];

	if(preg_match('/creator/i',$v['Field']))$creatorField=$v['Field'];
	if(preg_match('/createdate/i',$v['Field']))$createDateField=$v['Field'];
}
//------------ end edgar -------------
if($updatePKFieldValue[$object])q("UPDATE system_tables SET KeyField='".implode(',',$recordPKField)."'");

if(count($recordPKField)<>1){
	//2012-12-31 we only work with a single primary key right now
	mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='table has a compound or missing primary key'),$fromHdrBugs);
	exit($err);
}
//------------------------ end codeblock 1015037 ----------------------------------------



if($mode=='updateObject' || $mode=='insertObject' || $mode=='deleteObject'){
	//---------------- begin charles ----------------
	if(md5(stripslashes($profile['raw']))!==$profile['raw_hash']){
		//if(!$profile['EditDate'])error_alert('variable profile.EditDate is not passed and is required');
		//if(!$profile['ID'])error_alert('variable profile.ID is now required');
		//if(strtotime(q("SELECT EditDate FROM system_profiles WHERE ID='".$profile['ID']."'", O_VALUE))>strtotime($profile['EditDate']))error_alert('Profile values have been updated after the current page was sent from the server.  Complete any update or insert operations if possible, and then refresh this page');
		$profile['raw']=trim(stripslashes($profile['raw']));
		if(substr($profile['raw'],0,5)!=='array'){
			$profile['raw_adjusted']='array('."\n".$profile['raw']."\n".')';
		}else{
			$profile['raw_adjusted']=$profile['raw'];
		}
		ob_start();
		eval('$str='.$profile['raw_adjusted'].';');
		$err=ob_get_contents();
		ob_end_clean();
		echo $err;
		if($err){
			mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='Settings not updated; the array is not valid PHP expression!'),$fromHdrBugs);
			error_alert('your settings raw coding contains a parse error, see the code',1);
		}else{
			$str['_raw_']=$profile['raw'];
			$str=base64_encode(serialize($str));
			if($n=$profile['ID']){
				$e=date('Y-m-d H:i:s');
				q("UPDATE system_profiles SET
				Settings='$str',
				EditDate='$e'
				WHERE ID='$n'");
				if(!$suppressPrintEnv){
					?><script language="javascript" type="text/javascript">
					try{ window.parent.g('profile[EditDate]').value='<?php echo $e;?>'; }
					catch(e){ }
					</script><?php
				}
			}else{
				$profile['ID']=q("INSERT INTO system_profiles SET 
				Tables_ID='".$systemTable['ID']."',
				Identifier='$identifier',
				Type='Data View',
				Name='Table Profile',
				Settings='$str',
				CreateDate=NOW(),
				Creator='".sun()."'", O_INSERTID);
			}
			prn($qr);
		}
		if($submode=='updateProfileSettingsOnly')eOK();
	}
	//---------------- end charles ----------------
	if($f=$profileSettings['submodes'][$submode]){
		if(file_exists($COMPONENT_ROOT.'/'.$f)){
			require($COMPONENT_ROOT.'/'.$f);
		}else{
			mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='unused mode called'),$fromHdrBugs);
			error_alert('unable to locate submode component '.$f);
		}
	}	
	if(($s=$profileSettings['sub_table']) && $s['active']){
		if(!$s['post_processing_component'])error_alert('You have specified a sub table for this form, however there is no post processing component specified in the profile settings under sub_table.  Click the settings tab at the bottom and update this');
		if(!file_exists($COMPONENT_ROOT.'/'.$s['post_processing_component']))error_alert('The post processing component you specified ('.$s['post_processing_component'].') does not exist');
		require($COMPONENT_ROOT.'/'.$s['post_processing_component']);
	}
		
	/*
	identify date fields and number fields which will lose information (IL)
	
	*/
	//system error checking
	if(!$recordPKField[0]){
		mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='variable recordPKField is not present'),$fromHdrBugs);
		error_alert($err.', developer has been notified');
	}
	if($mode=='deleteObject'){
		error_alert('delete mode not developed');
	}
	if($mode==$insertMode && !$$recordPKField[0]){
		unset($GLOBALS[$recordPKField[0]]);
	}
	foreach($GLOBALS as $n=>$v){
		if(!is_string($v))continue;
		if(strtolower($v)=='null'){
			$GLOBALS[$n]='PHP:NULL';
		}else if(strtolower($v)=='\null'){
			$GLOBALS[$n]=str_replace('\\','',$v);
		}
	}
	//note if quasi resource is present we always update
	if($quasiResourceTypeField)$GLOBALS[$quasiResourceTypeField]='1';
	$sql=sql_insert_update_generic($MASTER_DATABASE,$object,($quasiResourceTypeField ? $updateMode : $mode));
	prn($sql);
	ob_start();
	$n=q($sql,$mode==$insertMode?O_INSERTID:O_AFFECTEDROWS, ERR_ECHO);
	$err=ob_get_contents();
	ob_end_clean();
	if($err){
		prn($err);
		mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err),$fromHdrBugs);
		error_alert('Error in query, developer has been notified');
	}
	if($mode==$insertMode){
		foreach($objectFields as $key=>$v){
			if(strtolower($v['Field'])==strtolower($recordPKField[0])){
				$autoinc=($v['Extra']=='auto_increment'?1:0);
				break;
			}
		}
		if($autoinc){
			$GLOBALS[$recordPKField[0]]=$n;
		}else{
			//it is the value that was passed on the form
		}
	}else{
		$affected_rows=$n;
	}
	if($f=$profileSettings['post_processing_component_end']){
		if(file_exists($COMPONENT_ROOT.'/'.$f)){
			require($COMPONENT_ROOT.'/'.$f);
		}else{
			mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='specified end processing component '.$f.' not present'),$fromHdrBugs);
			error_alert($err,1);
		}
	}
	if($mode==$insertMode && $navMode=='insert' && $quasiResourceTypeField){
		//2012-12-23: added (surprised I missed this) insert record and then update id and resourcetoken
		$newResourceToken=substr(date('YmdHis').rand('10000','99999'),3,16);
		$newID=quasi_resource_generic(
			$MASTER_DATABASE, 
			$object, 
			$newResourceToken, 
			$quasiResourceTypeField, 
			$sessionKeyField,
			$quasiResourceTokenField, 
			$recordPKField[0], 
			$creatorField, 
			$createDateField
		);
		?><script language="javascript" type="text/javascript">
		window.parent.sets['<?php echo $recordPKField[0];?>']='<?php echo $newID;?>';
		try{
		window.parent.g('<?php echo $quasiResourceTokenField;?>').value='<?php echo $newResourceToken;?>';
		}catch(e){}
		try{
		window.parent.<?php echo $quasiResourceTokenField;?>='<?php echo $newResourceToken;?>';
		}catch(e){}
		</script><?php
	}
	if($cbPresent){
		prn($recordPKField);
		$cbValue=$GLOBALS[$recordPKField[0]];
		if($n=$tableSettings['label']){
			$cbLabel=q("SELECT IF(TRIM('$n')!='',TRIM('$n'),".$recordPKField[0].") FROM $object WHERE ".$recordPKField[0]."='".$GLOBALS[$recordPKField[0]]."'", O_VALUE);
		}else{
			foreach(($relations=sql_table_relationships()) as $n=>$v){
				if(strtolower($v['table'])==strtolower($object)){
					$cbLabel=q("SELECT ".$v['label']." FROM $object WHERE ".$recordPKField[0]."='".$GLOBALS[$recordPKField[0]]."'", O_VALUE);
					break;
				}
			}
		}
		callback(array("useTryCatch"=>false));
	}
	if(preg_match('/insert|navig|kill/',$navMode)){
		$navigateCount=$count+($mode==$insertMode?1:0);
		$navigate=true;
	}
	goto bypass;
}

//------------------------ Navbuttons head coding v1.50 -----------------------------
if(!$sorter)$sorter=$recordPKField[0];
$navObject=$object.'_'.$recordPKField[0];
$updateMode='updateObject';
$insertMode='insertObject';
$deleteMode='deleteObject';
$insertType=1; //1=Save&New and Save&Close; 2 = Save and Save&New
#set these to 'disabled' if desired
$saveInitiallyDisabled='';
$saveAndNewInitiallyDisabled='';
$saveAndCloseInitiallyDisabled='';
//v1.4 change - some information about the coding
$navVer='1.50';
//v1.3 change - declare this function if you need to add parameters to the query string
$navQueryFunction='systementry_nav()';
//v1.3 change - deny transiting from Next to New mode (shutting off ability to insert)
$denyNextToNew=false;
//declare the query to get the idSet or subset, ordered by desired sort order - note that if you're using quasi resources, then be sure and filter them out.
$ids=q("SELECT ".$recordPKField[0]." FROM $object WHERE 1 ".($quasiResourceTypeField?"AND $quasiResourceTypeField IS NOT NULL":'')." ORDER BY $sorter",O_COL);

$nullCount=count($ids);
$j=0;
if($nullCount){
	foreach($ids as $v){
		$j++; //starting value=1
		if($j==$abs+$nav || (isset($$navObject) && $$navObject==$v)){
			$nullAbs=$j;
			//get actual primary key if passage by abs+nav
			if(!$$navObject) $$navObject=$v;
			break;
		}
	}
}else{
	$nullAbs=1;
}
if(strlen($$navObject)){
	//get the record for the object
	if($a=q("SELECT * FROM $object WHERE ".$recordPKField[0]."='".$$navObject."'",O_ROW)){
		$mode=$updateMode;
		@extract($a);
	}else{
		//object may have been deleted by another user, least perplexing approach is to present insert mode
		$mode=$insertMode;
		unset($$navObject);
		$nullAbs=$nullCount+1;
	}
}else{
	$mode=$insertMode;
	if($quasiResourceTypeField){
		//handle this
		$$navObject=quasi_resource_generic(
			$MASTER_DATABASE, 
			$object, 
			$ResourceToken, 
			$quasiResourceTypeField, 
			$sessionKeyField,
			$quasiResourceTokenField, 
			$recordPKField[0], 
			$creatorField, 
			$createDateField
		);
		//added 2012-12-12: for the hidden field
		$GLOBALS[$recordPKField[0]]=$$navObject;
	}
	$nullAbs=$nullCount+1; //where we actually are right then
}
//--------------------------- end coding --------------------------------
gmicrotime('afterhead');

$PageTitle='System Data Entry';
$suppressForm=false;
$tabVersion=3;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo dynamic_title($PageTitle.' - '.$AcctCompanyName);?></title>



<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
textarea.tabby:focus{
	border-style:dotted;
	}
.comment{
	cursor:pointer;
	border-bottom:1px dashed #666;
	}
#workSpace{
	clear:both;
	border:1px solid #ccc;
	padding:5px 10px;
	margin:7px 0px;
	}
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="/Library/js/jquery.tabby.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/jquery.bbq.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';
var isEscapable=1;
AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already
function systementry_nav(){
	var a,s='';
	(a=$.deparam.querystring());
	if(a.object)s+='&object='+a.object;
	if(a.identifier)s+='&identifier='+a.identifier;
	if(a._Profiles_ID_)s+='&_Profiles_ID_='+a._Profiles_ID_;
	try{
	if(ab==count && <?php echo $quasiResourceTokenField?'true':'false';?>)s+='&ResourceToken='+generate_date()+generate_rand(5);
	}catch(e){ }
	return s;
}

var cRow;
$(document).ready(function(e){
	$('#updateProfileSettings').click(function(e){
		var buffer=g('submode').value;
		g('submode').value='updateProfileSettingsOnly';
		g('form1').submit();
		g('submode').value=buffer;
	});


	var select_profile_confirm='You have started editing this record and will lose your changes.  Continue?';
	//------------- stanley -------------
	var select_profile_initial=g('select_profile').selectedIndex;
	$('#select_profile').change(function(){
		if(detectChange && !confirm(select_profile_confirm)){
			g('select_profile').selectedIndex=select_profile_initial;
			return false;
		}
		var s=(window.location+'').split('?');
		s[1]=s[1].replace(/_Profiles_ID_=[0-9]*&*/,'');
		s[1]='_Profiles_ID_='+this.value+(s[1].length?'&'+s[1]:'');
		window.location=s[0]+'?'+s[1];
	});
	//------------- end stanley -------------
	/* 
	added for the subtable functions
	major todos:
		re-focus on add/delete
		cannot delete the last row; if below a certain amount, more rows added on end to keep count, say 10 rows
		there is not any handling for select and checkboxes and maybe file elements
		need to be able to set autofill class for a string and also have the field be null and have the "real ID" go into say the Items_ID hidden field instead; and for that matter we will also need tag clouds here as well
	*/
	$('.subTable input[type=text]').focus(function(){
		cRow=$(this).closest('tr')[0];
	});
	$('.subTable input[type=text]').blur(function(){
		cRow=null;
	});
	$(this).keydown(function(e){
		if(!cRow)return;
		var cRowOther;
		if(e.keyCode==45 && e.ctrlKey){
			var clone=$(cRow).clone(true);
			clone.find('input[type=text],input[type=hidden]').val('');
			$(cRow).after(clone);
			detectChange=1;
		}else if(e.keyCode==46 && e.ctrlKey){
			$(cRow).remove();
			detectChange=1;
		}else if((e.keyCode==40 || e.keyCode==38)){
			//move cursor
			//we need to allow textareas, and if at the bottom of the range, THEN move down to next field, same for up
			if(e.ctrlKey)return; //allow for native value selection list!
			e.keyCode==38?cRowOther=$(cRow).prev('tr')[0]:cRowOther=$(cRow).next('tr')[0];
			if(!cRowOther)return;
			var there=$(cRowOther).find('input[name="'+e.target.name+'"]');
			there.focus();
			there.select();
		}
	});
});
<?php //this eventually needs to come from the profile ?>
//------------------- CUSTOM CODING --------------------

$(document).ready(function(){
	$('#printTransaction').click(function(){
		buffer=g('submode').value;
		g('submode').value='printTransaction';
		g('form1').submit();
		g('submode').value=buffer;
	});
});

//------------------ END CUSTOM CODING -----------------
</script>



</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onSubmit="return beginSubmit();">
<?php }?>
<div id="header">

<div id="btns150" class="fr"><?php
ob_start();
?>
<input id="Previous" type="button" name="Submit" value="Previous" class="navButton_A" onClick="focus_nav(-1, '<?php echo ($mode==$insertMode?'insert':'update')?>', <?php echo $mode==$insertMode?1:0?>, 0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs<=1?'disabled':''?> >
<?php
//Handle display of all buttons besides the Previous button
if($mode==$insertMode){
	if($insertType==2 /** advanced mode **/){
		//save
		?><input id="Save" type="button" name="Submit" value="Save" class="navButton_A" onClick="focus_nav(0,'insert',1,2<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveInitiallyDisabled?>><?php
	}
	//save and new - common to both modes
	?><input id="SaveAndNew" type="button" name="Submit" value="Save &amp; New" class="navButton_A" onClick="focus_nav(0,'insert', 1,1<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndNewInitiallyDisabled?>><?php
	if($insertType==1 /** basic mode **/){
		//save and close
		?><input id="SaveAndClose" type="button" name="Submit" value="Save &amp; Close" class="navButton_A" onClick="focus_nav(0,'insert', 1,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndCloseInitiallyDisabled?>><?php
	}
	?><input id="CancelInsert" type="button" name="Submit" value="Cancel" class="navButton_A" onClick="focus_nav_cxl('insert');"><?php
}else{
	//OK, and appropriate [next] button
	?><input id="OK" type="button" name="Submit" value="OK" class="navButton_A" onClick="focus_nav(0,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);">
	<input id="Next" type="button" name="Submit" value="Next" class="navButton_A" onClick="focus_nav(1,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs>$nullCount || ($denyNextToNew && $nullAbs==$nullCount) ?'disabled':''?>><?php
}
$navbuttons=ob_get_contents();
ob_end_clean();
//2009-09-10 - change button names, set default as =submit, hide unused buttons
if(!$addRecordText)$addRecordText='Add Record';
if(!isset($navbuttonDefaultLogic))$navbuttonDefaultLogic=true;
if($navbuttonDefaultLogic){
	$navbuttonSetDefault=($mode==$insertMode?'SaveAndNew':'OK');
	if($cbSelect){
		$navbuttonOverrideLabel['SaveAndClose']=$addRecordText;
		$navbuttonHide=array(
			'Previous'=>true,
			'Save'=>true,
			'SaveAndNew'=>true,
			'Next'=>true,
			'OK'=>true
		);
	}
}
$navbuttonLabels=array(
	'Previous'		=>'Previous',
    'Save'			=>'Save',
    'SaveAndNew'	=>'Save &amp; New',
    'SaveAndClose'	=>'Save &amp; Close',
    'CancelInsert'	=>'Cancel',
    'OK'			=>'OK',
    'Next'			=>'Next'
);
foreach($navbuttonLabels as $n=>$v){
	if($navbuttonOverrideLabel[$n])
	$navbuttons=str_replace(
		'id="'.$n.'" type="button" name="Submit" value="'.$v.'"', 
		'id="'.$n.'" type="button" name="Submit" value="'.h($navbuttonOverrideLabel[$n]).'"', 
		$navbuttons
	);
	if($navbuttonHide[$n])
	$navbuttons=str_replace(
		'id="'.$n.'" type="button"',
		'id="'.$n.'" type="button" style="display:none;"',
		$navbuttons
	);
}
if($navbuttonSetDefault)$navbuttons=str_replace(
	'<w="'.$navbuttonSetDefault.'" type="button"', 
	'<input id="'.$navbuttonSetDefault.'" type="submit"', 
	$navbuttons
);
echo $navbuttons;

// *note that we could go back to the same page the 'New Record' click appeared on, but there's major issues programmatically on whether it would shift because of the placement of the new record.
// *note that the primary key field is now included here to save time
?>
<input name="navVer" type="hidden" id="navVer" value="<?php echo $navVer?>" />
<?php if($ResourceToken){ ?>
<input name="ResourceToken" type="hidden" id="ResourceToken" value="<?php echo $ResourceToken?>" />
<?php } if($navQueryFunction){ ?>
<input type="hidden" name="navQueryFunction" id="navQueryFunction" value="<?php echo h($navQueryFunction);?>" />
<?php } ?>
<input name="object" type="hidden" id="object" value="<?php echo $object?>" />
<input name="navObject" type="hidden" id="navObject" value="<?php echo $navObject?>" />
<input name="nav" type="hidden" id="nav" />
<input name="navMode" type="hidden" id="navMode" value="" />
<input name="count" type="hidden" id="count" value="<?php echo $nullCount?>" />
<input name="abs" type="hidden" id="abs" value="<?php echo $nullAbs?>" />
<input name="insertMode" type="hidden" id="insertMode" value="<?php echo $insertMode?>" />
<input name="updateMode" type="hidden" id="updateMode" value="<?php echo $updateMode?>" />
<input name="deleteMode" type="hidden" id="deleteMode" value="<?php echo $deleteMode?>" />
<input name="mode" type="hidden" id="mode" value="<?php echo $mode?>" />
<input name="submode" type="hidden" id="submode" value="" />
<input name="componentID" type="hidden" id="componentID" value="<?php echo $localSys['componentID']?>" />
<input name="_Profiles_ID_" type="hidden" id="_Profiles_ID_" value="<?php echo $_Profiles_ID_;?>" />
<input name="recordPKField" type="hidden" id="recordPKField" value="<?php echo $recordPKField[0]?>" />
<input name="identifier" type="hidden" id="identifier" value="<?php echo $identifier?>" />
<?php
if(count($_REQUEST)){
	foreach($_REQUEST as $n=>$v){
		if(substr($n,0,2)=='cb'){
			if(!$setCBPresent){
				$setCBPresent=true;
				?><!-- callback fields automatically generated --><?php
				echo "\n";
				?><input name="cbPresent" id="cbPresent" value="1" type="hidden" /><?php
				echo "\n";
			}
			if(is_array($v)){
				foreach($v as $o=>$w){
					echo "\t\t";
					?><input name="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" id="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" type="hidden" value="<?php echo stripslashes($w)?>" /><?php
					echo "\n";
				}
			}else{
				echo "\t\t";
				?><input name="<?php echo $n?>" id="<?php echo $n?>" type="hidden" value="<?php echo stripslashes($v)?>" /><?php
				echo "\n";
			}
		}
	}
}
?><!-- end navbuttons 1.43 --></div>

</div>
<div id="mainBody">

<?php
ob_start(); //--------- begin tabs ---------
?>
<p class="gray">For now, copy and paste into Dreamweaver to edit.</p>
<a href="//relatebase.com/admin/base64_encode.php" title="this helps encode and decode this" onClick="return ow(this.href,'l1_base64','750,800');">Click here for the base64 encoder</a>
<br />

<input type="hidden" name="profile[ID]" id="profileID" value="<?php echo $systemProfile['ID'];?>" />
<input type="hidden" name="profile[EditDate]" id="profileID" value="<?php echo $systemProfile['EditDate'];?>" />
<textarea cols="80" rows="25" name="profile[raw]" id="profileRaw" onChange="dChge(this);" class="tabby"><?php
ob_start();
if(strlen($profileSettings['_raw_'])){
	echo $profileSettings['_raw_'];
}
$out=ob_get_contents();
$outMD5=md5($out);
ob_end_clean();
echo h($out);
?></textarea>
<input type="hidden" name="profile[raw_hash]" id="profileRawHash" value="<?php echo $outMD5?>" />
<br />
<input type="button" name="Button" value="Update Profile Settings" id="updateProfileSettings" />
<?php
get_contents_tabsection('settings');

?><div id="workSpace">
	<div class="fr">
		Current profile:<br />
		<?php

		$filterTable=true;
		//----------------------- begin stanley ------------------
		?><select id="select_profile" class="minimal"><?php
		/*
		this selects all profiles for a given table ($object)
		*/
		if($a=q("SELECT p.ID, p.Identifier, t.SystemName, t.Name AS `Table`, p.Name, p.Description FROM system_tables t JOIN system_profiles p ON t.ID=p.Tables_ID WHERE p.Type='Data View' ".($filterTable?"AND t.ID='".$systemTable['ID']."'":''), O_ARRAY)){
			$i=0;
			foreach($a as $n=>$v){
				$i++;
				if(!$filterTable && $buffer!=$v['SystemName']){
					if($i>1)echo '</optgroup>';
					?><optgroup label="<?php echo h($v['Table']);?>"><?php
				}
				?><option value="<?php echo $v['ID']?>" <?php echo $_Profiles_ID_==$v['ID']?'selected':''?>><?php echo $v['Name']?h($v['Name'] . ' ('.$v['Identifier'].')'):$v['Identifier'];?></option><?php
			}
			if(!$filterTable){ ?></optgroup><?php }
		}
		?></select>
		//------------------------- end stanley ---------------------
		?>
	</div>
<?php
ob_start();
form_field_presenter($profileSettings);
$out=ob_get_contents();
ob_end_clean();
if($profileSettings['HTML_inserts']){
	foreach($profileSettings['HTML_inserts'] as $n=>$v){
		$out=str_replace('<!-- {RBSYSTEMENTRY:'.$n.'} -->',"\n".trim($v)."\n",$out);
	}
}
echo $out;
?>
</div><?php

get_contents_tabsection('form');

echo $helpString;

get_contents_tabsection('help');
tabs_enhanced(
	array(
		'form'=>array(
			'label'=>'Form'
		),
		'settings'=>array(
			'label'=>'Settings'
		),
		'help'=>array(
			'label'=>'Help',
		),
	), 
	array(
		'location'=>'bottom',
		'aColor'=>'royalblue',
		'brdColor'=>'#aaa',
	)
);
?>

</div>
<div id="footer">
&nbsp;&nbsp;
</div>
<?php if(!$suppressForm){ ?>
</form>
<?php }?>
<?php if(!$hideCtrlSection){ ?>
<div id="showTester" title="Javascript Tester" onClick="g('tester').style.display='block';">&nbsp;</div>
<div id="tester" >
	<a href="#" onClick="g('ctrlSection').style.display='block';return false;">Show Control Section</a><br />
	<textarea name="test" cols="65" rows="4" id="test">clear_form();</textarea><br />
	<input type="button" name="button" value="Test" onClick="jsEval(g('test').value);"><br />
	<textarea id="result" name="result" cols="65" rows="3" ></textarea>
</div>
<div id="ctrlSection" style="display:<?php echo $testModeC ? 'block':'none'?>">
	<iframe name="w1" src="/Library/js/blank.htm"></iframe>
	<iframe name="w2" src="/Library/js/blank.htm"></iframe>
	<iframe name="w3" src="/Library/js/blank.htm"></iframe>
	<iframe name="w4" src="/Library/js/blank.htm"></iframe>
</div>
<?php } ?>
</body>
</html><?php page_end();
//skip the page output
bypass:
?>