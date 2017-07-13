<?php
/*
2012-04-21: CAREFUL!!!! You are in global scope
*/
$src=($nextRecord);
$r=q("SELECT * FROM _v_properties_master_list WHERE Properties_ID=".$src['Properties_ID'], O_ROW);
if(false){
	?>
	<link rel="stylesheet" type="text/css" href="/site-local/undohtml2.css" />
	<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
	<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
	<?php
}
if(!$clientpropertyCSSDeclared){
	$clientpropertyCSSDeclared=true;
	?><style type="text/css">
	.cP h1{
		margin-bottom:0px;
		}
	</style><?php
}
?>

<div id="p_<?php echo $Properties_ID?>" class="cP">
  <h1><?php echo $r['PropertyName'];?></h1>
  <div class="fr">
  Property type: <?php echo $r['Type'];?>
  </div>
  <div style="width:450px;">
  <div class="fr">
  [<a onclick="return ow(this.href,'l1_properties','700,700');" href="client_properties.php?Units_ID=<?php echo $r['ID'];?>">edit this information</a>]  </div>
  <p><?php echo $r['PropertyAddress'];?><br>
    <?php echo $r['PropertyCity'].', '.$r['PropertyState']. '  ' . $r['PropertyZip'];?><br>
    <?php echo $r['Phone'] . ($r['Phone2']?'/'.$r['Phone2']:'');?> (p)<br>
    <?php echo $r['Fax'] ?$r['Fax'].' (f)<br />':'';?>
  </p>
</p>
<?php
$Properties_ID=$r['Properties_ID'];
require($COMPONENT_ROOT.'/comp_301_propertyimages.php');?>
</div>
