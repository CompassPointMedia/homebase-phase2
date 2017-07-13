<?php
/*
2010-11-20: Pulled from list children in G iocosC are

hire date - have -- for 0000-00-00
track and have un_username= be set in place

*/
$dataset='properties'; 									#more of a concept
$datasetComponent='propertiesResults'; 						#THIS physical component
$datasetGroup=$dataset; 							//as of 2010-04-04, this is not used
if(!$datasetWord)$datasetWord='Property';
if(!$datasetWordPlural)$datasetWordPlural='Properties';
$datasetFocusPage='properties2.php';
$datasetAddObjectJSFunction='ow(this.href,\'l1_properties\',\'800,700\',true);'; //this is because opening an object is not well developed yet
$datasetQueryStringKey='Units_ID';
$datasetDeleteMode='deleteProperty';

//$datasetDebug['query']=md5($MASTER_PASSWORD);

//$datasetQuery=str_replace('[fieldlist]','m.*, COUNT(DISTINCT l.ID ) AS Leases',$sql);	//helpful on a search because of the variable complex query
$datasetQueryValidation=md5($MASTER_PASSWORD);
$datasetTable='_v_properties_master_list';			//this can be a single MySQL table or a view
$datasetTableIsView=true;
$datasetArrayType=O_ARRAY_ASSOC;					//added 2010-05-10 - this allows for non-standard left-column-equals-primary-key constructions; default=O_ARRAY_ASSOC
$datasetFieldList='*';
$modApType='embedded';
$modApHandle='first';
$globalBatchThreshold='10000';

$datasetTheme='';
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

$datasetAdditionalClassFunction='';	
$focusViewDeviceFunction='editLink';
function editLink($record){
	//created 2010-05-11
	global $editLink;
	unset($editLink);
	extract($record);
	$focusViewURL = (strtolower($Type)=='apt'?'properties3':'properties2').'.php?'.($ID ? 'Units_ID='.$ID : 'Properties_ID='.$Properties_ID);
	$focusViewTitle = 'View page for this '.(strtolower($Type)=='apt'?'apartment':'property');
	$focusViewSelfName = 'l1_properties';
	$focusViewSize = '700,700';
	//globalize component parameters
	$editLink=array(
		'focusViewURL' => $focusViewURL,
		'focusViewTitle' => $focusViewTitle,
		'focusViewSelfName' => $focusViewSelfName,
		'focusViewSize' => $focusViewSize
	);
	//output the link
	?><a id="editlink_<?php echo $ID?>" href="<?php echo $focusViewURL?>" title="<?php echo $focusViewTitle?>" onclick="return ow(this.href,'<?php echo $focusViewSelfName?>','<?php echo $focusViewSize?>');"><img src="/images/i/edit2.gif" width="15" height="18" alt="edit" /></a><?php
}

function prlist($param){
	global $apSettings;
	global $record, $submode,$qr;
	$a=$record;
	extract($a);
	ob_start();
	switch($param){
		case 'Address':
			global $staffOffices;
			if(!$staffOffices){
				$staffOffices=array(
					array(
						'Address'=>$AcctAddress,
						'City'=>'San Marcos',
						'State'=>'TX',
						'Zip'=>'78666',
					),
				);
			}
			$offc=$staffOffices[0];
			$googleURL='http://maps.google.com/maps?f=d&source=s_d&saddr='.urlencode($offc['Address']).',+'.urlencode($offc['City']).',+'.urlencode($offc['State']).'+'.urlencode($offc['Zip']).'&daddr='.urlencode($record['PropertyAddress']).',+'.urlencode($record['PropertyCity']).',+'.urlencode($record['PropertyState']).'+'.urlencode($record['PropertyZip']).',+United+States&hl=en&mra=ls&g='.urlencode($record['PropertyAddress']).',+'.urlencode($record['PropertyCity']).',+'.urlencode($record['PropertyState']).'+'.urlencode($record['PropertyZip']).',+United+States&ie=UTF8&z=12';
			?><div style="float:left;padding-right:7px;"><a title="Click here for a Google Map" onclick="return ow(this.href, 'l1_maps','750,600');" href="<?php echo $googleURL?>"><img src="/images/i/gmapicon.jpg" alt="child" width="16" height="16" border="0" /></a></div>
			<div style="float:left;">
			<?php echo h($record['PropertyAddress']); ?><br />
			<?php echo h($record['PropertyCity'].($record['PropertyState']!='TX' ? ', '.$record['PropertyState']:'').' '. ($apSettings['showZipNormally'] ? $record['PropertyZip'] : ''));?>
			</div><?php
		break;
		case 'FloorPlan':
			if($PictureCount){
				echo $PictureCount;
			}else{
				?>&nbsp;<?php
			}
		break;
		case 'Phones':
			if($Mobile)echo $Mobile.' (m)<br />';
			if($Phone)echo $Phone .' (p)<br />';
			if($Phone2)echo $Phone2 .' (p2)<br />';
			if($ContactEmail){
				?><a href="mailto:<?php echo $Email?>"><?php echo $Email;?></a><?php
			}
		break;
		case 'Status':
			?><a title="Lease this property or view current lease" onclick="return ow(this.href,'l2_leases','700,700');" href="leases.php?Units_ID=<?php echo $ID?>&Leases_ID=<?php 
			//Lease ID - only for an SFR
			if(strtolower($Type)=='sfr'){
				echo $CurrentLeases_ID?$CurrentLeases_ID:$FutureLeases_ID;
			}
			?>"><?php
			//anchor text
			if(strtolower($Type)=='sfr'){
				if($CurrentLeases_ID || $FutureLeases_ID){
					echo 'Leased-View';
				}else{
					echo 'Lease';
				}
			}else{
				echo 'Lease';
			}
			?></a><?php
		break;
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}
//declare the properties of the dataset->component
$availableCols[$datasetGroup][$modApType][$modApHandle]=array(
	'scheme'=>array(
		'FloorPlan'=>array(
			'header'=>'<img src="/images/i/misc/floorplan1.gif" alt="fp" width="29" height="31" />',
			'method'=>'function',
			'fieldExpressionFunction'=>'prlist("FloorPlan")',
			'sortable'=>false,
		),
		'Type'=>array(

		),
		'PropertyName'=>array(
			'header'=>'Name',
		),
		'Bedrooms'=>array(
			'header'=>'Bd.',
		),
		'Bathrooms'=>array(
			'header'=>'Bth.',
		),
		'Rent'=>array(
		),
		/*
		'Deposit'=>array(
			'header'=>'Dep.',
		),
		*/
		'SquareFeet'=>array(
			'header'=>'SqFt.',
		),
		'Address'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'prlist("Address")',
			'orderBy'=>'PropertyState $asc, PropertyCity $asc, PropertyAddress $asc',
			'nowrap'=>true,
		),
		'Phones'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'prlist("Phones")',
			'sortable'=>false,
		),
		/*
		'Quantity'=>array(
			'header'=>'Units',
		),
		
		'Status'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'prlist("Status")',
			'sortable'=>false,
			/*
			if one unit it's either leased or not and we link to the lease OR link to creating the lease
			if multiple units we compare:
				1/small = 
				1/medplus = no mention/available
				few/medplus = mostly available
				many/medplus = some available
				most/medplus = {count} available
			..duh what am I thinking..
			
			if(apt){
				tbd
			}else{
				if(<2 units){
					
				}
			}
				
			* /
		),
		*/
	)
);

//2010-06-03 converted active/inactive to discharged/in care
$datasetActiveUsage=false;
$hideObjectInactiveControl=true;

$datasetShowDeletion=false;

//handle incoming request
if($filterChildStatus){
	if($current==1 && count($offices)<2)error_alert('You must have at least one office showing');
	$_SESSION['userSettings']['filterChildStatus:'.$filterChildStatus]=($current ? 0 : 1);
	q("REPLACE INTO bais_settings SET UserName='".sun()."', 
	vargroup='Child', varnode='filterChildStatus', varkey='$filterChildStatus', varvalue=".($current ? 0 : 1));
}
foreach($_SESSION['userSettings'] as $n=>$v){
	if(!$v || !stristr($n,'filterChildStatus'))continue;
	$inStatusSet[]=end(explode(':',$n));
}

require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v103.php');

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
	<?php
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'darkgreen',
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
require($MASTER_COMPONENT_ROOT.'/dataset_component_v123.php');
?>