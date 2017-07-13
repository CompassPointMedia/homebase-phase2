<form name="form1" id="form1" action="/gf5/console/resources/bais_01_exe.php" method="post" target="w2">
	<span class="gray">&nbsp;</span><br />
	<input name="mode" type="hidden" id="mode" value="refreshComponent" />
	<input name="submode" type="hidden" id="submode" />
	<input name="component" type="hidden" id="component" value="invoiceList" />  
<?php
$testLimit=200;
if($submode=='printInvoices' || $submode=='sendInvoices'){
	$key=substr(md5(time().rand(1,100000)),0,16);
	$_SESSION['special']['print'][$key]=$_REQUEST;
	?><script language="javascript" type="text/javascript">
	window.parent.ow('/gf5/console/leases_<?php echo $submode=='printInvoices'?'print':'send'?>.php?key=<?php echo $key?>','l1_print','700,700');
	</script><?php
	$assumeErrorState=false;
	exit;
}else if($submode=='setVoid'){
	if(!$GLF_VoidReasons_ID)error_alert('You cannot un-void an invoice');
	if(q("SELECT tt.ChildTransactions_ID FROM finan_transactions t, finan_TransactionsTransactions tt WHERE t.Headers_ID=$Headers_ID AND t.ID=tt.ChildTransactions_ID", O_VALUE))error_alert('This invoice cannot be voided!  It has had a payment received against it.  You must first delete the payment to void this invoice');

	//void and zero out transactions - leave accounts_id intact
	q("UPDATE finan_headers SET HeaderStatus='Void', GLF_VoidReasons_ID=$GLF_VoidReasons_ID WHERE ID=$Headers_ID");
	q("UPDATE finan_transactions SET Extension=0, UnitPrice=0 WHERE Headers_ID=$Headers_ID");
	?><script language="javascript" type="text/javascript"><?php
	//voiding
	if($IncludeVoids){
		//void the invoice visually
		?>
		var c=window.parent.g('r_<?php echo $Headers_ID?>').className;
		c=c.replace(' void','');
		window.parent.g('r_<?php echo $Headers_ID?>').className=c+' void';
		<?php
	}else{
		//hide the invoice
		?>window.parent.g('r_<?php echo $Headers_ID?>').style.display='none';<?php
	}
	?></script><?php
	$assumeErrorState=false;
	exit;
}else if($submode=='filterClients'){
	q("REPLACE INTO bais_settings SET UserName='".sun()."', 
	vargroup='invoices', varnode='invoicesfilterClients', varkey='', varvalue='$Clients_ID'");
	$_SESSION['userSettings']['invoicesfilterClients']=$Clients_ID;	
}
/*
00. todo
--------
	DONE	remove bluescreen error
	DONE	wrap in form
	DONE	invoices() showing - -- fix this
	DONE	test the sorting out
	DONE	remove active/inactive
	NODE	** remove edit
	DONE	implement late charge
	add print tool
	have voiding working
	TIME TO INTRODUCE CTRL-D AND CTRL-H
	implement discrepancy
	
	contextualize the deletion feature

	
	lean out the layout some, more twd report  - like SFC activity reports
	hightlight color - retain even though a report - maybe ctrl-clicking
*/

/*
0. first require the two amigos at the bottom in latest version (for this it was precoding_v104, and component_v124)

DOCUMENTATION
-------------
This code block is a clean-up from other datasets I have created, grouped by how they affect the dataset, and in order of criticality.  Actually, a dataset can function off of a view or table with as little as the variables 1) $dataset and 2) $datasetTable.  Everything else configures the features and layout of that data.
*/

/*
1. first we declare the dataset name - must be unique throughout our system
Dataset Group is a concept that groups several datasets of the same basic information.  Dataset COMPONENT is the HTML identity of the object and is referenced in refreshing the component.  Currently (2011-04-02) datasetGroup is not used or significant other than it is the node-key of availableCols.
*/
$dataset='invoices';
$datasetGroup=$dataset;					//so far (2011-04-02) always same as dataset
$datasetComponent='invoiceList';		//used in HTML div id for component wrap (used for refresh and etc.)
if(!$datasetWord)$datasetWord='Invoice';
if(!$datasetWordPlural)$datasetWordPlural='Invoices';

if($test==17)$datasetDebug['query']=md5($MASTER_PASSWORD);
if($test==18)$datasetDebug['time']=md5($MASTER_PASSWORD);
$datasetReferenceFile=end(explode('/',__FILE__));
$datasetReferenceFileKey=md5('salt:hydroponic'.$MASTER_PASSWORD);

/*
1b. NOW that we have dataset handle declared, we can add other pre-coding type features.  These features should be geared toward keying ot the dataset and datasetComponent
*/

$datasetDefaultParams[$dataset]['IncludeVoids']=0;

/* --------------------------------- selection widget -------------------------------- */
if(!function_exists('widget_selection'))require($FUNCTION_ROOT.'/function_widget_selection_v100.php');
//woo hoooooooo!!!!
$dueStatusOutput=widget_selection(array(
	'filterNode' => 'invoiceDueStatuses',
	'optionLabel' => 'Due status',
	'varGroup' => 'invoices',
	'anchorLabel' => 'Due status',
	'anchorImgPath' => '/images/assets/i-due.jpg',
	'selOptions' => array(
		1=>'Forecasted Invoice (FI)',
		2=>'Due Invoice (DUE)',
		3=>'Past Due Invoice (PASTD)',
	)
));
$paidStatusOutput=widget_selection(array(
	'filterNode' => 'invoiceStatuses',
	'optionLabel' => 'invoice status',
	'varGroup' => 'invoices',
	'anchorLabel' => 'Paid status',
	'anchorImgPath' => '/images/assets/i-payment.jpg',
	'selOptions' => array(
		1=>'Unpaid',
		2=>'Partially Paid',
		3=>'Paid',
	)
));
/* -------------- now use the selection widget statusSet to customize the invoice query ------------- */
$statuses=array();
for($i=1;$i<=3;$i++){
	if(in_array($i,$widget_selection['invoiceStatuses']['statusSet']))$statuses[]=$i;
}
$statuses=implode(',',$statuses);
/*
balance = amountapplied + originaltotal + latecharge

				---------------------------------- first -----------------	 --------- latecharge -------
latecharge = if(status=1 OR (status=0 AND balance AND duedate over 30 days), if(latecharge,latecharge,10), 0)
latecharge = IF(i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND duedate over 30 days), IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 0.00)

//final result
balance = amountapplied + originaltotal + 
IF(
	i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
	IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 
	0.00
)

1 - unpaid
2 - partially paid
3 - paid

1 = NO PAYMENTS APPLIED
1,2 = NOT PAID IN FULL (balance > 0)
1,3 = !(amount applied and balance > 0)
2, = balance > 0 and amount applied
2,3 = amount applied
3 = balance = 0
*/
switch($statuses){
	case '1,2,3':
		$statusQuery='';
	break;
	case '1':
		$statusQuery='AND i.AmountApplied=0';
	break;
	case '1,2':
		//no invoices that are not paid in full
		$statusQuery='AND !(
		i.AmountApplied >= (i.OriginalTotal * -1) AND i.OriginalTotal * -1 > 0
		)';
	
		/*
		delete this by 1/1/12
		$statusQuery='AND
		IF(
			i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
			IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 
			0.00
		) > 0';
		*/
	break;
	case '1,3':
		$statusQuery='AND !(i.AmountApplied > 0 AND 
		IF(
			i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
			IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 
			0.00
		) > 0)';
	break;
	case '2':
		$statusQuery='AND (i.AmountApplied > 0 AND 
		IF(
			i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
			IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 
			0.00
		) > 0)';
	break;
	case '2,3':
		$statusQuery='AND i.AmountApplied > 0';
	break;
	case '3':
		$statusQuery='AND 
		IF(
			i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
			IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 
			0.00
		)
		=0';
	break;
}

$statuses=array();
for($i=1;$i<=3;$i++){
	if(in_array($i,$widget_selection['invoiceDueStatuses']['statusSet']))$statuses[]=$i;
}
$statuses=implode(',',$statuses);
switch($statuses){
	case '1,2,3':
		$dueStatusQuery='';
	break;
	case '1':
		//due date > now
		$dueStatusQuery='AND GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)>CURDATE()';
	break;
	case '1,2':
		//"not past due" - due date > now - 30
		$dueStatusQuery='AND GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)>DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
	break;
	case '1,3':
		//!(current invoices) - come back on this
		$dueStatusQuery='AND GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) NOT BETWEEN DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND CURDATE()';
	break;
	case '2':
		//now between due and due + 30
		$dueStatusQuery='AND CURDATE() BETWEEN GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) AND DATE_ADD(GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate), INTERVAL 30 DAY)';
	break;
	case '2,3':
		//not forecasted - due <= now
		$dueStatusQuery='AND GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) <= CURDATE()';
	break;
	case '3':
		//past due
		$dueStatusQuery='AND GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) < DATE_SUB(CURDATE(), INTERVAL 30 DAY)';
	break;
}

if(minroles()>ROLE_AGENT){
	$datasetClientFilter=$datasetInternalFilter=' AND p.Clients_ID IN('.implode(',',list_clients()).')';
}else if(minroles()>ROLE_MANAGER){
	$datasetInternalFilter=' AND i.ID IN('.implode(',',list_invoices()).')';
	$datasetClientFilter='';
}else{
	$datasetInternalFilter=$datasetClientFilter='';
}


$ClientList=q("SELECT
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
WHERE
p.Clients_ID=c.ID
$datasetClientFilter

GROUP BY p.Clients_ID, IF(p.Type='APT',p.ID,p.Clients_ID)
ORDER BY 
IF(COUNT(DISTINCT d.Headers_ID)>0,1,2), IF(p.Type='APT',1,2), IF(p.Type='APT', p.PropertyName, c.CompanyName)", O_ARRAY);
?><span class="gray" style="font-size:smaller;">Query took <?php echo $qr['time']?> seconds</span><?php
/*
2. next we declare the datasetTable; if datasetFieldList=* then no initial sort for user will be declared
	- datasetFieldList optional, default value will be *; better to use a comma separated list
	- datasetTable (table or view)
	- datasetQuery (alternate method, leave blank if not used)
	- datasetID default=ID - primary key of the dataset
	- datasetArrayType default=O_ARRAY_ASSOC
*/

$datasetTable='';
$datasetID='Leases_ID';
$datasetArrayType=O_ARRAY_ASSOC;
$datasetQueryValidation=md5($MASTER_PASSWORD);
//$datasetDebug['query']=md5($MASTER_PASSWORD);
if(isset($changeParams['IncludeVoids'])){
	$IncludeVoids=$_SESSION['userSettings']['invoicesIncludeVoids']=($changeParams['IncludeVoids'] ? '' : 'i.HeaderStatus!=\'Void\' AND ');
}else{
	$IncludeVoids=($_SESSION['userSettings']['invoicesIncludeVoids'] ? '' : 'i.HeaderStatus!=\'Void\' AND ');
}
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
l.GiftCard1, l.GiftCard2, l.GCBatch1, 
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
$IncludeVoids
l.Units_ID=pu.ID AND
pu.Properties_ID=p.ID AND
l.Agents_username=u.un_username AND
l.ID=lc.Leases_ID AND
lc.Contacts_ID=c.ID AND lc.Type='Primary' AND
/* ---- (i.AmountApplied + i.OriginalTotal < 0  OR listed_invoices) AND ---- */
i.HeaderType='Invoice' AND
i.Transactions_ID=lt.Transactions_ID AND 
lt.Leases_ID=l.ID ".($_SESSION['userSettings']['invoicesfilterClients']?"AND i.Clients_ID=".$_SESSION['userSettings']['invoicesfilterClients']:'')." 
$dueStatusQuery 
$statusQuery
$datasetInternalFilter
";
if($mode=='refreshComponent')prn($datasetQuery);
/*
***** AT THIS POINT THE DATASET WILL BEGIN TO SHOW THE "NATURAL" COLUMNS *****
	- active/inactive column
	- delete rows
	- focus control
	- default colors and alt highlighting
	- hightlight select

*/
//active/inactive toggle
$datasetActiveHideControl=true;
$datasetActiveUsage=true;
$datasetActiveActiveExpression='Active=1';
$datasetActiveInactiveExpression='Active=0';
$datasetActiveAllExpression='1';
$datasetActiveField='Active';
$datasetActiveActivateTitle='Make this item active';
$datasetActiveInactivateTitle='Make this item inactive';
$datasetActiveControl='someFunction(".$$datasetID.", ".($$datasetActiveField?1:0).");';
//allow this parameter to be passed remotely
if(!isset($datasetActiveHideControl))$datasetActiveHideControl=false;

//deletion of rows
$datasetDeleteMode='deleteInvoice';
$datasetShowDeletion=true;

//focus control
$datasetFocusPage='leases.php';
$datasetFocusAddObjectJSFunction='ow(this.href,\'l1_leases\',\'800,700\',true);'; //this is because opening an object is not well developed yet
$datasetFocusQueryStringKey='Leases_ID';

$datasetFocusViewDeviceFunction='';		//not used initially
$datasetAdditionalClassFunction='getVoidsEtc';		//not used initially
function getVoidsEtc($record){
	global $datasetAdditionalClass;
	if(strtolower($record['HeaderStatus'])=='void'){
		$datasetAdditionalClass=' void';
	}else{
		$datasetAdditionalClass='';
	}
}

//batching
$datasetDefaultBatch=1000;				//will be 50 if not declared, or if globalBatchThreshold not declared

//sorting
### see the variables datasetReferenceFile[Key] above


//breaks and grouping
$datasetShowBreaks=false;
$datasetBreakFields=array(
	1=>array(
		'column'=>'Office',
		'blank'=>'not specified'
	),
	2=>array(
		'column'=>'HomeName',
		'blank'=>'not specified'
	)
);


/*
4. now we declare availableCols.  Note that datasetGroup must be declared as it is a node in the array
	- this will override the "natural" columns in the datasetTable
	- this brings up the point that the protocol on availableCols is unmanageably difficult, and I need a GUI on this	
*/
$modApType='embedded';
$modApHandle='first';
ob_start();
?>
Print<br />
<span class="large">
<a href="#" onclick="return checkAll(true);">+</a> <a href="#" onclick="return checkAll(false);">-</a>
</span>
<?php
$printWithAllNone=ob_get_contents();
ob_end_clean();

$availableCols[$datasetGroup][$modApType][$modApHandle]=array(
	'scheme'=>array(
		'Print'=>array(
			/*tool*/
			'header'=>$printWithAllNone,
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Print")',
			'sortable'=>false,
		),
		'HeaderNumber'=>array(
			'header'=>'Inv #',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Number")',
			'orderBy'=>'CAST(HeaderNumber AS UNSIGNED)',
		),
		'Status'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Status")',
			'sortable'=>false,
		),
		'PropertyName'=>array(
			'header'=>'Property',
			'visibility'=>($_SESSION['userSettings']['invoicesfilterClients'] ? COL_HIDDEN : COL_VISIBLE),
		),
		'DateDueFrom'=>array(
			'header'=>'Due',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Gen:Date","DateDueFrom")',
		),
		'PrimaryTenant'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Tenant")',
			'orderBy'=>'c.LastName $asc, c.FirstName $asc',
		),
		'Agent'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Agent")',
			'nowrap'=>true,
			'colAttrib'=>array(
				'nowrap'=>'nowrap',
			),
			'orderBy'=>'Agents_username $asc',
		),
		'UnitAddress'=>array(
			'header'=>'Unit/<br />Addr',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:UnitAddress")',
		),
		'LeaseStartDate'=>array(
			'header'=>'Move-in',
			'datatype'=>'date',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("field:LeaseStartDate")',
		),
		'Rent'=>array(
			'header'=>'Rent<br />Amt',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("field:Rent")',
		),
		'Total'=>array(
			'header'=>'Invoice<br />Amt',
			'method'=>'function',
			/*
			'fieldExpressionFunction'=>'colConfig("Gen:Dollar", "OriginalTotal", 
			array(
				"negative"=>1, 
			))',
			*/
			'fieldExpressionFunction'=>'colConfig("field:OriginalTotal")',
			'colattribs'=>array(
				'id'=>'total_',
			),
		),
		'LateFee'=>array(
			'header'=>'Late<br />Fee',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:LateFee")',
			'sortable'=>false,
		),
		'AmountApplied'=>array(
			'header'=>'Prior<br />Pymt',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Applied")',
		),
		'Balance'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Balance")',
			'sortable'=>false,
		),
		'GC'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:GC")',
			'sortable'=>false,
		),
		'Void'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Void")',
			'sortable'=>false,
		),
		'Flag'=>array(
			'header'=>'New Discrepancy',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Discrepancy")',
		),
		'Verification_ID'=>array(
			'header'=>'Verify',
		),
	),
);

//appearance
$datasetTheme='';
$datasetFooterDisposition='tabularControls'; 	//however, the footer needs to show to nav a large batch like this
$datasetHideFooterAddLink=true;
$datasetHideColumnSelection=false;

ob_start();
?>
<div onmouseover="override_hidemenuie5=true;" onmouseout="override_hidemenuie5=false;">
<h3 class="nullBottom">Invoice <span id="latefeeInvoiceNbr"> </span></h3>
<br />
<label><input name="GLF_LateStatus" type="radio" value="-1" id="GLF_LateStatus-1" onchange="g('lateChargeUpdate').disabled=false;" /> 
Override</label><br />
<label><input name="GLF_LateStatus" type="radio" value="0" checked="checked" id="GLF_LateStatus0" onchange="g('lateChargeUpdate').disabled=false;" /> 
Use due date at 30 days</label><br />
<label><input name="GLF_LateStatus" type="radio" value="1" id="GLF_LateStatus1" onchange="g('lateChargeUpdate').disabled=false;" />
Assess late fee</label>
<br />
<br />
Charge of: 
<input name="GLF_LateCharge" type="text" id="GLF_LateCharge" value="<?php echo number_format($applicationLateFee,2);?>" size="7" onchange="g('lateChargeUpdate').disabled=false;" />
&nbsp;
<input type="button" name="Button" id="lateChargeUpdate" value="Update" disabled="disabled" onclick="lateCharge(this);" /> <span id="lateChargePending" style="display:none;"><img src="/images/i/ani/ani-gif-bars-ltgreen.gif" width="43" height="11" alt="processing.." /></span>
</div>
<?php
$inner=ob_get_contents();
ob_end_clean();

//--------------------------- functions only from here ------------------------------
if(!function_exists('write_menu'))require($FUNCTION_ROOT.'/function_write_menu_v100.php');

function dChgeField($n){
	//added 2011-10-31
	global $dChgeField;
	if(!$dChgeField[$n]){
		$dChgeField[$n]=true;
		?><input type="hidden" name="dChge[<?php echo $n?>]" id="dChge<?php echo $n?>" value="" /><?php
	}
}
function colConfig($param,$field='',$options=array()){
	global $record, $submode,$qr, $developerEmail, $fromHdrBugs, $modApType, $modApHandle;
	global $allInvoices, $thisLateFee, $applicationLateFee;
	$param=strtolower($param);
	$a=$record;
	extract($a);
	extract($options);
	ob_start();
	switch($param){
		case 'gen:dollar':
			if($absolute)$$field=abs($$field);
			if($negative)$$field *= -1;
			if($$field==0 && $nozero){
				if(is_string($nozero))echo $nozero;
				break;
			}
			echo (isset($currency) ? (is_string($currency) ? $currency : '$') : '').number_format($$field, (strlen($decimals)?$decimals : 2));
		break;
		case 'gen:date':
			echo t($$field, f_qbks);
		break;
		case 'inv:status':
			//this WAS in the late fee column but we need this for allInvoices
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
				echo 'VOID';
			}else if($OriginalTotal + $AmountApplied + $thisLateFee >= 0 /* and we have to consider the late fee here */){
				echo 'PAID';
			}else{
				if($AmountApplied > 0){
					echo 'PP/';
					$allInvoices['PP'][$Leases_ID]=$AmountApplied;
				}
				if($LeaseStartDate > date('Y-m-d')){
					echo $k='FI';
				}else if($DateDueFrom <= date('Y-m-d',strtotime('-30 days'))){
					echo $k='PASTD';
				}else{
					echo $k='DUE';
				}
				$allInvoices[$k][$Leases_ID]=abs($OriginalTotal + $AmountApplied) + $thisLateFee;
			}
			if(($GLF_DiscrepancyDate!='0000-00-00' && $GLF_DiscrepancyDate!='') || $GLF_DiscrepancyReason){
				$allInvoices['DIS'][$Leases_ID]=abs($OriginalTotal + $AmountApplied) + $thisLateFee;
			}
		break;
		case 'inv:tenant':
			echo $FirstName.' '.$LastName;
		break;
		case 'inv:agent':
			echo $un_firstname ? substr($un_firstname,0,1).'. '.$un_lastname : $Agents_username;
		break;
		case 'inv:unitaddress':
			dChgeField($Leases_ID);
			?><input type="text" name="formData[<?php echo $Leases_ID?>][UnitNumber]" value="<?php echo h($UnitNumber);?>" size="3" onchange="dChge(this,oro,<?php echo $Leases_ID;?>);" /><?php
		break;
		case 'field:leasestartdate':
			dChgeField($Leases_ID);
			?><input type="text" name="formData[<?php echo $Leases_ID?>][LeaseStartDate]" value="<?php echo t($LeaseStartDate, f_qbks);?>" size="8" onchange="dChge(this,oro,<?php echo $Leases_ID;?>);" /><?php
		break;
		case 'field:rent':
			dChgeField($Leases_ID);
			?><input type="text" name="formData[<?php echo $Leases_ID?>][Rent]" value="<?php echo number_format($Rent,2);?>" size="4" onchange="dChge(this,oro,<?php echo $Leases_ID;?>);" class="tar" /><?php
		break;
		case 'field:originaltotal':
			dChgeField($Leases_ID);
			?><input type="text" name="formData[<?php echo $Leases_ID?>][OriginalTotal]" value="<?php echo number_format(-$OriginalTotal,2);?>" size="4" onchange="dChge(this,oro,<?php echo $Leases_ID;?>);" class="tar" /><?php
		break;
		case 'inv:balance':
			$bal=abs($OriginalTotal + $AmountApplied)+$thisLateFee;
			if(($GLF_DiscrepancyDate!='0000-00-00' && $GLF_DiscrepancyDate!='') || $GLF_DiscrepancyReason){
				?><img src="/images/i/findicons.com-flag_red.png" width="16" height="16" alt="DIS" title="Discrepancy <?php echo date('n/j/Y',strtotime($GLF_DiscrepancyDate))?>: <?php echo h($GLF_DiscrepancyReason);?>" style="padding-right:4px;" /><?php
			}
			if($bal==0){
				?><span id="balance_<?php echo $Leases_ID;?>" class="gray">pd.</span><?php
			}else{
				$allInvoices['ACTIVE'][$Leases_ID]=$bal;
				?><span id="balance_<?php echo $Leases_ID;?>"><?php echo number_format($bal,2);?></span><?php
			}
		break;
		case 'inv:gc':
			if($GiftCard1>0)$gc[]=number_format($GiftCard1,0);
			if($GiftCard2>0)$gc[]=number_format($GiftCard2,0);
			if($gc){
				echo implode(',',$gc);
				if($GCBatch1){
					?><a href="root_giftcards.php?Batches_ID=<?php echo $GCBatch1;?>&hideHeader=1" title="View this batch (<?php echo $GCBatch1;?>)" onclick="return ow(this.href,'l1_gc','700,700');"><img src="/images/i/yes.gif" width="20" height="14" /></a><?php
				}
			}else{
				echo '&nbsp;';
			}
		break;
		case 'inv:void':
			?><select name="Status[<?php echo $ID?>]" id="void<?php echo $ID?>" onchange="dChge(this);setVoid(<?php echo $ID?>,this.value,g('IncludeVoids').checked)" class="smallSelect">
			<option value="">Select Reason</option>
			<?php
			global $voidreasons;
			if(!$voidreasons)$voidreasons=q("SELECT ID, Name FROM gl_voidreasons", O_COL_ASSOC);
			foreach($voidreasons as $n=>$v){
				?><option value="<?php echo $n?>" <?php echo $GLF_VoidReasons_ID==$n?'selected':''?>><?php echo h($v);?></option><?php
			}
			?>
			</select><?php
		break;
		case 'inv:discrepancy':
			$str= ($GLF_DiscrepancyDate!=='0000-00-00' ? date('n/j/Y',strtotime($GLF_DiscrepancyDate)). ': ':'').$GLF_DiscrepancyReason;
			echo strlen($str)?$str:'&nbsp;';
		break;
		case 'inv:number':
			?><a href="leases.php?Leases_ID=<?php echo $Leases_ID?>" onclick="return ow(this.href,'l1_leases','700,800');" title="click to view and edit this lease"><?php echo $HeaderNumber?></a><?php
		break;
		case 'inv:applied':
			if($Distributions>1){
				?><a id="pymt_<?php echo $Leases_ID?>" href="history.php?Headers_ID=<?php echo $ID?>" onclick="return ow(this.href,'l1_pymthistorybubble','500,230')" title="This invoice has multiple payments; click to view list"><?php echo number_format($AmountApplied,2);?></a><?php
			}else if($Distributions==1){
				?><a id="pymt_<?php echo $Leases_ID?>" href="payments.php?Payments_ID=<?php echo q("SELECT Headers_ID FROM finan_transactions WHERE ID='$ParentTransactions_ID'", O_VALUE);?>&FocusInvoices_ID=<?php echo $Leases_ID?>" onclick="return ow(this.href,'l1_payments','900,600');" title="Click to view this payment"><?php echo number_format($AmountApplied,2);?></a><?php
			}else{
				?><a id="pymt_<?php echo $Leases_ID?>" href="payments.php?Clients_ID=<?php echo $Clients_ID;?>&FocusInvoices_ID=<?php echo $Leases_ID?>" onclick="return ow(this.href,'l1_payments','900,600');" title="Click to view this payment" style="color:#888;">0.00</a><?php
			}
		break;
		case 'inv:latefee':				
			if($thisLateFee){
				//calculated under the status column
				?><span id="latefee_<?php echo $Leases_ID?>" class="red" oncontextmenu="return false;" onclick="hidemenuie5(event,1); showmenuie5(event,1);"><?php echo number_format($thisLateFee,2);?></span><?php
			}else if($GLF_LateStatus==-1){
				//override
				?><span id="latefee_<?php echo $Leases_ID?>" class="<?php if($DateDueFrom  < date('Y-m-d', strtotime('-30 days'))/*it is past due*/)echo 'red';?>" oncontextmenu="return false;" onclick="hidemenuie5(event,1); showmenuie5(event,1);">0.00</span><?php
			}else{
				?><div class="red" id="latefee_<?php echo $Leases_ID?>" oncontextmenu="return false;" onclick="hidemenuie5(event,1); showmenuie5(event,1);" style="float:right;">&nbsp;&nbsp;&nbsp;&nbsp;</div><?php
			}
			?><input type="hidden" id="GLF_LateStatusCharge<?php echo $Leases_ID?>" value="<?php echo $GLF_LateStatus.':'.$GLF_LateCharge.':'.$HeaderNumber;?>" /><?php
		break;
		case 'inv:print':
			?>
			<input type="checkbox" name="print[<?php echo $Leases_ID?>]" id="print_<?php echo $Leases_ID?>" value="1" />
<?php
		break;
		default:
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}

require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v104.php');


write_menu($options=array(
	'type'=>'toddler',
	'objectRegex'=>'^latefee_',
	'menuID'=>'definitionTools',
	'precalculated'=>'lateChargeInfo(event)',
	'inner'=>$inner,
));

ob_start();
//content including controls between titlage and dataset table
?>
<span class="gray">Please click the edit icon to access an invoice</span><br />
<?php
$datasetPreContent=get_contents();
?>
<table class="spacer fr">
	<tr>
	<td>
	[<a href="#" onclick="return listAction('printInvoices');">print</a> | <a href="#" onclick="return listAction('sendInvoices');">send</a>]
	</div>
	</td>
	<td>
	<select name="ClientList" id="ClientList" onchange="filterClients(this.value);">
		<option value="" style="font-style:italic;color:dimgray;">(all properties)</option>
		<?php
		$i=0;
		foreach($ClientList as $v){
			$i++;
			if(strtolower($v['Type'])!=strtolower($buffer1) || strtolower($v['HasLeases'])!=$buffer2 || $i==1){
				if($i>1)echo '</optgroup>';
				$buffer1=$v['Type'];
				$buffer2=$v['HasLeases'];
				?><optgroup label="<?php echo strtolower($buffer1)=='apt'?'Apartments':'Non-Apartments ('.$buffer1.')';?>"><?php
			}
			?><option value="<?php echo $v['Clients_ID']?>" <?php echo $_SESSION['userSettings']['invoicesfilterClients']==$v['Clients_ID']?'selected':''?>><?php echo h($v['Name']).($v['LeaseCount']?' ('.$v['LeaseCount'].')':'');?></option><?php
		}
		?></optgroup>
	</select>
	</td>
	<td>
	<label>
	<input type="checkbox" name="IncludeVoids" id="IncludeVoids" value="1" <?php echo $_SESSION['userSettings']['invoicesIncludeVoids']?'checked':''?> onclick="toggleVoids(this.checked);" /> 
	Voids 
	</label>
	</td>
	<td>
	<?php 
	echo $dueStatusOutput;
	?>
	</td>
	<td>
	<?php
	echo $paidStatusOutput;
	?>
	</td>
	<td>
	  <input name="bulkUpdate" type="submit" id="bulkUpdate" value="Update" onclick="g('submode').value='bulkUpdate';" disabled="disabled" />	</td>
	</tr>
</table>
<?php
$datasetPreSubContent=get_contents();

//html output items prior to dataset
if(!$refreshComponentOnly){
	?><style type="text/css">
	/* -- from filter gadget -- */
	<?php echo $filterGadgetCSS?>
	<?php
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'556b2f',
		'datasetColorRowAlt_'=>'99a682',
		'datasetColorSorted_'=>'wheat',
	));
	?>
	.frb{
		float:right;
		margin-left:5px;
		}
	.flb{
		float:left;
		margin-right:8px;
		}
	.smallSelect{
		width:95px;
		font-size:smaller;
		}
	.smallSelect option{
		font-size:13px;
		}
	.large{
		font-size:215%;
		font-weight:900;
		}
	.void td{
		text-decoration:line-through;
		/* background-color:mistyrose; */
		color:#555;
		}
	</style>
	<script language="javascript" type="text/javascript">
	function oro(a){
		//first dChge passthrough function 2011-10-31
		g('bulkUpdate').disabled=false;
		g('dChge'+a).value='1';
	}
	/* -- from filter gadget -- */
	<?php echo $filterGadgetJS?>
	function lateChargeInfo(e){
		if(!e) var e = window.event;
		e=GetSourceElement(e);
		SelectedLeases_ID=getParentMatching(e,/^r_/).id.replace('r_','');
		var a=g('GLF_LateStatusCharge'+SelectedLeases_ID).value.split(':');
		g('GLF_LateStatus'+(a[0]?a[0]:'0')).checked=true;
		g('GLF_LateCharge').value=(a[1]!='0.00' ? a[1] : '<?php echo number_format($applicationLateFee,2);?>');
		g('latefeeInvoiceNbr').innerHTML=a[2];
		g('lateChargePending').style.display='none';
	}
	function lateCharge(){
		g('lateChargePending').style.display='inherit';
		g('lateChargeUpdate').disabled=true;
		//get vars
		if(g('GLF_LateStatus-1').checked){
			GLF_LateStatus=-1;
		}else if(g('GLF_LateStatus0').checked){
			GLF_LateStatus=0;
		}else if(g('GLF_LateStatus1').checked){
			GLF_LateStatus=1;
		}
		window.open('/gf5/console/resources/bais_01_exe.php?mode=updateLateStatus&Leases_ID='+SelectedLeases_ID+'&GLF_LateStatus='+GLF_LateStatus+'&GLF_LateCharge='+escape(g('GLF_LateCharge').value), 'w2');
	}
	function checkAll(n){
		var a=g('form1').getElementsByTagName('input');
		for(var i in a){
			try{
			if(!a[i].id.match(/print_/))continue;
			a[i].checked=n;
			}catch(e){ }
		}
		return false;
	}
	function listAction(n){
		var a=g('form1').getElementsByTagName('input');
		var checked=0;
		for(var i in a){
			try{
			if(a[i].id.match(/print_/) && a[i].checked)checked++;
			}catch(e){ }
		}
		if(!checked){
			alert('You must have at least one invoice checked to print or email');
			return false;
		}
		g('submode').value=n;
		g('form1').submit();
		return false;
	}
	function toggleVoids(n){
		var url=('/gf5/console/resources/bais_01_exe.php?mode=refreshComponent&component=<?php echo $datasetComponent?>&changeParams[IncludeVoids]='+(n?1:0));
		window.open(url,'w2');
	}
	function filterClients(n){
		window.open('/gf5/console/resources/bais_01_exe.php?mode=refreshComponent&component=<?php echo $datasetComponent?>&submode=filterClients&Clients_ID='+n, 'w2');
	}
	function setVoid(n,o,p){
		if(o==''){
			alert('You cannot un-void an invoice');
			return;
		}else if(!confirm('Are you SURE you want to void this invoice?'))return;
		window.open('/gf5/console/resources/bais_01_exe.php?mode=refreshComponent&component=<?php echo $datasetComponent?>&submode=setVoid&Headers_ID='+n+'&GLF_VoidReasons_ID='+o+'&IncludeVoids='+(p?1:0),'w2');
	}
	</script>
<?php
}
require($MASTER_COMPONENT_ROOT.'/dataset_component_v124.php');
?></form><?php
if($refreshComponentOnly){
	?><script language="javascript" type="text/javascript">
	window.parent.g('selWList1').innerHTML=document.getElementById('selWList1').innerHTML;
	window.parent.g('selWList2').innerHTML=document.getElementById('selWList2').innerHTML;
	</script><?php
}

?>