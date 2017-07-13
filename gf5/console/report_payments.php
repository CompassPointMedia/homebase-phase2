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


if($ReportDateFrom)$ReportDateFrom=date('Y-m-d',strtotime($ReportDateFrom));
if($ReportDateTo)$ReportDateTo=date('Y-m-d',strtotime($ReportDateTo));

$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Payment List';?></title>



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

<link rel="stylesheet" type="text/css" href="/Library/css/DHTML/rich_calendar.css">
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar_lang_en.js"></script>

<style type="text/css">
</style>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
<h3>Payment List for <?php
echo q("SELECT CONCAT(un_firstname, ' ', un_lastname) FROM bais_universal WHERE un_username='$Agent'", O_VALUE).' &mdash; '.date('n/j/Y',strtotime($ReportDateFrom)).' to '.date('n/j/Y',strtotime($ReportDateTo));
?></h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>
</div>

</div>
<div id="mainBody">
<div class="suite1">


<div id="toolbar1" class="printhide"> 
	<h3><?php
	if(minroles()<ROLE_AGENT){
		?>Agent:
		<select class="th1" name="Agent" id="Agent">
		<?php
		$agents=q("SELECT un_firstname, un_lastname, un_username, un_email FROM bais_universal, bais_staff WHERE un_username=st_unusername ORDER BY un_lastname, un_firstname" , O_ARRAY_ASSOC);
		foreach($agents as $n=>$v){
			?><option value="<?php echo $n?>" <?php echo $n==$Agent?'selected':''?>><?php echo $v['un_lastname'].', '.$v['un_firstname']?></option><?php
		}
		?>
		</select>
		<?php
	}else{
		if($Agent!==sun())exit('Agent mismatch');
		?>
		<?php echo $_SESSION['admin']['firstName'] . ' ' . $_SESSION['admin']['lastName']?>
		<input type="hidden" name="Agent" id="Agent" value="<?php echo $Agent?>" />
		<?php
	}
	?></h3>
	From: 
	<img align="absbottom" onclick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input class="th1" name="ReportDateFrom" type="text" id="ReportDateFrom" value="<?php echo date('m/d/Y',strtotime($ReportDateFrom));?>" size="14" />
  to 
	<img align="absbottom" onclick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input class="th1" name="ReportDateTo" type="text" id="textfield4" value="<?php echo date('m/d/Y',strtotime($ReportDateTo));?>" size="14" />
	
  <input class="th1" type="button" name="button2" id="button1" value="Update" onclick="g('form1').setAttribute('method','get');g('form1').setAttribute('target','');g('form1').action='';g('form1').submit();return false;" />
  &nbsp;
  <input class="th1" type="button" name="button2" id="button2" value="Print" onclick="window.print();" />
  &nbsp;
  <input class="th1" type="button" name="button2" id="button4" value="Close" onclick="window.close();" />
  &nbsp; </div>
<?php require('components/comp_71_paymentreport.php');?>
</div>
 ��</div>
<div id="footer">
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