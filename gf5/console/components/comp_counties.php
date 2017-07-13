<?php

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
	function selectState(n){
		if(n)window.location='report_generic.php?report=counties&state='+n;
	}
	</script><?php
}

ob_start();
$a=q("SELECT Category, SubCategory, COUNT(*) AS Count FROM finan_items WHERE ResourceType IS NOT NULL GROUP BY Category, SubCategory ORDER BY Category, SubCategory", O_ARRAY);

?>
<h1>County List</h1>
State: <select name="state" id="state" onchange="selectState(this.value)">
<option value="">&lt;Select a state..&gt;</option>
<?php
foreach(q("SELECT st_code, st_name FROM aux_states WHERE st_country='United States' ORDER BY st_name", O_COL_ASSOC, $public_cnx) as $n=>$v){
	?><option value="<?php echo $n?>" <?php echo $state==$n?'selected':''?>><?php echo h($v);?></option><?php
}
?>
</select><br />

<p class="gray"><?php echo $description;?></p>
<?php
if($state){
	$a=q("SELECT co_name FROM aux_counties WHERE co_state='$state' ORDER BY co_name", O_COL, $public_cnx);
	?>
	<h3>With word County, Sorted A-Z</h3>
	<textarea cols="65" rows="15" onfocus="this.select();"><?php
	echo implode(' County, ',$a);
	echo ' County';
	?></textarea><br />
	<h3>Without word County, Sorted A-Z</h3>
	<textarea cols="65" rows="15" onfocus="this.select();"><?php
	echo implode(', ',$a);
	?></textarea><br />
	<h3>Google Maps Reference</h3>
	<p>
	<?php
	$i=0;
	foreach($a as $v){
		$i++;
		if($i>1)echo ', ';
		?><a href="https://maps.google.com/maps?q=<?php echo urlencode($v).'+County,+'.urlencode($state);?>&hl=en&t=m&z=9" target="googlemap" title="Click to view this county mapped on google"><?php echo $v.' County';?></a><?php
	}
	?></p><?php
}else{
	?><h3 class="gray">Select a state from the dropdown list first</h3><?php
}
$reportOutput=ob_get_contents();
ob_end_clean();
?>