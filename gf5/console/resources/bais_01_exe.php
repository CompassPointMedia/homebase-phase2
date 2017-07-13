<?php
//this page can be called multiple times
ini_set('error_reporting',7);
$bais_01_exe++;

if(!$mode)$mode=$_REQUEST['mode'];

# Identify this script
$localSys['scriptID']='';
$localSys['scriptVersion']='';
switch(true){
	case $mode=='rootManageRoles':
		$localSys['scriptID']='mg_application';
		$localSys['scriptVersion']='4.0';
	break;
	case $mode=='updateParameter':
	case $mode=='updateBillingSent':
	case $mode=='toggleDiscrepancy':
	case $mode=='pullProperty':
	case $mode=='search':
	case $mode=='refreshComponent':
	case $mode=='rentalSearch':
	case $mode=='insertClient':
	case $mode=='updateClient':
	case $mode=='deleteClient':
	case $mode=='insertLease':
	case $mode=='updateLease':
	case $mode=='deleteLease':
	case $mode=='listBuilder': /* added 2010-12-24 */
	case $mode=='QuickAddContact':
	case $mode=='manageUnitInventory':
	case $mode=='uploadPropertyPictures':
	case $mode=='updateStaff':
	case $mode=='insertStaff':
	case $mode=='deleteStaff':
	case $mode=='changepassword':
	case $mode=='updateLateStatus':
	case $mode=='updateTiers':
	case $mode=='commitGiftCardBatch':
	case $mode=='pullBilling':
	case $mode=='updateUnit':
	case $mode=='uploadFile':
	case $mode=='deleteObject':
	case $mode=='voidObject':
	case $mode=='getHistory':
	case $mode=='toggleActiveObject':
	case $mode=='couponVerification':
	case $mode=='deleteMySearches':
	case $mode=='insertProperty':
	case $mode=='updateProperty':
	case $mode=='deleteProperty':
	case $mode=='insertPayment':
	case $mode=='updatePayment':
	case $mode=='deletePayment':
	case $mode=='customerSearch':
	case $mode=='deleteBulletin':
	case $mode=='insertBulletin':
	case $mode=='updateBulletin':
	case $mode=='acknowledgeBulletin':
	case $mode=='dismissBulletin':
	case $mode=='sendFeedback':
	case $mode=='sendSearchResults':
	case $mode=='updateUtilities':
	case $mode=='updateAllUnits':
	case $mode=='updateClientData':
	case $mode=='talklisten':
	case $mode=='updateOffice':
	case $mode=='insertOffice':
	case $mode=='deleteOffice':
	case $mode=='downloadFile':
	case $mode=='deleteSearch':
	case $mode=='updatePreferences':
	case $mode=='updatePreferencesSettingsNodes':
	case $mode=='setVoid':
	case $mode=='uploadRelativeFile':
	case $mode=='downloadRelativeFile':
	case $mode=='createRelativeFolder':
	case $mode=='deleteRelativeFile':
	case $mode=='submitPayment':
	case $mode=='insertSupportContact':
	case $mode=='updateSupportContact':
	case $mode=='deleteSupportContact':
	case $mode=='dismissBulletin':
	case $mode=='sendFeedback':
	case $mode=='deleteBillingSent':
	case $modePassed=='insertDatasetComponent':
	case $modePassed=='updateDatasetComponent':
	case $modePassed=='deleteDatasetComponent':
	case $mode=='insertItem':
	case $mode=='updateItem':
	case $mode=='deleteItem':
	case $mode=='insertTheme':
	case $mode=='updateTheme':
	case $mode=='deleteTheme':
	case $mode=='recalcPrices':
	case $mode=='importManager':
	case $mode=='exportManager':
	case $mode=='deleteMapPending':
	case $mode=='clearLinkManager':
	case $mode=='exportSummaryUpdate':
	case $mode=='getCountyPointPerimeter':
	case $mode=='getStatePointPerimeter':
	case $mode=='insertObject':
	case $mode=='updateObject':
	case $mode=='deleteObject':
	case $mode=='insertObjects':
	case $mode=='updateObjects':
	case $mode=='deleteObjects':
	case $mode=='delInProcess':
	case $mode=='filesizeComparison':
	case $mode=='importProcess':
		$localSys['scriptID']='gen_access1';
		$localSys['scriptVersion']='4.0';
	break;
	default:
		mail('reroute@compasspointmedia.com','error line '.__LINE__,$mode.' is not a recognized mode in page '.__FILE__,$fromHdrBugs);
}
$localSys['build']=100;
$localSys['buildDate']='2010-11-20 12:00';
$localSys['buildNotes']='pulled over from G iocosaC are';
$localSys['pageType']='Executable Page';

//require config files if not already present

require_once($_SERVER['DOCUMENT_ROOT'].'/gf5/console/systeam/php/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/gf5/console/resources/bais_00_includes.php');

//shutdown coding
if(!$shutdownRegistered){
	$shutdownRegistered=true;
	$assumeErrorState=true;
	register_shutdown_function('iframe_shutdown');
	ob_start('store_html_output');
}
//2008-05-04 - store the post content
if($repostID){
	q("UPDATE gf_poststorage SET Reposted=Reposted+1 WHERE ID=$repostID");
}else if(count($_POST) && !$postStored){
	$postStored=true;
	$Poststorage_ID=q("INSERT INTO gf_poststorage SET UserName='".sun()."', Mode='$mode', Content='".
	base64_encode(serialize($_POST))."',
	Session='". base64_encode(serialize($_SESSION)) . "'", array($MASTER_HOSTNAME,$MASTER_USERNAME,$MASTER_PASSWORD,$MASTER_DATABASE));
}
//verify access to this page
if(!in_array($mode,array(
	'couponVerification'
	))){
	require_once($_SERVER['DOCUMENT_ROOT'].'/gf5/console/systeam/php/auth_i2_v100.php');
}

//2009-07-04 - autosave logic
/*
if($autosave){
	if(!$ResourceToken){
		mail($AcctEmail,'Error file '.__FILE__.', line '.__LINE__, get_globals(),$fromHdrBugs);
		error_alert('Unable to auto-save; missing ResourceToken field');
	}
	$Content=base64_encode(serialize(stripslashes_deep($_POST)));
	$Session=base64_encode(serialize(stripslashes_deep($_SESSION)));
	if($Autosave_ID=q("SELECT ID FROM system_autosave WHERE UserName='".sun()."' AND ResourceToken='$ResourceToken'", O_VALUE, C_MASTER)){
		q("UPDATE system_autosave SET
		Saved='0',
		Content='$Content',
		Mode='$mode',
		Session='$Session' WHERE ID=$Autosave_ID", C_MASTER);
	}else{
		$Autosave_ID=q("INSERT INTO system_autosave SET
		Saved='0',
		CreateDate=NOW(),
		UserName='".sun()."',
		ResourceToken='$ResourceToken',
		Mode='$mode',
		Content='$Content',
		Session='$Session'",O_INSERTID, C_MASTER);
	}
	prn($qr);
	?><script language="javascript" type="text/javascript">
	window.parent.status='Document successfully auto-saved';
	</script><?php
	$assumeErrorState=false;
	exit;
}else if(isset($autosave) && !$suppressPrintEnv){
	?><script language="javascript" type="text/javascript">
	window.parent.autosaveTimelapse=0;
	window.parent.formstate=window.parent.getformstate();
	</script><?php
}
*/

//handle blank fills if present
if($blankFills)
foreach($blankFills as $n=>$v){
	$v= str_replace('(','\(',
		str_replace(')','\)',
		str_replace('/','\/',$v)));
	if(isset($_POST[$n]))$$n=$_POST[$n]=preg_replace('/^'.$v.'$/','',$_POST[$n]);
}
if(!$suppressPrintEnv){
	if(!empty($_GET))prn($_GET);
	if(!empty($_POST))prn($_POST);
}
//found at end of switch-case
$navigate=false; 
switch(true){
	/* root privileges */
	case $mode=='rootManageRoles':
		//2010-11-20
		require($COMPONENT_ROOT.'/comp_00_roles.php');
		?><script language="javascript" type="text/javascript">
		window.parent.g('roles').innerHTML=document.getElementById('roles').innerHTML;
		</script><?php
	break;
	case $mode=='insertClient':
	case $mode=='updateClient':
	case $mode=='deleteClient':
		if($mode=='deleteClient'){
			error_alert('undeveloped');
		}
		//error checking and value conversion
		if($mode==$insertMode){
			if(!$FirstName || $LastName)error_alert('First and last name are required.  Middle name is helpful but optional');
		}
		if($mode==$insertMode){
			if(!$Password)$Password=substr(md5(time().rand(1,100000)),0,6);
			//get contact username
			if($Contacts_ID){
				//OK - but not developed right now
			}else{
				$UserName=sql_autoinc_text('_v_usernames','UserName',array($FirstName,$LastName));
				
				$Contacts_ID=q("INSERT INTO addr_contacts SET
				FirstName='$FirstName',
				MiddleName='$MiddleName',
				LastName='$LastName',
				Category='$Category',
				UserName='$UserName',
				Password='$Password',
				PasswordMD5='".md5($Password)."',
				Email='$Email',
				HomeMobile='$HomeMobile',
				CreateDate=NOW(),
				Creator='".sun()."'", O_INSERTID);
				
				q("INSERT INTO bais_universal SET un_username='$UserName', un_firstname='$FirstName', un_middlename='$MiddleName', un_lastname='$LastName', un_email='$Email', un_password='".md5($Password)."', un_creator='".sun()."', un_createdate=NOW()");
			}
			
			//get client username
			if(!$UserName)$UserName=sql_autoinc_text('_v_usernames','UserName',$ClientName);
			
			//fulfill the client record
			q("UPDATE finan_clients SET
			ResourceType=1,
			PrimaryFirstName='$FirstName',
			PrimaryMiddleName='$MiddleName',
			PrimaryLastName='$LastName',
			UserName='$UserName',
			PasswordMD5='".md5($Password)."',
			Email='$Email',
			WebPage='$WebPage',
			CompanyName='$ClientName',
			ClientName='$ClientName',
			Address1='$Address1',
			City='$City',
			State='$State',
			Zip='$Zip',
			Country='$Country',
			EditDate=NOW(),
			Editor='".sun()."'
			WHERE ID='$ID'", O_INSERTID);
			prn($qr);
			
			q("INSERT INTO finan_ClientsContacts SET
			Clients_ID='$ID',
			Contacts_ID='$Contacts_ID',
			Type='Primary',
			/*Position='$Position'*/
			Notes='Added by exe page line ".__LINE__."'");
			prn($qr);
			if(!$skipPropertyInfo){
				//create the property records
				$Properties_ID=q("INSERT INTO gl_properties SET
				Clients_ID='$ID',
				Type='$Type',
				SendCommission='$SendCommission',
				EscortCommission='$EscortCommission',
				PropertyName='$PropertyName',
				PropertyAddress='$PropertyAddress',
				PropertyCity='$PropertyCity',
				PropertyState='$PropertyState',
				PropertyZip='$PropertyZip',
				CreateDate=NOW(),
				Creator='".sun()."'", O_INSERTID);
				prn($qr);
				
				//set the first units
				$Units_ID=q("INSERT INTO gl_properties_units SET
				Properties_ID='$Properties_ID',
				Quantity='$Quantity',
				Rent='$Rent',
				LeaseDesired='$LeaseDesired',
				LeaseAllowed='$LeaseAllowed',
				Bedrooms='$Bedrooms',
				Bathrooms='$Bathrooms',
				SquareFeet='$SquareFeet',
				OtherAmenities='$OtherAmenities',
				CreateDate=NOW(),
				Creator='".sun()."'", O_INSERTID);
				prn($qr);
			}
		}else{
			$sql=sql_insert_update_generic($MASTER_DATABASE,'finan_clients','UPDATE');
			q($sql);
		}
		$navigate=true;
		$navigateCount=$count+($mode==$insertMode?1:0);
	break;
	case $mode=='insertProperty':
	case $mode=='updateProperty':
	case $mode=='deleteProperty':

		//field conversion
		$Active=(!$Inactive ? 1 : 0);
		if(!$Active && !trim($InactiveReason))error_alert('If you set a property inactive, you MUST specify a reason; remember that this property will no longer appear listed on searches');
		if(!$PetsAllowed)$PetsAllowed=0;
		if(!$GameRoom)$GameRoom=0;
		if(!$FitnessCenter)$FitnessCenter=0;
		if(!$BusinessCenter)$BusinessCenter=0;
		if(!$Basketball)$Basketball=0;
		if(!$Volleyball)$Volleyball=0;
		if(!$Pool)$Pool=0;
		if(!$HotTub)$HotTub=0;
		if(!$SecurityGates)$SecurityGates=0;
		if(!$PhonePaid)$PhonePaid=0;
		if(!$InternetPaid)$InternetPaid=0;
		if(!$Cable)$Cable=0;
		if(!$Gas)$Gas=0;
		if(!$ElectricPaid)$ElectricPaid=0;
		if(!$TrashPaid)$TrashPaid=0;

		$ResourceType=1;
		if($SendCommission_RBADDNEW){
			if(!preg_match('/^[0-9.]+$/',$SendCommission_RBADDNEW))error_alert('You specified a dollar amount for the send commission.  Make sure you specify a valid number');
			$SendCommission=$SendCommission_RBADDNEW;
		}else if(!$SendCommission)error_alert('Enter a specific send commission or amount for this rental');
		if($EscortCommission_RBADDNEW){
			if(!preg_match('/^[0-9.]+$/',$EscortCommission_RBADDNEW))error_alert('You specified a dollar amount for the escort commission.  Make sure you specify a valid number');
			$EscortCommission=$EscortCommission_RBADDNEW;
		}else if(!$EscortCommission)error_alert('Enter a specific escort commission or amount for this rental');
		
		if(in_array(2,$GLF_BillingMethod) && !valid_email($GLF_BillingEmail))error_alert('If you include billing method as email, you must include a valid email address');
		if(in_array(4,$GLF_BillingMethod) && !trim($GLF_BillingFax))error_alert('If you include billing method as fax, you must include a valid fax number');
		$GLF_BillingMethod=array_sum($GLF_BillingMethod);
		if(!$GLF_BillingMethod)error_alert('Select at least one billing method');
		
		if(strtolower($Type)=='apt'){
			//error checking
			if(strlen($PropertyZip)<5)error_alert('The Zip Code for this property is invalid');
			
			//duplicate checking
			
			$sql=sql_insert_update_generic($MASTER_DATABASE,'gl_properties','UPDATE');
			q($sql);
		}else{
			//for now I am assuming this is an SFR
			
			//this info goes in at the client level
			if($Clients_ID!='{RBADDNEW}'){
				q("UPDATE finan_clients SET
				GLF_BillingMethod=$GLF_BillingMethod,
				GLF_BillingEmail='$GLF_BillingEmail',
				GLF_BillingFax='$GLF_BillingFax'
				WHERE ID=$Clients_ID");
			}
			if($mode==$insertMode){
				$FirstName=$PrimaryFirstName;
				$LastName=$PrimaryLastName;
				$Company=$CompanyName;
				$ClientName=$CompanyName;
				$Email=$BillingEmail;
				$Phone=$BillingPhone;
				$Mobile=$BillingMobilePhone;
				$Fax=$BillingFax;
				$Address1=$BillingAddress;
				$ShippingAddress1=$BillingAddress;
				$City=$BillingCity;
				$State=$BillingState;
				$Zip=$BillingZip;
				$Country=$BillingCountry;
				$ShippingCity=$City;
				$ShippingState=$State;
				$ShippingZip=$Zip;
				$ShippingCountry=$Country;	
				$PropertyName=$CompanyName;
				
				$sql=sql_insert_update_generic('cpm180_'.$GCUserName,'gl_properties','INSERT');
				$Properties_ID=$ID=q($sql,O_INSERTID);
				prn($qr);

				$Name=$PropertyName;
				$Quantity=1;
				$buffer=$ID;
				unset($ID);
				$sql=sql_insert_update_generic('cpm180_'.$GCUserName,'gl_properties_units','INSERT');
				$Units_ID=q($sql,O_INSERTID);
				$ID=$buffer;
				prn($qr);
			}else{
				$sql=sql_insert_update_generic('cpm180_'.$GCUserName,'gl_properties','UPDATE');
				q($sql);
				prn($qr);
				$ID=$Units_ID;
				$sql=sql_insert_update_generic('cpm180_'.$GCUserName,'gl_properties_units','UPDATE');
				q($sql);
				prn($qr);
			}
		}
		if($Clients_ID=='{RBADDNEW}'){
			//error checking for client entry - I would like this to be another part of the exe page vs. this
			$UserName=sql_autoinc_text('_v_usernames','UserName',array($PrimaryFirstName, $PrimaryLastName));
			$Password=rand(100,999).'-'.rand(100,999);
			$PasswordMD5=md5($Password);
			$ClientName=sql_autoinc_text('finan_clients','ClientName',$CompanyName,array(
				'leftSep'=>'(',
				'rightSep'=>')',
				'returnLowerCase'=>false,
			));
			$Clients_ID=q("INSERT INTO finan_clients SET
			SessionKey='".$PHPSESSID."',
			ResourceToken='".($ResourceToken?$ResourceToken:'undefined line '.__LINE__)."',
			PrimaryFirstName='$PrimaryFirstName',
			PrimaryLastName='$PrimaryLastName',
			CompanyName='$CompanyName',
			ClientName='$ClientName',
			ResourceType=1,
			Address1='$BillingAddress',
			City='$BillingCity',
			State='$BillingState',
			Zip='$BillingZip',
			Country='$BillingCountry',
			UserName='$UserName',
			PasswordMD5='$PasswordMD5',
			Email='$BillingEmail',
			Fax='$BillingFax',
			Phone='$BillingPhone',
			Mobile='$BillingMobilePhone',
			Category='$Category',
			Notes='$finan_clientsNotes',
			GLF_OwnerTaxID='$GLF_OwnerTaxID',".
			(strtolower($Type)!='apt' ?
			"GLF_BillingMethod=$GLF_BillingMethod,
			 GLF_BillingEmail='$GLF_BillingEmail',
			 GLF_BillingFax='$GLF_BillingFax',"			 
			: '')
			."CreateDate=NOW(),
			Creator='".sun()."'", O_INSERTID);
			prn($qr);
			
			$Contacts_ID=q("INSERT INTO addr_contacts SET
			UserName='$UserName',
			Password='$Password',
			PasswordMD5='$PasswordMD5',
			Category='$Category',
			BusAddress='$BillingAddress',
			BusCity='$BillingCity',
			BusState='$BillingState',
			BusZip='$BillingZip',
			HomeMobile='$BillingMobilePhone',
			BusFax='$BillingFax',
			BusPhone='$BillingPhone',
			FirstName='$PrimaryFirstName',
			LastName='$PrimaryLastName',
			Email='$BillingEmail',
			Company='$CompanyName',
			CreateDate=NOW(),
			Creator='".sun()."'", O_INSERTID);
			prn($qr);
			
			q("INSERT INTO finan_ClientsContacts SET
			Clients_ID=$Clients_ID,
			Contacts_ID=$Contacts_ID,
			Type='Primary',
			Notes='added by exe page line ".__LINE__."'");
			
			q("UPDATE gl_properties SET Clients_ID=$Clients_ID WHERE ID=$ID");
			
			q("INSERT INTO bais_universal SET un_username='$UserName', un_firstname='$PrimaryFirstName', un_middlename='$PrimaryMiddleName', un_lastname='$PrimaryLastName', un_email='$BillingEmail', un_password='".md5($Password)."', un_creator='".sun()."', un_createdate=NOW()");
		}
		if($mode==$insertMode){
			require($COMPONENT_ROOT.'/comp_m1000.php');
			?><script language="javascript" type="text/javascript">
			try{
			window.parent.g('propertyClientList').innerHTML=document.getElementById('propertyClientList').innerHTML;
			}catche(e){ }
			</script><?php
		}
		if($navMode=='insert'){
			//clear the components
			$refreshComponentOnly=true;
			$ResourceToken=substr(date('YmdHis').rand(10000,99999),3,16);
			$ID=quasi_resource_generic($MASTER_DATABASE, 'gl_properties', $ResourceToken);
			require($COMPONENT_ROOT.'/comp_290_units_table_v100.php');
			?><script language="javascript" type="text/javascript">
			try{
			window.parent.g('ID').value=<?php echo $ID?>;
			}catch(e){ }
			try{
			window.parent.g('ResourceToken').value=<?php echo $ResourceToken?>;
			window.parent.ResourceToken=<?php echo $ResourceToken;?>;
			}catch(e){ }
			try{
			window.parent.g('UnitInventory').innerHTML=document.getElementById('UnitInventory').innerHTML;
			}catche(e){ }
			</script><?php
		}
		if($cbPresent){
			if($cbSelect=='Units_ID'){
				$cbValue='fixed:'.$Units_ID;
				$cbLabel=stripslashes("$PropertyName $PropertyAddress - $Bedrooms/$Bathrooms, $SquareFeet sqft, $".number_format($Rent,2));
			}
			callback(array("useTryCatch"=>false));
		}
		
		$navigate=true;
		$navigateCount=$count+1;
	break;
	case $mode=='manageUnitInventory':
		$refreshComponentOnly=true;
		if($submode=='insertObject')$Properties_ID=$ID;
		require('../components/comp_290_units_table_v100.php');
		?><script language="javascript" type="text/javascript">
		window.parent.g('UnitInventory').innerHTML=document.getElementById('UnitInventory').innerHTML;
		</script><?php
	break;
	case $mode=='refreshComponent':
		if($submode=='bulkUpdate'){
			foreach($dChge as $ID=>$v){
				if(!$v)continue;
				//ec
				q("UPDATE gl_leases l, gl_LeasesTransactions lt, finan_transactions t1, finan_headers h, finan_transactions t2  SET 
				l.UnitNumber='".$formData[$ID]['UnitNumber']."',
				l.Rent='".$formData[$ID]['Rent']."',
				l.LeaseStartDate='".date('Y-m-d',strtotime($formData[$ID]['LeaseStartDate']))."',
				t2.UnitPrice= IF(t2.UnitPrice < 0, ".$formData[$ID]['OriginalTotal']." * -1, ".$formData[$ID]['OriginalTotal']."),
				t2.Extension= IF(t2.Extension < 0, ".$formData[$ID]['OriginalTotal']." * -1, ".$formData[$ID]['OriginalTotal']."),
				t2.Editor='".sun()."', 
				l.Editor='".sun()."'
				WHERE
				l.ID=lt.Leases_ID AND
				lt.Transactions_ID=t1.ID AND
				t1.Headers_ID=h.ID AND
				h.ID=t2.Headers_ID AND
				l.ID=$ID");
				prn($qr);
			}
			?><script language="javascript" type="text/javascript">
			window.parent.g('bulkUpdate').disabled=true;
			alert('Lease information updated');
			</script><?php
			break;
		}
		if(!$component){
			mail($developerEmail,'refreshComponent() called, permissions denied',get_globals(), $fromHdrBugs);
			error_alert('Component variable not passed');
		}
		//NOTE 2010-08-01: array registeredComponents now moved to auth file
		if(strstr($component,':')){
			$a=explode(':',$component);
			if(md5($a[1].$MASTER_PASSWORD)!=$a[2])error_alert('Improper key passage for dynamic file component call');
			//2012-02-07: the component has self-validated; create this node on the fly
			if(strstr($a[1],'/')){
				$b=explode('/',$a[1]);
				$c=array_pop($b);
				if(count($c)==1 && strlen($GLOBALS[$c[0]])){
					$registeredComponents[$a[0]]=$GLOBALS[$c[0]].'/'.$c;
				}else{
					$registeredComponents[$a[0]]=implode('/',$b).'/'.$c;
				}
			}else{
				$registeredComponents[$a[0]]=$COMPONENT_ROOT.'/'.$a[1];
			}
			//this relates output (div id) to the component
			$component=$a[0];
		}else if($componentFile && $componentKey){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/gf5/console/components/'.$componentFile)){
				error_alert('Pardon me but I am unable to locate the file: "'.$componentFile.'"\n\nFrom: '.__FILE__.'\n\nPlease correct the value in the component file itself');
			}
		}else if(!$registeredComponents[$component]){
			error_alert('Pardon me but I am unable to locate the component "'.$component.'" on the server.\n\nPlease go to '.__FILE__.' and make sure that $registeredComponents[\\\''.$component.'\\\'] has a value declared');
			if(!file_exists($registeredComponents[$component])){
				error_alert('Pardon me but I am unable to locate the file: "'.$registeredComponents[$component].'"\n\nFrom: '.__FILE__.'\n\nPlease correct the value for $registeredComponents[\\\''.$component.'\\\'] to solve this problem');
			}
		}


		//permission checks

		$refreshComponentOnly=true;
		if($componentFile){
			require($_SERVER['DOCUMENT_ROOT'].'/gf5/console/components/'.$componentFile);
		}else{
			require($registeredComponents[$component]);
		}

		if($submode=='exportDataset'){
			//output CSV
			$assumeErrorState=false;
			$suppressNormalIframeShutdownJS=true;
			if($exportAsExcel){
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$exportFileName.'"');
				header('Cache-Control: max-age=0');
			}else{
				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="'.$component.'['.count($records).']-@'.date('Y-m-d_H-i-s').'.csv"');
			}
			echo $datasetOutput;
			exit;
		}else{
			?><script language="javascript" type="text/javascript">
			/*
			NOTE: the unset does not appear to be working when items have been deleted
			*/
			try{ var scrollY=window.parent.g('<?php echo $component?>_tbody').scrollTop; }catch(e){ }
			window.parent.g('<?php echo $component?>').innerHTML=document.getElementById('<?php echo $component?>').innerHTML;
			//attempt to reconstitute selected items for the group - store locally and unset existing since some may have been deleted
			var a=window.parent.hl_grp['<?php echo $component?>'];
			window.parent.hl_grp['<?php echo $component?>']=new Array();
			for(var j in a){
				try{ window.parent.g(j).onclick();	}catch(e){ }
			}
			try{ if(scrollY)window.parent.g('<?php echo $component?>_tbody').scrollTop=scrollY; }catch(e){ }
			</script><?php
		}
	break;
	case $mode=='rentalSearch':
		require('../components/comp_90_searchform_v100.php');
	break;
	case $mode=='deleteSearch':
		q("DELETE FROM gl_searches WHERE ID=$Searches_ID AND Creator='".sun()."'");
		?><script language="javascript" type="text/javascript">
		window.parent.g('s_<?php echo $Searches_ID;?>').style.display='none';
		</script><?php
	break;

	case $mode=='listBuilder':
		$letters = preg_replace("/[^a-z0-9 ]/si","",$letters);
		if($submode=='getContactsByLetters'){
			if($res=q("SELECT 
				ID, 
				TRIM(CONCAT(FirstName,' ',LastName)),
				IF(HomeCity!='',HomeCity,' '),
				IF(HomeState!='',HomeState,' '),
				IF(HomeMobile!='',HomeMobile,IF(HomePhone!='',HomePhone,' ')) 
				FROM addr_contacts WHERE LastName LIKE '".$letters."%' OR FirstName LIKE '".$letters."%' ORDER BY LastName, FirstName", O_ARRAY)){
				foreach($res as $r)echo implode('###',$r).'|';
			}
		}
		$assumeErrorState=false;
		$suppressNormalIframeShutdownJS=true;
		exit;
	break;
	case $mode=='QuickAddContact':
		$Password=rand(100,999).'-'.rand(100,999);
		$Contacts_ID=q("INSERT INTO addr_contacts SET
		UserName='".sql_autoinc_text('_v_usernames','UserName',array($FirstName,$LastName))."',
		Password='$Password',
		PasswordMD5=MD5('$Password'),
		Category='Rental Tenant',
		FirstName='$FirstName',
		MiddleName='$MiddleName',
		LastName='$LastName',
		Email='$Email',
		HomeMobile='$HomeMobile',
		HomePhone='$HomePhone',
		HomeAddress='$HomeAddress',
		HomeCity='$HomeCity',
		HomeState='$HomeState',
		HomeZip='$HomeZip',
		CreateDate=NOW(),
		Creator='".sun()."'",O_INSERTID);
		if($submode=='populateContactList'){
			?><script language="javascript" type="text/javascript">
			window.parent.contactSetValue('','',<?php echo $Contacts_ID?>, '<?php echo str_replace("'","\'",stripslashes($FirstName. ' '.($MiddleName?substr($MiddleName,0,1).'. ':''). $LastName))?>');
			window.parent.contactAddCancel();
			
			</script><?php
		}
	break;
	case $mode=='insertLease':
	case $mode=='updateLease':
	case $mode=='deleteLease':
		$Units_ID=str_replace('fixed:','',$Units_ID);
		/*
		delete lease
			no payments
			no agett check on
			error alert message
			void an invoice
		*/

		if(strlen($LeaseStartDate)<6 || strtotime($LeaseStartDate)==false)error_alert('You must specify a valid move-in date; this is the start date of the lease');

		if($mode==$updateMode && (q("SELECT HeaderStatus FROM finan_headers WHERE ID=$ID", O_VALUE)=='Void'))error_alert('You cannot edit a voided invoice.  Hit Ctrl-Alt-V to unvoid it first');
		
		if($mode=='deleteLease'){
			error_alert('not developed');
		}
		if(!$ToBeBilled)$ToBeBilled='0';
		$Rent=preg_replace('/[^.0-9]*/','',$Rent);
		$Extension=preg_replace('/[^.0-9]*/','',$Extension);

		$Verification_ID=implode(',',$Verification_ID);
		
		if($mode==$updateMode){
			$invoice=q("SELECT h.* FROM finan_headers h, finan_transactions t, gl_LeasesTransactions lt
			WHERE h.ID=t.Headers_ID AND t.ID=lt.Transactions_ID AND lt.Leases_ID=$ID GROUP BY h.ID", O_ROW);
		}
		//insert the lease - we take a static snapshot of what the commission was at that time
		extract(q("SELECT EscortCommission, SendCommission FROM gl_properties a, gl_properties_units b WHERE a.ID=b.Properties_ID AND b.ID=$Units_ID", O_ROW));
		$Extension=preg_replace('/[^.0-9]+/','',$Extension);
		if(!preg_match('/^[1-9][0-9]+(\.[0-9]{0,2})*$/',$Extension))error_alert('Please enter a valid amount of the invoice');
		if($Escort>1){
			if(!t($EscortDate))error_alert('You must specify an escort date');
		}

		if(preg_match('/tba|tbd/i',$UnitNumber))error_alert('Please do not use "TBA" or "TBD" for the unit number; if you do not know the unit number, please research it or leave it blank');

		//gift cards
		if(trim($GCData1Agent))$GCData1['Agent']=$GCData1Agent;
		if(trim($GCData1Business))$GCData1['Business']=$GCData1Business;
		if(trim($GCData1Organization))$GCData1['Organization']=$GCData1Organization;
		if(trim($GCData2Agent))$GCData2['Agent']=$GCData2Agent;
		if(trim($GCData2Business))$GCData2['Business']=$GCData2Business;
		if(trim($GCData2Organization))$GCData2['Organization']=$GCData2Organization;
		if(!trim($GCData1['Name']))unset($GCData1['Name']);
		if(!trim($GCData1['Phone']))unset($GCData1['Phone']);
		if(!trim($GCData1['Email']))unset($GCData1['Email']);
		if(!trim($GCData2['Name']))unset($GCData2['Name']);
		if(!trim($GCData2['Phone']))unset($GCData2['Phone']);
		if(!trim($GCData2['Email']))unset($GCData2['Email']);

		if($GiftCard1 && (!$GCData1['Name'] || !($GCData1['Phone'] || $GCData1['Email'])))error_alert('For gift card 1, you must at least specify a name, and either a phone or email address');
		if($GiftCard2 && (!$GCData2['Name'] || !($GCData2['Phone'] || $GCData2['Email'])))error_alert('For gift card 2, you must at least specify a name, and either a phone or email address');
		
		$GCData1=($GiftCard1 && !empty($GCData1) ? addslashes(serialize(stripslashes_deep($GCData1))) : '');
		$GCData2=($GiftCard2 && !empty($GCData2) ? addslashes(serialize(stripslashes_deep($GCData2))) : '');

		if($n=$GCData1Agent_RBADDNEW)q("REPLACE INTO aux_gl_agent SET Name='$n'");
		if($n=$GCData1Business_RBADDNEW)q("REPLACE INTO aux_gl_business SET Name='$n'");
		if($n=$GCData1Organization_RBADDNEW)q("REPLACE INTO aux_gl_organization SET Name='$n'");
		if($n=$GCData2Agent_RBADDNEW)q("REPLACE INTO aux_gl_agent SET Name='$n'");
		if($n=$GCData2Business_RBADDNEW)q("REPLACE INTO aux_gl_business SET Name='$n'");
		if($n=$GCData2Organization_RBADDNEW)q("REPLACE INTO aux_gl_organization SET Name='$n'");

		//better error checking on this
		$SubSplit=(	$SubSplit_RBADDNEW ? $SubSplit_RBADDNEW : $SubSplit );
		$SubSplit=preg_replace('/[^.0-9]+/','',$SubSplit);
		if(strlen($SubSplit) && !preg_match('/^[1-9][0-9]+(\.[0-9]{0,5})*$/',$SubSplit*100))error_alert('Please enter a valid agent split amount or leave this field blank');

		if(strlen($HeaderNumber) && !preg_match('/^[0-9]+$/',$HeaderNumber))error_alert('Invoice numbers can only be numeric');
		if($eID=q("SELECT ID FROM finan_headers WHERE HeaderType='Invoice' AND Accounts_ID='$InvoiceAccounts_ID' AND HeaderNumber='$HeaderNumber' ".($mode==$updateMode?" AND ID!=".$invoice['ID']:''),O_VALUE))error_alert('That invoice number is already used');
		if(!$IndividualLease)$IndividualLease='0';

		if($mode==$insertMode){
			extract($a=q("SELECT Clients_ID, Bedrooms, Bathrooms, SquareFeet FROM _v_properties_master_list WHERE ID=$Units_ID", O_ROW));
			prn($qr);
			prn($a);
			extract($b=q("SELECT 
			c.CompanyName AS BillingCompany,
			c.Address1 AS BillingAddress,
			c.City AS BillingCity,
			c.State AS BillingState,
			c.Zip AS BillingZip,
			c.Country AS BillingCountry,
			a.Email AS BillingEmail,
			a.LastName AS BillingLastName, 
			a.FirstName AS BillingFirstName
			FROM finan_clients c LEFT JOIN finan_ClientsContacts cc ON c.ID=cc.Clients_ID AND cc.Type='Primary' LEFT JOIN addr_contacts a ON cc.Contacts_ID=a.ID WHERE c.ID='$Clients_ID'", O_ROW));
			prn($b);

			$Leases_ID=q("INSERT INTO gl_leases SET
			Units_ID='$Units_ID',
			Rent='$Rent',
			IndividualLease='$IndividualLease',
			LeaseSignDate='".t($LeaseSignDate)."',
			LeaseStartDate='".t($LeaseStartDate)."',
			LeaseEndDate='".t($LeaseEndDate)."',
			LeaseLength='$LeaseLength',
			Referral='$Referral',
			ReferralOther='$ReferralOther',
			GiftCard1='$GiftCard1',
			GiftCard2='$GiftCard2',
			GCData1='$GCData1',
			GCData2='$GCData2',
			Verification_ID='$Verification_ID',
			VerificationDetails='$VerificationDetails',
			".(strlen($LeaseTerminationDate) && strtotime($LeaseTerminationDate)!=-1 ? "LeaseTerminationDate='".t($LeaseTerminationDate)."',":'')."
			UnitNumber='$UnitNumber',
			Escort='$Escort',
			EscortOther='$EscortOther',
			EscortDate='$EscortDate',
			/* Commission='$Commission', --2011-02-10: I am deprecating this field as it is 1) ambiguous and 2 not really a commission, the field in leases.php is 'Invoice Amount' */ 
			Agents_username='".($Agents_username && minroles()<ROLE_AGENT ? $Agents_username : sun())."',
			SubSplit='$SubSplit',
			SubAgents_username='$SubAgents_username',
			Pets='$Pets',
			PetsDescription='$PetsDescription',
			Comments='$Comments',
			CreateDate=NOW(),
			Creator='".sun()."'", O_INSERTID);
			prn($qr);
			
			//join the contacts
			$Contacts_ID=trim($Contacts_ID,'|');
			$Contacts_ID=explode('|',$Contacts_ID);
			$contactNames=implode(', ',q("SELECT CONCAT(FirstName,' ',LastName) FROM addr_contacts WHERE ID IN(".implode(',',$Contacts_ID).")", O_COL));
			$i=0;
			foreach($Contacts_ID as $id){
				$i++;
				q("INSERT INTO gl_LeasesContacts SET
				Leases_ID='$Leases_ID',
				Contacts_ID='$id',
				StartDate='$LeaseStartDate',
				EndDate='$LeaseEndDate',
				Type='".($i==1?'Primary':'Secondary')."'");
				prn($qr);
			}
			//create the invoice
			/*
			*/
			$Headers_ID=q("INSERT INTO finan_headers SET
			HeaderType='Invoice',
			HeaderDate=".($HeaderDate?"'".t($HeaderDate)."'":'CURDATE()').",
			".($HeaderNumber && !$AutoCreate ? "HeaderNumber='$HeaderNumber', ":'')."
			ResourceType=1,
			ResourceToken='".md5(time.rand(1,1000000))."',
			SessionKey='".$PHPSESSID."',
			Clients_ID=$Clients_ID,
			Accounts_ID='$InvoiceAccounts_ID',
			Classes_ID='$InvoiceClasses_ID',
			Notes='Added by exe page; Rental Locating Invoice',
			CreateDate=NOW(),
			Creator='".sun()."'", O_INSERTID);
			prn($qr);
			if(!$HeaderNumber || $AutoCreate){
				//update the header number as the max+1 of invoice numbers
				$HeaderNumber=q("SELECT MAX(CAST(HeaderNumber AS UNSIGNED)) FROM finan_headers WHERE HeaderType='Invoice' AND Accounts_ID='$InvoiceAccounts_ID'", O_VALUE)+1;
				prn($qr);
				q("UPDATE finan_headers SET HeaderNumber='$HeaderNumber' WHERE ID=$Headers_ID");
				prn($qr);
			}
			q("INSERT INTO finan_invoices SET
			Headers_ID=$Headers_ID,
			Category='Rental Locating Invoice',
			BillingFirstName='".addslashes($BillingFirstName)."',
			BillingLastName='".addslashes($BillingLastName)."',
			BillingCompany='".addslashes($BillingCompany)."',
			BillingAddress='".addslashes($BillingAddress)."',
			BillingCity='".addslashes($BillingCity)."',
			BillingState='".addslashes($BillingState)."',
			BillingZip='".addslashes($BillingZip)."',
			BillingCountry='".addslashes($BillingCountry)."',
			BillingPhone='".addslashes($BillingPhone)."',
			BillingEmail='".addslashes($BillingEmail)."'");
			prn($qr);
			
			//create the transaction
			$thisdescription=addslashes($PropertyAddress)." - $Bedrooms bdrm., $Bathrooms bath, $SquareFeet sqft.; Rental for ".number_format($Rent,2)."; tenants $contactNames; by $Agents_username";
			//header transaction
			$RootTransactions_ID=q("INSERT INTO finan_transactions SET
			Headers_ID=$Headers_ID,
			Accounts_ID=$InvoiceAccounts_ID,
			UnitPrice= ".$Extension.",
			Extension= ".$Extension.",
			CreateDate=NOW(),
			Creator='".sun()."'", O_INSERTID);
			
			$Transactions_ID=q("INSERT INTO finan_transactions SET
			Idx=1,
			Headers_ID=$Headers_ID,
			Items_ID=$RentalLocatingItems_ID,
			Accounts_ID=$RentalLocatingItemAccounts_ID,
			Name='Rental Locating Fee',
			Description='$thisdescription',
			SKU='RLI',
			Quantity=1,
			UnitPrice=-$Extension,
			Extension=-$Extension,
			CreateDate=NOW(),
			Creator='".sun()."'", O_INSERTID);
			prn($qr);
			
			//join lease to transaction
			q("INSERT INTO gl_LeasesTransactions SET
			Leases_ID=$Leases_ID,
			Transactions_ID=$Transactions_ID");
			prn($qr);
			
			error_alert('This is invoice number '.$InvoiceRentalLocatingPrefix.$HeaderNumber.'.  To view the invoice, click on Reporting > Invoices > Current Invoices on the main menu',1);
			
			//send an email to administrator - using GC system
			
			$navMode='kill';
			$navigate=true;
			$count++;
		}else{
			q("UPDATE gl_leases SET
			ToBeBilled=$ToBeBilled,
			LeaseSignDate='".t($LeaseSignDate)."',
			LeaseStartDate='".t($LeaseStartDate)."',
			LeaseEndDate='".t($LeaseEndDate)."',
			LeaseLength='$LeaseLength',
			Rent='$Rent',
			IndividualLease='$IndividualLease',
			".(strlen($LeaseTerminationDate) && strtotime($LeaseTerminationDate)!=-1 ? "LeaseTerminationDate='".t($LeaseTerminationDate)."',":'')."
			Comments='$Comments',
			Escort='$Escort',
			EscortOther='$EscortOther',
			EscortDate='$EscortDate',
			/* Commission='$Commission', */
			Agents_username='".($Agents_username && minroles()<ROLE_AGENT ? $Agents_username : sun())."',
			SubSplit='$SubSplit',
			Referral='$Referral',
			ReferralOther='$ReferralOther',
			GiftCard1='$GiftCard1',
			GiftCard2='$GiftCard2',
			GCData1='$GCData1',
			GCData2='$GCData2',
			Verification_ID='$Verification_ID',
			VerificationDetails='$VerificationDetails',
			".(strlen($LeaseTerminationDate) && strtotime($LeaseTerminationDate)!=-1 ? "LeaseTerminationDate='".t($LeaseTerminationDate)."',":'')."
			UnitNumber='$UnitNumber',
			Escort='$Escort',
			EscortOther='$EscortOther',
			EscortDate='$EscortDate',
			SubAgents_username='$SubAgents_username',
			Pets='$Pets',
			PetsDescription='$PetsDescription',
			EditDate=NOW(),
			Editor='".sun()."'
			WHERE ID='$ID'");
			prn($qr);
			
			//update root transaction and line item
			if($Headers_ID=q("SELECT t.Headers_ID FROM gl_LeasesTransactions lt, finan_transactions t WHERE lt.Transactions_ID=t.ID AND lt.Leases_ID='$ID'", O_VALUE)){
				q("UPDATE finan_headers h, finan_transactions t SET HeaderNumber='$HeaderNumber', Extension='$Extension' WHERE h.ID=t.Headers_ID AND h.Accounts_ID=t.Accounts_ID AND h.ID=$Headers_ID");
				prn($qr);
				q("UPDATE finan_headers h, finan_transactions t SET UnitPrice=-$Extension, Extension=-$Extension WHERE h.ID=t.Headers_ID AND h.Accounts_ID!=t.Accounts_ID AND h.ID=$Headers_ID");
				prn($qr);
			}
			
			//check for distribution changes that are out of range
		}
		if($mode==$updateMode && minroles()<ROLE_AGENT && isset($GiftCardInitialState)){
			switch(true){
				case !$GiftCardInitialState && $GiftCardPaid==1:
					//put in a batch!!
					q("UPDATE gl_leases SET GCBatch1=".q("INSERT INTO gl_batches SET Type='Gift cards',Quantity=1,CreateDate=NOW(),Creator='".sun()."'", O_INSERTID)." WHERE ID=$ID");
					prn($qr);
				break;
				case $GiftCardInitialState && $GiftCardPaid==0:
					//remove from the batch
					q("UPDATE gl_leases SET GCBatch1=0 WHERE ID=$ID");
					prn($qr);
					#leave the quantity the same in the batch even though it is one less
				break;
				case $GiftCardInitialState==0 && $GiftCardPaid==0:
				case $GiftCardInitialState==1 && $GiftCardPaid==1:
					//no action
					//no action
			}
		}
		if($mode==$insertMode){
			mail(
			$AcctBookkeeperEmail.',sfullman@compasspointmedia.com',
			'Invoice '.$HeaderNumber.' submitted',
			"Invoice #$HeaderNumber has been submitted by ".$_SESSION['admin']['firstName']. ' '.$_SESSION['admin']['lastName']."; this is a temporary email and will be replaced with an email with more details and a link to sign in and view the invoice automatically", 
			'From: do_not_reply@sanmarcos.gldatabase.com'
			);
		}
		
		$navigate=true;
		$navigateCount=1;
		
		if($navMode=='insert'){
			?><script language="javascript" type="text/javascript">
			window.parent.location='/gf5/console/leases.php?r=<?php echo rand(1000000,9999999);?>';</script><?php
			$assumeErrorState=false;
			exit;
		}else if($printAfter){
			?><script language="javascript" type="text/javascript">
			window.parent.location='/gf5/console/leases_print.php?Leases_ID=<?php echo $Leases_ID?$Leases_ID:$ID?>';
			</script><?php
			$assumeErrorState=false;
			exit;
		}
	break;
	case $mode=='insertPayment':
	case $mode=='updatePayment':
	case $mode=='deletePayment':
		if($mode=='deletePayment'){
			error_alert('undeveloped');
		}
		//remove commas
		$Amount=str_replace(',','',$Amount);
		foreach($ApplyTo as $n=>$v){
			$ApplyTo[$n]=str_replace(',','',$v);
		}
		if($mode==$insertMode){
			//insert payment header
			if(!$HeaderNumber) error_alert('There is no Check Confirmation Number.');
			if(!strtotime($DateCredited)) error_alert('Invalid Date for the check.');
			if(!is_numeric($Amount) || !is_numeric($Total) || !is_numeric(array_sum($ApplyTo))) error_alert('Make sure all of the amounts are numbers.');
			$Amount=number_format($Amount,2,'.',',');
			$Total=number_format($Total,2,'.',',');
			$Apply=number_format(array_sum($ApplyTo),2,'.',',');
			if($Amount!==$Total || $Total!==$Apply || $Amount!==$Apply) error_alert('The Amount, Total, and Application Fields need to be the same.');
			$Headers_ID=q("INSERT INTO finan_headers
			SET
			HeaderType='Payment',
			HeaderNumber='$HeaderNumber',
			HeaderDate='".t($DateCredited)."',
			ResourceType=1,
			ResourceToken='".md5(time().rand(1,1000000))."',
			SessionKey='".$PHPSESSID."',
			Clients_ID='$Clients_ID',
			Accounts_ID='$UndepositedFundsAccounts_ID',
			Notes='$Notes',
			CreateDate=NOW(),
			Creator='".sun()."'", O_INSERTID);
			prn($qr);
			
			//insert payment entry
			q("INSERT INTO finan_payments SET
			Headers_ID='$Headers_ID',
			Types_ID='$Types_ID'");
			prn($qr);
			
			//insert root transaction
			$RootTransactions_ID=q("INSERT INTO finan_transactions SET
			Headers_ID=$Headers_ID,
			Accounts_ID='$UndepositedFundsAccounts_ID',
			Extension='".$Total."',
			CreateDate=NOW(),
			Creator='".sun()."'", O_INSERTID);
			prn($qr);
			
			foreach($ApplyTo as $Invoices_ID=>$amt){
				if(!$amt)continue;
				$Transactions_ID=q("INSERT INTO finan_transactions SET
				Headers_ID=$Headers_ID,
				Accounts_ID=$InvoiceAccounts_ID,
				Name='Rental Locating Payment',
				Description='Payment ".($Types_ID==1?'Check #':'Cash')." $HeaderNumber',
				Extension='-".number_format($amt,2)."',
				CreateDate=NOW(),
				Creator='".sun()."'", O_INSERTID);
				prn($qr);
				
				if($Adjust[$Invoices_ID]){
					q("UPDATE finan_headers h, finan_transactions t SET
					t.Extension='".number_format($amt,2).",
					WHERE h.ID=t.Headers_ID AND h.Accounts_ID=t.Accounts_ID AND h.ID=$Invoices_ID");
					//flip
					q("UPDATE finan_headers h, finan_transactions t SET
					t.Extension=-".number_format($amt,2).",
					WHERE h.ID=t.Headers_ID AND h.Accounts_ID!=t.Accounts_ID AND h.ID=$Invoices_ID");
				}
				
				//apply the payment to the transaction(s) for this invoice.  For GL there's only one line item
				/* ********************** IMPORTANT NOTE/CONCEPT ****************************
				in quickbooks with invoices for more than one line item, a "short" payment is distributed pro-rata but rounded to them all; so for example for 
				[root] accounts_id=100
				1 accounts_id=25 amt=100
				2 accounts_id=73 amt=200
				3 accounts_id=18 amt=50
				
				a payment of 70.00 would be applied as 20 to 1, 40 to 2 and 10 to 3.  Each "income account" made 1/5th of total amount if considering this on a cash basis
				
				ALWAYS REMEMBER that each transaction will always balance to zero; however (so far) credit attribution is allocated between the non-root entries of transactions in finan_TransactionsTransactions
				
				*****************************************************************************/
				$trid=q("SELECT t.ID FROM finan_headers h, finan_transactions t WHERE 
				h.ID=$Invoices_ID AND 
				h.ID=t.Headers_ID AND
				h.Accounts_ID!=t.Accounts_ID", O_VALUE); //... again, here in Great Locations we are expecting to only find one row
				prn($qr);
				
				q("INSERT INTO finan_TransactionsTransactions SET
				ParentTransactions_ID='$Transactions_ID',
				ChildTransactions_ID='$trid',
				AmountApplied='".number_format($amt,2)."',
				Type='Payment'");
				prn($qr);

			}
			if($navMode=='insert'){
				?><script language="javascript" type="text/javascript">
				window.parent.g('paymentsLineItems').innerHTML='<tr><td colspan="100%"><em>Select a client from the list above</em></td></tr>';
				</script><?php
			}
		}else{
			q("UPDATE finan_headers
			SET
			HeaderNumber='$HeaderNumber',
			HeaderDate='".t($DateCredited)."',
			Notes='$Notes',
			EditDate=NOW(),
			Editor='".sun()."' WHERE ID=$ID");
			prn($qr);
			
			//insert payment entry
			q("UPDATE finan_payments SET
			Types_ID='$Types_ID' WHERE Headers_ID=$ID");
			prn($qr);
			
			//update root transaction
			$RootTransactions_ID=q("UPDATE finan_headers h, finan_transactions t SET
			t.Extension='".$Total."',
			t.EditDate=NOW(),
			t.Editor='".sun()."' WHERE h.ID=t.Headers_ID AND t.Headers_ID=$ID AND h.Accounts_ID=t.Accounts_ID", O_INSERTID);
			prn($qr);

			foreach($ApplyTo as $n=>$v)if(!$v)unset($ApplyTo[$n]);
			$loop=$ApplyTo; //this is the desired application, Invoices -> amount
			//uset unallocated invoices
			if($AppliedTo=q("SELECT t.Headers_ID, tt.AmountApplied
				FROM finan_transactions t, finan_TransactionsTransactions tt, finan_transactions t2
				WHERE t.ID=tt.ChildTransactions_ID AND tt.ParentTransactions_ID=t2.ID AND t2.Headers_ID=$ID", O_COL_ASSOC)){
				foreach($AppliedTo as $n=>$v)$loop[$n]=$v;
			}
			
			//now recurse the combined set
			foreach($loop as $n=>$v){
				if(isset($ApplyTo[$n]) && isset($AppliedTo[$n])){
					//common to both - skip if the amount hasn't changed
					if($ApplyTo[$n]==$AppliedTo[$n])continue;
					//allocate as necessary; simplified based on a one-line transaction
					q("UPDATE 
					finan_transactions t, finan_TransactionsTransactions tt, finan_transactions t2
					SET 
					t.Extension=-".$ApplyTo[$n].",
					AmountApplied='".$ApplyTo[$n]."'
					WHERE t.ID=tt.ParentTransactions_ID AND tt.ChildTransactions_ID=t2.ID AND
					t.Headers_ID=$ID AND
					t2.Headers_ID=$n");
					prn($qr);
				}else if(isset($ApplyTo[$n]) && !isset($AppliedTo[$n])){
					//enter new transaction and allocation
					$Transactions_ID=q("INSERT INTO finan_transactions SET
					Headers_ID=$ID,
					Accounts_ID=$InvoiceAccounts_ID,
					Name='Rental Locating Payment',
					Description='Payment ".($Types_ID==1?'Check #':'Cash')." $HeaderNumber',
					Extension='-".number_format($v,2)."',
					CreateDate=NOW(),
					Creator='".sun()."'", O_INSERTID);
					prn($qr);
					q("INSERT INTO finan_TransactionsTransactions SET ParentTransactions_ID=$Transactions_ID, ChildTransactions_ID='".q("SELECT t.ID FROM finan_transactions t, finan_headers h WHERE h.ID=t.Headers_ID AND h.ID=$n AND h.Accounts_ID!=t.Accounts_ID", O_VALUE)."', AmountApplied='$v'");
					prn($qr);
				}else if(!isset($ApplyTo[$n]) && isset($AppliedTo[$n])){
					//delete this transaction and allocation
					q("DELETE t.*, tt.* FROM
					finan_transactions t, finan_TransactionsTransactions tt, finan_transactions t2
					WHERE t.ID=tt.ParentTransactions_ID AND tt.ChildTransactions_ID=t2.ID AND
					t.Headers_ID=$ID AND t2.Headers_ID=$n");
					prn($qr);
				}
			}
		}
		$navigate=true;
		$navigateCount=$count+($mode==$insertMode?1:0);
	break;
	case $modePassed=='insertDatasetComponent':
	case $modePassed=='updateDatasetComponent':
	case $modePassed=='deleteDatasetComponent':
		$refreshComponentOnly=true;
		require($COMPONENT_ROOT.'/comp_1000_creator_v101.php');
	break;
	case $mode=='search':
		require($COMPONENT_ROOT.'/comp_400_searchkernel_v100.php');
	break;
	case $mode=='uploadPropertyPictures':
		//when did I put this in - this is not used or referenced
		$handle=substr(md5(time().rand(100,10000)),0,5).'_';
		$ext=strtolower(end(explode('.',$_FILES['uploadFile_1']['name'])));
		if(!preg_match('/^(gif|jpg|png|svg)$/',$ext))error_alert('The file is not an allowed extension! The following are allowed: gif|jpg|png|svg');
		if(!is_uploaded_file($_FILES['uploadFile_1']['tmp_name']))error_alert('Abnormal error, unable to upload file');

		$fileFullPath=$_SERVER['DOCUMENT_ROOT'].'/images/'.$GCUserName.'/properties/'.$handle.stripslashes($_FILES['uploadFile_1']['name']);
		if(!move_uploaded_file($_FILES['uploadFile_1']['tmp_name'],$fileFullPath)){
			mail($developerEmail,'error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
			error_alert('Unable to move file to the images folder');
		}
			
		//from hidden field
		$LocalPath=explode('/',$LocalPath);
		$LocalFileName=array_pop($LocalPath);
		$LocalPath=implode('/',$LocalPath);

		//we are putting it in the generic folder documentation/user/LOCS
		$node=tree_build_path('images/'.$GCUserName.'/properties');
		$Tree_ID=q("INSERT INTO relatebase_tree SET 
		Tree_ID=$node,
		Name='$handle".$_FILES['uploadFile_1']['name']."',
		MimeType='".$_FILES['uploadFile_1']['type']."',
		LocalFileName='$LocalFileName',
		CreateDate=NOW(), 
		Creator='".$_SESSION['systemUserName']."'", O_INSERTID);
		
		q("INSERT INTO gl_ObjectsTree SET Tree_ID=$Tree_ID, ObjectName='gl_properties', Objects_ID=$Properties_ID, 
		Description='$Description',
		MimeType='".$_FILES['uploadFile_1']['type']."',
		LocalFileName='$LocalFileName',
		Category='$Category',
		EditDate=NOW()");
		
		//call the component
		$refreshComponentOnly=true;
		require('../components/comp_300_propertyimages.php');
		?><script language="javascript" type="text/javascript">
		try{
		window.parent.opener.g('propertyImages').innerHTML=document.getElementById('propertyImages').innerHTML;
		}catch(e){ }
		//close the parent window - deactivate pending script
		window.parent.close();
		</script><?php
	break;
	case $mode=='updateStaff':
	case $mode=='insertStaff':
	case $mode=='deleteStaff':
		if($mode=='deleteStaff'){
			if(minroles()>ROLE_ADMIN)error_alert('You are not authorized to do this');
			$un_username=$_GET['un_username'];
			q("DELETE FROM bais_StaffOffices WHERE so_stusername='$un_username'");
			q("DELETE FROM bais_OfficesStaff WHERE os_stusername='$un_username'");
			q("DELETE FROM bais_staff WHERE st_unusername='$un_username'");
			q("DELETE FROM bais_universal WHERE un_username='$un_username'");
			?><script language="javascript" type="text/javascript">
			try{
				window.parent.g('r_<?php echo $un_username?>').style.display='none';
			}catch(e){ }
			</script><?php
			$assumeErrorState=false;
			exit;
		}
		$assignorOffices=list_offices('keys');
		if(!count($assignorOffices))error_alert('You do not have permission to add staff');

		if(!$st_active)$st_active='0';
		if(!$st_status)$st_status='0';
		if(!count($roles) /* || !count($offices)*/)error_alert('Select at least one role for this person');
		if(minroles()>=ROLE_AGENT && $un_username!==sun())error_alert('You do not have permission to modify this record (on the Access tab)');
		
		//error checking - type must be other than null, this could actually be complex and we want to give them "second chances" like on some of my other interfaces
		if(!t($st_hiredate, dironal))error_alert('The staff hire date is not a valid date (m/d/y)');
		if(!t($st_dischargedate, dironal))error_alert('The staff discharge date is not a valid date (m/d/y)');

		if($mode==$insertMode){
			//create the username
			$un_username=sql_autoinc_text('bais_universal', 'un_username', array($FirstName, $LastName));
		}
		//new duplicate prevention
		if($mode==$insertMode && trim($FirstName) && trim($LastName)){
			//get all aliases for first name
			if($Name=q("SELECT Name, Alias FROM aux_namealiases WHERE Alias=UCASE('$FirstName')", O_VALUE, $public_cnx)){
				$aliases=q("SELECT Alias FROM aux_namealiases WHERE Name=UCASE('$Name')", O_COL, $public_cnx);
				$aliases=addslashes_deep($aliases);
			}else{
				$aliases[]=$FirstName;
			}
			if($a=q("SELECT a.* FROM bais_universal a, bais_staff s WHERE a.un_username=s.st_unusername AND a.un_firstname IN('".implode("','",$aliases)."') AND (a.un_middlename='$MiddleName' OR a.un_middlename='".substr($MiddleName,0,1)."') AND 
			REPLACE(REPLACE(REPLACE(a.un_lastname,' ',''),'-',''),\"'\",'') = '".preg_replace('/[^a-z]*/i','',$LastName)."' AND a.un_username!='$un_username'", O_ROW)){
				prn($a);
				error_alert('There is already a staff member in the system named '.addslashes($a['un_firstname'].($a['un_middlename'] ? ' '.$a['un_middlename'] : '').' '.$a['un_lastname']).'.  The system will currently not allow two staff members with the same identical name');
			}
		}
		
		/*
		if($mode==$insertMode || (minroles()<=ROLE_ADMIN && q("SELECT sr_permissions FROM bais_StaffRoles WHERE sr_stusername='".sun()."' AND sr_roid=".ROLE_ADMIN." AND (sr_permissions & ".(PERM_ADMINISTRATIVE + PERM_CLERICAL).")>0", O_VALUE))){
			//ssn
			if(!preg_match('/unknown|unassigned|foreign/i',$SocSecurityNumber))$SocSecurityNumber=preg_replace('/[^0-9]* /','',$SocSecurityNumber);
			if(!preg_match('/^([0-9]{3})([0-9]{2})([0-9]{4})$/',$SocSecurityNumber,$a) && !preg_match('/unknown|unassigned|foreign/i',$SocSecurityNumber))error_alert('A social security number is required (format 000-00-0000).  Or you may any one of the following keywords: "unknown", "unassigned", or "foreign"');
			if(!preg_match('/unknown|unassigned|foreign/i',$SocSecurityNumber))$SocSecurityNumber=$a[1].'-'.$a[2].'-'.$a[3];
			//dob
			if(!t($BirthDate,dironal))error_alert('Date of birth is not valid; make sure it is a valid date, or leave it blank');
		}else{
			//not required right now
		}
		*/
		
		if(rand(1,50)==25)mail($developerEmail, 'Notice '.__FILE__.', line '.__LINE__,get_globals('(THIS IS FOR mapsahead) A staff entry or update was made.  Currently the Access Tab, with its combo of permissions and offices, is ambiguous.  You could be a RD over office 1 but a PD over office 2 exclusively.  The conflicts are between RD, PD, and CM:
		1. RD & AD
		2. RD & CM
		3. AD & CM
		or, any combo of CM *AND* the top/bottoms, or any combo of the split tops'),$doNotReplyEmail);
		
		$fl=__FILE__; $ln=__LINE__;
		q(($mode==$insertMode ? "INSERT INTO":"UPDATE")." bais_universal SET
		un_username='$un_username',
		".(
		$mode==$insertMode ? 
		($temporaryPassword ? "un_passwordtmp='$un_password'," :
		"un_password='".md5(stripslashes($un_password))."'," ): 
		''
		)."
		".($mode==$insertMode?"un_createdate='$dateStamp',":'')."
		".($mode==$insertMode?"un_creator='".$_SESSION['systemUserName']."',":'')."
		un_firstname='$FirstName',
		un_middlename='$MiddleName',
		un_lastname='$LastName',
		un_email='$Email'
		" . ($mode==$updateMode?" WHERE un_username='$un_username'":""));
		prn($qr);
		
		//build permissions from top down
		q(($mode==$insertMode ? "INSERT INTO":"UPDATE")." bais_staff SET
		st_unusername='$un_username',
		st_status='$st_status',
		st_active='$st_active',
		st_hiredate='$st_hiredate',
		st_dischargedate='$st_dischargedate',
		st_dischargereason='$st_dischargereason',
		Address='$Address',
		City='$City',
		State='$State',
		Zip='$Zip',
		Country='$Country',
		Phone='$Phone',
		WorkPhone='$WorkPhone',
		PagerVoice='$PagerVoice',
		GLF_Recruiter='$GLF_Recruiter', 
		GLF_TransactionFee='$GLF_TransactionFee', 
		GLF_EOFee='$GLF_EOFee',
		Cell='$Cell',
		".(isset($BirthDate) ? "BirthDate='$BirthDate'," : '')."
		".(isset($BirthDate) ? "SocSecurityNumber='$SocSecurityNumber'," : '')."
		".(isset($Gender) ? "Gender='$Gender'," : '')."
		".(isset($Race) ? "Race='$Race'," : '')."
		MisctextStaffnotes='$MisctextStaffnotes'	
		"
		.($mode==$insertMode ? ",st_createdate='$dateStamp'":"")
		.($mode==$insertMode ? ",st_creator='".$_SESSION['systemUserName']."'":"")
		.($mode==$updateMode ? ",st_editor='".$_SESSION['systemUserName']."'":"")
		.($mode==$updateMode?" WHERE st_unusername='$un_username'":""));
		prn($qr);

		//--------------------------------------------------------------------------------------
		$staffRoles=array(ROLE_ADMIN, ROLE_MANAGER, ROLE_AGENT);
		$ambiguousRoles=array(ROLE_MANAGER, ROLE_AGENT);
		$ambiguous=0;
		if($mode==$updateMode) $thisStaffRoles=q("SELECT REPLACE(sr_roid,'.0','') FROM bais_StaffRoles WHERE sr_stusername='".$un_username."'",O_COL);
		prn('passed roles');
		prn($roles);
		foreach($userType as $key=>$name){
			//exclude non-staff roles
			if(!in_array($key,$staffRoles))continue;
			//exclude levels above the level of the assignor
			if(minroles()>$key)continue;
			//count presence of ambiguous roles
			if(in_array($key,$ambiguousRoles))$ambiguous++;

			if($roles[$key]){
				if(q("SELECT a.*, REPLACE(sr_roid,'.0','') AS sr_roid FROM bais_StaffRoles a WHERE sr_stusername='$un_username' AND sr_roid='$key'", O_ROW)){
					//OK
				}else{
					$ln=__LINE__+1;
					q("INSERT INTO bais_StaffRoles SET sr_stusername='$un_username', sr_roid='$key', sr_assignor='".sun()."'");
					prn($qr);
				}
			}else{
				$ln=__LINE__+1;
				q("DELETE FROM bais_StaffRoles WHERE sr_stusername='$un_username' AND sr_roid='$key'");
				prn($qr);
			}
		}
		/*
		//now get count of ambiguous roles
		$roles=q("SELECT REPLACE(sr_roid,'.0','') FROM bais_StaffRoles WHERE sr_stusername='$un_username' AND sr_roid IN(".implode(',',$ambiguousRoles).")", O_COL);
		if(count($roles)>1){
			mail($developerEmail, 'Notice file '.__FILE__.', line '.__LINE__,get_globals('This is where a resolution email should be sent, OR better yet, they can only check one of the three ambiguous roles'),$fromHdrBugs);
			foreach($roles as $v)$roleString.=' '.$userType[$v].',';
			$roleString=trim(rtrim($roleString,','));
			$roleString=preg_replace('/, ([^,]+)/',' and $1',$roleString);
			$msg='Due to the fact that you selected '.count($roles).' roles for this staff member ('.
			$roleString	
			.'), '.
			($mode==$insertMode ? 'the staff was not assigned to any of the offices you selected' : 'any office changes you selected were not made')
			.'. To select offices for this staff member, select Staff > List Staff from the main menu, then select Assign Staff to Offices';
			error_alert(
				$msg,
				true
			);
			mail($_SESSION['email'],'Great Locations notice: assign staff to offices',$msg,$doNotReplyEmail);
		}else{
			$ln=__LINE__+1;
			q("DELETE FROM bais_OfficesStaff WHERE os_unusername IN('".implode("','",array_keys($assignorOffices))."') AND os_stusername='$un_username'");
			prn($qr);

			$ln=__LINE__+1;
			q("DELETE FROM bais_StaffOffices WHERE so_unusername IN('".implode("','",array_keys($assignorOffices))."') AND so_stusername='$un_username'");
			prn($qr);
			foreach($assignorOffices as $office=>$name){
				if($offices[$office]){
					if($roles[ROLE_AGENT]){
						/* bug 2010-05-31: not reading roles[ROLE_AGENT] * /
						$ln=__LINE__+1;
						q("REPLACE INTO bais_OfficesStaff SET os_unusername='$office', os_stusername='$un_username'");
						prn($qr);
					}else{
						$ln=__LINE__+1;
						q("REPLACE INTO bais_StaffOffices SET so_unusername='$office', so_stusername='$un_username'");
						prn($qr);
					}
				}
			}			
		}
		*/
		if($ProfileTree_ID){
			if($mode==$insertMode){
				if($ProfileTree_ID!= -1){
					//insert gallery
					$Gallery_ID=q("INSERT INTO gf_objects SET ParentObject='bais_staff', Objects_ID='$un_username', Relationship='Photo Gallery', Notes='Created or modified with staff record',CreateDate=NOW(), Creator='".sun()."'", O_INSERTID);
					prn($qr);
					//insert profile picture
					$ProfilePicture_ID=q("INSERT INTO gf_objects SET ParentObject='gf_objects', Objects_ID='$Gallery_ID', Relationship='Profile Picture Default', Notes='Created or modified with staff record', CreateDate=NOW(), Creator='".sun()."'");
					prn($qr);
					$path=tree_id_to_path($ProfileTree_ID);
					if(stristr($path,$ProfileKey)){
						q("INSERT INTO gl_ObjectsTree SET Objects_ID=$ProfilePicture_ID, Tree_ID=$ProfileTree_ID");
						prn($qr);
					}else{
						error_alert('key mismatch',true);
						mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
					}
				}
			}else{
				//delete previous
				q("DELETE
				b.*, c.*
				FROM gf_objects a, gf_objects b, gl_ObjectsTree c
				WHERE a.Objects_ID='$un_username' AND a.ParentObject='bais_staff' AND a.Relationship='Photo Gallery' AND
				b.Objects_ID=a.ID AND b.Relationship='Profile Picture Default' AND c.Objects_ID=b.ID");
				if($ProfileTree_ID!= -1){
					//gallery node
					if($Gallery_ID=q("SELECT ID FROM gf_objects WHERE ParentObject='bais_staff' AND Objects_ID='$un_username' AND Relationship='Photo Gallery'", O_VALUE)){
						prn($qr);
					}else{
						$Gallery_ID=q("INSERT INTO gf_objects SET ParentObject='bais_staff', Objects_ID='$un_username', Relationship='Photo Gallery', Notes='Created or edited with staff record',CreateDate=NOW(), Creator='".sun()."'", O_INSERTID);
						prn($qr);
					}
					//default profile picture node
					$ProfilePicture_ID=q("INSERT INTO gf_objects SET ParentObject='gf_objects', Objects_ID='$Gallery_ID', Relationship='Profile Picture Default', Notes='Created or edited with staff record', CreateDate=NOW(), Creator='".sun()."'",O_INSERTID);
					prn($qr);
					q("INSERT INTO gl_ObjectsTree SET Objects_ID=$ProfilePicture_ID, Tree_ID=$ProfileTree_ID");
					prn($qr);
				}
			}
		}
		if($cbPresent){
			if(preg_match('/_adminSettings_dbadmin_sendObjects_/',$cbSelect)){
				$cbValue='bais_staff:'.$un_username;
				$cbLabel=stripslashes($LastName .', '.$FirstName . ' ('.$Email.')');
			}
			callback(array("useTryCatch"=>false));
		}
		$navigate=true;
		$navigateCount=$count+1;
	break;
	case $mode=='changepassword':
		$OriginalPW=stripslashes($OriginalPW);
		$PW=stripslashes($PW);
		$ConfirmPW=stripslashes($ConfirmPW);
		$a=q("SELECT un_password, un_email FROM bais_universal WHERE un_username='$un_username'", O_ROW);
		$masterPWOverride=false;
		$message='';
		if(strtolower(md5($OriginalPW))==strtolower($MASTER_DBADMIN_PASSWORD) ||
		   strtolower(md5($OriginalPW))==strtolower(q("SELECT un_password FROM bais_universal WHERE un_username='".sun()."'", O_VALUE)) ||
		   minroles()<=ROLE_ADMIN
		){
			$masterPWOverride=true;
		}
		//who is this person
		switch(true){
			case $object=q("SELECT * FROM bais_staff WHERE st_unusername='$un_username'", O_ROW):
				$type='staff';
			break;
			case $object=q("SELECT * FROM gf_parents WHERE un_username='$un_username'", O_ROW):
				$type='parent';
			break;
			case $object=q("SELECT * FROM gf_therapists WHERE un_username='$un_username'", O_ROW):
				$type='therapist';
			break;
			default:
				$type='';
		}
		mail($developerEmail, 'Security file '.__FILE__.', line '.__LINE__,get_globals("This person has changed a password - update so that list_parents() etc is called otherwise we have a security breach"),$fromHdrBugs);
		
		switch(true){
			case strlen($PW)<$minPasswordLength || strlen($PW)>$maxPasswordLength:
				$message="Your password must be between $minPasswordLength and $maxPasswordLength characters in length"; break;
			case $PW!==$ConfirmPW:
				$message="Your password entries must match. Please retype"; break;
			case strtolower(md5($OriginalPW))!==strtolower($a['un_password']) && !$masterPWOverride:
				$message="The original password you entered does not match your current password.  If you do not know your current password you must contact an administrator"; break;
		}
		if($message){
			error_alert($message);
		}else{
			//execute
			q("UPDATE bais_universal SET un_password='".md5($PW)."' WHERE un_username='$un_username'");
			if($_POST['update'] && trim($a['un_email'])){
				mail($a['un_email'],'Your password has been changed', "Your $AcctCompanyName access password has been changed by ".q("SELECT CONCAT(un_firstname,' ',un_lastname) FROM bais_universal WHERE un_username='".sun()."'", O_VALUE)." (".sun().")\n\nTo sign in please go to:\nhttp://".$_SERVER['HTTP_HOST']."/gf5/console/login/\nUsername: $un_username\nNew password:$PW\n\nWe STRONGLY RECOMMEND that you delete this email and remove it from your trash bin as well.  If you have any questions please contact the person making these changes, or the Database Administrator", "From: info@fantasticshop.com");
				$sent=', and this parent or staff member has been notified by email';
			}
			?><script language="javascript" type="text/javascript">
			alert('Password has been updated<?php echo $sent?>');
			window.parent.close();
			</script><?php
		}
	break;
	case $mode=='pullProperty':
		$a=q("SELECT * FROM _v_properties_master_list WHERE ID='$Units_ID'", O_ROW);
		extract($a);
		?>
		<!-- address box -->
		<div id="FullAddress">
		<?php echo $a['PropertyAddress']?><br />
		<?php echo $PropertyCity.', '.$PropertyState. '&nbsp;&nbsp;'.$PropertyZip?>
		</div>
		
		<!-- unit info box -->
		<div id="unitInfo">
		<?php 
		if(!$Bedrooms || !$Bathrooms){
			?><div style="color:darkred;">Beds/baths not set up properly! <a href="properties<?php echo strtolower($Type)=='sfr'?2:3?>.php?Units_ID=<?php echo $Units_ID?>" onClick="return ow(this.href,'l1_properties','700,700');">Click to edit property info</a></div>
		<?php
		}else{
			if($Bedrooms) $bbs[]= $Bedrooms . ' bedroom'.($Bedrooms>1?'s':'');
			if($Bathrooms) $bbs[]= $Bathrooms . ' bathroom'.($Bathrooms>1?'s':'');
			if($SquareFeet)$bbs[]=$SquareFeet.' sq.ft.';
			echo @implode(', ',$bbs);
		}
		?>
		</div>
		
		<!-- commission info -->
		<div id="commissionInfo">
		<?php
		ob_start();
		$send=($SendCommission>2 ? number_format($SendCommission,2) : number_format($SendCommission * $Rent,2));
		$escort=($EscortCommission>2 ? number_format($EscortCommission,2) : number_format($EscortCommission * $Rent,2));
		if($SendCommission<=0 && $EscortCommission<=0){
			?><div style="color:darkred;">Commissions not set up properly! <a href="properties<?php echo strtolower($Type)=='sfr'?2:3?>.php?Units_ID=<?php echo $Units_ID?>" onclick="return ow(this.href,'l1_properties','700,700');">Click to edit property info</a></div>
			<?php
		}else{
			?>
			<strong><?php
			echo ($send ? $send : 'nothing for');
			echo ' send';
			if($SendCommission>2){
				echo ' (specific amount)';
			}else{
				echo $basedOn= ' ('. round($SendCommission*100,0).'% based on '.number_format($Rent,2).' rent)';
			}
			echo ',<br />';
			echo $escort . ' escorted';
			if($EscortCommission>2){
				echo ' (specific amount)';
			}else{
				echo $basedOn= ' ('. round($EscortCommission*100,0).'% based on '.number_format($Rent,2).' rent)';
			}
			?></strong>
			<?php
		}
		$commissionHTML=ob_get_contents();
		ob_end_clean();
		echo $commissionHTML;
		?>
		<div class="cb" style="font-size:1px;"> </div>
		</div>				
		<script language="javascript" type="text/javascript">
		window.parent.g('pullPropertyPending').style.display='none';
		window.parent.g('FullAddress').style.display='block';
		window.parent.g('FullAddress').innerHTML=document.getElementById('FullAddress').innerHTML;
		window.parent.g('unitInfo').innerHTML=document.getElementById('unitInfo').innerHTML;
		window.parent.g('commissionInfo').innerHTML=document.getElementById('commissionInfo').innerHTML;
		
		//set rent and rental period
		window.parent.g('Rent').value='<?php echo $Rent ? number_format($Rent,2) : ''?>';
		window.parent.g('Escort').value='';
		window.parent.__send__=<?php echo $SendCommission ? $SendCommission : '0.00'?>;
		window.parent.__escort__=<?php echo $EscortCommission ? $EscortCommission : '0.00'?>;
		window.parent.g('Extension').value='';
		</script><?php
	break;
	case $mode=='updateLateStatus':
		if(!($Headers_ID=q("SELECT t.Headers_ID FROM gl_LeasesTransactions lt, finan_transactions t WHERE lt.Transactions_ID=t.ID AND lt.Leases_ID=$Leases_ID", O_VALUE))){
			mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals($err='Unable to find Headers_ID from passed variable Leases_ID'),$fromHdrBugs);
			error_alert($err);
		}
		$GLF_LateCharge=preg_replace('/[^.0-9]/','',$GLF_LateCharge);
		if(!trim($GLF_LateCharge))error_alert('Enter a valid late charge amount (or 0.00)');
		q("UPDATE finan_headers SET GLF_LateStatus='$GLF_LateStatus', GLF_LateCharge='$GLF_LateCharge' WHERE ID=$Headers_ID");
		prn($qr);
		?><script language="javascript" type="text/javascript">
		//simple math
		var g=window.parent.g;
		var total=parseFloat(g('total_<?php echo $Leases_ID?>').firstChild.value);
		var pymt=parseFloat(g('pymt_<?php echo $Leases_ID?>').innerHTML);
		var latefee=<?php echo number_format($GLF_LateCharge,2)?>;
		e=total + latefee - pymt;
		g('latefee_<?php echo $Leases_ID?>').innerHTML=window.parent.number_format(latefee,2);
		g('balance_<?php echo $Leases_ID?>').innerHTML=(e ? window.parent.number_format(e,2) : 'pd.');
		//reset
		window.parent.g('definitionTools').style.visibility='hidden';
		</script><?php
	break;
	case $mode=='updateTiers':
		//precurse for valid values
		foreach($tier as $n=>$v){
			$p=0;
			for($i=1;$i<=3;$i++){
				if($v[$i]<$p || !preg_match('/^[0-9]+$/',$v[$i]) || $v[$i]>100)error_alert('You have an error for agent '.$n.'.  Tiers must be integer values, and must be between 0 and 100 per cent');
				$p=$v[$i];
			}
		}
		$EffectiveDate=$currentMonth.'-01';
		foreach($tier as $n=>$v){
			q("REPLACE INTO gl_tiers SET
			UserName='$n',
			EffectiveDate='$EffectiveDate',
			TierAmount1=".(isset($TierAmount[$n][1]) ? $TierAmount[$n][1] : '0').",
			TierAmount2=".(isset($TierAmount[$n][2]) ? $TierAmount[$n][2] : $tierBreakPoint1).",
			TierAmount3=".(isset($TierAmount[$n][3]) ? $TierAmount[$n][3] : $tierBreakPoint2).",
			TierPercent1='".($v[1]/100)."',
			TierPercent2='".($v[2]/100)."',
			TierPercent3='".($v[3]/100)."'");
			q("UPDATE bais_staff SET 
			GLF_Recruiter='".$GLF_Recruiter[$n]."',
			GLF_TransactionFee='".$GLF_TransactionFee[$n]."',
			GLF_EOFee='".$GLF_EOFee[$n]."' WHERE st_unusername='$n'");
		}
		error_alert('Tiers have been updated');
	break;
	case $mode=='toggleDiscrepancy':
		if(strlen($DiscrepancyDate) && strtotime($DiscrepancyDate)==false){
			?><script language="javascript" type="text/javascript">
			alert('Invalid date format for discrepancy');
			window.parent.g('ddate<?php echo $Invoices_ID;?>').focus();
			window.parent.g('ddate<?php echo $Invoices_ID;?>').select();
			</script><?php
			$assumeErrorState=false;
			exit;
		}
		q("UPDATE finan_headers SET 
		HeaderFlag='".($setTo ? 'Discrepancy' : '')."',
		GLF_DiscrepancyDate='".t($DiscrepancyDate)."',
		GLF_DiscrepancyReason='$DiscrepancyReason'
		WHERE ID=$Invoices_ID");
		prn($qr);
	break;
	case $mode=='updateBillingSent':
	case $mode=='deleteBillingSent':
		if($mode=='deleteBillingSent'){
			if(minroles()>ROLE_DBADMIN)error_alert('You do not have permission to do this task; you must be a db administrator or higher');
			q("DELETE FROM gl_batches WHERE ID=$Batches_ID");
			q("DELETE FROM gl_LeasesBatches WHERE Batches_ID='$Batches_ID'");
			?><script language="javascript" type="text/javascript">
			var l=window.parent.location+'';
			alert('Removed batch, refreshing the page');
			window.parent.location=l+'';
			</script><?php
			break;
		}
		if(!count($print))error_alert('No invoices were selected to bill!');

		$Batches_ID=q("INSERT INTO gl_batches SET
		Type='Billing',
		CreateDate=NOW(),
		Creator='".sun()."'", O_INSERTID);
		$Quantity=0;
		$toprint=$toemail=$tofax=0;
		foreach($print as $Leases_ID=>$v){
			$SendMethod=array_sum($v);
			q("INSERT INTO gl_LeasesBatches SET 
			Leases_ID=$Leases_ID,
			Batches_ID=$Batches_ID,
			SendMethod=$SendMethod,
			EditDate=NOW()");
			$Quantity++;
			if($v[1]){
				$toprint++;
			}
			
			if($v[4] || $v[2]){
				//get invoice data
				
			}

			if($v[4]){
				//create pdf document
				//create simple email with attach
				//email sender 03
				$tofax++;
				$tfID[]=$Leases_ID;
			}
			if($v[2]){
				//require email template
				//email sender 03
				//store the email output somewhere?
				$toemail++;
				$teID[]=$Leases_ID;
			}
			q("UPDATE gl_leases l, gl_LeasesTransactions lt, finan_transactions t, finan_headers h
			SET ToBeBilled=0 WHERE l.ID=lt.Leases_ID AND lt.Transactions_ID=t.ID AND t.Headers_ID=h.ID AND l.ID=$Leases_ID");
		}
		q("UPDATE gl_batches SET Quantity=$Quantity WHERE ID=$Batches_ID AND Type='Billing'");
		if($teID){
			$a=q("SELECT l.*, u.un_firstname, u.un_lastname FROM _v_leases_master l LEFT JOIN bais_universal u ON l.Agents_username=u.un_username WHERE l.ID IN(".implode(',',$teID).")", O_ARRAY_ASSOC);
			foreach($a as $Leases_ID=>$v) $send[$v['Properties_ID']][$Leases_ID]=$v;
			$k=0;
			foreach($send as $Properties_ID=>$leases){
				$property=q("SELECT
				t.UserName,
				cc.Contacts_ID,
				cc.Notes,
				PropertyName, ClientName, CompanyName,
				PropertyAddress, PropertyCity, PropertyState, PropertyZip, 
				IF(p.GLF_BillingEmail!='',p.GLF_BillingEmail,IF(c.GLF_BillingEmail!='',c.GLF_BillingEmail,c.Email)) AS Email,
				IF(PropertyContact, PropertyContact, CONCAT(c.PrimaryFirstName,' ',c.PrimaryLastName)) AS Contact
				FROM gl_properties p, finan_clients c 
				LEFT JOIN finan_ClientsContacts cc ON c.ID=cc.Clients_ID AND cc.Type='Primary'
				LEFT JOIN addr_contacts t ON cc.Contacts_ID=t.ID
				WHERE p.ID=$Properties_ID AND p.Clients_ID=c.ID", O_ROW);
				
				if(!$property['UserName'])mail($developerEmail, 'Notice in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($notice='A username is not present for a property - this may mean the person has no login'),$fromHdrBugs);
				$k++;
				$emailTo=sun('e');
				$emailSource=$_SERVER['DOCUMENT_ROOT'].'/gf5/console/emails/email_1101_invoices_due.php';
				require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
			}
		}
		if($toemail + $tofax){
			error_alert('Invoice'.($toemail + $tofax>1?'s have':' has').' been '.(($toemail && $tofax) ? 'emailed/faxed' : ($toemail ? 'emailed' : 'faxed')).($toprint?'. Now printing requested invoice(s)':''),1);
		}
		if($toprint){
			$key=md5(time().rand(1,1000000));
			$_SESSION['special']['print'][$key]=stripslashes_deep($_POST);
			?><script language="javascript" type="text/javascript">
			window.parent.location=('/gf5/console/leases_print.php?key=<?php echo $key;?>' /*,'l1_bar','700,700' */);
			//window.parent.location=('leases_print.php?key=<?php echo $key;?>');
			</script><?php
		}else{
			?><script language="javascript" type="text/javascript">
			window.parent.close();
			</script><?php
		}
	break;
	case $mode=='updateParameter':
		if(minroles()>ROLE_ADMIN)error_alert('You do not have permission to do this');
		if(is_array($varvalue)){
			foreach($varvalue as $n=>$v)if(!strlen($v))unset($varvalue[$n]);
			$varvalue=implode(',',$varvalue);
		}
		q("REPLACE INTO bais_settings SET UserName='system', vargroup='$vargroup', varnode='$varnode', varkey='$varkey', varvalue='$varvalue'");
		error_alert('Value updated');
	break;
	case $mode=='commitGiftCardBatch':
		if(!count($select))error_alert('You must select at least one invoice to pay gift cards on.  Note that both gift cards will be paidan invoice');
		$Batches_ID=q("INSERT INTO gl_batches SET Quantity=".count($select).", Type='Gift cards', CreateDate=NOW(), Creator='".sun()."'", O_INSERTID);
		foreach($select as $n=>$v)q("UPDATE gl_leases SET GCBatch1=$Batches_ID WHERE ID=$n");
		?><script language="javascript" type="text/javascript">
		alert('Batch #<?php echo $Batches_ID;?> has been created with <?php echo count($select)?> invoices.');
		window.parent.location='/gf5/console/root_giftcards.php?displayMode=unpaid';
		</script><?php
	break;
	case $mode=='pullBilling':
		$a=q("SELECT GLF_BillingMethod, GLF_BillingEmail, GLF_BillingFax FROM finan_clients WHERE ID=$Clients_ID", O_ROW);
		sleep(1);
		extract($a);
		?><script language="javascript" type="text/javascript">
		window.parent.g('GLF_BillingMethod').checked=<?php echo $GLF_BillingMethod & 1 ? 'true':'false'?>;
		window.parent.g('GLF_BillingMethod2').checked=<?php echo $GLF_BillingMethod & 2 ? 'true':'false'?>;
		window.parent.g('GLF_BillingEmail').value='<?php echo str_replace("'", "\'",$GLF_BillingEmail)?>';
		window.parent.g('GLF_BillingMethod3').checked=<?php echo $GLF_BillingMethod & 4 ? 'true':'false'?>;
		window.parent.g('GLF_BillingFax').value='<?php echo str_replace("'", "\'",$GLF_BillingFax)?>';
		window.parent.g('pullBillingStatus').style.display='none';
		</script><?php
	break;
	case $mode=='updateUnit':
		foreach(array(
			'WalkInClosets',
			'Furnished',
			'Storage',
			'Fireplace',
			'VaultedCeilings',
			'PrivateBalcony',
			'Dishwasher',
			'IceMaker',
			'Microwave',
		) as $v){
			if(!$$v)$$v='0';
		}
		$ID=$Units_ID;
		$sql=sql_insert_update_generic($MASTER_DATABASE,'gl_properties_units','UPDATE');
		q($sql);
		if($ProfileTree_ID==-1){
			q("DELETE ot.*, t.* FROM gl_ObjectsTree ot, relatebase_tree t
			WHERE 
			ot.Tree_ID=t.ID AND
			ot.Objects_ID=$Units_ID AND ot.ObjectName='gl_properties_units'");
		}
		?><script language="javascript" type="text/javascript">window.parent.close();</script><?php
	break;
	case $mode=='uploadFile':
		/*
		2011-05-04: this has been compiled from several redundant loader files and now we just have file_loader.php which will need to accommodate ALL file uploads.  This block is ONLY responsible for getting the Tree_ID record in
		
		*/

		//valid file extensions
		$validFileExtensions=array('gif','jpg','png','xls','xlsx','doc','docx','pdf','txt','html','htm','tif','tiff','xif');

		if(isset($Category) && !strlen($Category)){
			?><script language="javascript" type="text/javascript">
			window.parent.g('uploadFileWrap').innerHTML='<input name="uploadFile1" type="file" id="uploadFile1" onchange="uploadFile(this.value);" />';
			window.parent.g('Status').style.display='none';
			</script><?php
			error_alert('Select a category for this file');
		}
		if($submode=='uploadChildFile' || $submode=='uploadHomeFile'){
			error_alert('example only; not for this application');
			$paramKey=		($submode=='uploadChildFile' ? 'Children_ID' : 'Fosterhomes_ID');
			$fctn=			($submode=='uploadChildFile' ? 'list_children' : 'list_fosterhomes');
			$fldr=			($submode=='uploadChildFile' ? 'Children' : 'Homes');
			$obj=			($submode=='uploadChildFile' ? 'gf_children' : 'gf_fosterhomes');
			$cmp=			($submode=='uploadChildFile' ? 'comp_300_child_files.php' : 'comp_42_fh_documentation.php');
			$hndl=			($submode=='uploadChildFile' ? 'Child' : 'Home');

			//this block varies by tying the Tree_ID to ObjectsTree and calling the component and loading it
			if(!in_array($$paramKey,$fctn('keys')))error_alert('You do not have permission to upload documentation to this record');
			$handle=substr(md5(time().rand(100,10000)),0,5).'_';
			$ext=strtolower(end(explode('.',$_FILES['uploadFile1']['name'])));
			if(!preg_match('/^(gif|jpg|png|ods|odt|xls|xlsx|doc|docx|pdf|txt|html|htm|tif|tiff|xif)$/',$ext))error_alert('The file is not an allowed extension! The following are allowed: gif,jpg,png,xls,xlsx,ods,odt,doc,docx,pdf,txt,html,htm,tif,tiff,xif');
			if(!is_uploaded_file($_FILES['uploadFile1']['tmp_name']))error_alert('Abnormal error, unable to upload file');
			if($i=getimagesize($_FILES['uploadFile1']['tmp_name'])){
				$FileWidth=$i[0];
				$FileHeight=$i[1];
			}
	
			$fileFullPath=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$fldr.'/'.$handle.stripslashes($_FILES['uploadFile1']['name']);
			if(!move_uploaded_file($_FILES['uploadFile1']['tmp_name'],$fileFullPath)){
				mail($developerEmail,'error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
				error_alert('Unable to move file to the foster child folder');
			}
				
			//from hidden field
			$LocalPath=explode('/',$LocalPath);
			$LocalFileName=array_pop($LocalPath);
			$LocalPath=implode('/',$LocalPath);
	
			//we are putting it in the generic folder documentation/user/Children
			$node=tree_build_path('images/documentation/'.$GCUserName.'/'.$fldr);
			$Tree_ID=q("INSERT INTO relatebase_tree SET 
			Tree_ID=$node,
			Name='$handle".$_FILES['uploadFile1']['name']."',
			MimeType='".$_FILES['uploadFile1']['type']."',
			FileWidth='$FileWidth',
			FileHeight='$FileHeight',
			FileSize='".$_FILES['uploadFile1']['size']."',
			/* 
			-- not developed --
			LocalMachines_ID=
			LocalPath=
			-- */
			LocalFileName='$LocalFileName',
			CreateDate=NOW(), 
			Creator='".$_SESSION['systemUserName']."'", O_INSERTID);
			prn($qr);
			
			if($Documentation_ID=q("SELECT ID
				FROM gf_objects o 
				WHERE o.Objects_ID='".$$paramKey."' AND o.ParentObject='$obj' AND o.Relationship='Primary Documentation'", O_VALUE)){
				prn($qr);
			}else{
				$Documentation_ID=q("INSERT INTO gf_objects SET		
				Objects_ID='".$$paramKey."',
				ParentObject='$obj',
				Relationship='Primary Documentation',
				CreateDate=NOW(),
				Creator='".$_SESSION['systemUserName']."'", O_INSERTID);
				prn($qr);
			}
			//insert the sub-object
			$Objects_ID=q("INSERT INTO gf_objects SET
			Objects_ID='$Documentation_ID',
			ParentObject='gf_objects',
			Relationship='',
			Category='$Category',
			CreateDate=NOW(),
			Creator='".$_SESSION['systemUserName']."'", O_INSERTID);
			prn($qr);
			//join
			q("INSERT INTO gl_ObjectsTree SET
			Objects_ID=$Objects_ID,
			Tree_ID=$Tree_ID,
			ObjectName='gf_objects'");
			prn($qr);
			
			//call the component
			$ID=$$paramKey;
			$refreshComponentOnly=true;
			require($_SERVER['DOCUMENT_ROOT'].'/gf5/console/components/'.$cmp);
			?><script language="javascript" type="text/javascript">
			try{
			window.parent.opener.g('<?php echo $hndl?>Files').innerHTML=document.getElementById('<?php echo $hndl?>Files').innerHTML;
			}catch(e){ }
			//close the parent window - deactivate pending script
			window.parent.close();
			</script><?php
		}else if($submode=='uploadLOCFile'){
			error_alert('example only; not for this application');
			//this block varies by tying the Tree_ID to ObjectsTree and calling the component and loading it
			$handle=substr(md5(time().rand(100,10000)),0,5).'_';
			$ext=strtolower(end(explode('.',$_FILES['uploadFile1']['name'])));
			if(!preg_match('/^(gif|jpg|png|xls|xlsx|doc|docx|pdf|txt|html|htm|tif|tiff|xif)$/',$ext))error_alert('The file is not an allowed extension! The following are allowed: gif,jpg,png,xls,xlsx,doc,docx,pdf,txt,html,htm,tif,tiff,xif');
			if(!is_uploaded_file($_FILES['uploadFile1']['tmp_name']))error_alert('Abnormal error, unable to upload file');
	
			$fileFullPath=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/LOCS/'.$handle.stripslashes($_FILES['uploadFile1']['name']);
			if(!move_uploaded_file($_FILES['uploadFile1']['tmp_name'],$fileFullPath)){
				mail($developerEmail,'error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
				error_alert('Unable to move file to the LOC folder');
			}
				
			//from hidden field
			$LocalPath=explode('/',$LocalPath);
			$LocalFileName=array_pop($LocalPath);
			$LocalPath=implode('/',$LocalPath);
	
			//we are putting it in the generic folder documentation/user/LOCS
			$node=tree_build_path('images/documentation/'.$GCUserName.'/LOCS');
			$Tree_ID=q("INSERT INTO relatebase_tree SET 
			Tree_ID=$node,
			Name='$handle".$_FILES['uploadFile1']['name']."',
			MimeType='".$_FILES['uploadFile1']['type']."',
			/* 
			-- not developed --
			LocalMachines_ID=
			LocalPath=
			-- */
			LocalFileName='$LocalFileName',
			CreateDate=NOW(), 
			Creator='".$_SESSION['systemUserName']."'", O_INSERTID);
			
			q("INSERT INTO gf_ChildrenLocs_files SET Tree_ID=$Tree_ID, Childrenlocs_ID=$Childrenlocs_ID, CreateDate=CURDATE(), Creator='".sun()."'");
			
			//call the component
			$ID=$Childrenlocs_ID;
			$refreshComponentOnly=true;
			require('../components/comp_82_loc_documentation.php');
			?><script language="javascript" type="text/javascript">
			try{
			window.parent.opener.g('LOCFiles').innerHTML=document.getElementById('LOCFiles').innerHTML;
			}catch(e){ }
			//close the parent window - deactivate pending script
			window.parent.close();
			</script><?php
		}else{
			//get parameters
			$handle=substr(md5(time().rand(100,10000)),0,5).'_';
			$ext=strtolower(end(explode('.',$_FILES['uploadFile1']['name'])));

			$fileFullPath=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/properties/'.$handle.stripslashes($_FILES['uploadFile1']['name']);
			if(!move_uploaded_file($_FILES['uploadFile1']['tmp_name'],$fileFullPath)){
				mail($developerEmail,'error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
				error_alert('Unable to move file to the general folder');
			}

			//from hidden field
			$LocalPath=explode('/',$LocalPath);
			$LocalFileName=array_pop($LocalPath);
			$LocalPath=implode('/',$LocalPath);

			//we are putting it in the generic folder documentation/user/general
			$node=tree_build_path('images/documentation/'.$GCUserName.'/properties');
			$Tree_ID=q("INSERT INTO relatebase_tree SET 
			Tree_ID=$node,
			Name='$handle".$_FILES['uploadFile1']['name']."',
			MimeType='".$_FILES['uploadFile1']['type']."',
			/* 
			-- not developed --
			LocalMachines_ID=
			-- */
			LocalPath='$LocalPath',
			LocalFileName='$LocalFileName',
			CreateDate=NOW(), 
			Creator='".$_SESSION['systemUserName']."'", O_INSERTID);
			
			if($submode=='uploadPropertyPicture' && ($Units_ID || $Tree_ID)){
				if(preg_match('/\.(jpg|gif|png|svg)$/i',$_FILES['uploadFile1']['name'])){
					//2012-04-18 newest coding
					if($ReplaceTree_ID){
						q("DELETE FROM gl_ObjectsTree WHERE Tree_ID=$ReplaceTree_ID");
						q("DELETE FROM relatebase_tree WHERE ID=$ReplaceTree_ID");
						q("UPDATE relatebase_tree SET ID=$ReplaceTree_ID WHERE ID=$Tree_ID");
					}
					if($Category=='Main Image'){
						q("UPDATE gl_ObjectsTree SET Category='Premise Picture' WHERE ".($Units_ID?'ObjectName=\'gl_properties_units\' AND Objects_ID='.$Units_ID : 'ObjectName=\'gl_properties\' AND Objects_ID='.$Properties_ID)." AND Category='Main Image'");
					}
					q("INSERT INTO gl_ObjectsTree SET
					Objects_ID=".($Units_ID ? $Units_ID : $Properties_ID).",
					ObjectName='".($Units_ID ? 'gl_properties_units' : 'gl_properties')."',
					Tree_ID=".($ReplaceTree_ID ? $ReplaceTree_ID : $Tree_ID).",
					Category='$Category',
					Description='$Description',
					EditDate=NOW()");
					?><script language="javascript" type="text/javascript">
					window.parent.opener.refreshComponent('propertyImages','','section=gl_properties<?php echo $Units_ID?'_units':''?>&<?php echo $Units_ID?'Units_ID='.$Units_ID:'Properties_ID='.$Properties_ID;?>');
					window.parent.close();
					</script><?php
				}
			}else if($submode=='uploadPropertyFile'){
				if($Documentation_ID=q("SELECT ID
					FROM gf_objects o 
					WHERE o.Objects_ID='".$Properties_ID."' AND o.ParentObject='gl_properties' AND o.Relationship='Primary Documentation'", O_VALUE)){
					prn($qr);
				}else{
					$Documentation_ID=q("INSERT INTO gf_objects SET		
					Objects_ID='".$Properties_ID."',
					ParentObject='gl_properties',
					Relationship='Primary Documentation',
					CreateDate=NOW(),
					Creator='".$_SESSION['systemUserName']."'", O_INSERTID);
					prn($qr);
				}
				//insert the sub-object
				$Objects_ID=q("INSERT INTO gf_objects SET
				Objects_ID='$Documentation_ID',
				ParentObject='gf_objects',
				Relationship='Property File',
				Category='$Category',
				CreateDate=NOW(),
				Creator='".$_SESSION['systemUserName']."'", O_INSERTID);
				prn($qr);
				//join
				q("INSERT INTO gl_ObjectsTree SET
				Objects_ID=$Objects_ID,
				Tree_ID=$Tree_ID,
				ObjectName='gf_objects',
				EditDate=NOW()");
				prn($qr);
				
				?><script language="javascript" type="text/javascript">
				window.parent.opener.refreshComponent('propertyFiles','','Properties_ID=<?php echo $Properties_ID;?>');
				window.parent.close();
				</script><?php
			}else if($submode=='floorPlan'){
				//2011-12-02 we are going to allow this without prejudice on permissions
				if($Units_ID && preg_match('/\.(jpg|gif|png|svg)$/i',$_FILES['uploadFile1']['name'])){
					$targetTree_ID=$Tree_ID;
					$ProfileKey=$handle;
					q("INSERT INTO gl_ObjectsTree SET
					Objects_ID=$Units_ID,
					ObjectName='gl_properties_units',
					Tree_ID=$Tree_ID,
					Category='Floor Plan',
					Description='$Description',
					EditDate=NOW()");
				}
			}

			//run callback - will close window.parent normally
			if($cbPresent){
				callback(array("useTryCatch"=>false));
			}
		}
	break;
	case $mode=='deleteObject':
		//permissions
		if(minroles()>ROLE_ADMIN)error_alert('You do not have permission to delete this transaction');
		if($a=q("SELECT HeaderType, HeaderStatus, Accounts_ID FROM finan_headers h WHERE ID=$Headers_ID", O_ROW)){
			//OK
		}else{
			error_alert('Unable to locate object; it has probably been deleted already');
		}
		if($b=q("SELECT 
			COUNT(DISTINCT tt1.ChildTransactions_ID) AS ThisToOthers, 
			COUNT(DISTINCT tt2.ParentTransactions_ID) AS OthersToThis
			FROM finan_transactions t 
			LEFT JOIN finan_TransactionsTransactions tt1 ON t.ID=tt1.ParentTransactions_ID 
			LEFT JOIN finan_TransactionsTransactions tt2 ON t.ID=tt2.ChildTransactions_ID
			WHERE t.Headers_ID=$Headers_ID
			GROUP BY t.Headers_ID
			HAVING 
			COUNT(DISTINCT tt1.ChildTransactions_ID) >0 OR
			COUNT(DISTINCT tt2.ParentTransactions_ID) >0", O_ROW)){
			extract($b);
			switch(true){
				//2d matrix
				case $a['HeaderType']=='Invoice' && $OthersToThis:
					//deleting an invoice to which payments have been made - normally do resubmit its
					/* !!! however for GLF, we do NOT let them do this !!! */
					$err='You cannot delete an invoice to which one or more payments have been applied.  Type Ctrl-H for a history to find the payments, and delete them first';
				break;
				case $a['HeaderType']=='Invoice' && $ThisToOthers:
					//this doesn't happen, a invoice is an originating transaction, see notes above
				break;
				case $a['HeaderType']=='Payment' && $OthersToThis:
					//you cannot delete a payment that has been deposited
					$err='You cannot delete a payment that has been deposited; first delete it from the deposit';
				break;
				case $a['HeaderType']=='Payment' && $ThisToOthers;
					//they can do this, the portal would have asked them to confirm
				break;
			}
			if($err)error_alert($err);
		}
		q("DELETE t.*, tt.* FROM finan_transactions t LEFT JOIN finan_TransactionsTransactions tt ON t.ID=tt.ParentTransactions_ID OR t.ID=tt.ChildTransactions_ID WHERE t.Headers_ID=$Headers_ID");
		prn($qr);
		q("DELETE h.*, i.*, p.* FROM finan_headers h 
		LEFT JOIN finan_invoices i ON h.ID=i.Headers_ID 
		LEFT JOIN finan_payments p ON h.ID=p.Headers_ID
		WHERE h.ID=$Headers_ID");
		prn($qr);
		
		//how do we know to do this? and a better way would be to navigate to the next sequence
		?><script language="javascript" type="text/javascript">
		window.parent.close();
		</script><?php
	break;
	case $mode=='voidObject':
		if(minroles()>ROLE_ADMIN)error_alert('You do not have permission to delete an invoice');
		if($a=q("SELECT HeaderType, HeaderStatus, Accounts_ID FROM finan_headers h WHERE ID=$Headers_ID", O_ROW)){
			//OK
		}else{
			error_alert('Unable to locate object; it has probably been deleted');
		}
		if($a['HeaderType']!=='Invoice')error_alert('This is not an invoice; only invoices can be voided');
		if($a['HeaderStatus']=='Void'){
			$voiding=false;
		}else if($a['HeaderStatus']=='Current'){
			$voiding=true;
		}else{
			mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='headerstatus neither current nor void'),$fromHdrBugs);
			error_alert($err.', developer has been notified');

		}
		if($voiding && $b=q("SELECT 
			COUNT(DISTINCT tt2.ParentTransactions_ID) AS OthersToThis
			FROM finan_transactions t 
			LEFT JOIN finan_TransactionsTransactions tt2 ON t.ID=tt2.ChildTransactions_ID
			WHERE t.Headers_ID=$Headers_ID
			GROUP BY t.Headers_ID
			HAVING 
			COUNT(DISTINCT tt2.ParentTransactions_ID) >0", O_ROW)){
			error_alert('There are payments applied to this invoice; it cannot be voided');
		}
		if($voiding){
			$amount=q("SELECT Extension FROM finan_headers h, finan_transactions t WHERE h.ID=$Headers_ID AND h.ID=t.Headers_ID AND h.Accounts_ID=t.Accounts_ID", O_VALUE);
			q("DELETE tt.* FROM finan_TransactionsTransactions tt, finan_transactions t WHERE (t.ID=tt.ParentTransactions_ID OR t.ID=ChildTransactions_ID) AND t.Headers_ID=$Headers_ID");
			q("UPDATE finan_headers h, finan_invoices i, finan_transactions t SET
			h.HeaderStatus='Void', 
			t.Quantity=NULL,
			t.UnitPrice=NULL,
			t.Extension=0.00,
			i.Comments=CONCAT(i.Comments,' *** VOID: $".number_format($amount,2)." ***')
			WHERE h.ID=t.Headers_ID AND h.ID=i.Headers_ID AND h.ID=$Headers_ID");
		}else{
			q("UPDATE finan_headers h SET h.HeaderStatus='Current' WHERE h.ID=$Headers_ID");
			error_alert('This invoice has been unvoided',1);
		}
		?><script language="javascript" type="text/javascript">
		window.parent.location='/gf5/console/leases.php?Leases_ID=<?php echo $Headers_ID?>';
		</script><?php
	break;
	case $mode=='getHistory':
		if($b=q("SELECT 
			COUNT(DISTINCT tt2.ParentTransactions_ID) AS OthersToThis
			FROM finan_transactions t 
			LEFT JOIN finan_TransactionsTransactions tt2 ON t.ID=tt2.ChildTransactions_ID
			WHERE t.Headers_ID=$Headers_ID
			GROUP BY t.Headers_ID
			HAVING 
			COUNT(DISTINCT tt2.ParentTransactions_ID) >0", O_ROW)){
			?><script language="javascript" type="text/javascript">
			window.parent.ow("/gf5/console/history.php?Headers_ID=<?php echo $Headers_ID;?>",'l1_pymthistorybubble','500,230');
			</script><?php
		}else{
			error_alert('No payments have been applied to this invoice');
		}
	break;
	case $mode=='toggleActiveObject':
		if($component=='Staff'){
			if(!$_SESSION['admin']['roles'][ROLE_ADMIN] && !$_SESSION['admin']['roles'][ROLE_MANAGER])error_alert('You must be an administrator to make a staff record active/inactive');
			
			q("UPDATE bais_staff SET st_active='".($current==1?0:1)."' WHERE st_unusername='$node'");
			prn($qr);
			?><script language="javascript" type="text/javascript">
			var g=window.parent.g;
			g('r_<?php echo $node?>_active').innerHTML=(<?php echo $current?> ? '<img src="/images/i/garbage2.gif" width="18" height="21" align="absbottom" />' : '&nbsp;');
			g('r_<?php echo $node?>_active').title=('Make this foster child '+(<?php echo $current?> ? '':'in')+'active');
			g('r_<?php echo $node?>').setAttribute('active', (<?php echo $current?> ? '0' : '1'));
			if(window.parent.hideInactiveStaff && <?php echo $current?>){
				g('r_<?php echo $node?>').style.display='none';
				var c=parseInt(window.parent.g('listStaff_count').innerHTML)-1;
				window.parent.g('listStaff_count').innerHTML=c;
			}
			</script><?php
			break;
		}else{
			error_alert('Not developed');
		}
	break;
	case $mode=='couponVerification':
		$err1='Your coupon code is not valid';
		if(!preg_match('/^[0-9]+-[0-9]+$/',$code))error_alert($err1);
		$a=explode('-',$code);
		$Searches_ID=ltrim($a[0],'0');
		$Units_ID=ltrim($a[1],'0');
		if($a=q("SELECT
			c.ID AS Contacts_ID, c.FirstName, c.LastName, c.Email, s.ID AS Searches_ID, u.ID AS Units_ID, p.Type, v.Status
			FROM
			addr_contacts c 
			JOIN gl_searches s ON c.ID=s.Contacts_ID
			JOIN gl_coupons o ON s.Coupons_ID=o.ID
			LEFT JOIN gl_properties_units u ON u.ID='$Units_ID'
			LEFT JOIN gl_properties p ON p.ID=u.Properties_ID
			LEFT JOIN gl_coupons_verifications v ON Searches_ID=$Searches_ID AND Units_ID=$Units_ID
			WHERE
			c.Email='$Email' AND
			s.ID=$Searches_ID", O_ROW)){
			if(!$a['Units_ID'])error_alert($err1);
			if(strtolower($a['Type'])=='apt' && !trim($UnitNumber))error_alert('This is an apartment, so please provide a unit number to complete your survey');
			if($a['Status']>0)error_alert('It appears this coupon request has already been processed');
			
			//enter the record
			q("INSERT INTO gl_coupons_verifications SET Searches_ID=$Searches_ID, Units_ID='".$a['Units_ID']."', UnitNumber='$UnitNumber', Comments='$Comments', Status=1");
			
			//mail agent
			
			//mail administrator - here we need to use the logic from simple-fostercare - ugh..
			
			//redirect them as needed
			$a=array(
				'_POST'=>stripslashes_deep($_POST),
				'data'=>$a,
			);
			$key=md5(time().rand(1,1000000));
			$_SESSION['special']['verifications'][$key]=$a;
			?><script language="javascript" type="text/javascript">
			window.parent.location='/gf5/console/coupons.php?key=<?php echo $key;?>';
			</script><?php
		}else error_alert($err1);
	break;
	case $mode=='customerSearch':
		$refreshComponentOnly=true;
		sleep(2);
		require('../components/comp_502_dataset_contacts_v100.php');
		?><script language="javascript" type="text/javascript">
		window.parent.g('<?php echo $datasetComponent;?>').innerHTML=document.getElementById('<?php echo $datasetComponent;?>').innerHTML;
		window.parent.g('pending').innerHTML='';
		</script><?php
	break;
	case $mode=='deleteMySearches':
		if($a=q("SELECT * FROM gl_searches WHERE ID='$Searches_ID'", O_ROW)){
			if(minroles()>=ROLE_AGENT && $a['Creator']!==sun())error_alert('You do not have authority to delete this search');
			q("DELETE FROM gl_searches WHERE ID=$Searches_ID");
			?><script language="javascript" type="text/javascript">
			window.parent.g('r_<?php echo $Searches_ID;?>').style.display='none';
			</script><?php
		}else error_alert('Unable to locate this search');
	break;
	case $mode=='deleteBulletin':
	case $mode=='insertBulletin':
	case $mode=='updateBulletin':
		if($mode=='deleteBulletin'){
			q("DELETE FROM gf_bulletins WHERE ID=$ID");
			q("DELETE FROM gf_UniversalBulletins WHERE Bulletins_ID=$ID");
			if($cbPresent){
				callback(array("useTryCatch"=>false));
			}
			break;
		}
		//error checking
		/*
		if(($x=strtotime($EffectiveDate))==false)error_alert('Please include a valid effective date of the bulletin');
		$EffectiveDate=date('Y-m-d H:i:s',$x);
		*/
		
		if(!$Sticky)$Sticky='0';
		if(!$Title)error_alert('Enter a title');
		//if(!$Description)error_alert('Enter a description (up to 255 characters)');
		if(strlen($Description)>255)error_alert('Your short description is '.strlen($Description).' characters and can only be 255 characters in length');

		if($mode==$insertMode)$EffectiveDate=date('Y-m-d H:i:s');
		$IncludeGroupsArray=$IncludeGroups;
		$IncludeGroups=implode(',',$IncludeGroups);
		if(!$IncludeGroups)error_alert('Select at least one group of people this bulletin is intended for');
		if(($mode==$updateMode || $mode=='deleteBulletin') && sun()!=$bl_unusername)error_alert('You are not the author of this bulletin and may not edit or delete it');


		//file attachment
		if($_FILES['uploadFile1']['name'] && !$Shunt){
			$handle=substr(md5(time().rand(100,10000)),0,5).'_';
			$ext=strtolower(end(explode('.',$_FILES['uploadFile1']['name'])));
			if(!preg_match('/^(gif|jpg|png|ods|odt|xls|xlsx|doc|docx|pdf|txt|html|htm|tif|tiff|xif)$/',$ext))error_alert('The file is not an allowed extension! The following are allowed: gif,jpg,png,xls,xlsx,ods,odt,doc,docx,pdf,txt,html,htm,tif,tiff,xif');
			if(!is_uploaded_file($_FILES['uploadFile1']['tmp_name']))error_alert('Abnormal error, unable to upload file');
			if($i=getimagesize($_FILES['uploadFile1']['tmp_name'])){
				$FileWidth=$i[0];
				$FileHeight=$i[1];
			}
			$fileFullPath='/images/documentation/'.$GCUserName.'/general';
			$node=tree_build_path($fileFullPath);
			$fileFullPath=$_SERVER['DOCUMENT_ROOT'].$fileFullPath.'/'.$handle.stripslashes($_FILES['uploadFile1']['name']);
			if(!move_uploaded_file($_FILES['uploadFile1']['tmp_name'],$fileFullPath)){
				mail($developerEmail,'error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
				error_alert('Unable to move file to the foster child folder');
			}
		}

		
		//Meaning changed 2011-10-23; binary 1=therapists, 2=parents, 4=staff (generally), 8=peers
		$data=array();
		// !!!!NOT USED!!!! - if(in_array(1,$IncludeGroups) /* therapists */ && $a=list_therapists())$data=array_merge($data,$a);
		
		if(minroles()<ROLE_AGENT && in_array(ROLE_CLIENT,$IncludeGroupsArray) /* clients */ && $a=list_clients('keys')){
			if($a=q("SELECT t.UserName, IF(t.Email,t.Email,c.Email) AS Email, t.PasswordMD5 AS Password, t.LastName, t.FirstName, t.MiddleName MiddleName, ".ROLE_CLIENT." AS Position
			FROM gl_properties p, finan_clients c, finan_ClientsContacts cc, addr_contacts t WHERE
			t.UserName!='' AND 
			p.Active=1 AND
			p.Clients_ID=c.ID AND c.ID=cc.Clients_ID AND cc.Contacts_ID=t.ID", O_ARRAY_ASSOC))$data=array_merge($data,$a);
		}
		if(minroles()<ROLE_AGENT && in_array(ROLE_AGENT,$IncludeGroupsArray)){
			if($a=q("SELECT un_username AS UserName, un_email AS Email, un_password AS Password, un_firstname AS FirstName, un_lastname AS LastName, un_middlename AS MiddleName, ".ROLE_AGENT." AS Position FROM bais_universal, bais_staff, bais_StaffRoles WHERE un_username=st_unusername AND st_unusername=sr_stusername AND st_status=1 AND sr_roid=".ROLE_AGENT." GROUP BY un_username", O_ARRAY_ASSOC))$data=array_merge($data,$a);
		}
		if(minroles()<ROLE_AGENT && in_array(ROLE_MANAGER,$IncludeGroupsArray)){
			if($a=q("SELECT un_username AS UserName, un_email AS Email, un_password AS Password, un_firstname AS FirstName, un_lastname AS LastName, un_middlename AS MiddleName, ".ROLE_MANAGER." AS Position FROM bais_universal, bais_staff, bais_StaffRoles WHERE un_username=st_unusername AND st_unusername=sr_stusername AND st_status=1 AND sr_roid=".ROLE_MANAGER." GROUP BY un_username", O_ARRAY_ASSOC))$data=array_merge($data,$a);
		}
		if(minroles()<ROLE_AGENT && in_array(ROLE_ADMIN,$IncludeGroupsArray)){
			if($a=q("SELECT un_username AS UserName, un_email AS Email, un_password AS Password, un_firstname AS FirstName, un_lastname AS LastName, un_middlename AS MiddleName, ".ROLE_ADMIN." AS Position FROM bais_universal, bais_staff, bais_StaffRoles WHERE un_username=st_unusername AND st_unusername=sr_stusername AND st_status=1 AND sr_roid=".ROLE_ADMIN." GROUP BY un_username", O_ARRAY_ASSOC))$data=array_merge($data,$a);
		}
		if(minroles()<ROLE_AGENT && in_array(ROLE_DBADMIN,$IncludeGroupsArray)){
			if($a=q("SELECT un_username AS UserName, un_email AS Email, un_password AS Password, un_firstname AS FirstName, un_lastname AS LastName, un_middlename AS MiddleName, ".ROLE_DBADMIN." AS Position FROM bais_universal, bais_staff, bais_StaffRoles WHERE un_username=st_unusername AND st_unusername=sr_stusername AND st_status=1 AND sr_roid=".ROLE_DBADMIN." GROUP BY un_username", O_ARRAY_ASSOC))$data=array_merge($data,$a);
		}
		foreach($data as $n=>$v){
			//exclude myself
			if(strtolower($v['UserName'])==sun())unset($data[$n]);
		}

		
		if(empty($data))error_alert('Your selection does not include anyone to send a bulletin to');
		if(strlen($Contents)<30)error_alert('Enter the body of the bulletin');
		if(!$Shunt){
			$sql=sql_insert_update_generic($MASTER_DATABASE,'gf_bulletins', $mode, $options);
			prn($sql);
			$newID=q($sql, O_INSERTID);
			if($mode==$insertMode)$ID=$Bulletins_ID=$newID;
			if($fileFullPath){
				$Tree_ID=q("INSERT INTO relatebase_tree SET
				Tree_ID=$node,
				Name='$handle".$_FILES['uploadFile1']['name']."',
				MimeType='".$_FILES['uploadFile1']['type']."',
				FileSize='".$_FILES['uploadFile1']['size']."',
				FileWidth='".$FileWidth."',
				FileHeight='".$FileHeight."',
				LocalFileName='".$_FILES['uploadFile1']['name']."',
				CreateDate=NOW(), 
				Creator='".sun()."'", O_INSERTID);
				
				q("INSERT INTO gl_ObjectsTree SET
				Objects_ID='$Bulletins_ID',
				ObjectName='gf_bulletins',
				Tree_ID='$Tree_ID'");
			}
		}
		if($mode==$insertMode && $SendMail){
			$emailSource=$_SERVER['DOCUMENT_ROOT'].'/gf5/console/emails/email_08_submitbulletin.php';
			
			//attach a file
			if($fileFullPath)$fileArray=$fileFullPath;
			
			foreach($data as $UserName=>$v){
				$type=$chain[$UserName];
				require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
				if($Shunt)error_alert('One sample email sent to '.$ShuntEmail.($_FILES['uploadFile1']['name']?' (no attachment has been included)':''));
				if($type==ROLE_CLIENT){
					$clientList[]=$v;
				}else{
					$stafflist[]=$v;
				}
			}
			if(trim($CC) && $CC!=='(optional, separate by commas)'){
				$SendCC=true;
				$v['Email']=$CC;
				require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
			}
			//send creator a summary
			$emailSource=$_SERVER['DOCUMENT_ROOT'].'/gf5/console/emails/email_08_submitbulletinreport.php';
			require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
			error_alert('Bulletins sent',true);
		}
		prn($qr);
		//now refresh the list view
		if($cbPresent){
			callback(array("useTryCatch"=>false));
		}
		//navigate the window
		$navigate=true;
		$navigateCount=$count+1;
	break;
	case $mode=='acknowledgeBulletin':
		sleep(2);
		q("REPLACE INTO gf_UniversalBulletins SET un_username='".sun()."', Bulletins_ID=$Bulletins_ID, Status='Read'");
		?><script language="javascript" type="text/javascript">
		window.parent.g('sendingAck').innerHTML='<strong>Thank you</strong>';
		</script><?php
	break;
	case $mode=='dismissBulletin':
		q("REPLACE INTO gf_UniversalBulletins SET un_username='".sun()."', Bulletins_ID=$Bulletins_ID, Status='Dismissed'");
		?><script language="javascript" type="text/javascript">
		alert('Bulletin has been dismissed');
		window.parent.g('r_<?php echo $Bulletins_ID?>').style.display='none';
		</script><?php
	break;
	case $mode=='sendFeedback':
		//get the parent info and article info
		$article=q("SELECT * FROM gf_bulletins WHERE ID=$Bulletins_ID", O_ROW);
		$writer=q("SELECT * FROM bais_universal WHERE un_username='".$article['bl_unusername']."'", O_ROW);
		$sender=q("SELECT * FROM bais_universal WHERE un_username='".sun()."'", O_ROW);
		ob_start();
		require('../emails/email_05_bulletin_feedback.php');
		$out=ob_get_contents();
		ob_end_clean();
		$writer['un_email']=$developerEmail;
		
		if(mail(
			($writer['un_email'] ? $writer['un_email'] : 'bugreports@'.$applicationDomainName.','.$developerEmail),
			($writer['un_email'] ? stripslashes($fbsubject) : '(no email present for '.$writer['un_username'].')'),
			$out,
			'From: bulletins@'.$applicationDomainName
		)){
			?><script language="javascript" type="text/javascript">
			window.parent.g('fbsubject').value='';
			window.parent.g('fbbody').value='';
			alert('Mail sent successfully');
			</script><?php
		}
	break;
	case $mode=='sendSearchResults':
		if(minroles()>ROLE_AGENT)error_alert('You do not have access to do this');
		if(!$FullName)error_alert('Enter a full name');
		if(!valid_email($Email) || !trim($Email))error_alert('Enter a valid email address');
		sleep(1);
		$emailSource = $_SERVER['DOCUMENT_ROOT'] . '/gf5/console/emails/email_100_search_results_send_v100.php';
		require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
		?><script language="javascript" type="text/javascript">
		window.parent.g('emailStat').style.visibility='hidden';
		</script><?php
		error_alert('A link has been sent to '.$Email.'.  The link will expire 7 days from the time they receive it');
	break;
	case $mode=='updateUtilities':
		if(minroles()>ROLE_ADMIN)error_alert('You do not have access to this');
		foreach($utilities as $vargroup=>$v){
			foreach($v as $varnode=>$w){
				foreach($w as $varkey=>$varvalue){
					prn("$vargroup:$varnode:$varkey:$varvalue");
					if(!preg_match('/[0-9]+\.[0-9]+/',$varvalue)){
						$err++;
						$errs[]=$varvalue;
						continue;
					}
					q("REPLACE INTO bais_settings SET UserName='{system:utilities}', vargroup='$vargroup', varnode='$varnode', varkey='$varkey', varvalue='$varvalue'");
					prn($qr);
				}
			}
		}
		prn($errs);
		error_alert($err ? 'You have '.$err.' error'.($err>1?'s':'').'; you must use decimal numbers for utilities prices' : 'Utility prices have been updated');
	break;
	case $mode=='updateAllUnits':
		if(minroles()>ROLE_AGENT)$myproperties=list_properties();
		$prefix=array(
			'ApplicationFee'=>'p.',
			'CosignerFee'=>'p.',
		);
		$float=array(
			'Rent','Deposit','AdminFee','ApplicationFee','CosignerFee','PetDeposit','PetExtra'
		);
		$integer=array(
			'Quantity',
		);
		$checkboxes=array(
			'ElectricPaid','GasPaid','WaterPaid','TrashPaid','InternetPaid','Cable','PetsAllowed',
		);
		$data=array_transpose($data);
		unset($errs);
		foreach($data as $Units_ID=>$v){
			if(!$v['ID']){
				unset($data[$Units_ID]);
				continue;
			}
			$Properties_ID=q("SELECT Properties_ID FROM gl_properties_units WHERE ID=$Units_ID", O_VALUE);
			if(minroles()>ROLE_AGENT && !in_array($Properties_ID,$myproperties)){
				//not authorized
				mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='no access, should not happen'),$fromHdrBugs);
				continue;
			}
			foreach($checkboxes as $w)if(!isset($v[$w]))$v[$w]='0';
			
			unset($err);
			$sql="UPDATE gl_properties p, gl_properties_units u
			SET ";
			foreach($v as $o=>$w){
				if($o=='ID')continue;
				if($o=='Edit_Notes')continue;
				if((in_array($o,$float) && !preg_match('/^[0-9]+(\.[0-9]{2})*$/',$w))
					||
				   (in_array($o,$integer) && !preg_match('/^[0-9]+$/',$w))){
					$err[]=$o;
					break;
				}
				$sql.=$prefix[$o].$o.'=\''.$w.'\',';
			}
			if(count($err)){
				$errs[]=$Units_ID;
				continue;
			}

			$sql=rtrim($sql,',');
			$sql.=' WHERE p.ID=u.Properties_ID AND u.ID='.$Units_ID;

			//enter changes to values
			if($compare=q("SELECT ".'p.'.implode(', p.',$propertiesFields).', u.'.implode(', u.',$unitsFields)."
				FROM gl_properties p, gl_properties_units u WHERE p.ID=u.Properties_ID AND u.ID=$Units_ID", O_ROW)){
				foreach($compare as $o=>$w){
					if($w != $v[$o]){
						if(($w=='' && $v[$o]==0) || ($w==0 && $v[$o]==''))continue;
						q("INSERT INTO gf_modifications SET Objects_ID=".(in_array($o,$propertiesFields)?$Properties_ID:$Units_ID).", ObjectName='".(in_array($o,$propertiesFields)?'gl_properties':'gl_properties_units')."', SubObjectName='$o', Value='".addslashes($w)."', Creator='".sun()."', EditDate=NOW(), Notes='".$v['Edit_Notes']."'");
						prn($qr['query']);
					}
				}
			}else{
				mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='fail to find compare values'),$fromHdrBugs);
				continue; //deleted?
			}
			
			//make the query
			ob_start();
			q($sql, ERR_ECHO);
			$out=ob_get_contents();
			ob_end_clean();
			if($out)mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($out),$fromHdrBugs);
			prn($qr);
			?><script language="javascript" type="text/javascript">
			window.parent.g('c_<?php echo $Units_ID;?>').value='';
			</script><?php

		}
		?><script language="javascript" type="text/javascript">
		window.parent.g('submitStatus').style.visibility='hidden';
		</script><?php
		if($errs){
			error_alert('There were at least one error(s) in your updates.  All dollar values must be in the format 0.00, and quantity must be an integer 1 or greater.  If you  are having difficulty locating the error, try refreshing the page');
		}else{
			?><script language="javascript" type="text/javascript">
			window.parent.detectChange=0;
			</script><?php
		}
	break;
	case $mode=='updateClientData':
		require('../system_bulkmodifycompany_1.php');
	break;
	case $mode=='talklisten':
		//report in
		#mail($developerEmail,'fu','fu',$fromHdrBugs);
		break;
		q("UPDATE bais_logs SET lg_lastping=NOW() WHERE lg_stusername='".sun()."' AND lg_id='".$_SESSION['loginID']."'");
		if($qr['affected_rows']){

		}else{
			//disconnected from the login record
		}
		
		if($users=q("SELECT st_unusername, un_firstname, un_lastname
		FROM bais_logs l, bais_staff s, bais_universal u
		WHERE l.lg_stusername=s.st_unusername AND s.st_unusername=u.un_username AND
		DATE_ADD(lg_lastping, INTERVAL 20 SECOND) > NOW() GROUP BY u.un_username", O_ARRAY)){
			foreach($users as $v){
				echo '^'.implode('|',$v);
			}
		}
		
		$navigate=false;
	break;
	case $mode=='updateOffice':
	case $mode=='insertOffice':
		if($mode==$insertMode){
			$root=strtolower(substr(preg_replace('/[^0-9a-z]*/i','',$Address),0,16));
			$un_username=$oa_unusername=$of_oausername=sql_autoinc_text('bais_universal','un_username',$root);
		}else{
			$un_username=$of_oausername=$oa_unusername;
		}
		//error checking - type must be other than null, this could actually be complex and we want to give them "second chances" like on some of my other interfaces
		$fl=__FILE__; $ln=__LINE__;
		q(($mode==$insertMode ? "INSERT INTO":"UPDATE")." bais_universal SET
		un_username='$un_username',
		un_email='$Email',
		un_createdate='$dateStamp',
		un_creator='".$_SESSION['systemUserName']."'" . ($mode==$updateMode?" WHERE un_username='$un_username'":""));
		prn($qr);
		
		q(($mode==$insertMode ? "INSERT INTO":"UPDATE")." bais_orgaliases SET
		".($mode==$insertMode ? "oa_unusername='$oa_unusername',":'')."
		oa_orgcode=".(trim($oa_orgcode)?"'$oa_orgcode'":'NULL').",
		oa_org1='$oa_org1',
		oa_org2='$oa_org2',
		oa_businessname='$oa_businessname'". ($mode==$updateMode?" WHERE oa_unusername='$oa_unusername'":""));
		prn($qr);
		$sql=sql_insert_update_generic($MASTER_DATABASE,"bais_offices", $mode, $options);
		q($sql);
		prn($qr);

		if($PrimaryOffice){
			q("UPDATE bais_offices SET PrimaryOffice=IF(of_oausername='$oa_unusername',1,0)");
		}
		
		if($cbPresent){
			if($cbSelect=='fh_oausername'){
				$cbValue=$oa_unusername;
				$cbLabel=stripslashes($oa_businessname);
			}
			callback(array("useTryCatch"=>false));
		}
		$navigate=true;
		$navigateCount=$count+($mode==$insertMode ? 1 : 0);break;
	case $mode=='deleteOffice':
		//bottom up
		if(minroles()>ROLE_ADMIN){
			error_alert('You do not have authorization to delete an office location');
		}
		q("DELETE FROM bais_OfficesStaff WHERE os_unusername='$oa_unusername'");
		prn($qr);
		q("DELETE FROM bais_StaffOffices WHERE so_unusername='$oa_unusername'");
		prn($qr);
		q("DELETE FROM bais_offices WHERE of_oausername='$oa_unusername'");
		prn($qr);
		q("DELETE FROM bais_orgaliases WHERE oa_unusername='$oa_unusername'");
		prn($qr);
		q("DELETE FROM bais_universal WHERE un_username='$oa_unusername'");
		prn($qr);
		?><script language="javascript" type="text/javascript">
		window.parent.g('r_<?php echo $oa_unusername?>').style.display='none';
		</script><?php
	break;
	case $mode=='downloadFile':
		if(!q("SELECT ID FROM relatebase_tree WHERE ID=$Tree_ID AND Name='$file'", O_VALUE)){
			mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
			error_alert('File mismatch');
		}
		$path=$_SERVER['DOCUMENT_ROOT'].tree_id_to_path($Tree_ID);
		$nameAs=preg_replace('/^[a-z0-9]+_/i','',$file);
		header ("Accept-Ranges: bytes");  
		header ("Connection: close");  
		header ("Content-type: application/octet-stream");  
		header ("Content-Length: ". filesize($path));   
		header ("Content-Disposition: attachment; filename=\"$nameAs\"");
		
		readfile($path);
		$suppressNormalIframeShutdownJS=true;
		$assumeErrorState=false;
		exit;
	break;
	case $mode=='updatePreferences':
		//fill unpassed checkboxes..

		if(minroles() >= ROLE_AGENT)error_alert('You do not have permission to perform this task');
		if($a=q("SELECT AdministratorSettings FROM gf_account WHERE GCUserName='$GCUserName'", O_VALUE)){
			$a=unserialize(base64_decode($a));
		}else{
			$a=array();
		}
		if(minroles() <= ROLE_ADMIN && $_adminSettings[ROLE_ADMIN]){
			//admin settings
			foreach($_adminSettings[ROLE_ADMIN] as $n=>$v){
				if(!trim($v))error_alert('No field for Equivalent Titles can be blank');
			}
		}
		$AdministratorSettings=array(
			/* db administrator settings */
			ROLE_DBADMIN=>
			(minroles()<=ROLE_DBADMIN ? stripslashes_deep($_adminSettings[ROLE_DBADMIN]) : $a[ROLE_DBADMIN]),
			/* foundation director settings */
			ROLE_ADMIN=>
			(minroles()<=ROLE_ADMIN ? stripslashes_deep($_adminSettings[ROLE_ADMIN]) : $a[ROLE_ADMIN])
		);
		q("UPDATE gf_account SET AdministratorSettings='".base64_encode(serialize($AdministratorSettings))."' WHERE GCUserName='$GCUserName'");
		prn($qr);
		error_alert('Your settings have been updated');
	break;
	case $mode=='updatePreferencesSettingsNodes':
		if(count($settingsNodes)){
			foreach($settingsNodes as $vargroup=>$v){
				foreach($v as $varnode){
					if($a=$_POST[$varnode]){
						foreach($a as $varkey=>$varvalue){
							q("REPLACE INTO bais_settings SET UserName='".sun()."', vargroup='$vargroup', varnode='$varnode', varkey='$varkey', varvalue='$varvalue'");
							prn($qr);
						}
					}
				}
			}
		}
		?><script language="javascript" type="text/javascript">
		if(confirm('Settings updated; would you like to reload them? (You will not see changes unless you do this or log out)')){
			window.parent.location='/gf5/console/preferences.php?refreshUserSettings=1';
		}
		</script><?php
	break;
	case $mode=='setVoid':
		//added 2012-04-28
		if(minroles()>ROLE_ADMIN)error_alert('You do not have access to this');
		$Headers_ID=q("SELECT t.Headers_ID FROM finan_transactions t, gl_LeasesTransactions lt WHERE lt.Transactions_ID=t.ID AND lt.Leases_ID=$Leases_ID", O_VALUE);
		if($setVoid==1){
			if(!$GLF_VoidReasons_ID)error_alert('You must specify a reason for voiding this invoice');
			if(q("SELECT tt.ChildTransactions_ID FROM finan_transactions t, finan_TransactionsTransactions tt WHERE t.Headers_ID=$Headers_ID AND t.ID=tt.ChildTransactions_ID", O_VALUE))error_alert('This invoice cannot be voided!  It has had a payment applied to it.  You must first delete the payment to void this invoice.  Press Ctrl-H to get a history of payment(s) for this invoice');
			//void and zero out transactions - leave accounts_id intact
			q("UPDATE finan_headers SET HeaderStatus='Void', GLF_VoidReasons_ID=$GLF_VoidReasons_ID WHERE ID=$Headers_ID");
			q("UPDATE finan_transactions SET Extension=0, UnitPrice=0 WHERE Headers_ID=$Headers_ID");
		}else if($setVoid==='0'){
			q("UPDATE finan_headers SET HeaderStatus='Current', GLF_VoidReasons_ID=NULL WHERE ID=$Headers_ID");
		}
		?><script language="javascript" type="text/javascript">
		var l=window.parent.location+'';
		window.parent.location=l;
		</script><?php
	break;
	case $mode=='uploadRelativeFile':
	case $mode=='downloadRelativeFile':
	case $mode=='createRelativeFolder':
	case $mode=='deleteRelativeFile':
		require('../intranet.php');
	break;
	case $mode=='submitPayment':
		unset($payment);
		if(!count($ApplyTo))error_alert('no payments shown');
		if(array_sum($ApplyTo)==0)error_alert('You have not specified any amount(s) to pay!');
		$a=q("SELECT Headers_ID, Properties_ID, OriginalTotal, AmountApplied
		FROM _v_leases_master
		WHERE Headers_ID IN(".implode(',',array_keys($ApplyTo)).")", O_ARRAY_ASSOC);
		if(count($a)!==count($ApplyTo)){
			mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='Abnormal error, unable to fetch payment info from database; an admin has been notified.'),$fromHdrBugs);
			error_alert($err);
		}
		foreach($ApplyTo as $Headers_ID=>$amt){
			//get info
			if($amt==0)continue;
			if(!preg_match('/^[.0-9]+$/',$amt))error_alert('Your payment amount(s) must be a valid number');
			$due=abs($a[$Headers_ID]['AmountApplied'] + $a[$Headers_ID]['OriginalTotal']);
			if($due<=0){
				mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='Abnormal error, non-due or paid invoice showing on list'),$fromHdrBugs);
				error_alert($err);
			}
			if($amt>$due)error_alert('You cannot pay more than is due on an invoice');
			$payments[$a[$Headers_ID]['Properties_ID']][$Headers_ID]=$amt;
		}
		//now process payment
		$CCConfirmationNumber=12345;
		
		//now enter payments in db
		$insertMode='insertPayment';
		$mode='insertPayment';
		$Types_ID=3;
		$alpha=64;
		$Notes='Client comment: '.$Notes;
		$suppressNavigate=true;
		foreach($payments as $Properties_ID=>$ApplyTo){
			extract(q("SELECT p.Clients_ID, PrimaryFirstName, PrimaryLastName, PropertyName FROM finan_clients c, gl_properties p WHERE c.ID=p.Clients_ID AND p.ID=$Properties_ID", O_ROW));
			$DateCredited=date('m/d/Y');
			$Amount=$Total=array_sum($ApplyTo);
			$alpha++;
			$HeaderNumber=$CCConfirmationNumber . (count($payments)>1 ? chr($alpha) : '');
			require(__FILE__);
			//this is the basic coding I have for sending emails
			$invoices=q("SELECT HeaderNumber FROM _v_leases_master WHERE Headers_ID IN(".implode(',',array_keys($ApplyTo)).")", O_COL);
			$lastInvoice=array_pop($invoices);
			$invoices=implode(', ',$invoices).(count($invoices) ? ' and ':'').$lastInvoice;
			$allInvoices[]=$invoices;
			$emailTo=$developerEmail;
			$emailSource='../emails/email_01_payment_receipt_payer.php';
			require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
		}
		$properties=q("SELECT PropertyName FROM _v_leases_master WHERE Properties_ID IN(".implode(',',array_keys($payments)).")", O_COL);
		$lastProperty=array_pop($properties);
		$properties=implode(', ',$properties).(count($properties) ? ' and ':'').$lastProperty;

		$emailTo=$developerEmail;
		$emailSource='../emails/email_02_payment_receipt_admin.php';
		require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
		//redirect them to completion which self-truncates
		$_SESSION['special']['payment']=array(
			'_POST'=>$_POST,
			'ConfirmationNumber'=>$CCConfirmationNumber,
		);
		?><script language="javascript" type="text/javascript">
		window.parent.location='/gf5/console/home.php?section=OnlinePayment&node=done';
		</script><?php
		error_alert('done');
	break;
	case $mode=='insertSupportContact':
	case $mode=='updateSupportContact':
	case $mode=='deleteSupportContact':
		$email=array();
		if($mode==$updateMode && minroles()>ROLE_ADMIN)error_alert('You do not have access to edit a support ticket');
		if(!$_POST['RequestType'])error_alert('Please enter a request type');
		if(!$_POST['Summary'])error_alert('Please enter at lease a summary of the request/problem');
		
		//enter in database
		if(!$Status)$Status='Submitted';
		if($mode==$updateMode){
			unset($UserName);
		}else{
			$UserName=sun();
		}
		if($Status!=$OriginalStatus)$StatusDate=date('Y-m-d H:i:s');
		$Notify=implode(',',$Notify);
		$sql=sql_insert_update_generic('cpm180',"gf_supporttickets", $mode, $options);
		$Supporttickets_ID=q($sql, O_INSERTID);
		prn($qr);
		
		$Notify=explode(',',$Notify);
		if($mode==$insertMode){
			if($Notify['Developer'] || true){
				$systemEmail['to'][]=$developerEmail;
			}
			if($Notify['Staff']){
				if($_SESSION['admin']['roles'][ROLE_CASE_MANAGER]!=''){
					$systemEmail['to'][]=q("SELECT un_email FROM bais_universal WHERE un_username='".$apSettings['adminUserName']."'" , O_VALUE);
				}
			}	
			$systemEmail['to']=implode(',',$systemEmail['to']).','.$_SESSION['admin']['email'];
			$emailSource=$_SERVER['DOCUMENT_ROOT'].'/gf5/console/emails/email_03_submitticket.php';
			require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
		}else if($mode==$updateMode && $Update){
			
			$systemEmail['to'][]=$developerEmail;
			
			if(trim($NotifyEmail))$systemEmail['to'][]=$NotifyEmail;
			$record=q("SELECT * FROM cpm180.gf_supporttickets WHERE ID=$ID", O_ROW);
			$creator=q("SELECT * FROM cpm180_".$record['GCUserName'].".bais_universal WHERE un_username='".$record['UserName']."'", O_ROW);
			prn($qr);
			$GCUserName=$record['GCUserName'];
			extract(q("SELECT AcctCompanyName FROM cpm180.gf_account WHERE GCUserName='$GCUserName'", O_ROW));
			if($creator['un_email'])$systemEmail['to'][]=$creator['un_email'];
			$systemEmail['to']=implode(',',$systemEmail['to']);
			$emailSource=$_SERVER['DOCUMENT_ROOT'].'/gf5/console/emails/email_03_replyticket.php';
			require($MASTER_COMPONENT_ROOT.'/emailsender_03.php');
		}
		?><script language="javascript" type="text/javascript">
		alert('<?php echo $mode==$insertMode? 'Your support ticket has been submitted' : ($mode==$updateMode && $Update ? 'Your response has been sent' : '');?>');
		window.parent.close();
		</script><?php
	break;
	case $mode=='dismissBulletin':
		q("REPLACE INTO gf_UniversalBulletins SET un_username='".sun()."', Bulletins_ID=$Bulletins_ID, Status='Dismissed'");
		?><script language="javascript" type="text/javascript">
		alert('Bulletin has been dismissed');
		window.parent.g('r_<?php echo $Bulletins_ID?>').style.display='none';
		</script><?php
	break;
	case $mode=='sendFeedback':
		//get the parent info and article info
		$article=q("SELECT * FROM gf_bulletins WHERE ID=$Bulletins_ID", O_ROW);
		$writer=q("SELECT * FROM bais_universal WHERE un_username='".$article['bl_unusername']."'", O_ROW);
		$sender=q("SELECT * FROM bais_universal WHERE un_username='".sun()."'", O_ROW);
		$emailTo=($writer['un_email'] ? $writer['un_email'] : 'bugreports@'.$applicationDomainName.','.$developerEmail);
		$emailSubj=($writer['un_email'] ? stripslashes($fbsubject) : '(no email present for '.$writer['un_username'].')');
		$emailFrom='bulletins@'.$applicationDomainName;
		?><script language="javascript" type="text/javascript">
		window.parent.g('fbsubject').value='';
		window.parent.g('fbbody').value='';
		alert('Mail sent successfully');
		</script><?php
	break;
	case $mode=='insertItem':
	case $mode=='updateItem':
	case $mode=='deleteItem':
		if($mode==$deleteMode || $mode=='deleteItem'){
			if(minroles()>ROLE_ADMIN)error_alert('You must be an administrator to delete a record');
			$SKU=q("SELECT SKU FROM finan_items WHERE ID=".($_ID?$_ID:$ID),O_VALUE);
			q("DELETE FROM finan_items WHERE ID=".($_ID?$_ID:$ID));
			$source=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/master/'.strtoupper($SKU).'.jpg';
			$target=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.strtoupper($SKU).'.jpg';
			unlink($source);
			unlink(str_replace($SKU,'.thumbs.dbr/'.$SKU,$source));
			unlink($target);
			unlink(str_replace($SKU,'.thumbs.dbr/'.$SKU,$target));
			?><script language="javascript" type="text/javascript">
			try{
			<?php if($_ID){ ?>window.parent.g('r_<?php echo $_ID;?>').style.display='none';<?php }else{ ?>//window.parent.close();<?php } ?>
			}catch(e){ }
			</script><?php
			
			$navigate=true;
			$navigateCount=$count - 1;
			break;
		}

		$toline=__LINE__;
		//2013-03-28: autosave, passes 2nd param to error_alert, if in autosave unset the field
		if(!function_exists('eau')){
			function eau($str=''){
				global $autosave;
				if($autosave)return $str;
			}
		}


		if(!$autosave){
			$ResourceType=1;
			if($mode==$insertMode){
				$a=q("SELECT Grouping, COUNT(*) FROM finan_items WHERE ResourceType IS NOT NULL GROUP BY Grouping ORDER BY Grouping", O_COL_ASSOC);
				foreach($a as $MaxGroup=>$Count){
					//ok
				}
				$Grouping=$MaxGroup + ($Count>=200?1:0);
			}
		}

		//common error checking and data translation
		//added 2012-08-28
		if(!($merchantUPCCode=q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='items' AND varnode='settings' AND varkey='merchantUPCCode'", O_VALUE)))
		/* we alert them even on autosave as the post would not be accepted anyway */
		error_alert('You have not set up a merchant UPC code.  First go to Home > Preferences in the main window and set this up');
		$HMR_UPCCheckDigit='';
		for($i=0;$i<=9;$i++){
			$HMR_UPCCheckDigit.=UPC_checkdigit(str_pad($merchantUPCCode,6,'0',STR_PAD_LEFT).str_pad($ID,4,'0',STR_PAD_LEFT). $i, array('return'=>'digit'));
		}

		$toline=__LINE__;

		if($mode==$updateMode && !(minroles()<=ROLE_ADMIN || sun()==q("SELECT Creator FROM finan_items WHERE ID=$ID", O_VALUE)))error_alert('You must be an administrator to update a record, OR the creator of that record.');
		if($mode==$updateMode && q("SELECT IsLocked FROM finan_items WHERE ID=$ID", O_VALUE))error_alert('This record is locked and can only be edited/deleted after it has been unlocked; if you are an administrator, select "Unlock Records" on the home page and follow the instructions given to unlock a product.');

		$toline=__LINE__;

		//data conversion & error checking
		$MetaTitle=str_replace("\n",',',trim($MetaTitle));
		$MetaTitle=preg_replace('/[,]{2,}/',',',$MetaTitle);
		$MetaDescription=str_replace("\n",',',trim($MetaDescription));
		$MetaDescription=preg_replace('/[,]{2,}/',',',$MetaDescription);
		$MetaKeywords=str_replace("\n",',',trim($MetaKeywords));
		$MetaKeywords=preg_replace('/[,]{2,}/',',',$MetaKeywords);
		
		if(!$MIVA_ToBeExported)$MIVA_ToBeExported='0';
		if(!$EBAY_ToBeExported)$EBAY_ToBeExported='0';
		if(!$AMAZON_ToBeExported)$AMAZON_ToBeExported='0';
		if(!$HBS_YearEstimated)$HBS_YearEstimated='0';
		if(strlen($HBS_Year) && !preg_match('/^[1-2][0-9]{2}(0s|[0-9])$/',$HBS_Year))
		error_alert('Year of picture, if indicated, must be an integer year, or ending with 0s, for example 1930s',eau('HBS_Year'));
		if(!strlen($HBS_Year) && $HBS_YearEstimated)
		error_alert('Uncheck "estimated" next to the year unless you enter an actual value for the year',eau('HBS_Year,HBS_YearEstimated'));
		//product code per Andreas Toman
		if(!strlen($SEO_Filename))
		error_alert('The SEO URL is REQUIRED; it also serves as the product code in MIVA', eau('SEO_Filename'));
		if(preg_match('/jpg|html/i',$SEO_Filename))
		error_alert('The SEO URL cannot contain the strings "jpg" or "html"',eau('SEO_Filename'));
		if(strlen($SEO_Filename))$SEO_Filename=str_replace(' ','-',$SEO_Filename);
		if(strlen($SEO_Filename))$SEO_Filename=preg_replace('/-{2,}/','-',$SEO_Filename);
		if(!preg_match('/^[-0-9a-z]+$/i',$SEO_Filename))
		error_alert('The SEO URL may only contain a dash (-), a-z, and 0-9',eau('SEO_Filename'));
		if($match=q("SELECT * FROM finan_items WHERE LEFT(SEO_Filename,50)='".substr($SEO_Filename,0,50)."' AND ID!=$ID", O_ROW))error_alert('The SEO URL must be unique in its first 50 characters',eau('SEO_Filename'));
		if(strlen($SEO_Filename))$SEO_Filename=strtolower($SEO_Filename);

		$toline=__LINE__;

		$required=array('MetaTitle','MetaDescription','MetaKeywords', 'Name','Description','Category');
		foreach($required as $v)if(!strlen($$v))error_alert($v.' is required',eau($v));

		if($GCUserName=='art'){			
			#SKU
			if($mode==$insertMode){
				$SKU1=strtoupper($SKU1);
				if(!$SKU1)$_err1_=error_alert('Enter the first part of the SKU',eau('SKU1'));
				if(!preg_match('/^[A-Z]{2}$/',$SKU1))$_err1_=error_alert('Enter the first part of the SKU; it must be two capital letters',eau('SKU1'));
				if($SKU3=='(auto)'){
					$max=q("SELECT MAX(SUBSTRING(SKU,3,3)) FROM finan_items WHERE SKU!='' AND SUBSTRING(SKU,1,2)='$SKU1'", O_VALUE);
					$max=preg_replace('/^0+/','',$max) + 1;
					$SKU3=str_pad($max,3,'0',STR_PAD_LEFT);
				}else if(!preg_match('/^[0-9]{3}$/',$SKU3)){
					$_err2_=error_alert('Enter the second part of the SKU as a 3-digit number',eau('SKU3'));
				}else{
					if(q("SELECT COUNT(*) FROM finan_items WHERE SKU='$SKU1$SKU3'", O_VALUE))$_err3_=error_alert('That SKU is already in use',eau('SKU1,SKU3'));
				}
				if(!$_err1_ && !$_err2_ && !$_err3_)$SKU="$SKU1$SKU3";
			}else{
				//we just have the SKU
				$SKU=strtoupper($SKU);
				if($SKU!=$OriginalSKU && q("SELECT COUNT(*) FROM finan_items WHERE SKU='$SKU' AND SKU IS NOT NULL AND ID!='$ID'", O_VALUE))error_alert('You have changed the SKU, however this is a duplicate SKU number',eau('SKU'));
				if(!preg_match('/^[A-Z]{2}[0-9]{3}$/',$SKU))error_alert('SKU must be 2 capital letters followed by 3 numbers',eau('SKU'));
			}
			if($FileName)$HBS_OriginalFileName=$FileName;
		}else if($GCUserName=='hmr'){
			$ThumbData=base64_encode(serialize($ThumbData));
			
			//added 2012-11-12
			if(q("SELECT COUNT(*) FROM aux_states WHERE st_country='Canada' AND st_name='$PrimaryRegion'", O_VALUE, $public_cnx) && $PrimaryCountry!=='Canada' && $PrimaryCountry!=='North America')error_alert('You selected a Canadian province ay.  You need to select Canada or North America as the country or select a different region',eau('PrimaryRegion,PrimaryCountry'));
			if(q("SELECT COUNT(*) FROM aux_states WHERE st_country='United States' AND st_name='$PrimaryRegion'", O_VALUE, $public_cnx) && $PrimaryCountry!=='United States')error_alert('You selected one of the 50 states or DC.  You need to select United States as the country or select a different region',eau('PrimaryRegion,PrimaryCountry'));
			
			#SKU
			if($mode==$insertMode){
				$SKU1=strtoupper($SKU1);
				$SKU2=strtoupper($SKU2);
				if(!$SKU1)
				$_err1_=error_alert('Enter the first part of the SKU',eau('SKU1'));
				if(strlen($SKU1)<4 && !preg_match('/^[A-Z]{2}$/',$SKU2))
				$_err2_=error_alert('Enter the second part of the SKU; it must be two capital letters',eau('SKU2'));
				if(strlen($SKU1)==4)$SKU2='';
				if($SKU3=='(auto)' || !trim($SKU3)){
					$max=q("SELECT MAX(SUBSTRING(SKU,5,4)) FROM finan_items WHERE SKU!='' AND SUBSTRING(SKU,1,4)='$SKU1$SKU2' AND ID!='$ID'", O_VALUE);
					$max=preg_replace('/^0+/','',$max) + 1;
					$SKU3=str_pad($max,4,'0',STR_PAD_LEFT);
				}else if(!preg_match('/^[0-9]{4}$/',$SKU3)){
					$_err3_=error_alert('Enter the third part of the SKU as a 4-digit number',eau('SKU3'));
				}else{
					if(q("SELECT COUNT(*) FROM finan_items WHERE SKU='$SKU1$SKU2$SKU3' AND ID!='$ID'", O_VALUE))
					$_err4_=error_alert('That SKU is already in use',eau('SKU'));
				}
				if(!$_err1_ && !$_err2_ && !$_err3_ && !$_err4_)$SKU="$SKU1$SKU2$SKU3";
				if($autosave)$SKU='';
			}else{
				//we just have the SKU
				$SKU=strtoupper($SKU);
				if($SKU!=$OriginalSKU && q("SELECT COUNT(*) FROM finan_items WHERE SKU='$SKU' AND SKU IS NOT NULL AND SKU='' AND ID!='$ID'", O_VALUE))error_alert('You have changed the SKU, however this is a duplicate SKU number');
				if(!preg_match('/^[A-Z]{4}[0-9]{4}$/',$SKU))error_alert('SKU must be 4 capital letters followed by 4 numbers',eau('SKU'));
			}
			if($FileName)$HBS_OriginalFileName=$FileName;
			
			$Lat3=0;
			if(trim($Lat1)){
				//calculate centroid
				$a=explode('|',$Lat1);
				foreach($a as $i=>$v)$a[$i]=explode(',',$v);
				if(count($a)==1)error_alert('You cannot select just a single point for the latitude/longitude of a map.  Click the Location tab, and click to add more points, or click clear to remove all points',eau('Lat1'));
	
				/*
				figure out roughly where we are and what a degree of latitude and longitude mean, and convert
				latitude = left to right
				longitude goes through poles
				//http://paulbourke.net/geometry/polyarea/
				//http://www.ncgia.ucsb.edu/giscc/units/u014/u014.html	
				
				*/
				if(count($a)==2){
					$a=array(
						0=>array($a[0][0],$a[0][1]),
						1=>array($a[0][0],$a[1][1]),
						2=>array($a[1][0],$a[1][1]),
						3=>array($a[1][0],$a[0][1]),
					);
					$Lat1=$a[0][0].','.$a[0][1].'|'.$a[1][0].','.$a[1][1].'|'.$a[2][0].','.$a[2][1].'|'.$a[3][0].','.$a[3][1];
				}
	
				//first we get the approximate mid lat and mid lon for scaling
				foreach($a as $i=>$v){
					list($py,$px)=$v;
					//simpler
					$xsum+=$px;
					$ysum+=$py;
				
					//this is more advanced
					$xmax=max(abs($px),$xmax);
					$xmin=min(abs($px),$xmin?$xmin:180);
					$ymax=max(abs($py),$ymax);
					$ymin=min(abs($py),$ymin?$ymin:90);
				}
				//these are expressed in true lat/lon BUT may not have correct signs
				$xmid=abs($xmax-$xmin)/2;
				$ymid=abs($ymax-$ymin)/2;
				
				$xmidavg=$xsum/count($a);
				$ymidavg=$ysum/count($a);
				
				$xmultiplier=cos(deg2rad($ymid + $ymin))*69.0;
				$ymultiplier=69.3;
				
				$xexpanse=abs($xmax-$xmin)*$xmultiplier;
				$yexpanse=abs($ymax-$ymin)*$ymultiplier;
				$maxdim=max($xexpanse,$yexpanse);
				//google zoom scale  miles per pixel = 73.5294 * e ^ -Zk  (where k=.69534) calc'd by Sam
				$_dim=750;
				for($i=12;$i>=0;$i--){
					//figured from a 500x500 canvas
					$dim=$_dim * 73.5294 * pow(M_E, (-1 * $i * .69534));
					if($dim > $maxdim*1.1){
						$Lat3=$i;
						break;
					}
				}
				#error_alert('zoom scale is: '.$Lat3,1);
			
				foreach($a as $i=>$v){
					list($py,$px)=$v;
					list($py_next,$px_next)=$a[($i+1)%count($a)];
					$apoly += .5 * ($px*$py_next - $px_next*$py)*$xmultiplier*$ymultiplier;
				}
				$apoly=abs($apoly);
				//now centroid
				foreach($a as $i=>$v){
					list($py,$px)=$v;
					list($py_next,$px_next)=$a[($i+1)%count($a)];
	
					$cx+=1/6 /$apoly * ($px + $px_next)*$xmultiplier*($px*$py_next - $px_next*$py)*$xmultiplier*$ymultiplier;
					$cy+=1/6 /$apoly * ($py + $py_next)*$ymultiplier*($px*$py_next - $px_next*$py)*$xmultiplier*$ymultiplier;
				}
				//key coords to the last point - fine in most cases
				$cx=abs($cx)*($px<0?-1:1);
				$cy=abs($cy)*($py<0?-1:1);
				$Lat2=($cx/$xmultiplier).','.($cy/$ymultiplier);
			}
		}else{
			error_alert('Account username not recognized');
		}

		$toline=__LINE__;
		
		if($mode==$insertMode){
			//2012-11-14: keeps the two dates in synch for new entries!
			$EditDate='MYSQL:NOW()';
			$CreateDate='MYSQL:NOW()';
		}

		$toline=__LINE__;
		

		//added 2013-04-10
		if($SKU=='')unset($SKU);

		$sql=sql_insert_update_generic($MASTER_DATABASE,'finan_items', 'UPDATE', '', $options);
		if($autosave){
			/*
			ob_start();
			print_r($sql);
			$out=ob_get_contents();
			ob_end_clean();
			*/
		}else prn($sql);

		$toline=__LINE__;

		q($sql, O_INSERTID);
		prn($qr);

		$toline=__LINE__;
		
		if($autosave)break;
		
		$navigate=true;
		$navigateCount=$count + ($mode==$insertMode?1:0);
		if($mode==$insertMode && $FileName){
			//move from pending_master/oldfilename.jpg -> ../master/sku.jpg
			rename(
				$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master/'.stripslashes($FileName),
				$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/master/'.$SKU.'.jpg'
			);
			//move from pending/oldfilename.jpg -> ../sku.jpg
			rename(
				$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending/'.stripslashes($FileName),
				$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$SKU.'.jpg'
			);
			
			?><script language="javascript" type="text/javascript">
			try{
			window.parent.opener.g('r_<?php echo md5(stripslashes($FileName));?>').style.display='none';
			}catch(e){ }
			</script><?php 
		}else if($mode==$updateMode && $OriginalSKU!=$SKU){
			//move from pending_master/oldfilename.jpg -> ../master/sku.jpg
			rename(
				$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$OriginalSKU.'.jpg',
				$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$SKU.'.jpg'
			);
			//move from pending/oldfilename.jpg -> ../sku.jpg
			rename(
				$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/master/'.$OriginalSKU.'.jpg',
				$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/master/'.$SKU.'.jpg'
			);
			error_alert('The file '.$OriginalSKU.' has also been renamed to '.$SKU,1);
		}
		if(!$autosave && is_uploaded_file($_FILES['uploadFile1']['tmp_name'])){
			if(!($g=getimagesize($_FILES['uploadFile1']['tmp_name'])))error_alert('Unable to get image size for uploaded file');
			if($g[0]<=1500 && $g[1]<=1500){
				//this is the thumb
				move_uploaded_file($_FILES['uploadFile1']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.strtoupper($SKU).'.jpg');
				error_alert('You have uploaded a file that is interpreted as a "thumbnail".  You still need to upload the main map picture to handle digital downloads',1);
			}else{
				$g2=($g[0]>$g[1] /*landscape*/ ? '10000x900' : '900x10000');

				if($g[0]>$g[1]){
					$max=0;$min=1;
				}else{
					$min=0;$max=1;
				}
				$divisor=$g[$min] / 23;
				$overflow=$g[$max]/$divisor;
				
				$source=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/master/'.strtoupper($SKU).'.jpg';
				$target=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.strtoupper($SKU).'.jpg';
				move_uploaded_file($_FILES['uploadFile1']['tmp_name'], $source);
				$str="convert -size $g2 \"$source\" -resize $g2 +profile '*' \"$target\"";			
				$result=`$str`;
				q("UPDATE finan_items SET
				Width1='".$g[0]."',
				Height1='".$g[1]."',
				FileSize='".filesize($source)."',
				DPI1='".$divisor."' WHERE ID=$ID");
			}
		}
		
		//first implemented in Maps Ahead on 2012-08-09
		if($mode==$insertMode){
			//translate vars
			$Items_ID=$ID;
			$item=q("SELECT * FROM finan_items WHERE ID='$ID'", O_ROW);

			//declare the sendObject key
			$sendObject='new_map';
			require($COMPONENT_ROOT.'/comp_00_sendlogic_v102.php');
		}

		if($navMode=='insert'){
			?><script language="javascript" type="text/javascript">
			try{
			window.parent.g('creatingNew').style.display='none'; }catch(e){ }
			try{
			window.parent.g('imgWarning').style.display='none'; }catch(e){ }
			try{
			window.parent.g('imgViewport').innerHTML=' '; }catch(e){ }
			</script><?php
		}else if($navMode=='kill'){
			if($mode==$insertMode)error_alert('The new SKU number for this file is '.$SKU,1);
		}



	break;
	case $mode=='insertTheme':
	case $mode=='updateTheme':
	case $mode=='deleteTheme':
		if($mode=='deleteTheme'){
			if(q("SELECT COUNT(*) FROM finan_items WHERE Name='$Name'", O_VALUE))error_alert('This theme value is used and cannot be deleted');
			if($mode==$deleteMode){error_alert('not developed');}
			q("DELETE FROM finan_items_themes WHERE Name='$Name'");
			?><script language="javascript" type="text/javascript">
			window.parent.g('r_<?php echo md5(stripslashes($Name));?>').style.display='none';
			</script><?php
			break;
		}
		if(!strlen($Name))error_alert('Theme name must be present');
		if(!strlen($Description))error_alert('Description must be present');
		if(($mode==$insertMode || ($mode==$updateMode && strtolower($Name)!=strtolower($OriginalName)))&& q("SELECT Name FROM finan_items_themes WHERE Name='$Name'", O_VALUE))error_alert('This is a duplicate theme name');
		if($mode==$updateMode){
			q("UPDATE finan_items_themes SET Name='$Name',Description='$Description' WHERE Name='$OriginalName'");
			prn($qr);
			q("UPDATE finan_items SET Theme='$Name' WHERE Theme='$OriginalName'");
			prn($qr);
		}else{
			q("INSERT INTO finan_items_themes SET Name='$Name',Description='$Description'");
			prn($qr);
		}
		?><script language="javascript" type="text/javascript">
		window.parent.opener.themes['<?php echo str_replace('\'','\\\'',stripslashes($Name));?>']='<?php echo str_replace('\'','\\\'',stripslashes($Description));?>';
		</script><?php
		if($cbPresent){
			if($cbSelect=='Theme'){
				$ID=stripslashes($Name);
				$cbValue=stripslashes($Name);
				$cbLabel=stripslashes($Name);
			}
			callback(array("useTryCatch"=>false));
		}
		$navigate=false;
	break;
	case $mode=='recalcPrices':

		if($Width1>$Height1){
			$max='Width1';$min='Height1';
		}else{
			$min='Width1';$max='Height1';
		}
		$divisor=$$min / 23;
		$overflow=$$max/$divisor;

		$HMR_Price1= round(((23 * $overflow) * PRINT_BASIC) + PRINT_BASIC_MARKUP,2);
		if(fmod($HMR_Price1*100,2))$HMR_Price1+=.01;

		$HMR_Price2= round((23 * $overflow) * PRINT_LAMINATED,2);
		if(fmod($HMR_Price2*100,2))$HMR_Price2+=.01;

		$HMR_Price3= round((23 * $overflow) * PRINT_GICLEE,2);
		if(fmod($HMR_Price3*100,2))$HMR_Price3+=.01;

		$HMR_Price4= round((23 * $overflow) * PRINT_CANVAS,2);
		if(fmod($HMR_Price4*100,2))$HMR_Price4+=.01;


		?><script language="javascript" type="text/javascript">
		window.parent.g('HMR_Price1').value='<?php echo number_format($HMR_Price1,2);?>';
		window.parent.g('HMR_Price2').value='<?php echo number_format($HMR_Price2,2);?>';
		window.parent.g('HMR_Price3').value='<?php echo number_format($HMR_Price3,2);?>';
		window.parent.g('HMR_Price4').value='<?php echo number_format($HMR_Price4,2);?>';
		</script><?php
	break;
	case $mode=='importManager':
		$refreshComponentOnly=true;
		require($_SERVER['DOCUMENT_ROOT'].'/gf5/console/components/comp_900_importmanager_v104.php');
	break;
	case $mode=='exportManager': /*2012-07-04*/
		require('../components/comp_exportmanager_v101.php');
	break;
	case $mode=='deleteMapPending':
		//make sure someone is not working on the map
		if(minroles()>ROLE_ADMIN)error_alert('You must be an administrator to delete a file');
		if($a=q("SELECT ID, SKU, Creator, IsLocked, ResourceType FROM finan_items WHERE HBS_OriginalFileName='$FileName'", O_VALUE)){
			//an administrator can do this, or the person who is working on the map
			extract($a);
			sleep(2);
			?><script language="javascript" type="text/javascript">
			window.parent.g('deleteMapPending_<?php echo md5(stripslashes($FileName));?>').innerHTML='';
			</script><?php
			if($ResourceType==1)error_alert('This file has already been processed into the products database; normally this means it has just recently been processed and you have not refreshed the file list since then.  To delete the product, or replace the file, do a search for Part number "'.$SKU.'" and delete by pressing Ctrl-D');
		}
		unlink($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master/'.stripslashes($FileName));
		unlink($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master/.thumbs.dbr/'.stripslashes($FileName));
		unlink($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending/'.stripslashes($FileName));
		unlink($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending/.thumbs.dbr/'.stripslashes($FileName));
		?><script language="javascript" type="text/javascript">
		window.parent.g('deleteMapPending_<?php echo md5(stripslashes($FileName));?>').innerHTML='';
		window.parent.g('r_<?php echo md5(stripslashes($FileName));?>').style.display='none';
		</script><?php
		eOK(__LINE__);
	break;
	case $mode=='clearLinkManager':
		require($COMPONENT_ROOT.'/comp_9100_link_manager_v100.php');
		?><script language="javascript" type="text/javascript">
		window.parent.g('linkManager').innerHTML=document.getElementById('linkManager').innerHTML;
		</script><?php
	break;
	case $mode=='exportSummaryUpdate':
		foreach($change as $ID=>$v){
			if(!$v)continue;
			$sql="UPDATE finan_items SET EditDate=EditDate, MIVA_ToBeExported=".$MIVA_ToBeExported[$ID].", AMAZON_ToBeExported=".$AMAZON_ToBeExported[$ID].", EBAY_ToBeExported=".$EBAY_ToBeExported[$ID]." WHERE ID=$ID";
			q($sql);
			$s+=$qr['affected_rows'];
		}
		error_alert('Total of '.$s.' record'.($s>1?'s':'').' changed');
	break;
	case $mode=='getStatePointPerimeter':
	
	break;
	case $mode=='getCountyPointPerimeter':
		if(strstr($FIPS,'-')){
			//get the PointPerimeter of the county
			$a=explode('-',$FIPS);
			$a= q("SELECT PointPerimeter, Latitude, Longitude FROM aux_counties WHERE StateID=".$a[0]." AND CountyID=".$a[1], O_ROW, $public_cnx);
			$center='"center":{"lng":"'.$a['Longitude'].'","lat":"'.$a['Latitude'].'"}';
			echo '{"points":['.$a['PointPerimeter'].'],'.$center.',"zoom":10}';
		}else{
			$a=trim(q("SELECT st_PointPerimeter2 FROM aux_states WHERE st_stateid='$FIPS'", O_VALUE, $public_cnx));

			$b=current(explode('}',$a));
			$b=preg_replace('/[^-.0-9,]+/','',$b);
			$b=explode(',',$b);
			$center='"center":{"lng":"'.$b[0].'","lat":"'.$b[1].'"}';
			echo '{"points":'.$a.','.$center.',"zoom":7}';
		}
		$suppressNormalIframeShutdownJS=true;
		eOK();
	break;
	case $mode=='insertObject':
	case $mode=='updateObject':
	case $mode=='deleteObject':
		require('../systementry.php');
	break;
	//note plural
	case $mode=='insertObjects':
	case $mode=='updateObjects':
	case $mode=='deleteObjects':
		require('../components/comp_1000_systementry_dataobject_v100.php');
	break;
	case $mode=='delInProcess':
		if(!($a=q("SELECT * FROM finan_items WHERE ResourceToken='$ResourceToken' AND ResourceToken!=''", O_ROW)))error_alert('Unable to find resource for deletion');
		if($a['Creator']!==sun() && minroles()>ROLE_ADMIN)error_alert('This is not your file or you do not have authority to remove this record');
		if($a['ResourceType']=='1')error_alert('This file has already been submitted! You cannot delete it this way.  You will need to delete the record as you would a processed file');
		q("DELETE FROM finan_items WHERE ResourceToken='$ResourceToken'");
		?><script language="javascript" type="text/javascript">
		window.parent.g('p_<?php echo $ResourceToken;?>').style.display='none';
		</script><?php
	break;
	case $mode=='_k_deleteName': /*this is just a test with K*/
		q("DELETE FROM k_names WHERE ID=$ID");
		?><script language="javascript" type="text/javascript">
		window.parent.g('r_<?php echo $ID;?>').style.display='none';
		</script><?php
		error_alert('name successfully delete');
	break;
	case $mode=='_k_insertName': 
		$ID=q("INSERT INTO k_names SET FirstName='$FirstName',LastName='$LastName'",O_INSERTID);
		?><script language="javascript" type="text/javascript">
		alert('Record id <?php echo $ID;?> successfully created');
		var l= window.parent.opener.location+'';
		alert('i am now updating the page');
		window.parent.opener.location=l;
		window.parent.close();
		</script><?php
	break;
	case $mode=='filesizeComparison':
		require($COMPONENT_ROOT.'/comp_950_filesizecomparison_v100.php');
	break;
	case $mode=='importProcess':
		require($COMPONENT_ROOT.'/comp_900_importmanager_v200.php');
	break;
	default:
		mail('sfullman@compasspointmedia.com','error line '.__LINE__,$mode.' is not a recognized mode in page '.__FILE__,'From: bugreports@fantasticshop.com');
}

//navigation section - configured by each mode above
if($navigate && !$suppressNavigate && $navigateCount){
	prn('navigating:');
	navigate($navigateCount);
}
$assumeErrorState=false;
?>