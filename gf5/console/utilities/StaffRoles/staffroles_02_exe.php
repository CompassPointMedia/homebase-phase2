<?php
//this is an executable script which only outputs control javascript after execution on the server side.
session_start();
$localSys['scriptID']='staffroles';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='executer01';

//main configuration file
require('../systeam/php/config.php');
//Verify access to this page
require_once('../../console/systeam/php/auth_i2_v100.php');
//===================== INCLUDES ===========================
if(!$roleAccessPresent){
	?><script>alert('You do not have permission to do this task');</script><?php
	exit('This page allows access through a specific role only.  Please see your administrator');
}
$db_cnx=mysqli_connect($MASTER_HOSTNAME, $MASTER_USERNAME, $MASTER_PASSWORD);
mysqli_select_db($db_cnx, $MASTER_DATABASE);

//only process we have going initially
if($_case=='changestatus'){
	//for safety
	if(!$st_unusername || !$ro_id){
		?><script>alert('Either no username or no role id passed');</script><?php
		exit;
	}
	//newStatus = 1 = delete, = 2 = instate
	if($newStatus==1){
		//delete or revoke status
echo 		$sql="DELETE FROM bais_StaffRoles WHERE sr_stusername='$st_unusername' AND sr_roid='$ro_id'";
		$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($result));
		//reactivate the parent window's div tag
		?>
		<html>
		<script>
		window.parent.statusBar1.innerHTML='';
		</script>
		</html>
		<?php
		page_end();
		exit;
	}else if($newStatus==2){
		//instate status
		$sql="SELECT a.*, REPLACE(sr_roid,'.0','') AS sr_roid FROM bais_StaffRoles a WHERE sr_stusername='$st_unusername' AND sr_roid='$ro_id'";
		$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
		if(mysqli_num_rows($result)){
			//this should not typically happen
			$sql="UPDATE bais_StaffRoles SET sr_editor='".$_SESSION[admin][userName]."', sr_editdate='".$dateStamp."'";
			$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
		}else{
			$a[stusername]=$st_unusername;
			$a[roid]=$ro_id;
			$a[assignor]=$_SESSION[admin][userName];
			$sql=sql_query_gen("bais_StaffRoles",'cpm028',"INSERT INTO",$a);
			$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
		}
		?>
		<html>
		<script>
		window.parent.statusBar1.innerHTML='';</script>
		</html>
		<?php
		page_end();
		exit;
	}
}else{
	?>
	<html>
	<script>
	alert('No _case passed or case undefined');
	</script>
	</html>
	<?php
	page_end();
}
?>