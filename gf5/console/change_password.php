<?php 
/*
Created 2010-11-24 SF

*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

if($a=q("SELECT * FROM bais_universal WHERE un_username='$un_username'", O_ROW)){
	foreach($a as $n=>$v)$a[$n]=htmlentities($v);
	@extract($a);
}else{
	exit('no username passed');
}
$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Receive Payments';?></title>



<link rel="stylesheet" type="text/css" href="/site-local/undohtml2.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
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
var isEscapable=0;
var isDeletable=0;
var isModal=0;
var talks=0; //whether this page broadcasts overall state changes which other pages listen for
var listens=0;
</script>

<style type="text/css">
</style>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
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
<input name="ID" type="hidden" id="ID" value="<?php echo $ID?>">
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
<input name="submode" type="hidden" id="submode">
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
<h3>Update Staff Information</h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>
</div>

</div>
<div id="mainBody">



<div style="width:530px;padding: 7px 8px 0px 12px;">
	<div style="float:right;width:200px;">
		<?php echo $AcctCompanyName?> database stores all passwords in an encrypted format.  If you do not know know your password, it must be reset by an <a href="mailto:<?php echo $AcctEmail?>">administrator</a>.
	</div>
	<h2>Change password for: <strong><?php echo $un_firstname . ' ' . $un_lastname?></strong></h2>
	<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>Current Password<br />
				-or-<br />
				Master Admin Key</td>
			<td valign="top"><input name="OriginalPW" type="password" id="OriginalPW"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>New Password</td>
			<td><input name="PW" type="password" id="PW"></td>
		</tr>
		<tr>
			<td>re-type Password</td>
			<td><input name="ConfirmPW" type="password" id="ConfirmPW"></td>
		</tr>
	</table>
	<?php
	if(strtolower(sun())!==strtolower($un_username)){
		?><br /><input name="update" type="checkbox" id="update" value="1" onclick="if(this.checked)alert('Caution: This will send the new password to the staff member or client via plaintext.');">
		Notify staff member or client of changed password. <br /><?php
		if(!$un_email){
			?><span style="color:darkred;font-weight:900;">User has no current email address!</span><?php
		}
	}
	?>
	<input name="mode" type="hidden" id="mode" value="changepassword">
	<input name="un_username" type="hidden" id="un_username" value="<?php echo $un_username?>">
	<p>&nbsp;</p>
	<p>
		<input type="submit" name="Submit" value=" Change Password ">
		&nbsp;&nbsp;
		<input type="button" name="Button" value="Cancel" onclick="window.close();">
	</p>
</div>



</div>
<div id="footer">
<script>
darken();
</script>
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