<?php
/* Created 2011-02-04 by Samuel
this is the AGENT payment interface and is first use of a check in the finan_ accounting system 2.0.
how it works
	insert mode: we are showing invoices/leases that have   received a payment but have not been paid out to the agent
	update mode: we are showing transactions specifically related to that Checks_ID


	todo
	----
	2011-03-27:
		remove upper right tally and tier text
		pop-up to current tier for last and this month
		list splits this agent receives..
		Invoice - Date - FromAgent - Details - paidCheck(s) - Paid
		
		change tier to link with simple list and add view like perdiems for state - update tiers

	DONE	line out with css
	DONE	link out invoices pymts
	DONE	overheading
	DONE	lines down
	DONE	grey out 50%
	DONE	GIFT CARDS
	DONE	properties 2vs3



	*** RBAL RESETS PAST MONTH PERIOD ***

	notes?
	[button: new check for this agent]
	adjust date ranges
	even if we start out mid-month, we need to still tally previous payments
	COMMISSIONS SPLITS
	<previous payments>
	ABILITY TO MAKE A CHECK PAYMENT (PARTIAL)
		should have the ability to dismiss a payment for a reason
		any new check proffer should have all undismissed checks showing, and range should reset
		any paid check should pull up invisibly from the first of the first month paid, through VISIBLY (?) of the last day of that month and maybe even beyond.  BUT the key is that a payment determines range
	--------------------------------------------------------------


*/
function calc_tiers($tier,$paymentBalance,$thisPayment){
	//separate out $breakPoint and $percent
	foreach($tier as $breakPoint=>$percent){
		$i++;
		$breakPoints[$i]=$breakPoint;
		$percents[$i]=$percent;
	}
	//now we can key up or down
	ob_start();
	$ascendingBalance=$paymentBalance;
	foreach($breakPoints as $i=>$breakPoint){
		//if the paymentBalance goes through this region, skip it
		if(isset($breakPoints[$i+1]) && $breakPoints[$i+1]<=$ascendingBalance){
			prn('we are past this bracket '.$i);
			continue;
		}
		prn('-------------------');
		prn('we are at '.$i.' ('.$percents[$i].')');
		if(isset($breakPoints[$i+1]))prn('higher break point present');
		if($breakPoints[$i+1]-$ascendingBalance<$thisPayment)prn('payment goes into that bracket');
		
		$amountToBePaidInThisBracket= (isset($breakPoints[$i+1]) && $breakPoints[$i+1]-$ascendingBalance<$thisPayment ? $breakPoints[$i+1]-$ascendingBalance : $thisPayment);

		prn('applying '.$amountToBePaidInThisBracket.' at '.$percents[$i]);
		
		//map out the array
		prn('$output["'.$percents[$i].'"]=round($amountToBePaidInThisBracket,2);');
		eval('$output["'.$percents[$i].'"]=round($amountToBePaidInThisBracket,2);');
		
		$thisPayment-=$amountToBePaidInThisBracket;
		$ascendingBalance+=$amountToBePaidInThisBracket;
		prn('new thisPayment = '.$thisPayment);
		prn('new ascendingBalance = '.$ascendingBalance);
		
		if($thisPayment<=0)break;
	}
	ob_end_clean();
	return $output;
}

$currentMonth=date('Y-m-t',strtotime('today'));
$lastMonth=date('Y-m-t',strtotime('last month'));

$ReportDateFrom=substr($lastMonth,0,8).'01';
$ReportDateTo=$currentMonth;

$agent=q("SELECT un_email Email, un_firstname FirstName, un_lastname LastName, un_middlename MiddleName FROM bais_universal WHERE un_username='$Agents_username'", O_ROW);

//get agent tier
if($t=q("SELECT * FROM gl_tiers WHERE UserName='$Agents_username'", O_ROW)){
	$tier=array();
	unset($buffer);
	foreach($t as $n=>$v){
		if(substr($n,0,11)=='TierPercent'){
			if(isset($buffer) && $v>0)$tier[$buffer]=$v;
		}else if(substr($n,0,10)=='TierAmount'){
			$buffer=$v;
		}
	}
}else{
	q("INSERT INTO gl_tiers SET
	Username='$Agents_username',
	TierAmount1=0,
	TierPercent1=.5,
	TierAmount2=5000,
	TierPercent2=.6,
	TierAmount3=7000,
	TierPercent3=.7");
	$tier=array(
		0=>.5,
		5000=>.6,
		7000=>.7,
	);
}


/* sample tier only ... */
$tier=array(
	0=>.50,
	1550=>.60,
	2100=>.65,
	3000=>.70,
	9000=>.75,
);


if($paymentSplits=q("SELECT * FROM _v_mapsahead_agents_checks_commission WHERE Agent='$Agents_username' AND PaymentDate BETWEEN '$ReportDateFrom' AND '$ReportDateTo' ORDER BY PaymentDate, PaymentTotal, Payments_ID", O_ARRAY)){
	//we are going to (re)calc the tiers now
	$payments=array();
	$paymentBalance=0;
	foreach($paymentSplits as $n=>$v){
		if(!$payments[$v['Payments_ID']]){
			$payments[$v['Payments_ID']]=true;
			//jump up by TOTAL of payment
			$percent=calc_tiers($tier,$paymentBalance,$v['PaymentTotal']);
			$paymentSplits[$n]['Tier']=$percent;
			$paymentBalance+=$v['PaymentTotal'];
		}else{
			$paymentSplits[$n]['Tier']=$percent;
		}
	}
}
#prn($paymentSplits,1);

if(!$refreshComponentOnly){
	?><style type="text/css">
	.agents a{
		color:#000;
		}
	.agents td{
		border-bottom:1px dotted #666;
		padding:2px 3px 1px 4px;
		}
	.agents th{
		/*font-weight:400;*/
		padding:2px 3px 1px 4px;
		}
	.agents .sub th{
		border-bottom:1px solid black;
		}
	tr.alt td{
		background-color:#f5f5f5;
		}
	@media screen{
	
	}
	@media print{
		input{
			display:none;
			}
	}
	.agents tr.alt td.invoice{
		background-color:#ccd2db;
		}
	.agents tr td.invoice{
		background-color:#dbe0e9;
		}
	.agents tr.alt td.payment{
		background-color:#cad6ca;
		}
	.agents tr td.payment{
		background-color:#d8e4d8;
		}
	.agents tr.alt td.agent{
		background-color:#f1dacb;
		}
	.agents tr td.agent{
		background-color:#ffe8d9;
		}
	.agents .right{
		border-right:1px solid darkred;
		}
	.agents .bottom{
		border-bottom:1px solid #000;
		}
	.agents .top{
		border-top:1px solid #000;
		}
	.agents .summary td{
		border-top:1px solid #000;
		}
	.agents .hasfield, .agents .hasfield input{
		padding:0px 3px;
		}
	.agents .percent{
		color:#555;
		}
	.agents .mid{
		border-right:1px solid #999;
		}
	/* gift cards */
	.bp, .nbp{
		cursor:pointer;
		}
	.bp, .nbp, .pt, .po{
		padding-left:12px;
		}
	.bp{
		/* green checkmark */
		background-image:url("/images/i/arrows/check-darkgreen.png");
		background-repeat:no-repeat;
		background-position:bottom left;
		}
	.nbp{
		/* red checkmark */
		background-image:url("/images/i/arrows/check-darkred.png");
		background-repeat:no-repeat;
		background-position:bottom left;
		}
	.pt, .po{
		/* green but grayed out */
		background-image:url("/images/i/arrows/check-darkgreen.png");
		background-repeat:no-repeat;
		background-position:bottom left;
		filter:alpha(opacity=50);
		-moz-opacity:.5;
		opacity:.50;
		}
	
	</style>
	<script language="javascript" type="text/javascript">
	function toggleGCPaid(o){
		var a=o.id.split('_');
		o.className=(o.className=='bp'?'nbp':'bp');
		g('GCPaid'+a[0]+'_'+a[1]+'_'+a[2]).value=(o.className=='bp'?1:'');
		o.setAttribute('title', 'Gift card will '+(o.className=='npb'?'NOT ':'')+'be paid by this commission check; double-click to change this');
	}
	</script>
	<?php
}
?>
<div id="agentPayment">
<div class="fr" style="width:350px;">
Sales from <?php echo date('n/j/Y',strtotime($ReportDateFrom))?> including below: $4,399.00<br />
Current percent tier: <strong>50%</strong>
</div>
<h1 class="fl nullTop"><?php echo str_replace('-','',$agent['LastName'] . ', '.$agent['FirstName'])?></h1>
<p class="fl">
	<?php echo date('n/j/Y',strtotime($ReportDateFrom))?> to <?php echo date('n/j/Y',strtotime($ReportDateTo))?> 
	<input type="submit" name="Submit" value="Update dates.." class="th1b" />
	&nbsp;&nbsp;
	Check #
	<input name="HeaderNumber" type="text" id="HeaderNumber" size="7" class="th1" />
	&nbsp;&nbsp;
	Date:
	<input name="HeaderDate" type="text" id="HeaderDate" size="12" class="th1" />
</p>
<table class="cb agents" width="100%" border="0" cellspacing="0">
<thead>
  <tr>
  	<th>&nbsp;</th>
  	<th class="tac" colspan="2"><h3>Invoice</h3></th>
  	<th class="tac" colspan="3"><h3>Payment</h3></th>
  	<th class="tac" colspan="6"><h3>Agent</h3></th>
  	<th>&nbsp;</th>
  </tr>
  <tr class="bottom">
    <th>Property</th>
    <th class="top tac">Inv#</th>
    <th class="tar top">Full Amt.</th>
    <th class="tar top">Check #</th>
    <th class="top">Date</th>
    <th class="tar top">Paid</th>
    <th class="tar top">R.Bal.</th>
    <th class="tac top">Agent % </th>
    <th class="tar top">Due</th>
    <th class="tac top">- Spl. </th>
    <th class="tac top">- GC1</th>
    <th class="tac top">- GC2</th>
    <th class="tac top">Paid</th>
    <th>Notes</th>
  </tr>
</thead>
<tbody>
<?php
$runningBalance=0;
$i=0;
if($paymentSplits)
foreach($paymentSplits as $n=>$v){
	$i++;
	//if($i==1)prn($paymentSplits);
	extract($v);
	?><tr class="<?php echo !fmod($i,2)?'alt':''?>">
  <td class="right"><a tabindex="-1" href="properties3.php?Properties_ID=<?php echo $Properties_ID?>" onclick="return ow(this.href,'l1_properties','700,700');" title="View this property"><strong><?php echo $PropertyName?></strong></a></td>
		<td class="tar invoice"><a class="popup" tabindex="-1" href="leases.php?Invoices_ID=<?php echo $Invoices_ID?>" title="view this lease" onclick="return ow(this.href,'l1_leases','600,750');"><?php 
		//invoice #
		echo $HeaderNumber?></a></td>
		<td class="tar invoice right"><?php 
		//amount of invoice (original total)
		echo number_format($InvoiceAmount,2);?></td>
		<td class="tar payment mid"><a class="popup" tabindex="-1" href="payments.php?Payments_ID=<?php echo $Payments_ID?>" title="view this payment" onclick="return ow(this.href,'l2_payments','850,500');">
		  <?php
		//client's check number - was it just for this invoice or other invoices?
		echo $PaymentNumber;
		?>
		</a></td>
		<td class="payment mid"><?php 
		//date of *this* payment
		echo date('n/j/y',strtotime($PaymentDate));?></td>
		<td class="tar payment right"><?php 
		//total applied to this invoice? I am not sure [1]
		echo number_format($AmountApplied,2);?></td>
		<td class="tar agent"><?php
		//*this* payment received - summed in a running balance
		$runningBalance+=$AmountApplied;
		echo number_format($runningBalance,2);
		?></td>
		<td class="tac agent percent mid"><?php
		/* this is the most important part - running calc of his total */
		$tiers=calc_tiers($tier, $runningBalance, $PaymentTotal);
		$agentDue=0;
		foreach($tiers as $o=>$w){
			echo count($tiers)==1 ? round($o*100,2).'%' : number_format($w,2).' @ '.round($o*100,2).'%<br />';
			$agentDue+=$o*$w;
		}
		
		
		?></td>
		<td class="tar agent mid">
		  <?php 
		//this is based on the position of that payment in time
		$sigmaAgentDue+=$agentDue;
		echo number_format($agentDue,2);?></td>
		<td class="tar agent mid"><?php
		if($SplitProffer){
			$sigmaAgentDue -= (
				$SplitProffer ?
				min($agentDue, $SplitProffer > 2 ? $SplitProffer : round($agentDue * $SplitProffer)) :
				0
			);
			echo number_format(-($SplitProffer > 2 ? $SplitProffer : round($agentDue*$SplitProffer,2)),2);
		}else{
			?>&nbsp;<?php
		}
		?></td>
		<td class="tar agent mid"><?php
		/*
		status									appearance
		---------------------					----------------
		paid by this payment
		paid by other payment
		being paid by this payment (default)
		not being padi by this payment
		
		*/
		if($GiftCard1){
			if(!$GCPaid1){
				//being paid by this payment and un-clickable
				$status='bp';
			}else if($GCPaid1==$Payments_ID){
				//paid by this payment
				$status='pt';
			}else if($GCPaid1!=$Payments_ID){
				//paid by other payment
				$status='po';
			}
			?>
			<div id="1_<?php echo $Leases_ID?>_<?php echo $Payments_ID?>" class="<?php echo $status?>" <?php if($status=='bp'){ ?>ondblclick="toggleGCPaid(this);" title="Gift card will be paid by this commission check; double-click to change this"<?php } ?>>
			-<?php echo number_format($GiftCard1,2);?><?php echo $status=='po'?'*':''?>
			</div>
			<input type="hidden" name="GCPaid1[<?php echo $Leases_ID?>][<?php echo $Payments_ID?>]" id="GCPaid1_<?php echo $Leases_ID?>_<?php echo $Payments_ID;?>" />
			<?php
		}else{
			?>&nbsp;<?php
		}
		
		?></td>
		<td class="tar agent mid"><?php
		if($GiftCard2){
			if(!$GCPaid2){
				//being paid by this payment and un-clickable
				$status='bp';
			}else if($GCPaid2==$Payments_ID){
				//paid by this payment
				$status='pt';
			}else if($GCPaid2!=$Payments_ID){
				//paid by other payment
				$status='po';
			}
			?>
			<div id="2_<?php echo $Leases_ID?>_<?php echo $Payments_ID?>" class="<?php echo $status?>" <?php if($status=='bp'){ ?>ondblclick="toggleGCPaid(this);" title="Gift card will be paid by this commission check; double-click to change this"<?php } ?>>
			-<?php echo number_format($GiftCard2,2);?><?php echo $status=='po'?'*':''?>
			</div>
			<input type="hidden" name="GCPaid2[<?php echo $Leases_ID?>][<?php echo $Payments_ID?>]" id="GCPaid2_<?php echo $Leases_ID?>_<?php echo $Payments_ID;?>" />
			<?php
		}else{
			?>&nbsp;<?php
		}
		
		?></td>
		<td class="tac agent right hasfield"><?php
		//-------------------------- amount agent paid - may be one or multiple checks ------------------------------
		?>
		<input class="th1 tar" name="Amt[<?php echo invoicePymt?>]" type="text" id="Amt[<?php echo invoicePymt?>]" value="<?php if($CheckAllocation)echo number_format($CheckAllocation,2);?>" size="7" />		</td>
		<td>&nbsp;</td>
	</tr><?php
	
}
if(true || $paymentSplits){
	?><tr class="summary">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="tar"><strong><?php echo number_format($runningBalance,2);?></strong></td>
		<td>&nbsp;</td>
		<td class="tar"><?php echo number_format($sigmaAgentDue,2);?></td>
		<td class="tar">&nbsp;</td>
		<td class="tar">&nbsp;</td>
		<td class="tar">&nbsp;</td>
		<td class="tac"><?php if($paymentSplits){ ?><input name="Extension" type="text" class="th1" id="Extension" value="<?php if($Extension)echo number_format($Extension,2);?>" size="7" /><?php } ?></td>
		<td>&nbsp;</td>
	</tr><?php
}
?>
</tbody>
</table>
</div>