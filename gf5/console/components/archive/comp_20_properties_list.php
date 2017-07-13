<?php
/*
2010-11-20: Pulled from list children in G iocosC are

hire date - have -- for 0000-00-00
track and have un_username= be set in place

*/
$dataset='Units'; 							#more of a concept
$datasetComponent='unitList'; 				#THIS physical component
$datasetGroup=$dataset; 						//as of 2010-04-04, this is not used
if(!$datasetWord)$datasetWord='Unit';
if(!$datasetWordPlural)$datasetWordPlural='Units';
$datasetFocusPage='units.php';
$datasetAddObjectJSFunction='ow(this.href,\'l1_units\',\'800,700\',true);'; //this is because opening an object is not well developed yet
$datasetQueryStringKey='Units_ID';
$datasetDeleteMode='deleteUnits';

$datasetQuery=''; 								//this is left blank for list_members; needed because a view didn't contain the same data as a query!
$datasetTable='_v_properties_master_list';		//this can be a single MySQL table or a view
$datasetTableIsView=true;
$datasetArrayType=O_ARRAY_ASSOC;				//added 2010-05-10 - this allows for non-standard left-column-equals-primary-key constructions; default=O_ARRAY_ASSOC
$datasetFieldList='*';
$modApType='embedded';
$modApHandle='first';
$globalBatchThreshold='10000';

$datasetTheme='report';
$footerDisposition='tabularControls'; 				//however, the footer needs to show to nav a large batch like this
$datasetHideFooterAddLink=true;
$hideColumnSelection=false;

//2010-06-03: for gf_children this is not used initially
$datasetShowBreaks=true;
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
$datasetInternalFilter="Clients_ID=$Clients_ID";

$datasetHideCount=true;

$datasetOverrideSort='';								#not used initially

function prlist($param){
	global $record, $submode,$qr;
	$a=$record;
	extract($a);
	ob_start();
	switch($param){
		case 'PropertyName':
			?><a href="properties3.php?Properties_ID=<?php echo $Properties_ID?>" title="View/edit this property" onclick="return ow(this.href,'l1_properties','800,650');"><?php echo $PropertyName;?></a><?php
		break;
		case 'Units':
			echo $Quantity;
		break;
		case 'SquareFeet':
			echo $SquareFeet;
		break;
		case 'Bedrooms':
			echo preg_replace('/\.0+$/','',$Bedrooms);
		break;
		case 'Bathrooms':
			echo preg_replace('/\.0+$/','',$Bathrooms);
		break;
		case 'Rent':
			echo number_format($Rent,2);
		break;
		case 'Occupancy':
			?><a href="reports_occupancy.php?Properties_ID=<?php echo $Properties_ID?>&Units_ID=<?php echo $ID?>"><?php echo $Occupancy;?> tenant<?php echo $Occupancy==1?'':'s'?></a><?php
		break;
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}

//declare the properties of the dataset->component
$availableCols[$datasetGroup][$modApType][$modApHandle]=array(
	'scheme'=>array(
		'PropertyName'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'prlist("PropertyName")',
			'orderBy'=>'PropertyName $asc',
			'visibility'=>COL_AVAILABLE,
		),
		'Units'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'prlist("Units")',
			'orderBy'=>'Units $asc',
			'colattribs'=>array(
				'align'=>'right',
			),
		),
		'SquareFeet'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'prlist("SquareFeet")',
			'orderBy'=>'SquareFeet $asc',
			'colattribs'=>array(
				'align'=>'right',
			),
		),
		'Bedrooms'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'prlist("Bedrooms")',
			'orderBy'=>'Bedrooms $asc',
			'colattribs'=>array(
				'align'=>'right',
			),
		),
		'Bathrooms'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'prlist("Bathrooms")',
			'orderBy'=>'Bathrooms $asc',
			'colattribs'=>array(
				'align'=>'right',
			),
		),
		'Rent'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'prlist("Rent")',
			'orderBy'=>'Rent $asc',
			'colattribs'=>array(
				'align'=>'right',
			),
		),
		'Occupancy'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'prlist("Occupancy")',
			'orderBy'=>'ClientName $asc',
			'colattribs'=>array(
				'align'=>'center',
			),
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
	?><style type="text/css">
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
	#unitList_heading{
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