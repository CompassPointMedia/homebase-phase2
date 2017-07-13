<?php
if($Clients_ID && $invoices=q("
	SELECT
	i.*, l.ID AS Leases_ID, l.UnitNumber, l.Agents_username, 
	GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) AS DateDueFrom,
	u.un_firstname, un_lastname
	FROM 
	_v_x_finan_headers_master i, gl_LeasesTransactions lt, gl_leases l LEFT JOIN bais_universal u ON l.Agents_username=u.un_username 
	
	WHERE
	(
	/* ------------ condition 1: outstanding balance; came from comp_invoices_251_(dataset_from_scratch_03).php --------------- */
	i.AmountApplied + i.OriginalTotal + IF(
		i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
		IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 
		0.00
	) < 0  
	
	OR
	/* ----------------- condition 2: invoices paid by this payment ------------------ */
	".($mode==$updateMode ? "i.ID IN('".
	implode("','",q("SELECT t2.Headers_ID FROM
	finan_transactions t, finan_TransactionsTransactions tt, finan_transactions t2
	WHERE 
	t.Headers_ID='$Payments_ID' AND 
	t.ID=tt.ParentTransactions_ID AND
	tt.ChildTransactions_ID=t2.ID", O_COL)).
	"')" : '0')."
	
	) AND 
	i.Clients_ID='$Clients_ID' AND 
	i.HeaderType='Invoice' AND
	i.HeaderStatus!='Void' AND
	i.Transactions_ID=lt.Transactions_ID AND 
	lt.Leases_ID=l.ID ", O_ARRAY)){
	//OK
}

if(!$refreshComponentOnly){
	?><style type="text/css">
	.hasInvoices{
		background-color:#FF9933;
		}
	.pymts th{
		font-family:Georgia, "Times New Roman", Times, serif;
		font-weight:400;
		font-size:109%;
		border-bottom:1px solid black;
		padding:4px 5px 2px 4px;
		}
	.pymts td{
		padding:3px 5px 1px 4px;
		}
	.pymts .invRow{
		border-bottom:1px solid #ccc;
		}
	.pymts .linkA a{
		}
	.pymts .linkA{
		background-color:cornsilk;
		}
	.pymts input.th1{
		padding-right:3px;
		text-align:right;
		}
	.pymts .total{
		font-size:111%;
		font-family:Georgia, "Times New Roman", Times, serif;
		}
	.focus td{
		background-color:lightgreen;
		}
	</style>



<script type="text/javascript" language="javascript">
$(function(){	$('.pymts .th1').numeric('.'); });
function updateTotal(){
	if(typeof applied=='undefined')applied=document.getElementsByTagName('input');
	var fillTotal=0.00;
	for(var i in applied){
		try{
		if(!applied[i].id.match(/^ApplyTo/))continue;
		if(!applied[i].value.length)continue;
		var n=parseFloat(applied[i].value);
		n=Math.round(n*100)/100;
		fillTotal+=n;
		nout=n+(parseInt(n)==n ? '.00':'');
		if(nout.match(/\.[0-9]$/))nout+='0';
		applied[i].value=nout;
		}catch(e){
			if(e.description)alert(i);
		}
	}
	fillTotal=Math.round(fillTotal*100)/100 + (parseInt(fillTotal)==fillTotal ? '.00':'');
	if(fillTotal.match(/\.[0-9]$/))fillTotal+='0';
	g('Total').value=fillTotal;
}
</script>
	<script language="javascript" type="text/javascript">
	var client='<?php echo $Clients_ID?>';
	function selectClientInvoices(n){
		if(detectChange && !confirm('You have started entering payment information and this will be lost if you switch clients.  Continue?'))return false;
		window.open('resources/bais_01_exe.php?mode=refreshComponent&component=paymentsGUI&Clients_ID='+n,'w2');
	}
	$('.nbr').numeric({allow:"."});
	</script><?php
}
?>
    <div id="paymentsGUI">
Client: 
<select name="Clients_ID" id="Clients_ID" onfocus="client=this.value;" onchange="selectClientInvoices(this.value);dChge(this);" class="th1">
<option value="">&lt;Select..&gt;</option>
<?php
//list clients with properties
if($a=q("SELECT
	a.ID, a.ClientName, IF(COUNT(i.ID),1,2) AS HasInvoices,
	COUNT(DISTINCT i.ID) AS Invoices
	FROM
	finan_clients a LEFT JOIN _v_x_finan_headers_master i ON i.HeaderType='Invoice' AND a.ID=i.Clients_ID AND AmountApplied+OriginalTotal < 0,
	gl_properties p, finan_ClientsContacts cc, addr_contacts c
	WHERE
	a.ID=p.Clients_ID AND a.ID=cc.Clients_ID AND cc.Contacts_ID=c.ID
	GROUP BY a.ID
	ORDER BY IF(COUNT(i.ID),1,2), a.ClientName", O_ARRAY)){
	$buffer=$i=0;
	foreach($a as $v){
		$i++;
		if($buffer!=$v['HasInvoices']){
			$buffer=$v['HasInvoices'];
			if($i>1)echo '</optgroup>';
			?><optgroup label="<?php echo $v['HasInvoices']==1?'Outstanding Invoices':'No Invoices'?>"><?php
		}
		?><option value="<?php echo $v['ID']?>" class="<?php echo $v['HasInvoices']==1?'hasInvoices':''?>" <?php echo $Clients_ID==$v['ID']?'selected':''?>><?php echo h($v['ClientName']).($v['Invoices']?' ('.$v['Invoices'].')':'');?></option><?php
	}
	?></optgroup><?php
}
?>
</select>
<br>
Check number:
<input name="HeaderNumber" type="text" class="th1" id="HeaderNumber" value="<?php echo h($HeaderNumber);?>" size="9" onchange="dChge(this);" />
<script language="javascript" type="text/javascript">
try{g('HeaderNumber').focus();}catch(e){}
</script>
payment type: 
<select name="Types_ID" id="Types_ID" onchange="dChge(this)" class="th1">
	<option value="1" <?php echo $Types_ID==1?'selected':''?>>Check</option>
	<option value="2" <?php echo $Types_ID==2?'selected':''?>>Cash</option>
	<option value="3" <?php echo $Types_ID==3?'selected':''?>>Credit Card</option>
	<option value="4" <?php echo $Types_ID==4?'selected':''?>>Money Order</option>
</select>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />
Amount: 
<input name="Amount" type="text" class="th1" id="Amount" value="<?php if(count($Amounts))echo number_format(-array_sum($Amounts),2);?>" size="7" onchange="dChge(this);" />
&nbsp;&nbsp;&nbsp;
Date Received: 
<input name="DateCredited" type="text" class="th1" id="DateCredited" value="<?php if(strlen($HeaderDate)){ echo t($HeaderDate,f_qbks);}else if($mode==$insertMode)echo date('n/j/Y');?>" size="12" onchange="dChge(this);" />
<br>
Memo: 
<input name="Notes" type="text" class="th1" id="Notes" value="<?php echo h($Notes)?>" size="45" maxlength="255" onchange="dChge(this);" />
<br>
<br>
<table width="100%" border="0" cellspacing="0" class="pymts">
	<thead>
      <tr>
        <th class="tac">Pay</th>
        <th>Invoice #</th>
        <th>Pri. Tenant</th>
        <th>Agent</th>
		<th>Unit/Address</th>
        <th class="tar">Inv. Amt.</th>
		<th class="tar">Late Fee</th>


        <!-- <th>Date</th> -->
        <th class="tar">Prior Pymts.</th>
        <th><?php echo $mode==$insertMode? 'New Pymt.':'Amt Applied';?></th>
        <th>Bal. Due</th>
        <!-- <th class="tac">adj</th> -->
      </tr>
	</thead>
	<tfoot>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td class="tar total">Total:&nbsp;</td>
		<td><input name="Total" type="text" class="th1" id="Total" size="6" onchange="dChge(this);" value="<?php if(count($Amounts))echo number_format(-array_sum($Amounts),2);?>" /></td>
		<td>&nbsp;</td>
		<!-- <td>&nbsp;</td> -->
	</tr>
	</tfoot>
	<tbody id="paymentsLineItems">
	<?php
	if($invoices){
		foreach($invoices as $n=>$v){
			?><tr class="invRow<?php echo $FocusInvoices_ID==$v['ID']?' focus':''?>">
				<td class="tac"><input type="checkbox" name="checkbox" value="1" onclick="g('ApplyTo<?php echo $v['ID']?>').value=(this.checked ? g('amt<?php echo $v['ID']?>').innerHTML.replace(',','') : ''); updateTotal(); dChge(this);" tabindex="-1" /></td>
				<td class="linkA"><a href="leases.php?Leases_ID=<?php echo $v['Leases_ID']?>" title="View this lease information" onclick="return ow(this.href,'l2_leases','700,700');" tabindex="-1"><?php echo $v['HeaderNumber']?></a></td>
				<td><?php 
				if(minroles()>ROLE_MANAGER && sun()!=$v['Agents_username']){
					?><em class="gray">private</em><?php
				}else{
					echo q("SELECT 
					CONCAT(FirstName,' ',LastName)
					FROM gl_LeasesContacts lc, addr_contacts c 
					WHERE lc.Contacts_ID=c.ID AND lc.Leases_ID='".$v['Leases_ID']."' AND lc.Type='Primary'", O_VALUE);
				}
				?></td>
				<td><?php
				if(minroles()>ROLE_MANAGER && sun()!=$v['Agents_username']){
					?><em class="gray">private</em><?php
				}else{
					echo $v['un_lastname'] || $v['un_firstname'] ? $v['un_firstname'].' '.$v['un_lastname'] : $v['Agents_username'];
				}			
				?></td>
				<td><?php echo $v['UnitNumber'] ? $v['UnitNumber'] : '&nbsp;'?></td>
				<td class="tar"><?php echo number_format(-$v['OriginalTotal'],2);?></td>
				<td class="tar"><?php
				$thisLateFee=0;
				if(
					/* force */
					$v['GLF_LateStatus']==1 || 
					/* neutral and balance, but late */
					($v['GLF_LateStatus']==0 && abs($v['OriginalTotal'] + $v['AmountApplied'])>0 && $v['DateDueFrom']<date('Y-m-d', strtotime('-30 days')))){
	
					$thisLateFee=($v['GLF_LateCharge']>0 ? $v['GLF_LateCharge'] : $applicationLateFee);
	
					?><span id="latefee_<?php echo $v['ID']?>" class="red"><?php echo number_format($thisLateFee,2);?></span><?php
				}else if($v['GLF_LateStatus']==-1){
					//override
					?><span id="latefee_<?php echo $v['ID']?>" class="<?php if($v['DateDueFrom']  < date('Y-m-d', strtotime('-30 days'))/*it is past due*/)echo 'red';?>">0.00</span><?php
				}else{
					?><span class="red" id="latefee_<?php echo $v['ID'];?>">&nbsp;&nbsp;&nbsp;&nbsp;</span><?php
				}
				?></td>


				<!-- <td><?php echo t($v['HeaderDate'],f_qbks);?></td> -->
				<!-- <td class="tar"><span id="amt<?php echo $v['ID']?>"><?php echo number_format(-$v['AmountApplied'] - $v['OriginalTotal'] + $thisLateFee,2);?></span></td> -->
				<td class="tar"><?php echo number_format($v['AmountApplied'],2);?></td>
				<td><input name="ApplyTo[<?php echo $v['ID']?>]" type="text" class="th1" style="text-align:right;" id="ApplyTo<?php echo $v['ID']?>" size="6" onchange="dChge(this);updateTotal();" value="<?php if($Amounts[$v['ID']])echo number_format(-$Amounts[$v['ID']],2);?>" /></td>
				<td class="tar">
				<span id="amt<?php echo $v['ID']?>"><?php echo number_format(-$v['AmountApplied'] - $v['OriginalTotal'] + $thisLateFee,2);?></span>
				</td>
				<!-- <td class="tac"><input tabindex="-1" type="checkbox" name="Adjust[<?php echo $v['ID']?>" id="Adjust[<?php echo $v['ID']?>" value="1" class="th1" onchange="dChge(this);" /> 
			    </td> -->
			</tr>
			<?php
		}
	}else{
		?><tr><td colspan="100%"><em><?php echo $Clients_ID? 'No outstanding invoices/leases listed for this client. <a href="leases_pre.php?Clients_ID='.$Clients_ID.'" title="Create a new lease for this property or apartment" onclick="return ow(this.href,\'l1_leases\',\'700,700\');">Click to add a lease</a>':'First select a client from the list above'?></em></td></tr><?php
	}
	?>
	</tbody>
</table>

<!--
Credits (unapplied portion):<br />
I want to:<br />
<input name="radiobutton" type="radio" value="radiobutton" />
Write a check to the client<br />
<input name="radiobutton" type="radio" value="radiobutton" /> 
Apply a credit memo<br /> 
<br />
-->
</div>
