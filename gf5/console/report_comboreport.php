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

if(minroles()>=ROLE_AGENT)exit('You do not have access to this report');


/*
2012-05-10
	
2012-03-25 Combo Agent/Property Invoice Due/Forecast Report

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

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo ($PageTitle='Combo Agent/Property Invoice Due/Forecast Report - '.$AcctCompanyName);?></title>



<link rel="stylesheet" type="text/css" href="/Library/css/cssreset01.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
.yat .bottom td, .yat th{
	background-color:darkolivegreen;
	color:white;
	border:1px solid #000;
	}
.yat2 {
	border-collapse:collapse;
	margin-top:15px;
	}
.yat2 h2{
	margin:0px;
	}
.yat2 td{
	border:1px solid #000;
	padding:10px 10px;
	}
.void td{
	background-color:#eee;
	color:#444;
	}
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
var isEscapable=2;
var isDeletable=0;
var isModal=0;
var talks=0; //whether this page broadcasts overall state changes which other pages listen for
var listens=0;

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already

function interlock1(n){
	g('agentList').style.display=(n=='agent'?'block':'none');
	g('propertyList').style.display=(n=='property'?'block':'none');
	g('agents').disabled=(n=='agent'?false:true);
	g('properties').disabled=(n=='property'?false:true);
}
function interlock2(n){
	g('dates').style.display=(n=='5' || n=='6'?'inline':'none');
}
function setForm(n){
	if(n==1){
		g('emailPending').style.display='inline';
		g('form1').action='';
		g('mode').value='sendMailing';
		g('form1').target='w2';
		g('form1').method='post';
		g('form1').submit();
		//revert
		g('form1').target='';
		g('form1').method='get';
		g('mode').value='';
		return false;
	}
}
</script>

<link rel="stylesheet" type="text/css" href="/Library/css/DHTML/rich_calendar.css">
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar_lang_en.js"></script>


</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
<h3>Combo Agent/Property Invoice Due/Forecast Report</h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>



<?php
if(!$orderBy)$orderBy='property';

?>
	<div id="toolbar1" class="printhide">
	Summarize report by: 
	<select name="orderBy" id="orderBy" onChange="dChge(this);interlock1(this.value);">
		<option <?php echo $orderBy=='agent'?'selected':''?> value="agent">Agent</option>
		<option <?php echo $orderBy=='property'?'selected':''?> value="property">Property</option>
	</select>
	<br />


	<div id="agentList" style="display:<?php echo $orderBy=='agent'?'block':'none';?>">
	<?php 
	if(is_array($agents))foreach($agents as $n=>$v)if($v=='-1')unset($agents[$n]);
	?>
	<span class="gray">Select agent(s):</span><br />
	<select name="agents[]" size="10" multiple="multiple" id="agents" style="min-width:350px;" <?php if($orderBy!=='agent')echo 'disabled';?>>
	<option value="-1" <?php echo empty($agents)?'selected':''?>>(All agents)</option>
	<?php
	$topProducerQuantity=25;
	if($a=q("SELECT
		IF(COUNT(DISTINCT l.ID)>=$topProducerQuantity,'yes','no') AS TopProducer, COUNT(DISTINCT l.ID) AS Count, un_username, un_firstname, un_middlename, un_lastname
		FROM
		bais_universal u 
		JOIN bais_staff s ON un_username=st_unusername 
		LEFT JOIN bais_StaffRoles sr ON s.st_unusername=sr_stusername AND sr_roid>=20 
		LEFT JOIN gl_leases l ON l.Agents_username=un_username AND l.CreateDate > DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
		
		WHERE (st_active=1 AND sr_roid IS NOT NULL) ".($Agents_username ? " OR un_username='$Agents_username'":'')."
		GROUP BY un_username
		ORDER BY IF(COUNT(DISTINCT l.ID)>=$topProducerQuantity,1,2), un_lastname, un_firstname", O_ARRAY)){
		$i=0;
		foreach($a as $v){
			$i++;
			if($v['TopProducer']!=$buffer){
				if($i>1)echo '</optgroup>';
				$buffer=$v['TopProducer'];
				?><optgroup label="<?php echo $v['TopProducer']=='yes'?'Top Producers':'Other'?>"><?php
			}
			?><option value="<?php echo $v['un_username']?>" <?php echo @in_array($v['un_username'],$agents)?'selected':''?>><?php echo h($v['un_lastname'] . ($v['un_lastname'] && $v['un_firstname'] ?  ', ':''). $v['un_firstname'].($v['TopProducer']=='yes'?' ('.$v['Count'].')':''))?></option> <?php
		}
		?></optgroup><?php
	}  
	?>
	</select>
	</div>



	<div id="propertyList" style="display:<?php echo $orderBy=='property'?'block':'none';?>">
	<?php 
	if(is_array($properties))foreach($properties as $n=>$v)if($v=='-1')unset($properties[$n]);
	?>
	<span class="gray">Select propertie(s):</span><br />
	<select name="properties[]" size="10" multiple="multiple" id="properties" onChange="dChge(this);" style="min-width:350px;" <?php if($orderBy!=='property')echo 'disabled';?>>
	<option value="-1" <?php echo empty($properties)?'selected':''?>>(All properties)</option>
	<?php
	if($a=q("SELECT
		p.Clients_ID,
		p.ID AS Properties_ID,
		IF(p.Type='APT', p.PropertyName, c.CompanyName) AS Name,
		p.Type,
		COUNT(DISTINCT p.ID) AS PropertyCount,
		COUNT(DISTINCT d.Headers_ID) AS LeaseCount,
		IF(COUNT(DISTINCT d.Headers_ID)>0, 1, 0) AS HasLeases
		FROM
		gl_properties p
		LEFT JOIN _v_invoices_due_all d ON d.Properties_ID=p.ID,
		finan_clients c 
		WHERE p.Clients_ID=c.ID
		
		GROUP BY p.Clients_ID, IF(p.Type='APT',p.ID,p.Clients_ID)
		ORDER BY 
		IF(COUNT(DISTINCT d.Headers_ID)>0,1,2), IF(p.Type='APT',1,2), IF(p.Type='APT', p.PropertyName, c.CompanyName)", O_ARRAY)){
		$i=0;
		foreach($a as $v){
			$i++;
			if(strtolower($v['Type'])!=strtolower($buffer1) || strtolower($v['HasLeases'])!=$buffer2 || $i==1){
				if($i>1)echo '</optgroup>';
				$buffer1=$v['Type'];
				$buffer2=$v['HasLeases'];
				?><optgroup label="<?php echo strtolower($buffer1)=='apt'?'Apartments':'Non-Apartments ('.$buffer1.')';?>"><?php
			}
			?><option value="<?php echo $v['Properties_ID']?>" <?php echo @in_array($v['Properties_ID'],$properties)?'selected':''?>><?php echo h($v['Name']).($v['LeaseCount']?' ('.$v['LeaseCount'].')':'');?></option><?php
		}
		?></optgroup><?php
	}  
	?>
	</select>
	</div>
	<br />
		Invoice types: 
		  <select name="Status" id="Status" onchange="dChge(this);interlock2(this.value);">
		  <option value="1" <?php echo $Status==1?'selected':''?>>Due &amp; Past Due Invoices</option>
		  <option value="2" <?php echo $Status==2?'selected':''?>>Due Invoices</option>
		  <option value="3" <?php echo $Status==3?'selected':''?>>Past Due Invoices</option>
		  <option value="4" <?php echo $Status==4?'selected':''?>>Partially Paid Invoices</option>
		  <option value="5" <?php echo $Status==5?'selected':''?>>Forecast Invoices</option>
		  <option value="6" <?php echo $Status==6?'selected':''?>>Voided Invoices</option>
		  <option value="7" <?php echo $Status==7?'selected':''?>>Late-entered Invoices</option>
	      </select>
		<span id="dates" style="display:<?php echo $Status>4 && $Status<7?'inline':'none';?>">
	    from: <img align="absbottom" onClick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input name="ReportDateFrom" type="text" id="ReportDateFrom" value="<?php echo date('m/d/Y',strtotime($ReportDateFrom));?>" size="14" />
		to
		<img align="absbottom" onClick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input name="ReportDateTo" type="text" id="textfield4" value="<?php echo date('m/d/Y',strtotime($ReportDateTo));?>" size="14" />
		</span>

		<input type="button" name="button" id="button1" value="Update" onClick="g('form1').setAttribute('method','get');g('form1').setAttribute('target','');g('form1').action='';g('form1').submit();return false;" /> &nbsp;
		<input type="button" name="button" id="button2" value="Print" onClick="window.print();" /> 
		&nbsp;
		<input type="button" name="button" id="button4" value="Close" onClick="window.close();" />&nbsp;	
	  </div>
	<?php if($Status>4 && $Status<7){ ?>
	<div class="screenhide">
	<h2>Report date from <?php echo date('m/d/Y',strtotime($ReportDateFrom));?> to <?php echo date('m/d/Y',strtotime($ReportDateTo));?></h2>
	</div>
	<?php } ?>
</div>

</div>
<div id="mainBody">

<?php if($mode=='sendMailing')ob_start();?>
<div class="suite1">



<?php
/*
we are looking for:
	all invoices that are due
	all invoices that are past due
	all invoices that are both
	all forecast invoices
where
	agent=1,some,all
	exclusively or
	property=1,some,all
	
	
*/

for($_i_=1; $_i_<=1; $_i_++){ //--------- break loop ---------
if(!$_SERVER['QUERY_STRING']){
	?><h3>Select from the options above and click Update</h3><?php
	break;
}


$agentsPropertiesQuery=$statusQuery='';
if($orderBy=='property'){
	$agentsPropertiesQuery=(count($properties) ? ' AND p.ID IN('.implode(',',$properties).')' : '');
}else{
	$agentsPropertiesQuery=(count($agents) ? " AND Agents_UserName IN('".implode("','",$agents)."')" : '');
}
$statusQuery=($Status==6 ? '' : ' AND ABS(i.AmountApplied) < ABS(OriginalTotal)');
switch($Status){
	case 1: //due and past due invoices
		$statusQuery.=' AND GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) <= CURDATE()';
	break;
	case 2: //due invoices but not past due - date between 29 days ago and today
		$statusQuery.=" AND GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()";
	break;
	case 3: //past due invoices only - date older than 30 days ago
		$statusQuery.=" AND GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) < DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
	break;
	case 4: //partially paid invoices
		$statusQuery.=" AND ABS(i.AmountApplied) > 0";
	break;
	case 5: //forecast invoices
		$statusQuery.=" AND GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) BETWEEN DATE_ADD(CURDATE(), INTERVAL 1 DAY) AND '$ReportDateTo'";
	break;
	case 6: //voided invoices
		$statusQuery.=" AND i.HeaderStatus='Void' AND GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) BETWEEN '$ReportDateFrom' AND '$ReportDateTo'";
	break;
	case 7: //late-entered invoices
		$statusQuery.=" AND DATE_ADD(GREATEST(l.LeaseStartDate, l.LeaseSignDate), INTERVAL 7 DAY)<l.CreateDate";
	break;
}

?>

<?php
if($a=q("SELECT i.*, l.Agents_UserName, l.SubAgents_UserName, l.LeaseStartDate, l.SubSplit, GiftCard1 + GiftCard2 AS GiftCard, lt.Leases_ID , pu.Properties_ID,
	GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) AS DateDueFrom,
	c.ID AS Contacts_ID,
	c.FirstName, c.LastName,
	l.Units_ID,
	l.Rent,
	l.UnitNumber,
	pu.Properties_ID,
	p.Clients_ID,
	p.PropertyName,
	p.PropertyAddress,
	p.PropertyCity,
	p.PropertyState,
	p.PropertyZip,
	p.OfficeHours,
	u.un_username, u.un_lastname, u.un_firstname,

	GLF_LateStatus,
	GLF_LateCharge,
	HeaderStatus,
	GLF_DiscrepancyDate,
	".($Status==6?"v.Name AS VoidReason,":'')."
	GLF_DiscrepancyReason
	


	FROM
	_v_x_finan_headers_master i ".($Status==6?'LEFT JOIN gl_voidreasons v ON i.GLF_Voidreasons_ID=v.ID':'').", 
	gl_LeasesTransactions lt, 
	gl_leases l LEFT JOIN gl_LeasesContacts lc ON l.ID=lc.Leases_ID AND lc.Type='Primary' LEFT JOIN addr_contacts c ON lc.Contacts_ID=c.ID,
	gl_properties_units pu,
	gl_properties p,
	bais_universal u
	WHERE i.Transactions_ID=lt.Transactions_ID AND lt.Leases_ID=l.ID AND l.Units_ID=pu.ID AND pu.Properties_ID=p.ID AND l.Agents_UserName=u.un_username
	
	/* agents selected OR properties selected */
	$agentsPropertiesQuery

	/* status */
	$statusQuery
	
	ORDER BY ".($orderBy=='property' ? 'p.PropertyName, p.ID, l.CreateDate' : 'un_lastname, un_firstname, p.PropertyName, p.ID, l.CreateDate'), O_ARRAY)){
	$i=0;
	$key=($orderBy=='property' ? 'Properties_ID' : 'un_username');
	foreach($a as $v){
		extract($v);
		$i++;
		
		//from comp_invoices_251_(dataset_from_scratch_03).php
		if(
			/* force */
			$GLF_LateStatus==1 || 
			/* neutral and balance, but late */
			($GLF_LateStatus==0 && abs($OriginalTotal + $AmountApplied)>0 && $DateDueFrom<date('Y-m-d', strtotime('-30 days')))){

			$thisLateFee=($GLF_LateCharge>0 ? $GLF_LateCharge : $applicationLateFee);
		}else{
			$thisLateFee=0;
		}

		$status='';
		if(strtolower($HeaderStatus)=='void'){
			$status='VOID';
		}else if($OriginalTotal + $AmountApplied + $thisLateFee >= 0 /* and we have to consider the late fee here */){
			$status='PAID';
		}else{
			if($AmountApplied > 0){
				$status='PP/';
			}
			if($LeaseStartDate > date('Y-m-d')){
				$status.='FI';
			}else if($DateDueFrom <= date('Y-m-d',strtotime('-30 days'))){
				$status.='PASTD';
			}else{
				$status.='DUE';
			}
		}


		if($v[$key]!==$buffer){
			if($i>1){
				if(false){ ?><table><?php } ?>
				<tr class="topborder nobo bottom">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td class="tar"><h3 class="nullBottom">$<?php 
				$allOriginalTotals += $originalTotals;
				echo number_format($originalTotals,2);?></h3></td>
				<td class="tar"><h3 class="nullBottom">$<?php 
				$allLateFees+= $lateFees;
				echo number_format($lateFees,2);?></h3></td>
				<td class="tar"><h3 class="nullBottom">$<?php 
				$allDues += $dues;
				echo number_format($dues,2);?></h3></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				</tr>
				<?php
				if(false){ ?></table><?php }
				//reset
				$originalTotals='';
				$lateFees='';
				$dues='';
				echo '</tbody></table>'."\n";?>
				<?php
				if($mode=='sendMailing'){
					$outputs[$buffer]=ob_get_contents();
					ob_end_clean();
					echo $outputs[$buffer];
				}
			}
			$buffer=$v[$key];
			?>
			<div class="header">
			<?php if($orderBy=='agent'){ ?>
			<h2><a href="staff.php?un_username=<?php echo $v['un_username'];?>" title="View/edit this staff member record" onclick="return ow(this.href,'l1_staff','700,700');"><?php echo $v['un_lastname']. ', '.$v['un_firstname'];?></a></h2>
			<?php } else{ 
			
			$p=q("SELECT * FROM finan_clients WHERE ID='$Clients_ID'", O_ROW);
			
			if(minroles()<ROLE_AGENT && $orderBy=='property'){
				?>
				<div class="fr">
				<label><input type="checkbox" name="mailing[<?php echo $Properties_ID?>]" value="1" /> Select for mailing</label>
				</div>
				<?php
			}
			?>
			<h2><a href="properties2.php?Properties_ID=<?php echo $v['Properties_ID'];?>" title="View/edit this property full record" onclick="return ow(this.href,'l1_properties','750,700');"><?php echo $v['PropertyName'];?></a></h2>
			<p>
			<?php
			if($PropertyAddress)echo $PropertyAddress.'<br />';
			if($PropertyCity)echo $PropertyCity . ', '.$PropertyState. '  '.$PropertyZip.'<br />';
			if($p['Phone'])echo $p['Phone'] . '(p)<br />';
			if($p['Fax'])echo $p['Fax'] . '(f)<br />';
			if($OfficeHours){
				?><span class="gray"><?php echo $OfficeHours;?></span><?php
			}
			?>
			</p>
			<?php } ?>
			</div>
			<?php if($mode=='sendMailing')ob_start();?>
			<table class="yat">
			<thead>
			<tr>
				<th>Inv. # </th>
				<th>Tenant Name</th>
				<th class="tar">Rental<br />
				  Amt.</th>
				<th class="tar">Invoice Amt.</th>
				<th class="tar">Late Fee</th>
				<th class="tar">Total<br />Due</th>
				<th class="tac">Move-in Date</th>
				<th class="tac">Unit #</th>
				<th>Invoice<br />
			    due by: </th>
				<th>Status</th>
				<?php if($Status==6){ ?>
				<th>Reason</th>
				<?php }else{ ?>
				<th>p/d</th>
				<?php } ?>
				<?php if($orderBy=='agent'){ ?>
				<th>Property Name</th>
				<?php }else if($orderBy=='property'){ ?>
				<th>Agent Name</th>
				<?php } ?>
				<?php if(false){ ?><th>Date</th><?php } ?>
			</tr>
			</thead>
			<tbody>
			<?php
		}
		?><tr <?php echo $Status==6?'class="void"':''?>>
			<td><?php if($mode!=='sendMailing'){ ?><a title="View or edit this invoice" href="leases.php?Leases_ID=<?php echo $Leases_ID;?>" onClick="return ow(this.href,'l1_leases','700,700');"><?php } ?>
			<?php echo $HeaderNumber;?>
			<?php if($mode!=='sendMailing'){ ?></a><?php } ?></td>
			<td><?php echo $v['FirstName'] . ' '. $v['LastName'];?></td>
			<td class="tar"><?php echo number_format($v['Rent'],2);?></td>
			<td class="tar"><?php $originalTotals+=$v['OriginalTotal']*-1; echo number_format($v['OriginalTotal']*-1,2);?></td>
			<td class="tar"><?php $lateFees+=$thisLateFee; echo ($thisLateFee ? '<span class="red">'.number_format($thisLateFee,2).'</span>' : '&nbsp;');?></td>
			<td class="tar"><?php
			//example 15.00 *(partial payment applied)
			$due= $v['OriginalTotal']*-1 + $thisLateFee - $AmountApplied;
			$dues+=$due;
			echo number_format($due,2);
			if($AmountApplied>0){
				echo $note1='<sup class="basic"><a href="note1" style="font-family:Georgia;color:darkred;">*</a></sup>';
			}
			?></td>
			<td class="tac"><?php echo date('n/j/Y',strtotime($LeaseStartDate));?></td>
			<td class="tac"><?php echo $v['UnitNumber'];?></td>
			<td><?php echo date('n/j/Y',strtotime($DateDueFrom.' +30 days'));?></td>
			<td><?php echo $status;?></td>

			<?php if($Status==6){ ?>
			<td><?php echo $VoidReason;?></td>
			<?php }else{ ?>
			<td nowrap="nowrap"><?php
			if($status=='PASTD'){
				$pd=floor((time() - strtotime($DateDueFrom)) / (24*3600));
				echo $pd>90 ? 'over 90' : $pd;
			}else echo '&nbsp;';			
			?></td>
			<?php } ?>
			<?php if($orderBy=='agent'){ ?>
			<td><?php echo $v['PropertyName'];?></td>
			<?php }else if($orderBy=='property'){ ?>
			<td><?php echo $v['un_lastname'] . ($un_lastname && $un_firstname ? ', ' : '').$v['un_firstname'];?></td>
			<?php } ?>
			<?php if(false){ ?><td><?php echo date('n/j/Y',strtotime($HeaderDate));?></td><?php } ?>
		</tr>
		<?php
	}
	//same as coding above
	if(false){ ?><table><?php } ?>
	<tr class="topborder nobo bottom">
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td class="tar"><h3 class="nullBottom">$<?php 
	$allOriginalTotals += $originalTotals;
	echo number_format($originalTotals,2);?></h3></td>
	<td class="tar"><h3 class="nullBottom">$<?php 
	$allLateFees+= $lateFees;
	echo number_format($lateFees,2);?></h3></td>
	<td class="tar"><h3 class="nullBottom">$<?php 
	$allDues += $dues;
	echo number_format($dues,2);?></h3></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<?php if(false){ ?><td>&nbsp;</td><?php }?>
	</tr>
	<?php
	if(false){ ?></table><?php }
	//reset
	$originalTotals='';
	$lateFees='';
	$dues='';
	?>
	</tbody></table>
	<?php
	if($mode=='sendMailing'){
		$outputs[$Properties_ID]=ob_get_contents();
		ob_end_clean();
		echo $outputs[$Properties_ID];
	}
	?>
	<?php if($allDues>0){ ?>
	<table class="yat2">
	  <tr>
		<td><h2>Invoices Total:</h2></td>
		<td class="tr"><h2>$<?php echo number_format($allOriginalTotals,2);?></h2></td>
	  </tr>
	  <?php if($allLateFees>0){ ?>
	  <tr>
		<td><h2>Late Fees:</h2></td>
		<td class="tr"><h2>$<?php echo number_format($allLateFees,2);?></h2></td>
	  </tr>
	  <?php } ?>
	  <?php if($allDues>$allOriginalTotals){ ?>
	  <tr>
		<td><h2>Grand Total:</h2> </td>
		<td class="tr"><h2>$<?php echo number_format($allDues,2);?></h2></td>
	  </tr>
	  <?php } ?>
	</table>
	<?php }?>
      <?php
}else{
	?><span class="gray">No results found by that criteria</span><?php
}
if($note1){
	?>
	<a name="note1"></a>
	<sup class="basic">*</sup><span class="gray">Payments have been applied to this invoice</span><br />
	<?php
}

}//------------ end break loop --------------
?>
<?php
if(minroles()<ROLE_AGENT){
	?><br />
	<br />
	<div class="balloon1">NOTE: this is still in testing mode which means emails will be sent to YOUR email address</div>
	<?php
	if($Status!=3 && $Status!=5){
		?>
		Show these comment before the invoice list <em class="gray">(optional)</em>:<br />
		<textarea name="EmailComments" id="EmailComments" onchange="dChge(this);" rows="4" cols="45"></textarea><br />
		<?php
	}
	?>
	
	<input type="submit" name="Submit" value="Send Statements/Lists" onclick="return setForm(1);" />&nbsp;<span id="emailPending" style="display:none;"><img src="/images/i/ani/ani-fb-orange.gif" width="16" height="11" /></span>
	<input name="mode" type="hidden" id="mode" />
	<?php
}
?>
</div>

<?php if($mode=='sendMailing') ob_end_clean(); ?>
<?php
if($mode=='sendMailing' && $orderBy=='property'){
	if(!count($_POST['mailing'])){
		?><script language="javascript" type="text/javascript">window.parent.g('emailPending').style.display='none';</script><?php
		error_alert('select at least one property to send to');
	}
	foreach($_POST['mailing'] as $Properties_ID=>$null){
		$property=q("SELECT * FROM gl_properties WHERE ID=$Properties_ID", O_ROW);
		$client=q("SELECT c.*, t.FirstName, t.LastName, t.Email AS ContactEmail FROM finan_clients c LEFT JOIN  finan_ClientsContacts cc ON c.ID=cc.Clients_ID AND cc.Type='Primary' LEFT JOIN addr_contacts t ON cc.Contacts_ID=t.ID WHERE c.ID=".$property['Clients_ID'], O_ROW);
		$emailTo=sun('e');
		$emailSource=$_SERVER['DOCUMENT_ROOT'].'/gf5/console/emails/email_06_sendstatements_v100.php';
		require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
	}
	?><script language="javascript" type="text/javascript">
	window.parent.g('emailPending').style.display='none';
	</script><?php
	error_alert('Successfully sent out '.count($_POST['mailing']).' email'.(count($mailing)>1?'s':''));
}
?>
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