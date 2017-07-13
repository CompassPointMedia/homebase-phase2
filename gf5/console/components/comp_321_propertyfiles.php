<?php
$node=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/files'; //however files can be located all over
if(!function_exists('create_thumbnail'))require($FUNCTION_ROOT.'/function_create_thumbnail_v200.php');
if(!is_dir($node) && !mkdir($node)){
	$msg='Unable to create system folder '.$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/files';
	mail($developerEmail,'error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
	error_alert($msg);
}
if(!is_dir($node.'/.thumbs.dbr') && !mkdir($node.'/.thumbs.dbr')){
	$msg='Unable to create system folder '.$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/files/.thumbs.dbr';
	mail($developerEmail,'error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
	error_alert($msg);
}

if($submode=='deleteFile'){
	if($file=q("SELECT Name FROM relatebase_tree WHERE ID='$Tree_ID' AND Name='$file'", O_ROW)){
		$Objects_ID=q("SELECT Objects_ID FROM gl_ObjectsTree WHERE Tree_ID=$Tree_ID AND ObjectName='gf_objects'", O_VALUE);
		q("DELETE FROM gl_ObjectsTree WHERE Tree_ID=$Tree_ID");
		q("DELETE FROM gf_objects WHERE ID='$Objects_ID'");
		q("DELETE FROM relatebase_tree WHERE ID='$Tree_ID'");
		unlink($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$CGUserName.'/files/'.$file['Name']);
	}else{
		error_alert('unable to locate file for deletion',1);
	}
}
if($files=q("SELECT 
LCASE(t.Name) AS node,
ot.Tree_ID,
o2.Category,
t.Name,
t.FileSize,
t.FileWidth,
t.FileHeight,
IF(u.un_username IS NOT NULL, CONCAT(un_lastname,', ',un_firstname), t.Creator) AS Creator,
t.CreateDate,
t.Editor,
t.EditDate
FROM
gf_objects o1, gf_objects o2, gl_ObjectsTree ot, relatebase_tree t LEFT JOIN bais_universal u ON t.Creator=u.un_username
WHERE
o1.Objects_ID='$Properties_ID' AND
o1.ParentObject='gl_properties' AND
o1.Relationship='Primary Documentation' AND
o2.Objects_ID=o1.ID AND
o2.ParentObject='gf_objects' AND
ot.ObjectName='gf_objects' AND 
ot.Objects_ID=o2.ID AND 
ot.Tree_ID=t.ID", O_ARRAY_ASSOC)){
	foreach($files as $n=>$v){
		$p=tree_id_to_path($v['Tree_ID']);
		if(!file_exists($_SERVER['DOCUMENT_ROOT'].($p))){
			mail($developerEmail,'file deleted on the fly in '.__FILE__,get_globals(),$fromHdrBugs);
			continue;
			//remove on the fly
			q("DELETE FROM relatebase_tree WHERE ID='".$v['Files_ID']."'");
			unset($files[$n]);
		}
	}
}
if(!$refreshComponentOnly){
	?>
	<style type="text/css">
	.fileList{
		border-collapse:collapse;
		background-color:#fff;
		border:1px solid #000;
		}
	.fileList th{
		background-color:#f6e8da;
		color:#000;
		padding:3px 4px 1px 5px;
		}
	.fileList td{
		background-image:url("/images/i/grad/linedottedhoriz-444.png");
		background-position:center bottom;
		background-repeat:repeat-x;
		
		padding:1px 4px 1px 5px;
		}
	.fileList .catRow{
		background-image:url("/images/i/grad/linesolidhoriz-000.png");
		background-position:center bottom;
		background-repeat:repeat-x;
		}
	.fileList td.last{
		padding-right:20px;
		}
	<?php if(count($files)>4){ ?>
	.fileList tbody{
		overflow-x:none;
		overflow-y:scroll;
		height:100px;
		}
	<?php } ?>
	.fileList .icon img{
		padding:2px;
		border:1px solid #ccc;
		margin:1px;
		}
	</style>
	<script language="javascript" type="text/javascript">

	</script>
	<?php
}
?>
<div id="propertyFiles" refreshparams="noparams">
	<input type="button" name="Button" value="Add a File or Brochure.." onclick="return ow('file_loader.php?_cb_Properties_ID=<?php echo $Properties_ID?>&submode=uploadPropertyFile&CategoryGroup=PropertyDocumentationCategories','l1_upload','450,400')" />
	<table class="fileList" width="100%">
	<thead>
	<tr>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
	<th>Name</th>
	<th>Size</th>
	<th>Created</th>
	<th>by..</th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($files){
		foreach($files as $n=>$v){
			$Key=current(explode('_',$v['Name']));
			if($v['Category']!==$catBuffer || !isset($catBuffer)){
				$catBuffer=$v['Category'];
				?><tr class="catRow">
				<td colspan="100%"><h3 class="nullBottom"><?php echo $v['Category']?></h3></td>
				</tr><?php
			}
			?><tr>
			<td valign="middle" align="center" class="icon"><?php
			$showThumb=false;
			if(preg_match('/(jpg|png|gif)$/i',$v['Name'])){
				if($gis=@getimagesize($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/files/.thumbs.dbr/'.$v['Name'])){
					$showThumb=true;
					$width=$height=95;
				}else if(create_thumbnail(
					$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/files/'.$v['Name'],
					$shrink='95,95', 
					$crop='', 
					$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/files/.thumbs.dbr/'.$v['Name'])){
					$showThumb=true;
					$width=$height=95;
				}
			}
			if($showThumb){
				?><img src="/images/reader.php?Tree_ID=<?php echo $v['Tree_ID']?>&Key=<?php echo $Key?>&thumbnail=default" alt="thumbnail view" /><?php
			}else{
				?>&nbsp;<?php
			}
			?></td>
			<td>[<a href="resources/bais_01_exe.php?mode=downloadFile&Tree_ID=<?php echo $v['Tree_ID']?>&file=<?php echo urlencode($v['Name']);?>&suppressPrintEnv=1" target="w2">view</a>]&nbsp;[<a href="resources/bais_01_exe.php?mode=refreshComponent&component=propertyFiles&submode=deleteFile&ID=<?php echo $section=='gl_properties_units'?$Units_ID:$Properties_ID;?>&Tree_ID=<?php echo $v['Tree_ID']?>&file=<?php echo urlencode($v['Name']);?>" target="w2" onclick="return confirm('Are you sure you want to delete this file?');">delete</a>]</td>
			<td><?php echo preg_replace('/^[a-f0-9]+_/i','',$v['Name']);?></td>
			<td><?php echo number_format($v['FileSize']/1024,2).'K';?></td>
			<td><?php echo date('n/j/y',strtotime($v['CreateDate']));?></td>
			<td class="last"><?php echo $v['Creator']?></td>
			</tr><?php
		}
	}else{
		?><tr>
		<td colspan="100%"><em style="color:#aaa;">No files present or viewable</em></td>
		</tr><?php
	}
	?>
	</tbody>
	</table>
</div>