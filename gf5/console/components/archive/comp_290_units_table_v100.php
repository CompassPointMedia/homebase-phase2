<?php

/*
2010-12-23: this was copied from comp_41_homes_household_members_v100.php


-->THIS from Giocos aCare - "they can access this foster home if the function lists the ID, OR if they are the creator of the record and it's unfulfilled (quasi-resource)"
if(
	!in_array($ID, list_fosterhomes('keys')) && 
	!q("SELECT ID FROM gf_fosterhomes WHERE ID='$ID' AND ResourceType IS NULL AND Creator='".sun()."'", O_VALUE
)){
	mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
	error_alert('You do not have access to add or update caregivers in this home');
}
if($passedResourceToken)$ResourceToken=$passedResourceToken;
*/


$unitsUpdateAuthorized=true;
$unitsDeleteAuthorized=true;
$gfObjectPre='UNIT';
$componentMode='manageUnitInventory';

if($mode==$componentMode){
	if($submode=='deleteObject'){
		//delete the child record itself
		if(q("SELECT COUNT(*) FROM _v_leases_master WHERE Units_ID='".$GLOBALS[$gfObjectPre.'_ID']."'", O_VALUE))error_alert('You cannot delete these units; they have been used in leases.  Click the lease report for these units to view');
		q("DELETE FROM gl_properties_units WHERE Properties_ID='$ID' AND ID='".$GLOBALS[$gfObjectPre.'_ID']."'");
	}else if($submode=='addObject'){
		//meaning, add existing - not used for this
	}else{
		//---- error checking here ----
		if(!preg_match('/^[1-9]+(\.[0-9]+)*$/',$GLOBALS[$gfObjectPre.'Bedrooms']))
			error_alert('Specify correct number of bedrooms');
		if(!preg_match('/^[1-9]+(\.[0-9]+)*$/',$GLOBALS[$gfObjectPre.'Bathrooms']))
			error_alert('Specify correct number of bathrooms');
		if(!preg_match('/^[1-9]+[0-9]+(\.[0-9]+)*$/',$GLOBALS[$gfObjectPre.'Rent']))
			error_alert('Specify correct amount for rent');
		if(!preg_match('/^[1-9]+[0-9]*$/',$GLOBALS[$gfObjectPre.'Quantity']))
			error_alert('Specify number of units of this type available');
	}

	if($submode=='insertObject'){
		$NewUnits_ID=q("INSERT INTO gl_properties_units SET 
		Properties_ID='$ID',
		Rent='".$GLOBALS[$gfObjectPre.'Rent']."',
		Deposit='".$GLOBALS[$gfObjectPre.'Deposit']."',
		Quantity='".$GLOBALS[$gfObjectPre.'Quantity']."',
		Bedrooms='".$GLOBALS[$gfObjectPre.'Bedrooms']."',
		Bathrooms='".$GLOBALS[$gfObjectPre.'Bathrooms']."',
		SquareFeet='".$GLOBALS[$gfObjectPre.'SquareFeet']."',
		Description='".$GLOBALS[$gfObjectPre.'Description']."',
		CreateDate=NOW(),
		Creator='".sun()."'", O_INSERTID);
		prn($qr);
	}else if($submode=='updateObject'){
		$NewUnits_ID=q("UPDATE gl_properties_units SET 
		Rent='".$GLOBALS[$gfObjectPre.'Rent']."',
		Deposit='".$GLOBALS[$gfObjectPre.'Deposit']."',
		Quantity='".$GLOBALS[$gfObjectPre.'Quantity']."',
		Bedrooms='".$GLOBALS[$gfObjectPre.'Bedrooms']."',
		Bathrooms='".$GLOBALS[$gfObjectPre.'Bathrooms']."',
		SquareFeet='".$GLOBALS[$gfObjectPre.'SquareFeet']."',
		Description='".$GLOBALS[$gfObjectPre.'Description']."',
		EditDate=NOW(),
		Editor='".sun()."' WHERE ID='".$GLOBALS[$gfObjectPre.'_ID']."'");
		prn($qr);
	}
}

$unitinfo=q("SELECT
a.ID,
b.Clients_ID,
a.Rent,
a.Deposit,
a.Bedrooms,
a.Bathrooms,
a.SquareFeet,
a.Description,
a.Quantity
FROM gl_properties_units a, gl_properties b WHERE a.Properties_ID=b.ID AND a.Properties_ID='".($Properties_ID ? $Properties_ID : $ID)."' ORDER BY a.SquareFeet", O_ARRAY);
if(!$refreshComponentOnly){
	?><style type="text/css">
	.addedRecord td{
		background-color:papayawhip;
		}
	#UnitInventory{
		background-color:linen;
		border:1px solid #777;
		padding:7px;
		}
	#UnitInventory a{
		color:#3b5998;
		}
	</style>
	<script language="javascript" type="text/javascript">
	function editObject<?php echo $gfObjectPre?>(o){
		g('<?php echo $gfObjectPre?>Quantity').value=o.getAttribute('Quantity');
		g('<?php echo $gfObjectPre?>Rent').value=o.getAttribute('Rent');
		g('<?php echo $gfObjectPre?>Deposit').value=o.getAttribute('Deposit');
		g('<?php echo $gfObjectPre?>Bedrooms').value=o.getAttribute('Bedrooms');
		g('<?php echo $gfObjectPre?>Bathrooms').value=o.getAttribute('Bathrooms');
		g('<?php echo $gfObjectPre?>Description').value=o.getAttribute('Description');
		g('<?php echo $gfObjectPre?>SquareFeet').value=o.getAttribute('SquareFeet');

		g('<?php echo $gfObjectPre?>_ID').value=o.getAttribute('id').replace('dependentobject_','');
		g('<?php echo $gfObjectPre?>AddUpdate').value='Update';
		g('<?php echo $gfObjectPre?>Bedrooms').focus();
		g('<?php echo $gfObjectPre?>Bedrooms').select();
		g('<?php echo $gfObjectPre?>More').style.display='inline';
	}
	function insertObject<?php echo $gfObjectPre?>(n){
		//usurp mode value, submit and reset
		var buffer=g('mode').value;
		g('mode').value='<?php echo $componentMode?>';
		g('submode').value=(n=='Add' ? 'insertObject' : 'updateObject');
		g('form1').submit();
		
		g('mode').value=buffer;
		g('submode').value='';
	}
	function deleteObject<?php echo $gfObjectPre?>(ID, <?php echo $gfObjectPre?>_ID,ResourceToken){
		if(!confirm('This will permanently remove this record.  Continue?'))return;
		window.open('resources/bais_01_exe.php?mode=<?php echo $componentMode?>&submode=deleteObject&ID='+ID+'&<?php echo $gfObjectPre?>_ID='+<?php echo $gfObjectPre?>_ID+'&passedResourceToken='+ResourceToken, 'w2');
	}
	function clearObjectForm<?php echo $gfObjectPre?>(){
		alert('Not developed');
	}
	</script><?php
}
?>
<div id="UnitInventory">
<table class="subTable1">
<thead>
	<tr>
		<?php if($unitsUpdateAuthorized){ ?>
		<th>&nbsp;&nbsp;</th>
		<?php } ?>
		<th>Qty.</th>
		<th>Bed/<br />
	    Bth</th>
		<th>Rent</th>
		<th>Deposit</th>
		<th>S.F.</th>
		<?php if($unitinfo[1]['Clients_ID']){ ?>
		<th>Lease</th>
		<?php } ?>
	</tr>
</thead>
<?php if($unitsUpdateAuthorized){ ?>
<tfoot>
<tr>
	<td colspan="102">


	  Bedrooms:
	  <input name="<?php echo $gfObjectPre?>Bedrooms" type="text" id="<?php echo $gfObjectPre?>Bedrooms" value="<?php echo preg_replace('/\.0+$/','',$u['Bedrooms'])?>" size="5" onchange="dChge(this);" />
	  <br />
	  Bathrooms:
	  <input name="<?php echo $gfObjectPre?>Bathrooms" type="text" id="<?php echo $gfObjectPre?>Bathrooms" value="<?php echo preg_replace('/\.0+$/','',$u['Bathrooms'])?>" size="5" onchange="dChge(this);" />
	  <br />
	  Square feet:
	  <input name="<?php echo $gfObjectPre?>SquareFeet" type="text" id="<?php echo $gfObjectPre?>SquareFeet" value="<?php echo $u['SquareFeet']?>" size="5" onchange="dChge(this);" />
	  <br />
	  Rent:
	  <input name="<?php echo $gfObjectPre?>Rent" type="text" id="<?php echo $gfObjectPre?>Rent" value="<?php echo number_format($u['Rent'],2)?>" size="5" onchange="dChge(this);" />
	  <br />
	  Dep.:
	  <input name="<?php echo $gfObjectPre?>Deposit" type="text" id="<?php echo $gfObjectPre?>Deposit" value="<?php echo number_format($u['Deposit'],2)?>" size="5" onchange="dChge(this);" />
	  <br />
	  Quantity of this type: 
	  <input name="<?php echo $gfObjectPre?>Quantity" type="text" id="<?php echo $gfObjectPre?>Quantity" value="<?php echo $u['Quantity']?>" size="5" onchange="dChge(this);" />
	  <br />
	  Notes: 
	  <input name="<?php echo $gfObjectPre?>Description" type="text" id="<?php echo $gfObjectPre?>Description" onchange="dChge(this);" value="<?php echo $u['Description']?>" size="50" />
	  <br /> 
	  <br />
	  <input name="<?php echo $gfObjectPre?>_ID" type="hidden" id="<?php echo $gfObjectPre?>_ID" onchange="dChge(this);" />
	  <br />
	  
	  
	  <input name="<?php echo $gfObjectPre?>AddUpdate" type="button" id="<?php echo $gfObjectPre?>AddUpdate" value="Add" onclick="javascript:insertObject<?php echo $gfObjectPre?>(this.value);" />
	  <span id="<?php echo $gfObjectPre?>More" style="display:<?php echo 'none';?>;">
	  <input type="button" name="Button" value="More details.." onclick="return ow('units.php?Units_ID='+g('<?php echo $gfObjectPre;?>_ID').value,'l1_units','700,700');" />
	  </span>	  </td>
</tr>
</tfoot>
<?php } ?>
<tbody>
<?php
if($unitinfo){
	foreach($unitinfo as $u){
		?><tr>
			<?php if($unitsUpdateAuthorized){ ?>
			<td>
			[<a href="javascript:editObject<?php echo $gfObjectPre?>(g('dependentobject_<?php echo $u['ID']?>'));">edit</a>]
			<?php if($unitsDeleteAuthorized){ ?>
			&nbsp;&nbsp;
			[<a href="javascript:deleteObject<?php echo $gfObjectPre?>(<?php echo $ID?>,<?php echo $u['ID']?>,'<?php echo $ResourceToken?>');">remove</a>]
			<?php } ?>			</td>
			<?php } ?>
	
			<td id="dependentobject_<?php echo $u['ID']?>" nowrap="nowrap" 
			Quantity="<?php echo h($u['Quantity'])?>"
			Bedrooms="<?php echo h($u['Bedrooms'])?>"
			Bathrooms="<?php echo h($u['Bathrooms'])?>"
			Rent="<?php echo h($u['Rent'])?>"
			Deposit="<?php echo h($u['Deposit'])?>"
			Description="<?php echo h($u['Description'])?>"
			SquareFeet="<?php echo number_format($u['SquareFeet'],2)?>"
			
			class="<?php if($u['ID']==$addedRecordID)echo 'addedRecord';?>"><?php echo $u['Quantity']?></td>
			<td><?php echo $u['Bedrooms']?>-<?php echo $u['Bathrooms']?></td>
			<td class="tar"><?php echo $u['Rent']?></td>
			<td class="tar"><?php echo number_format($u['Deposit'],2)?></td>
			<td class="tac"><?php echo $u['SquareFeet']?></td>
			<?php if($unitinfo[1]['Clients_ID']){ ?>
			<td>[<a href="leases.php?Units_ID=<?php echo $u['ID']?>" title="Lease this unit" onclick="<?php if($u['unitIsFull']){ ?>if(!confirm('These units appear to all be leased out.  Are you sure?'))return false;<?php } ?> return ow(this.href,'l1_leases','700,700');">lease</a>]</td>
			<?php } ?>
		</tr><?php
	}
}else{
	?><tr>
		<td colspan="98"><em>No units listed.  Fill in the fields below and click Add to add apartment units</em></td>
	</tr><?php
}
?>
</tbody>
</table>
</div>