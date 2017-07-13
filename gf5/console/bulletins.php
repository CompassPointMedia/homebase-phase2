<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');



//------------------------ Navbuttons head coding v1.41 -----------------------------
//change these first vars and the queries for each instance
$object='Bulletins_ID';
$recordPKField='ID'; //primary key field
$navObject='Bulletins_ID';
$updateMode='updateBulletin';
$insertMode='insertBulletin';
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
$ids=@q("SELECT ID FROM gf_bulletins WHERE bl_unusername IN('".implode("','",list_chain_below())."')",O_COL);
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
if(strlen($$object) /*|| $Assns_ID=q("SELECT ID FROM sma_assns WHERE ResourceToken!='' AND ResourceToken='$ResourceToken' AND ResourceType IS NOT NULL", O_VALUE)*/){
	//get the record for the object
	if($a=q("SELECT * FROM gf_bulletins WHERE ID='".$$object."'",O_ROW)){
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


$hideCtrlSection=false;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo ($PageTitle='Manage Bulletins - '.$AcctCompanyName);?></title>



<link rel="stylesheet" type="text/css" href="/site-local/undohtml2.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
#intendedFor{
	border:1px solid darkred;
	padding:15px;
	margin:10px 0px;
	background-color:cornsilk;
	}
</style>
<script language="Javascript" type="text/javascript" src="/Library/js/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>

<link href="/Library/ckeditor_3.4/_samples/sample.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Library/ckeditor_3.4/ckeditor.js"></script>

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
var isEscapable=0;
var isDeletable=0;
var isModal=0;
var talks=0; //whether this page broadcasts overall state changes which other pages listen for
var listens=0;
</script>
<?php ob_start(); //buffer to change enctype on form ?>

<link rel="stylesheet" type="text/css" href="/Library/css/DHTML/rich_calendar.css">
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar_lang_en.js"></script>

<style type="text/css">
</style>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<?php
//set form type to handle files
$out=ob_get_contents();
ob_end_clean();
echo str_replace('<form id="form1" ','<form id="form1" enctype="multipart/form-data"',$out);
?>
<div id="headerBar1">
	<div id="btns140" style="float:right;">
	<!--
	Navbuttons version 1.41. Last edited 2008-01-21.
	This button set came from devteam/php/snippets
	Now used in a bunch of RelateBase interfaces and also client components. Useful for interfaces where sub-records are present and being worked on.
	-->
	<!--
	<input id="Previous" type="button" name="Submit" value="Previous" onclick="focus_nav(-1, '<?php echo ($mode==$insertMode?'insert':'update')?>', <?php echo $mode==$insertMode?1:0?>, 0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs<=1?'disabled':''?>>
	-->
	<?php
	//Handle display of all buttons besides the Previous button
	if($mode==$insertMode){
		if($insertType==2 /** advanced mode **/){
			//save
			?><input id="Save" type="button" name="Save" value="Save" onclick="focus_nav(0,'insert',1,2<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveInitiallyDisabled?> /><?php
		}
		//save and new - common to both modes
		?><!--
		<input id="SaveAndNew" type="button" name="SaveAndNew" value="Save &amp; New" onclick="focus_nav(0,'insert', 1,1<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndNewInitiallyDisabled?> />
		--><?php
		if($insertType==1 /** basic mode **/){
			//save and close
			?><input id="SaveAndClose" type="button" name="SaveAndClose" value="Submit Bulletin" onclick="focus_nav(0,'insert', 1,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndCloseInitiallyDisabled?> /><?php
		}
		?><input id="CancelInsert" type="button" name="CancelInsert" value="Cancel" onclick="focus_nav_cxl('insert');" /><?php
	}else{
		//OK, and appropriate [next] button
		?><input id="OK" type="button" name="ActionOK" value="OK" onclick="focus_nav(0,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" />
		<!--
		<input id="Next" type="button" name="Next" value="Next" onclick="focus_nav(1,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs>$nullCount || ($denyNextToNew && $nullAbs==$nullCount) ?'disabled':''?> />
		--><?php
		
	}
	// *note that we could go back to the same page the 'New Record' click appeared on, but there's major issues programmatically on whether it would shift becuase of the placement of the new record.
	// *note that the primary key field is now included here to save time
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
	<input name="mode" type="hidden" id="mode" value="<?php echo $mode?>" />
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
	?>
	</div>
	Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</div>
</div>
<div id="mainBody">
<div class="suite1">


<h2>Bulletin Manager</h2>
<?php
if($mode==$insertMode){ 
	?><label>
	<input name="SendMail" type="checkbox" id="SendMail" value="1" checked="checked" onchange="dChge(this);" />
	Send email link to all <?php echo minroles()>=ROLE_AGENT ? 'clients' : 'people in my office';?></label>
	(can only be done now - not later on!)<br />
    <br />
    <div id="intendedFor">
	  <h4>This bulletin is intended for:</h4>
	  <?php
	  @$IncludeGroups=explode(',',$IncludeGroups);
	  ?>
	  <table cellpadding="3">
        <tr>
          <td nowrap="nowrap" style="padding:0px 25px 0px 0px;"><label>
            <input name="IncludeGroups[]" type="checkbox" id="IncludeGroups[]" value="<?php echo ROLE_CLIENT;?>" <?php echo in_array(ROLE_CLIENT, $IncludeGroups) ? 'checked':''?> onchange="dChge(this);" <?php echo minroles()>ROLE_MANAGER?'disabled':''?> />
Properties
          </label><br />
            <label>
            <input name="IncludeGroups[]" type="checkbox" id="IncludeGroups[]" value="<?php echo ROLE_AGENT;?>" <?php echo in_array(ROLE_AGENT, $IncludeGroups) ? 'checked':''?> onchange="dChge(this);" <?php echo minroles()>ROLE_AGENT?'disabled':''?> />
Agents
            </label><br />
            <label>
            <input name="IncludeGroups[]" type="checkbox" id="IncludeGroups[]" value="<?php echo ROLE_MANAGER;?>" <?php echo in_array(ROLE_MANAGER, $IncludeGroups) ? 'checked':''?> onchange="dChge(this);" <?php echo minroles()>ROLE_MANAGER?'disabled':''?> />
Managers</label>
		</td>
          <td nowrap="nowrap"><label>
            <input name="IncludeGroups[]" type="checkbox" id="IncludeGroups[]" value="<?php echo ROLE_ADMIN;?>" <?php echo in_array(ROLE_ADMIN, $IncludeGroups) ? 'checked':''?> onchange="dChge(this);" <?php echo minroles()>ROLE_MANAGER?'disabled':''?> />
Administrators
          </label><br />
            <label>
            <input name="IncludeGroups[]" type="checkbox" id="IncludeGroups[]" value="<?php echo ROLE_DBADMIN;?>" <?php echo in_array(ROLE_DBADMIN, $IncludeGroups) ? 'checked':''?> onchange="dChge(this);" <?php echo minroles()>ROLE_MANAGER?'disabled':''?> />
DB Admins </label></td>
        </tr>
      </table>
    </div>
	Include a CC to the following emails: 
	<input name="CC" type="text" class="gray" id="CC" onclick="if(this.value=='(optional, separate by commas)'){this.value='';this.className='';}else{this.value='(optional, separate by commas)';this.className='gray';}" value="(optional, separate by commas)" size="45" onchange="dChge(this);" />
	<br />
	<?php 
	if(minroles()<ROLE_FOUNDATION_DIRECTOR){
		?>
		<label><input type="checkbox" name="checkbox" value="checkbox" />
		"Sticky Bulletin" (remains in certain locations even after it has been read)</label>
		<br /><?php
	}
	?>
	<?php if($test==17 || $_SESSION['admin']['roles'][ROLE_DBADMIN]){ ?>
	<br />

	<input name="Shunt" type="checkbox" id="Shunt" value="1" onclick="if(this.checked)alert('This will send ONE sample email to the address specified to the right instead of the client or staff');" onchange="dChge(this);" />
	Send one email as a test to: 
	 <input name="ShuntEmail" type="text" id="ShuntEmail" onchange="dChge(this);" value="<?php echo $_SESSION['admin']['email']?>" size="35" />
	<br />
	<?php } ?>
	<?php
}else{
	$ip=array(
	'Clients'=> ($IncludeGroups & 2),
	'Staff'=> ($IncludeGroups & 4),
	'Staff Peers'=> ($IncludeGroups & 8),
	);
	if(array_sum($ip)){
		?>
		<p>This bulletin has been sent to: <strong><?php echo implode(', ',array_keys($ip));?></strong></p>
		<?php
	}
	if(!$SendMail){
		?>
		<p>The bulletin was <strong>not</strong> sent out by email</p>
		<?php	
	}
}
?>
		<br />
		Importance: 
		<select name="Importance" id="Importance" onchange="dChge(this);">
		  <option <?php echo $Importance=='Normal'?'selected':''?> value="Normal">Normal</option>
		  <option <?php echo $Importance=='Normal'?'selected':''?> value="High">High</option>
		  <option <?php echo $Importance=='Normal'?'selected':''?> value="Critical">Critical</option>
		  <option <?php echo $Importance=='Normal'?'selected':''?> class="gray" value="Low">(Low)</option>
        </select>
<br />
Title: <input name="Title" type="text" id="Title" onchange="dChge(this)" value="<?php echo h($Title)?>" size="128" />
<br />
Short Description: (255 characters) <br />
<textarea name="Description" cols="55" rows="2" id="Description" onchange="dChge(this)"><?php echo h($Description)?></textarea>
<input name="bl_unusername" type="hidden" id="bl_unusername" value="<?php echo $mode==$updateMode ? $bl_unusername : sun()?>" />
<br />
<strong>Content of bulletin</strong>:&nbsp;&nbsp;&nbsp;
<label>
<input name="EmailContents" type="checkbox" id="EmailContents" onchange="dChge(this);" value="1" checked="checked" />
Show this content <u>in the email itself</u></label>
<br />
<textarea cols="80" id="Contents" name="Contents" rows="10" onchange="dChge(this);"><?php
//this is easy
echo h(trim($Contents) ? $Contents : '<p></p>');
?></textarea>
<script type="text/javascript">
var editor = CKEDITOR.replace( 'Contents' );
setTimeout('CheckDirty(\'Contents\')',1000);
</script>

<br />
<br />
File attachment (limit 1 per bulletin): <input name="uploadFile1" id="uploadFile1" type="file" onchange="dChge(this);" />


</div>
</div>
<div id="footer">
&nbsp;
</div>
</form>
<?php if(!$hideCtrlSection){ ?>
<div id="showTester" title="Javascript Tester" onclick="g('tester').style.display='block';">&nbsp;</div>
<div id="tester" >
	<a href="#" onclick="g('ctrlSection').style.display='block';return false;">Show Control Section</a><br />
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
</html><?php page_end();?>