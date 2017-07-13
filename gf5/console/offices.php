<?php
//identify this script/GUI
$localSys['scriptID']='mgresources';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

$hideCtrlSection=false;

//------------------------ Navbuttons head coding v1.41 -----------------------------
//change these first vars and the queries for each instance
$object='oa_unusername';
$recordPKField='oa_unusername'; //primary key field
$navObject='oa_unusername';
$updateMode='updateOffice';
$insertMode='insertOffice';
$insertType=1; //1=Save&New and Save&Close; 2 = Save and Save&New
#set these to 'disabled' if desired
$saveInitiallyDisabled='';
$saveAndNewInitiallyDisabled='';
$saveAndCloseInitiallyDisabled='';
//v1.4 change - some information about the coding
$navVer='1.41';
//v1.3 change - declare this function if you need to add parameters to the query string
$navQueryFunction=''; //nav_query_add()
//v1.3 change - deny transiting from Next to New mode (shutting off ability to insert)
$denyNextToNew=false;
//declare the query to get the idSet or subset, ordered by desired sort order - note that if you're using quasi resources, then be sure and filter them out.
$ids=q("SELECT oa_unusername FROM bais_orgaliases", O_COL);
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
if(strlen($$object) /* || $Assns_ID=q("SELECT ID FROM sma_assns WHERE ResourceToken!='' AND ResourceToken='$ResourceToken' AND ResourceType IS NOT NULL", O_VALUE) */){
	//get the record for the object
	if($a=q("SELECT * FROM bais_orgaliases, bais_offices WHERE oa_unusername=of_oausername AND oa_unusername='$oa_unusername'", O_ROW)){
		$mode=$updateMode;
		@extract($a);
	}else{
		//object may have been deleted by another user, least perplexing approach is to present insert mode
		$mode=$insertMode;
		unset($$object);
		$nullAbs=$nullCount+1;
	}
}else{
	$mode=$insertMode;
	$nullAbs=$nullCount+1; //where we actually are right then
}
//--------------------------- end coding --------------------------------
foreach($blankFills as $n=>$v){
	if(!trim($a[$n]) && !isset($$n)){
		$a[$n]=h($v);
	}
}
@extract($a);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Manage Office Locations</title>
<link id="cssUndoHTML" rel="stylesheet" href="/site-local/undohtml2.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />

<link rel="stylesheet" href="/Library/css/layers/layer_engine_v200.css" type="text/css"/>
<style>
.largerTreb{
	font-size:136%; font-weight:800
}
.fldSet1{
	padding:20px 20px 5px 10px;
	margin-left:10px;
}
body, .objectWrapper{
	background-color:#e3d5d9; /** bec8d9 **/
}
#searchGradCell{
	height:<?php echo $htFix?>px;
}
.xbc_{
	font-size:104%
	cursor:pointer;
}
.bc_{
	font-size:104%
	cursor:pointer;
}
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle .02 coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
var ResourceToken='<?php echo $ResourceToken;?>';
var Assns_ID='<?php echo $Assns_ID?>';

//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';
var pagekey='<?php echo $oa_unusername?>';
</script>
</head>
<body ><form name="form1" action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" target="w2">
<div id="headerBar1">
<div id="btns140" style="float:right;">
<!--
Navbuttons version 1.41. Last edited 2008-01-21.
This button set came from devteam/php/snippets
Now used in a bunch of RelateBase interfaces and also client components. Useful for interfaces where sub-records are present and being worked on.
-->
<?php
//Things to do to install this button set:
#1. install contents of this div tag (btns140)
#2. the coding above needs to go in the head of the document, change as needed to connect to the specific table(s) or get the resource in a different way
#3. must declare the following vars in javascript:
// var thispage='whatever.php';
// var thisfolder='myfolder';
// var count='[php:echo $nullCount]';
// var ab='[php:echo $nullAbs]';
#4. need js functions focus_nav() and focus_nav_cxl() in place
?>
<input id="Previous" type="button" name="Submit" value="Previous" onclick="focus_nav(-1, '<?php echo ($mode==$insertMode?'insert':'update')?>', <?php echo $mode==$insertMode?1:0?>, 0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs<=1?'disabled':''?> >
<?php
//Handle display of all buttons besides the Previous button
if($mode==$insertMode){
	if($insertType==2 /** advanced mode **/){
		//save
		?><input id="Save" type="button" name="Save" value="Save" onclick="focus_nav(0,'insert',1,2<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveInitiallyDisabled?>><?php
	}
	//save and new - common to both modes
	?><input id="SaveAndNew" type="button" name="SaveAndNew" value="Save &amp; New" onclick="focus_nav(0,'insert', 1,1<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndNewInitiallyDisabled?>><?php
	if($insertType==1 /** basic mode **/){
		//save and close
		?><input id="SaveAndClose" type="button" name="SaveAndClose" value="Save &amp; Close" onclick="focus_nav(0,'insert', 1,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndCloseInitiallyDisabled?>><?php
	}
	?><input id="CancelInsert" type="button" name="CancelInsert" value="Cancel" onclick="focus_nav_cxl('insert');"><?php
}else{
	//OK, and appropriate [next] button
	?><input id="OK" type="button" name="ActionOK" value="OK" onclick="focus_nav(0,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);">
	<input id="Next" type="button" name="Next" value="Next" onclick="focus_nav(1,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs>$nullCount || ($denyNextToNew && $nullAbs==$nullCount) ?'disabled':''?>><?php
}
// *note that we could go back to the same page the 'New Record' click appeared on, but there's major issues programmatically on whether it would shift becuase of the placement of the new record.
// *note that the primary key field is now included here to save time
?>
<input name="<?php echo $recordPKField?>" type="hidden" id="<?php echo $recordPKField?>" value="<?php echo $$object;?>">
<input name="navVer" type="hidden" id="navVer" value="<?php echo $navVer?>">
<input name="navObject" type="hidden" id="navObject" value="<?php echo $navObject?>">
<input name="nav" type="hidden" id="nav">
<input name="navMode" type="hidden" id="navMode" value="">
<input name="count" type="hidden" id="count" value="<?php echo $nullCount?>">
<input name="abs" type="hidden" id="abs" value="<?php echo $nullAbs?>">
<input name="insertMode" type="hidden" id="insertMode" value="<?php echo $insertMode?>">
<input name="updateMode" type="hidden" id="updateMode" value="<?php echo $updateMode?>">
<input name="mode" type="hidden" id="mode" value="<?php echo $mode?>">
<input name="componentID" type="hidden" id="componentID" value="<?php echo $localSys['componentID']?>">
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
					?><input name="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" id="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" type="hidden" value="<?php echo stripslashes($w);?>" /><?php
					echo "\n";
				}
			}else{
				echo "\t\t";
				?><input name="<?php echo $n?>" id="<?php echo $n?>" type="hidden" value="<?php echo stripslashes($v);?>" /><?php
				echo "\n";
			}
		}
	}
}
?><!-- end navbuttons 1.41 --></div>


Currently signed in: <strong><?php echo htmlentities($_SESSION['admin']['firstName'] . ' ' . $_SESSION['admin']['lastName'])?></strong>
</div>
<div style="width:630px;padding:7px 8px 0px 12px;">
<input name="oa_unusername" type="hidden" id="oa_unusername" value="<?php echo $oa_unusername?>">
<fieldset style="width:500px;">
<legend style="font-size:147%;font-weight:900;">Office Information</legend>
	<p>
	  <label>
	  <input name="PrimaryOffice" type="checkbox" id="PrimaryOffice" value="1" <?php
	  echo $PrimaryOffice || ($mode==$insertMode && !q("SELECT COUNT(*) FROM gf_offices", O_VALUE)) ? 'checked':'';
	  ?> onchange="dChge(this);" /> This is the main office </label>
	</p>
	Office Email (optional):
<input name="Email" type="text" class="ghost" id="Email"  onFocus="fill(' Email',this,1)" onBlur="fill(' Email',this,0)" onChange="mgeChge(this)" value="<?php echo $Email?>" size="35">
	<br />
Title:
<input name="oa_businessname" type="text" id="oa_businessname" value="<?php echo $oa_businessname?>" size="45" onChange="mgeChge(this);">
<br />
Shorter Title: 
<input name="oa_org2" type="text" id="oa_org2" value="<?php echo $oa_org2?>" maxlength="25" onchange="mgeChge(this);" />
<br />
<br />
Identifier:
<input name="oa_org1" type="text" id="oa_org1" value="<?php echo $oa_org1?>" size="6" maxlength="5" onChange="mgeChge(this);">
(Required, must be unique, 5 characters max) <br />
Office code: 
<input name="oa_orgcode" type="text" id="oa_orgcode" value="<?php echo $oa_orgcode?>" size="6" maxlength="5" onchange="mgeChge(this);" />
<br />
QuickBooks Class: 
<select name="Classes_ID" id="Classes_ID" onchange="mgeChge(this);">
<option value="">&lt; select.. &gt;</option>
<?php
foreach(q("SELECT IF(aa.ID IS NOT NULL, CONCAT(aa.Name,':'),'') AS Prefix, a.ID, a.Name FROM finan_classes a LEFT JOIN finan_classes aa ON a.Classes_ID=aa.ID", O_ARRAY) as $n=>$v){
	?><option value="<?php echo $v['ID']?>" <?php echo $v['ID']==$Classes_ID?'selected':''?>><?php echo $v['Prefix'].$v['Name']?></option><?php
}
?>
</select>
<br />
<textarea name="Address" cols="35" rows="2" id="Address" class="ghost" onBlur="fill(' Address',this,0)"  onFocus="fill(' Address',this,1)" onChange="mgeChge(this)"><?php echo $Address?></textarea>
<br />
<input name="City" type="text" class="ghost" id="City" onFocus="fill(' City',this,1)" onBlur="fill(' City',this,0)" onChange="mgeChge(this)" value="<?php echo $City?>" size="19">
<select name="State" id="State" onChange="countryInterlock('State','State','Country');mgeChge(this)" style="width:150px;">
	<option value="" class="ghost"> State <?php 
	$states=q("SELECT st_code, st_name FROM aux_states",$public_cnx,O_COL_ASSOC);
	foreach($states as $n=>$v){
		?><option value="<?php echo $n?>" <?php
		if($State==$n){
			$gotState=true;
			echo 'selected';
		}
		?>><?php echo htmlentities($v)?><?php
	}
	if(!$gotState && $State!=''){
		?><option value="<?php echo $State?>" style="background-color:tomato;" selected><?php echo $State?></option><?php
	}
?></select>
<input name="Zip" type="text" class="ghost" id="Zip" onFocus="fill(' Zip',this,1)" onBlur="fill(' Zip',this,0)" onChange="mgeChge(this)" value="<?php echo $Zip?>" size="10" maxlength="10">
<br />
<select name="Country" id="Country" onChange="countryInterlock('Zip','State','Zip');mgeChge(this)">
	<option value="" class="ghost"> Country<?php 
	$countries=q("SELECT ct_code, ct_name FROM aux_countries",$public_cnx,O_COL_ASSOC);
	foreach($countries as $n=>$v){
			?><option value="<?php echo $n?>" <?php
			if($Country==$n){
				$gotCountry=true;
				echo 'selected';
			}
			?>><?php echo htmlentities($v)?><?php
	}
	if(!$gotCountry && $Country!=''){
		?><option value="<?php echo $Country?>" style="background-color:tomato;" selected><?php echo $Country?></option><?php
	}
?></select>
<br />
Office Phone:
<input name="WorkPhone" type="text" id="WorkPhone" value="<?php echo $WorkPhone?>" size="18" onChange="mgeChge(this)" />
&nbsp;Fax:
<input name="Fax" type="text" id="Fax" value="<?php echo $Fax?>" size="18" onChange="mgeChge(this)" />
<br />
Mobile (optional)
<input name="Cell" type="text" id="Cell" value="<?php echo $Cell?>" onChange="mgeChge(this)" />
</fieldset><br />
</div></form>
<script>
darken();
</script>
<?php if(!$hideCtrlSection){ ?>
<div id="showTester" title="Javascript Tester" onclick="g('tester').style.display='block';">&nbsp;</div>
<div id="tester" >
	<a href="#" onclick="g('ctrlSection').style.display=op[g('ctrlSection').style.display]; return false;">iframes</a><br />
	<textarea name="test" cols="65" rows="4" id="test">clear_form();</textarea><br />
	<input type="button" name="button" value="Test" onclick="jsEval(g('test').value);"><br />
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
</html><?php page_end()?>