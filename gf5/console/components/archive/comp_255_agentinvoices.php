<?php
/*
2010-11-20: Pulled from list children in G iocosC are

hire date - have -- for 0000-00-00
track and have un_username= be set in place

*/
#$datasetDebug['query']=md5($MASTER_PASSWORD);
$dataset='Invoices'; 									#more of a concept
$datasetComponent='InvoiceListAgent'; 						#THIS physical component
$datasetGroup=$dataset; 							//as of 2010-04-04, this is not used
if(!$datasetWord)$datasetWord='Invoice';
if(!$datasetWordPlural)$datasetWordPlural='Invoices';
$datasetFocusPage='leases.php';
$datasetAddObjectJSFunction='ow(this.href,\'l1_leases\',\'800,700\',true);'; //this is because opening an object is not well developed yet
$datasetQueryStringKey='Leases_ID';
$Dataset_ID='Leases_ID';
$datasetDeleteMode='deleteInvoice';

$datasetQuery='SELECT * FROM `_v_y_finan_invoices_mapsahead` where amountapplied + originaltotal !=0 AND Agents_username=\''.$Agents_username.'\''; 
$datasetQueryValidation=md5($MASTER_PASSWORD);

$datasetTable='';		//this can be a single MySQL table or a view
$datasetTableIsView=true;
$datasetArrayType=O_ARRAY_ASSOC;					//added 2010-05-10 - this allows for non-standard left-column-equals-primary-key constructions; default=O_ARRAY_ASSOC
$datasetFieldList='*';
$modApType='embedded';
$modApHandle='first';
$globalBatchThreshold='10000';

$datasetTheme='report';
$footerDisposition='tabularControls'; 				//however, the footer needs to show to nav a large batch like this
$datasetHideFooterAddLink=true;
$hideColumnSelection=false;

//2010-06-03: for gf_children this is not used initially
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
		case 'therapists':
			if(minroles() < ROLE_CLIENT){
				$focusViewURL = 'therapists.php?Therapists_ID='.$ID;
				$focusViewTitle = 'View this therapists\'s info';
				$focusViewSelfName = 'l1_therapists';
				$focusViewSize = '850,700';
			}
		break;
		case 'household member':
			//edit the home they are a part of - no focus
			if($Fosterhomes_ID=q("SELECT a.ID
				FROM gf_fosterhomes a, gf_objects b WHERE
				b.ParentObject='gf_fosterhomes' AND b.Objects_ID=a.ID AND b.ID=$ID",O_VALUE)){
				$focusViewURL = 'homes.php?Fosterhomes_ID='.$Fosterhomes_ID;
				$focusViewTitle = 'View this foster home and household member\'s info';
				$focusViewSelfName = 'l1_homes';
				$focusViewSize = '815,750';
			}
		break;
		case '':
		case 'caregiver':
		case 'non-fostex':
			$focusViewURL = 'subcontractors.php?Subcontractors_ID='.$ID;
			$focusViewTitle = 'View this '.($Relationship=='non-fostex' || !$Relationship ? 'person' : strtolower($Relationship)).'\'s info';
			$focusViewSelfName = 'l1_subcontractors';
			$focusViewSize = '850,700';
		break;
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
		case 'Status':
			if(abs($OriginalTotal+$AmountApplied)>0){
				?><a title="Pay on this invoice" onclick="return ow(this.href,'l1_payments','850,500');" href="payments.php?Clients_ID=<?php echo $Clients_ID?>">pay</a><?php
			}
		break;
		case 'HeaderDate':
			echo t($HeaderDate);
		break;
		case 'Escorted':
			echo $Escorted==3?'Y':'N';
		break;
		case 'OriginalTotal':
			echo number_format(-$OriginalTotal,2);
		break;
		case 'Name':
			echo trim(implode(' ', array($PrimaryFirstName, $PrimaryLastName)));	
			if($Email)echo '<br /><a href="mailto:'.$Email.'">'.$Email.'</a>';
		break;
		case 'AmountApplied':
			if($ParentTransactions_ID && $Distributions==1){
				$Payments_ID=q("SELECT Headers_ID FROM finan_transactions WHERE ID=$ParentTransactions_ID", O_VALUE);
				?><a href="payments.php?Payments_ID=<?php echo $Payments_ID?>" title="edit this payment" onclick="return ow(this.href,'l1_payments','850,500');"><?php
			}
			echo $AmountApplied ? number_format($AmountApplied,2) : '&nbsp;';
			if($ParentTransactions_ID && $Distributions==1){
				?></a><?php
			}
		break;
		case 'Balance':
			echo number_format(-$OriginalTotal - $AmountApplied,2);
		break;
		case 'SubSplit':
			echo $SubSplit>0 ? number_format($SubSplit,2) : '&nbsp;';
		break;
		case 'TenantName':
			echo $FirstName. ' ' . $LastName. ($ContactCount>1 ? ' <em>(more)</em>':'');
			if($ContactEmail)echo '<br />'.$ContactEmail;
		break;
		case 'ClientName':
			?><a title="view this property" href="properties3.php?Units_ID=<?php echo $Units_ID; ?>" onclick="return ow(this.href,'l1_property','700,700');"><?php echo $ClientName?></a><?php
		break;
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}

//declare the properties of the dataset->component
if(false){
	if($datasetTable){
		$a=q("SELECT * FROM $datasetTable LIMIT 1", O_ROW);
	}else if($datasetQuery){
		ob_start();
		$a=q(preg_replace('/LIMIT\s+[ ,0-9]+$/i','',stripslashes($datasetQuery)).' LIMIT 1', O_ROW, ERR_ECHO);
		$err=ob_get_contents();
		ob_end_clean();
		if($err) error_alert('your syntax is incorrect for the string dataset query; click the test link to test out the query');
	}else{
		error_alert('To create initial columns you need either a string query or table selected (Data Source tab)');
	}
	if(!$a)error_alert('Currently you need at least one record to be returned from you table or query to create initial columns');
	if(!$qr['output']){
		mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals($err='unable to get qr.output in building the initial columns for a dataset'),$fromHdrBugs);
		error_alert($err);
	}
	//unset previous availableCols
	unset($_SESSION['special']['datasets'][$Location]['availableCols'][$datasetGroup][$modApType][$modApHandle]['scheme']);
	foreach($qr['output'] as $n=>$v){
		$_SESSION['special']['datasets'][$Location]['availableCols'][$datasetGroup][$modApType][$modApHandle]['scheme'][$n]=array();
	}
}else{
$availableCols[$datasetGroup][$modApType][$modApHandle]=array(
	'scheme'=>array(
		'HeaderNumber'=>array(
			'header'=>'Inv#',
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
	)
);
}

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