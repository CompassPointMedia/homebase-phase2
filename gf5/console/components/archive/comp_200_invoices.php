<?php
/*
2010-11-20: Pulled from list children in G iocosC are

hire date - have -- for 0000-00-00
track and have un_username= be set in place

*/
$dataset='Invoices'; 							#more of a concept
$datasetComponent='invoiceList'; 				#THIS physical component
$datasetGroup=$dataset; 						//as of 2010-04-04, this is not used
if(!$datasetWord)$datasetWord='Invoice';
if(!$datasetWordPlural)$datasetWordPlural='Invoices';
$datasetFocusPage='leases.php';
$datasetAddObjectJSFunction='ow(this.href,\'l1_units\',\'800,700\',true);'; //this is because opening an object is not well developed yet
$datasetQueryStringKey='Leases_ID';
$datasetDeleteMode='deleteInvoices';

$datasetQuery=''; 							//this is left blank for list_members; needed because a view didn't contain the same data as a query!
$datasetTable='_v_y_finan_invoices_mapsahead';		//this can be a single MySQL table or a view
$datasetTableIsView=true;
$datasetArrayType=O_ARRAY;				//added 2010-05-10 - this allows for non-standard left-column-equals-primary-key constructions; default=O_ARRAY_ASSOC
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
		'column'=>'PropertyName',
		'blank'=>'not specified',
		'label'=>'function:propertyHeading()',
	),
);
function propertyHeading(){
	global $record,$nextRecord,$section;
	$a=($section=='top' || !$nextRecord ? $record : $nextRecord);
	extract($a);
	ob_start();
	?><a href="properties3.php?Properties_ID=<?php echo $Properties_ID?>" title="View/edit this property" onclick="return ow(this.href,'l1_properties','800,650');"><img src="/images/i/edit2.gif" /> <?php echo $PropertyName?></a><?php
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}
$focusViewDeviceFunction='';							#not used initially
$datasetAdditionalClassFunction='';						#not used initially
$datasetHideCount=true;
$datasetOverrideSort='';								#not used initially

function user_defined_column($param, $codeblock=''){
	global $record, $submode,$qr;
	$a=$record;
	extract($a);
	ob_start();
	switch($param){
		case 'something':
			if($codeblock){
				eval($codeblock);
			}else{
				echo 'something';
			}
		break;
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}
function invoices($param){
	global $record, $submode,$qr;
	$a=$record;
	extract($a);
	ob_start();
	switch($param){
		case 'ctrls':
			?>
			[<a title="View this lease" onclick="return ow(this.href,'l2_leases','700,700');" href="leases.php?Leases_ID=<?php echo $Leases_ID?>">view lease</a>]&nbsp;
			[<a title="Receive a payment for this invoice" onclick="return ow(this.href,'l1_payments','850,500');" href="payments.php?Clients_ID=<?php echo $Clients_ID?>">rec. pymt</a>]
			 
			<?php
		break;
		case 'OriginalTotal':
			echo number_format(-$OriginalTotal,2);
		break;
		case 'ClientName':
			?><a href="clients.php?Clients_ID=<?php echo $Clients_ID?>" onclick="return ow(this.href,'l1_clients','700,700');"><?php echo h($ClientName);?></a><?php
		break;
		case 'Balance':
			echo number_format(-$AmountApplied-$OriginalTotal,2);
		break;
		case 'Aging':
			echo (strtotime(date('Y-m-d'))-strtotime($HeaderDate))/(3600*24);
		break;
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}
//declare the properties of the dataset->component
$availableCols[$datasetGroup][$modApType][$modApHandle]=array(
	'scheme'=>array(
		'ctrls'=>array(
			'header'=>'&nbsp;&nbsp;',
			'method'=>'function',
			'fieldExpressionFunction'=>'invoices("ctrls")',
		),
		'HeaderDate'=>array(
			'header'=>'Date',
		),
		'HeaderNumber'=>array(
			'header'=>'Num.',
		),
		'ClientName'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'invoices("ClientName")',
		),
		'OriginalTotal'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'invoices("OriginalTotal")',
		),
		'Aging'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'invoices("Aging")',
		),
		'Balance'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'invoices("Balance")',
		),
		
	)
);


$datasetActiveUsage=false;
$hideObjectInactiveControl=true;

//allow this parameter to be passed remotely
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
	#invoiceList_heading{
		display:none;
		}
		
	<?php
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'8b4513',
		'datasetColorRowAlt_'=>'f1e8e2',
		'datasetColorSorted_'=>'wheat',
	));
	?>
	</style>
	<script language="javascript" type="text/javascript">
	/* -- from filter gadget -- */
	<?php echo $filterGadgetJS?>
	</script><?php
}
if(!$datasetFetchSettings)require($MASTER_COMPONENT_ROOT.'/dataset_component_v123.php');
?>