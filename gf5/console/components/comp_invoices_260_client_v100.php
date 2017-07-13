<?php
/*
Client invoices view 2012-04-19
Build steps:
1) included 
	dataset
	datasetComponent
	datasetDebug
	datasetQuery
	datasetQueryValidation
	two master components
	at this point you have an ugly table with inactive icon showing, no highlight select, and no delete or focus capability
2) decide which columns to show and under what conditions
	set up availableCols
	declared datasetFunction=parse - this is a new protocol that makes it easier to call an expression function	
	set up very-generic function  col() and also col_invoices() where that _invoices extension is the default
TO DO:
	have subtotals and grand totals
3) now I want to remove the delete feature, and make the edit single-click functional + highlight_select; and remove add button
	datasetShowDeletion=false
	datasetFocusViewDeviceFunction=focus_invoice
	highlight select "works" but .hlrow td is NO LONGER declared in /Library/css/DHTML/data_04_i1.css - so I added dataset_complexDataCSS()
	datasetHideFooterAddLink
	datasetHideEditControls - only if we are on online payment mode
4) now the goal is to header break by property and to call a function that shows the apartment by a function or required file
*/
$dataset='invoices';
$datasetGroup='invoices';
$datasetComponent='invoiceListClient';
$datasetQueryValidation=md5($MASTER_PASSWORD);
$datasetDebug['query']= ($testQuery==17 ? md5($MASTER_PASSWORD) : '');
$datasetDebug['time']= ($testTime==17 ? md5($MASTER_PASSWORD) : '');
$datasetQuery="SELECT
i.*, 
l.Rent,
l.ID AS Leases_ID, 
l.Agents_username, 
l.LeaseSignDate,
l.LeaseStartDate,
l.LeaseEndDate,
l.LeaseTerminationDate,
l.UnitNumber,
Verification_ID,
VerificationDetails,
Escort,
u.un_firstname, 
un_lastname,
c.FirstName,
c.LastName,
GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) AS DateDueFrom,
p.PropertyName,
p.ID AS Properties_ID
FROM 
_v_x_finan_headers_master i, 
gl_LeasesTransactions lt, 
gl_leases l,
gl_properties_units pu,
gl_properties p,
bais_universal u,
gl_LeasesContacts lc,
addr_contacts c
WHERE
AmountApplied + OriginalTotal < 0 AND 
$IncludeVoids
l.Units_ID=pu.ID AND
pu.Properties_ID=p.ID AND
l.Agents_username=u.un_username AND
l.ID=lc.Leases_ID AND
lc.Contacts_ID=c.ID AND lc.Type='Primary' AND
/* ---- (i.AmountApplied + i.OriginalTotal < 0  OR listed_invoices) AND ---- */
i.HeaderType='Invoice' AND
i.Transactions_ID=lt.Transactions_ID AND 
lt.Leases_ID=l.ID AND p.Clients_ID IN('".implode("','",list_clients())."')
$dueStatusQuery 
$statusQuery";
$datasetFunction='parse';
#-- 3 above: --
$datasetActiveHideControl=true;
$datasetShowDeletion=false;
$datasetFocusViewDeviceFunction='focus_invoice';
$datasetHideFooterAddLink=true;
$datasetHideEditControls=($section=='OnlinePayment'?true:false);
#-- 4 above: --
$datasetBreaks=true;//($section=='OnlinePayment'?false:true);
$datasetBreakFields=array(
	1=>array(
		'column'=>'Properties_ID',
	),
);
$datasetOptionsTop=array('section'=>'top', 'disposition'=>($section=='OnlinePayment' ? 'footer_idler':'header_invoices'));
$datasetOptionsMid=array('section'=>'mid', 'disposition'=>($section=='OnlinePayment' ? 'footer_idler':'header_invoices'));
$datasetOptionsBottom=array('section'=>'bottom', 'disposition'=>($section=='OnlinePayment' ? 'footer_invoices':'header_invoices'));
$datasetCalcFields=array(
	array(
		'name'=>'OriginalTotal',
		'calc'=>'sum',
		'function'=>'number_format(abs($n),2)',
	),
);
#-- 5 --
$datasetNoRecordsRow='invoices_norecords()';
function invoices_norecords(){
	global $invoices_norecords;
	$invoices_norecords=true;
	?><h2>Actually, no invoices currently need to be paid!</h2><?php
}
function footer_invoices(){
	global $availableCols,$dataset,$modApType,$modApHandle;
	global $col_invoices_surcharge;
	?><tr><?php 
	$a=$availableCols[$dataset][$modApType][$modApHandle]['scheme'];
	foreach($a as $handle=>$scheme){
		if(!$scheme['outputted'])continue;
		?><td class="tar"><?php
		if($handle=='AmountPaid'){
			echo '$<span id="Total">'.number_format(@array_sum($_SESSION['special']['payment']),2).'</span>';
		}else{
			?>&nbsp;<?php
		}
		?></td><?php
	}
	?></tr><?php
	if($col_invoices_surcharge){
		?><tr><td colspan="100%"><span class="asterisk">*</span> Invoices older than 7 days are charged a 1.5% processing surcharge.  We appreciate your prompt payment!</td></tr><?php
	}
}
function footer_idler(){
	//nothing
}

function focus_invoice($r){
	/* 2012-04-20, what we have here is a basic opening device
	not sure use of the global function->var feature, and it certainly has never been used consistently or with any protocol
	*/
	global $focus_invoice;
	extract($r);
	?><a href="leases.php?Leases_ID=<?php echo $Leases_ID;?>" title="View details and print" onclick="return ow(this.href,'l1_invoices','750,700');"><img src="/images/i/edit2.gif" alt="edit" /></a><?php
}
function col($options=array()){
	/* 2012-04-19: hopefully last version of this function :) 
	one goal = to call an external function AMAP and to keep this generic
	generally, we want to do the following:
		convert a value (such as foreign key to string)
		store an array also so we don't run the same operation
		calculate running balances
		format a value
	the general order of these operations is
		convert
		store
		calculate running balances or data
		format for output
	*/
	global $record,$mode,$submode,$dataset;
	global $qr,$fl,$ln,$developerEmail,$fromHdrBugs, $modApType,$modApHandle;
	if(is_array($options)){
		extract($options);	//should contain $field
		unset($options['field']);
	}else{
		$field=$options;
	}
	//additional globals, for example allInvoices, thisLateFee, applicationLateFee
	if($global)eval('global $'.preg_replace('/,\s*/',', $',$global).';');
	$r=$record;

	//typical conversion calls
	if($invert)$r[$field] *= -1;
	if($absolute)$r[$field]=abs($r[$field]);

	//store fetches
	
	//process
	ob_start();
	if($function=='default')$function='col_'.$dataset;
	$out=($function ? $function($field,$options) : $r[$field]);
	$err=ob_get_contents();
	ob_end_clean();
	if($err){
		//notify
	}
	
	if($format){
		//format as a date, etc. etc. or a link or email link or a picture sized as needed - for example I only need Tree_ID=571 & disposition=350x,2
		switch(true){
			case substr($format,0,5)=='date:':
				$out=date(end(explode(':',$format)),strtotime($out));
			break;
			case substr($format,0,13)=='number_format':
				$a=explode(':',$format);
				$a=explode(',',$a[1]);
				$out=($a[1]?$a[1]:'').number_format($out,($a[0]?$a[0]:2));
			break;
		}
	}
	return $out;
}
function col_invoices($field,$options=array()){
	/* 
	2012-04-20: this is the native function for this dataset
	NOTE only the coding between the lines needs to be updated
	
	*/
	global $record,$submode,$MASTER_USERNAME,	$qr,$fl,$ln,$developerEmail,$fromHdrBugs, $modApType,$modApHandle;
	extract($options);
	//additional globals, for example allInvoices, thisLateFee, applicationLateFee
	if($global)eval('global $'.preg_replace('/,\s*/',', $',$global).';');

	$a=$record;
	extract($a);
	ob_start();
	switch(true){
		//------------------------------ custom coding ------------------------------
		case $field=='HeaderNumber':
			?><a href="leases_print.php?Leases_ID=<?php echo $Leases_ID?>" title="View details of this invoice" tabindex="-1" onclick="return ow(this.href,'l1_leases','750,700');"><?php echo $HeaderNumber;?></a><?php
		break;
		case $field=='Contacts_ID':
			echo $FirstName.' '.$LastName;
		break;
		case $field=='LateFee':
			//this WAS in the late fee column but we need this for allInvoices
			if(
				/* force */
				$GLF_LateStatus==1 || 
				/* neutral and balance, but late */
				($GLF_LateStatus==0 && abs($OriginalTotal + $AmountApplied)>0 && $DateDueFrom<date('Y-m-d', strtotime('-30 days')))){

				$thisLateFee=($GLF_LateCharge>0 ? $GLF_LateCharge : $applicationLateFee);
				$allInvoices['LATEFEES'][$Leases_ID]=$thisLateFee;
			}else{
				$thisLateFee=0.00;
			}
			if($thisLateFee>0){
				echo number_format($thisLateFee,2);
			}else{
				echo '&nbsp;&nbsp;&nbsp;';
			}
			$thisStatus='';
			if(strtolower($HeaderStatus)=='void'){
				$thisStatus='VOID';
			}else if($OriginalTotal + $AmountApplied + $thisLateFee >= 0 /* and we have to consider the late fee here */){
				$thisStatus='PAID';
			}else{
				if($AmountApplied > 0){
					$thisStatus='PP/';
					$allInvoices['PP'][$Leases_ID]=$AmountApplied;
				}
				if($LeaseStartDate > date('Y-m-d')){
					$thisStatus.=($k='PENDING');
				}else if($DateDueFrom <= date('Y-m-d',strtotime('-30 days'))){
					$thisStatus.=($k='PASTD');
				}else{
					$thisStatus.=($k='DUE');
				}
				$allInvoices[$k][$Leases_ID]=abs($OriginalTotal + $AmountApplied) + $thisLateFee;
			}
			if(($GLF_DiscrepancyDate!='0000-00-00' && $GLF_DiscrepancyDate!='') || $GLF_DiscrepancyReason){
				$allInvoices['DIS'][$Leases_ID]=abs($OriginalTotal + $AmountApplied) + $thisLateFee;
			}
		break;
		case $field=='Agent':
			echo $un_lastname . ', '.$un_firstname; 
		break;
		case $field=='Balance':
			$bal=abs($OriginalTotal + $AmountApplied)+$thisLateFee;
			if(($GLF_DiscrepancyDate!='0000-00-00' && $GLF_DiscrepancyDate!='') || $GLF_DiscrepancyReason){
				?><img src="/images/i/findicons.com-flag_red.png" width="16" height="16" alt="DIS" title="Discrepancy <?php echo date('n/j/Y',strtotime($GLF_DiscrepancyDate))?>: <?php echo h($GLF_DiscrepancyReason);?>" style="padding-right:4px;" /><?php
			}
			if($bal==0){
				?><span id="balance_<?php echo $Leases_ID;?>" class="gray">pd.</span><?php
			}else{
				if(strtotime(date('n/j/Y',strtotime($DateDueFrom)).' +7 days')<time()){
					global $col_invoices_surcharge;
					$col_invoices_surcharge=true;
					?><input type="hidden" name="surcharge[<?php echo $Leases_ID;?>]" value="1" /><?php
				}
				$allInvoices['ACTIVE'][$Leases_ID]=$bal;
				?>
				<span class="asterisk" style="visibility:<?php echo $col_invoices_surcharge?'visible':'hidden';?>">*</span>
				<span id="balance_<?php echo $Leases_ID;?>"><?php echo number_format($bal,2);?></span><?php
			}
		break;
		case $field=='Status':
			echo $thisStatus;
			$usedProperties[$Properties_ID]=$Properties_ID;
		break;
		case $field=='PayOnline':
			if($thisStatus!=='PAID'){
				?><a href="home.php?section=OnlinePayment&Leases_ID=<?php echo $Leases_ID?>" title="Click to pay this invoice online" onclick="if(!alerted){alert('You are now being taken to the payments form.  You may pay multiple invoices with a single check account or credit card number">[pay online]</a><?php
			}
		break;
		case $field=='AmountPaid':
			global $bal;
			if($_REQUEST['Leases_ID']==$Leases_ID){
				$_SESSION['special']['payment'][$Leases_ID]=$amt=$bal;
			}else{
				$amt=$_SESSION['special']['payment'][$Leases_ID];
			}
			?><input name="ApplyTo[<?php echo $ID?>]" type="text" class="ApplyTo th1 tar" id="ApplyTo<?php echo $ID?>" size="6" onchange="dChge(this);updateTotal();" value="<?php if($amt)echo number_format($amt,2);?>" /><?php
		break;
		//------------------------------ end custom coding ------------------------------
		default:
			//this means that field or columns' action was not specified
			echo $field;
			global $col_notified;
			if(!$col_notified[$field]){
				$col_notified[$field]=true;
				mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='Field '.$field.' not set up'),$fromHdrBugs);
			}
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}
function header_invoices($options=array()){
	/* 2012-04-21: first use of a function to output a complete group break header; must include the <tr><td> tags as well */
	extract($options);
	echo '<tr class="dataobjectHeading"><td colspan="100%">'."\n";	
	global_extractor($_SERVER['DOCUMENT_ROOT'].'/gf5/console/components/comp_invoices_261_clientproperty_v100.php');
	echo '</td></tr>'."\n";
	global $datasetPlaceholder;
	echo $datasetPlaceholder='<!-- dataset placeholder here -->';
	echo "\n";
}
$a=array(
	'PropertyName'=>array(
	),
	'HeaderNumber'=>array(
		'header'=>'Inv#',
		'headerAlignment'=>'center',
		'function'=>'col(field=HeaderNumber&function=default)',
		'colattribs'=>array(
			'class'=>'tac',
		),
	),
	'HeaderDate'=>array(
		'header'=>'Date',
		'function'=>'col(field=HeaderDate&format=date:n/j/Y)',
	),
	'Unit'=>array(
		'header'=>'Addr./Unit',
	),
	'Tenant'=>array(
		'function'=>'col(field=Contacts_ID&function=default)',
	),
	'Rent'=>array(
		'function'=>'col(Rent)',
		'headerAlignment'=>'right',
	),
	'Amount'=>array(
		'header'=>'Amount',
		'function'=>'col(field=OriginalTotal&absolute=true&format=number_format:2,$)',
		'headerAlignment'=>'right',
	),
	'LateFee'=>array(
		'function'=>'col(field=LateFee&function=default&global=allInvoices,thisStatus)',
		'headerAlignment'=>'right',
	),
	'AmountApplied'=>array(
		'header'=>'Payments',
		'headerAlignment'=>'right',
		'function'=>'col(field=AmountApplied&format=number_format)',
	),
	'Balance'=>array(
		'headerAlignment'=>'right',
		'function'=>'col(field=Balance&function=default&global=thisLateFee,allInvoices,bal)',
	),
	'Status'=>array(
		'function'=>'col(field=Status&function=default&global=thisStatus,usedProperties)',
		'sortable'=>false,
	),
	'Agent'=>array(
		'function'=>'col(field=Agent&function=default)',
	),
	'PayOnline'=>array(
		'function'=>'col(field=PayOnline&function=default&global=thisStatus)',
		'sortable'=>false,
		'visibility'=>($section=='OnlinePayment'?COL_HIDDEN:COL_VISIBLE),
	),
	'AmountPaid'=>array(
		'function'=>'col(field=AmountPaid&function=default&global=thisStatus,bal)',
		'sortable'=>false,
		'visibility'=>($section!='OnlinePayment'?COL_HIDDEN:COL_VISIBLE),
	),
);
$availableCols[$datasetGroup][$modApType][$modApHandle]['scheme']=$a;

require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v104.php');

if(!$refreshComponentOnly){
	?><style type="text/css"><?php
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'556b2f',
		'datasetColorRowAlt_'=>'99a682',
		'datasetColorSorted_'=>'wheat',
	));
	?>
	.asterisk{
		font-family:Georgia, "Times New Roman", Times, serif;
		font-size:109%;
		color:darkred;
		}
	</style>
	<script language="javascript" type="text/javascript">
	</script>
	<?php
}

require($MASTER_COMPONENT_ROOT.'/dataset_component_v130.php');

?>