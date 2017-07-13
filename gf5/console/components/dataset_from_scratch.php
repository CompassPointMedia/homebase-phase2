<?php
























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



/*
2. next we declare the datasetTable; if datasetFieldList=* then no initial sort for user will be declared
	- datasetFieldList optional, default value will be *; better to use a comma separated list
	- datasetTable (table or view)
	- datasetQuery (alternate method, leave blank if not used)
	- datasetID default=ID - primary key of the dataset
	- datasetArrayType default=O_ARRAY_ASSOC
*/
$datasetTable='_v_invoices_report';
$datasetID='ID';
$datasetQuery='';
$datasetArrayType=O_ARRAY_ASSOC;

/*
***** AT THIS POINT THE DATASET WILL BEGIN TO SHOW THE "NATURAL" COLUMNS *****
	- active/inactive column
	- delete rows
	- focus control
	- default colors and alt highlighting
	- hightlight select

*/
//active/inactive toggle
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
$datasetAdditionalClassFunction='';		//not used initially


//batching
$datasetDefaultBatch=1000;				//will be 50 if not declared, or if globalBatchThreshold not declared


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
$availableCols[$datasetGroup][$modApType][$modApHandle]=array(
	'scheme'=>array(
		'Print'=>array(
			/*tool*/
		),
		'HeaderNumber'=>array(
			'header'=>'Inv #',
		),
		'Status'=>array(

		),
		'PrimaryTenant'=>array(

		),
		'Agent'=>array(

		),
		'UnitAddress'=>array(
			'header'=>'Unit/Addr',
		),
		'MoveIn'=>array(
			'header'=>'Move-in',
		),
		'Rent'=>array(
			'header'=>'Rent Amt',
		),
		'Total'=>array(
			'header'=>'Invoice Amt',
		),
		'LateFee'=>array(
		),
		'PaymentsApplied'=>array(
			'header'=>'Prior Pymt',
		),
		'Balance'=>array(

		),
		'Void'=>array(

		),
		'Flag'=>array(
			'header'=>'New Discrepancy',
		),
		'Verification'=>array(
			'header'=>'Verify',
		),
	),
);

//appearance
$datasetTheme='';
$datasetFooterDisposition='tabularControls'; 	//however, the footer needs to show to nav a large batch like this
$datasetHideFooterAddLink=true;
$datasetHideColumnSelection=false;

//--------------------------- functions only from here ------------------------------
function colConfig($n,$options=array()){
	global $record, $submode,$qr, $developerEmail, $fromHdrBugs;
	$a=$record;
	extract($a);
	ob_start();
	switch($param){
		case 'Name':
			
		break;
		default:
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}

require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v104.php');


ob_start();
//content including controls between titlage and dataset table

$datasetPreContent=get_contents();

//html output items prior to dataset
if(!$refreshComponentOnly){
	?><style type="text/css">
	/* -- from filter gadget -- */
	<?php echo $filterGadgetCSS?>
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


require($MASTER_COMPONENT_ROOT.'/dataset_component_v124.php');

?>