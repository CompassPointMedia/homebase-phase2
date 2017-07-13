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
if(!$targetDate)$targetDate='CreateDate';
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

if(minroles()>ROLE_AGENT)exit('You do not have access to this report');
if(minroles()==ROLE_AGENT){
	$Agents_UserName=sun();
}else{
	ob_start();
	$a=q("SELECT un_username Agents_UserName, un_firstname FirstName, un_middlename MiddleName, un_lastname LastName, un_email Email FROM bais_universal u, bais_staff s WHERE un_username=st_unusername AND st_active=1 AND (st_dischargedate>CURDATE() OR !st_dischargedate) ORDER BY un_lastname, un_firstname", O_ARRAY_ASSOC);
	?>
	<script language="javascript" type="text/javascript">
	function selectAgent(o){
		var l=window.location+'';
		if(l.indexOf('?')==-1){
			l+='?';
		}else{
			l=l.replace(/&$/,'');
			l+='&';
		}
		l+='Agents_UserName='+o.value
		window.location=l;
	}
	</script>
	<select name="Agents_UserName" id="Agents_UserName" onChange="selectAgent(this);">
	<option value="">&lt;Select..&gt;</option>
	<?php
	foreach($a as $n=>$v){
		?><option value="<?php echo $n;?>" <?php echo $Agents_UserName==$n?'selected':'';?>><?php echo $v['LastName'].', '.$v['FirstName'];?></option><?php
	}
	?>
	</select>
	<?php
	$selectAgent=ob_get_contents();
	ob_end_clean();

	if(!$Agents_UserName){
		?>
		<h1>Agent Report</h1>
		<p>Select an agent from the list:</p>
		<?php
		echo $selectAgent;
		exit;
	}
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo ($PageTitle='Agent Payment Report - '.$AcctCompanyName);?></title>



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
<h3>Agent Report</h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>

	<div id="toolbar1" class="printhide">
		Show Invoices 
		  <select name="targetDate" id="targetDate">
		    <option value="CreateDate" <?php echo $targetDate=='CreateDate'?'selected':''?>>entered</option>
		    <option value="LeaseSignDate" <?php echo $targetDate=='LeaseSignDate'?'selected':''?>>signed on</option>
		    <option value="LeaseStartDate" <?php echo $targetDate=='LeaseStartDate'?'selected':''?>>move-in date</option>
	      </select>
	    from: <img align="absbottom" onClick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input name="ReportDateFrom" type="text" id="ReportDateFrom" value="<?php echo date('m/d/Y',strtotime($ReportDateFrom));?>" size="14" />
		to
		<img align="absbottom" onClick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input name="ReportDateTo" type="text" id="textfield4" value="<?php echo date('m/d/Y',strtotime($ReportDateTo));?>" size="14" />

		<input type="button" name="button" id="button1" value="Update" onClick="g('form1').setAttribute('method','get');g('form1').setAttribute('target','');g('form1').action='';g('form1').submit();return false;" /> &nbsp;
		<input type="button" name="button" id="button2" value="Print" onClick="window.print();" /> &nbsp;
		<input type="button" name="button" id="button3" value="Export" onClick="window.open('resources/bais_01_exe.php?mode=refreshComponent&component=aparcombined&suppressPrintEnv=1&submode=exportDataset&ReportDateFrom=<?php echo $ReportDateFrom;?>&ReportDateTo=<?php echo $ReportDateTo?>','w2');" /> &nbsp;
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
	if($rd=q("SELECT i.*, l.Agents_UserName, l.SubAgents_UserName, l.LeaseStartDate, l.SubSplit, GiftCard1 + GiftCard2 AS GiftCard, lt.Leases_ID , pu.Properties_ID,
		GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) AS DateDueFrom,
		c.ID AS Contacts_ID,
		c.FirstName, c.LastName,
		l.Units_ID,
		pu.Properties_ID,
		p.PropertyName,

		GLF_LateStatus,
		GLF_LateCharge,
		HeaderStatus,
		GLF_DiscrepancyDate,
		GLF_DiscrepancyReason

	
		FROM
		_v_x_finan_headers_master i, 
		gl_LeasesTransactions lt, 
		gl_leases l LEFT JOIN gl_LeasesContacts lc ON l.ID=lc.Leases_ID AND lc.Type='Primary' LEFT JOIN addr_contacts c ON lc.Contacts_ID=c.ID,
		gl_properties_units pu,
		gl_properties p
		 WHERE i.Transactions_ID=lt.Transactions_ID AND lt.Leases_ID=l.ID AND l.Units_ID=pu.ID AND pu.Properties_ID=p.ID AND '$Agents_UserName' IN (Agents_UserName, SubAgents_UserName) AND l.$targetDate BETWEEN '$ReportDateFrom' AND '$ReportDateTo' ORDER BY $targetDate", O_ARRAY)){
		foreach($rd as $n=>$v){
			//get amounts invoiced and not, and payments made
			($bh=q("SELECT COUNT(*) AS billed, MAX(EditDate) AS LastBilled FROM gl_LeasesBatches WHERE Leases_ID='".$v['Leases_ID']."'", O_ROW));
			$b=($bh['billed'] ? 'billed' : 'unbilled');
			$rd[$n]['billed']=$bh['billed'];
			$rd[$n]['LastBilled']=$bh['LastBilled'];
			
			$Properties[$v['Properties_ID']]=$v['Properties_ID'];
			
			if(strtolower($v['Agents_UserName'])==strtolower($Agents_UserName)){
				$total['total']['OriginalTotal'][$n]=abs($v['OriginalTotal']);
				$total['total']['AmountApplied'][$n]=$v['AmountApplied'];
				$total['total']['GiftCard'][$n]=$v['GiftCard'];
				$total[$b]['OriginalTotal'][$n]=abs($v['OriginalTotal']);
				$total[$b]['AmountApplied'][$n]=$v['AmountApplied'];
				$total[$b]['GiftCard'][$n]=$v['GiftCard'];
				if($v['SubAgents_UserName']){
					$total['total']['Split'][$n]= ($v['SubSplit']>1 ? $v['SubSplit'] : abs($v['OriginalTotal']) * .5 * $v['SubSplit']);
					$total[$b]['Split'][$n]= ($v['SubSplit']>1 ? $v['SubSplit'] : abs($v['OriginalTotal']) * .5 * $v['SubSplit']);
				}else{
					
				}
			}else{
				//this is a split to the agent
				$total['splittotal']['OriginalTotal'][$n]=abs($v['OriginalTotal']);
				$total['splittotal']['AmountApplied'][$n]=$v['AmountApplied'];
				$total['split'.$b]['OriginalTotal'][$n]=abs($v['OriginalTotal']);
				$total['split'.$b]['AmountApplied'][$n]=$v['AmountApplied'];
				$total['splittotal']['Split'][$n]= ($v['SubSplit']>1 ? $v['SubSplit'] : abs($v['OriginalTotal']) * .5 * $v['SubSplit']);
				$total['split'.$b]['Split'][$n]= ($v['SubSplit']>1 ? $v['SubSplit'] : abs($v['OriginalTotal']) * .5 * $v['SubSplit']);
			}
			
			
			//# of payments?
		}
	}
	?>
	
	<h2><?php echo strtoupper($AcctCompanyName)?> INVOICING REPORT</h2>
	For invoices <?php echo $targetDate=='CreateDate' ? 'entered' : ($targetDate=='LeaseStartDate' ? 'with a move-in date' : 'signed');?> between <?php echo date('n/j/Y',strtotime($ReportDateFrom));?> and <?php echo date('n/j/Y',strtotime($ReportDateTo));?><br />
	Report type: Agent<br />
	Agent: <?php
	if(minroles()==ROLE_AGENT){
		echo $_SESSION['admin']['firstName'] . ' ' . $_SESSION['admin']['lastName'];
	}else{
		//all agents I have access to
		echo $selectAgent;
	}
	?>
	<style type="text/css">
	</style>
  <table width="100%">
    <tr>
      <th>My Invoices </th>
      <th class="tar">Total</th>
      <th class="tar">#</th>
      <th class="tar">Payments On </th>
      <th class="tar">Balance</th>
      <th class="tar">Agent Split<a href="#note1"><sup class="basic">1</sup></a></th>
      <th class="tar">Gift Cards</th>
    </tr>
    <tr>
      <th>Total Invoiced </th>
      <td class="tar" title="represents total invoices you have created during this period"><?php echo number_format($tAll=@array_sum($total['total']['OriginalTotal']),2);?></td>
      <td class="tar"><?php echo count($total['total']['OriginalTotal']);?></td>
      <td class="tar" title="represents all payments that have been applied to the invoices listed"><?php echo number_format($p[]=@array_sum($total['total']['AmountApplied']),2);?></td>
      <td class="tar" title="represents outstanding monies due from the invoices listed"><?php echo number_format($tAll - $p[0],2);?></td>
      <td class="tar"><?php echo number_format(@array_sum($total['total']['Split']),2);?></td>
      <td class="tar"><?php echo number_format(@array_sum($total['total']['GiftCard']),2);?></td>
    </tr>
    <tr>
      <th>Total Billed </th>
      <td class="tar"><?php echo number_format($tBilled=@array_sum($total['billed']['OriginalTotal']),2);?></td>
      <td class="tar"><?php echo count($total['billed']['OriginalTotal']);?></td>
      <td class="tar"><?php echo number_format($p[]=@array_sum($total['billed']['AmountApplied']),2);?></td>
      <td class="tar"><?php echo number_format($tBilled - $p[1],2);?></td>
      <td class="tar"><?php echo number_format(@array_sum($total['billed']['Split']),2);?></td>
      <td class="tar"><?php echo number_format(@array_sum($total['billed']['GiftCard']),2);?></td>
    </tr>
    <tr>
      <th>Total Unbilled </th>
      <td class="tar"><?php echo number_format($tUnbilled=@array_sum($total['unbilled']['OriginalTotal']),2);?></td>
      <td class="tar"><?php echo count($total['unbilled']['OriginalTotal']);?></td>
      <td class="tar"><?php echo number_format($p[]=@array_sum($total['unbilled']['AmountApplied']),2);?></td>
      <td class="tar"><?php echo number_format($tUnbilled - $p[2],2);?></td>
      <td class="tar"><?php echo number_format(@array_sum($total['unbilled']['Split']),2);?></td>
      <td class="tar"><?php echo number_format(@array_sum($total['unbilled']['GiftCard']),2);?></td>
    </tr>
    <tr>
      <td>(see forecast invoices for more data) </td>
      <td class="tar">&nbsp;</td>
      <td class="tar">&nbsp;</td>
      <td class="tar"><?php echo number_format(@array_sum($p),2);?></td>
      <td class="tar">&nbsp;</td>
      <td class="tar">&nbsp;</td>
      <td class="tar">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <th class="tar">Split to Me </th>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th>Other Agent Invoices<sup class="basic"><a href="#note2">2</a></sup></th>
      <th class="tar">Total</th>
      <th class="tar">#</th>
      <th class="tar">Payments On </th>
      <th class="tar">Balance</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>
	<?php unset($p);?>
    <tr>
      <th>Total Invoiced </th>
      <td class="tar" title="represents total invoices you have created during this period"><?php echo number_format($tAll=@array_sum($total['splittotal']['OriginalTotal']),2);?></td>
      <td class="tar"><?php echo count($total['splittotal']['OriginalTotal']);?></td>
      <td class="tar" title="represents all payments that have been applied to the invoices listed"><?php echo number_format($p[]=@array_sum($total['splittotal']['AmountApplied']),2);?></td>
      <td class="tar" title="represents outstanding monies due from the invoices listed"><?php echo number_format($tAll - $p[0],2);?></td>
      <td class="tar"><?php echo number_format(@array_sum($total['splittotal']['Split']),2);?></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th>Total Billed </th>
      <td class="tar"><?php echo number_format($tBilled=@array_sum($total['splitbilled']['OriginalTotal']),2);?></td>
      <td class="tar"><?php echo count($total['splitbilled']['OriginalTotal']);?></td>
      <td class="tar"><?php echo number_format($p[]=@array_sum($total['splitbilled']['AmountApplied']),2);?></td>
      <td class="tar"><?php echo number_format($tBilled - $p[1],2);?></td>
      <td class="tar"><?php echo number_format(@array_sum($total['splitbilled']['Split']),2);?></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th>Total Unbilled </th>
      <td class="tar"><?php echo number_format($tUnbilled=@array_sum($total['splitunbilled']['OriginalTotal']),2);?></td>
      <td class="tar"><?php echo count($total['splitunbilled']['OriginalTotal']);?></td>
      <td class="tar"><?php echo number_format($p[]=@array_sum($total['splitunbilled']['AmountApplied']),2);?></td>
      <td class="tar"><?php echo number_format($tUnbilled - $p[2],2);?></td>
      <td class="tar"><?php echo number_format(@array_sum($total['splitunbilled']['Split']),2);?></td>
      <td>&nbsp;</td>
    </tr>
    <tr id="notes">
      <td colspan="7">
	  <a name="note1"></a>
	  <sup class="basic">1</sup> <em class="gray">This is estimated based on 50% of the billed amount</em>
	  <br />
	  <a name="note2"></a>
	  <sup class="basic">2</sup> <em class="gray">These are in the same time period but different from Agent's (your) invoices</em>
		</td>
      </tr>
  </table>

<?php
if($a=$total['total']['OriginalTotal']){
	$Properties=q("SELECT c.ID, c.CompanyName FROM finan_clients c, gl_properties p WHERE c.ID=p.Clients_ID AND p.ID IN('".implode("','",$Properties)."')", O_COL_ASSOC);
	?><table class="spacer">
	<thead>
	<tr>
	<th>&nbsp;</th>
	<th>Date</th>
	<th>Inv#</th>
	<th>Property</th>
	<th>Move-in</th>
	<th>Client</th>
	<th>Amount</th>
	<th>Paid On</th>
	<th>Last Billed</th>
	<th>Status</th>
	</tr>
	</thead><?php
	$i=0;
	foreach($a as $n=>$v){
		$i++;
		extract($rd[$n]);
		$sumOriginalTotal+=abs($OriginalTotal);
		$sumAmountApplied+=abs($AmountApplied);
		$Status='';
		if(
			/* force */
			$GLF_LateStatus==1 || 
			/* neutral and balance, but late */
			($GLF_LateStatus==0 && abs($OriginalTotal + $AmountApplied)>0 && $DateDueFrom<date('Y-m-d', strtotime('-30 days')))){

			$thisLateFee=($GLF_LateCharge>0 ? $GLF_LateCharge : $applicationLateFee);
			$allInvoices['LATEFEES'][$Leases_ID]=$thisLateFee;
		}else{
			$thisLateFee=0;
		}


		if(strtolower($HeaderStatus)=='void'){
			$Status= 'VOID';
		}else if($OriginalTotal + $AmountApplied + $thisLateFee >= 0 /* and we have to consider the late fee here */){
			$Status= 'PAID';
		}else{
			if($AmountApplied > 0){
				$Status= 'PP/';
				$allInvoices['PP'][$Leases_ID]=$AmountApplied;
			}
			if($LeaseStartDate > date('Y-m-d')){
				$Status= $k='FI';
			}else if($DateDueFrom <= date('Y-m-d',strtotime('-30 days'))){
				$Status= $k='PASTD';
			}else{
				$Status= $k='DUE';
			}
			$allInvoices[$k][$Leases_ID]=abs($OriginalTotal + $AmountApplied) + $thisLateFee;
		}
		if(($GLF_DiscrepancyDate!='0000-00-00' && $GLF_DiscrepancyDate!='') || $GLF_DiscrepancyReason){
			$allInvoices['DIS'][$Leases_ID]=abs($OriginalTotal + $AmountApplied) + $thisLateFee;
		}

		?><tr>
		<td style="margin-bottom:0px;"><?php
		if($CreateDate>$DateDueFrom){
			$redflag++;
			?><a href="#redflag" title="this invoice was entered late"><img src="/images/i/findicons.com-flag_red.png" width="16" height="16" alt="entered late" align="absbottom" /></a><?php
		}
		?></td>
		<td><?php echo date('n/j/Y',strtotime($HeaderDate));?></td>
		<td><a title="View or edit this invoice" href="leases.php?Leases_ID=<?php echo $Leases_ID;?>" onClick="return ow(this.href,'l1_leases','700,700');"><?php echo $HeaderNumber;?></a></td>
		<td><a href="properties2.php?Units_ID=<?php echo $Units_ID;?>" title="View/edit this property" onClick="return ow(this.href,'l1_properties','750,750');"><?php echo $Properties[$Properties_ID];?></a></td>
		<td><?php echo date('n/j/Y',strtotime($LeaseStartDate));?></td>
		<td><a href="contacts.php?Contacts_ID=<?php echo $Contacts_ID;?>" title="View/edit details about this customer" onClick="return ow(this.href,'l1_contacts','600,750');"><?php echo $FirstName . ' '. $LastName;?></a></td>
		<td class="tar"><?php echo number_format(abs($OriginalTotal),2);?></td>
		<td class="tar"><?php echo number_format($AmountApplied,2);?></td>
		<td><?php
		if($billed){
			echo date('n/j/Y',strtotime($LastBilled)).($billed>1 ? '<span style="font-size:119%;">*</span>' : '');
		}else{
			echo '&nbsp;';
		}
		?></td>
		<td><?php echo $Status;?></td>
		</tr><?php
	}
	if($i){
		?><tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><h3>$<?php echo number_format($sumOriginalTotal,2);?></h3></td>
		<td><h3>$<?php echo number_format($sumAmountApplied,2);?></h3></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr><?php
	}
	?></table>
<?php
}
if($redflag){
	?>
	<a name="redflag"></a>
	<sup class="basic"><img src="/images/i/findicons.com-flag_red.png" width="16" height="16" alt="entered late" align="absbottom" /></sup> <em class="gray">This invoice was entered late (after both the lease sign date and the move-in date).  This delays billing and slows processing of commission checks</em>
	<br />
	
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