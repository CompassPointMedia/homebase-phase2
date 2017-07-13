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


/*
2012-03-16 todo this report
DONE	client showing and linkable
DONE	status showing
DONE	move-in date showing
DONE	asterisk* for invoices entered after move-in date (red flag - get agents to stop this :)
other invoices (to me)minimally showing
links clickable to a larger report concept of invoices on a light-content report, perhaps Leases_ID=14835,1483,4899,.. etc.
	- alternately, clicking highlights the invoices below in that category
	
contact clickable and hook into system
Agent split not working?
see what other fields were in the old db reports that need in there
subtotals at bottom for some columns
*/

//assets to get started
if($ReportDateFrom){
	//allow any date
	$ReportDateFrom=date('Y-m-d',strtotime($ReportDateFrom));
}else{
	$ReportDateFrom=date('Y-m',strtotime('now -1 month')).'-01';
}
if($ReportDateTo){
	$ReportDateTo=date('Y-m-d',strtotime($ReportDateTo));
}else{
	$ReportDateTo=date('Y-m-t',strtotime('now -1 month'));
}

if(minroles()>ROLE_MANAGER)exit('You do not have access to this report');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo ($PageTitle='Period-to-Date Office Report - '.$AcctCompanyName);?></title>



<link rel="stylesheet" type="text/css" href="/Library/css/cssreset01.css" />
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

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
<h3>Period-to-Date Office Report</h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>

	<div id="toolbar1" class="printhide">
		Show Invoices 
		  <select name="targetDate" id="targetDate">
		    <option value="CreateDate" <?php echo $targetDate=='CreateDate'?'selected':''?>>entered</option>
	      </select>
	    from: <img align="absbottom" onClick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input name="ReportDateFrom" type="text" id="ReportDateFrom" value="<?php echo date('m/d/Y',strtotime($ReportDateFrom));?>" size="14" />
		to
		<img align="absbottom" onClick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input name="ReportDateTo" type="text" id="textfield4" value="<?php echo date('m/d/Y',strtotime($ReportDateTo));?>" size="14" />

		<input type="button" name="button" id="button1" value="Update" onClick="g('form1').setAttribute('method','get');g('form1').setAttribute('target','');g('form1').action='';g('form1').submit();return false;" /> &nbsp;
		<input type="button" name="button" id="button2" value="Print" onClick="window.print();" /> 
		&nbsp;
		<input type="button" name="button" id="button4" value="Close" onClick="window.close();" />&nbsp;	
	  </div>
	<div class="screenhide">
	<h2>Report date from <?php echo date('m/d/Y',strtotime($ReportDateFrom));?> to <?php echo date('m/d/Y',strtotime($ReportDateTo));?></h2>
	</div>
</div>

</div>
<div id="mainBody">
<div class="suite1">

<?php
if($rd=q("SELECT i.*, l.Agents_UserName, l.SubAgents_UserName, l.LeaseStartDate, l.SubSplit, GiftCard1 + GiftCard2 AS GiftCard, lt.Leases_ID,
	GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) AS DateDueFrom,
	l.Units_ID,
	GLF_LateStatus,
	GLF_LateCharge,
	HeaderStatus,
	GLF_DiscrepancyDate,
	GLF_DiscrepancyReason,
	IF(u.un_username IS NOT NULL, CONCAT(u.un_lastname,', ',u.un_firstname), l.Agents_UserName) AS Agent,
	IF(u2.un_username IS NOT NULL, CONCAT(u2.un_lastname,', ',u2.un_firstname), l.SubAgents_UserName) AS SubAgent,
	COUNT(lb.Batches_ID) AS Billed
	FROM
	_v_x_finan_headers_master i, 
	gl_LeasesTransactions lt, 
	gl_leases l LEFT JOIN gl_LeasesBatches lb ON l.ID=lb.Leases_ID
	LEFT JOIN bais_universal u ON l.Agents_UserName = u.un_username
	LEFT JOIN bais_universal u2 ON l.SubAgents_UserName = u2.un_username
	WHERE i.Transactions_ID=lt.Transactions_ID AND 
	lt.Leases_ID=l.ID AND 
	i.CreateDate BETWEEN '$ReportDateFrom' AND '$ReportDateTo'
	GROUP BY i.ID
	", O_ARRAY)){
	foreach($rd as $n=>$v){
		extract($v);
		$totalAmountInvoiced+= -$OriginalTotal;
		$totalAgentSplit+=($SubSplit>2.0 ? $SubSplit : (-$OriginalTotal * .5 * $SubSplit));
		$totalGiftCard+=$GiftCard;
		$Quantity++;
		if($Billed)$AmountBilled+= -$OriginalTotal;
		$AmountPaid+= $AmountApplied;
		$data[$Agent]['UserName']=$Agents_UserName;
		$data[$Agent]['AmountInvoiced']+= -$OriginalTotal;
		$data[$Agent]['Quantity']++;
		if($SubSplit>0)$data[$Agent]['Split']+= ($SubSplit>2.0 ? $SubSplit : ( -$OriginalTotal * .5 * $SubSplit));
		if($GiftCard>0)$data[$Agent]['GiftCard']+=$GiftCard;
		if($SubAgent)$data[$SubAgent]['SplitTo']+= ($SubSplit>2.0 ? $SubSplit : ( -$OriginalTotal * .5 * $SubSplit));
	}
	ksort($data);
}
?>
<h3 class="fr"><?php echo date('n/j/Y');?></h3>
<h2><?php echo strtoupper($AcctCompanyName)?> INVOICING REPORT</h2>
Period <strong><?php echo date('n/j/Y',strtotime($ReportDateFrom));?> through <?php echo date('n/j/Y',strtotime($ReportDateTo));?></strong><br />
<span class="gray">NOTE: Totals shown represent invoices <u>created</u> during this time period</span><br />
<br />

Report type: <strong>Office</strong> <br />
<br />
<table class="spacer">
  <tr>
	<td>Total Amount Invoiced: </td>
	<td class="tr"><strong><?php echo number_format($totalAmountInvoiced,2);?></strong></td>
	<td>Average Amount of Invoice: </td>
	<td class="tr"><strong><?php if($Quantity) echo number_format($totalAmountInvoiced / $Quantity, 2);?></strong></td>
  </tr>
  <tr>
	<td>Total Number of Invoices: </td>
	<td class="tr"><strong><?php echo $Quantity;?><span style="visibility:hidden;">.00</span></strong></td>
	<td>Average Number of Invoices/Contributing: </td>
	<td class="tr"><?php if(count($data)) echo number_format(round($Quantity / count($data),2),2);?></td>
  </tr>
  <tr>
	<td>Total Agent Split <sup>1</sup></td>
	<td class="tr"><?php echo number_format($totalAgentSplit,2);?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
  </tr>
  <tr>
	<td>Total Gift Card Expenses: </td>
	<td class="tr"><?php echo number_format($totalGiftCard,2);?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
  </tr>
  <tr>
    <td>Amount in Invoices Billed: </td>
	<td class="tr"><?php echo number_format($AmountBilled,2);?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Amount in Invoices Unbilled: </td>
	<td class="tr"><?php echo number_format($totalAmountInvoiced - $AmountBilled,2);?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Amount in Invoices Paid: </td>
	<td class="tr"><?php echo number_format($AmountPaid,2);?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Amount in Invoices Unpaid: </td>
	<td class="tr"><?php echo number_format($totalAmountInvoiced - $AmountPaid,2);?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<sup>1</sup><span class="gray">(based on 50% agent commission)</span><br />
<br />
<?php
if($data){
	?><table class="spacer">
	<thead>
	<tr>
	<th>Agent Name </th>
	<th>Amount Invoiced </th>
	<th>Nbr. Invoices </th>
	<th>Total Agent Split </th>
	<th>Total Split To </th>
	<th>Total Gift Card Expense </th>
	</tr>
	</thead><?php
	$i=0;
	foreach($data as $Agent=>$v){
		$i++;
		?><tr>
		<td><a href="staff.php?un_username=<?php echo $v['UserName'];?>" title="view/edit this agent info" onclick="return ow(this.href,'l1_staff','700,700');"><?php echo $Agent;?></a></td>
		<td class="tr"><a href="report_agentreport.php?targetDate=CreateDate&ReportDateFrom=<?php echo $ReportDateFrom;?>&ReportDateTo=<?php echo $ReportDateTo;?>&Agents_UserName=<?php echo $v['UserName'];?>" title="View specific agent report for this same date period"><?php echo number_format($v['AmountInvoiced'],2);?></a></td>
		<td class="tc"><?php echo $v['Quantity'];?></td>
		<td class="tr"><?php echo number_format($v['Split'],2);?></td>
		<td class="tr"><?php echo number_format($v['SplitTo'],2);?></td>
		<td class="tr"><?php echo number_format($v['GiftCard'],2);?></td>
		</tr><?php
	}
	if($i){
		?><tr>
		<td>&nbsp;</td>
		<td class="tr"><h3>$<?php echo number_format($totalAmountInvoiced,2);?></h3></td>
		<td class="tc"><h3><?php echo $Quantity;?></h3></td>
		<td class="tr"><h3><?php echo number_format($totalAgentSplit,2);?></h3></td>
		<td class="tr"><h3>&nbsp;</h3></td>
		<td class="tr"><h3><?php echo number_format($totalGiftCard,2);?></h3></td>
		</tr><?php
	}
	?></table>
<?php
}
?>

</div>
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