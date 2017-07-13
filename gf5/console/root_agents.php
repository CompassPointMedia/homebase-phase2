<?php
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='generic';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');


$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Agent Control Panel - '.$AcctCompanyName;?></title>



<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />


<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle .02 coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
var ResourceToken='<?php echo $ResourceToken;?>';
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';

<?php 
//js var user settings
js_userSettings();
?>
</script>

<?php 
$link='/site-local/gl_extension_'.$GCUserName.'.css';
if(file_exists($_SERVER['DOCUMENT_ROOT'].$link)){ ?>
<link id="cssExtension" rel="stylesheet" type="text/css" href="<?php echo $link?>" />
<?php } ?>
</head>

<body>
<div id="mainWrap">

	<?php require($_SERVER['DOCUMENT_ROOT'].'/gf5/console/gf_header_login_002.php');?>

	
	<div id="mainBody">

<?php

//day ranges for this month and previous month
$currentMonth=date('Y-m-t',strtotime('today'));
$lastMonth=date('Y-m-t',strtotime('last month'));

$agentList=q("SELECT u.un_username, CONCAT(u.un_lastname,', ',u.un_firstname) AS Name, -SUM(OriginalTotal) AS Total, COUNT(i.ID) AS Count FROM bais_universal u LEFT JOIN _v_y_finan_invoices_mapsahead i ON i.Agents_username=u.un_username AND AmountApplied + OriginalTotal < 0, bais_staff s WHERE u.un_username=s.st_unusername GROUP BY u.un_username ORDER BY un_lastname, un_firstname", O_ARRAY_ASSOC);
if($test==1)prn($qr);
foreach($agentList as $n=>$v){
	$agents[]=$n;
}
$lastMonthSales=q("SELECT Agent, SUM(PaymentCount) AS Count, SUM(AmountApplied) AS Sum
FROM 
_v_mapsahead_agents_checks_commission 
WHERE
PaymentDate BETWEEN '".substr($lastMonth,0,8).'01'."' AND '$lastMonth' GROUP BY Agent", O_ARRAY_ASSOC);

$currentMonthSales=q("SELECT Agent, SUM(PaymentCount) AS Count, SUM(AmountApplied) AS Sum
FROM 
_v_mapsahead_agents_checks_commission 
WHERE
PaymentDate BETWEEN '".substr($currentMonth,0,8).'01'."' AND '$currentMonth' GROUP BY Agent", O_ARRAY_ASSOC);
?>

	<h1>Agent Control Panel</h1>
	<p>Agent Summary Info&nbsp;&nbsp;<a onclick="return ow(this.href,'l1_tiers','800,700');" href="report_tiers.php">Agent Tiers</a> </p>

	<table class="data0" width="100%" border="0" cellspacing="0">
	<thead>
      <tr>
        <th rowspan="2">Agent Name </th>
        <th colspan="2" rowspan="2"><span title="Outstanding invoices for this agent">OSI</span></th>
        <th colspan="2">checks previous month</th>
        <th colspan="2">checks month-to-date</th>
        <th colspan="2">splits month-to-date</th>
        <th rowspan="2">payments</th>
        <th rowspan="2">&nbsp;</th>
      </tr>
      <tr>

        <th>count</th>
        <th>total</th>
        <th>count</th>
        <th>total</th>
        <th>count</th>
        <th>total</th>
        </tr>
	<thead>
	<tbody>
	<?php
	$i=0;
	foreach($agentList as $n=>$v){
		$i++;
		?><tr class="<?php echo !fmod($i,2)?'alt':''?>">
			<td><a href="staff.php?un_username=<?php echo $n?>" title="Edit this member's information" onclick="return ow(this.href,'l1_staff','700,700');"><strong><?php echo $v['Name']?></strong></a></td>
			<td nowrap="nowrap" class="tac">
			<?php 
			if($v['Total']){
				?>(<?php echo $v['Count']?>)<?php
			}
			?>			</td>
			<td nowrap="nowrap" class="tar">
			<?php
			if($v['Total']){
				?><a href="agents_osi.php?Agents_username=<?php echo $n?>" title="View outstanding invoices for this agent" onclick="return ow(this.href,'l1_agentosi','850,500');"><?php echo number_format($v['Total'],2)?></a><?php
			}else{
				?><em>(none)</em><?php
			}
			?>			</td>
			<td class="tac"><?php
			if($lastMonthSales[$n]['Count']){
				?>
				<a href="report_payments.php?ReportDateFrom=<?php echo substr($lastMonth,0,8).'01';?>&ReportDateTo=<?php echo $lastMonth?>&Agent=<?php echo $n?>" title="view report of these payments" onclick="return ow(this.href,'l2_agentpayments','700,700');"><?php echo $lastMonthSales[$n]['Count']?></a>
				<?php
			}else{
				?><span class="ghost">0</span><?php
			}
			?>			</td>
			<td class="tar"><?php
			if($lastMonthSales[$n]['Count']){
				?>
				<a href="report_payments.php?ReportDateFrom=<?php echo substr($lastMonth,0,8).'01';?>&ReportDateTo=<?php echo $lastMonth?>&Agent=<?php echo $n?>" title="view report of these payments" onclick="return ow(this.href,'l2_agentpayments','700,700');"><?php echo number_format($lastMonthSales[$n]['Sum'],2);?></a>
				<?php
			}else{
				?><span class="ghost">0.00</span><?php
			}
			?>			</td>


			<td class="tac"><?php
			if($currentMonthSales[$n]['Count']){
				?>
				<a href="report_payments.php?ReportDateFrom=<?php echo substr($currentMonth,0,8).'01';?>&ReportDateTo=<?php echo $currentMonth?>&Agent=<?php echo $n?>" title="view report of these payments" onclick="return ow(this.href,'l2_agentpayments','700,700');"><?php echo $currentMonthSales[$n]['Count']?></a>
				<?php
			}else{
				?><span class="ghost">0</span><?php
			}
			?>			</td>
			<td class="tar"><?php
			if($currentMonthSales[$n]['Count']){
				?>
				<a href="report_payments.php?ReportDateFrom=<?php echo substr($currentMonth,0,8).'01';?>&ReportDateTo=<?php echo $currentMonth?>&Agent=<?php echo $n?>" title="view report of these payments" onclick="return ow(this.href,'l2_agentpayments','700,700');"><?php echo number_format($currentMonthSales[$n]['Sum'],2);?></a>
				<?php
			}else{
				?><span class="ghost">0.00</span><?php
			}
			?>			</td>

			<td class="tac">&nbsp;</td>
			<td class="tar">&nbsp;</td>
			<td class="tar">&nbsp;</td>
			<td><a href="agentchecks.php?Agents_username=<?php echo $n?>" onclick="return ow(this.href,'l1_agentchecks','850,500');">pay commission</a></td>
		</tr><?php
	}
	?><tr>
		<td>&nbsp;</td>
		<td colspan="2">&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</tbody>
    </table>



	</div>
	<div id="footer">
	<div id="footer">
<p>Home Base&reg; Management DB ver. 5.0 - All rights reserved &copy;2006-<?php echo date('Y');?></p>
</div>

	</div>
	<?php if(!$hideCtrlSection){ ?>
	<div id="showTester" title="Javascript Tester" onclick="g('tester').style.display='block';">&nbsp;</div>
	<div id="tester" >
		<a href="#" onclick="g('ctrlSection').style.display=op[g('ctrlSection').style.display]; return false;">iframes</a><br />
		<textarea name="test" cols="65" rows="4" id="test">g('field').value</textarea><br />
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
</div>
</body>
</html><?php page_end()?>