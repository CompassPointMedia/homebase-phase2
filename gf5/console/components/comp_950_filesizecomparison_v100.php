<?php
if($submode=='query'){
	foreach($sizes as $n=>$v){
		if(!trim($v)){
			unset($sizes[$n]);
			continue;
		}
		if(preg_match('/^([.0-9]+)\s*x\s*([.0-9]+)$/',$v,$m)){
			$sizes[$n]=array('width'=>$m[1],'height'=>$m[2]);
		}else error_alert('At least one of your frame sizes is an invalid format.  Enter for example 9x12 or 8.95 x11.5');
	}
	foreach($Batches_ID as $n=>$v)if(!$v)unset($Batches_ID[$n]);
	if($ar){
		if(!$ar_gtlt || !$ar_range)error_alert('You have chosen to filter by aspect ratio. Include all necessary fields for this, or uncheck this filter');
		function gtlt($rlx,$a,$b){
			return ($rlx=='greater than'? $a>$b : $a<$b);
		}
	}
	if($stats=q("SELECT f.* FROM _v_hbs_filestats_items f ".(count($Batches_ID) ? "JOIN gen_batches_entries e ON e.Objects_ID=f.Filestats_ID AND e.Batches_ID IN(".implode(',',$Batches_ID).")" : '')."
	WHERE f.Width>0 AND f.Height>0", O_ARRAY)){
		?><div id="filesizeComparisonResults"><?php
		foreach($stats as $n=>$v){
			if($ar && !gtlt($ar_gtlt, max($v['Width'],$v['Height'])/min($v['Width'],$v['Height']), $ar_range)){
				$filtered++;
				unset($stats[$n]);
				continue;
			}
			if($v['Items_ID'])$Items_ID[]=$v['Items_ID'];
			$stats[$n]['orientation']=($v['Width']<=$v['Height']?'Portrait':'Landscape');
			foreach($sizes as $o=>$w){
				//we want rotation, undercoverage, blank length, and ppi
				
				//we want to fit the entire image inside the box
				$dmin=min($w['width'],$w['height']);
				$dmax=max($w['width'],$w['height']);
				$fmax=max($v['Width'],$v['Height']);
				$fmin=min($v['Width'],$v['Height']);

				$stats[$n]['sizes'][$o]['fmin']=$fmin;
				$stats[$n]['sizes'][$o]['fmax']=$fmax;
				$stats[$n]['sizes'][$o]['dmin']=$dmin;
				$stats[$n]['sizes'][$o]['dmax']=$dmax;

				if($dmax/$dmin <= $fmax/$fmin){
					$stats[$n]['sizes'][$o]['shape']='tall';

					//now we expand/contract until two walls touch
					$stats[$n]['sizes'][$o]['ppi']=$ppi=round(max($fmax/$dmax,$PixelDensity),3);
					if($fmax/$dmax<$PixelDensity){
						$stats[$n]['sizes'][$o]['vertical_under']=round($dmax - $fmax/$PixelDensity,3);
					}
					$stats[$n]['sizes'][$o]['horizontal_under']=round($dmin - $fmin/$ppi,3);

				}else{
					$stats[$n]['sizes'][$o]['shape']='wide';

					//now we expand/contract until two walls touch
					$stats[$n]['sizes'][$o]['ppi']=$ppi=round(max($fmin/$dmin,$PixelDensity),3);
					if($fmin/$dmin<$PixelDensity){
						$stats[$n]['sizes'][$o]['horizontal_under']=round($dmin - $fmin/$PixelDensity,3);
					}
					$stats[$n]['sizes'][$o]['vertical_under']=round($dmax - $fmax/$ppi,3);

				}
				
			}
		}
		if($Items_ID)$Items_ID=q("SELECT ID, SKU FROM finan_items WHERE ID IN(".implode(',',$Items_ID).")", O_COL_ASSOC);
		?>
		<h2>Total of <?php echo count($stats);?> Pictures</h2>
		<table class="fscomp"><?php
		$i=0;
		foreach($stats as $n=>$v){
			$i++;
			if($i==1){
				?><thead>
				<tr>
				<th>&nbsp;</th>
				<th>File</th>
				<th>Or.</th>
				<?php
				foreach($v['sizes'] as $o=>$w){
					?><th>
					<h3 class="nullTop"><?php echo $w['dmin'].'&nbsp;x&nbsp;'.$w['dmax'].'<br />';?></h3>
					<p class="gray"><?php echo '('.round($w['dmin']/$w['dmax'],3).')';?></p>
					</th><?php
				}
				if($showRawOutput)
				foreach($v['sizes'] as $o=>$w){
					?><th>&nbsp;</th><?php
				}
				?>
				</tr>
				</thead><tbody><?php
			}
			?><tr>
			<td class="img">
			<?php
			if($SKU=$Items_ID[$v['Items_ID']]){
				ob_start();
				$a=tree_image(array(
					'src'=>'images/documentation/'.$GCUserName.'/'.$SKU.'.jpg',
					'disposition'=>'105x105',
					'boxMethod'=>2,
				));
				$img=ob_get_contents();
				ob_end_clean();
				?><a href="<?php echo current(explode('&disposition=',$a['src']));?>" onclick="return ow(this.href,'l1_img','800,800');" title="view full-size image"><?php echo $img;?></a><?php
			}
			?>
			</td>
			<td><strong><?php
			//link entered records
			if($v['Items_ID']){ ?><a href="products.php?Items_ID=<?php echo $v['Items_ID'];?>" onclick="return ow(this.href,'l1_items','785,700');"><?php }
			?><?php echo $v['FileName'];?><?php
			if($v['Items_ID']){ ?></a><?php }
			?></strong><br />
			<?php echo $v['Width'] .'x'.$v['Height'];?>
			</td>
			<td><?php echo substr($v['orientation'],0,1);?></td>
			<?php
			foreach($v['sizes'] as $o=>$w){
				$px=7;
				$bw=round($w['dmin']*$px,0);
				$bh=round($w['dmax']*$px,0);
				$ow=round($w['fmin']/$w['ppi'] * $px,0);
				$oh=round($w['fmax']/$w['ppi'] * $px,0);
				if($w['horizontal_under']/$w['dmin'] > $UnderCoverage || $w['vertical_under']/$w['dmax'] > $UnderCoverage){
					$status='short';
				}else{
					$status='ok';
				}
				?><td class="tac <?php echo $status;?>">
				<div class="bound tac" style="width:<?php echo $bw;?>px;height:<?php echo $bh;?>px;">
				<div class="outline tac" style="width:<?php echo $ow;?>px;height:<?php echo $oh-10;?>px;">
				<?php 
				echo 
				'<strong>'.round($w['dmin']-$w['horizontal_under'],2).'</strong>'
				.'<br />x<br />'.
				'<strong>'.round($w['dmax']-$w['vertical_under'],2).'</strong>';
				echo '<br />';
				echo round($w['ppi'],2).'ppi';
				?>
				</div>
				</div>
				</td><?php
			}
			if($showRawOutput)
			foreach($v['sizes'] as $o=>$w){
				?><td><?php prn($w);?></td><?php
			}
			?></tr><?php
		}
		?></tbody>
		</table>
		<?php
		//prn($stats);		
		?>
		</div>
		<script language="javascript" type="text/javascript">
		try{
		window.parent.g('filesizeComparisonResults').innerHTML=document.getElementById('filesizeComparisonResults').innerHTML;
		}catch(e){ }
		</script>	
		<?php
	}else error_alert('No matches were found in your search criteria');
	
	eOK();
}
$mode='filesizeComparison';
if(!$PixelDensity)$PixelDensity=125;
if(!$UnderCoverage)$UnderCoverage=.15;
if(!$refreshComponentOnly){
	?><style type="text/css">
	
	.ok .bound{
		border:2px solid darkgreen;	
		}
	.short .bound{
		border:2px solid darkred;	
		}
	.ok .outline{
		background-color:cornsilk;
		}
	.short .outline{
		background-color:pink;
		}
	.bound{
		background-color:#eee;
		}
	.fscomp th{
		background-color:DarkSeaGreen;
		}
	.fscomp{
		border-collapse:collapse;
		}
	.fscomp th, .fscomp td{
		padding:2px 5px;
		}
	.fscomp th h3{
		padding:0px;
		margin:0px;
		}
	.outline{
		padding:5px 0px;
		}
	.img img{
		padding:3px;
		border:1px solid #ccc;
		}
	</style>
	<script language="javascript" type="text/javascript">
	
	</script><?php
}
?>
<form method="get" action="resources/bais_01_exe.php" target="w2">
<div id="filesizeComparison">
	<h2>Filesize Comparison and Framing Chart</h2>
	<p class="gray">Select imported images and pending images and determine groups of workable framing sizes</p>
	
	<div class="fr">
		Minimal pixel density/inch: 
		<select name="PixelDensity" id="PixelDensity" onchange="dChge(this);">
		<?php
		for($i=300; $i>=100; $i=$i-5){
			?><option value="<?php echo $i?>" <?php echo round($PixelDensity,0)==$i?'selected':'';?>><?php echo $i.'px';?></option><?php
		}
		?>
		</select>
		<br />
		Acceptable under-coverage: 
		<select name="UnderCoverage" id="UnderCoverage">
		<option value="">&lt;select..&gt;</option>
		<?php
		for($i=0; $i<=85; $i++){
			?><option value="<?php echo round($i/100,2);?>" <?php echo round($UnderCoverage,2)==round($i/100,2)?'selected':''?>><?php echo $i . '%';?></option><?php
		}
		?>
		</select>
		<p class="gray">Amounts over this % will be flagged<br>
		</p>
		  <label>
		  <input name="ar" type="checkbox" id="ar" value="1">
        Only accept files with aspect ratio</label> 
          <select name="ar_gtlt" id="ar_gtlt">
            <option value="greater than">greater than</option>
            <option value="less than">less than</option>
          </select>
          <select name="ar_range" id="ar_range">
		  <option value="">&lt;select..&gt;</option>
		  <?php
		  for($i=.65; $i<=2.50; $i=round($i+.05,2)){
		  	?><option value="<?php echo $i?>"><?php echo $i?></option><?php
		  }
		  ?>
          </select>
		</p>
	</div>
	<?php
	$a=q("SELECT b.ID, b.Description, b.CreateDate, COUNT(*) AS Count
	FROM gen_batches b JOIN gen_batches_entries e ON b.ID=e.Batches_ID WHERE e.ObjectName='hbs_filestats' GROUP BY b.ID ORDER BY b.CreateDate DESC", O_ARRAY);
	
	?>
	Batch: <br /><select name="Batches_ID[]" multiple="multiple" size="<?php echo min(10,count($a)+1);?>" onchange="dChge(this);" id="Batches_ID">
	<option value="">(any)</option>
	<?php
	if($a){
		foreach($a as $v){
			?><option value="<?php echo $v['ID'];?>" <?php echo @in_array($v['ID'],$Batches_ID)?'selected':'';?>><?php echo h($v['ID'].' - '.$v['Description'].' - '.date('n/j/Y \a\t g:iA',strtotime($CreateDate)).' ('.$Count.')');?></option><?php
		}
	}
	
	?>
	</select><br />
	<table cellpadding="0">
	  <tr>
		<td rowspan="4" class="tac" style="font-size:119%;padding:15px;" valign="middle"><h3 align="center">Select Sizes</h3>
		  <p align="center">(WxH)<br />
		  in inches</p>      </td>
		<td><input name="sizes[]" type="text" id="sizes[]" value="<?php echo $sizes[0];?>" onchange="dChge(this);" /></td>
	  </tr>
	  <tr>
		<td><input name="sizes[]" type="text" id="sizes[]" value="<?php echo $sizes[1];?>" onchange="dChge(this);" /></td>
	  </tr>
	  <tr>
		<td><input name="sizes[]" type="text" id="sizes[]" value="<?php echo $sizes[2];?>" onchange="dChge(this);" /></td>
	  </tr>
	  <tr>
		<td><input name="sizes[]" type="text" id="sizes[]" value="<?php echo $sizes[3];?>" onchange="dChge(this);" /></td>
	  </tr>
	</table>
	<label><input name="showRawOutput" type="checkbox" id="showRawOutput" value="1">
	show raw output </label> <br />
	<br />
	<input type="submit" name="Submit" value="Evaluate" onclick="g('submode').value='query';" />
	<input type="button" name="Button" value="Close" onclick="window.close();" />
    <input name="mode" type="hidden" id="mode" value="<?php echo $mode;?>" />
    <input name="submode" type="hidden" id="submode" />
    <input name="ID" type="hidden" id="ID" value="<?php echo $ID;?>" />
	<div id="filesizeComparisonResults">
	<p class="gray">Select files and enter sizes and click Evaluate to see results</p>
	</div>
</div>

</form>