<?php 
/*
*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

if(minroles()>ROLE_AGENT)exit('You do not have access to this');


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='List Texts - '.$AcctCompanyName?></title>



<link id="cssUndoHTML" rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="../../site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="../../Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
textarea {
    background-color: #F4EEDF;
    border: 1px solid #999999;
    padding: 5px;
	}
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
function useThis(n){
	window.opener.g('<?php echo $field;?>').value=g('r_'+n).innerHTML;
	window.close();
	return false;
}
</script>


</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onSubmit="return beginSubmit();">
<?php }?>
<div id="header">
<?php
$a=q("SELECT
TRIM($field) AS $field, MAX(EditDate) AS LastUsed, COUNT(*) AS TotalUsed FROM finan_items WHERE $field!=''
GROUP BY TRIM($field) ORDER BY MAX(EditDate) DESC", O_ARRAY);
?>
&nbsp;

<div id="btns140" class="fr">
  <input name="insertMode" type="hidden" id="insertMode" value="<?php echo $insertMode?>" />
  <input name="updateMode" type="hidden" id="updateMode" value="<?php echo $updateMode?>" />
  <input name="deleteMode" type="hidden" id="deleteMode" value="<?php echo $deleteMode?>" />
  <input name="mode" type="hidden" id="mode" value="<?php echo $mode?>" />
  <?php
if(count($_REQUEST)){
	foreach($_REQUEST as $n=>$v){
		if(substr($n,0,2)=='cb'){
			if(!$setCBPresent){
				$setCBPresent=true;
				?>
  <!-- callback fields automatically generated -->
  <?php
				echo "\n";
				?>
  <input name="cbPresent" id="cbPresent" value="1" type="hidden" />
  <?php
				echo "\n";
			}
			if(is_array($v)){
				foreach($v as $o=>$w){
					echo "\t\t";
					?>
  <input name="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" id="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" type="hidden" value="<?php echo stripslashes($w)?>" />
  <?php
					echo "\n";
				}
			}else{
				echo "\t\t";
				?>
  <input name="<?php echo $n?>" id="<?php echo $n?>" type="hidden" value="<?php echo stripslashes($v)?>" />
  <?php
				echo "\n";
			}
		}
	}
}
?>
</div>
</div>
<div id="mainBody">
<h1>Text Field: <?php echo $field;?></h1>

<table class="yat">
<thead>
<tr>
	<th>&nbsp;</th>
	<th>Text</th>
	<th>Times Used</th>
	<th>Last Updated</th>
</tr>
</thead>
<tbody>
<?php
if($a){
	$i=0;
	foreach($a as $n=>$v){
		extract($v);
		$i++;
		$j++;
		?><tr class="<?php echo !fmod($j,2)?'alt':''?>">
			<td>[<a href="#" onclick="return useThis(<?php echo $j?>);">use this</a>]</td>
			<td id="r_<?php echo $j?>"><?php echo trim($$field);?></td>
			<td><?php echo $TotalUsed;?></td>
			<td><?php echo date('n/j/Y \a\t g:iA',strtotime($LastUsed));?></td>
		</tr><?php
	}
}else{
	?><tr>
	<td colspan="100%"><em class="gray">No records found for that criteria</em></td>
	</tr><?php
}
?>
</tbody>
</table>



<input type="button" name="Button" value="Close" onClick="window.close()" />
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