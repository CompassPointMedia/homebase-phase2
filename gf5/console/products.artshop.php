<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

//different products page by account
if(!$apSettings['productFile'])$apSettings['productFile']='products.php';
if(end(explode('/',$_SERVER['PHP_SELF']))!=$apSettings['productFile']){
	header('Location: '.str_replace(end(explode('/',$_SERVER['PHP_SELF'])),$apSettings['productFile'],$_SERVER['PHP_SELF']).($_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:''));
	exit;
}

if(!function_exists('get_image'))require($FUNCTION_ROOT.'/function_get_image_v220.php');
if(!isset($imgArray))$imgArray=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName, array('positiveFilters'=>'\.(jpg|gif|png|svg)$',));
function specific_fields($node,$options=array()){
	global $record, $finan_itemsFields, $developerEmail, $fromHdrBugs, $MASTER_USERNAME, $mode, $insertMode, $updateMode;
	extract($options);
	if($excludeFields){
		$excludeFields=explode('|',strtolower(implode('|',$excludeFields)));
	}
	//lcase record
	$r=$record;
	foreach($r as $n=>$v){
		unset($r[$n]);
		$r[strtolower($n)]=$v;
	}
	unset($r[strtolower($node.'_tobeexported')]);
	
	//this node
	$fields=$finan_itemsFields[$node];
	foreach($fields as $n=>$v){
		if(@in_array(strtolower($n),$excludeFields))continue;

		//in array but no longer in db
		if(!isset($r[strtolower($node.'_'.$n)])){
			mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='check node='.$node.', n='.$n),$fromHdrBugs);
			continue;
		}
		unset($r[strtolower($node.'_'.$n)]);
		
		
		//not really needed any more
		if(stristr($n,'ToBeExported'))continue;

		?><div>
		<div style="float:left;padding-right:7px;width:<?php echo $width?$width:'240px';?>"><?php echo preg_replace('/([a-z0-9])([A-Z])/','$1 $2',str_replace($node.'_','',$n));?>:</div>
		<span><?php
		if($v['fieldtype']=='textarea'){
			?><textarea name="<?php echo $node.'_'.$n;?>" id="<?php echo $node.'_'.$n;?>" rows="<?php echo $v['rows']?$v['rows']:4?>" cols="<?php echo $v['cols']?$v['cols']:4?>" onChange="dChge(this);"><?php echo h(strlen($record[$node.'_'.$n]) ? $record[$node.'_'.$n] : ($mode==$insertMode ? $v['default'] : ''));?></textarea><?php
		}else if($v['fieldtype']=='checkbox'){
			?><input type="checkbox" name="<?php echo $node.'_'.$n;?>" id="<?php echo $node.'_'.$n;?>" value="1" <?php echo $record[$node.'_'.$n] || ($mode==$insertMode && $v['default']==1)?'checked':'';?> onChange="dChge(this);" /><?php
		}else{
			?><input type="text" name="<?php echo $node.'_'.$n;?>" id="<?php echo $node.'_'.$n;?>" value="<?php echo h($record[$node.'_'.$n] ? $record[$node.'_'.$n] : ($mode==$insertMode ? $v['default']:''));?>" onChange="dChge(this);" <?php
			if($v['attributes'])foreach($v['attributes'] as $o=>$w)echo ' '.$o.'="'.h($w).'"';
			?> /><?php
		}
		?>
		</span>
		</div><?php
	}
	if(!empty($r))
	foreach($r as $n=>$v){


		if(@in_array(strtolower(preg_replace('/'.$node.'_/i','',$n)),$excludeFields))continue;

		if(!preg_match('/^'.$node.'_/i',$n))continue;
		foreach($record as $o=>$w){
			if(strtolower($o)==strtolower($n))break;
		}
		?><div>
		<div style="float:left;padding-right:7px;width:<?php echo $width?$width:'240px';?>"><?php echo preg_replace('/([a-z0-9])([A-Z])/','$1 $2',preg_replace('/'.$node.'_/i','',$o));?>:</div>
		<span><input type="text" name="<?php echo $o;?>" id="<?php echo $o;?>" value="<?php echo h($record[$o]);?>" onChange="dChge(this);" /></span>
		</div><?php
	}
}
//------------------------ Navbuttons head coding v1.43 -----------------------------
//change these first vars and the queries for each instance
$object='Items_ID';
$recordPKField='ID'; //primary key field
$navObject='Items_ID';
$updateMode='updateItem';
$insertMode='insertItem';
$deleteMode='deleteItem';
$insertType=1; //1=Save&New and Save&Close; 2 = Save and Save&New
#set these to 'disabled' if desired
$saveInitiallyDisabled='';
$saveAndNewInitiallyDisabled='';
$saveAndCloseInitiallyDisabled='';
//v1.4 change - some information about the coding
$navVer='1.43';
//v1.3 change - declare this function if you need to add parameters to the query string
$navQueryFunction='nav_query_add()';
//v1.3 change - deny transiting from Next to New mode (shutting off ability to insert)
$denyNextToNew=false;
//declare the query to get the idSet or subset, ordered by desired sort order - note that if you're using quasi resources, then be sure and filter them out.
if($searchtype && $q){
	$searchtypeResult=searchtype();
	if($searchtypeResult['records'] && !$Items_ID){
		$a=current($searchtypeResult['records']);
		$Items_ID=$a['ID'];
	}
}
$ids=q("SELECT ID FROM finan_items WHERE ResourceType IS NOT NULL ORDER BY Name",O_COL);
/*
(another good example more complex)
$ids=q("SELECT ID FROM `$cc`.finan_invoices WHERE Accounts_ID='$Accounts_ID' ORDER BY InvoiceDate, CAST(InvoiceNumber AS UNSIGNED)",O_COL);
*/

$nullCount=count($ids);
$j=0;
if($nullCount){
	foreach($ids as $v){
		$j++; //starting value=1
		if($j==$abs+$nav || (isset($$object) && $$object==$v)){
			$nullAbs=$j;
			//get actual primary key if passage by abs+nav
			if(!$$object) $$object=$v;
			break;
		}
	}
}else{
	$nullAbs=1;
}
//note the coding to on ResourceToken - this will allow a submitted page to come up again if the user Refreshes the browser
if(strlen($$object) || $Items_ID=q("SELECT ID FROM finan_items WHERE ResourceToken='$ResourceToken'", O_VALUE)){
	//get the record for the object
	if($record=q("SELECT * FROM finan_items WHERE ID='".$$object."'",O_ROW)){
		unset($record['Items_ID']);
		@extract($record);
		$mode=($ResourceType ? $updateMode : $insertMode);
		if($mode==$insertMode && !$FileName){
			if(!($FileName=$HBS_OriginalFileName)){
				mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='unable to pull filename from query'),$fromHdrBugs);
				exit($err.', developer has been notified');
			}
		}
	}else{
		mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='insert recovery option disabled'),$fromHdrBugs);
		exit($err.', developer has been notified');

		//object may have been deleted by another user, least perplexing approach is to present insert mode
		$mode=$insertMode;
		unset($$object);
		$nullAbs=$nullCount+1;
	}
}else{
	$mode=$insertMode;
	if(!$FileName){
		?>
		<h1>Add Method Disabled</h1>
		<p>
		You are not able to add a product this way.  Please close this window, and on the main menu click <pre>Products > Show Unassigned Files</pre>.  Click on any file to begin the adding process.<br />
		<input type="button" name="close" value="Close" onClick="window.close();" />
		</p><?php
		exit;
	}
	if(!(@$FileSize=filesize($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master/'.stripslashes($FileName)))){
		$err=('Unable to get size of requested file; please close window and try again');
	}
	if(!($g=getimagesize($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master/'.stripslashes($FileName)))){
		$err=('Unable to get dimensions of requested file; please close window and try again');
	}
	if($err){
		mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err),$fromHdrBugs);
		exit($err);
	}
	$$object=$ID=quasi_resource_generic($MASTER_DATABASE, 'finan_items', $ResourceToken, $typeField='ResourceType', $sessionKeyField='sessionKey', $resourceTokenField='ResourceToken', $primary='ID', $creatorField='Creator', $createDateField='CreateDate', C_DEFAULT, $options=array(
		'insertFields' => array('HBS_OriginalFileName','FileSize','Width1','Height1'),
		'insertValues' => array($FileName, $FileSize, $g[0], $g[1])
	));
	$nullAbs=$nullCount+1; //where we actually are right then
}
if($mode==$insertMode){
	$record=q("EXPLAIN finan_items", O_ARRAY);
	foreach($record as $n=>$v){
		$record[$v['Field']]='';
		unset($record[$n]);
	}
	//but do not extract it

	if(@$g=getimagesize($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master/'.stripslashes($FileName))){
		$Width1=$g[0];
		$Height1=$g[1];
	}else{
		$triggerError['FileName']=true;
	}
}
//--------------------------- end coding --------------------------------

$hideCtrlSection=false;
if(minroles()>ROLE_AGENT)exit('You do not have access to this page');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo ($PageTitle='Manage Products - '.$AcctCompanyName);?></title>



<link rel="stylesheet" type="text/css" href="/Library/css/cssreset01.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
.em{
	font-style:italic;
	}
body{
	background-image:url("/images/assets/productsbg-artshop.jpg");
	background-repeat:no-repeat;
	background-position:0px 0px;
	}
.tabSectionStyleIII{
	background:rgba(128, 128, 128, 0.6);
	}
textarea{
	background-color:#fff;
	}
.yat .bottom td, .yat th{
	background-color:darkolivegreen;
	color:white;
	border:1px solid #000;
	}

textarea{
	background-color:#F4EEDF;
	padding:5px;
	border:1px solid #999;
	}
fieldset{
	margin-top:15px;
	}
legend{
	font-size:119%;
	font-weight:900;
	letter-spacing:0.03em;
	}
#individualStatus{
	font-family:Georgia, "Times New Roman", Times, serif;
	color:darkgreen;
	font-size:larger;
	}
.j1mt td{
	padding:3px 7px 1px 2px;
	}
.barcode{
	background-color:white;
	font-family:"3 of 9 Barcode";
	font-size:80px;
	padding:20px 35px;
	
	}
#morePoints{
	margin:7px 0px;
	border:1px dotted #000;
	background-color:rgba(255,0,255,.05);
	border-radius:7px;
	-moz-border-radius:7px;
	padding:10px 5px;
	}
.redBold{
	color:darkred;
	font-weight:900;
	}
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js" type="text/javascript"></script>
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
var isDeletable=true; //required to fire off
var isModal=0;
var talks=0; //whether this page broadcasts overall state changes which other pages listen for
var listens=0;

var deleteWarning='This will permanently delete this record from the database; are your sure?';

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already
//var customDeleteHandler='deleteObject()'; //optional; default submits to bais_01_exe.php?mode=(deleteMode)

<?php if($_COOKIE['tenhanced_default']=='section6c')echo 'AddOnloadCommand(\'initialize()\');';?>

function interlock1(o){
	g('SKU2').disabled=(o.value.length==4);
	if(g('SKU2').disabled==true)g('SKU2').value='';
}
function seeOthers(h,mode){
	if(mode=='insertItem'){
		ow(h+g('SKU1').value+(g('SKU2').disabled ? '' : g('SKU2').value),'l1_rpt','950,500');
	}else{
		ow(h+g('SKU').value,'l1_rpt','950,500');
	}
	return false;
}
var bsValue='';
function sfn(o){
	bsValue=g('uploadFile1').value+'';
	setTimeout('bsFunction2()',100);
}
function bsFunction2(){
	g('selectedFileName').innerHTML=bsValue;
}
function recalc(){
	var w=parseInt(g('Width1').value);
	var h=parseInt(g('Height1').value);
	if(!w || !h){
		alert('You need to have a file width and height specified');
		return false;
	}
	window.open('/gf5/console/resources/bais_01_exe.php?mode=recalcPrices&Width1='+w+'&Height1='+h,'w2');
	return false;
}
/*
function deleteObject(){
	try{
	if(g('mode').value==g('deleteMode').value)return;
	}catch e();
	if(typeof deleteWarning=='undefined')deleteWarning='This will permanently delete this record.  Are you sure?';
	if(!confirm(deleteWarning))return;
	window.open('/gf5/console/bais_01_exe.php?mode=deleteObject&object='+datasetObject+'&Objects_ID='+g('ID').value, 'w2');
}
*/
window.resizeTo(925,700);
</script>

<link rel="stylesheet" type="text/css" href="/Library/css/DHTML/rich_calendar.css">
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar_lang_en.js"></script>
<?php ob_start();?>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onSubmit="return beginSubmit();">
<div id="header">
<?php
//set form to handle files
$out=ob_get_contents();
ob_end_clean();
echo  str_replace('onsubmit="return beginSubmit();"', 'enctype="multipart/form-data"  onsubmit="return beginSubmit();"',$out);
?>
<div id="btns140" class="fr"><?php
ob_start();
if($mode==$updateMode){
	?><input type="submit" name="Submit" value="Save" onClick="g('navMode').value='remain';detectChange=0;" />&nbsp;&nbsp;&nbsp;&nbsp;<?php
}
?>
<input id="Previous" type="button" name="Submit" value="Previous" class="navButton_A" onClick="focus_nav(-1, '<?php echo ($mode==$insertMode?'insert':'update')?>', <?php echo $mode==$insertMode?1:0?>, 0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs<=1?'disabled':''?> >
<?php
//Handle display of all buttons besides the Previous button
if($mode==$insertMode){
	if($insertType==2 /** advanced mode **/){
		//save
		?><input id="Save" type="button" name="Submit" value="Save" class="navButton_A" onClick="focus_nav(0,'insert',1,2<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveInitiallyDisabled?>><?php
	}
	if(false){
		//save and new - common to both modes
		?><input id="SaveAndNew" type="button" name="Submit" value="Save &amp; New" class="navButton_A" onClick="if(!confirm('This will save this record and clear the form, however you will not have an image associated with the cleared form.  You can press Save & Close instead.  Are you sure?'))return false; focus_nav(0,'insert', 1,1<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndNewInitiallyDisabled?>><?php
	}
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
	'<input id="'.$navbuttonSetDefault.'" type="button"', 
	'<input id="'.$navbuttonSetDefault.'" type="submit"', 
	$navbuttons
);
echo $navbuttons;

// *note that we could go back to the same page the 'New Record' click appeared on, but there's major issues programmatically on whether it would shift because of the placement of the new record.
// *note that the primary key field is now included here to save time
?>
<?php
if($FileName){
	?><input type="hidden" name="FileName" id="FileName" value="<?php echo stripslashes($FileName);?>" /><?php
}
?>
<input name="<?php echo $recordPKField?>" type="hidden" id="<?php echo $recordPKField?>" value="<?php echo $$object;?>" />
<input name="navVer" type="hidden" id="navVer" value="<?php echo $navVer?>" />
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

<h1><?php echo $AcctCompanyName;?> Product Management</h1>

<?php
if($searchtypeResult){
	extract($searchtypeResult);
	if($records)
	subsearch(array(
		'resultsLabel'=>'<strong>Items in this group</strong>',
		'recordPKField'=>'ID',
		'primaryLabelField'=>'SKU',
		'navObject'=>'Items_ID',
		'result'=>$records,
		'navQueryString'=>'searchtype='.$searchtype.'&q='.urlencode(stripslashes( is_array($q) ? implode('|',$q) : $q )),
	));
}
?>
<?php
if($triggerError['FileName']){
	?><div id="imgWarning" class="balloon1">WARNING!! You are creating a record for file <strong><?php echo stripslashes($FileName);?></strong>, however the main stock image is not present in /images/documentation/<?php echo $GCUserName;?>/pending_master.</div><?php
}else if($FileName){
	?><div id="creatingNew">Creating new product from file: <strong class="red"><?php echo stripslashes($FileName);?></strong></div><?php
}

?>

Name:<br /> 
  <textarea name="Name" cols="65" rows="1.5" id="Name" onChange="dChge(this);"><?php echo $n=h($Name ? $Name : ($mode==$insertMode && $FileName ? preg_replace('/\.(jpg|gif|png|svg)$/i','',$FileName) : ''));?></textarea> <span id="nL" class="<?php echo strlen($n)>70?'redbold':''?>"><span id="nameLengthVal"><?php echo strlen($n);?></span> characters</span>
  <script language="javascript" type="text/javascript">
  function nameLength(){
  	g('nameLengthVal').innerHTML=g('Name').value.length;
	g('nL').className=(g('Name').value.length>70?'redBold':'');
	setTimeout('nameLength()',100);
  }
  nameLength();
  </script>
  <br />
SKU: 
<?php
if($mode==$insertMode){
	//in case offered
	$SKU1=substr($SKU,0,2);
	?>
	<select name="SKU1" id="SKU1" onChange="dChge(this);interlock1(this)" style="width:80px;">
	<option value="">--</option>
	<?php 
	foreach(q("SELECT a.Identifier, CONCAT(a.LastName, IF(a.FirstName!='',', ',''),a.FirstName) FROM art_artists a ORDER BY a.Identifier", O_COL_ASSOC) as $n=>$v){
		?><option value="<?php echo $n;?>" <?php if($n==$SKU1)echo $selected='selected';?>><?php echo $n . ($v!==$n?' - ' . $v:'');?></option><?php
	}
	if($SKU1 && !$selected){
		?><option selected="selected" value="<?php echo $SKU1;?>"><?php echo $SKU1;?></option><?php 
	}
	?>
	</select>
	<input name="SKU3" type="text" id="SKU3" onChange="dChge(this);" value="<?php echo $mode==$insertMode?'(auto)':$SKU3;?>" size="4" maxlength="6" <?php echo $mode==$insertMode?'class="ghost"':''?> onFocus="if(this.value=='(auto)'){this.className='';this.value='';}" onBlur="if(this.value==''){this.className='ghost';this.value='(auto)';}" /> 
	<?php
}else{
	?>
	<input type="text" name="SKU" id="SKU" onChange="dChge(this);" value="<?php echo $SKU;?>" />
	<input type="hidden" name="OriginalSKU" id="OriginalSKU" value="<?php echo $SKU;?>" />
	<?php
}
?>&nbsp;&nbsp;[<a href="report_generic.php?report=itemsquery&searchtype=SKU1&q=" onClick="return seeOthers(this.href, '<?php echo $mode?>');">see others</a>]

<?php ob_start(); //------------- begin tabs ------------------- ?>

Category:
<?php
//this hijacks the function to do a SELECT DISTINCT list with add-new capability
echo relatebase_dataobjects_settings('Category',array(
	'a'=>array(
		'AddThroughModification'=>'distinct',
		'ForeignKeyField'=>'Category',
		'AllowAddNew'=>true,
		'AddThrough'=>'simple',
		'InsertLabel'=>'< Select.. >',
		'MapsToField'=>'DISTINCT Category',
		'LabelField'=>'Category',
		'InTable'=>'finan_items',
		'JoinType'=>'oneToMany',
		'AllowBlankOnUpdate'=>'(none)',
		'oneToManyDatasetWhere'=>'Category!=\'\''
	),
	'configNode'=>'Category',
));
?>&nbsp;&nbsp;
Subcategory:
<?php

//this hijacks the function to do a SELECT DISTINCT list with add-new capability
ob_start();
echo relatebase_dataobjects_settings('SubCategory',array(
	'a'=>array(
		'AddThroughModification'=>'distinct',
		'ForeignKeyField'=>'SubCategory',
		'AllowAddNew'=>true,
		'AddThrough'=>'simple',
		'InsertLabel'=>'< Select.. >',
		'MapsToField'=>'DISTINCT SubCategory',
		'LabelField'=>'SubCategory',
		'InTable'=>'finan_items',
		'JoinType'=>'oneToMany',
		'AllowBlankOnUpdates'=>'(none)',
		'oneToManyDatasetWhere'=>'SubCategory!=\'\''
	),
	'configNode'=>'SubCategory',
));
$out=ob_get_contents();
ob_end_clean();
echo str_replace('name="SubCategory"','name="SubCategory" tabindex="-1"',$out);
?>

<br />
<fieldset>
<legend>File Attributes</legend>
<div class="fr" style="width:200px;">
<div class="fr">[<a href="#" onClick="return recalc();" title="recalculate prices">recalculate</a>]</div>
Price:<br />
<?php
if($Width1 && $Height1){
	if($Width1>$Height1){
		$max='Width1';$min='Height1';
	}else{
		$min='Width1';$max='Height1';
	}
	$divisor=$$min / 23;
	$overflow=$$max/$divisor;
}
?>
<?php if($mode==$insertMode && (!$Width1 || !$Height1)){ ?>
Prices are calcuated based on pixel dimensions of the file
<?php }else{ ?>
<?php
if(!$Width1 || !$Height1){
	?><div class="red">Price cannot be shown; no pixel dimensions provided!</div><?php
}else{
	?>
	<table cellpadding="0">
      <tr>
        <td>Basic print: </td>
        <td>$ <input name="HMR_Price1" type="text" id="HMR_Price1" onChange="dChge(this);" value="<?php
	$actual= round((23 * $overflow) * PRINT_BASIC,2);
	$actual=floor($actual)+ .99;
	echo number_format($actual,2);
	 ?>" size="4" maxlength="6" readonly="readonly" />        </td>
      </tr>
      <tr>
        <td>Laminated:</td>
        <td>$          <input name="HMR_Price2" type="text" id="HMR_Price2" onChange="dChge(this);" value="<?php
	$actual= round((23 * $overflow) * PRINT_LAMINATED,2);
	$actual=floor($actual)+ .99;
	echo number_format($actual,2);
	 ?>" size="4" maxlength="6" readonly="readonly" />        </td>
      </tr>
      <tr>
        <td>Giclee:</td>
        <td>$          <input name="HMR_Price3" type="text" id="HMR_Price3" onChange="dChge(this);" value="<?php
	$actual= round((23 * $overflow) * PRINT_GICLEE,2);
	$actual=floor($actual)+ .99;
	echo number_format($actual,2);
	 ?>" size="4" maxlength="6" readonly="readonly" />        </td>
      </tr>
      <tr>
        <td>Canvas:</td>
        <td>$          <input name="HMR_Price4" type="text" id="HMR_Price4" onChange="dChge(this);" value="<?php
	$actual= round((23 * $overflow) * PRINT_CANVAS,2);
	$actual=floor($actual)+ .99;
	echo number_format($actual,2);
	 ?>" size="4" maxlength="6" readonly="readonly" />        </td>
      </tr>
    </table>
	<br />
	Based on size: <?php echo number_format($Width1/$divisor,2). ' x '.number_format($Height1/$divisor,2);?>
	
	<?php
}
?>
<?php } ?>
</div>
File Size:
<input name="FileSize" type="text" id="FileSize" value="<?php echo h($FileSize);?>" size="7" onChange="dChge(this);" />
<?php if($FileSize){ ?>
(<?php echo round($FileSize / 1024 / 1024,2);?>MB)
<?php }else{ ?>
<em class="gray">(bytes)</em>
<?php } ?>
<br />
Width: 
<input name="Width1" type="text" id="Width1" value="<?php echo h($Width1);?>" size="7" onChange="dChge(this);" />
<em class="gray">(pixels)</em><br />
Height: 
<input name="Height1" type="text" id="Height1" value="<?php echo h($Height1);?>" size="7" onChange="dChge(this);" />
<em class="gray">(pixels)</em><br />
DPI: 
<input name="DPI1" type="text" id="DPI1" value="<?php echo number_format($divisor,4);?>" size="9" onChange="dChge(this);" readonly />
 <em class="gray">(for 23&quot; dimension)</em><br />
</fieldset>
<br />
<div class="fr" style="width:370px;">Artist: 
  <select name="Artists_Identifier" id="Artists_Identifier" onchange="newOption(this, 'systementry.php', 'l2_artists', '600,600', 'object=art_artists');dChge(this);" cbtable="art_artists">
  <option value="PHP:NULL"><?php echo $mode==$insertMode?'&lt;Select..&gt;':'(none/unknown)';?></option>
  <?php
  if($a=q("SELECT Identifier, LastName, FirstName FROM art_artists ORDER BY LastName, FirstName", O_ARRAY))
  foreach($a as $v){
  	?><option value="<?php echo $v['Identifier'];?>" <?php echo $Artists_Identifier==$v['Identifier']?'selected':''?>><?php echo $v['LastName']. ($v['FirstName'] ? ', '.$v['FirstName']:'');?></option><?php
  }
  ?>
  <option value="{RBADDNEW}" style="background-color:thistle;">&lt;Add new..&gt;</option>
  </select>
  <br />
  Publication: 
  <select name="Publications_ID" id="Publications_ID" onchange="newOption(this, 'systementry.php', 'l2_pubs', '600,600', 'object=art_publications');dChge(this);" cbtable="art_publications">
  <option value="PHP:NULL"><?php echo $mode==$insertMode?'&lt;Select..&gt;':'(none/unknown)';?></option>
  <?php
  if($a=q("SELECT ID, PublicationName FROM art_publications ORDER BY PublicationName", O_COL_ASSOC))
  foreach($a as $n=>$v){
  	?><option value="<?php echo $n;?>" <?php echo $Publications_ID==$n?'selected':''?>><?php echo $v;?></option><?php
  }
  ?>
  <option value="{RBADDNEW}" style="background-color:thistle;">&lt;Add new..&gt;</option>
  </select>
  <br />
  <br />
  <br />
  Theme:
  <select name="Theme" id="Theme" onChange="dChge(this);interlock2(this.value);" cbtable="finan_items">
    <option value="">&lt;Select..&gt;</option>
    <?php
	$a=q("SELECT Name, Description FROM finan_items_themes ORDER BY Name", O_COL_ASSOC);
	foreach($a as $n=>$v){
		?>
    <option value="<?php echo h($n)?>" <?php echo strtolower($Theme)==strtolower($n)?'selected':''?>><?php echo h($n);?></option>
    <?php
	}
	?>
    <option style="background-color:cornsilk;" value="{RBADDNEW}">&lt;Add new..&gt;</option>
  </select> 
  &nbsp;[<a href="list_themes.php" title="View and manage product themes" onClick="return ow(this.href,'l1_themes', '650,350');">see all</a>] 
  <script language="JavaScript" type="text/javascript">
	function interlock2(n){
		if(n=='')return;
		if(n=='{RBADDNEW}'){
			newOption(g('Theme'), '/gf5/console/themes.php', 'l1_themes', '650,350', 'cat=hat');
			return;
		}
		if(g('SubTheme').value!='' && !confirm('Replace Theme Paragrph?'))return false;
		g('SubTheme').value=themes[n];
	}
	var themes={<?php
	if($a){
		$i=0;
		echo "\n";
		foreach($a as $n=>$v){
			$i++;
			if($i>1)echo ', '."\n";
			echo '\''.str_replace('\'','\\\'',$n).'\'';
			echo ':';
			echo '\''.str_replace('\'','\\\'',$v).'\'';
		}
	}
	?>}
	function upn(){
		if(g('Name').value==''){
			alert('Enter a name at top first');
			return false;
		}
		if(g('MetaTitle').value!='' && !confirm('Replace existing value?'))return false;
		g('MetaTitle').value=g('Name').value;
		return false;
	}
	function ckw(){
		g('MetaKeywords').value=g('MetaKeywords').value.replace(/, /g,',');
		return false;
	}
	</script>
  <br />
Theme Paragraph: [<a href="list_texts.php?field=SubTheme" title="See a list of usages of this text" onClick="return ow(this.href,'l1_texts','700,350');">see more</a>] <br />
<textarea id="SubTheme" name="SubTheme" onChange="dChge(this);" rows="3" cols="35"><?php echo h($SubTheme);?></textarea>
</div>
<input type="hidden" name="ART_COVER" value="0" />
<label><input type="checkbox" name="ART_Cover" id="ART_Cover" value="1" <?php echo $ART_Cover?'checked':''?> onchange="dChge(this);" /> Cover</label>
<br />
Year of this picture:
<?php
if($mode==$insertMode && preg_match('/([1-2][0-9]{2}([0-9]|0s))/i',str_replace('\'','',$FileName),$m)){
	$HBS_Year=$m[1];
}
?>
<input name="HBS_Year" type="text" id="HBS_Year" value="<?php if($HBS_Year!='0000')echo h($HBS_Year);?>" onChange="dChge(this);" size="10" maxlength="20" />
&nbsp;
(<label>
<input name="HBS_YearEstimated" type="checkbox" id="HBS_YearEstimated" value="1" <?php echo $HBS_YearEstimated?'checked':''?> onChange="dChge(this);" />
Estimated</label>)<br />
<br />
Description:<br />
<textarea id="Description" name="Description" onChange="dChge(this);" rows="2" cols="55"><?php echo h($Description);?></textarea>
<br />
Longer Description <em class="gray">(optional)</em>:<br />
<textarea cols="55" id="LongDescription" name="LongDescription" rows="5" onChange="dChge(this);"><?php
//this is easy
echo h($LongDescription);
?></textarea>

<fieldset><legend>SEO</legend>
Title: <input name="MetaTitle" type="text" id="MetaTitle" value="<?php echo h($MetaTitle);?>" size="45" onChange="dChge(this);" /> 
&nbsp;[<a href="#" onClick="return upn();">copy product name</a>] &nbsp;&nbsp;<span id="charlen"></span> characters
<script language="javascript" type="text/javascript">
function chars(){
var n = g('MetaTitle').value.length;
g('charlen').innerHTML=n;
setTimeout('chars()',200);
}
chars();
</script>
<br />
Description: <br />
<textarea name="MetaDescription" cols="60" rows="3" id="MetaDescription" onChange="dChge(this);"><?php echo h($MetaDescription);?></textarea>
<br />
Keywords:
<br />
<div>
<div style="float:left;"><textarea name="MetaKeywords" cols="35" rows="3" id="MetaKeywords" onChange="dChge(this);"><?php echo h($MetaKeywords);?></textarea></div>
<div style="float:left;"> &nbsp; [<a href="#" onClick="return ckw();">clean comma-space</a>]</div>
<div class="cb">SEO-Friendly URL <span class="red">(MUST BE UNIQUE)</span>:<br /> 
<input name="SEO_Filename" type="text" id="SEO_Filename" onChange="dChge(this);" value="<?php

  if($SEO_Filename){
  	echo h($SEO_Filename);
  }else if($mode==$insertMode && strlen($FileName)){
  	echo h(preg_replace('/[^-a-z0-9A-Z]/','',preg_replace('/-+/','-',str_replace(' ','-',preg_replace('/\.jpg$/i','',$FileName)))));
  }else if(false && !$SEO_Filename && $Name){
  	echo h(preg_replace('/[^-a-z0-9A-Z]/','',preg_replace('/-+/','-',str_replace(' ','-',preg_replace('/\.jpg$/i','',$Name)))));
  }
  ?>" size="56" maxlength="56" /> &nbsp;&nbsp;<span id="SEO_FilenameChars"></span>
  <br />
  <script language="javascript" type="text/javascript">
  function Chars(n){
  	g(n+'Chars').innerHTML='('+g(n).value.length+' characters)';
	setTimeout('Chars(\''+n+'\');',100);
  }
  Chars('SEO_Filename');
  </script>
</div>
</div>
</fieldset>

<?php
//------------------------- store tab ------------------------------
get_contents_tabsection('section1');
?>
<table cellpadding="0">
  <tr>
    <th scope="col" style="padding:1px 4px;">Status</th>
    <th class="tac" scope="col" style="padding:1px 4px;">Last Export </th>
    <th scope="col" style="padding:1px 4px;">Other Records </th>
  </tr>
  <tr>
    <td><label>
      <input type="checkbox" name="MIVA_ToBeExported" value="1" <?php echo isset($MIVA_ToBeExported) && $MIVA_ToBeExported=='0' ? '' : 'checked';?> onChange="dChge(this);" />
needs to be exported to MIVA </label></td>
    <td class="tac"><?php
	if($mode!=$insertMode && $a=q("SELECT b.ID, b.CreateDate FROM gen_batches b, gen_batches_entries e WHERE b.ID=e.Batches_ID AND b.SubType='MIVA' AND /* e.ObjectName='finan_items'  AND */ e.Objects_ID=$ID ORDER BY b.CreateDate DESC LIMIT 1", O_ROW)){
		?><a href="report_generic.php?report=itemsquery&searchtype=batch&q=<?php echo $a['ID'];?>" onClick="return ow(this.href,'l1_reportexport','825,700');" title="view this batch's records"><?php echo date('n/j/Y \a\t g:iA',strtotime($a['CreateDate']));?></a><?php
	}else{
		?><span class="gray">N/A</span><?php
	}
	?></td>
    <td class="tac"><?php
	if($a=q("SELECT COUNT(*) FROM finan_items i WHERE 1 AND i.MIVA_ToBeExported=1", O_VALUE)){
		?><a href="report_generic.php?report=itemsquery&searchtype=merchanttobeexported&q=MIVA" onClick="return ow(this.href,'l1_reportexport','825,700');" title="view these records"><?php echo $a.' total';?></a><?php
	}else{
		?><em class="gray">(none)</em><?php 
	}
	?></td>
  </tr>
  <tr>
    <td><label>
      <input type="checkbox" name="EBAY_ToBeExported" value="1" <?php echo isset($EBAY_ToBeExported) && $EBAY_ToBeExported=='0' ? '' : 'checked';?> onChange="dChge(this);" />
needs to be exported to EBay</label></td>
    <td class="tac"><?php
	if($mode!=$insertMode && $a=q("SELECT b.ID, b.CreateDate FROM gen_batches b, gen_batches_entries e WHERE b.ID=e.Batches_ID AND b.SubType='EBAY' AND /* e.ObjectName='finan_items'  AND */ e.Objects_ID=$ID ORDER BY b.CreateDate DESC LIMIT 1", O_ROW)){
		?><a href="report_generic.php?report=itemsquery&searchtype=batch&q=<?php echo $a['ID'];?>" onClick="return ow(this.href,'l1_reportexport','825,700');" title="view this batch's records"><?php echo date('n/j/Y \a\t g:iA',strtotime($a['CreateDate']));?></a><?php
	}else{
		?><span class="gray">N/A</span><?php
	}
	?></td>
    <td class="tac"><?php
	if($a=q("SELECT COUNT(*) FROM finan_items i WHERE 1 AND i.EBAY_ToBeExported=1", O_VALUE)){
		?><a href="report_generic.php?report=itemsquery&searchtype=merchanttobeexported&q=EBAY" onClick="return ow(this.href,'l1_reportexport','825,700');" title="view these records"><?php echo $a.' total';?></a><?php
	}else{
		?><em class="gray">(none)</em><?php 
	}
	?></td>
  </tr>
  <tr>
    <td><label>
      <input type="checkbox" name="AMAZON_ToBeExported" value="1" <?php echo isset($AMAZON_ToBeExported) && $AMAZON_ToBeExported=='0' ? '' : 'checked';?> onChange="dChge(this);" />
needs to be exported to Amazon</label></td>
    <td class="tac"><?php
	if($mode!=$insertMode && $a=q("SELECT b.ID, b.CreateDate FROM gen_batches b, gen_batches_entries e WHERE b.ID=e.Batches_ID AND b.SubType='AMAZON' AND /* e.ObjectName='finan_items'  AND */ e.Objects_ID=$ID ORDER BY b.CreateDate DESC LIMIT 1", O_ROW)){
		?><a href="report_generic.php?report=itemsquery&searchtype=batch&q=<?php echo $a['ID'];?>" onClick="return ow(this.href,'l1_reportexport','825,700');" title="view this batch's records"><?php echo date('n/j/Y \a\t g:iA',strtotime($a['CreateDate']));?></a><?php
	}else{
		?><span class="gray">N/A</span><?php
	}
	?></td>
    <td class="tac"><?php
	if($a=q("SELECT COUNT(*) FROM finan_items i WHERE 1 AND i.AMAZON_ToBeExported=1", O_VALUE)){
		?><a href="report_generic.php?report=itemsquery&searchtype=merchanttobeexported&q=AMAZON" onClick="return ow(this.href,'l1_reportexport','825,700');" title="view these records"><?php echo $a.' total';?></a><?php
	}else{
		?><em class="gray">(none)</em><?php 
	}
	?></td>
  </tr>
</table>

<input name="button" type="button" value="Vendor Export Status" onClick="return ow('export_summary.php','l1_expsummary','550,600');" />

	<?php #specific_fields('MIVA');?>

<?php
//------------------------- store tab ------------------------------
get_contents_tabsection('section2');
?><br />

<div id="imgViewport"><?php
//we assume this is not present in any coding or db info - and cannot be passed
unset($Tree_ID);

if($mode==$insertMode || !$ResourceType){
	if($FileName){
		//in the pending folder
		$trueFileHTTPPath='images/documentation/'.$GCUserName.'/pending/'.stripslashes($FileName);
		$disposition='600x';
		ob_start();
		$a=tree_image($trueFileHTTPPath);
		$img=ob_get_contents();
		ob_end_clean();
		?>
		click image to view larger size<br />

		<a href="/images/reader.php?Tree_ID=<?php echo $a['Tree_ID'];?>&Key=<?php echo md5($a['Tree_ID'].$MASTER_PASSWORD);?>&disposition=x" onClick="return ow(this.href,'l1_fullsize','1200,1200');">
		<?php echo $img;?>		</a>
		<?php
		//for thumbnail creator below
		$Tree_ID=$a['Tree_ID'];
		list($imgTrueWidth, $imgTrueHeight) = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$trueFileHTTPPath);
	}else{
		$showUpload=true;	
	}
}else{
	$get_imageReturnMethod='array';
	if($a=get_image($SKU,$imgArray)){
		$a=current($a);
		//in the root
		$trueFileHTTPPath='images/documentation/'.$GCUserName.'/'.$a['name'];
		$Tree_ID=tree_build_path($trueFileHTTPPath);
		$imgTrueWidth=$a['width'];
		$imgTrueHeight=$a['height'];
		?>
		File name: <strong><?php echo $a['name'];?></strong><br />
		Working image size: <strong><?php echo round($a['size'],2).'Kb';?></strong><br />
		Dimensions: <strong><?php echo $a['width'] . 'x'.$a['height'];?></strong><br />
		<br />
		<img src="<?php echo '/images/reader.php?Tree_ID='.$Tree_ID.'&Key='.md5($Tree_ID.$MASTER_PASSWORD);?>" alt="img" /><?php
	}else{
		$showUpload=true;	
	}
}
if($showUpload){
	?>
	<h3>Upload a File</h3>
	<p>
	PLEASE BE PATIENT when uploading large files as it may take 5-15 minutes or more depending on the uploaded file size.<br />
	Pictures with a dimension smaller than 1500 pixels on either
	edge will be interpreted as the &quot;main thumbnail&quot;, and will be named as {your SKU}.jpg - however you must upload the full-sized file as well <span class="red">before you can sell this item online!</span><br />
	<br />
	<br />
	<div>
	<div style="float:left;padding-top:5px;">Select file from your computer: </div>	<div style="float:left; width:90px; text-align:left; overflow:hidden;"><div style="margin-left:-148px;"><input type="file" name="uploadFile1" id="uploadFile1" onChange="dChge(this);sfn(this)" /></div></div>
	<div id="selectedFileName" style="float:left;padding-top:5px;color:darkgreen;"> (no file selected) </div>
	<div id="fileProcessing" style="display:none;"><img src="/images/i/ani/ani-fb-orange.gif" width="16" height="11" /></div>
	</div>
	</p>
	<p><br />
    </p>
	<?php
}
?>
</div>

<?php
//------------------------- store tab ------------------------------
get_contents_tabsection('section6');
?>

<?php
if($mode==$insertMode){
	?><p class="gray">Currently being created <strong><?php if($FileName)echo 'with file '.$FileName;?></strong> by <?php echo sun('fnln');?></p><?php
}else{
	if(minroles()<=ROLE_ADMIN){
		?><div class="fr">
		<input type="button" name="Button" value="Delete Record" onClick="PropKeyPress({keyCode:100,ctrlKey:true,returnValue:false});" tabindex="-1" />
		</div><?php
	}
	?>
	<table>
	<tr>
		<td class="tar">Internal ID:</td>
		<td><strong><?php echo $ID?></strong></td>
	</tr>
	<tr>
		<td class="tar">Created:</td>
		<td><?php echo date('F jS \a\t g:iA',strtotime($CreateDate)) . ' by '.(sun()==$Creator?'you':$Creator);?></td>
	</tr>
	<?php
	if(abs(strtotime($CreateDate) - strtotime($EditDate))>5){
		?>
		<tr>
			<td class="tar">Last edit:</td>
			<td><?php echo date('F jS Y \a\t g:iA',strtotime($EditDate)) . ($Editor ? ' by '.(sun()==$Editor?'you':$Editor):'');?></td>
		</tr>
		<?php
	}
	?>
	</table>
	
	<?php
	if($mode==$updateMode){
		?><h2>UPC Codes</h2>
		Your merchant code: <?php 
		if($merchantUPCCode=q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='items' AND varnode='settings' AND varkey='merchantUPCCode'", O_VALUE)){
			echo $merchantUPCCode;
		}else{
			$merchantUPCCode='000000';
			?><a href="preferences.php" target="_parent">not present, go to settings</a>
		<?php
		}
		?><br />
		<table class="j1mt" cellpadding="0">
          <tr>
            <th>Type</th>
            <th>UPC Code </th>
            <th>Bar Code </th>
          </tr>
          <tr>
            <td>Basic Print: </td>
            <td><?php echo UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). PROD_BASIC);?></td>
            <td class="barcode"><?php echo UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). PROD_BASIC);?></td>
          </tr>
          <tr>
            <td>Laminated:</td>
            <td><?php echo UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). PROD_LAMINATED);?></td>
            <td class="barcode"><?php echo UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). PROD_LAMINATED);?></td>
          </tr>
          <tr>
            <td>Fine Paper:</td>
            <td><?php echo UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). PROD_GICLEE);?></td>
            <td class="barcode"><?php echo UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). PROD_GICLEE);?></td>
          </tr>
          <tr>
            <td>Canvas:</td>
            <td><?php echo UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). PROD_CANVAS);?></td>
            <td class="barcode"><?php echo UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). PROD_CANVAS);?></td>
          </tr>
          <tr>
            <td>Digital:</td>
            <td><?php echo UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). PROD_DIGITAL);?></td>
            <td class="barcode"><?php echo UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). PROD_DIGITAL);?></td>
          </tr>
        </table>
		<span class="gray">Don't see barcode in the 3rd column? <a href="/FRE3OF9X.TTF" target="_blank">Download the font here</a></span>
		
	<?php
		
	}
}

?>

<?php
//------------------------- store tab ------------------------------
get_contents_tabsection('section7');
?>
<?php
if(minroles()<ROLE_AGENT)$adminMode=1;
CMSB('help');
?>
<?php
//------------------------- store tab ------------------------------
get_contents_tabsection('section8');
?>

this should never show..

<?php
tabs_enhanced(array(
	'section1'=>array(
		'label'=>'Key Values'
	),
	'section2'=>array(
		'label'=>'Export'
	),
	'section6'=>array(
		'label'=>'File/Image'
	),
	'section7'=>array(
		'label'=>'Stats'
	),
	'section8'=>array(
		'label'=>'Help'
	),
));
?>
</div>
<div id="footer">
&nbsp;
</div>
</form>
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
</html><?php page_end();?>