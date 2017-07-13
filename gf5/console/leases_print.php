<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

if($key){
	if(!($_POST=$_SESSION['special']['print'][$key]))exit('unable to locate request');
	extract($_POST);
	if(!($data=q("SELECT * FROM _v_leases_master WHERE ID IN(".implode(',',array_keys($print)).")", O_ARRAY_ASSOC)))
	exit('unable to locate the requested invoice(s)');
}else if(!($data=q("SELECT * FROM _v_leases_master WHERE ID='$Leases_ID'", O_ARRAY_ASSOC)))exit('Unable to locate the invoice id');
$hideCtrlSection=true;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Print Invoice';?></title>



<link id="cssUndoHTML" rel="stylesheet" href="../../site-local/undohtml2.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="../../site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="../../Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
/* local CSS styles */
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="../../site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var isEscapable=1;
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already
</script>


</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onsubmit="return beginSubmit();">
<?php }?>
<div id="header"></div>
<div id="mainBody">
<div id="toolbar1" class="printhide">
<?php if(minroles()<ROLE_CLIENT){ ?>
<p>
    <input class="th1b" type="button" name="Button" value="Print Invoice<?php echo count($data)>1?'s':''?>" onclick="window.print();" />&nbsp;
	<?php if($mode!='updateBillingSent'){ ?>
    <input class="th1b" type="button" name="Submit2" value="Edit This Lease" onclick="window.location='leases.php?Leases_ID=<?php echo $Leases_ID?>';" />&nbsp;
    <input class="th1b" type="button" name="Submit3" value="Add Another Lease" onclick="window.location='leases.php';" />&nbsp;
	<?php } ?>
    <input class="th1b" type="button" name="Submit4" value="Close" onclick="window.close();" />
</p>
<?php } ?>
</div>
<?php
require('components/comp_259_invoiceprint.php');
?>
</div>
<div id="footer">
&nbsp;&nbsp;
</div>
<?php if(!$suppressForm){ ?>
</form>
<?php }?>
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