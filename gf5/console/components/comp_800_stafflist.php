<?php


/*

these were brought in from list_members to achieve the following:
* integrate the filter object, BUT use child region (Regions_ID) instead of status (Statuses_ID)
* handle direct export and sunset the previous menu option to export children
* standardize variables such as $dataset - even though in Great Locations they may not be used like the rbrfm components

*/
$dataset='Staff';							//not used exc. for filter gadget
$datasetComponent='listStaffMaster'; 			//not used exc. for filter gadget
$datasetTable='_v_staff_master_information'; 		//not used
$datasetTableIsView=true; 					//not used
$datasetActiveUsage=true; 					//not used
//regions of these children - used by filtergadget - note "status" cognate is really "regions" here
$useStatusFilterOptions=false;
$statusWord='Staff';
/*
$statusFilterIDField='oa_unusername';
$statusFilterNameField='oa_org2';
$statusFilterTable='bais_orgaliases';
$statusFilterQueryWhere='1';
$statusFilterQueryOrder='ORDER BY oa_org2';
$statusFilterDefaultShown=100; 				//e.g. all regions
$filterGadgetMode='refreshComponent'; 		//first used in this comp_50; all processing done in filtergadget
$filterGadgetUserName=sun(); //first used in this comp_50
*/

$positions=array(
	'1.0'=>'DB Administrator',
	'2.0'=>$wordShortFoundationDirector,
	'2.5'=>$wordShortRegionalDirector,
	'3.0'=>$wordShortProgramDirector,
	'10.0'=>$wordShortCaseManager
);

$hideObjectInactiveControl=false;

if($sort){
	q("REPLACE INTO bais_settings SET UserName='".sun()."', vargroup='staff',varnode='defaultStaffSort',varkey='',varvalue='$sort'");
	q("REPLACE INTO bais_settings SET UserName='".sun()."', vargroup='staff',varnode='defaultStaffSortDirection',varkey='',varvalue='".($dir?$dir:1)."'");
	$_SESSION['userSettings']['defaultStaffSort']=$sort;
	$_SESSION['userSettings']['defaultStaffSortDirection']=($dir?$dir:1);
}else{
	$sort=$_SESSION['userSettings']['defaultStaffSort'];
	$dir=$_SESSION['userSettings']['defaultStaffSortDirection'];
}
if(isset($hideInactive)){
	//update settings and environment
	q("REPLACE INTO bais_settings SET UserName='".sun()."', varnode='hideInactiveStaff',varkey='',varvalue='$hideInactive'");
	$_SESSION['userSettings']['hideInactiveStaff']=$hideInactive;
	?><script language="javascript" type="text/javascript">
	hideInactiveStaff=<?php echo $hideInactive?>;
	window.parent.hideInactiveStaff=<?php echo $hideInactive?>;
	</script><?php
}


$asc=($dir==-1?'DESC':'ASC');
switch(true){
	case $sort=='email':
		$orderBy="Email $asc";
	break;
	case $sort=='position':
		$orderBy="MinRole $asc, LastName $asc, FirstName $asc";
	break;
	case $sort=='st_hiredate':
		$orderBy="st_hiredate $asc";
	break;
	case $sort=='homes':
		$orderBy="Homes $asc";
	break;
	case $sort=='children':
		$orderBy="Children $asc";
	break;
	case $sort=='lastlogin':
		$orderBy="LastLogin $asc, LOCCount $asc";
	break;
	case $sort=='logactivity':
		$orderBy="LogActivity $asc";
	break;
	default:
		$sort='fullname';
		$orderBy="LastName $asc, FirstName $asc";
}


if(isset($hideInactive)){
	//update settings and environment
	q("REPLACE INTO bais_settings SET UserName='".sun()."', varnode='hideInactiveStaff',varkey='',varvalue='$hideInactive'");
	$_SESSION['userSettings']['hideInactiveStaff']=$hideInactive;
	?><script language="javascript" type="text/javascript">
	hideInactiveStaff=<?php echo $hideInactive?>;
	window.parent.hideInactiveStaff=<?php echo $hideInactive?>;
	</script><?php
}

if(!$refreshComponentOnly){
	?><style type="text/css">
	.data2 th{
		background-color:papayawhip;
		color:#333;
		}
	.complexData .position{
		text-align:right;
		border-right:1px dotted #333;
		}
	</style>
	<script language="javascript" type="text/javascript">
	var state=<?php echo $showFosterParents?'false':'true'?>; //hide

	hl_bg['stopt']='#6c7093';
	hl_baseclass['stopt']='normal';
	hl_class['stopt']='hlrow';
	
	ogrp['stopt']=new Array();
	ogrp['stopt']['sort']='';
	ogrp['stopt']['rowId']='';
	ogrp['stopt']['highlightGroup']='stopt';
	AssignMenu('^optionsStaff$', 'optionsStaffMenu');

	function optionsStaff(){
		g('oh02').innerHTML=(hideInactiveStaff?'Show inactive staff':'Hide inactive staff');
	}
	function fosterParentState(s){
		for(i=1;i<=fp;i++){
			g('fosterparent'+i).style.display=(s?'table-row':'none');
		}
		g('showHideFp').innerHTML=(s?'Hide Assigned Foster Parents':'Show Assigned Foster Parents');
		state=!s;
		return false;
	}
	</script>
	<div id="optionsStaffMenu" class="menuskin1" style="z-index:1000;" onMouseOver="hlght2(event)" onMouseOut="llght2(event)" onclick="executemenuie5(event)" precalculated="optionsStaff();">
		<?php
		//note that hideInactiveFosterhomes is a javascript settings variable
		?><div id="oh02" nowrap="nowrap" class="menuitems" command="toggleActive('listStaffMaster',hideInactiveStaff);" status="option2">Show inactive staff</div>
	</div>
	<?php
}
$ids=q("SELECT * FROM _v_staff_master_information WHERE ".($userSettings['hideInactiveStaff'] ? "st_active=1" : '1')."
ORDER BY $orderBy", O_ARRAY);
?>
<div class="menubar fr">
	<a id="optionsStaff" title="View Options" href="javascript:;" onclick="hidemenuie5(event,1);showmenuie5(event,1)" oncontextmenu="return false;"><img src="/images/i/home.jpg" alt="Staff" width="32" height="32" /> Options</a>&nbsp;&nbsp;
</div>
<div id="listStaffMaster" refreshparams="noparams">
	<h3>Staff (<span id="listStaffMaster_count"><?php echo count($ids)?></span>)</h3>
	<table width="100%" cellspacing="0" cellpadding="0" class="complexData">
		<thead>
		<tr>
			<?php if(!$hideObjectInactiveControl){ ?>
			<th title="Hide or show inactive staff" class="activetoggle"><a href="javascript:toggleActive('listStaffMaster',hideInactiveStaff);">&nbsp;&nbsp;</a></th>
			<?php } ?>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			
			
			<!-- new fields -->
			<th <?php echo $sort=='fullname' ? 'class="sorted"':''?>><a href="resources/bais_01_exe.php?mode=refreshComponent&component=listStaffMaster&sort=fullname&dir=<?php echo !$dir || ($sort=='fullname' && $dir=='-1') ? 1 : '-1'; ?>" target="w2" title="Sort by Full Name"> Name</a></th>
			<th>Phones</th>
			<th <?php echo $sort=='email' ? 'class="sorted"':''?>><a href="resources/bais_01_exe.php?mode=refreshComponent&component=listStaffMaster&sort=email&dir=<?php echo !$dir || ($sort=='email' && $dir=='-1') ? 1 : '-1'; ?>" target="w2" title="Sort by Email">Email</a></th>
			<th <?php echo $sort=='lastlogin' ? 'class="sorted"':''?>><a href="resources/bais_01_exe.php?mode=refreshComponent&component=listStaffMaster&sort=lastlogin&dir=<?php echo !$dir || ($sort=='lastlogin' && $dir=='-1') ? 1 : '-1'; ?>" target="w2" title="Sort by Last Login">Last Login</a></th>
			<th <?php echo $sort=='logactivity' ? 'class="sorted"':''?>><a href="resources/bais_01_exe.php?mode=refreshComponent&component=listStaffMaster&sort=logactivity&dir=<?php echo !$dir || ($sort=='logactivity' && $dir=='-1') ? 1 : '-1'; ?>" target="w2" title="Sort by Log Activity">Log Activity</a></th>
		  </tr>
		</thead>
		<tfoot>
			<tr><td colspan="96">
				<a href="staff.php?cbFunction=refreshComponent&cbParam=fixed:staffList" onclick="return ow(this.href,'l1_staff','800,700');"><img src="/images/i/add_32x32.gif" width="32" height="32">&nbsp;Add Staff..</a>
			</td></tr>
		</tfoot>
		<tbody id="staffList_tbody" style="height:350px;overflow-x:hidden;overflow-y:scroll;"><?php
		if(count($ids))
		foreach($ids as $v){
			//apply any filters here
			$i++;
			extract($v);
			?>
			<tr id="r_<?php echo $UserName?>" onclick="h(this,'stopt',0,0,event);" ondblclick="h(this,'stopt',0,0,event);openStaff();" oncontextmenu="h(this,'stopt',0,1,event);" class="normal<?php echo fmod($i,2)?' alt':''?>" deletable="<?php echo $deletable?>" active="<?php echo $st_active?>">
				<?php if(!$hideObjectInactiveControl){ ?>
				<td id="r_<?php echo $UserName?>_active" title="Make this staff record <?php echo $st_active ? 'in':''?>active" onclick="toggleActiveObject('listStaffMaster','<?php echo $UserName?>');" class="activetoggle"><?php
				if(!$st_active){
					?><img src="/images/i/garbage2.gif" width="18" height="21" align="absbottom" /><?php
				}else{
					?>&nbsp;<?php
				}
				?></td>
				<?php } ?>
				<td nowrap="nowrap">
				<?php
				if($_SESSION['admin']['roles'][ROLE_FOUNDATION_DIRECTOR] || $_SESSION['admin']['roles'][ROLE_PROGRAM_DIRECTOR] || $_SESSION['admin']['roles'][ROLE_PROGRAM_DIRECTOR_ASSISTANT]){
					if(!$accesses){
						if($accesses=list_casemanagers('keys')){
						
						}else{
							$accesses=array();
						}
					}
					if(in_array($UserName,$accesses)){
						?><a href="resources/bais_01_exe.php?mode=deleteStaff&amp;un_username=<?php echo $UserName?>" title="Delete this Case Manager or Staff" target="w3"onclick="return confirm('Are you sure you want to permanently delete this staff member?');"><img src="/images/i/del2.gif" alt="delete case manager" width="16" height="18" border="0" /></a><?php
					}
					?><?php
				}
				?>
				<a href="staff.php?un_username=<?php echo $UserName?>" onclick="return ow(this.href,'l1_pds','800,700');" title="View this staff member's record"><img src="/images/i/edit2.gif" width="15" height="18" border="0"></a></td>
				<td><?php
					if($un_email){
						?><a title="<?php echo $un_email?>" href="mailto:<?php echo $un_email?>"><img src="/images/i/mail1_30x30.gif" alt="email" width="30" height="30" border="0" /></a><?php
					}
					?>
					&nbsp;</td>
					
					
				<td <?php echo $sort=='fullname' ? 'class="sorted"':''?>>				
				<a href="staff.php?un_username=<?php echo $UserName?>" onclick="return ow(this.href,'l1_pds','800,700');" title="View this staff member's record"><?php echo $LastName . ', '.$FirstName?></a> <?php if($DBA)echo '[*DBA]';?></td>				
				<td <?php echo $sort=='phones' ? 'class="sorted"':''?>><?php
					foreach(array('Phone','Work Phone','Cell') as $w){
						if($x=$GLOBALS[str_replace(' ','',$w)])echo $x . ' ('.$w.')<br />';
					}
					?>&nbsp;</td>
				<td <?php echo $sort=='email' ? 'class="sorted"':''?>><a href="mailto:<?php echo $Email?>?subject=Fostex Contact" title="Email this staff member"><?php echo $Email?></a></td>
				<td <?php echo $sort=='lastlogin' ? 'class="sorted"':''?>><?php if($LastLogin && $LastLogin!=='0000-00-00 00:00:00')echo date('m/d/Y \a\t g:iA',strtotime($LastLogin));?> </td>
				<td <?php echo $sort=='logactivity' ? 'class="sorted"':''?>><?php if($LogActivity){ echo $LogActivity; }else{ echo '--'; }?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<script language="javascript" type="text/javascript">
	var fp=<?php echo $fp?$fp:0?>;
	window.parent.fp=<?php echo $fp?$fp:0?>;
	</script>
</div>