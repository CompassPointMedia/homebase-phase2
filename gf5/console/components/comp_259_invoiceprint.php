<?php
/*
things to do:
verify that payments is correct
parameterize legal by account
do styling and layout
link out to client record, lease record - all CONTEXTUALLY BASED ON who is seeing and where we came from

*/

set_time_limit(30*60);

if(!$refreshComponentOnly){
	?>
	<style type="text/css">
	@media screen{
		a{
			color:cadetblue;
			}
		.invoices{
			border:1px solid #aaa;
			padding:5px 20px;
			margin-top:25px;
			}
		.invstatus{
			border:1px dashed #666;
			padding:10px;
			width:150px;
			}
		.legal{
			padding-top:15px;
			}
		.clientAddress{
			background-color:ivory;
			padding:5px 10px;
			}
	}
	@media print{
		.invoices{
			position:relative;
			padding-bottom:75px;
			}
		.invstatus{
			display:none;
			}
		.legal{
			margin-top:100px;
			}
		.clientAddress{
			border-top:1px solid #ccc;
			}
		<?php if(count($data)>1){ ?>
		.pagebreak{
			page-break-after:always;
			}
		<?php } ?>
		#invoiceHeader{
			margin-top:30px;
			height:100px;
			padding-left:35px;
			}
		.clientAddress{
			margin-top:55px;
			height:100px;
			padding-left:35px;
			}
		
	}
	.amtDueBy{
		border:1px solid peru;
		margin:10px 0px;
		padding:15px;
		font-size:119%;
		background-color:ivory;
		}
	</style>
	<script language="javascript" type="text/javascript">
	
	</script><?php
}

//get invoice settings
@extract($params=q("SELECT varkey, varvalue FROM bais_settings WHERE username='system' AND vargroup='invoices' AND varnode='settings'", O_COL_ASSOC));

if(!empty($print))
foreach($print as $v)$prints+=($v[1]?1:0);

$i=0;
$j=0;
foreach($data as $n=>$v){
	$i++;
	extract($v);
	$DateDueFrom=max($LeaseSignDate, $LeaseStartDate, substr($CreateDate,0,10));
	
	$invoice=q("SELECT OriginalTotal, AmountApplied FROM _v_y_finan_invoices_mapsahead WHERE Leases_ID='$n'", O_ROW);
	@extract($invoice);
	if($print[$ID][1])$j++;
	$t=q("SELECT 
	c.ID, c.FirstName, c.LastName, c.MiddleName, c.HomeAddress, c.HomeCity, c.HomeState, c.HomeZip, c.HomeMobile
	FROM addr_contacts c, gl_LeasesContacts lc WHERE c.ID=lc.Contacts_ID AND lc.Leases_ID='$n' ORDER BY IF(lc.Type='Primary',1,2)", O_ARRAY);
	?>
	<div id="invoice_<?php echo $ID?>" class="invoices<?php echo true || $j<$prints ? ' pagebreak':''?><?php echo !$print[$ID][1] ? ' xprinthide' : '';?>">

		<div id="acctAddress" class="fr">
			<p>&bull;<?php echo $AcctPhone;?> p
			<?php if($AcctFax) echo '<br />'.$AcctFax.' f';?>
			&nbsp;&nbsp;
			&bull; <?php echo str_replace('http://','',$AcctWebsite);?>
		</div>

		<div id="invoiceHeader">
			<?php
			if($gis=@getimagesize($_SERVER['DOCUMENT_ROOT'].'/images/logos/'.$GCUserName.'.gif')){ ?>
			<img src="/images/logos/<?php echo $GCUserName?>.gif" width="<?php echo $gis[0]?>" height="<?php echo $gis[1]?>" alt="company logo" />
			<?php } ?>
			<p><?php echo $AcctAddress;?><br />
			<?php echo $AcctCity;?>, <?php echo $AcctState . ' ' . $AcctZip;?></p>
			</p>
		
		</div>




		<div class="cb"> </div>
		<?php
		//get some more information here
		$a=q("SELECT
		CompanyName, Address1, Address2, City, State, Zip, Phone, Fax
		FROM finan_clients WHERE ID=$Clients_ID", O_ROW);	
		if(strlen($a['Address1']) && $a['Address1']!=$PropertyAddress)$useClientAddress=true;
		?>
		<div class="fr">
		Phone on file: <?php echo $PropertyPhone ? $PropertyPhone : $a['Phone'];?><br />
		<?php if($PropertyFax || $a['Fax']){ ?>
		Fax on file: <?php echo $PropertyFax ? $PropertyFax : $a['Fax'];?>
		<?php }else{ ?>
		<span class="gray">(no fax on file)</span>
		<?php } ?>
		</div>
		<div class="clientAddress">
			<p><strong><?php echo $PropertyName?></strong><br />
			<?php if($useClientAddress && $a['CompanyName']!=$PropertyName){ ?>
			<?php echo '<span style="font-size:smaller;">c/o</span> '.$a['CompanyName'];?><br />
			<?php } ?>
			<?php echo $useClientAddress ? $a['Address1'] : $PropertyAddress?><br />
			<?php echo $useClientAddress && trim($a['Address2']) ? $a['Address2'].'<br />' : '';?>
			<?php echo $useClientAddress ? $a['City'] : $PropertyCity?>, <?php echo $useClientAddress ? $a['State'] : $PropertyState?>  <?php echo $useClientAddress ? $a['Zip'] : $PropertyZip?><br />
			</p>
		</div>
		<h1 class="nullBottom">Invoice</h1>
	  <div class="fr" style="font-size:129%;font-weight:400;">Invoice Number: <strong><a href="leases.php?Leases_ID=<?php echo $ID; ?>" title="view/edit this invoice" onclick="return ow(this.href,'l1_leases','700,700');"><?php echo $HeaderNumber?></a></strong></div>
	    <h3>Tenant Information</h3>
	<?php
	if($mode=='updateBillingSent' && array_sum($print[$ID])!=1){
		unset($sentBy);
		if($print[$ID][2])$sentBy[]='email';
		if($print[$ID][4])$sentBy[]='auto-fax';
		?><div class="fr invstatus">
		<?php 
		if($sentBy){
			?>Sent by: <strong><?php echo implode(', ',$sentBy);?></strong><br />
			<?php
		}
		?>
		<?php if(!$print[$ID][1]){ ?>
		<strong>This invoice will not be printed out!</strong>
		<?php } ?>
		</div>
	<?php
	}
	?>
      <p>Tenant Name: <strong><?php echo $t[1]['LastName'].', '.$t[1]['FirstName']?></strong><br />
	  <?php
	  if($TenantCount>1){
	  	?>Additional tenants: <?php
		echo @implode('; ',q("SELECT CONCAT(LastName,', ',FirstName) FROM addr_contacts c, gl_LeasesContacts lc WHERE c.ID=lc.Contacts_ID AND lc.Type!='Primary' AND lc.Leases_ID=$ID AND LastName NOT LIKE '%Tester%'", O_COL));
		?><br /><?php
	  }
	  ?>
	    Move-in date: <?php echo date('n/j/Y',strtotime($LeaseStartDate));?><br />
	    Unit number: <?php echo $UnitNumber?><br />
	    Agent's Name: <?php echo q("SELECT CONCAT(un_firstname, ' ', un_lastname) FROM bais_universal WHERE un_username='$Agents_username'", O_VALUE);?><br />
	    Rental Amount: $<?php echo number_format($Rent,2);?><br />
	  </p>
	  <div class="amtDueBy">
	  <p>Invoice Total: $<?php echo number_format( -$OriginalTotal,2);?><br />
	  <?php if($AmountApplied>0){ ?>
	  Remaining Balance: $<?php echo number_format( -$OriginalTotal - $AmountApplied,2);?><br />
	  <?php } ?>
      Amount due before: <?php echo date('n/j/Y', strtotime($DateDueFrom.' +31 days'));?></p>
	  </div>
	  <?php 
	  if($payments=q("SELECT 
		  h.HeaderNumber,
		  h.ID,
		  h.HeaderDate,
		  SUM(tt.AmountApplied) AS AmountApplied
		  FROM
		  finan_headers h, finan_transactions t, finan_TransactionsTransactions tt, finan_transactions t2, finan_headers h2, gl_LeasesTransactions lt
		  WHERE
		  h.ID=t.Headers_ID AND 
		  t.ID=tt.ParentTransactions_ID AND 
		  tt.ChildTransactions_ID=t2.ID AND 
		  t2.Headers_ID=h2.ID AND 
		  t2.Accounts_ID !=h2.Accounts_ID AND
		  t2.ID=lt.Transactions_ID AND
		  lt.Leases_ID=$ID
		  GROUP BY h.ID", O_ARRAY)){
	  	?>
		<p>PAYMENTS RECEIVED SO FAR<br />
		<table>
		<thead>
		<tr>
			<th>Date</th>
			<th>Amount</th>
			<th>Check #</th>
		</thead>
		<?php
		foreach($payments as $o=>$w){
			?><tr>
			<td><?php echo date('n/j/Y',strtotime($w['HeaderDate']));?></td>
			<td><?php echo number_format($w['AmountApplied'],2);?></td>
			<td><?php echo $w['HeaderNumber'];?></td>
			</tr><?php
		}
		?></table><?php
	}
	if(date('Ymd',time())>date('Ymd',strtotime($DateDueFrom.' +30 days'))){
		?>
		<span style="color:darkred;">THIS INVOICE IS PAST DUE!</span><br />
		<?php
	}
	if(min($_SESSION['admin']['roles'])<ROLE_AGENT){
		?><div class="printhide gray" style="font-size:10px;">This invoice generated <?php echo date('n/j/Y \a\t g:iA');?> by <?php echo $_SESSION['admin']['userName'];?></div><?php
	}
	if($invoiceLegal){
		?>
		<div class="legal">
		<span class="gray"><?php echo nl2br($invoiceLegal);?></span>
		</div>
		<?php
	}
	?>
	<p>&nbsp;</p>
    </div>	
	<?php
}
?>
