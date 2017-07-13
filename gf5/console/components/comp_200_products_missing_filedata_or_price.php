<?php
$component='recordsMissingDataPrice';
$componentKey=md5(__FILE__.$MASTER_PASSWORD);

if($submode=='recalc'){
	sleep(2);
	if(minroles()>ROLE_ADMIN){
		?><script language="javascript" type="text/javascript">
		window.parent.g('recalcProgress').style.display='none';
		alert('Only an administrator may perform this action');
		</script><?php
		$eOK();
	}
	if($a=q("SELECT ID, SKU, Name, CreateDate, Creator, FileSize, Width1, Height1, IF(FileSize>0 AND Width1>0 AND Height1>0,1,0) AS HasDimensions FROM finan_items WHERE ResourceType IS NOT NULL AND (FileSize=0 OR Width1=0 OR Height1=0 OR HMR_Price1=0 OR HMR_Price2=0 OR HMR_Price3=0 OR HMR_Price4=0 OR HMR_Price1 IS NULL OR HMR_Price2 IS NULL OR HMR_Price3 IS NULL OR HMR_Price4 IS NULL) ORDER BY SKU", O_ARRAY)){
		foreach($a as $v){
			extract($v);
			if(!$HasDimensions)continue;
			if(!$Width1 || !$Height1)continue; //abnormal
			if($Width1>$Height1){
				$max='Width1';$min='Height1';
			}else{
				$min='Width1';$max='Height1';
			}
			$divisor=$$min / 23;
			$overflow=$$max/$divisor;

			$HMR_Price1= round(((23 * $overflow) * PRINT_BASIC) + PRINT_BASIC_MARKUP,2);
			if(fmod($HMR_Price1*100,2))$HMR_Price1+=.01;
			$HMR_Price2= round((23 * $overflow) * PRINT_LAMINATED,2);
			if(fmod($HMR_Price2*100,2))$HMR_Price2+=.01;
			$HMR_Price3= round((23 * $overflow) * PRINT_GICLEE,2);
			if(fmod($HMR_Price3*100,2))$HMR_Price3+=.01;
			$HMR_Price4= round((23 * $overflow) * PRINT_CANVAS,2);
			if(fmod($HMR_Price4*100,2))$HMR_Price4+=.01;
			
			$v['HMR_Price1']=$HMR_Price1;
			$v['HMR_Price2']=$HMR_Price2;
			$v['HMR_Price3']=$HMR_Price3;
			$v['HMR_Price4']=$HMR_Price4;
			
			q("UPDATE finan_items SET EditDate=EditDate, HMR_Price1='".number_format($HMR_Price1,2)."', HMR_Price2='".number_format($HMR_Price2,2)."', HMR_Price3='".number_format($HMR_Price3,2)."', HMR_Price4='".number_format($HMR_Price4,2)."' WHERE ID=$ID");
			$updated++;
			$updateList[]=$v;
		}
	}else{
	}
	error_alert($updated ? 'Total of '.$updated.' record'.($updated==1?'':'s').' were updated':'Pardon me but I found nothing that appeared to need recalculating', $updated?1:'');
	if($SKUs){
		$recipient=q("SELECT * FROM bais_universal WHERE un_username='ric'", O_ROW);
		$emailTo='rglaubinger@yahoo.com,sales@amazingcity.com,sfullman@compasspointmedia.com';
		$emailSource=$_SERVER['DOCUMENT_ROOT'].'/gf5/console/emails/email_1030_prices_updated.php';
		require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
	}
}
if(!$refreshComponentOnly){
	?><style type="text/css">
	#recordsMissingDataPrice{
	}
	</style>
	<script language="javascript" type="text/javascript">
	function recalc(){
		g('recalcProgress').style.display='inline';
		window.open('/gf5/console/resources/bais_01_exe.php?mode=refreshComponent&component=<?php echo $component;?>&componentFile=comp_200_products_missing_filedata_or_price.php&componentKey=<?php echo $componentKey;?>&submode=recalc','w2');
	}
	</script><?php
}
$a=q("SELECT ID, SKU, Name, CreateDate, Creator, FileSize, Width1, Height1, IF(FileSize>0 AND Width1>0 AND Height1>0,1,0) AS HasDimensions	
	FROM finan_items WHERE ResourceType IS NOT NULL AND (FileSize=0 OR Width1=0 OR Height1=0 OR HMR_Price1=0 OR HMR_Price2=0 OR HMR_Price3=0 OR HMR_Price4=0 OR HMR_Price1 IS NULL OR HMR_Price2 IS NULL OR HMR_Price3 IS NULL OR HMR_Price4 IS NULL) ORDER BY SKU", O_ARRAY);
?>
<div id="recordsMissingDataPrice">
<h2>Products missing filesize, dimensions or price data <?php echo count($a)?'('.count($a).')':'<em class="gray" style="font-weight:400;">none</em>';?></h2>
<?php
if(count($a)){
	?>
	<input type="button" name="Button" value="Recalculate.." onclick="recalc()" /> <span id="recalcProgress" style="display:none;"><img src="/images/i/ani/ani-fb-orange.gif" width="16" height="11" /> Recalculating prices.. </span>
	<br />
  <?php
}
?>
<table id="hcTable3">
<thead><tr>
	<th>SKU</th>
	<th>Name</th>
	<th>Added..</th>
	<th>by..</th>
	<th>Dimensions?</th>
	<th>Prices?*</th>
</tr></thead>
<tbody>
<?php
if($a){
	foreach($a as $v){
		extract($v);
		?><tr>
		<td><a href="products.php?Items_ID=<?php echo $ID;?>" onclick="return ow(this.href,'l1_items','850,700');"><?php echo $SKU;?></a></td>
		<td><?php echo $Name;?></td>
		<td nowrap="nowrap"><?php echo date('n/j \a\t g:iA',strtotime($CreateDate));?></td>
		<td><?php echo $Creator;?></td>
		<td class="tac"><?php echo $HasDimensions?'<span style="color:darkgreen;">Yes</span>':'&nbsp;';?></td>
		<td class="tac"><?php echo $HMR_Price1>0 && $HMR_Price2>0 && $HMR_Price3>0 && $HMR_Price4>0 ? '<span style="color:darkgreen;">Yes</span>':'&nbsp;';?></td>
		</tr><?php
	}
}else{
	?><tr>
	<td colspan="100%"><span class="gray">(No products missing file data or prices)</span></td>
	</tr><?php
}
?>
</tbody>
</table>
</div>