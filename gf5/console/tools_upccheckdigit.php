<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo dynamic_title($PageTitle.' - '.$AcctCompanyName);?></title>



<link id="cssUndoHTML" rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="../../site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="../../Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
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
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';

var isEscapable=2;

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already
</script>

</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onSubmit="return beginSubmit();">
<?php }?>
<div id="header">
&nbsp;

</div>
<div id="mainBody">

<?php
if($merchantUPCCode=q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='items' AND varnode='settings' AND varkey='merchantUPCCode'", O_VALUE)){
	//ok
	?><h3>Merchant code: <?php echo $merchantUPCCode;?></h3>
	<p class="gray">first four digits represent system id; next 10 digits represent check digits for "product types" 0 through 9.  SKU is also to the right for a reference; finally in parenthesis is a 1 or 0, depending on whether the update actually changed any value for this field</p>
	<?php
}else{
	exit('no merchant upc code available');
}
foreach($a=q("SELECT ID, SKU FROM finan_items WHERE ResourceType IS NOT NULL", O_COL_ASSOC) as $ID=>$SKU){
	$str='';
	for($i=0;$i<=9;$i++){
		$str.=UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). $i, array('return'=>'digit'));
	}
	q("UPDATE finan_items SET HMR_UPCCheckDigit='$str' WHERE ID=$ID");
	prn(str_pad($ID,4,'0',STR_PAD_LEFT).':'.$str.' - '.$SKU. ' ('.$qr['affected_rows'].')');
}

?>
<h3>All UPC check digits have been successfully processed (<?php echo count($a);?>)</h3>
<input type="button" name="Close" value="Close" onclick="window.close();" />


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
</html><?php page_end();?>