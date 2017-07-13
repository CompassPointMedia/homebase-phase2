<?php
ob_start();

if(!$refreshComponentOnly){
	?><style type="text/css">
	h1,h2,h3,h4,h5,h6{
		font-family:Georgia, "Times New Roman", Times, serif;
		color:midnightblue;
		}
	.reportTable{
		border-collapse:collapse;
		}
	.reportTable th,
	.reportTable td{
		border:1px solid #000;
		padding:1px 7px;
		}
	</style>
	<script language="javascript" type="text/javascript">
	$(document).ready(function(){
		$('#order').change(function(){
			window.location='/gf5/console/report_generic.php?report=filesonserver&order='+this.value;
		});
	});
	</script><?php
}

if($report=='catsubcat'){
	$a=q("SELECT Category, SubCategory, COUNT(*) AS Count FROM finan_items WHERE ResourceType IS NOT NULL GROUP BY Category, SubCategory ORDER BY Category, SubCategory", O_ARRAY);
	
	?>
	<h1>Category &amp; Subcategory List</h1>
	<?php if(minroles()<=ROLE_ADMIN){ ?>
	<p><a href="report_generic.php?report=catsubcateditor">Click here</a> for the bulk category and subcategory manager (administrators only)</p>
	<?php } ?>
	<p class="gray"><?php echo $subtitle;?></p>
	<table id="catsubcat" class="reportTable">
	<thead>
	<tr>
		<th>Category</th>
		<th>Subcategory</th>
		<th>#Records</th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($a){
		$i=0;
		foreach($a as $v){
			extract($v);
			$i++;
			
			if($buffer!==$Category && $i>1){
				?><tr>
				<td colspan="2"><h2><?php echo $buffer;?></h2></td>
				<td><h2><?php echo $subtotal;?></h2></td>
				</tr><?php
				$subtotal=0;
			}
			$subtotal+=$Count;
			?><tr class="<?php echo !fmod($i,2)?'alt':''?>">
				<td><?php echo $Category;?></td>
				<td><?php echo $SubCategory;?></td>
				<td><a href="report_generic.php?report=itemsquery&searchtype=categorysubcategory&q=<?php echo urlencode($Category.'|'.$SubCategory);?>" title="View records with this category/subcategory combination" onclick="return ow(this.href,'l2_reports','700,700');"><?php echo $Count;?> record(s)</a></td>
			</tr><?php
			$buffer=$Category;
		}
	}else{
		?><tr>
		<td colspan="100%"><em class="gray">No records found for that criteria</em></td>
		</tr><?php
	}
	?></tbody>
	</table><?php
}else if($report=='filesonserver'){
	
	$master=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/master');
	if(!$order)$order='ID';
	if($order=='AspectRatio')$order='GREATEST(Width1,Height1)/LEAST(Width1,Height1)';
	
	$a=q("SELECT ID, Grouping, SKU, Name, HBS_OriginalFileName AS FileName, Width1, Height1, GREATEST(Width1,Height1)/LEAST(Width1,Height1) AS AspectRatio, DPI1, FileSize, HMR_OldSKU
FROM finan_items WHERE 1 ORDER BY $order", O_ARRAY_ASSOC);
	?>
	<h1>Picture Inventory on Server</h1>
	<select id="order">
	<option value="ID" <?php echo $order=='ID'?'selected':''?>>ID</option>
	<option value="AspectRatio" <?php echo $order=='AspectRatio'?'selected':''?>>Aspect ratio</option>
	</select>
	<p class="gray">
	This lists all maps ordered by ID#; current additions using the map reservation system will all have original files associated with them but first import maps will not.
	</p>
	NOTE: Ordering by aspect ratio added 1/1/2014.
	<table class="yat"><thead>
	<tr>
	<th>Seq.#</th>
	<th>ID</th>
	<th>Gr.</th>
	<th>SKU</th>
	<th>Orig.&nbsp;Name</th>
	<th>Size(k)</th>
	<th colspan="2">Dimensions</th>
	<th>Asp.Rat.</th>
	<th>System</th>
	<th>Actual</th>
	<th>D/L</th>
	</tr></thead><tbody>
	<?php
	$i=0;
	foreach($a as $n=>$v){
		$i++;
		?><tr>
		<td class="tac"><?php echo $i;?></td>
		<td class="tac"><?php echo $n;?></td>
		<td class="tac"><?php echo $v['Grouping'];?></td>
		<td><a href="products.php?Items_ID=<?php echo $n;?>" title="<?php echo h($v['Name']);?>" onclick="return ow(this.href,'l1_products','800,600');"><?php echo $v['SKU'];?></a></td>
		<td<?php if(!trim($v['FileName']))echo ' class="tac gray"';?> title="<?php echo h($v['FileName']);?>"><?php echo $v['FileName']?substr($v['FileName'],0,35).(strlen($v['FileName'])>35?'..':''):'(N/A)';?></td>
		<td class="tar"><?php echo number_format(round($v['FileSize']/1024,2),2);?></td>
		<td class="tar"><?php echo $v['Width1'];?></th>
		<td class="tar"><?php echo $v['Height1'];?></th>
		<td class="tac"><?php echo $v['AspectRatio'];?></td>
		<td class="tac"><?php
		unset($o,$actual);
		if($o=$master[strtolower($v['SKU']).'.jpg']){
			if(floor($v['FileSize'])==floor($o['size']*1024) && $v['Width1']==$o['width'] && $v['Height1']==$o['height']){
				?><img src="/images/i/yes.gif" width="20" height="14" alt="OK" /><?php
			}else{
				$actual=true;
				echo 'warn';
			}
		}else{
			?><span class="gray"><img src="/images/i/delete_red.gif" width="13" height="13" /> (N/A)</span><?php
		}
		?></td>
		<td><?php
		if($actual)echo $o['width'].'x'.$o['height'].':'.round($o['size'],2).'k';
		?></td>
		<td class="tac"><?php 
		if($o){
			?><a href="/images/documentation/<?php echo $GCUserName;?>/master/<?php echo $o['name'];?>" onclick="return ow(this.href,'l1_pic','900,700');" title="Click to open and save this picture">(view)</a><?php
		}else{
			?>&nbsp;<?php
		}
		?></td>
	</tr><?php
	}
	?></tbody></table>
	<?php
	/*
	$translation=q("SELECT LCASE(SKU), HBS_OriginalFileName FROM finan_items WHERE SKU!='' AND (HBS_OriginalFileName='' OR HBS_OriginalFileName IS NULL) ORDER BY IF(HBS_OriginalFileName IS NULL OR HBS_OriginalFileName='',1,2), SKU", O_COL_ASSOC);
	prn(count($translation));
	prn($translation);
	*/
	if(false){
		$root=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName);
		$pending_master=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master');
		$a=array_merge(
			array_keys($root), 
			array_keys($master),
			array_keys($pending_master)
		);
		sort($a);
		prn($a);
	}
}else if($report=='activitybydate'){
	$a=q("SELECT CreateDate, COUNT(*) AS Count, COUNT(DISTINCT Creator) AS DC, SKU, ID, Creator FROM finan_items WHERE ResourceType IS NOT NULL GROUP BY CreateDate ORDER BY CreateDate DESC, SKU", O_ARRAY);
	
	?>
	<h1>Activity by Date</h1>
	<p class="gray"><?php echo $subtitle;?></p>
	<table id="catsubcat" class="reportTable">
	<thead>
	<tr>
		<th colspan="2">Date</th>
		<th>#Records</th>
		<th>SKU</th>
		<th>by</th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($a){
		$i=0;
		foreach($a as $v){
			extract($v);
			$i++;
			?><tr class="<?php echo !fmod($i,2)?'alt':''?>">
				<td nowrap="nowrap" style="border-right:1px dotted #666;"><?php if(!strstr($CreateDate,'0000-00-00') && !is_null($CreateDate))echo date('m-d-Y',strtotime($CreateDate));?></td>
				<td nowrap="nowrap" style="border-left:none;"><?php if(!strstr($CreateDate,'00:00:00') && !is_null($CreateDate)){ echo date('g:i:s A',strtotime($CreateDate)); }else{ echo '&nbsp;'; }?></td>
				<td><?php echo $Count;?></td>
				<td><?php
				if($Count==1){
					?><a href="products.php?Items_ID=<?php echo $ID?>" onclick="return ow(this.href,'l1_items','850,700');" title="view this product"><?php echo $SKU;?></a><?php
				}else{
					$b=q("SELECT ID, SKU FROM finan_items WHERE ResourceType IS NOT NULL AND CreateDate".(is_null($CreateDate)?' IS NULL':"='$CreateDate'")." LIMIT 4", O_COL_ASSOC);
					$j=0;
					foreach($b as $ID=>$SKU){
						$j++;
						if($j==4){
							echo ', more...';
							break;
						}
						if($j>1 && $j<4)echo ', ';
						
						?><a href="products.php?Items_ID=<?php echo $ID?>" onclick="return ow(this.href,'l1_items','850,700');" title="view this product"><?php echo $SKU;?></a><?php
					}
				}
				?></td>
				<td><?php
				if($Count==1 || $DC==1){
					echo $Creator;
				}else{
					echo '<em class="gray">(various)</em>';
				}
				?></td>
			</tr><?php
		}
	}else{
		?><tr>
		<td colspan="100%"><em class="gray">No records found for that criteria</em></td>
		</tr><?php
	}
	?></tbody>
	</table><?php


}else if($report=='groupings'){
	$a=q("SELECT Grouping, COUNT(*) as count, MIN(ID) as minid, MAX(ID) as maxid, MIN(CreateDate) as mindate, MAX(CreateDate) as maxdate FROM finan_items WHERE ResourceType IS NOT NULL GROUP BY Grouping ORDER BY Grouping", O_ARRAY);
	
	?>
	<h1>Groupings</h1>
	<p class="gray">Click on view link to see all products in the specific grouping</p>
	<table class="yat"><thead>
	<tr>
	<th><h4 class="nullTop nullBottom">Grp.</h4></th>
	<th><h4 class="nullTop nullBottom">Records</h4></th>
	<th class="tac" colspan="2"><h4 class="nullTop nullBottom">ID's</h4></th>
	<th class="tac" colspan="2"><h4 class="nullTop nullBottom">Date Range</h4></th>
	<th rowspan="2">&nbsp;</th>
	</tr>
	<tr>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
	<th>From</th>
	<th>To</th>
	<th class="tac">From</th>
	<th class="tac">To</th>
	</tr>
	</thead><tbody>
	<?php
	foreach($a as $n=>$v){
		?><tr>
		<td class="tac"><?php echo $v['Grouping'];?></td>
		<td class="tac"><?php echo $v['count'];?></td>
		<td><?php echo $v['minid'];?></td>
		<td><?php echo $v['maxid'];?></td>
		<td><?php echo str_replace('/'.date('Y'),'',date('n/j/Y \a\t g:iA',strtotime($v['mindate'])));?></td>
		<td><?php echo str_replace('/'.date('Y'),'',date('n/j/Y \a\t g:iA',strtotime($v['maxdate'])));?></td>
		<td>[<a href="report_generic.php?report=itemsquery&searchtype=grouping&q=<?php echo $v['Grouping'];?>">view</a>]</td>
		</tr><?php
	}
	?>	
	</tbody></table><?php
}
$reportOutput=ob_get_contents();
ob_end_clean();
?>