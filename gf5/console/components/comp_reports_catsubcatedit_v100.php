<?php 
if(minroles()>ROLE_ADMIN)exit('You do not have access to this tool');

if($CatSub){
	$a=explode('|',$CatSub);
	$Category=$a[0];
	$SubCategory=$a[1];
}
$passcomponent='editCategorySubcategory:'.end(explode('/',__FILE__)).':'.md5(end(explode('/',__FILE__)).$MASTER_PASSWORD);

if(!$refreshComponentOnly){
	ob_start();
	?><style type="text/css">
	.wrap{
		width:85%;
		border:1px solid #ccc;
		overflow:scroll;
		}
	#form{
		width:85%;
		border:1px solid darkred;
		margin:10px 0px;
		padding:5px 20px;
		}
	#list1{
		height:250px;
		}
	h1,h2,h3,h4,h5,h6{
		font-family:Georgia, "Times New Roman", Times, serif;
		color:midnightblue;
		}
	.reportTable{
		border-collapse:collapse;
		width:100%;
		}
	.reportTable th,
	.reportTable td{
		padding:1px 7px;
		}
	.reportTable td{
		border-bottom:1px dotted #ccc;
		}
	.reportTable tr{
		cursor:pointer;
		}
	.reportTable tr[class^=hdr]{
		cursor:auto;
		}
	.reportTable .selected{
		background-color:#66CC99;
		}
	.reportTable .selected:hover{
		background-color:#8AC087;
		}
	.reportTable .std:hover{
		background-color:mintcream;
		}
	</style>
	<script language="javascript" type="text/javascript">
	function selectRecords(n){
		$('#pending1').html('<img src="/images/i/ani/ani-fb-darkred.gif" width="16" height="11" /> Loading category/subcategory..');
		window.open('resources/bais_01_exe.php?mode=refreshComponent&component=<?php echo $passcomponent;?>&CatSub='+n,'w2');
	}
	function interlock(o){
		if(o.id=='action'){
			switch(o.value){
				case 'catsubonly':
					g('Category').disabled=false;
					g('SubCategory').disabled=false;
					makeBoxes('disabled',false);
				break;
				case 'allsubs':
					g('Category').disabled=true;
					g('SubCategory').disabled=false;
					makeBoxes('disabled',true);
				break;
				case 'allcats':
					g('Category').disabled=false;
					g('SubCategory').disabled=true;
					makeBoxes('disabled',true);
				break;
				default:
			}
		}
	}
	function makeBoxes(attr,v){
		$('.makeBox').each(function(){
			$(this).attr(attr,v);
		});
	}
	$('#form1').submit(function(){
		$('#pending').html('Loading');
	});
	</script><?php
	$headerOutput=ob_get_contents();
	ob_end_clean();
}

ob_start();
if($submode=='updateCategorySubcategory'){
	if($Category==$OriginalCategory && $SubCategory==$OriginalSubCategory)error_alert('You must change the category or subcategory');
	if($action!='allsubs' && !strlen($Category))error_alert('Category cannot be blank');
	if($action!='allcats' && !strlen($SubCategory))error_alert('Subcategory cannot be blank');
	if($action=='catsubonly' && !count($records))error_alert('You must select one or more records for this type of update');
	if(!$action)error_alert('Select an action to perform on this/these categories and subcategories');
	
	if($action=='allcats'){
		q("UPDATE finan_items SET 
		EditDate=EditDate, 
		Category='$Category' 
		WHERE Category='$OriginalCategory'");
	}else if($action=='allsubs'){
		q("UPDATE finan_items SET 
		EditDate=EditDate, 
		SubCategory='$SubCategory' 
		WHERE SubCategory='$OriginalSubCategory'");
	}else if($action=='catsubonly'){
		q("UPDATE finan_items SET EditDate=EditDate, SubCategory='$SubCategory' WHERE Category='$OriginalCategory' AND SubCategory='$OriginalSubCategory' AND ID IN(".implode(',',$records).")");
		prn($qr);
	}
	if($n=$qr['affected_rows']){
		error_alert($n.' record'.($n==1?'':'s').' changed',1);
	}else{
		error_alert('No record(s) have been updated.  They may have been changed by someone else or deleted');
	}
}


?><div id="editCategorySubcategory"><?php
$a=q("SELECT
a.Category, a.SubCategory, COUNT(DISTINCT a.ID) AS Count, COUNT(DISTINCT b.Category) AS SuperCategories
FROM finan_items a JOIN finan_items b ON a.SubCategory=b.SubCategory
WHERE a.ResourceType IS NOT NULL GROUP BY a.Category, a.SubCategory", O_ARRAY);
?>
<h1>Bulk Category and Subcategory Updater</h1>
<p class="gray">Select any combination of category and subcategory to select and make changes</p>
<div id="list1" class="wrap">
<table id="catsubcat" class="reportTable">
<thead>
<tr class="hdr">
	<th>Category</th>
	<th>&nbsp;</th>
	<th>Subcategory</th>
	<th>Records</th>
	<th title="This is the number of Categories this Subcategory is found in">Super</th>
</tr>
</thead>
<tbody>
<?php
if($a){
	$i=0;
	foreach($a as $v){
		$i++;
		if($buffer!==$v['Category'] && $i>1){
			?><tr class="hdr"><td colspan="100%"><h2 class="nullTop"><?php echo $buffer;?> - <?php echo $subtotal;?></h2></td></tr><?php
			$subtotal=0;
		}
		$subtotal+=$v['Count'];
		?><tr id="<?php echo h($v['Category'].'|'.$v['SubCategory']);?>" class="<?php echo strtolower($Category)==strtolower($v['Category']) && strtolower($SubCategory)==strtolower($v['SubCategory']) ? 'selected':'std'?>" onclick="selectRecords(this.id);">
			<td><?php echo $v['Category'];?></td>
			<td><span class="big">&raquo;</span></td>
			<td><?php echo $v['SubCategory'];?></td>
			<td class="tar"><a href="report_generic.php?report=itemsquery&searchtype=categorysubcategory&q=<?php echo urlencode($v['Category'].'|'.$v['SubCategory']);?>" title="View records with this category/subcategory combination" onclick="return ow(this.href,'l2_reports','700,700');"><?php echo $v['Count'];?></a></td>
			<td class="tar"><?php
			if($v['SuperCategories']>1){
				?><span title="This subcategory is found in <?php echo $v['SuperCategories'];?> categor<?php echo $v['SuperCategories']==1?'y':'ies';?>"><?php echo $v['SuperCategories'];?></span><?php
			}else{
				?>&nbsp;<?php
			}
			?></td>
		</tr><?php
		$buffer=$v['Category'];
	}
}else{
	?><tr><td colspan="100%"><em class="gray">No records found for that criteria</em></td></tr><?php
}
?></tbody>
</table>
</div>
<div id="pending1">&nbsp;</div>
<div id="list2">
<?php
if($Category && $SubCategory && $m=q("SELECT ID, Creator, CreateDate, SKU, Name, Description FROM finan_items WHERE Category='".addslashes($Category)."' AND SubCategory='".addslashes($SubCategory)."' ORDER BY SKU", O_ARRAY)){
	?>
	<div id="form">
	  Action: 
	<select name="action" id="action" onchange="interlock(this)">
		<option value="catsubonly">Change this category and subcategory combination only</option>
		<option value="allcats">Change ENTIRE category, regardless of subcategory</option>
		<option value="allsubs">Change ENTIRE subcategory, regardless of category</option>
	</select>
	<br />
	Category from <strong><?php echo stripslashes($Category);?></strong> to: 
	<input name="Category" type="text" id="Category" value="<?php echo h(stripslashes($Category));?>" onchange="dChge(this);" size="20" />
	<input name="OriginalCategory" type="hidden" id="OriginalCategory" value="<?php echo h(stripslashes($Category));?>" />
	<br />
	Subcategory from <strong><?php echo stripslashes($SubCategory);?></strong> to: <input name="SubCategory" type="text" id="SubCategory" value="<?php echo h(stripslashes($SubCategory));?>" onchange="dChge(this);" size="20" />
	<input name="OriginalSubCategory" type="hidden" id="OriginalSubCategory" value="<?php echo h(stripslashes($SubCategory));?>" />
	<br />
	<input type="submit" name="Submit" value="Update" />
	<span id="pending"></span>	
	</div>
	<table class="reportTable">
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th>SKU</th>
		<th>Name</th>
		<th>Description</th>
		<th>by..</th>
		<th>on..</th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($m as $n=>$v){
		?><tr>
		<td><input type="checkbox" name="records[<?php echo $v['ID'];?>]" id="r<?php echo $v['ID'];?>" value="<?php echo $v['ID'];?>" checked="checked" onchange="dChge(this);" class="makeBox" /></td>
		<td><a href="products.php?Items_ID=<?php echo $v['ID'];?>" onclick="return ow(this.href,'l1_items','850,700');" title="View/edit this item"><?php echo $v['SKU'];?></a></td>
		<td><?php echo $v['Name'];?></td>
		<td><?php echo $v['Description'];?></td>
		<td><?php echo $v['Creator'];?></td>
		<td><?php echo str_replace(' ','&nbsp;',date('n/j \a\t g:iA',strtotime($v['CreateDate'])));?></td>
		</tr><?php
	}
	?>
	</tbody>
	</table>	
	<input name="mode" type="hidden" id="mode" value="refreshComponent" />
	<input name="component" type="hidden" id="component" value="<?php echo $passcomponent;?>" />
	<input name="submode" type="hidden" id="submode" value="updateCategorySubcategory" />
	<br />
<?php
}
?>
</div>
</div><?php

$reportOutput=ob_get_contents();
ob_end_clean();
if($mode=='refreshComponent')echo $reportOutput;

?>