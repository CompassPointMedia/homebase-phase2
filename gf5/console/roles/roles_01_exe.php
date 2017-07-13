<?php
//this is an executable script which only outputs control javascript after execution on the server side.
session_start();
$localSys['scriptID']='roles';
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
	//error checks
	if(!$ro_name || !$ro_description){
		?><script>alert('Role name and description cannot be blank');</script><?php
		exit;
	}
	if(!$rankAtTop && !$refrank){
		?><script>alert('You must select a ranking for this role');</script><?php
		exit;
	}
	//see if the record is a duplicate
	$sql="SELECT ro_name from bais_roles WHERE LCASE(ro_name)='".strtolower($ro_name)."'";
	$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
	if(mysqli_num_rows($result)){
		?><script>alert('That role name is already present.  Role names must be unique');</script><?php
		exit;
	}
	//get ranking
	if($rankAtTop){
		$sql="SELECT MIN(ro_rank) AS ThisMin from bais_roles";
		$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
		$r=mysqli_fetch_array($result,$db_cnx);
		$x=$r[ThisMin];		
	}else{
		$x=$refrank;
	}
	$sql="UPDATE bais_roles SET ro_rank = ro_rank+1 WHERE ro_rank>'$x' ORDER BY ro_rank DESC";
	$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
	$_POST[ro_rank]=$x;
	$sql=sql_query_gen('bais_roles','','INSERT INTO', $_POST);
	$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
	$id=mysqli_insert_id($db_cnx);
	if($addAfter){
		//we need to refresh the dropdown list
		?><html><body><div id="refranking"><select name="refrank" id="refrank"><option value=''>-- select --<?php
		$sql="SELECT ro_rank, ro_name FROM bais_roles ORDER BY ro_rank ASC";
		$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
		while($r=mysqli_fetch_array($result,$db_cnx)){
			extract($r);
			echo "<option value=$ro_rank>".htmlentities($ro_name);
		}
		?></select></div></body></html><?php
		//we clear the parent form
		?><script>
		wp=window.parent.form1;
		wp.ro_name.value='';
		wp.ro_description.value='';
		window.parent.refranking.innerHTML=refranking.innerHTML;
		wp.rankAtTop.checked=false;
		if(typeof wp.cb!=='undefined'){
			window.parent.opener.evaluator(wp.cb);
			window.parent.opener.ro_id=<?php echo $id?>;
		}
		</script><?php
	}else{
		?><script>
		wp=window.parent;
		if(typeof wp.cb!=='undefined'){
			wp.opener.evaluator(wp.cb);
			wp.opener.ro_id=<?php echo $id?>;
			wp.close();
		}
		</script><?php
	}	
}else if($_case=='delete'){
	?><script>alert('Role update not developed');</script><?php
}else if($_case=='update'){
	//
	?><script>alert('Role update not developed');</script><?php
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