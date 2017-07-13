<?php

/*
suppress delete and focus

*/
$datasetGroup='products';
$datasetComponent='productsList';
$suppressDatasetPreSubContent=true;
$suppressDataset_complexDataCSS=true;
$datasetTableClass='notused';
$datasetActiveHideControl=true;
$datasetHideEditControls=true;
$datasetHideFooterAddLink=true;
$filterGadgetAlwaysVisible=true;
$filterGadgetSuppressForm=true;
$datasetFile=end(explode('/',__FILE__));

function batch_products($options=array()){
	global $record,$mode,$submode,$dataset;
	global $qr,$fl,$ln,$developerEmail,$fromHdrBugs, $modApType,$modApHandle;
	if(is_array($options)){
		extract($options);	//should contain $field
		unset($options['field']);
	}else{
		$field=$options;
	}
	if($field=='SKU'){
		ob_start();
		?><a href="products.php?Items_ID=<?php echo $record['ID'];?>" onclick="return ow(this.href,'l1_items','850,700');"><strong><?php echo $record['SKU'];?></strong></a><?php
		$out=ob_get_contents();
		ob_end_clean();
		return $out;
	}	
}
$availableCols[$datasetGroup][$modApType][$modApHandle]['scheme']=array(
	'CreateDate'=>array(
		'header'=>'Created',
		'function'=>'col(field=CreateDate&format=date:n/j/Y \a\t<\b\r>g\:iA)',
	),
	'EditDate'=>array(
		'header'=>'Last Edited',
		'function'=>'col(field=EditDate&format=date:n/j/Y \a\t<\b\r>g\:iA)',
	),
	'Batches'=>array(
		'header'=>'<span class="red">*</span>',
	),
	'SKU'=>array(
		'header'=>'SKU',
		'function'=>'batch_products(field=SKU)',
	),
	'Name'=>array(),	
);

if($submode){
	if($submode=='updateDatasetFilters'){
		goto overpass;
	}else if($submode=='setRecords' || $submode=='setRecordsExport'){
		if($tenhanced_default=='byselection'){
			$exportBatch=json_decode(stripslashes($stringify));

				#prn($exportBatch);
				#error_alert(look);

			//--------------- begin Thatcher ----------------				
			unset($a);
			foreach($exportBatch as $n=>$v){
				if(($count[$n]=count($v))>0)$total+=$count[$n];
				foreach($v as $o=>$w){
					$a[$n][$o]=$w;
				}
			}
			if(!$total)error_alert('You do not have any records set '.($submode=='setRecordsExport'?' to batch export':''));
			
			if($submode=='setRecords'){
				$_SESSION['special']['exportBatch']=$a;
				error_alert('Records set for export.  You must do the actual batch export before you sign out.');
			}
			if(!$total)error_alert('Select at least one merchant to export to');
			foreach($exportBatch as $merchant=>$ids){
				if(!array_sum($ids))continue;
				//foreach($ids as $n=>$v)if(!$v)unset($ids[$n]);
				//set the hard merchant flag=0 based on selected records
				if(!$doNotUpdate) q("UPDATE finan_items SET EditDate=EditDate, ".$merchant."_ToBeExported=0 WHERE ID IN(".implode(',',$ids).")");
				prn($qr);
				$i=0;
				foreach($ids as $null=>$ID){
					$i++;
					if($i==1){
						$Batches_ID=q("INSERT INTO gen_batches SET
						Description='Batch export to $merchant',
						StartTime=NOW(),
						Type='Export',
						SubType='$merchant',
						Status='Pending',
						Quantity='".count($ids)."',
						Notes='".($BatchComment ? $BatchComment : "Added by exe page line ".__LINE__)."',
						CreateDate=NOW(),
						Creator='".sun()."'", O_INSERTID);
						prn($qr);
						$batches[]=$Batches_ID;
					}
					q("INSERT INTO gen_batches_entries SET Batches_ID=$Batches_ID, ObjectName='finan_items', Objects_ID=$ID");
					//prn($qr);
				}
			}
			
			$_SESSION['special']['exportBatch']=array();
			?><script language="javascript" type="text/javascript">
			window.parent.location='/gf5/console/export_manager.php?disposition=preExport&batches=<?php echo implode(',',$batches);?>';
			</script><?php
			//--------------- end Thatcher ----------------				
		}else if($tenhanced_default=='byquery'){
			if($output=='spreadsheet'){
				if(!count($fields))error_alert('Select the fields to export');
				//set fields
				$availableCols[$datasetGroup][$modApType][$modApHandle]['scheme']=array(
					'ID'=>array(),
					'SKU'=>array(),
				);
				foreach($fields as $v)$availableCols[$datasetGroup][$modApType][$modApHandle]['scheme'][$v]=array();
				$mode='refreshComponent';
				$submode='exportDataset';
				$refreshComponentOnly=true;
				require($COMPONENT_ROOT.'/comp_300_products_hmr_v100.php');

				if($submode=='exportDataset'){
					//output CSV
					$assumeErrorState=false;
					$suppressNormalIframeShutdownJS=true;
					if($exportAsExcel){
						header('Content-Type: application/vnd.ms-excel');
						header('Content-Disposition: attachment;filename="'.$exportFileName.'"');
						header('Cache-Control: max-age=0');
					}else{
						header("Content-Type: application/octet-stream");
						header('Content-Disposition: attachment; filename="'.$component.'['.count($records).']-@'.date('Y-m-d_H-i-s').'.csv"');
					}
					echo $datasetOutput;
					exit;
				}
			}else if($output=='batch'){
				$doNotUpdate=true;
				$mode='refreshComponent';
				$submode='exportDataset';
				$refreshComponentOnly=true;
				$suppressDatasetComponent=true;
				require($COMPONENT_ROOT.'/comp_300_products_hmr_v100.php');
				
				$BatchComment=$BatchComment2;
				unset($exportBatch);
				unset($count);
				unset($batches);
				if(!count($selectedVendors))error_alert('To export a batch by selection, choose at least one vendor');
				if(!count($records))error_alert('Abnormal error, or no records present to batch!');
				foreach($selectedVendors as $vendor){
					foreach($records as $n=>$v){
						$exportBatch[strtoupper($vendor)][]=$v['ID'];
					}
				}
				if(count($_SESSION['special']['filterQuery'][$dataset])){
					$andor=($_SESSION['special']['filterQueryJoin'][$dataset]==1?' or ':' and ');
					$BatchComment=trim($BatchComment) . (trim($BatchComment)?'<br />':'').'<div class="filter">'.implode($andor,$_SESSION['special']['filterQuery'][$dataset]).'</div>';
				}
				
				//--------------- begin Thatcher ----------------				
				unset($a);
				foreach($exportBatch as $n=>$v){
					if(($count[$n]=count($v))>0)$total+=$count[$n];
					foreach($v as $o=>$w){
						$a[$n][$o]=$w;
					}
				}
				if(!$total)error_alert('You do not have any records set '.($submode=='setRecordsExport'?' to batch export':''));
				
				if($submode=='setRecords'){
					$_SESSION['special']['exportBatch']=$a;
					error_alert('Records set for export.  You must do the actual batch export before you sign out.');
				}
				if(!$total)error_alert('Select at least one merchant to export to');
				foreach($exportBatch as $merchant=>$ids){
					if(!array_sum($ids))continue;
					//foreach($ids as $n=>$v)if(!$v)unset($ids[$n]);
					//set the hard merchant flag=0 based on selected records
					if(!$doNotUpdate) q("UPDATE finan_items SET EditDate=EditDate, ".$merchant."_ToBeExported=0 WHERE ID IN(".implode(',',$ids).")");
					prn($qr);
					$i=0;
					foreach($ids as $null=>$ID){
						$i++;
						if($i==1){
							$Batches_ID=q("INSERT INTO gen_batches SET
							Description='Batch export to $merchant',
							StartTime=NOW(),
							Type='Export',
							SubType='$merchant',
							Status='Pending',
							Quantity='".count($ids)."',
							Notes='".addslashes(($BatchComment ? $BatchComment : "Added by exe page line ".__LINE__))."',
							CreateDate=NOW(),
							Creator='".sun()."'", O_INSERTID);
							prn($qr);
							$batches[]=$Batches_ID;
						}
						q("INSERT INTO gen_batches_entries SET Batches_ID=$Batches_ID, ObjectName='finan_items', Objects_ID=$ID");
						//prn($qr);
					}
				}
				
				$_SESSION['special']['exportBatch']=array();
				?><script language="javascript" type="text/javascript">
				window.parent.location='/gf5/console/export_manager.php?disposition=preExport&batches=<?php echo implode(',',$batches);?>';
				</script><?php
				//--------------- end Thatcher ----------------				
			}
		}else if($tenhanced_default=='byupload'){
			if($createBatch && !count($selectedVendors2))error_alert('Select at least one vendor to export to');
			
			if(!is_uploaded_file($_FILES['uploadFile_1']['tmp_name']))
			error_alert('Abnormal error, unable to upload file');
			$fields=q("EXPLAIN finan_items", O_ARRAY);
			foreach($fields as $n=>$v){
				$fields[strtolower($v['Field'])]=$v;
				unset($fields[$n]);
			}
			$i=0;
			unset($a,$systemid,$systemsku);
			$fp=fopen($_FILES['uploadFile_1']['tmp_name'],'r');
			while($r=fgetcsv($fp,100000)){
				$i++;
				if($i==1){
					foreach($r as $n=>$v){
						if(strtolower($v)=='id'){
							$systemid=$n;
							continue;
						}
						if(strtolower($v)=='sku'){
							$systemsku=$n;
							continue;
						}
						if($fields[strtolower(preg_replace('/[^a-z0-9_]*/i','',$v))])$fieldMap[$n]=$fields[strtolower(preg_replace('/[^a-z0-9_]*/i','',$v))]['Field'];
					}
					if(!isset($systemid) || !isset($systemsku))error_alert('ID or SKU column was missing!');
					$fieldString='ID, SKU, EditDate, '.implode(', ',$fieldMap);
					continue;
				}
				$id=$r[$systemid];
				$all[$id]=$id;
				foreach($r as $n=>$v){
					if(!($rd=q("SELECT $fieldString FROM finan_items WHERE ResourceType IS NOT NULL AND ID=".$r[$systemid], O_ROW))){
						//cannot find record!
						continue;
					}
					if(strtoupper($r[$systemsku])!=strtoupper($rd['SKU'])){
						//sku changes not allowed yet
						continue;
					}
					if($fieldMap[$n]=='CreateDate' || $fieldMap[$n]=='EditDate'){
						$v=date('Y-m-d H:i:s',strtotime($v));
					}
					if($n==$systemid || $n==$systemsku)continue;
					if(!$fieldMap[$n])continue;
					if($rd[$fieldMap[$n]]!=$v){
						$str.=($fieldMap[$n].'|:|'.$rd[$fieldMap[$n]].'|:|'.$n.'|:|'.$v.'|')."\n";
						$updates[$id][$fieldMap[$n]]=$v;
					}
				}
			}
			if(count($updates)){
				foreach($updates as $id=>$v){
					$sql='UPDATE finan_items SET ';
					foreach($v as $o=>$w)$sql.=$o.'=\''.addslashes($w).'\',';
					if($updateTimestamp){
						$sql.=' Editor=\''.sun().'\',';
					}else{
						$sql.=' EditDate=EditDate,';
					}
					$sql=rtrim($sql,',');
					$sql.=' WHERE ID='.$id;

					mail($developerEmail,'query',$sql,$fromHdrBugs);

					ob_start();
					q($sql,ERR_ECHO);
					$err=ob_get_contents();
					ob_end_clean();
					if($err){
						
					}
				}
			}else{
				error_alert('No records needed updating!');
			}
			mail($developerEmail,'look',$str,$fromHdrBugs);
			
			if($createBatch){
				$BatchComment=$BatchComment3;
				unset($exportBatch);
				unset($count);
				unset($batches);
				foreach($selectedVendors2 as $vendor){
					foreach($all as $id){
						if(!$updates[$id] && $limitToChanged)continue;
						$exportBatch[strtoupper($vendor)][]=$id;
					}
				}
				//--------------- begin Thatcher ----------------				
				unset($a);
				foreach($exportBatch as $n=>$v){
					if(($count[$n]=count($v))>0)$total+=$count[$n];
					foreach($v as $o=>$w){
						$a[$n][$o]=$w;
					}
				}
				if(!$total)error_alert('You do not have any records set '.($submode=='setRecordsExport'?' to batch export':''));
				
				if($submode=='setRecords'){
					$_SESSION['special']['exportBatch']=$a;
					error_alert('Records set for export.  You must do the actual batch export before you sign out.');
				}
				if(!$total)error_alert('Select at least one merchant to export to');
				foreach($exportBatch as $merchant=>$ids){
					if(!array_sum($ids))continue;
					//foreach($ids as $n=>$v)if(!$v)unset($ids[$n]);
					//set the hard merchant flag=0 based on selected records
					if(!$doNotUpdate) q("UPDATE finan_items SET EditDate=EditDate, ".$merchant."_ToBeExported=0 WHERE ID IN(".implode(',',$ids).")");
					prn($qr);
					$i=0;
					foreach($ids as $null=>$ID){
						$i++;
						if($i==1){
							$Batches_ID=q("INSERT INTO gen_batches SET
							Description='Batch export to $merchant',
							StartTime=NOW(),
							Type='Export',
							SubType='$merchant',
							Status='Pending',
							Quantity='".count($ids)."',
							Notes='".($BatchComment ? $BatchComment : "Added by exe page line ".__LINE__)."',
							CreateDate=NOW(),
							Creator='".sun()."'", O_INSERTID);
							prn($qr);
							$batches[]=$Batches_ID;
						}
						q("INSERT INTO gen_batches_entries SET Batches_ID=$Batches_ID, ObjectName='finan_items', Objects_ID=$ID");
						//prn($qr);
					}
				}
				
				$_SESSION['special']['exportBatch']=array();
				?><script language="javascript" type="text/javascript">
				window.parent.location='/gf5/console/export_manager.php?disposition=preExport&batches=<?php echo implode(',',$batches);?>';
				</script><?php
				//--------------- end Thatcher ----------------				
			}
			
			
			error_alert('by upload not developed');
		}else error_alert('Click on the tab for the export type you want to perform');
	}
	eOK();
}
overpass:

if(!$refreshComponentOnly){
	?>
	<style type="text/css">
	.tabSectionStyleIII{
		background:rgba(201, 218, 248, 0.35);
		}
	.notused td{
		padding:3px 5px 1px 2px;
		}
	</style>
	<script language="javascript" type="text/javascript">
	function toggle(o){
		var M=o.id.replace('c_','');
		$('._'+M).each(
			function(i,v){
				$(this).attr('checked',o.checked);
			}
		);
		detectChange=1;
	}
	$(document).ready(function(){
		$('#setRecords').click(function(){g('submode').value='setRecords';});
		$('#setRecordsExport').click(function(){g('submode').value='setRecordsExport';});
		$('._MIVA, #c_MIVA').change(function(){ ct=0; $('._MIVA').each(function(){ $(this).attr('checked')?ct++:'' }); g('_MIVA_ct').innerHTML=ct; });
		$('._AMAZON, #c_AMAZON').change(function(){ ct=0; $('._AMAZON').each(function(){ $(this).attr('checked')?ct++:'' }); g('_AMAZON_ct').innerHTML=ct; });
		$('._EBAY, #c_EBAY').change(function(){ ct=0; $('._EBAY').each(function(){ $(this).attr('checked')?ct++:'' }); g('_EBAY_ct').innerHTML=ct; });
		$('#form1').submit(function(){
			v={};
			v['MIVA']=[];
			v['AMAZON']=[];
			v['EBAY']=[];
			$('._MIVA').each(function(){ if($(this).attr('checked'))v['MIVA'][v['MIVA'].length]=$(this).val(); });
			$('._AMAZON').each(function(){ if($(this).attr('checked'))v['AMAZON'][v['AMAZON'].length]=$(this).val(); });
			$('._EBAY').each(function(){ if($(this).attr('checked'))v['EBAY'][v['EBAY'].length]=$(this).val(); });
			g('stringify').value=stringify(v);
		});
	});
	</script><?php
}

$vend=array_keys($vendors['settings']);
$vendorFilter=implode('_ToBeExported=1 OR ',$vend).'_ToBeExported=1';
$vendorFields='i.'.implode('_ToBeExported, i.',$vend).'_ToBeExported';
if(!$order)$order='i.CreateDate DESC';
$records=q("SELECT i.ID, i.CreateDate, i.EditDate, i.SKU, i.Name, COUNT(DISTINCT e.ID) AS Exported, $vendorFields FROM finan_items i LEFT JOIN gen_batches_entries e ON i.ID=e.Objects_ID AND e.ObjectName='finan_items' WHERE ($vendorFilter) AND ResourceType IS NOT NULL GROUP BY i.ID ORDER BY $order", O_ARRAY);
?>
<h2>Select Records for Batch Export </h2>

<?php ob_start(); //---------------- begin tabs --------------- ?>
<h3>Total of <?php echo count($records);?></h3>
<p class="gray">The records shown below represent new records, or records flagged as &quot;to be exported&quot; for <u>at least one vendor</u>. Only those records you specifically check will be exported in the next batch. You are encouraged (but not required) to keep the records for batches to all vendors &quot;in synch&quot;. Click Set Records &amp; Export below when complete, or just Set Records if you wish to set some records and come back later. </p>
<p class="red"><strong>NOTE</strong>: Your selections will be removed if you log out 

<input name="mode" type="hidden" id="mode" value="refreshComponent" />
<input name="component" type="hidden" id="component" value="<?php echo $datasetComponent . ($datasetFile ? ':'.$datasetFile.':'.md5($datasetFile.$MASTER_PASSWORD) : '')?>" />
<input name="submode" type="hidden" id="submode" />
<input name="stringify" type="hidden" id="stringify" />
<input name="suppressPrintEnv" type="hidden" id="suppressPrintEnv" value="1" />
</p>
Comment for this batch (optional): 
<input name="BatchComment" type="text" id="BatchComment" value="<?php echo h($BatchComment);?>" size="55" maxlength="255" />
<br />

<table id="toBeExported" class="yat">
<thead>
<tr>
  <th rowspan="2" valign="bottom"><a href="export_select.php?order=<?php echo urlencode(strstr($order,'CreateDate DESC')?'i.CreateDate ASC':'i.CreateDate DESC');?>" title="Sort by this field">Created</a></th>
  <th rowspan="2" valign="bottom"><a href="export_select.php?order=<?php echo urlencode(strstr($order,'EditDate DESC')?'i.EditDate ASC':'i.EditDate DESC');?>" title="Sort by this field">Last<br />
    Edited</a></th>
	<th rowspan="2" valign="bottom" style="font-size:209%;color:darkred;font-family:Georgia, 'Times New Roman', Times, serif;" title="This column indicates whether the record has been exported before">*</th>
  <th rowspan="2" valign="bottom"><a href="export_select.php?order=<?php echo urlencode(strstr($order,'SKU DESC')?'i.SKU ASC':'i.SKU DESC');?>" title="Sort by this field">SKU</a></th>
	<th rowspan="2" valign="bottom">Name</th>
	<?php
	foreach($vend as $v){
		?><th><?php echo strtoupper($v);?></th><?php
	}
	?>
</tr>
<tr>
	<?php
	foreach($vend as $v){
		?><th class="tac" style="padding:4px;border-bottom:1px solid #000;">
		<input type="checkbox" name="checkbox" id="c_<?php echo strtoupper($v);?>" onclick="toggle(this);" title="Check/uncheck all records in this column" />
		</th><?php
	}
	?>
</tr>
</thead>
<?php
ob_start();
?>
<tbody>
<?php
if($records){
	$i=0;
	foreach($records as $n=>$v){
		extract($v);
		foreach($vend as $w)if($GLOBALS[$w.'_ToBeExported'])$GLOBALS[$w]++;
		?><tr>
		  <td><?php echo preg_replace('/[-0: ]/','',$CreateDate) ? date('n/j/Y \a\t g:iA',strtotime($CreateDate)) : '<em class="gray">unknown</em>';?></td>
			<td><?php if(abs(strtotime($CreateDate)-strtotime($EditDate))>15 && preg_replace('/[-0: ]/','',$EditDate))echo (preg_replace('/[-0: ]/','',$EditDate) ? date('n/j/Y \a\t g:iA',strtotime($EditDate)) : '<em class="gray">unknown</em>');?></td>
			<td class="gray"><?php echo $Exported;?></td>
			<td><a href="products.php?Items_ID=<?php echo $ID;?>" title="view/edit this record" onclick="return ow(this.href,'l1_items','800,700');">
		    <?php echo $SKU;?></a></td>
			<td><?php echo $Name;?></td>
			<?php 
			$i=0;
			foreach($vend as $w){ 
			$i++;
			?>
			<td class="tac" <?php echo $i>1?'class="bl"':''?> <?php echo @in_array($ID,$_SESSION['special']['exportBatch'][$w])?'style=background-color:powderblue;"':''?>><?php
			if($GLOBALS[strtoupper($w).'_ToBeExported']){
				?>
				<input name="exportBatch[<?php echo strtoupper($w)?>][<?php echo $ID;?>]" type="checkbox" id="<?php echo strtoupper($w).'_ToBeExported['.$ID.']';?>" class="_<?php echo strtoupper($w);?>" value="<?php echo $ID;?>" onchange="dChge(this);" <?php echo @in_array($ID,$_SESSION['special']['exportBatch'][$w])?'checked':''?> /></td>
				<?php
			}else{
				?>&nbsp;<?php
			}
			?>
			<?php } ?>
		</tr><?php
	}
}else{
	?><tr>
	<td colspan="103"><em class="gray">No records found for that criteria</em></td>
	</tr><?php
}
?>
</tbody>
<?php
$body=ob_get_contents();
ob_end_clean();
?><tfoot>
<tr>
	<td colspan="4" rowspan="2">TOTALS:</td>
	<td class="tar">Selected:</td>
	<?php foreach($vend as $w){ ?>
	<td id="_<?php echo strtoupper($w);?>_ct" class="tac"><?php
	if($n=count($_SESSION['special']['exportBatch'][$w])){
		echo $n;
	}
	 ?></td>
	<?php } ?>
</tr>
<tr>
  <td class="tar">Available for export:</td>
	<?php foreach($vend as $w){ ?>
	<td class="tac"><span style="font-family:Georgia, 'Times New Roman', Times, serif"><?php echo $GLOBALS[strtoupper($w)];?></span></td>
	<?php } ?>
</tr>
</tfoot><?php
echo $body;
?>
</table>
<?php
get_contents_tabsection('byselection');
?>
<div>
<label><input name="output" type="radio" value="spreadsheet" checked="checked" />
Export a Spreadsheet</label>
<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php
$b=$_SESSION['userSettings']['exportSelectFields'] ? $b=explode(',',$b) : $b=array('category','subcategory','name','description','createdate','creator','metatitle','metakeywords','subtheme',);
$a=array();
foreach(q("EXPLAIN finan_items", O_ARRAY) as $n=>$v){
	$a[strtolower($v['Field'])]=$v;
	$a[strtolower($v['Field'])]['order']=(in_array(strtolower($v['Field']),$b)?1:2);
}
$a=subkey_sort($a,'order');
?>
<select name="fields[]" size="10" multiple>
<?php
foreach($a as $n=>$v){
	if($n=='id' || $n=='sku')continue;
	if(!$itemsFieldsUse[$n])continue;
	?><option value="<?php echo $v['Field'];?>" <?php echo in_array(strtolower($v['Field']),$b)?'selected':'';?>><?php echo $v['Field'];?></option><?php
}
?>
</select>
<span class="gray">(ID and SKU are always exported)</span><br />
<label><input name="output" type="radio" value="batch" checked="checked" />
Create a Batch</label> <span class="gray">(Select at least one)</span>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input name="selectedVendors[MIVA]" type="checkbox" id="selectedVendors[MIVA]" value="MIVA" /> 
MIVA</label><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input name="selectedVendors[AMAZON]" type="checkbox" id="selectedVendors[AMAZON]" value="AMAZON" /> 
Amazon</label><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label><input name="selectedVendors[EBAY]" type="checkbox" id="selectedVendors[EBAY]" value="EBAY" /> 
eBay</label>
<br />
Comment for this batch (optional): 
<input name="BatchComment2" type="text" id="BatchComment2" value="<?php echo h($BatchComment2);?>" size="55" maxlength="255" />
<br />
<br />
<br />
<?php
require($COMPONENT_ROOT.'/comp_300_products_hmr_v100.php');
//require($MASTER_COMPONENT_ROOT.'/comp_01_filtergadget_v200a_i2.php');
//prn($_SESSION);
?>
</div>
<?php
get_contents_tabsection('byquery');
?>
<div>
	<h2>Upload a File</h2>
	<input name="uploadFile_1" type="file" id="uploadFile_1" />
	<br />
  <span class="gray">File must have the system ID in place. Changes to SKU are not allowed. Making a backup (the option below) is highly recommended. </span><br />
	<br />
	<label><input name="createBatch" type="checkbox" id="createBatch" value="1" checked="checked" />
	Create a batch from updated records</label><br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<label>
	<input name="selectedVendors2[MIVA]" type="checkbox" id="selectedVendors2[MIVA]" value="MIVA" />
	MIVA</label>
	<br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<label>
	<input name="selectedVendors2[AMAZON]" type="checkbox" id="selectedVendors2[AMAZON]" value="AMAZON" />
	Amazon</label>
	<br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<label>
	<input name="selectedVendors2[EBAY]" type="checkbox" id="selectedVendors2[EBAY]" value="EBAY" />
	eBay</label>
	<br />
	<br />
	<label><input name="limitToChanged" type="checkbox" id="limitToChanged" value="1" checked="checked" /> 
	Limit batch to records that are actually changed</label>
	<br />
	<!--
	<label><input name="emailCopyPreChange" type="checkbox" id="emailCopyPreChange" value="1" checked="checked" /> 
	Email me a copy of rows that will actually be changed <span class="gray">(as they were before the update)</span></label>
	<br />
	-->
	<label><input name="updateTimestamp" type="checkbox" id="updateTimestamp" value="1" checked="checked" /> 
	Update the timestamp (Edit Date field)</label><br />
	Comment for this batch (optional):
    <input name="BatchComment3" type="text" id="BatchComment3" value="<?php echo h($BatchComment3);?>" size="55" maxlength="255" /> 
	<br /> 
	
</div>
<?php
get_contents_tabsection('byupload');
?>
help
<?php
get_contents_tabsection('help');
?>

<?php
tabs_enhanced(array(
	'byselection'=>array(
		'label'=>'By selection'
	),
	'byquery'=>array(
		'label'=>'By query'
	),
	'byupload'=>array(
		'label'=>'By upload'
	),
	'help'=>array(
		'label'=>'Help'
	),
),
	array(
		'fade'=>true,
		'status_field'=>true,
	)
);
?>
