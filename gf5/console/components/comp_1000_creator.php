<?php
/* 2011-01-22; Samuel with Parker helping.  

here is the format for the user defined coding for a column:
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


*/
$tables=q("SHOW TABLES",O_COL);

if($datasetTable=='{RBADDNEW}')unset($datasetTable);

//------- tabs coding --------
$__tabs__['datasetCreator']['tabSet']=array(
	'Data Source'	=>'dcDataSource',
	'Columns'	=>'dcColumns',
	'Headers/Breaks'	=>'dcHeadersBreaks',
	'Controls'	=>'dcControls',
	'Appearance'	=>'dcAppearance',
	'Everything Else'	=>'dcMisc',
	'Help'	=>'dcHelp',
);

if(!$refreshComponentOnly){
	?><style type="text/css">
	#showHideCreator{
		cursor:pointer;
		}
	.objectWrapper{
		background-color:lightsteelblue;
		}
	.datasetCol{
		border-bottom:1px solid #000;
		padding:10px;
		margin-bottom:10px;
		}
	</style>
	<script type="text/javascript" language="javascript">
	function UpdateFields(n){
		if(n=='{RBADDNEW}'){
			ow('https://75.125.10.34:2087/3rdparty/phpMyAdmin/tbl_create.php?db=<?php echo $MASTER_DATABASE?>&table='+prompt('Enter table name','')+'&num_fields='+prompt('Enter number of fields',''),'l1_addtable','700,700');
			return false;
		}
		window.open('/gf5/console/components/parkertest.php?mode=availableCols&table='+g('datasetTable').value, 'w2');
	}
	function showHide(o,n,showText,hideText){
		if(g(n).style.display=='none'){
			g(n).style.display='block';
			o.firstChild.src='/images/i/plusminus-minus.gif';
			o.firstChild.nextSibling.innerHTML=hideText;
		}else{
			g(n).style.display='none';
			o.firstChild.src='/images/i/plusminus-plus.gif';
			o.firstChild.nextSibling.innerHTML=showText;
		}
	}
	function showHideCreator(o){
		if(g('creator').style.display=='none'){
			g('creator').style.display='block';
			g('showHideCreatorImg').src='/images/i/plusminus-minus.gif';
			g('showHideText').innerHTML='hide creator';
		}else{
			g('creator').style.display='none';
			g('showHideCreatorImg').src='/images/i/plusminus-plus.gif';
			g('showHideText').innerHTML='show creator';
		}
	}
	</script>
	<?php	
}
?>
<div id="datasetCreator">
<span id="showHideCreator" onclick="showHideCreator(this);"><img src="/images/i/plusminus-minus.gif" id="showHideCreatorImg" /><span id="showHideText">hide creator</span></span>
<div id="creator" class="objectWrapper">
<?php
//-------------------------- tabs ------------------------
ob_start();
?>
	<input name="rows_datasource" type="radio" value="tabular" <?php echo (!$datasetTable && !$datasetQuery) || $datasetTable ? 'checked':''?> />
	Tabular source of data: 
	<select id="datasetTable" name="datasetTable" onchange="UpdateFields(this.value);">
		<option value="">--Select--</option>
		<?php 
		foreach($tables as $v){
			?><option value="<?php echo $v?>" <?php echo $datasetTable==$v?'selected':''?>><?php echo $v;?></option><?php
		}
		?>
		<option value="{RBADDNEW}">&lt;Add new table..</option>
	</select>
	
	<label>Is this a view? <input type="checkbox" value="1" name="datasetTableIsView" /></label>
	<input name="mode" type="hidden" id="mode" value="datasetComponentCreator" />
	<br />
	<input name="rows_datasource" type="radio" value="string" <?php echo $datasetQuery?'checked':''?> /> 
	String Query: [<a title="test this query" onclick="return ow(this.href+escape(g('datasetQuery').value),'l1_testquery','800,700');" href="http://relatebase-rfm.com:2086/3rdparty/phpMyAdmin/import.php?db=<?php echo $MASTER_DATABASE?>&table=addr_contacts&show_query=1&token=10d89ed8d5e21128ad702281edf6ac57&sql_query=">test</a>]<br />
	<textarea name="datasetQuery" cols="60" rows="10" id="datasetQuery"><?php echo h($datasetQuery);?></textarea>


	<p>
	Dataset Delete Mode : <input name="datasetDeleteMode" type="text" id="datasetDeleteMode" value="<?php echo $datasetDeleteMode?>" />
	Dataset Field List : <input name="datasetFieldList" type="text" id="datasetFieldList" value="<?php echo $datasetFieldList?>" />
	</p>

	<p>
	Dataset Handle : <input name="dataset" type="text" id="dataset" value="<?php echo $dataset;?>" /><br />
	Component Handle : <input name="datasetComponent" type="text" id="datasetComponent" value="<?php echo $datasetComponent;?>" />	
	</p>
	<p>
	Name Of Dataset : <input name="datasetWord" type="text" id="datasetWord" value="<?php echo $datasetWord;?>" /><br />
	Plural Name Of Dataset : <input name="datesetWordPlural" type="text" id="datasetWordPlural" value="<?php echo $datasetWordPlural?>"/>
	</p>
	<p>
	Dataset Focus Page : <input name="datasetFocusPage" type="text" id="datasetFocusPage" value="<?php echo $datasetFocusPage?>" />
	Dataset Query Key Field : <input name="datasetQueryStringKey" type="text" id="datasetQueryStringKey" value="<?php echo $datasetQueryStringKey?>" /><br />
	</p>
	<p>
	Where clause filter : <input type="text" name="datasetInternalFilter" id="datasetInternalFilter" value="<?php echo $datasetInternalFilter?>" /><br />
	Batch Threshold : <input type="text" name="globalBatchThreshold" id="globalBatchThreshold" value="<?php echo $globalBatchThreshold?>" /><br />
	Scrolling Threshold : <input type="text" name="tbodyScrollingThreshold" id="tbodyScrollingThreshold" value="<?php echo $tbodyScrollingThreshold?>" /><br />
	
	</p>
	<p>
	Dataset Array Type : 
	<select name="datasetArrayType">
		<option value="<?php echo O_ARRAY_ASSOC?>" <?php echo $datasetArrayType==O_ARRAY_ASSOC?'selected':''?>>Associative Array</option>
		<option value="<?php echo O_ARRAY?>" <?php echo $datasetArrayType==O_ARRAY?'selected':''?>>Array</option>
		<option value="<?php echo O_ROW?>" <?php echo $datasetArrayType==O_ROW?'selected':''?>>Row</option>
		<option value="<?php echo O_COL?>" <?php echo $datasetArrayType==O_COL?'selected':''?>>Column</option>
	</select>
	<input type="hidden" id="zeroVal" value="1" />	
	</p>


<?php 
//------------------------------- tab 1 ---------------------------------
get_contents_tabsection('dcDataSource');
?>
<div style="height:500px; overflow:scroll; border:1px solid #000; background-color:white; padding:5px;">
<?php
$i=0;
if($a=$availableCols[$datasetGroup][$modApType][$modApHandle]['scheme'])
foreach($a as $n=>$v){
	$i++;
	$v['key']=$n;
	?>
<div id="col_<?php echo $i?>" class="datasetCol">Column name: 
  <input name="availableCols[<?php echo $i?>][key]" type="text" id="availableCols[<?php echo $i?>][key]" value="<?php echo h($v['key']);?>" />
  <br />
  Show column as: 
  <select name="aCols[<?php echo $i?>][fieldExpressionFunction]" id="aCols[<?php echo $i?>][fieldExpressionFunction]">
    <option <?php echo $v['method']=='field'?'selected':''?> value="field">Single Field</option>
    <option <?php echo $v['method']=='expression'?'selected':''?> value="expression">SQL Expression</option>
    <option <?php echo $v['method']=='function'?'selected':''?> value="function">PHP Code</option>
  </select>
  <br />
  Code to evaluate:<br />
  <textarea name="aCols[<?php echo $i?>][formula]" cols="45" rows="3" id="aCols[<?php echo $i?>][formula]"><?php echo h($v['formula']);?></textarea>
  <br />
	</div>
<?php
}
?>
<span style="cursor:pointer;" onclick="showHide(this, 'rawAvailableCols', 'show raw coding..','hide raw coding..');"><img src="/images/i/plusminus-plus.gif" style="padding-right:7px;" /><span>show raw coding..</span></span>

<div id="rawAvailableCols" style="display:none;">
<?php
if($a)prn($a);
?>
</div>
</div>
<?php 
//------------------------------- tab 1b  ---------------------------------
get_contents_tabsection('dcColumns');
?>

<?php 
//------------------------------- tab 2 ---------------------------------
get_contents_tabsection('dcHeadersBreaks');
?>



<?php 
//------------------------------- tab 3 ---------------------------------
get_contents_tabsection('dcControls');
?>
	Style of Output: 
	<select name="datasetTheme">
		<option value="Report" <?php echo strtolower($datasetTheme)=='report'?'selected':'';?>>
			Report
		</option>
		<option value="" <?php echo $datasetTheme==''?'selected':'';?>>
			Table
		</option>
	</select>


<?php 
//------------------------------- tab 4 ---------------------------------
get_contents_tabsection('dcAppearance');
?>


<?php 
//------------------------------- tab 5 ---------------------------------
get_contents_tabsection('dcMisc');
?>



<?php 
//------------------------------- tab 6 ---------------------------------
get_contents_tabsection('dcHelp');
?>

<?php
//----------------------- compile tabs --------------------------
$tabWidth=700;
require($MASTER_COMPONENT_ROOT.'/comp_tabs_v220.php');
?>
</div>
<br />

<div id="availableCols">
</div>
<?php 
/*
$datasetAddObjectJSFunction='ow(this.href,\'l1_employees\',\'800,700\',true);'; //this is because opening an object is not well developed yet
$modApType='embedded';
$modApHandle='first';
$datasetActiveUsage=false;
$hideColumnSelection=false; //however, we need to show column selection still
$footerDisposition='tabularControls'; //however, the footer needs to show to nav a large batch like this
$datasetHideFooterAddLink=true;
$hideColumnSelection=false;
$datasetShowDeletion=false; //no deletion needed on report
$datasetShowBreaks=true;
$datasetBreakFields=array(
	1=>array(
		'column'=>'Week',
		'blank'=>'not specified',
		'header'=>'Week #',
	),
	2=>array(
		'column'=>'Employee_ID',
		'blank'=>'not specified',
		'header'=>'Employee',
	)
);
*/
?>
	<p>
	<input type="submit" name="Submit" value="Submit">
	</p>
</div>
<?php
/*
prn($fields);
prn($attributes);
*/
if(($mode=='insertDatasetComponent' && ($datasetTable || $datasetQuery)) || $mode=='updateDatasetComponent'){
	unset($ds);
	$ds=stripslashes_deep($_POST);
	extract($ds);
	//while we are building
	if($availableCols){
	
	}else{
		//..and this is not going to be part of the output code
		//get fields
		//put in similar format as avaialble cols
		//maybe use some common sense assumptions
	}
	if($datasetTable=='{RBADDNEW}')unset($datasetTable);
	if($datasetQuery && !$datasetTable)unset($ds['datasetTable']);
	if(!$databaseQuery && $datasetTable)unset($ds['datasetQuery']);
	
	//to show the dataset
	$datasetQueryValidation=md5($MASTER_PASSWORD);
	
	require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v103test.php');
	require($MASTER_COMPONENT_ROOT.'/dataset_component_v123test.php');
}



?>