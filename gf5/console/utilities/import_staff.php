<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='focusview';
$localSys['pageType']='Properties Window';
$localSys['pageLevel']=0;

require('../systeam/php/config.php');

require('../resources/bais_00_includes.php');

require('../systeam/php/auth_i2_v100.php');

//store the file

//read it

//translate the headers by some means

//insert or update the staff

//associate roles

//associate offices



//send a report on what was not done

$fields=array(
	'username'=>0,
	'firstname'=>1,
	'middlename'=>0,
	'lastname'=>1,
	'cell'=>0,
	'email'=>0,
	'offices'=>0,
	'title'=>0,
	'roles'=>1,
	
);

/* 
can't find a minimum number of fields (first row probably not present)
see insert into bais_staff and add to $fields array (all not required)
*/

?><form enctype="multipart/form-data" method="post">
upload: 
  <input type="file" name="file">
  <input type="submit" name="Submit" value="Submit">
</form><?php
if($_FILES['file']){
	if(is_uploaded_file($_FILES['file']['tmp_name'])){
		$fp=fopen($_FILES['file']['tmp_name'], 'r');
		$i=0;
		while($r=fgetcsv($fp,10000)){
			$i++;
			if($i==1){
				//here is logic to trans. the fields
				foreach($r as $n=>$v){
					$field=preg_replace('/[^a-z0-9]/','',strtolower($v));
					if(isset($fields[strtolower($field)])){
						define($field,$n);
					}
				}
				continue;
			}
			if($r[username]){
				//OK
			}else{
				error_alert('PY create username from fctn');
			}
			q("INSERT INTO bais_universal SET
			un_username='".addslashes($r[username])."',
			un_firstname='".addslashes($r[firstname])."',
			un_middlename='".addslashes($r[middlename])."',
			un_lastname='".addslashes($r[lastname])."',
			un_email='".addslashes($r[email])."',
			un_creator='".sun()."',
			un_createdate=NOW()");

			q("INSERT INTO bais_staff SET
			st_unusername='".addslashes($r[username])."',
			".($r[hiredate] ? "st_hiredate='".date('Y-m-d',strtotime($r[hiredate]))."'," : '')."
			".($r[dischargedate] ? "st_hiredate='".date('Y-m-d',strtotime($r[dischargedate]))."'," : '')."
			".($r[dischargereason] ? "st_hiredate='".date('Y-m-d',strtotime($r[dischargereason]))."'," : '')."
			Gender='".addslashes($r[gender])."',
			Race='".addslashes($r[race])."',
			SocSecurityNumber='".addslashes($r[socsecuritynumber])."',
			BirthDate='".addslashes($r[birthdate])."',
			JobTitle='".addslashes($r[jobtitle])."',
			Address='".addslashes($r[address])."',
			City='".addslashes($r[city])."',
			State='".addslashes($r[state])."',
			Zip='".addslashes($r[zip])."',
			Country='".addslashes($r[country])."',
			Phone='".addslashes($r[phone])."',
			WorkPhone='".addslashes($r[workphone])."',
			PagerVoice='".addslashes($r[pagervoice])."',
			Cell='".addslashes($r[cell])."',
			MisctextStaffnotes='".addslashes($r[misctextstaffnotes])."',
			
			st_createdate=NOW(),
			st_creator='".sun()."'");
			
			unset($roles);
			
			if(trim($r[roles]) && ($a=explode(',',trim($r[roles]))))
			foreach($a as $v){
				$v=trim($v);
				if(!$v)continue;
				if($ro_id=q("SELECT ro_id FROM bais_roles WHERE ro_name='".addslashes($v)."' OR ro_shortname='".addslashes($v)."'", O_VALUE)){
					//all roles theyhave
					$roles[]=$ro_id;
					
					q("INSERT INTO bais_StaffRoles SET
					sr_stusername='".$r[username]."',
					sr_roid='$ro_id',
					sr_assignor='".$_SESSION['admin']['roles']."'");
				}
			}
			
			if(trim($r[offices]) && ($a=explode(',',trim($r[offices]))))
			foreach($a as $v){
				$v=trim($v);
				if(!$v)continue;
				if($oa_unusername=q("SELECT oa_unusername FROM bais_orgaliases WHERE 
					oa_businessname='".addslashes($v)."' OR 
					oa_orgcode='".addslashes($v)."' OR 
					oa_org1='".addslashes($v)."' OR 
					oa_org2='".addslashes($v)."'", O_VALUE)){
					q("INSERT INTO bais_StaffOffices SET
					so_stusername='".$r[username]."',
					so_unusername='$oa_unusername',
					so_roid='".($roles[0] ? $roles[0] : q("SELECT MIN(ro_id) FROM bais_roles", O_VALUE))."',
					so_assignor='".$_SESSION['admin']['roles']."'");
				}
			}

		}
	}
}

?>