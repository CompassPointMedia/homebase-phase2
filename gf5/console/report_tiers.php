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


if(minroles()<ROLE_AGENT){
	//OK
}else{
	exit('You do not have access to this report');
}
if(!$curMonth || !$curYear){
	$d=date('Y-m');
	$a=explode('-',$d);
	$curMonth=$a[1];
	$curYear=$a[0];
}

$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Tiers for All Sales Agents - '.date('F, Y',strtotime($curYear.'-'.$curMonth.'-01'));?></title>



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

function switchMonths(o){
	var e=o.value.split('-');
	window.location='report_tiers.php?curYear='+e[0]+'&curMonth='+e[1];
}
</script>

<link rel="stylesheet" type="text/css" href="/Library/css/DHTML/rich_calendar.css">
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar_lang_en.js"></script>

<style type="text/css">
.spacer th{
	border-bottom:1px solid #333;
	background-color:cornsilk;
	}
.spacer .different{
	background-color:orange;
	border:1px solid #ccc;
	}
</style>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
<h3>Tiers for All Sales Agents</h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>
</div>

</div>
<div id="mainBody">
<h1>Commission Tiers <?php echo date('F, Y',strtotime($curYear.'-'.$curMonth.'-01'));?></h1>
<p>Select different month: 
<select name="currentMonth" id="currentMonth" onchange="switchMonths(this);">
<?php for($i=1; $i<=12; $i++){ 
$g=$i-6;
$a=date('Y-m',strtotime('NOW '.($g!=0 ? ($g>0?'+':'').$g.' MONTHS' : '')));
$a=explode('-',$a);
$thisYear=$a[0];
$thisMonth=$a[1];
?>
<option value="<?php echo $thisYear.'-'.$thisMonth?>" <?php echo $thisYear==$curYear && $thisMonth==$curMonth?'selected':''?>><?php echo date('F, Y',strtotime($thisYear.'-'.$thisMonth.'-01'));?></option>
<?php } ?>
</select>
</p>
<?php
$agents=q("SELECT un_username, un_firstname, un_lastname, un_middlename, un_email, GLF_Recruiter, GLF_TransactionFee, GLF_EOFee, t.* FROM bais_universal LEFT JOIN gl_tiers t ON t.UserName=un_username AND t.EffectiveDate='".$curYear.'-'.str_pad($curMonth,2,'0',STR_PAD_LEFT).'-01'."', bais_staff WHERE un_username=st_unusername ORDER BY un_lastname, un_firstname", O_ARRAY);
//precurse the array
?>
<input type="hidden" name="mode" id="mode" value="updateTiers" />
  <table class="spacer" width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <th rowspan="2" scope="col">Agent</th>
      <th rowspan="2" scope="col">Office</th>
      <th rowspan="2" scope="col">Recruiter</th>
      <th class="tac" colspan="3">Leasing % </th>
      <th rowspan="2" scope="col">Transaction Fee </th>
      <th rowspan="2" scope="col">E&amp;O Fee </th>
    </tr>
    <tr>
      <th>0-<?php echo number_format($tierBreakPoint1-1,0);?></th>
      <th><?php echo number_format($tierBreakPoint1,0);?>-<?php echo number_format($tierBreakPoint2-1,0);?></th>
      <th><?php echo number_format($tierBreakPoint2,0);?>+</th>
	</tr>
	<?php 
	foreach($agents as $agent){ 
	extract($agent);
	//get offices
	$a=q("SELECT oa_org1, oa_org1 FROM bais_StaffOffices, bais_orgaliases WHERE so_stusername='$un_username' AND so_unusername=oa_unusername", O_COL_ASSOC);
	$b=q("SELECT oa_org1, oa_org1 FROM bais_OfficesStaff, bais_orgaliases WHERE os_stusername='$un_username' AND os_unusername=oa_unusername", O_COL_ASSOC);
	if(!$a)$a=array();
	if(!$b)$b=array();
	$o=array_merge($a,$b);
	?>
    <tr>
      <td nowrap="nowrap"><a href="staff.php?un_username=<?php echo $un_username?>" tabindex="-1" onclick="return ow(this.href,'l1_staff','700,700');"><strong><?php echo $un_lastname . ', '.$un_firstname.($un_middlename?' '.substr($un_middlename,0,1).'.':'');?></strong></a></td>
      <td nowrap="nowrap"><?php echo @implode(', ',$o);?></td>
      <td nowrap="nowrap"><input name="GLF_Recruiter[<?php echo $un_username?>]" type="text" class="tar" value="<?php echo $GLF_Recruiter;?>" size="12" maxlength="35" onchange="dChge(this)" /></td>
      <?php
	  if(!$agent['EffectiveDate']){
	  	if($last=q("SELECT * FROM gl_tiers WHERE UserName = '$un_username' ORDER BY EffectiveDate DESC LIMIT 1", O_ROW)){
			$agent['TierAmount1']=$last['TierAmount1'];
			$agent['TierPercent1']=$last['TierPercent1'];
			$agent['TierAmount2']=$last['TierAmount2'];
			$agent['TierPercent2']=$last['TierPercent2'];
			$agent['TierAmount3']=$last['TierAmount3'];
			$agent['TierPercent3']=$last['TierPercent3'];
		}else{
			$agent['TierAmount1']=$tierBreakPoint0;
			$agent['TierPercent1']=$tierPercentage0;
			$agent['TierAmount2']=$tierBreakPoint1;
			$agent['TierPercent2']=$tierPercentage1;
			$agent['TierAmount3']=$tierBreakPoint2;
			$agent['TierPercent3']=$tierPercentage2;
		}
	  }
	  for($i=1;$i<=3;$i++){
	  	?><td>
		<input name="tier[<?php echo $un_username?>][<?php echo $i?>]" type="text" value="<?php echo floor($agent['TierPercent'.$i]*100);?>" size="2" onchange="dChge(this)" class="tar <?php echo $agent['TierPercent'.$i]!=($i==1?.5:($i==2?.6:.7))?'different':''?>" />
		</td><?php
	  }
	  ?>
      <td class="tar"><input class="tar" name="GLF_TransactionFee[<?php echo $un_username?>]" type="text" value="<?php if($GLF_TransactionFee>0)echo number_format($GLF_TransactionFee,2)?>" size="4" onchange="dChge(this)" />        </td>
      <td class="tar"><input class="tar" name="GLF_EOFee[<?php echo $un_username?>]" type="text" value="<?php if($GLF_EOFee>0)echo number_format($GLF_EOFee,2)?>" size="4" onchange="dChge(this)" /></td>
    </tr>
	<?php } ?>
  </table>
  
  <br />
  <input type="submit" name="Submit" value="Update" />
&nbsp;
<input type="button" name="Submit2" value="Close" onclick="if(detectChange && !confirm('You have made changes.  This action will cause your changes to be lost.  Continue?'))window.close()" />
</div>
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