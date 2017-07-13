<?php 
ob_start();
/*
2012-11-02
* removed setExportRecords! exporting is no longer done via report_generic.php - but via export_select.php with a matrix of any {vendor}_tobeexported=1 in records, and individually for all vendors.

2012-10-30: by default, records must be CHECKED to be added to the export
2012-07-04: item query return - designed to show multiple types of queries and event. in various configs.
searchtype=
	SKU1 - first 4 digits of the sku
	merchanttobeeqpirted
*/
function location_MIVAONLY(){
	/* 2012-11-02 */
	global $ID;
	?>
	<input type="hidden" name="exportBatch[MIVA][<?php echo $ID;?>]" value="0" />
	<input type="checkbox" name="exportBatch[MIVA][<?php echo $ID;?>]" id="exportBatch[MIVA][<?php echo $ID;?>]" class="toggle" value="1" /><?php
}
if(strlen($toggle) && $toggle==='0')unset($form['checkboxes']);

if(!$refreshComponentOnly){
	?><style type="text/css">
	th.wider, td.wider{
		padding-left:12px;
		padding-right:12px;
		}
	</style>
	<script language="javascript" type="text/javascript">
	$(document).ready(function(){
		$('#exportLocationMaps').click(function(e){
			if(!confirm('This will export these maps and longitude/latitude data immediately.  Continue?'))return false;
		});
		$('#allNone').click(function(){
			var t=$(this).attr('checked');
			$('.toggle').each(function(i,v){
				$(this).attr('checked',t);
			});
		});
	});
	</script><?php
}
unset($records);

//added 2012-08-26: look in config.php
@extract(searchtype());

?>
<h1><?php echo $title;?></h1>
<p class="gray"><?php echo $subtitle;?></p>
<?php
if($searchtype=='location'){
	?><select onchange="window.location='report_generic.php?report=itemsquery&searchtype=location&toggle='+this.value;">
	<option value="1" <?php echo $toggle==='1'?'selected':''?>>Show maps WITH a polygon</option>
	<option value="0" <?php echo $toggle==='0'?'selected':''?>>Show maps WITHOUT a polygon</option>
	</select>
	<br />
	<?php
}

?>
[<a href="products.php?searchtype=<?php echo $searchtype;?>&q=<?php echo urlencode(stripslashes( is_array($q) ? implode('|',$q) : $q ));?>" onclick="return ow(this.href,'l1_items','815,700');">view as a search group..</a>]
<table id="products" class="yat">
<thead>
<tr>
<?php if($form['checkboxes']){ ?>
	<th class="tac wider"><input type="checkbox" id="allNone" title="Click to check/uncheck all records" /></th>
<?php } ?>
	<th>SKU</th>
	<th>added</th>
	<th>by</th>
	<th>Product Code <div class="red" style="font-weight:400;">(first 50 characters red)</div></th>
	<th>Name<div class="red" style="font-weight:400;">(first 70 characters red)</div></th>
	<th>Category</th>
	<th>Featured Cities</th>
	<th>Situated In</th>
	<th>Following Towns</th>
</tr>
</thead>
<tbody>
<?php
if($records){
	$i=0;
	foreach($records as $n=>$v){
		extract($v);
		$i++;
		?><tr class="<?php echo !fmod($i,2)?'alt':''?>">
			<?php if($fctn=$form['checkboxes']){ ?>
			<td class="tac wider"><?php
			//this generates the checkbox
			$fctn();
			?></td>
			<?php } ?>
			<td><a href="products.php?Items_ID=<?php echo $ID?>" onclick="return ow(this.href,'l1_items','750,700');" title="View/edit this product"><?php echo $SKU;?></a></td>
			<td><?php echo date('n/j@g:iA',strtotime($CreateDate));?></td>
			<td><?php echo $Creator;?></td>
			<td style="width:150px;"><?php echo preg_replace('/^(.{50})/','<strong style="font-size:15px;" class="red">$1</strong>',str_replace('-',' ',$SEO_Filename));?></td>
			<td><?php 
			if(strlen($Name)>70){
				echo preg_replace('/^(.{70})/','<strong style="font-size:15px;" class="red">$1</strong>',$Name).substr($Name,70,1000);
			}else{
				echo $Name;
			}
			?></td>
			<td><?php echo $Category;?></td>
			<?php
			$key='Featured';
			$words[$key]=8;
			$str='';
			$title='';
			$GLOBALS[$key]=@preg_split('/\s+/',strip_tags($GLOBALS[$key]));
			for($j=0;$j<count($GLOBALS[$key]);$j++){
				if($j<$words[$key])$str.=($j?' ':'').$GLOBALS[$key][$j];
				$title.=($j?' ':'').$GLOBALS[$key][$j];
			}
			if(count($GLOBALS[$key])>$words[$key])$str.= '...';
			?>
			<td title="<?php echo h($title);?>"><?php 
			echo $str;?></td>
			<?php
			$key='Description';
			$words[$key]=8;
			$str='';
			$title='';
			$GLOBALS[$key]=@preg_split('/\s+/',strip_tags($GLOBALS[$key]));
			for($j=0;$j<count($GLOBALS[$key]);$j++){
				if($j<$words[$key])$str.=($j?' ':'').$GLOBALS[$key][$j];
				$title.=($j?' ':'').$GLOBALS[$key][$j];
			}
			if(count($GLOBALS[$key])>$words[$key])$str.= '...';
			?>
			<td title="<?php echo h($title);?>"><?php 
			echo $str;?></td>
			<?php
			$key='LongDescription';
			$words[$key]=8;
			$str='';
			$title='';
			$GLOBALS[$key]=@preg_split('/\s+/',strip_tags($GLOBALS[$key]));
			for($j=0;$j<count($GLOBALS[$key]);$j++){
				if($j<$words[$key])$str.=($j?' ':'').$GLOBALS[$key][$j];
				$title.=($j?' ':'').$GLOBALS[$key][$j];
			}
			if(count($GLOBALS[$key])>$words[$key])$str.= '...';
			?>
			<td title="<?php echo h($title);?>"><?php 
			echo $str;?></td>
		</tr><?php
	}
}else{
	?><tr>
	<td colspan="100%"><em class="gray">No records found for that criteria</em></td>
	</tr><?php
}
?>
</tbody>
</table>
<?php
if($searchtype=='location'){
	?>
	<h2>
	<label>
	<input type="checkbox" name="doNotUpdate" id="doNotUpdate" value="1" checked="checked" /> Do not update database; just create this batch
	</label>
	</h2>
	<br />

	<?php
}
$reportOutput=ob_get_contents();
ob_end_clean();
?>