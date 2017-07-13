<?php
/* take-from
datasetWord MUST be var-like because hl_group uses it for the node as <?php $datasetWord;?>opt - we need another value for this, develop following:
	$datasetTitle
	$datasetDescription
	$datasetInstructions
	(and implement legacy logic accordingly)

open the search 
default sort down
delete a search

*/

/*
dataset
datasetGroup
datasetQuery
required 2 files (ready to go)
availableCols (here is where we do some work)
SPECIAL: status (unpaid, pp, and paid) and dueStatus (fi, due, pd) required work and the interruption of normal flow in the build; we needed to put variables in datasetQuery, and analyze those variables beforehand (and they were buggers too)


SPECIAL: status (unpaid, pp, and paid) and dueStatus (fi, due, pd) required work and the interruption of normal flow in the build; we needed to put variables in datasetQuery, and analyze those variables beforehand (and they were buggers too)
OOPS! We cannot do this as a query, need a view because HAVING is dicey
we do not use active/inactive:
	datasetActiveHideControl=true;
now the focus device
	datasetFocusPage=leases.php
	but this gave this url: leases.php?_ID=13618
	so we add this: datasetFocusQueryStringKey=Leases_ID
	and this is available: datasetFocusAddObjectJSFunction='ow(this.href,\'l1_leases\',\'800,700\',true);'; //because opening an object is not well developed yet

*/
$dataset='mySearch';
$datasetGroup='mySearch';
$datasetComponent='mySearchList';
$datasetWord='My Searches';
$datasetWordPlural='My Searches';
$datasetTable='_v_searches';
$datasetTableIsView=true;
$tbodyScrollingThreshold=100000;
$datasetActiveHideControl=true;
$datasetFocusPage='search_result_popup.php';
$datasetFocusPageDims='850,600';
$datasetFocusQueryStringKey='Searches_ID';


$datasetFocusViewDeviceFunction='recordFocusSearch';
function recordFocusSearch($a){
	?><a href="/gf5/console/search_result_popup.php?Searches_ID=<?php echo $a['ID'];?>" onclick="return ow(this.href,'l1_search','800,800');;">edit</a><?php
}
function colConfigSearch($param,$field='',$options=array()){
	global $record, $submode,$qr, $developerEmail, $fromHdrBugs, $modApType, $modApHandle;
	global $allInvoices, $thisLateFee, $applicationLateFee;
	$param=strtolower($param);
	$a=$record;
	extract($a);
	extract($options);
	ob_start();
	switch($param){
		case 'createdate':
			echo date('n/j/Y \a\t g:iA',strtotime($CreateDate));
		break;
		case 'for':
			if($Contacts_ID){
				echo $LastName.', '.$FirstName;
			}else{
				?><em class="gray">nobody</em><?php
			}
		break;	
		case 'leased':
			echo $Leases_ID;
		break;
		case 'criteria':
			$b=unserialize(base64_decode($SearchCriteria));
			extract($b);
			if($PriceRange)echo 'Price range: '.$PriceRange.'<br />';
			if($Bedrooms)echo 'Beds: '.$Bedrooms.'<br />';
			if($Bathrooms)echo 'Baths: '.$Bathrooms.'<br />';
		break;
		default:
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}
$a=array(
	'CreateDate'=>array(
		'header'=>'Date',
		'method'=>'function',
		'fieldExpressionFunction'=>'colConfigSearch("CreateDate")',
	),
	'For'=>array(
		'method'=>'function',
		'fieldExpressionFunction'=>'colConfigSearch("For")',
	),
	'Leased'=>array(
		'method'=>'function',
		'fieldExpressionFunction'=>'colConfigSearch("Leased")',
	),
	'Criteria'=>array(
		'method'=>'function',
		'fieldExpressionFunction'=>'colConfigSearch("Criteria")',
	),
	'InitialResults'=>array(
		'header'=>'Results',
	),
);
$availableCols[$datasetGroup][$modApType][$modApHandle]['scheme']=$a;


$datasetFile=end(explode('/',__FILE__));

/*
2012-02-09: OK the remaining coding is additional to relatebase console/comp_30_list_events_v110.php as specified on that date

*/
$datasetInternalFilter=(minroles()>=ROLE_AGENT ? "Creator='".sun()."'":'');

if($test==17)$datasetDebug['query']=md5($MASTER_PASSWORD);

require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v104.php');
if(!$refreshComponentOnly){
	?><style type="text/css">
	<?php
	/*
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'a37029',
		'datasetColorRowAlt_'=>'f1e8e2',
		'datasetColorSorted_'=>'wheat',
		'datasetColorHighlight_'=>'d3b8a5',
	));
	*/
	?>
	</style>
	<script language="javascript" type="text/javascript">
	/* 
	"HW" Coding
	2012-02-08: added very simple functions specific to the event 
	2012-02-09: first effort at making this generic
	*/
	<?php if(!$datasetJSDeclared){ ?>
	<?php $datasetJSDeclared=true; ?>
	var dataset={};
	function addObject(o){
		ow(dataset[o][datasetFocusPage], 'l1_'+dataset[o][datasetWord], (typeof dataset[o][datasetFocusPageDims]!=='undefined' ? dataset[o][datasetFocusPageDims] : '750,700'),true);
		return false;
	}
	function openObject(o){
		for(var i in hl_grp[dataset[o][datasetWord]+'opt']){
			ow(dataset[o][datasetFocusPage]+'?'+dataset[o][datasetFocusQueryStringKey]+'='+i.replace('r_',''),'l1_'+dataset[o][datasetWord], (typeof dataset[o][datasetFocusPageDims]!=='undefined' ? dataset[o][datasetFocusPageDims] : '750,700'));
			return false;
		}
	}
	<?php }else{ ?>
	
	<?php }?>
	obj='<?php echo $datasetComponent;?>';
	dataset[obj]={
	<?php if($n=$datasetFocusPageDims){ ?>'datasetFocusPageDims':'<?php echo $n;?>',<?php } ?>
		'datasetGroup':'<?php echo $datasetGroup;?>',
		'dataset':'<?php echo $dataset;?>',
		'datasetFocusPage':'<?php echo $datasetFocusPage;?>',
		'datasetFocusQueryStringKey':'<?php echo $datasetFocusQueryStringKey;?>',
		'datasetWord':'<?php echo strtolower($datasetWord);?>'
	}
	</script><?php
}
require($MASTER_COMPONENT_ROOT.'/dataset_component_v124.php');
?>