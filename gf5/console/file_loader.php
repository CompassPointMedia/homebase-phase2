<?php
//note the introduction of the new _cb_ cognate; this is stripped but cbPresent is NOT set
//_cb_Children_ID=$Children_ID&CategoryGroup=ChildDocumentationCategories[&Category=Admission]


//[optional parameters]

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
<title><?php echo $PageTitle='File loader';?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link id="cssUndoHTML" rel="stylesheet" href="/site-local/undohtml2.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
/* local CSS styles */
</style>

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

function uploadFile(n){
	g('LocalFileName').value=n;
	g('form1').submit();
	g('status').style.display='block';
}
</script>
</head>

<body <?php if($UserAgentType=='Mobile')echo 'class="mobile"';?>>
<div id="mainBody">
<h2>Upload a File</h2>
<p class="balloon1">Remember that depending on the size of your file, uploading can take anywhere from a few seconds to OVER AN HOUR for files of 100MB.  Leave this window open while the file is uploading; your file will show in the  file list as soon as it has been uploaded.</p>
<br />
<form name="form1" id="form1" action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" target="w2">
	<input name="mode" type="hidden" id="mode" value="<?php echo $mode?$mode:'uploadFile';?>" />
	<input name="submode" type="hidden" id="submode" value="<?php echo $submode?>" />

	<?php
	if(count($_REQUEST)){
		foreach($_REQUEST as $n=>$v){
			if(substr($n,0,2)!=='cb' && substr($n,0,4)!=='_cb_')continue;
			if(!$setCBPresent && substr($n,0,2)=='cb'){
				$setCBPresent=true;
				?><!-- callback fields automatically generated --><?php
				echo "\n";
				?><input name="cbPresent" id="cbPresent" value="1" type="hidden" /><?php
				echo "\n";
			}
			if(is_array($v)){
				foreach($v as $o=>$w){
					echo "\t\t";
					?><input name="<?php echo str_replace('_cb_','',$n);?>[<?php echo is_numeric($o)? '': $o?>]" id="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" type="hidden" value="<?php echo stripslashes($w)?>" /><?php
					echo "\n";
				}
			}else{
				echo "\t\t";
				?><input name="<?php echo str_replace('_cb_','',$n);?>" id="<?php echo str_replace('_cb_','',$n);?>" type="hidden" value="<?php echo stripslashes($v)?>" /><?php
				echo "\n";
			}
		}
	}
	?>
	<?php
	if($a=q("SELECT VarKey FROM `bais_settings` WHERE username='{system}' AND VarNode='$CategoryGroup'", O_COL)){
		?>
		Category: 
		<select name="Category" id="Category">
		<option value="">&lt;Select..&gt;</option>
		<?php
		foreach($a as $v){
			?><option value="<?php echo $v?>" <?php echo preg_replace('/^[0-9]+ - /','',$Category)==preg_replace('/^[0-9]+ - /','',$v)?'selected':''?>><?php echo h(preg_replace('/^[0-9]+ - /','',$v));?></option><?php
		}
		?></select>
		<br />
		<?php
	}
	?>
	<h2>Choose a file from your computer:</h2>
	
	<em class="gray">(Upload begins as soon as you select a file)</em><br />
	<span id="uploadFileWrap"><input name="uploadFile1" type="file" id="uploadFile1" onchange="uploadFile(this.value);" /></span>
	
	&nbsp;&nbsp;
	<input name="LocalFileName" type="hidden"  id="LocalFileName" value="" />
	<div id="status" style="display:none;">
		Uploading..
		<img src="/images/i/ani-gif-bars-ltgreen.gif" alt="file upload in progress" width="220" height="19" /><br />
		<input type="button" name="Button" value="Cancel" onclick="if(confirm('This will cancel this upload.  Continue?'))window.close();" />	
	</div>
</form>
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