<?php
/*
2012-04-24 - pulled from main report for thinner strips others can use

todo:
2012-04-24: 	

bugs:
	2012-04-24: gas not working, doesn't stick

*/

$a=q("SELECT
u.ID AS Units_ID, u.*, p.*
FROM finan_clients c, gl_properties p, gl_properties_units u
WHERE c.ID=p.Clients_ID AND p.ID=u.Properties_ID AND p.ResourceType IS NOT NULL ".(minroles()>ROLE_AGENT?" AND p.ID IN(".implode(',',list_properties()).")":'').($Properties_ID ? " AND p.ID=$Properties_ID":'')."
ORDER BY p.Type, p.PropertyName, p.ID, u.Bedrooms, u.Bathrooms", O_ARRAY);
function newproperty($n){
	global $ID, $newproperty;
	if($newproperty[$ID][$n]){
		return false;
	}else{
		$newproperty[$ID][$n]=1;
		return true;
	}
}
?><script language="javascript" type="text/javascript">
function dC(n){
	g('c_'+n).value='1';
	detectChange=1;
}
function btns(n){
	if(n=='close' && detectChange && !confirm('Are you sure you want to close this window? changes you have made will be lost.'))return false;
	if(n=='submit' && !detectChange){
		alert('You have not made changes to any fields; make changes before submitting');
		return false;
	}
}
</script>
<style type="text/css">
.alt td{
	background-color:dimgray;
	}
#allunits{
	margin:5px 20px;
	}
#allunits input[type="text"], #allunits select{
	padding:0px;
	font-size:12px;
	}
#allunits input[type="text"], #allunits select{
	padding:1px;
	font-size:12px;
	border-color:darkblue;
	}
	
#allunits td{
	padding:1px 3px 1px 3px;
	}
.br{ border-right:1px solid #666; }
.bl{ border-left:1px solid #666; }
.bb{ border-bottom:1px solid #666; }
</style>
<table id="allunits" class="yat">
<thead>
<tr>
	<th colspan="6" class="tc br bb bl">General Info</th>
	<th colspan="5" class="tc br bb bl">Pricing</th>
	<th colspan="9" class="tc br bb bl">Utilities</th>
	<th colspan="3" class="tc bb">Pets</th>
</tr>
<tr>
	<th class="bl">&nbsp;</th>
	<th>Edit Notes</th>
	<th>Property</th>
	<th>Bed/Bath</th>
	<th>Sq.Ft.</th>
	<th>Qty.</th>
	
	<!-- main expenses -->
	<th class="bl">Rent</th>
	<th>Deposit</th>
	<th>Admin Fee</th>
	<th>App Fee</th>
	<th>Cosign Fee</th>


	<!-- utilities -->
	<th class="tc bl">Electric<br />pd.</th> <!-- yes/no -->
	<th>Gas</th>
	<th>pd.</th>
	<th>Water<br />pd.</th>
	<th>Trash<br />pd.</th>
	<th>Inet</th>
	<th>Inet<br />pd</th>
	<th>Cable</th>
	<th>pd.</th>


	<th class="bl">OK?</th>
	<th>Deposit</th>
	<th class="br">Extra</th>
</tr>
</thead>
<tbody>
<?php
if($a){
	$i=0;
	foreach($a as $n=>$v){
		extract($v);
		$i++;
		if($i==1 || $buffer!==strtolower($Type)){
			if(false && $i>1){
				//close previous
				?><tr>
				<th colspan="6" class="br bb bl">&nbsp;</th>
				<th colspan="5" class="br bb bl">&nbsp;</th>
				<th colspan="9" class="br bb bl">&nbsp;</th>
				<th colspan="3">&nbsp;</th>
				</tr><?php
			}
			$j=0;
			$buffer=strtolower($Type);
			?><tr>
			<th colspan="6" class="br bb bl"><h1><?php echo $Type;?></h1></th>
			<th colspan="5" class="br bb bl">&nbsp;</th>
			<th colspan="9" class="br bb bl">&nbsp;</th>
			<th colspan="3" class="br bb">&nbsp;</th>
			</tr><?php
		}
		$j++;
		?><tr id="r_<?php echo $Units_ID;?>" class="<?php echo !fmod($j,2)?'alt':''?>">
			<td class="bl">
			<input type="hidden" name="data[ID][<?php echo $Units_ID;?>]" id="c_<?php echo $Units_ID;?>" value="0" />
			<a href="properties3.php?Units_ID=<?php echo $Units_ID;?>" title="view and edit this property/unit" onClick="return ow(this.href,'l1_properties','800,700');"><img src="/images/i/edit2.gif" /></a></td>
			<td><input type="text" name="data[Edit_Notes][<?php echo $Units_ID?>]" size="15" maxlength="255" /></td>
			<td nowrap="nowrap"><?php echo $PropertyName;?></td>
			<td class="tc"><strong><?php echo $Bedrooms . '/'. $Bathrooms;?></strong></td>
			<td class="tr"><strong><?php echo $SquareFeet;?></strong></td>
			<td><input type="text" name="data[Quantity][<?php echo $Units_ID?>]" value="<?php echo $Quantity;?>" onChange="dC(<?php echo $Units_ID;?>)" size="2" class="tar" /></td>
			
			<!-- main expenses -->
			<td class="bl"><input type="text" name="data[Rent][<?php echo $Units_ID?>]" value="<?php echo number_format($Rent,2);?>" onChange="dC(<?php echo $Units_ID;?>)" size="4" class="tar" /></td>
			<td><input type="text" name="data[Deposit][<?php echo $Units_ID?>]" value="<?php echo number_format($Deposit,2);?>" onChange="dC(<?php echo $Units_ID;?>)" size="4" class="tar" /></td>
			<td><?php if(newproperty(1)){?>
			<input type="text" name="data[AdminFee][<?php echo $Units_ID?>]" value="<?php echo number_format($AdminFee,2);?>" onChange="dC(<?php echo $Units_ID;?>)" size="4" class="tar" />
			<?php }?></td>
			<td><?php if(newproperty(2)){?>
			<input type="text" name="data[ApplicationFee][<?php echo $Units_ID?>]" value="<?php echo number_format($ApplicationFee,2);?>" onChange="dC(<?php echo $Units_ID;?>)" size="4" class="tar" />
			<?php }?></td>
			<td><?php if(newproperty(3)){?>
			  <input type="text" name="data[CosignerFee][<?php echo $Units_ID?>]" value="<?php echo number_format($CosignerFee,2);?>" onChange="dC(<?php echo $Units_ID;?>)" size="4" class="tar" />
			  <?php }?></td>
			<!-- utilities -->
			<td class="tc bl"><?php if(newproperty(4)){?>
			  <input name="data[ElectricPaid][<?php echo $Units_ID?>]" type="checkbox" onChange="dC(<?php echo $Units_ID;?>)" value="1" <?php echo $ElectricPaid ? 'checked':''?> />
			  <?php }?></td><td>
			    <?php if(newproperty(104)){?>
			    <select name="data[Gas][<?php echo $Units_ID?>]" onChange="dC(<?php echo $Units_ID?>);">
			<option value="">None</option>
			<option value="W" <?php echo $Gas=='W'?'selected':'';?>>W</option>
			<option value="H" <?php echo $Gas=='H'?'selected':'';?>>H</option>
			<option value="W,H" <?php echo $Gas=='W,H'?'selected':'';?>>W,H</option>
			<option value="W,H,S" <?php echo $Gas=='W,H,S'?'selected':'';?>>W,H,S</option>
			</select>
			    <?php }?></td>
			  <td class="tc"><?php if(newproperty(5)){?>
			    <input type="checkbox" name="data[GasPaid][<?php echo $Units_ID?>]" value="1" <?php echo $GasPaid ? 'checked':''?> onChange="dC(<?php echo $Units_ID;?>)" />
			    <?php }?></td>
			  <td class="tc"><?php if(newproperty(6)){?>
			    <input type="checkbox" name="data[WaterPaid][<?php echo $Units_ID?>]" value="1" <?php echo $WaterPaid ? 'checked':''?> onChange="dC(<?php echo $Units_ID;?>)" />
			    <?php }?></td>
			  <td class="tc"><?php if(newproperty(7)){?>
			    <input type="checkbox" name="data[TrashPaid][<?php echo $Units_ID?>]" value="1" <?php echo $TrashPaid ? 'checked':''?> onChange="dC(<?php echo $Units_ID;?>)" />
			    <?php }?></td>
			  <td><?php if(newproperty(8)){?>
			    <input type="text" name="data[InternetProvider][<?php echo $Units_ID?>]" value="<?php echo $InternetProvider;?>" onChange="dC(<?php echo $Units_ID;?>)" size="4" class="tar" />
			    <?php }?></td>
			  <td class="tc"><?php if(newproperty(9)){?>
			    <input type="checkbox" name="data[InternetPaid][<?php echo $Units_ID?>]" value="1" <?php echo $InternetPaid ? 'checked':''?> onChange="dC(<?php echo $Units_ID;?>)" />
			    <?php }?></td>
			  <td><?php if(newproperty(10)){?>
			    <select name="data[CableProvider][<?php echo $Units_ID;?>]" onChange="dC(<?php echo $Units_ID?>);">
			      <option value="">None</option>
			      <?php
			foreach($cableProviders as $v){
				?><option value="<?php echo $v;?>" <?php echo strtolower($CableProvider)==strtolower($v)?'selected':''?>><?php echo $v;?></option><?php
			}
			?>
		        </select>
			    <?php }?></td>
			  <td class="tc"><?php if(newproperty(11)){?>
			    <input type="checkbox" name="data[Cable][<?php echo $Units_ID?>]" value="1" <?php echo $Cable ? 'checked':''?> onChange="dC(<?php echo $Units_ID;?>)" />
			    <?php }?></td>
			  <td class="tc bl"><?php if(newproperty(12)){?>
			    <input type="checkbox" name="data[PetsAllowed][<?php echo $Units_ID?>]" value="1" <?php echo $PetsAllowed ? 'checked':''?> onChange="dC(<?php echo $Units_ID;?>)" />
			    <?php }?></td>
			  <td><?php if(newproperty(13)){?>
			    <input type="text" name="data[PetDeposit][<?php echo $Units_ID?>]" value="<?php echo number_format($PetDeposit,2);?>" onChange="dC(<?php echo $Units_ID;?>)" size="4" class="tar" />
			    <?php }?></td>
			  <td><?php if(newproperty(14)){?>
			    <input type="text" name="data[PetExtra][<?php echo $Units_ID?>]" value="<?php echo number_format($PetExtra,2);?>" onChange="dC(<?php echo $Units_ID;?>)" size="4" class="tar" />
			    <?php }?></td>
			</tr><?php
	}
}else{
	?><tr>
	<td colspan="100%"><em class="gray">No records found for that criteria</em></td>
	</tr><?php
}
?>
</tbody>
</table>
