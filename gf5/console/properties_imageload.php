<?php
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

$hideCtrlSection=false;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Upload a File</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link rel="stylesheet" type="text/css" href="/site-local/undohtml2.css" />
<link id="cssSimple" rel="stylesheet" href="/site-local/gf2_simple.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
.balloon1{
	border:1px solid darkred;
	background-color:papayawhip;
	padding:8px;
	font-weight:400;
	font-family:Georgia, "Times New Roman", Times, serif;
	}
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
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

function uploadFile(n){
	g('LocalFileName').value=n;
	g('form1').submit();
	g('status').style.display='block';
}
</script>
</head>

<body>
<div id="mainBody">
<h2>Upload a File</h2>
<p class="balloon1">Remember that depending on the size of your file, uploading can take anywhere from a few seconds to OVER AN HOUR for files of 100MB.  If you have a file over 2MB, PLEASE USE WinZip, StuffIt or another program to compress the file.  Leave this window open while the file is uploading; your file will show in the Level of Care documentation list as soon as it has been uploaded.</p>
<br /><br />
<form name="form1" id="form1" action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" target="w2">
	Choose a file from your computer:<br />
	(Upload begins as soon as you select a file)<br />
	<span id="fileElement"><input name="uploadFile_1" type="file" id="uploadFile_1" onchange="uploadFile(this.value);" /></span>
	
	&nbsp;&nbsp;
	<input name="mode" type="hidden" id="mode" value="uploadLOCFile" />
	<input  name="Children_ID" type="hidden" id="Children_ID" value="<?php echo $Children_ID?>" />
	<input  name="Childrenlocs_ID" type="hidden" id="Childrenlocs_ID" value="<?php echo $Childrenlocs_ID?>" />
	<input name="LocalFileName" type="hidden"  id="LocalFileName" value="" />
	<div id="status" style="display:none;">
		Uploading..
		<img src="/images/i/ani-progress-h-ltgreen.gif" alt="file upload in progress" width="220" height="19" /><br />
		<input type="button" name="Button" value="Cancel" onclick="if(confirm('This will cancel this upload.  Continue?'))window.close();" />	
	</div>
</form>
<p>
<br />
<br />
</p>
</div>
<?php if(!$hideCtrlSection){ ?>
<div id="ctrlSection" style="display:<?php echo $testModeC?'block':'none'?>">
	<iframe name="w1" src="/Library/js/blank.htm"></iframe>
	<iframe name="w2" src="<?php if($returnAction=='getDoc' && $document){
		//get the requested document
		echo '/index_01_exe.php?suppressPrintEnv=1&mode=getDoc&document='.$document;
	}else{
		echo '/Library/js/blank.htm';
	}?>"></iframe>
</div>
<?php } ?>
</body>
</html><?php page_end()?>