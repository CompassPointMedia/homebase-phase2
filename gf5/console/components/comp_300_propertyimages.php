<style type="text/css">
#propertyImages img{
	border:1px solid #555;
	padding:5px;
	margin:10px 0px 0px 10px;
	}
</style>
<div id="propertyImages">
<?php
if($a=q("SELECT
	b.ID, b.Name, b.Title, 
	a.Description, a.Category, a.ObjectName
	FROM 
	gl_ObjectsTree a, relatebase_tree b
	WHERE 
	a.Tree_ID=b.ID AND
	(
	(a.Objects_ID='$Properties_ID' AND a.ObjectName='gl_properties')
	OR
	(a.Objects_ID='$Units_ID' AND a.ObjectName='gl_properties_units')
	)
	ORDER BY IF(a.Category='Main Image', 1,2), IF(a.Category='Floor Plan',2,1)", O_ARRAY)){
	foreach($a as $v){
		//see if image exists
		
		?>
		<div class="fl">
		<img src="/images/reader.php?Tree_ID=<?php echo $v['ID']?>&Key=<?php echo substr($v['Name'],0,6);?>&disposition=540x540" alt="<?php echo h($v['Title']);?>" />
		<?php 
		if(strtolower($v['Category'])==strtolower('Main Image'))echo '<br /><strong>Main Image</strong>';
		if($v['Description'])echo '<br />'.$v['Description'];
		?>
		</div>
		<?php
	}
}
?>
<div class="cb"> </div>
</div>