<?php
/*
Created 2010-11-20 by Samuel
this was copied from component: comp_41_homes_household_members_v100.php, created 2010-03-23 by Samuel - very solid component acting in a quasi-resource environment

*/
if($mode=='rootManageRoles'){
	/*
	2010-11-20 - these are NOT implemented but need to be
	* cannot delete 1 and 2, and cannot rename them
	* can delete anything else, but not if it has processes assigned
	
	*/
	
	if($submode=='deleteRole'){
		//delete the role
		q("DELETE FROM bais_roles WHERE ro_id='$ro_id'");
	}else{
		if(!$GLOBALS['ro_name'])error_alert('Enter the role title');
		if(!$GLOBALS['ro_shortname'])error_alert('Enter the role short name');
		if(!$GLOBALS['ro_description'])error_alert('Enter a description for this role');
		if(!$GLOBALS['ro_rank'])error_alert('Enter a ranking for this role');
	}

	if($submode=='insertRole'){
		$addedRecordID=q("INSERT INTO bais_roles SET
		ro_name='$ro_name',
		ro_shortname='$ro_shortname',
		ro_description='$ro_description',
		ro_rank='$ro_rank',
		ro_locked='1',
		ro_createdate=NOW(),
		ro_creator='".$_SESSION['admin']['roles']."'", O_INSERTID);
		prn($qr);
	}else if($submode=='updateRole'){
		q("UPDATE bais_roles SET
		ro_name='$ro_name',
		ro_shortname='$ro_shortname',
		ro_description='$ro_description',
		ro_rank='$ro_rank',
		ro_locked='1',
		ro_editdate=NOW(),
		ro_editor='".$_SESSION['admin']['roles']."'
		WHERE ro_id='$ro_id'", O_INSERTID);
		prn($qr);
	}
}
if(!$refreshComponentOnly){
	?><style type="text/css">
	.addedRecord td{
		background-color:cornsilk;
		}
	#roles{
		background-color:linen;
		border:1px solid #777;
		padding:7px;
		}
	#roles a{
		color:saddlebrown;
		}
	</style>
	<script language="javascript" type="text/javascript">
	function editRole(o){
		g('ro_id').value=o.getAttribute('ro_id');
		g('ro_name').value=o.getAttribute('ro_name');
		g('ro_shortname').value=o.getAttribute('ro_shortname');
		g('ro_description').value=o.getAttribute('ro_description');
		g('ro_rank').value=o.getAttribute('ro_rank');
		g('AddUpdate').value='Update';
		g('ro_name').focus();
		g('ro_name').select();
	}
	function insertRole(n){
		//usurp mode value, submit and reset
		g('submode').value=(n=='Add' ? 'insertRole' : 'updateRole');
		g('form1').submit();
		g('submode').value='';
	}
	function deleteRole(ro_id){
		if(!confirm('This will permanently remove this role and associated privileges.  Continue?'))return;
		window.open('/gf5/console/resources/bais_01_exe.php?mode=rootManageRoles&submode=deleteRole&ro_id='+ro_id, 'w2');
	}
	function clearObjectForm(){
		g('ro_id').value='';
		g('ro_name').value='';
		g('ro_shortname').value='';
		g('ro_description').value='';
		g('ro_rank').value='';
		g('AddUpdate').value='Add';
		g('FirstName').focus();
	}
	</script><?php
}
$roles=q("SELECT * FROM bais_roles ORDER BY ro_rank", O_ARRAY);
?>
<div id="roles">
<table class="subTable1">
<thead>
	<tr>
		<th>&nbsp;&nbsp;</th>
		<th>Role Title </th>
		<th>Role Short Name </th>
		<th>Description</th>
		<th>Index</th>
	</tr>
</thead>
<tfoot>
<tr>
	<td colspan="101"><br>
	  <a href="javascript:clearObjectForm();"><strong>Add new role</strong></a> <br>
	  Role Title:
	  <input name="ro_name" type="text" id="ro_name" onchange="mgeChge(this);" size="20" maxlength="100" /> 
	  <br>
	  Role Short Name:
	  <input name="ro_shortname" type="text" id="ro_shortname" onchange="mgeChge(this);" size="15" maxlength="17" />
	  <br>
	  Description:<br>
  <textarea name="ro_description" cols="40" rows="3" id="ro_description" onChange="mgeChge(this);"></textarea>
		<br>
		Index (rank): 
		<select name="ro_rank" id="ro_rank" onchange="mgeChge(this);">
			<option value="">&lt;select&gt;</option>
			<?php for($i=1;$i<=45;$i++){ ?><option value="<?php echo $i?>"><?php echo $i?></option><?php } ?>
		</select>
		<br>
		<em>(lower numbers equal higher authority)</em>
	  <p>
	    <input name="ro_id" type="hidden" id="ro_id" />
	    <input name="submode" type="hidden" id="submode">
	    <input name="mode" type="hidden" id="mode" value="rootManageRoles">
	    <input name="AddUpdate" type="button" id="AddUpdate" value="Add" onclick="javascript:insertRole(this.value);" />		
      </p>	  </td></tr>
</tfoot>
<tbody>
<?php
if($roles)
foreach($roles as $v){
	?><tr>
		<td nowrap>
		[<a href="javascript:editRole(g('role_<?php echo $v['ro_id']?>'));">edit</a>]
		&nbsp;&nbsp;
		[<a href="javascript:deleteRole(<?php echo $v['ro_id']?>);">remove</a>]		</td>
		<td id="role_<?php echo $v['ro_id']?>" nowrap="nowrap" 
		ro_id="<?php echo h($v['ro_id']);?>"
		ro_name="<?php echo h($v['ro_name']);?>"
		ro_shortname="<?php echo h($v['ro_shortname']);?>"
		ro_description="<?php echo h($v['ro_description']);?>"
		ro_rank="<?php echo h($v['ro_rank']);?>"><?php echo $v['ro_name'];?></td>
		<td><?php echo $v['ro_shortname'];?></td>
		<td><?php echo $v['ro_description'];?></td>
		<td><?php echo $v['ro_rank'];?></td>
	</tr><?php
}
?>
</tbody>
</table>
</div>