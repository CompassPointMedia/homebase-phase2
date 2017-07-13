<?php 
//identify this script/GUI
$localSys['scriptGroup']='';
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='1.0';
$localSys['pageType']='Properties Window';

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

$PageTitle='Product number gaps';
$suppressForm=false;
$tabVersion=3;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo dynamic_title($PageTitle.' - '.$AcctCompanyName);?></title>



<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<style type="text/css">
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="/Library/js/jquery.tabby.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
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
var isEscapable=1;
AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already
</script>



</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onSubmit="return beginSubmit();">
<?php }?>
<div id="header">

</div>
<div id="mainBody">
<div class="fr">
  <input type="button" name="Button" value="Close" onclick="window.close();" />
</div>
<h1>Part Number Gaps</h1>
<p class="gray">
This report shows SKU groups that have missing numbers in their sequence.  It does not show gaps of more than one product in a sequence.
</p>
<?php
$a=q("SELECT SKU, Category, SubCategory FROM finan_items WHERE ResourceType IS NOT NULL ORDER BY SKU", O_ARRAY);
foreach($a as $n=>$v){
	$skus[strtoupper(substr($v['SKU'],0,4))][ltrim(substr($v['SKU'],4,4),'0')]=ltrim(substr($v['SKU'],4,4),'0');
	$skuCats[strtoupper(substr($v['SKU'],0,4))][strtolower($v['Category'].':'.$v['SubCategory'])]=$v['Category'].':'.$v['SubCategory'];
}
?><table class="yat">
<thead>
<tr>
<th>SKU</th>
<th>Total#</th>
<th>Gaps</th>
<th>Categories</th>
</tr>
</thead><tbody>
<?
foreach($skus as $n=>$v){
	?><tr>
	<td><a href="report_generic.php?report=itemsquery&searchtype=SKU1&q=<?php echo $n;?>" onclick="return ow(this.href,'l1_rpt','950,500');" title="view SKU's in this group"><?php echo $n;?></a></td>
	<td class="tac"><?php echo count($v);?></td>
	<td nowrap="nowrap"><?php 
	if(end($v)==count($v)){
		echo '<em class="gray">none</em>';
	}else{
		$buffer=0;
		foreach($v as $o=>$w){
			if($w-1!=$buffer)echo '<strong>*</strong>missing '.str_pad($w-1,4,'0',STR_PAD_LEFT).'<br />';
			$buffer=$o;
		}
	}
	?></td>
	<td><?php
	echo implode('<br />',$skuCats[$n]);
	?></td>
	</tr><?php	
}
?>
</tbody>
</table>

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
</html><?php page_end();
//skip the page output
bypass:
?>