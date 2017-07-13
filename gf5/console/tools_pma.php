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

prn(q('delete FROM bais_universal where un_username=""', O_ARRAY));
exit;

$a=q("SELECT un_username, un_firstname, un_lastname, un_email, st_unusername, sr.* FROM bais_universal JOIN bais_staff ON un_username=st_unusername JOIN bais_StaffRoles sr ON st_unusername=sr_stusername", O_ARRAY);
prn($a);

exit;

prn(q("SELECT * FROM finan_items where resourcetoken=3101108040520079", O_ROW),1);

exit;

$tables=q("SHOW TABLES IN cpm180_hmr", O_ARRAY);
$i=0;
$skip=explode(',',$skip);

	q("drop view if exists _v_delete");
	q("drop view if exists _v_items_ebay_kay");
	q("drop view if exists _v_items_kay");

foreach($tables as $n=>$v){
	$i++;
	unset($tables[$n]);
	$t=current($v);
	$count=0;
	if(!in_array($i,$skip) && $i<=$stop)$count=q("SELECT COUNT(*) FROM `$t`",O_VALUE);
	prn($i.': '.$t.($count?'('.$count.')':''));
	#q("OPTIMIZE table `$t`");	
	if(true || $i>$stop)continue;

	$tables[$t]['name']=$t;
	$tables[$t]['count']=q("SELECT COUNT(*) FROM `$t`", O_VALUE);
	
}
?><table><thead>
<tr>
<th>Table</th>
<th>Records</th>
</tr></thead><tbody>
<?php
foreach($tables as $n=>$v){
	?><tr><td><?php echo $v['name'];?></td><td><?php echo $v['count'];?></td></tr><?php
}
?>
</tbody></table><?php
?>


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