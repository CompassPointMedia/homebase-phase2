<?php 
/*
BAIS Login (for San Marcos Area Arts Council) version 2.0 - html template 
This is improved from the GF use of BAIS Login, and locations for js and css file locations have been moved closer to those for the Ecommerce Site version 4.0
*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');




$hideCtrlSection=false;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Dataset Creator and Editor'?></title>



<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
/* local CSS styles */
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
<?php ob_start();?>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<?php
$out=ob_get_contents();
ob_end_clean();
echo str_replace('<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">','',$out);
?>

<div id="headerBar1">
	<div id="btns140" class="fr"><?php
	ob_start();
	?>
	<input id="Previous" type="button" name="Submit" value="Previous" class="navButton_A" onclick="focus_nav(-1, '<?php echo ($mode==$insertMode?'insert':'update')?>', <?php echo $mode==$insertMode?1:0?>, 0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs<=1?'disabled':''?>>
	<?php
	//Handle display of all buttons besides the Previous button
	if($mode==$insertMode){
		if($insertType==2 /** advanced mode **/){
			//save
			?><input id="Save" type="button" name="Submit" value="Save" class="navButton_A" onclick="focus_nav(0,'insert',1,2<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveInitiallyDisabled?> /><?php
		}
		//save and new - common to both modes
		?><input id="SaveAndNew" type="button" name="Submit" value="Save &amp; New" class="navButton_A" onclick="focus_nav(0,'insert', 1,1<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndNewInitiallyDisabled?> /><?php
		if($insertType==1 /** basic mode **/){
			//save and close
			?><input id="SaveAndClose" type="button" name="Submit" value="Save &amp; Close" class="navButton_A" onclick="focus_nav(0,'insert', 1,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndCloseInitiallyDisabled?> /><?php
		}
		?><input id="CancelInsert" type="button" name="Submit" value="Cancel" class="navButton_A" onclick="focus_nav_cxl('insert');" /><?php
	}else{
		//OK, and appropriate [next] button
		?><input id="OK" type="button" name="Submit" value="OK" class="navButton_A" onclick="focus_nav(0,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" />
		<input id="Next" type="button" name="Submit" value="Next" class="navButton_A" onclick="focus_nav(1,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs>$nullCount || ($denyNextToNew && $nullAbs==$nullCount) ?'disabled':''?> /><?php
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
	<span class="lg1">Dataset Creator</span>
	<br />
	Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</div>


</div>
<div id="mainBody">

<?php require('components/comp_1000_creator_v101.php');?>

</div>
<div id="footer">
&nbsp;
<?php ob_start();?>
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
</html><?php
$out=ob_get_contents();
ob_end_clean();
echo str_replace('</form>','',$out);

//this function can vary and may flush the document 
function_exists('page_end') ? page_end() : mail($developerEmail,'page end function not declared', 'File: '.__FILE__.', line: '.__LINE__,'From: '.$hdrBugs01);
?>