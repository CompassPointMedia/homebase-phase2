<?php
/*
2010-11-20: Pulled from list children in G iocosC are

hire date - have -- for 0000-00-00
track and have un_username= be set in place

*/
#$datasetDebug['query']=md5($MASTER_PASSWORD);
$dataset='Payments'; 									#more of a concept
$datasetComponent='PaymentList'; 						#THIS physical component
$datasetGroup=$dataset; 							//as of 2010-04-04, this is not used
if(!$datasetWord)$datasetWord='Payment';
if(!$datasetWordPlural)$datasetWordPlural='Payments';
$datasetFocusPage='payments.php';
$datasetAddObjectJSFunction='ow(this.href,\'l1_payments\',\'850,500\',true);'; //this is because opening an object is not well developed yet
$datasetQueryStringKey='Payments_ID';
$Dataset_ID='ID';
$datasetDeleteMode='deletePayment';

$datasetQuery=''; 
//$datasetQueryValidation=md5($MASTER_PASSWORD);

$datasetTable=($useTable=='_v_mapsahead_agents_checks_split'?'_v_mapsahead_agents_checks_split':'_v_mapsahead_agents_checks_commission');
$datasetTableIsView=true;
$datasetArrayType=O_ARRAY_ASSOC;
$datasetFieldList='*';
$modApType='embedded';
$modApHandle='first';
$globalBatchThreshold='10000';

$datasetTheme='report';
$footerDisposition='tabularControls'; 				//however, the footer needs to show to nav a large batch like this
$datasetHideFooterAddLink=true;
$hideColumnSelection=false;

$datasetInternalFilter="Agent='$Agent' AND PaymentDate BETWEEN ".($ReportDateFrom?"'".$ReportDateFrom."'":'DATE_ADD(CURDATE() INTERVAL -30 DAY)')." AND ".($ReportDateTo?"'".$ReportDateTo."'" : 'CURDATE()');
$focusViewDeviceFunction='';							#not used initially
$datasetAdditionalClassFunction='';						#not used initially
function editLink($record){
	//created 2010-05-11
	global $editLink;
	unset($editLink);
	extract($record);
	switch(strtolower($Relationship)){
		case 'foster parent':
			$focusViewURL = 'parents.php?Parents_ID='.$ID;
			$focusViewTitle = 'View this foster parent\'s info';
			$focusViewSelfName = 'l1_parents';
			$focusViewSize = '850,700';
		break;
		case 'staff':
			if(minroles() < ROLE_CLIENT){
				$focusViewURL = 'staff.php?un_username='.$ID;
				$focusViewTitle = 'View this staff member\'s info';
				$focusViewSelfName = 'l1_pds';
				$focusViewSize = '800,700';
			}
		break;
		default:
	}
	//globalize component parameters
	$editLink=array(
		'focusViewURL' => $focusViewURL,
		'focusViewTitle' => $focusViewTitle,
		'focusViewSelfName' => $focusViewSelfName,
		'focusViewSize' => $focusViewSize
	);
	if($focusViewURL){
		?><a href="<?php echo $focusViewURL?>" title="<?php echo $focusViewTitle?>" onclick="return ow(this.href,'<?php echo $focusViewSelfName?>','<?php echo $focusViewSize?>');"><img src="/images/i/s/hlw-25x25-9EA9B4/edit-color.png" width="25" height="25" alt="edit" /></a><?php
	}else{
		?><img src="/images/i/spacer.gif" width="25" height="25" alt="  " /><?php
	}
}
function addClass($record){
	//no CBC, due within 60 days, due within 30 days, past due, pending, resolved, failed
	extract($record);
}

$datasetOverrideSort='';								#not used initially

function genlist($param){
	global $record, $submode,$qr;
	$a=$record;
	extract($a);
	ob_start();
	switch($param){
		case 'PaymentNumber':
			if($PaymentCount==1){
				?><a href="payments.php?Payments_ID=<?php echo $Payments_ID?>" title="Edit this payment" onclick="return ow(this.href,'l2_payments','850,500');"><?php echo $PaymentNumber; ?></a><?php
			}else{
				?>multiple:<?php
			}
		break;
		case 'HeaderNumber':
			?><a href="leases.php?Leases_ID=<?php echo $Leases_ID?>" title="View or edit this lease" onclick="return ow(this.href,'l1_leases','700,700');"><?php echo $Invoices_ID?></a><?php
		break;
		case 'PropertyName':
			?><a href="properties<?php echo strtolower($Type)=='sfr'?'2':'3'?>.php?Properties_ID=<?php echo $Properties_ID?>" title="View or edit this property" onclick="return ow(this.href,'l1_leases','800,700');"><?php echo $PropertyName?></a><?php
		break;
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}

//declare the properties of the dataset->component
$availableCols[$datasetGroup][$modApType][$modApHandle]=array(
	'scheme'=>array(
		'HeaderNumber'=>array(
			'header'=>'Invoice#',
			'method'=>'function',
			'fieldExpressionFunction'=>"genlist('HeaderNumber')",
		),
		'HeaderDate'=>array(
			'header'=>'Date',
		),
		'PropertyName'=>array(
			'header'=>'Property',
			'method'=>'function',
			'fieldExpressionFunction'=>"genlist('PropertyName')",
		),
		'InvoiceAmount'=>array(
			'header'=>'Amount',
		),
		'PaymentNumber'=>array(
			'header'=>'Pd.Ck#',
			'method'=>'function',
			'fieldExpressionFunction'=>"genlist('PaymentNumber')",
		),
		'AmountApplied'=>array(
			'header'=>'Pd.Amt.',
		),
		'PaymentDate'=>array(
			'header'=>'On Date',
		),
	/*
		'PropertyName'=>array(
			'header'=>'Client',
		),
		'HeaderNumber'=>array(
			'header'=>'Invoice',
		),
		'HeaderDate'=>array(
			'header'=>'Date',
			'method'=>'function',
			'fieldExpressionFunction'=>'genlist("HeaderDate")',
		),
		'ClientName'=>array(
			'header'=>'Property',
			'nowrap'=>true,
			'method'=>'function',
			'fieldExpressionFunction'=>'genlist("ClientName")',
		),
		'Rent'=>array(

		),
		'OriginalTotal'=>array(
			'header'=>'Inv.<br />Amt.',
			'method'=>'function',
			'fieldExpressionFunction'=>'genlist("OriginalTotal")',
		),
		'AmountApplied'=>array(
			'header'=>'Amt.<br />Paid',
			'method'=>'function',
			'fieldExpressionFunction'=>'genlist("AmountApplied")',
		),
		'Status'=>array(
			'header'=>'&nbsp;',
			'method'=>'function',
			'fieldExpressionFunction'=>'genlist("Status")',
			'nowrap'=>true,
		),



		'Name'=>array(
			'header'=>'Name',
			'method'=>'function',
			'fieldExpressionFunction'=>'genlist("Name")',
			'visibility'=>COL_AVAILABLE,
		),
		'TentantName'=>array(
			'header'=>'Tenant',
			'method'=>'function',
			'fieldExpressionFunction'=>'genlist("TentantName")',
			'visibility'=>COL_AVAILABLE,
		),
		'Escorted'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'genlist("Escorted")',
			'visibility'=>COL_AVAILABLE,
		),
		'Balance'=>array(
			'header'=>'Balance',
			'method'=>'function',
			'fieldExpressionFunction'=>'genlist("Balance")',
			'visibility'=>COL_AVAILABLE,
		),
		'Agents_username'=>array(
			'header'=>'Agent',
			'visibility'=>COL_AVAILABLE,
		),
		'SubAgents_username'=>array(
			'header'=>'Split<br />Agent',
		),
		'SubSplit'=>array(
			'header'=>'Split',
			'method'=>'function',
			'fieldExpressionFunction'=>'genlist("SubSplit")',
		),
		'Verified'=>array(
			'header'=>'Verified',
			'visibility'=>COL_AVAILABLE,
		),
	*/
	)
);

//2010-06-03 converted active/inactive to discharged/in care
$datasetActiveUsage=false;
$datasetActiveActiveExpression='st_active=1';
$datasetActiveInactiveExpression='st_active=0';
$datasetActiveAllExpression='1';
$datasetActiveField='st_active';
//allow this parameter to be passed remotely
if(!isset($hideObjectInactiveControl))$hideObjectInactiveControl=true;
$datasetActiveControl='';
$datasetActiveActivateTitle='';
$datasetActiveInactivateTitle='';
$datasetShowDeletion=false;

if(!$datasetFetchSettings)require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v103.php');

//not used yet
ob_start();
$datasetPreContent=get_contents();

//html output items
if(!$refreshComponentOnly){
	?>
<style type="text/css">
	/* -- from filter gadget -- */
	<?php echo $filterGadgetCSS?>
	.frb{
		float:right;
		margin-left:5px;
		}
	.flb{
		float:left;
		margin-right:8px;
		}
	.frb a, .flb a{
		color:#000;
		}
	#PaymentList_heading{
		display:none;
		}
	<?php
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'8b4513',
		'datasetColorRowAlt_'=>'f1e8e2',
		'datasetColorSorted_'=>'wheat',
		'datasetColorHighlight_'=>'b0c4c5',
	));
	?>
	</style>
	<script language="javascript" type="text/javascript">
	function datasetSort(component,col,e){
		if(typeof e== "undefined")e=window.event;
		sortCtrl=0;sortShift=0;sortAlt=0;
		if(e.ctrlKey)sortCtrl=1;
		if(e.shiftKey)sortShift=1;
		if(e.altKey)sortAlt=1;
		window.open('resources/bais_01_exe.php?test=7&mode=refreshComponent&component='+component+'&sort='+col+'&sortAlt='+sortAlt+'&sortCtrl='+sortCtrl+'&sortShift='+sortShift+'&Agents_username=<?php echo $Agents_username?>','w2');
		return false;
	}

	/* -- from filter gadget -- */
	<?php echo $filterGadgetJS?>
	</script><?php
}
if(!$datasetFetchSettings)require($MASTER_COMPONENT_ROOT.'/dataset_component_v123.php');
?>