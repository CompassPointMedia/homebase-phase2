<?php
//identify this script/GUI
$localSys['scriptID']='ecommerce';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';

require('../systeam/php/config.php');

require('../resources/bais_00_includes.php');

require('../systeam/php/auth_i2_v100.php');

$hideCtrlSection=false;


//--------------------------------------------------
$db_cnx=mysqli_connect($MASTER_HOSTNAME, $MASTER_USERNAME, $MASTER_PASSWORD);
mysqli_select_db($db_cnx, $MASTER_DATABASE);

//only process we have going initially
if($_case=='insert'){
	//error checking
	if(!preg_match('/^[a-z0-9_]{4,}$/i',$st_unusername) || !trim($un_password)){
		?><script>alert('UserName must contain only letters and numbers, and password must be present')</script><?php
		exit;
	}
	if(!$un_firstname || !$un_lastname){
		?><script>alert('First name and last name cannot be blank')</script><?php
		exit;
	}
	if(!preg_match('/^([-a-z0-9_]+)(\.[-a-z0-9_]+)*@([-a-z0-9]+)\.[a-z0-9]+$/i',$st_email)){
		?><script>alert('Email is not valid')</script><?php
		exit;
	};
	//lower case values
	$_POST[st_unusername]=strtolower($_POST[st_unusername]);
	$_POST[st_email]=strtolower($_POST[st_email]);
	
	//if they are not in universal contacts, insert them now
	$sql="SELECT * FROM bais_universal WHERE un_username = '".strtolower(trim($_POST[st_unusername]))."' OR un_email = '".strtolower(trim($_POST[st_email]))."'";
	$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
	if(!mysqli_num_rows($result)){
		$sql="INSERT INTO bais_universal SET
		un_username = '".$_POST[st_unusername]."',
		un_password ='".md5($_POST[un_password])."',
		un_email = '".$_POST[st_email]."',
		un_firstname = '".$_POST[un_firstname]."',
		un_lastname = '".$_POST[un_lastname]."',
		un_createdate ='$dateStamp',
		un_creator = 'system',
		un_editdate ='$timeStamp'";
		$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
	}
	
	$sql=sql_query_gen('bais_staff','','INSERT INTO',$_POST);
	$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
	if($grantUsage){
		q("UPDATE bais_staff SET st_status=1 WHERE st_unusername='$st_unusername'");
	}

	//build out javascript -- call the include page which will refresh the contents
	?>
	<html>
	<script>
	//this window when called will 
	window.parent.w2.location='manage_staff_01_stafflist.php?srcregion=staffList&tgtregion=staffList&highlight=<?php echo $st_unusername?>';
	//select the new -- can't figure out how to do this right now -- because of time lag it needs to come from the source page

	//focus on the new page
	window.parent.form1.focus();
	//enable the OK button if not already done
	window.parent.document.all.ctrlOK.disabled=false;
	//re-disable the Add Process button
	window.parent.document.all.submitNew.disabled=true;
	<?php if(!$addAfter){?>
	//focus on the existing process tab
	window.parent.seladdstaff_i_existingStaff.onclick();
	alert('Staff Added OK<?php echo $grantUsage?" and added to Admins":""?>');
	<?php }?>
	//clear the form
	wp=window.parent.form1;
	wp.un_firstname.value='';
	wp.un_lastname.value='';
	wp.st_email.value='';
	wp.st_unusername.value='';
	wp.un_password.value='';
	</script>
	</html>
	<?php
	exit;
}else if($_case=='removefromusage'){
	//remove from usage table, will cascade
	$sql="UPDATE bais_staff SET st_status=1 WHERE st_unusername='$st_unusername'";
	$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
	?><script>
		//deselect key values not needed
			//window.parent.username='';
			//window.parent.fullname='';
		//hide record by refreshing
		window.parent.w2.location='manage_staff_01_stafflist.php?srcregion=staffList&tgtregion=staffList';
	</script><?php
	
}else if($_case=='remove'){
	//remove record entirely -- requires special permissions
	if(!$st_unusername){
		?><script>alert('No username submitted, try again');</script><?php
		exit;
	}
	$sql="DELETE FROM bais_staff WHERE st_unusername='$st_unusername'";
	$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
	?><script>
		//deselect key values not WORKING correctly but needed
		//window.parent.system_username='';
		//window.parent.system_fullname='';
		//hide record by refreshing
		try{
			window.parent.w2.location='manage_staff_01_stafflist.php?srcregion=staffList&tgtregion=staffList';
		}catch(e){ }
		</script>
	<?php
	exit;
}else if($_case=='installusage'){
	if(!$st_unusername){
		?><script>alert('No username submitted, try again');</script><?php
		exit;
	}
	//we CANNOT replace into or we'll destroy permissions downstream
	$sql="SELECT * from bais_staff WHERE st_unusername='$st_unusername'";
	$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
	if(mysqli_num_rows($result)){
		//do nothing -- the option should have been grayed out on the context menu
		?><script>alert('User <?php echo $st_unusername?> already has usage');</script><?php
		exit;
	}else{
		$sql=sql_query_gen('bais_staff','','INSERT INTO',$a);
		$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
		?><script>
			window.parent.w2.location='manage_staff_01_stafflist.php?srcregion=staffList&tgtregion=staffList';
			</script>
		<?php
	}
}else if($_case=='resetpassword'){
	if(!$un_username){
		?><script defer>alert('No username passed, abnormal error.  Contact IT Staff')</script><?php
		exit;
	}
	if(strlen(trim($string))<3){
		?><script defer>alert("No password selected, or it's WAY too short.  Try again");</script><?php
		exit;
	}
	$sql="UPDATE bais_universal SET un_password='".md5($string)."' WHERE un_username='$un_username'";
	$result=mysql_query($sql) or die(mysqli_error());
	?><script defer>alert('Password Changed.  Be sure and notify the user!')</script><?php
	exit;
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