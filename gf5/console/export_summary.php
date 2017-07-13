<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

$PageTitle='Export Summary';
$suppressForm=false;

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
.bl{
border-left:1px dotted #666;
}
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="Javascript" type="text/javascript" src="/Library/js/jquery.js"></script>
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
<script language="javascript" type="text/javascript">
function toggle(o){
	var M=o.id.replace('c_','');
	$('._'+M).each(
		function(i,v){
			$(this).attr('checked',o.checked);
		}
	);
	$('.change').each(
		function(i,v){ $(this).attr('value',1); }
	);
	detectChange=1;
}
</script>
<?php
if(true)$display='marked'; // || neverexported
if($display=='marked'){
	$records=q("SELECT ID, SKU, Name, MIVA_ToBeExported, AMAZON_ToBeExported, EBAY_ToBeExported FROM finan_items WHERE ResourceType IS NOT NULL AND (MIVA_ToBeExported=1 OR Amazon_ToBeExported=1 OR Ebay_ToBeExported=1) ORDER BY SKU", O_ARRAY);
	/*
}else{
	$records=q("SELECT i.ID, i.SKU, i.Name, MIVA_ToBeExported, AMAZON_ToBeExported, EBAY_ToBeExported FROM finan_items i LEFT JOIN gen_batches_entries e ON i.ID=e.Objects_ID AND (e.ObjectName='' OR e.ObjectName='finan_items') WHERE e.ID IS NULL ORDER BY SKU", O_ARRAY);
	*/
}
?>
<input name="mode" type="hidden" id="mode" value="exportSummaryUpdate" />

<h2>Export Summary</h2>
<p class="gray">This will do a "hard" update export status for each vendor selected.  Check or uncheck records as needed, then click Submit below</p>
<!-- not using this for now and may never, delete by 10/31/2012 
Show: 
<select name="display" id="display" onchange="window.location='export_summary.php?display='+this.value;">
	<option value="display" <?php echo $display=='marked'?'selected':''?>>Records marked as needing export</option>
	<option value="neverexported" <?php echo $display=='marked'?'selected':''?>>Records never exported (no export batch on record)</option>
</select>
-->
<table id="toBeExported" class="yat">
<thead>
<tr>
	<th rowspan="2" valign="bottom">SKU</th>
	<th rowspan="2" valign="bottom">Name</th>
	<?php
	foreach($exportTypes as $n=>$v){
		?><th><?php echo strtoupper($n);?></th><?php
	}
	?>
</tr>
<tr>
	<?php
	foreach($exportTypes as $n=>$v){
		?><th class="tac" style="padding:4px;border-bottom:1px solid #000;">
		<input type="checkbox" name="checkbox" id="c_<?php echo strtoupper($n);?>" onclick="toggle(this);" title="Check/uncheck all records in this column" />
		</th><?php
	}
	?>
</tr>
</thead>
<?php
ob_start();
?>
<tbody>
<?php
if($records){
	$i=0;
	foreach($records as $n=>$v){
		extract($v);
		if($MIVA_ToBeExported)$MIVA++;
		if($AMAZON_ToBeExported)$AMAZON++;
		if($EBAY_ToBeExported)$EBAY++;
		?><tr>
			<td><a href="products.php?Items_ID=<?php echo $ID;?>" title="view/edit this record" onclick="return ow(this.href,'l1_items','800,700');">
			<input id="change<?php echo $ID;?>" name="change[<?php echo $ID;?>]" class="change" type="hidden" />
		    <?php echo $SKU;?></a></td>
			<td><?php echo $Name;?></td>
			<?php 
			$i=0;
			foreach($exportTypes as $o=>$w){ 
			$i++;
			?>
			<td class="tac" <?php echo $i>1?'class="bl"':''?> <?php echo !$v[strtoupper($o).'_ToBeExported']?'style=background-color:palevioletred;"':''?>><input name="<?php echo strtoupper($o).'_ToBeExported['.$ID.']';?>" type="hidden" value="0" />
		    <input name="<?php echo strtoupper($o).'_ToBeExported['.$ID.']';?>" type="checkbox" id="<?php echo strtoupper($o).'_ToBeExported['.$ID.']';?>" class="_<?php echo strtoupper($o);?>" value="1" onchange="dChge(this);g('change<?php echo $ID;?>').value=1;" <?php echo $v[strtoupper($o).'_ToBeExported']?'checked':''?> /></td>
			<?php } ?>
		</tr><?php
	}
}else{
	?><tr>
	<td colspan="100%"><em class="gray">No records found for that criteria</em></td>
	</tr><?php
}
?>
</tbody>
<?php
$body=ob_get_contents();
ob_end_clean();
?><tfoot>
<tr>
	<td colspan="2">TOTALS:</td>
	<?php foreach($exportTypes as $o=>$w){ ?>
	<td class="tac"><h3 style="font-family:Georgia, 'Times New Roman', Times, serif"><?php echo $GLOBALS[strtoupper($o)];?></h3></td>
	<?php } ?>
</tr>
</tfoot><?php
echo $body;
?>
</table>


<input type="submit" name="Submit" value="Submit" />
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