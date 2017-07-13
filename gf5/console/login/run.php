<?php
$localSys['scriptID']='login';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='';
//pageLevel NOT declared here as we don't want to count login

require_once($_SERVER['DOCUMENT_ROOT'].'/gf5/console/systeam/php/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/gf5/console/resources/bais_00_includes.php');



$a=q("SELECT ID, IF(CompanyName, CompanyName, ClientName) AS CompanyName, UserName, PrimaryFirstName, PrimaryMiddleName, PrimaryLastName, Email, Notes FROM finan_clients WHERE ResourceType IS NOT NULL", O_ARRAY);
foreach($a as $v){
	extract($v);
	if($UserName){
		//OK
	}else if($PrimaryFirstName || $PrimaryLastName){
		$UserName=sql_autoinc_text();
		q("UPDATE finan_clients SET UserName='$UserName' WHERE ID=$ID");
	}else if($CompanyName){
		$UserName=sql_autoinc_text();
	}else{
		//no information
		prn('no username gotten');
		continue;
	}
	if($Contacts_ID=q("SELECT 
	cc.Contacts_ID
	FROM addr_contacts c, finan_ClientsContacts cc
	WHERE c.ID=cc.Contacts_ID AND cc.Clients_ID=$ID AND cc.Type='Primary'", O_VALUE)){
		q("UPDATE addr_contacts SET 
		UserName='$UserName', 
		FirstName=IF('$PrimaryFirstName'!='','$PrimaryFirstName',FirstName),
		LastName=IF('$PrimaryLastName'!='','$PrimaryLastName',LastName)
		Email=IF('$Email'!='','$Email',Email)
		WHERE ID=$Contacts_ID");
	}else{
		prn('unable to find contact record for client '.$ID);
		continue;
	}
	//insert the universal record
	q("INSERT INTO bais_universal SET un_username='$UserName', un_firstname='$PrimaryFirstName', un_middlenam='$PrimaryMiddleName', un_lastname='$PrimaryLastName', un_email='$Email', un_createdate=NOW(), un_creator='sfullman'");
}
?>