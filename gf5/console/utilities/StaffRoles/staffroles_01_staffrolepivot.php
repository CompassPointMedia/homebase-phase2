<?php
$sql="SELECT 
*
FROM bais_universal, bais_staff 
WHERE 
st_active=1 AND 
un_username = st_unusername
ORDER BY un_lastname, un_firstname";
$result=q($sql);
$buffer='';
/****
Table layout library item
This is my first table layout library item, it covers a pivot table accross one direction and I'd like to make it modular.
note that the id's for the child rows need to include the id for the parent object as well

****/
//get number of roles present
foreach(q("SELECT REPLACE(ro_id,'.0','') AS ro_id, ro_shortname, ro_description FROM bais_roles ORDER BY ro_rank ASC", O_ARRAY) as $r){
	$roleCount++;
	$roleArray[$r['ro_id']]=$r['ro_shortname'];
	$roleTitles[$r['ro_id']]=$r['ro_description'];
}
?>
<div id="roleList" class="overflowInset110" style="height:350px;width:95%;overflow:auto;background-color:white;">
<table cellpadding="2" cellspacing="0" class="data1" width="100%">
<colgroup><col style="border-right:1px solid #000;"><?php
foreach($roleArray as $n=>$v){
	$jsArray1.='roleCols['.$n.']="'.$v.'";';
	?><col align="center" id="col_<?php echo $n?>"><?php
}
?></colgroup>
<script language="javascript" type="">
var roleCols=new Array(); <?php echo $jsArray1;?>
</script>
<thead>
	<th>Name</th><?php
//we need to get a list of roles the user can assign to someone.  Rule is that they can assign their level access or greater

//get user's highest rank (lowest rank number)
$userRank=q("SELECT MIN(ro_rank) AS Rank FROM bais_roles, bais_StaffRoles WHERE ro_id=sr_roid AND sr_stusername='".$_SESSION[admin][userName]."'", O_VALUE);

//run through roles array
if(count($roleArray)){
	$rank=1000000000;
	foreach($roleArray as $n=>$v){
		//think this is redundant
		$sql="SELECT ro_rank from bais_roles WHERE ro_id=$n";
		$rankresult=q($sql);
		$r=mysqli_fetch_array($rankresult);
		if($r[ro_rank]<$rank){
			$rank=$r[ro_rank];
		}
		?>
		<th id="ro_<?php echo $n?>" title="<?php echo $v.' -- '.$roleTitles[$n]?>" onclick="h(this,'role',1,0,event);colHlt(this);" onContextMenu="h(this,'role',1,1);colHlt(this);" onDblClick="h(this,'role',1,0);role('open');"><?php echo $v?></th>
		<?php
	}
}
?></thead>
<?php
while($r=mysqli_fetch_array($result)){
	$i++;
	extract($r);
	?>
	<tr id="u_<?php echo $st_unusername?>" onclick="h(this,'usr',1,0,event);" onContextMenu="h(this,'usr',1,1); " onDblClick="h(this,'usr',1,0);admin('open');"><td nowrap><?php echo htmlentities($un_firstname.' '.$un_lastname.' ('.$st_unusername.')')?></td>
<?php
if(count($roleArray)){
	foreach($roleArray as $n=>$v){
		//set initial checked value
		$sql="SELECT a.*, REPLACE(sr_roid,'.0','') AS sr_roid FROM bais_StaffRoles a WHERE sr_stusername='".$st_unusername."' AND sr_roid='".$n."'";
		$res2=q($sql);
		if(mysqli_num_rows($res2)==1){
			$checked='checked';
		}else{
			$checked='';
		}
		//get role rank
		$roleRank=q("SELECT ro_rank FROM bais_roles WHERE ro_id=$n", O_VALUE);
		?><td><?php
		?><input name="st_<?php echo $st_unusername?>_ro_<?php echo $n?>" type="checkbox" onclick="hl_cxl=1;assign_role(this,'<?php echo $st_unusername?>',<?php echo $n?>)" value="1" class="cbox" <?php echo $checked?><?php 
		//handle disablement; admin can assign their own rank and no greater
		if(!strlen($userRank) || ($roleRank<$userRank)){
			echo ' disabled';
		}
		?>></td><?php
	}
}
?></tr>
	<?php
}
?>
</table></div>
<?php
if(false && !$refreshComponentOnly){ ?>
	<div id="roleMenu" class="menuskin1" onMouseOver="highlightie5(event)" onMouseOut="lowlightie5(event)" onclick="executemenuie5(event);" precalculated="role_perms(this,'p001')">
		<div class="menuitems" command="role('open')" status="Edit this role" style="font-weight:900">Open</div>
		<div class="menuitems" command="role('remove')" status="Delete role">Delete</div>
		<hr class="mhr" />
		<div class="menuitems" command="colHide(colIdx,0);" status="Hide this column (role) from view">Hide Role</div>
		<div class="menuitems" command="colHide(colIdx,1);" status="Restore View of All Hidden Roles">Show Hidden Roles</div>
		<hr class="mhr" />
		<div class="menuitems" command="role('new')" status="Add new role">Add Role..</div>
	</div>
	<script>
	//declare the ogrp.handle.sort value even if blank
	ogrp['roleCols']=new Array();
	ogrp['roleCols']['sort']='';
	ogrp['roleCols']['rowId']='';
	ogrp['roleCols']['highlightGroup']='role';
	hl_grp['role']=new Array(); AssignMenu('^ro_', 'roleMenu');
	</script>
	<div id="userMenu" class="menuskin1" onMouseOver="highlightie5(event)" onMouseOut="lowlightie5(event)" onclick="executemenuie5(event);" precalculated="admin_perms(this,'p001')"> 
		<div class="menuitems" command="admin('open');" status="Edit User" style="font-weight:900">Edit</div>
		<div class="menuitems" command="admin('grantall');" status="Grant all listed roles to user">Grant All Roles</div>
		<div class="menuitems" command="admin('removeall');" status="Revoke all listed roles from user's privileges">Revoke</div>
		<div class="menuitems" command="admin('report');" status="Summary report on user activity">Summary Report</div>
		<div class="menuitems" command="admin('contact');" status="Contact this user">Contact</div>
		<hr class="mhr" />
		<div class="menuitems" command="admin('delete');" status="Remove User">Remove</div>
		<hr class="mhr" />
		<div class="menuitems" command="admin('new');" status="Add New User">New User</div>
	</div>
	<script>
	//declare the ogrp.handle.sort value even if blank
	ogrp['userRows']=new Array();
	ogrp['userRows']['sort']='';
	ogrp['userRows']['rowId']='';
	ogrp['userRows']['highlightGroup']='usr';
	hl_grp['usr']=new Array(); AssignMenu('^u_', 'userMenu');
	</script><?php
}
?>