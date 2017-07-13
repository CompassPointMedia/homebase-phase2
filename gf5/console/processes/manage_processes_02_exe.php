<?php
//this is an executable script which only outputs control javascript after execution on the server side.
session_start();
$localSys['scriptID']='processes';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='executer01';

//main configuration file
require('../systeam/php/config.php');
//Verify access to this page
require_once('../console/systeam/php/auth_i2_v100.php');
//===================== INCLUDES ===========================
if(!$roleAccessPresent){
	?><script>alert('You do not have permission to do this task');</script><?php
	exit('This page allows access through a specific role only.  Please see your administrator');
}
$db_cnx=mysqli_connect($MASTER_HOSTNAME, $MASTER_USERNAME, $MASTER_PASSWORD);
mysqli_select_db($db_cnx, $MASTER_DATABASE);

//only process we have going initially
if($_POST['_case']=='insert'){
	//for safety
	unset($_POST[pr_id]);
	$sql=sql_query_gen("bais_processes",'','',$_POST);
	$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
	$id=mysqli_insert_id($db_cnx);

	//build out javascript -- write innerHTML to the appropriate rolegroup table
	?>
	<html>
	<script>
	//build the selection as the first element
	window.parent.pr_list.innerHTML='<LI class=listImage1 id=pr_<?php echo $id?> onclick=highlight_select(this); label="<?php echo htmlentities($pr_name);?>"><?php echo htmlentities($pr_name);?></LI>'+window.parent.pr_list.innerHTML;
	//highlight the selection, also sets the value and label
	window.parent.pr_<?php echo $id?>.onclick();
	//focus on the new page
	window.parent.form1.focus();
	//enable the OK button if not already done
	window.parent.document.all.ctrlOK.disabled=false;
	//re-disable the Add Process button
	window.parent.document.all.submitNew.disabled=true;
	<?php if(!$addAfter){?>
	//focus on the existing process tab
	window.parent.seladdproc_i_existingProc.onclick();
	alert('Process Added OK');
	<?php }?>
	</script>
	</html>
	<?php
	exit;
}else if($_case=='join'){
	//we are going to join admin role to a process, provided it's not unique
	$sql="SELECT ro_name from bais_RolesProcesses, bais_roles WHERE ro_id=rp_roid AND rp_roid=$ro_id AND rp_prid='$pr_id'";
	$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
	if(mysqli_num_rows($result)){
		$r=mysqli_fetch_array($result,$db_cnx);
		extract($r);
		?>
		<script>
		alert('This process already belongs to the role of <?php echo $ro_name?>');
		</script>
		<?php
		exit;
	}else{
		//insert the join, refresh the subwindow
		if(!$ro_id || !$pr_id){
			?><script>alert('Abnormal error: no pr_id or ro_id value passed');</script><?php  exit;
		}
		$a[rp_roid]=$ro_id;
		$a[rp_prid]=$pr_id;
		$a[rp_notes]='Added by process '.$localSys['scriptID'].' version '.$localSys['scriptVersion'];
		//MUST declare the mode as INSERT INTO here (join table)
		$sql=sql_query_gen("bais_RolesProcesses",'','INSERT INTO',$a);
		$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
		
		//refresh the listing, wish there was a way to preserve state, I can also write dynamically a new line but that seems over doing it
		?>
		<script>
		window.parent.location=window.parent.location;
		</script>
		<?php
	}
}else if($_case=='removeroleprocess'){
	//
	if(!$ro_id || !$pr_id){
		?><script>alert('Please select a process');</script><?php
		exit;
	}
	$sql="DELETE FROM bais_RolesProcesses WHERE rp_roid=$ro_id AND rp_prid='$pr_id' LIMIT 1";
	$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
	exit;
	//this would refresh the parent but not used anymore
	?>
	<script>
	window.parent.location=window.parent.location;
	</script>
	<?php
}else if($_case=='update'){
	if(!$pr_id){
		?><script>alert('Abnormal Error: no process id passed');</script><?php
		exit;
	}
	if(!trim($pr_name) || !trim($pr_version) || !preg_match('/^[a-z0-9_]+$/i',$pr_handle)){
		?><script>alert('Process must have a name, handle, and version.\nHandle must contain only numbers and letters or the underscore character');</script><?php
		exit;
	}
	echo $sql=sql_query_gen("bais_processes",'','UPDATE',$_POST);
	echo "<br />";
	$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));

	//handle callbacks
	?><script>window.parent.ctrlOK.disabled=false;</script><?php
	
	
}else{
	?>
	<html>
	<script>
	alert('No action performed, abnormal error');
	</script>
	</html>
	<?php
}
?>