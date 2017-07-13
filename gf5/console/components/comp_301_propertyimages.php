<?php
/*
2012-04-18
$section; //gl_properties || gl_properties_units

*/
if($submode=='propImageDelete'){
	q("DELETE FROM gl_ObjectsTree WHERE Tree_ID=$Tree_ID");
	q("DELETE FROM relatebase_tree WHERE ID=$Tree_ID");
}

if(!$refreshComponentOnly){
	?><style type="text/css">
	.propertyThumbs img{
		border:1px solid #555;
		padding:4px;
		cursor:pointer;
		}
	.fli{
		float:left;
		margin:0px 5px 5px 0px;
		}
	#galleryWrap{
		margin:10px 0px;
		}
	.on div{
		background-color:gold;
		}
	#MainImage div{
		border:1px dotted red;
		}
	#FloorPlan div{
		border:1px dotted blue;
		}
	</style>
	<script language="javascript" type="text/javascript">
	function manageImages(o){
		var action=o.id;
		switch(true){
			case action=='propImageAdd':
				ow('/gf5/console/file_loader.php?<?php echo $section=='gl_properties_units' ? '_cb_Units_ID='.$Units_ID : '_cb_Properties_ID='.$Properties_ID;?>&submode=uploadPropertyPicture&CategoryGroup=<?php echo $section=='gl_properties_units'?'PictureCategoriesUnit':'PictureCategoriesProperty'?>','l1_loader','500,450');
			break;
			case action=='propImageReplace':
				if(!confirm('This will replace the selected image with one that you upload.  This will prevent broken links on search results that are sent by email.  Continue?'))return;
				ow('/gf5/console/file_loader.php?_cb_ReplaceTree_ID='+Tree_ID+'&<?php echo $section=='gl_properties_units' ? '_cb_Units_ID='.$Units_ID : '_cb_Properties_ID='.$Properties_ID;?>&submode=uploadPropertyPicture&CategoryGroup=<?php echo $section=='gl_properties_units'?'PictureCategoriesUnit':'PictureCategoriesProperty'?>','l1_loader','500,450');
			break;
			case action=='propImageDelete':
				if(!confirm('Are you sure you want to remove this image?'))return;
				refreshComponent('propertyImages','','submode='+action+'&<?php echo $section=='gl_properties_units' ? 'Units_ID='.$Units_ID : 'Properties_ID='.$Properties_ID;?>&Tree_ID='+Tree_ID);
			break;
			default:
				//clicking on images
				g('gallery').src=o.firstChild.src.replace('disposition=105x105&boxMethod=2','disposition=570x');
				var a=document.getElementsByClassName('fli');
				for(var i in a){
					a[i].parentNode.className='off';
				}
				o.parentNode.className='on';
				g('propImageReplace').disabled=false;
				g('propImageDelete').disabled=false;
				Tree_ID=o.id.replace('thumb_','');
		}
	}
	</script><?php
}
?>
<div id="propertyImages" class="propertyImages">
<?php if(minroles()>ROLE_AGENT){ ?>
<!-- ability for a client to upload photos? certainly needed but not in scope -->
<?php }else{ ?>
<input id="propImageAdd" type="button" name="Button" value="Add" onclick="manageImages(this)" />
<input id="propImageReplace" type="button" name="Button" value="Replace" disabled="disabled" onclick="manageImages(this)" />
<input id="propImageDelete" type="button" name="Button" value="Delete" disabled="disabled" onclick="manageImages(this)" />
<?php } ?>
<?php
if($a=q("SELECT
	b.ID, b.Name, b.Title, 
	a.Description, a.Category, a.ObjectName
	FROM 
	gl_ObjectsTree a, relatebase_tree b
	WHERE 
	a.Tree_ID=b.ID AND
	".($section=='gl_properties_units' ? "a.Objects_ID='$Units_ID' AND a.ObjectName='gl_properties_units'" : "a.Objects_ID='$Properties_ID' AND a.ObjectName='gl_properties'")."
	ORDER BY IF(a.Category='Main Image', 1,IF(a.Category='Floor Plan',2,3))", O_ARRAY)){
	?>
<div class="propertyThumbs"><?php
	$i=0;
	foreach($a as $v){
		$i++;
		//see if image exists
		if($i==1)$first=$v;
		?>
		<span <?php if($v['Category']=='Main Image' || $v['Category']=='Floor Plan')echo 'id="'.str_replace(' ','',$v['Category']).'"';?> class="off"><div id="thumb_<?php echo $v['ID'];?>" class="fli" onclick="manageImages(this);"><img src="/images/reader.php?Tree_ID=<?php echo $v['ID']?>&Key=<?php echo substr($v['Name'],0,6);?>&disposition=105x105&boxMethod=2" alt="<?php echo h($v['Title']);?>" />
		</div></span>
		<?php
	}
	?></div>
	<div class="cb"> </div>
	<div id="galleryWrap">
	<img  id="gallery" src="/images/reader.php?Tree_ID=<?php echo $first['ID']?>&Key=<?php echo substr($first['Name'],0,6);?>&disposition=570x" alt="<?php echo h($first['Title']);?>" />
	</div>
	<?php
}else{
	?>
	<p>
	<span class="gray">No images present for this <?php echo $section=='gl_properties_units'?'unit':'property';?>.  Click the <?php echo minroles()>ROLE_AGENT?'Tools tab':'Add button';?> above to begin loading images</span>
	</p>
	<?php
}
?>
</div>