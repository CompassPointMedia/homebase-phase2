<?php
/* 2012-05-29
gotchas
	function=default; that means col_$dataset
	format=date:n/j/Y g:iA; - the problem is the : so you escape by \:
	to have the new parsing kernel working you need to declare:
		$datasetFunction='parse';
improvements
	went from focus_payments to focus_record() all generic, adding 
		(optional) datasetFocusPage
		(from navbuttons) navObject

*/
$dataset='products';
$datasetGroup='products';
$datasetWord = $datasetWordShort = 'Item';
$datasetComponent='productsList';
$datasetQueryValidation=md5($MASTER_PASSWORD);
$datasetFunction='parse';
$datasetFocusViewDeviceFunction='focus_record';
$datasetFocusViewDims='975,650';
$datasetTable='finan_items';
if(!$datasetFile)$datasetFile=end(explode('/',__FILE__)); //2013-05-06 added logic as this is contained in comp 975
$recordPKField[]='ID';

if($test==17)$datasetDebug['query']=md5($MASTER_PASSWORD);

/* 2012-05-29 these are new to this dataset component as of this date */
$datasetFocusPage='products';
$navObject='Items_ID'; //note this is from the nav buttons snippet


$datasetQuery="SELECT
i.*, COUNT(e.ID) AS Batches
FROM finan_items i LEFT JOIN gen_batches_entries e ON i.ID=e.Objects_ID AND e.ObjectName='finan_items' WHERE i.ResourceType IS NOT NULL GROUP BY i.ID
ORDER BY i.Name ASC";
$datasetQuery="SELECT
* 
FROM finan_items WHERE ResourceType IS NOT NULL
ORDER BY Name ASC";

function focus_record($r){
	/* 2012-04-20, what we have here is a basic opening device
	not sure use of the global function->var feature, and it certainly has never been used consistently or with any protocol
	*/
	global $dataset,$datasetFocusPage,$datasetFocusViewDims,$datasetID,$navObject;
	extract($r);
	?><a href="<?php echo ($datasetFocusPage?$datasetFocusPage:$dataset).'.php?'.($navObject?$navObject:'ID').'='.$$datasetID;?>" title="View details" onclick="return ow(this.href,'l1_<?php echo $dataset;?>','<?php echo $datasetFocusViewDims?$datasetFocusViewDims:'750,700';?>');"><img src="/images/i/edit2.gif" alt="edit" /></a><?php
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
				$out=date(str_replace('date:','',$format),strtotime($out));
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
function col_products($field,$options=array()){
	/* 
	2012-04-20: this is the native function for this dataset
	NOTE only the coding between the lines needs to be updated
	
	*/
	global $record,$submode,$MASTER_USERNAME,$MASTER_PASSWORD,$FUNCTION_ROOT, $GCUserName, $qr,$fl,$ln,$developerEmail,$fromHdrBugs, $modApType,$modApHandle;
	extract($options);
	//additional globals, for example allInvoices, thisLateFee, applicationLateFee
	if(!empty($global))eval('global $'.preg_replace('/,\s*/',', $',$global).';');

	$a=$record;
	extract($a);
	ob_start();
	switch(true){
		//------------------------------ custom coding ------------------------------
		case $field=='Img':
			global $imgArray;
			//array('positiveFilters'=>'\.(jpg|gif|png|svg)$',)
			if(!isset($imgArray))$imgArray=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName);
			if(!function_exists('get_image'))require($FUNCTION_ROOT.'/function_get_image_v220.php');
			if($a=get_image($SKU,$imgArray,array('get_imageReturnMethod'=>'array'))){
				$a=current($a);
				$Tree_ID=tree_build_path('images/documentation/'.$GCUserName.'/'.$a['name']);
				$src='/images/reader.php?Tree_ID='.$Tree_ID.'&Key='.md5($Tree_ID.$MASTER_PASSWORD).'&disposition=50x50&boxMethod=2';
				?><img src="<?php echo $src;?>" width="50" height="50" alt="img" /><?php
			}else{
				?>&nbsp;<?php
			}
		break;
		case $field=='LongDescription':
			$s=preg_split('/\s+/',strip_tags($LongDescription));
			?><div style="width:210px;"><?php
			for($i=0;$i<=30;$i++)echo ($i>0?' ':'').$s[$i].($i==30 && count($s)>31?'...':'');
			?></div><?php
		break;
		case $field=='FileSize':
			if($FileSize>0)echo round($FileSize / 1024 / 1024, 2).'MB';
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

$a=array(
	'Img'=>array(
		'header'=>'Img',
		'function'=>'col(field=Img&function=default)',
	),
	'CreateDate'=>array(
		'header'=>'Created',
		'function'=>'col(field=CreateDate&format=date:n/j/Y g\:iA)',
	),
	'Creator'=>array(
		'header'=>'By..',
	),
	'EditDate'=>array(
		'header'=>'Edited',
		'function'=>'col(field=EditDate&format=date:n/j/Y g\:iA)',
	),
	'SKU'=>array(
	),
	'Category'=>array(
	),
	'SubCategory'=>array(
	),
	'Name'=>array(
	),
	'Description'=>array(
		'header'=>'Short Desc.',
	),
	'LongDescription'=>array(
		'header'=>'Description',
		'function'=>'col(field=LongDescription&function=default)',
	),	
	'Featured'=>array(
		
	),
	'MetaTitle'=>array(
		
	),
	'SEO_Filename'=>array(
		
	),
	'UnitPrice'=>array(
		'header'=>'Price',
		'visibility'=>COL_HIDDEN,
	),
	'HMR_Price1'=>array(
		'header'=>'Pr#1',
	),
	'HMR_Price2'=>array(
		'header'=>'Pr#2',
	),
	'HMR_Price3'=>array(
		'header'=>'Pr#3',
	),
	'HMR_Price4'=>array(
		'header'=>'Pr#4',
	),
	'FileSize'=>array(
		'header'=>'Size',
		'function'=>'col(field=FileSize&function=default)',	
	),
	'Width1'=>array(
		'header'=>'W(px)',
	),
	'Height1'=>array(
		'header'=>'H(px)',
	),
	'DPI1'=>array(
		'header'=>'DPI',
	),
	'EBAY_ItemID'=>array(
		'header'=>'Ebay Item#',
	),
	'AMAZON_listingid'=>array(
		'header'=>'Amz. Listing',
	),
	'AMAZON_asin1'=>array(
		'header'=>'Amz. ASIN',
	),
);
if(!$availableCols[$datasetGroup][$modApType][$modApHandle]['scheme'])
	$availableCols[$datasetGroup][$modApType][$modApHandle]['scheme']=$a;

require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v200a.php');

//added 2012-07-16: first use of filter gadget in a long time
$useStatusFilterOptions=false;
$outputStatusFilterOptions=false;
ob_start();
require($_SERVER['DOCUMENT_ROOT'] . '/components/comp_01_filtergadget_v200a.php');
$fg=ob_get_contents();
ob_end_clean();

ob_start();
?><div class="fr">
<table><tr><?php
if(!$suppressDatasetPreSubContent){
	?><td valign="bottom" style="vertical-align:bottom;">
	<a href="root_products_unassigned.php">click here to view unassigned maps</a>
	&nbsp;&nbsp;
	<a href="resources/bais_01_exe.php?mode=refreshComponent&component=productsList:comp_300_products_hmr_v100.php:b3bd6b6ea9dda1a57820fcb191bcd2d8&suppressPrintEnv=1&submode=exportDataset" target="w2">Export this data..</a>
	</td><?php
}
?><td>
<?php echo $fg;?>
</td></tr></table>
</div>
<?php
$datasetPreSubContent=ob_get_contents();
ob_end_clean();



if(!$refreshComponentOnly){
	?>
	<?php echo $filterGadgetCSS?>
	<?php echo $filterGadgetJS;?>
	<style type="text/css">
	/* -- from filter gadget -- */
	<?php
	if(!$suppressDataset_complexDataCSS)
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'556b2f',
		'datasetColorRowAlt_'=>'99a682',
		'datasetColorSorted_'=>'wheat',
	));
	?>
	/*
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
	*/
	</style>
	<script language="javascript" type="text/javascript">
	</script><?php
}
if(!$suppressDatasetComponent)require($MASTER_COMPONENT_ROOT.'/dataset_component_v200a.php');

?>