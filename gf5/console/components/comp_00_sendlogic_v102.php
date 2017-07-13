<?php
/*
NOTE: this comp should be invisible as far as variables, except for 
	sendObjectWhyReceiving
	sendObjectThisIsMe
	sendObjectRecipientHighestRole
2010-09-17
* added gf_logs into the acceptable objects for which I need to get an office (either through ChildrenFosterhomes_ID OR FosterhomesParents_ID)
2010-08-05
* improved output for testing $sendLogicTest
* prevented multiple sends to a user
2010-07-19
near completion.  In a nutshell, the logic this uses is as follows:
	* look for specificSends.  There is no checking for responsibilities on this list
	* look for people in that office (area directors) if this option is selected
	* look for people up one level (either regional or senior directors, until we find someone)

	
2010-07-13
* complete upgrade.  Based on ability to now separate between AD and RD in gf_StaffOffices.  I can be an AD of an office but not an RD and vice versa.  This was lacking before today when I introduced the field so_roid defaulting to 3.0
Created 2010-06-01
* NOTE notes bottom of page - may be useful before I approached it as below

In all cases the office is the office of the object (child or foster home), NOT of the staff.  "In the office" is defined as people assigned to that office with that permission, namely an Area Director (AD)(Program Director), including perhaps the person operating.  "Up one level" means, for a min.roles=ROLE_PROGRAM_DIRECTOR, the regional director(s) of that office, and for min.roles=ROLE_REGIONAL_DIRECTOR, the senior director(s) of the database with that permission

This system requires that we standardize some the format of the emails so tha the recipient is always that array, i.e. recipient.FirstName, etc., and we also know these parameters
	sendObjectThisIsMe - if the recipient is in fact the operator
	sendObjectRecipientHighestRole - the min(session.admin.roles) of the recipient
	sendObjectWhyReceiving - 
*/

//as of today only bais_staff: are developed
$myRole=minroles();
$myCognate=($myRole==ROLE_THERAPIST ? 'gf_therapists' : ($myRole==ROLE_PARENT ? 'gf_parents' : 'bais_staff'));

if(!defined('SEND_SPECIFIC'))define('SEND_SPECIFIC',8);
if(!defined('SEND_UPONELEVEL'))define('SEND_UPONELEVEL',4);
if(!defined('SEND_OFFICE'))define('SEND_OFFICE',2);


ob_start();
unset($sendableRecipients);
$sendLogicSettings=$adminSettings[ROLE_DBADMIN]['sendObjects'][$sendObject];

$sends=array();
if($sendLogicSettings['specificSends']){
	print_r("\n1\n");
	print_r($sendLogicSettings['specificSends']);
	foreach($sendLogicSettings['specificSends'] as $v){
		//each recipient is a key
		$sends[$v][]=SEND_SPECIFIC;
	}
}
if(!function_exists('get_log_office')){
	function get_log_office($ID){
		return false;
		
		//this apparently from Simp LeFoste RcaRE was never right
		$a=q("SELECT fp.FosterhomesParents_ID AS FPID, cf.ChildrenFosterhomes_ID AS CFID
		FROM gf logs a
		LEFT JOIN gf_FosterhomesParents fp ON fp.ID=a.FosterhomesParents_ID
		LEFT JOIN gf_ChildrenFosterhomes cf ON cf.ID=a.ChildrenFosterhomes_ID
		WHERE a.ID='$ID'", O_ROW);
		if(!$a)return false;
		return ($a['CFID'] ? $a['CFID'] : $a['FPID']);
	}
}

if($_office_=
	($sendLogicSettings['objectTable']=='finan_items' ? 
	q("SELECT of_oausername FROM bais_offices ORDER BY IF(PrimaryOffice=1,1,2) LIMIT 1", O_VALUE) : 
	($sendLogicSettings['objectTable']=='gf_fosterhomes' ? 
	false /* q("SELECT fh_oausername FROM gf_fosterhomes WHERE ID='".($Fosterhomes_ID ? $Fosterhomes_ID : $ID)."'", O_VALUE) */ : 
	($sendLogicSettings['objectTable']=='gf_parents' ? 
	false /* q("SELECT un_username FROM gf_parents WHERE ID='".($Parents_ID)."'", O_VALUE) */ : 
	($sendLogicSettings['objectTable']=='gf_logs' ?
	get_log_office($ID): ''
	/* no further operations right now */))))){

	print_r("\n2\n");
	print_r($_office_);
	//get same office staff with those permissions - program (area) directors
	if($sendLogicSettings['notifyOfficeLevel'] && ($backupArea=q("SELECT so_stusername, MIN(REPLACE(sr_roid,'.0','')) FROM gf_StaffOffices, bais_StaffRoles WHERE so_stusername=sr_stusername AND so_unusername='".$_office_."' AND so_roid=".ROLE_PROGRAM_DIRECTOR." AND (so_permissions & ".$sendLogicSettings['permissions'].")>0 GROUP BY so_stusername", O_COL_ASSOC))){
		print_r("\n3\n");
		print_r($qr);
		//send to all ADs with that permission
		foreach($backupArea as $n=>$v){
			$sends[$myCognate.':'.$n.':'.$v][]=SEND_OFFICE;
		}
	}
	if($sendLogicSettings['notifyUpOneLevel']){
		$upOneLevel=false;
		if($apSettings['implementRegionalDirector']){
			//get same office staff with those permissions - regional directors (used first by Lutheran)
			if($backupRegion=q("SELECT so_stusername, MIN(REPLACE(sr_roid,'.0','')) FROM gf_StaffOffices, bais_StaffRoles WHERE so_stusername=sr_stusername AND so_unusername='".$_office_."' AND so_roid=".ROLE_REGIONAL_DIRECTOR." AND (so_permissions & ".$sendLogicSettings['permissions'].")>0 GROUP BY so_stusername", O_COL_ASSOC)){
				//regional directors for this office with those permissions
				foreach($backupRegion as $n=>$v){
					$sends[$myCognate.':'.$n.':'.$v][]=SEND_UPONELEVEL;
					$upOneLevel=true;
				}
			}else{
				//we continue
			}
		}
		if(!$upOneLevel && ($backupSenior=q("SELECT sr_stusername, 2 FROM bais_StaffRoles WHERE sr_roid=2 AND (sr_permissions & ".$sendLogicSettings['permissions'].")>0" , O_COL_ASSOC))){
			print_r("\n4\n");
			print_r($qr);
			//senior directors with those permissions
			foreach($backupArea as $n=>$v){
				$sends[$myCognate.':'.$n.':'.$v][]=SEND_UPONELEVEL;
				$upOneLevel=true;
			}
		}
	}
	if($sends){
		print_r("\n5\n");
		print_r($sends);
		foreach($sends as $_recip_n=>$sendObjectWhyReceiving){
			$_recip_n=explode(':',$_recip_n);
			switch(true){
				case $_recip_n[0]=='bais_staff':
				case true:
					//this is a standard recipient row
					$recipient=q("SELECT un_firstname AS FirstName, un_middlename AS MiddleName, un_lastname AS LastName, un_email AS Email, un_username AS UN FROM bais_universal WHERE un_username='".$_recip_n[1]."'", O_ROW);	
			}
			if(!$recipient['Email']){
				//cannot send to this person - update rules? ... email someone? whatToDoOnNoEmail= :)
				continue;
			}
			$sendObjectThisIsMe=($_recip_n[1]==sun());
			
			//2010-08-06 prevent multiple sends to single user
			if($sendableRecipients[$_recip_n[1]]){
				continue;
			}else{
				$sendableRecipients[$_recip_n[1]]=true;
			}
			//------------- send the email ---------------
			$emailSource=$_SERVER['DOCUMENT_ROOT'].'/gf5/console/emails/'.$adminSettings[ROLE_DBADMIN]['registeredTemplates'][$sendLogicSettings['template']]['file'];
			prn($emailSource);
			$emailTo=($shuntToDeveloper ? $developerEmail : $recipient['Email']);
			require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
		}
	}else{
		//nobody to send to
		mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals($err='Unable to find anyone to send this notification email to'),$fromHdrBugs);
	}
}else{
	mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals($err='Unable to find the office of the operation object'),$fromHdrBugs);
}

if($sendLogicTest){
	$out=ob_get_contents();
	mail($developerEmail, 'Send logic report '.__FILE__.', line '.__LINE__,get_globals($out),$fromHdrBugs);
}
ob_end_clean();
echo $out;

?>