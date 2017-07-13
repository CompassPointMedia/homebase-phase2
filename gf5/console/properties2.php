<?php 
/*
Created 2010-11-24 SF

*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';

$getStates=true;
$getCountries=true;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

$__tabs__['memberMain']['tabSet']=array(
	'Rental Info'	=>'prRentalDetails',
	'Utilities-Amenities'	=>'prUtilities',
	'Pictures'	=>'prPictures',
	'Brochures'	=>'prBrochures',
);


if($Units_ID){
	$Properties_ID=q("SELECT Properties_ID FROM gl_properties_units WHERE ID=$Units_ID", O_VALUE);
}else if($Properties_ID){
	$Units_ID=q("SELECT ID FROM gl_properties_units WHERE Properties_ID='$Properties_ID' LIMIT 1", O_VALUE);
}

//------------------------ Navbuttons head coding v1.43 -----------------------------
//change these first vars and the queries for each instance
$object='Properties_ID';
$recordPKField='ID'; //primary key field
$navObject='Properties_ID';
$updateMode='updateProperty';
$insertMode='insertProperty';
$deleteMode='deleteProperty';
$insertType=1; //1=Save&New and Save&Close; 2 = Save and Save&New
#set these to 'disabled' if desired
$saveInitiallyDisabled='';
$saveAndNewInitiallyDisabled='';
$saveAndCloseInitiallyDisabled='';
//v1.4 change - some information about the coding
$navVer='1.43';
//v1.3 change - declare this function if you need to add parameters to the query string
$navQueryFunction='nav_query_add()';
//v1.3 change - deny transiting from Next to New mode (shutting off ability to insert)
$denyNextToNew=false;
//declare the query to get the idSet or subset, ordered by desired sort order - note that if you're using quasi resources, then be sure and filter them out.
$ids=q("SELECT ID FROM gl_properties WHERE 1 ORDER BY PropertyName",O_COL);
$nullCount=count($ids);
$j=0;
if($nullCount){
	foreach($ids as $v){
		$j++; //starting value=1
		if($j==$abs+$nav || (isset($$object) && $$object==$v)){
			$nullAbs=$j;
			//get actual primary key if passage by abs+nav
			if(!$$object) $$object=$v;
			break;
		}
	}
}else{
	$nullAbs=1;
}
//note the coding to on ResourceToken - this will allow a submitted page to come up again if the user Refreshes the browser
if(strlen($$object)){
	//get the record for the object
	if($units=q("SELECT a.* FROM _v_properties_master_list a WHERE ID=$Units_ID",O_ARRAY)){
		$mode=$updateMode;
		@extract($units[1]);
		
		/* important, redirect to correct page */
		if(strtolower($Type)=='apt'){
			header('Location: ' .str_replace('properties2','properties3',$_SERVER['PHP_SELF']).($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
			exit;
		}
		
		foreach($units as $n=>$v){
			if(!$firstType)$firstType=strtolower($v['Type']);
			$types[strtolower($v['Type'])]++;
		}
		if(count($types)==1){
			$disposition=$firstType;
		}else{
			mail($developerEmail, 'Mixed property type, currently an error in file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
			$disposition='mixed';
		}
		
	}else{
		//object may have been deleted by another user, least perplexing approach is to present insert mode
		$mode=$insertMode;
		unset($$object);
		$nullAbs=$nullCount+1;
	}
}else{
	$mode=$insertMode;
	$nullAbs=$nullCount+1; //where we actually are right then
	if(!$disposition)$disposition='apt';
}
//--------------------------- end coding --------------------------------
if($mode==$updateMode){
	if($submode=='addRental'){
		if(!is_numeric($Rent)){
			error_alert('The rent is not a number');
		}
		if($DateAvailable){}
		if(!is_numeric($Bedrooms)){
			error_alert('The Bedrooms is not a number');
		}
		if(!is_numeric($Bathrooms)){
			error_alert('The Bathrooms is not a number');
		}
		if(!is_numeric($Deposit)){
			error_alert('The Deposit is not a number');		
		}
		q("INSERT INTO gl_properties_units SET 
		Properties_ID='$Properties_ID', 
		Quantity='1', 
		Bedrooms='$Bedrooms', 
		Bathrooms='$Bathrooms', 
		LeaseDesired='$LeaseDesired', 
		LeaseAllowed='$LeaseAllowed'");
	}
}
$hideCtrlSection=false;

if(!$LeaseDesired)$LeaseDesired=12;
if(!$LeaseAllowed)$LeaseAllowed=12;
if(!$BillingCountry)$BillingCountry='USA';

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='House Info - '.($mode==$insertMode?'(new property)':h($PropertyName));?></title>



<link rel="stylesheet" type="text/css" href="/Library/css/undohtml3.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
.objectWrapper{
	background-color:#f1d5c8;
	}
#topSection{
	padding:5px;
	}
.tabWrapper {
	background-color:blanchedalmond;
	padding:5px 2px;
	min-height:400px;
	}
#addNewClient{
	display:<?php if(true){ ?>none<?php }?>;
	border:1px dotted darkred;
	padding:7px;
	margin:2px 15px;
	}

</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';
var isEscapable=0;
var isDeletable=0;
var isModal=0;
var talks=0; //whether this page broadcasts overall state changes which other pages listen for
var listens=0;


function addNewClient(o){
	if(o.value=='{RBADDNEW}'){
		g('addNewClient').style.display='block';
		g('PrimaryFirstName').focus();
	}else{
		g('addNewClient').style.display='none';
		var a=['PrimaryFirstName','PrimaryLastName','CompanyName','BillingAddress','BillingCity','BillingState','BillingZip','BillingCountry']
		for(var j in a)	g(a[j]).value='';
	}
}
function fillCompany(){
	if(g('PrimaryFirstName').value!=='' && g('PrimaryLastName').value!=='' && g('CompanyName').value==''){
		g('CompanyName').value=g('PrimaryLastName').value+', '+g('PrimaryFirstName').value;
	}
}
function openLease(n){
	if(typeof n=='undefined')n='';
	ow('leases.php?Leases_ID='+n+'&Units_ID=<?php echo $Units_ID?>','l2_leases','700,700');
}
function pullBilling(n){
	if(n=='{RBADDNEW}'){
		g('GLF_BillingMethod').checked=false;
		g('GLF_BillingMethod2').checked=false;
		g('GLF_BillingEmail').value='';
		g('GLF_BillingMethod3').checked=false;
		g('GLF_BillingFax').value='';
	}else{
		g('pullBillingStatus').style.display='inline';
		window.open('resources/bais_01_exe.php?mode=pullBilling&Clients_ID='+n,'w2');
	}
}
</script>
<?php
$Type='SFR';
//------- tabs coding --------
$tabPrefix='clientOptions';
$cg[$tabPrefix]['CGLayers']=array(
	'Amenities'		=>'prAmenities',
	'Inventory'		=>'prInventory',
	'Leases &amp; Tenants'		=>'prLeases',
	'Billing'		=>'prBilling',
);
//-----------------------------
?>


</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
	<div id="btns140" class="fr"><?php
	ob_start();
	?>
	<input id="Previous" type="button" name="Submit" value="Previous" class="navButton_A" onClick="focus_nav(-1, '<?php echo ($mode==$insertMode?'insert':'update')?>', <?php echo $mode==$insertMode?1:0?>, 0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs<=1?'disabled':''?>>
	<?php
	//Handle display of all buttons besides the Previous button
	if($mode==$insertMode){
		if($insertType==2 /** advanced mode **/){
			//save
			?><input id="Save" type="button" name="Submit" value="Save" class="navButton_A" onClick="focus_nav(0,'insert',1,2<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveInitiallyDisabled?> /><?php
		}
		//save and new - common to both modes
		?><input id="SaveAndNew" type="button" name="Submit" value="Save &amp; New" class="navButton_A" onClick="focus_nav(0,'insert', 1,1<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndNewInitiallyDisabled?> /><?php
		if($insertType==1 /** basic mode **/){
			//save and close
			?><input id="SaveAndClose" type="button" name="Submit" value="Save &amp; Close" class="navButton_A" onClick="focus_nav(0,'insert', 1,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndCloseInitiallyDisabled?> /><?php
		}
		?><input id="CancelInsert" type="button" name="Submit" value="Cancel" class="navButton_A" onClick="focus_nav_cxl('insert');" /><?php
	}else{
		//OK, and appropriate [next] button
		?><input id="OK" type="button" name="Submit" value="OK" class="navButton_A" onClick="focus_nav(0,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" />
		<input id="Next" type="button" name="Submit" value="Next" class="navButton_A" onClick="focus_nav(1,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs>$nullCount || ($denyNextToNew && $nullAbs==$nullCount) ?'disabled':''?> /><?php
	}
	$navbuttons=ob_get_contents();
	ob_end_clean();
	//2009-09-10 - change button names, set default as =submit, hide unused buttons
	if(!$addRecordText)$addRecordText='Add Record';
	if(!isset($navbuttonDefaultLogic))$navbuttonDefaultLogic=true;
	if($navbuttonDefaultLogic){
		$navbuttonSetDefault=($mode==$insertMode?'SaveAndNew':'OK');
		if($cbSelect){
			$navbuttonOverrideLabel['SaveAndClose']=$addRecordText;
			$navbuttonHide=array(
				'Previous'=>true,
				'Save'=>true,
				'SaveAndNew'=>true,
				'Next'=>true,
				'OK'=>true
			);
		}
	}
	$navbuttonLabels=array(
		'Previous'		=>'Previous',
		'Save'			=>'Save',
		'SaveAndNew'	=>'Save &amp; New',
		'SaveAndClose'	=>'Save &amp; Close',
		'CancelInsert'	=>'Cancel',
		'OK'			=>'OK',
		'Next'			=>'Next'
	);
	foreach($navbuttonLabels as $n=>$v){
		if($navbuttonOverrideLabel[$n])
		$navbuttons=str_replace(
			'id="'.$n.'" type="button" name="Submit" value="'.$v.'"', 
			'id="'.$n.'" type="button" name="Submit" value="'.h($navbuttonOverrideLabel[$n]).'"', 
			$navbuttons
		);
		if($navbuttonHide[$n])
		$navbuttons=str_replace(
			'id="'.$n.'" type="button"',
			'id="'.$n.'" type="button" style="display:none;"',
			$navbuttons
		);
	}
	if($navbuttonSetDefault)$navbuttons=str_replace(
		'<input id="'.$navbuttonSetDefault.'" type="button"', 
		'<input id="'.$navbuttonSetDefault.'" type="submit"', 
		$navbuttons
	);
	echo $navbuttons;
	
	// *note that we could go back to the same page the 'New Record' click appeared on, but there's major issues programmatically on whether it would shift because of the placement of the new record.
	// *note that the primary key field is now included here to save time
	?>
	<input name="<?php echo $recordPKField?>" type="hidden" id="<?php echo $recordPKField?>" value="<?php echo $$object;?>" />
	<input name="navVer" type="hidden" id="navVer" value="<?php echo $navVer?>" />
	<input name="navObject" type="hidden" id="navObject" value="<?php echo $navObject?>" />
	<input name="nav" type="hidden" id="nav" />
	<input name="navMode" type="hidden" id="navMode" value="" />
	<input name="count" type="hidden" id="count" value="<?php echo $nullCount?>" />
	<input name="abs" type="hidden" id="abs" value="<?php echo $nullAbs?>" />
	<input name="insertMode" type="hidden" id="insertMode" value="<?php echo $insertMode?>" />
	<input name="updateMode" type="hidden" id="updateMode" value="<?php echo $updateMode?>" />
	<input name="deleteMode" type="hidden" id="deleteMode" value="<?php echo $deleteMode?>" />
	<input name="ResourceToken" type="hidden" id="ResourceToken" value="<?php echo $ResourceToken?>" />
	<input name="mode" type="hidden" id="mode" value="<?php echo $mode?>" />
	<input name="submode" type="hidden" id="submode" value="<?php echo $submode?>" />
	<input name="componentID" type="hidden" id="componentID" value="<?php echo $localSys['componentID']?>" />
	<input name="Units_ID" type="hidden" id="Units_ID" value="<?php echo $Units_ID?>" />
	
	<?php
	if(count($_REQUEST)){
		foreach($_REQUEST as $n=>$v){
			if(substr($n,0,2)=='cb'){
				if(!$setCBPresent){
					$setCBPresent=true;
					?><!-- callback fields automatically generated --><?php
					echo "\n";
					?><input name="cbPresent" id="cbPresent" value="1" type="hidden" /><?php
					echo "\n";
				}
				if(is_array($v)){
					foreach($v as $o=>$w){
						echo "\t\t";
						?><input name="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" id="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" type="hidden" value="<?php echo stripslashes($w)?>" /><?php
						echo "\n";
					}
				}else{
					echo "\t\t";
					?><input name="<?php echo $n?>" id="<?php echo $n?>" type="hidden" value="<?php echo stripslashes($v)?>" /><?php
					echo "\n";
				}
			}
		}
	}
	?><!-- end navbuttons 1.43 --></div>
	<h3><?php echo $PageTitle?></h3>
	<p>
	  Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
        </p>
</div>

</div>
<div id="mainBody">
<div class="suite1">
<div id="topSection">
	<div id="leaseInfo" class="fr">
	<?php
	if($mode==$insertMode){
		if(!$cbTable && !$cbSelect){
			?>
			<h3 class="nullBottom">To lease this property</h3>
			<p>You must first add the property before leasing it</p>
			<?php
		}
	}else{
		//prn($units[1]);
		if($id=$units[1]['CurrentLeases_ID']){
			$a=q("SELECT a.*, IF(LeaseTerminationDate, LeaseTerminationDate, LeaseEndDate) AS EffectiveEndDate FROM gl_leases a WHERE a.ID=".$id, O_ROW);
			?>
			Currently leased for <?php echo number_format($a['Rent'],2);?><br />
			Lease ends: <?php echo t($a['EffectiveEndDate'], f_qbks);?> <!-- (n days) --><br />
			<?php
			if($leaseContacts=q("SELECT * FROM
				gl_LeasesContacts lc LEFT JOIN addr_contacts c ON lc.Contacts_ID=c.ID
				WHERE 
				lc.Leases_ID='$id' ORDER BY IF(lc.Type='Primary',1,2)", O_ARRAY)){
				?>
				Tenants: <?php
				$i=0;
				foreach($leaseContacts as $v){
					$i++;
					if($i==1){
						$contact=$v;
					}else if($i>1){
						echo ', ';
					}
					?><a href="contacts.php?Contacts_ID=<?php echo $v['Contacts_ID']?>" title="view this tenant's information<?php if(strtolower($v['Type'])=='primary')echo ' (this is the primary tenant)';?>" onclick="return ow(this.href, 'l2_contacts','1050,450');" <?php if(strtolower($v['Type'])=='primary')echo 'class="primary"'?>><?php echo $v['FirstName']. ' '.$v['LastName'].(strtolower($v['Type'])=='primary' ? '*':'')?></a><?php
				}
				?>
				Contact: <?php echo $contact['HomeMobile'] ? $contact['HomeMobile'] .' (m)<br />':''?>
				<?php echo $contact['HomePhone'] ? $contact['HomePhone'] . ' (h)<br />':''?>
				<?php echo $contact['BusPhone'] ? $contact['BusPhone'] . ' (w)<br />':''?>
				<?php if($contact['Email']){ ?><a href="mailto:<?php echo $contact['Email']?>"><?php echo $contact['Email']?></a><br />
<?php } ?>
				<!--
				From: <?php echo date('n/j/Y', strtotime($lease['LeaseStartDate']));?> to <?php echo date('n/j/Y', strtotime($lease['LeaseTerminationDate']!='0000-00-00'? $lease['LeaseTerminationDate'] : $lease['LeaseEndDate']));?> <?php echo $lease['LeaseTerminationDate']!='0000-00-00' ? ' (terminated early)':''?><br />
				Lease amount: <?php echo number_format($lease['Rent'],2);?>/month<br />
				-->
				<?php
			}
			?>
			<input name="Button" type="button" id="Submit" value="Change this Lease.." onclick="openLease(<?php echo $id?>);" />
			<br />
			<?php
		}
		if($units[1]['CurrentLeases_ID'] && $units[1]['FutureLeases_ID'])echo '<br />';
		if($id=$units[1]['FutureLeases_ID']){
			$a=q("SELECT a.*, IF(LeaseTerminationDate, LeaseTerminationDate, LeaseEndDate) AS EffectiveEndDate FROM gl_leases a WHERE a.ID=".$id, O_ROW);
			?>
			<?php echo $units[1]['CurrentLeases_ID']?'Next lease':'Lease'?> scheduled for: <?php echo t($units[1]['FutureLeaseStart'],f_qbks);?> <!-- (n days) --><br />
			Expires: <?php echo t($units[1]['FutureLeaseEnd'],f_qbks);?><br />
			Rent amount: <?php echo number_format($a['Rent'],2);?><br />
			<?php
			if($leaseContacts=q("SELECT * FROM
				gl_LeasesContacts lc LEFT JOIN addr_contacts c ON lc.Contacts_ID=c.ID
				WHERE 
				lc.Leases_ID='$id' ORDER BY IF(lc.Type='Primary',1,2)", O_ARRAY)){
				?>
				Tenants: <?php
				$i=0;
				foreach($leaseContacts as $v){
					$i++;
					if($i==1){
						$contact=$v;
					}else if($i>1){
						echo ', ';
					}
					?><a href="contacts.php?Contacts_ID=<?php echo $v['Contacts_ID']?>" title="view this tenant's information<?php if(strtolower($v['Type'])=='primary')echo ' (this is the primary tenant)';?>" onclick="return ow(this.href, 'l2_contacts','1050,450');" <?php if(strtolower($v['Type'])=='primary')echo 'class="primary"'?>><?php echo $v['FirstName']. ' '.$v['LastName'].(strtolower($v['Type'])=='primary' ? '*':'')?></a><?php
				}
				?><br />
				Contact: <?php echo $contact['HomeMobile'] ? $contact['HomeMobile'] .' (m)<br />':''?>
				<?php echo $contact['HomePhone'] ? $contact['HomePhone'] . ' (h)<br />':''?>
				<?php echo $contact['BusPhone'] ? $contact['BusPhone'] . ' (w)<br />':''?>
				<?php if($contact['Email']){ ?><a href="mailto:<?php echo $contact['Email']?>"><?php echo $contact['Email']?></a><br />
<?php } ?>
				<!--
				From: <?php echo date('n/j/Y', strtotime($lease['LeaseStartDate']));?> to <?php echo date('n/j/Y', strtotime($lease['LeaseTerminationDate']!='0000-00-00'? $lease['LeaseTerminationDate'] : $lease['LeaseEndDate']));?> <?php echo $lease['LeaseTerminationDate']!='0000-00-00' ? ' (terminated early)':''?><br />
				Lease amount: <?php echo number_format($lease['Rent'],2);?>/month<br />
				-->
				<?php
			}
			?>
			<input name="Button" type="button" id="Submit" value="Change this Lease.." onclick="openLease(<?php echo $id?>);" />
			<br />
			<?php
		}
		if(!$units[1]['CurrentLeases_ID'] && !$units[1]['FutureLeases_ID']){
			?>
			Currently not leased<br />
			<input name="Button" type="button" id="Submit" value="Lease this property" onclick="openLease();" />
			<?php
		}
	}
	?>
	</div>
	<?php 
	$TypeLabel='Single Property';
	?>
	<h2><img alt="SFR" src="/images/assets/ico_home_sfr.png" width="44" height="45" align="absbottom" />
  <?php echo $mode==$insertMode ? 'Add' : 'Update'?> a <?php echo $TypeLabel?></h2>
  <p>Client: 
    <select class="th1" name="Clients_ID" id="Clients_ID" onchange="dChge(this); addNewClient(this); pullBilling(this.value)">
	<option value="">&lt;Select..&gt;</option>
	<option value="{RBADDNEW}" style="background-color:thistle;">&lt; Add New.. &gt;</option>
	<?php
	$clients=q("SELECT
	c.ID, c.CompanyName, c.PrimaryFirstName, c.PrimaryLastName, IF(p.PropertyCity!='', p.PropertyCity, '(city not listed)') AS PropertyCity
	FROM finan_clients c LEFT JOIN gl_properties p ON c.ID=p.Clients_ID
	GROUP BY c.ID ORDER BY IF(p.PropertyCity='San Marcos',1,2), p.PropertyCity, c.CompanyName", O_ARRAY);
	$i=0;
	foreach($clients as $n=>$v){
		$i++;
		if(strtolower($currentCity)!==strtolower($v['PropertyCity'])){
			$currentCity=$v['PropertyCity'];
			if($i==1)echo '</optgroup>';
			?><optgroup label="<?php echo $v['PropertyCity']?>"><?php
		}
		?><option value="<?php echo $v['ID']?>" <?php echo $Clients_ID==$v['ID']?'selected':''?>><?php echo $v['CompanyName']?></option><?php
	}
	
	?></optgroup>
	<?php
	reset($clients);
	?><optgroup label="(alphabetically)"><?php
	foreach($clients as $n=>$v){
		?><option value="<?php echo $v['ID']?>"><?php echo $v['CompanyName']?></option><?php
	}
	?></optgroup>
    </select>
	<span id="pullBillingStatus" style="display:none;"><img src="/images/i/ani/ani-gif-bars-ltgreen.gif" width="43" height="11" alt="processing.." /></span>
	<div id="addNewClient" <?php if(false){ ?> style="display:none;" <?php } ?>>
	First name: <input class="th1" name="PrimaryFirstName" type="text" id="PrimaryFirstName" value="<?php echo h($PrimaryFirstName);?>" size="12" maxlength="35" onchange="dChge(this);" onblur="fillCompany();" />
	&nbsp;&nbsp;
	Last name: <input class="th1" name="PrimaryLastName" type="text" id="PrimaryLastName" value="<?php echo h($PrimaryLastName);?>" size="16" maxlength="35" onchange="dChge(this);" onblur="fillCompany();" />
	<br />
	Company name: <input class="th1" name="CompanyName" type="text" id="CompanyName" value="<?php echo h($CompanyName);?>" size="45" maxlength="35" onchange="dChge(this);" onblur="fillCompany();" />
	<br />
	<div class="fl">
		<h4>Billing address:</h4>
	</div>
	<div class="fl">
		<input class="th1" name="BillingAddress" type="text" id="BillingAddress" value="<?php echo h($BillingAddress);?>" size="35" maxlength="35" onchange="dChge(this);" />
		<br />
		City: <input class="th1" name="BillingCity" type="text" id="BillingCity" value="<?php echo h($BillingCity);?>" size="14" maxlength="35" onchange="dChge(this);" />
		&nbsp;&nbsp;State: 
		<select class="th1" name="BillingState" id="BillingState" style="width:100px;" onchange="dChge(this);countryInterlock('BillingState','BillingState','BillingCountry');">
			<option value="" class="ghost"> &lt;Select..&gt; </option>
			<?php
			$gotState=false;
			foreach($states as $n=>$v){
				?><option value="<?php echo $n?>" <?php
				if($BillingState==$n){
					$gotState=true;
					echo 'selected';
				}
				?>><?php echo h($v)?></option><?php
			}
			if(!$gotState && $BillingState!=''){
				?><option value="<?php echo h($State)?>" style="background-color:tomato;" selected="selected"><?php echo $State?></option><?php
			}
			?>
		</select>
		&nbsp;&nbsp;Zip: 
		<input class="th1" name="BillingZip" type="text" id="BillingZip" value="<?php echo h($BillingZip);?>" size="5" onchange="dChge(this);" />
		&nbsp;&nbsp;<br />
		Country: 
		<select tabindex="-1" name="BillingCountry" class="th1" id="BillingCountry" style="width:175px;" onchange="dChge(this);countryInterlock('Country','State','Country');">
			<option value="" class="ghost"> &lt;Select..&gt; </option>
			<?php
			$gotCountry=false;
			foreach($countries as $n=>$v){
				?><option value="<?php echo $n?>" <?php
				if($Country==$n){
					$gotCountry=true;
					echo 'selected';
				}
				?>><?php echo h($v)?></option><?php
			}
			if(!$gotCountry && $Country!=''){
				?><option value="<?php echo h($Country)?>" style="background-color:tomato;" selected="selected"><?php echo $Country?></option><?php
			}
			?>
		</select>
		<br />
		Phone: <input class="th1" name="BillingPhone" type="text" id="BillingPhone" value="<?php echo h($BillingPhone);?>" onchange="dChge(this);" />
		<br />
		Mobile phone: <input class="th1" name="BillingMobilePhone" type="text" id="BillingMobilePhone" value="<?php echo h($BillingMobilePhone);?>" onchange="dChge(this);" />
		<br />
		Fax: <input class="th1" name="BillingFax" type="text" id="BillingFax" value="<?php echo h($BillingFax);?>" onchange="dChge(this);" />
		<br />
		Email: <input class="th1" name="BillingEmail" type="text" id="BillingEmail" value="<?php echo h($BillingEmail);?>" onchange="dChge(this);" />
		<br />
		<div class="fl">Notes:</div>
		<div class="fl">
			<textarea name="finan_clientsNotes" cols="35" rows="4" class="th1" id="finan_clientsNotes" onchange="dChge(this);"><?php echo h($finan_clientsNotes);?></textarea>
		</div>
	</div>
	<div class="cb"> </div>
	
	Owner Tax ID: <input name="GLF_OwnerTaxID" type="text" class="th1" id="GLF_OwnerTaxID" onchange="dChge(this);" value="<?php echo h($GLF_OwnerTaxID);?>" />
	</div>
	Property type: 
	<select name="Type" id="Type" class="th1" onchange="dChge(this);">
	<?php
	foreach($typeLabels as $n=>$v){
		if($n=='apt')continue;
		?><option value="<?php echo $n?>" <?php echo strtolower($Type)==$n || ($mode==$insertMode && $n=='sfr') ? 'selected':''?>><?php echo $v?></option><?php
	}
	?>
	  </select>
	<br />

	<h4>Rental  Address:</h4>
	<div class="fl">
		<input class="th1" name="PropertyAddress" type="text" id="PropertyAddress" value="<?php echo h($PropertyAddress);?>" size="35" maxlength="35" onchange="dChge(this);" />
		&nbsp;&nbsp;&nbsp;&nbsp;
		<br />
		City: <input class="th1" name="PropertyCity" type="text" id="PropertyCity" value="<?php echo h($PropertyCity);?>" size="20" maxlength="35" onchange="dChge(this);" />
		&nbsp;&nbsp;
		State:
		<select class="th1" name="PropertyState" id="PropertyState" style="width:125px;" onchange="dChge(this);">
			<option value="" class="ghost"> &lt;Select..&gt; </option>
			<?php
			$gotState=false;
			foreach($states as $n=>$v){
				?><option value="<?php echo $n?>" <?php
				if($PropertyState==$n){
					$gotState=true;
					echo 'selected';
				}
				?>><?php echo h($v)?></option><?php
			}
			if(!$gotState && $PropertyState!=''){
				?><option value="<?php echo h($PropertyState)?>" style="background-color:tomato;" selected="selected"><?php echo $PropertyState?></option>
				<?php
			}
			?>
		</select>
		&nbsp;&nbsp;Zip:
		<input class="th1" name="PropertyZip" type="text" id="PropertyZip" value="<?php echo h($PropertyZip);?>" size="7" maxlength="35" onchange="dChge(this);" />
		<br />
		Send invoices by:<br />
		<label>
		<input name="GLF_BillingMethod[]" type="checkbox" id="GLF_BillingMethod" value="1" class="th1" onchange="dChge(this);" <?php echo $mode==$insertMode || ($GLF_BillingMethod & 1) ? 'checked':''?> /> 
		Print and then fax/mail  </label>
		<br />
		<label>
	<input name="GLF_BillingMethod[]" type="checkbox" id="GLF_BillingMethod2" value="2" class="th1" onchange="dChge(this);" <?php echo ($GLF_BillingMethod & 2) ? 'checked':''?> />	Email</label>
		<input name="GLF_BillingEmail" type="text" id="GLF_BillingEmail" value="<?php echo $GLF_BillingEmail;?>" class="th1" onchange="dChge(this);" />    
		<br />
		<label>
	<input name="GLF_BillingMethod[]" type="checkbox" id="GLF_BillingMethod3" value="4" class="th1" onchange="dChge(this);" <?php echo ($GLF_BillingMethod & 4) ? 'checked':''?> />	Auto-Fax</label>
		<input name="GLF_BillingFax" type="text" id="GLF_BillingFax" value="<?php echo $GLF_BillingFax;?>" class="th1" onchange="dChge(this);" />
	</div>
	<div class="cb"> </div>
</div>
<div class="objectWrapper">
<?php
//Begin Tabs
ob_start();
//Details Tab
?>
<fieldset><legend>Rental Info</legend>

	Send Commission: 
	<select class="th1" name="SendCommission" id="SendCommission" onfocus="buffer=this.value;" onchange="dChge(this); dollarAmt(this,1,'SendCommission');">
      <option value="">&lt;Select..&gt;</option>
      <?php
	  for($i=.20; $i<=1; $i+=.05){
	  	?><option value="<?php echo $i?>" <?php echo abs($SendCommission-$i)<.001 || ($mode==$insertMode && $i==.5)?'selected':''?>><?php echo round($i*100).'%'?></option><?php
	  }
	  if($SendCommission >=1 && $SendCommission<=2){
	  	?><option value="<?php echo $SendCommission?>" selected="selected"><?php echo round($SendCommission*100,0).'%';?></option><?php
	  }
	  ?>
      <option value="{RBADDNEW}" <?php if($SendCommission>2)echo 'selected';?>>Specific $ amount..</option>
    </select>
	&nbsp;&nbsp;
	<input name="SendCommission_RBADDNEW" type="text" class="th1" id="SendCommission_RBADDNEW" value="<?php if($SendCommission>2)echo number_format($SendCommission,2);?>" size="7" style="visibility:<?php echo $SendCommission>2 ? 'visible':'hidden'?>" />
    <br />
    Escort Commission: 
    <select class="th1" name="EscortCommission" id="EscortCommission" onfocus="buffer=this.value;" onchange="dChge(this); dollarAmt(this,1,'EscortCommission');">
      <option value="">&lt;Select..&gt;</option>
      <?php
	  for($i=.20; $i<=1.001; $i+=.05){
	  	?>
      <option value="<?php echo $i?>" <?php echo abs($EscortCommission-$i)<.001 || ($mode==$insertMode && $i==.5)?'selected':''?>><?php echo round($i*100).'%'?></option>
      <?php
	  }
	  if($EscortCommission >=1 && $EscortCommission<=2){
	  	?>
      <option value="<?php echo $EscortCommission?>" selected="selected"><?php echo round($EscortCommission*100,0).'%';?></option>
      <?php
	  }
	  ?>
      <option value="{RBADDNEW}" <?php if($EscortCommission>2)echo 'selected';?>>Specific $ amount..</option>
    </select>
&nbsp;&nbsp;
<input name="EscortCommission_RBADDNEW" type="text" class="th1" id="EscortCommission_RBADDNEW" value="<?php if($EscortCommission>2)echo number_format($EscortCommission,2);?>" size="7" style="visibility:<?php echo $SendCommission>2 ? 'visible':'hidden'?>" />
<br />
	Rent: <input name="Rent" type="text" class="th1" id="Rent" onchange="dChge(this);" value="<?php echo h($Rent);?>" size="8" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	Date first available: <input name="DateAvailable" type="text" class="th1" id="DateAvailable" onchange="dChge(this);" value="<?php echo $mode==$insertMode && !$DateAvailable ? 'today' : t($DateAvailable, f_qbks);?>" size="14" />
	<br />
	Square Feet: <input name="SquareFeet" type="text" class="th1" id="SquareFeet" onchange="dChge(this);" value="<?php echo h($SquareFeet);?>" size="6" />
	<br />
	Bedrooms: <input name="Bedrooms" type="text" class="th1" id="Bedrooms" onchange="dChge(this);" value="<?php echo h($Bedrooms);?>" size="3" maxlength="3" />
	<br />
	Bathrooms:
	<input name="Bathrooms" type="text" class="th1" id="Bathrooms" onchange="dChge(this);" value="<?php echo h($Bathrooms);?>" size="3" maxlength="3" />
	<br />
	Deposit: <input name="Deposit" type="text" class="th1" id="Deposit" onchange="dChge(this);" value="<?php echo h($Deposit);?>" size="7" />
	<br />
	Lease term (months):
	<select class="th1" name="LeaseDesired" id="LeaseDesired" style="width:125px;" onchange="dChge(this);">
	<option value="">&lt;Select..&gt;</option>
	<option value="1" <?php echo $LeaseDesired==1?'selected':''?>>month-to-month</option>
	<option value="2" <?php echo $LeaseDesired==2?'selected':''?>>2 mo.</option>
	<option value="3" <?php echo $LeaseDesired==3?'selected':''?>>3 mo.</option>
	<option value="4" <?php echo $LeaseDesired==4?'selected':''?>>4 mo.</option>
	<option value="5" <?php echo $LeaseDesired==5?'selected':''?>>5 mo.</option>
	<option style="background-color:silver;" value="6" <?php echo $LeaseDesired==6?'selected':''?>>6 months</option>
	<option value="7" <?php echo $LeaseDesired==7?'selected':''?>>7 mo.</option>
	<option value="8" <?php echo $LeaseDesired==8?'selected':''?>>8 mo.</option>
	<option value="9" <?php echo $LeaseDesired==9?'selected':''?>>9 mo.</option>
	<option value="10" <?php echo $LeaseDesired==10?'selected':''?>>10 mo.</option>
	<option value="11" <?php echo $LeaseDesired==11?'selected':''?>>11 mo.</option>
	<option style="background-color:silver;" value="12" <?php echo $LeaseDesired==12?'selected':''?>>One year</option>
	<option value="13" <?php echo $LeaseDesired==13?'selected':''?>>13 mo.</option>
	<option value="14" <?php echo $LeaseDesired==14?'selected':''?>>14 mo.</option>
	<option value="15" <?php echo $LeaseDesired==15?'selected':''?>>15 mo.</option>
	<option value="16" <?php echo $LeaseDesired==16?'selected':''?>>16 mo.</option>
	<option value="17" <?php echo $LeaseDesired==17?'selected':''?>>17 mo.</option>
	<option value="18" <?php echo $LeaseDesired==18?'selected':''?>>18 mo.</option>
    </select>
	<br />
	Will accept lease term for:
	<select class="th1" name="LeaseAllowed" id="LeaseAllowed" style="width:125px;" onchange="dChge(this);">
      <option value="">&lt;Select..&gt;</option>
      <option value="1" <?php echo $LeaseAllowed==1?'selected':''?>>month-to-month</option>
      <option value="2" <?php echo $LeaseAllowed==2?'selected':''?>>2 mo.</option>
      <option value="3" <?php echo $LeaseAllowed==3?'selected':''?>>3 mo.</option>
      <option value="4" <?php echo $LeaseAllowed==4?'selected':''?>>4 mo.</option>
      <option value="5" <?php echo $LeaseAllowed==5?'selected':''?>>5 mo.</option>
      <option style="background-color:silver;" value="6" <?php echo $LeaseAllowed==6?'selected':''?>>6 months</option>
      <option value="7" <?php echo $LeaseAllowed==7?'selected':''?>>7 mo.</option>
      <option value="8" <?php echo $LeaseAllowed==8?'selected':''?>>8 mo.</option>
      <option value="9" <?php echo $LeaseAllowed==9?'selected':''?>>9 mo.</option>
      <option value="10" <?php echo $LeaseAllowed==10?'selected':''?>>10 mo.</option>
      <option value="11" <?php echo $LeaseAllowed==11?'selected':''?>>11 mo.</option>
      <option style="background-color:silver;" value="12" <?php echo $LeaseAllowed==12?'selected':''?>>One year</option>
      <option value="13" <?php echo $LeaseAllowed==13?'selected':''?>>13 mo.</option>
      <option value="14" <?php echo $LeaseAllowed==14?'selected':''?>>14 mo.</option>
      <option value="15" <?php echo $LeaseAllowed==15?'selected':''?>>15 mo.</option>
      <option value="16" <?php echo $LeaseAllowed==16?'selected':''?>>16 mo.</option>
      <option value="17" <?php echo $LeaseAllowed==17?'selected':''?>>17 mo.</option>
      <option value="18" <?php echo $LeaseAllowed==18?'selected':''?>>18 mo.</option>
    </select>
	</fieldset>
	<!-- <input type="button" name="Add" value="Add" id="Add" onclick="addRental();" />	
	<input type="hidden" name="submode" value="" id="submode" class="submode" /> -->
<?php
//End Details Tab
get_contents_tabsection('prRentalDetails');
?>
Description/overview: <br />
<textarea name="Description" cols="45" rows="4" id="Description" class="th1" onchange="dChge(this);"><?php echo h($Description)?></textarea>
<br />
<p>
<input type="checkbox" name="PetsAllowed" id="PetsAllowed" value="1" <?php echo read_logical($PetsAllowed)?'checked':'';?> onchange="dChge(this);" class="th1" />
Pets allowed<br />
Pet restrictions: 
<input name="PetRestrictions" type="text" id="PetRestrictions" value="<?php echo h($PetRestrictions);?>" onchange="dChge(this);" class="th1" />
<br />
Pet weight limit: 
<input name="PetWeightLimit" type="text" id="PetWeightLimit" class="th1" value="<?php echo h($PetWeightLimit);?>" onchange="dChge(this);" />
<br />
Pet deposit: 
<input name="PetDeposit" type="text" id="PetDeposit" value="<?php echo h($PetDeposit);?>" onchange="dChge(this);" class="th1" />
<br />
Pet Policy(s):<br />
<textarea name="PetPolicies" cols="35" rows="3" id="PetPolicies" class="th1" onchange="dChge(this);"><?php echo h($PetPolicies)?></textarea>
</p>
<h4>Recreational Features</h4>
<table border="0" cellspacing="0">
<tr>
  <td><label>
	<input type="checkbox" name="GameRoom" id="GameRoom" class="th1" value="1" <?php echo read_logical($GameRoom)?'checked':'';?> onchange="dChge(this);"/>
Game Room </label>
	<br />
	<label>
	<input type="checkbox" name="FitnessCenter" id="FitnessCenter" class="th1" value="1" <?php echo read_logical($FitnessCenter)?'checked':'';?> onchange="dChge(this);"/>
Fitness Center </label>
	<br />
	<label>
	<input type="checkbox" name="BusinessCenter" id="BusinessCenter" class="th1" value="1" <?php echo read_logical($BusinessCenter)?'checked':'';?> onchange="dChge(this);"/>
Business Center </label>
	<br />
	<label>
	<input type="checkbox" name="Basketball" id="Basketball" class="th1" value="1" <?php echo read_logical($Basketball)?'checked':'';?> onchange="dChge(this);"/>
Basketball </label></td>
  <td><label>
	<input type="checkbox" name="Volleyball" id="Volleyball" class="th1" value="1" <?php echo read_logical($Volleyball)?'checked':'';?> onchange="dChge(this);"/>
Volleyball </label>
	<br />
	<label>
	<input type="checkbox" name="Pool" id="Pool" class="th1" value="1" <?php echo read_logical($Pool)?'checked':'';?> onchange="dChge(this);"/>
Pool </label>
	<br />
	<label>
	<input type="checkbox" name="HotTub" id="HotTub" class="th1" value="1" <?php echo read_logical($HotTub)?'checked':'';?> onchange="dChge(this);"/>
Hot tub</label></td>
</tr>
</table>
<p>
Parking: 
<select name="Parking" class="th1" id="Parking">
	<option onchange="dChge(this);" value="Lot" <?php echo $Parking=='Lot'?'selected':''?>>Lot</option>
	<option onchange="dChge(this);" value="Covered" <?php echo $Parking=='Covered'?'selected':''?>>Covered</option>
	<option onchange="dChge(this);" value="One Car Garage" <?php echo $Parking=='One Car Garage'?'selected':''?>>One Car Garage</option>
	<option onchange="dChge(this);" value="Two Car Garage" <?php echo $Parking=='Two Car Garage'?'selected':''?>>Two Car Garage</option>
</select>
<br />
<label>
<input type="checkbox" name="SecurityGates" id="SecurityGates" class="th1" value="1" <?php echo read_logical($SecurityGates)?'checked':'';?> onchange="dChge(this);"/>
Security Gates</label>
</p>
<h4>Utilities</h4>
<p>
<input type="checkbox" name="PhonePaid" id="PhonePaid" class="th1" value="1" <?php echo read_logical($PhonePaid)?'checked':'';?> onchange="dChge(this);"/>
Phone paid<br />
<input type="checkbox" name="InternetPaid" id="InternetPaid" class="th1" value="1" <?php echo read_logical($InternetPaid)?'checked':'';?> onchange="dChge(this);"/>
Internet paid<br />
<label>
<input type="checkbox" name="Cable" id="Cable" class="th1" value="1" <?php echo read_logical($Cable)?'checked':'';?> onchange="dChge(this);"/>
Cable paid </label>
<br />
<label>
<input type="checkbox" name="Gas" id="Gas" class="th1" value="1" <?php echo read_logical($Gas)?'checked':'';?> onchange="dChge(this);"/>
Gas paid </label>
- applies to:
<select name="GasPaid" id="GasPaid" class="th1" onchange="dChge(this);">
	<option value="None" <?php echo $GasPaid=='None'?'selected':'';?>>None</option>
	<option value="Hot Water, Stove, Heat" <?php echo $GasPaid=='Hot Water, Stove, Heat'?'selected':'';?>>Hot Water, Stove, Heat</option>
	<option value="Hot Water, Heat" <?php echo $GasPaid=='Hot Water, Heat'?'selected':'';?>>Hot Water, Heat</option>
	<option value="Heat" <?php echo $GasPaid=='Heat'?'selected':'';?>>Heat</option>
	<option value="Hot Water" <?php echo $GasPaid=='Hot Water'?'selected':'';?>>Hot Water</option>
</select>
<br />
<label>
<input type="checkbox" name="ElectricPaid" id="ElectricPaid" class="th1" value="1" <?php echo read_logical($ElectricPaid)?'checked':'';?> onchange="dChge(this);"/>
Electric Paid </label>
<br />
<label>
Water Paid:
<select name="WaterPaid" id="WaterPaid" class="th1">
	<option value="0" <?php echo $WaterPaid=='0'?'selected':'';?> onchange="dChge(this);">No</option>
	<option value=".5" <?php echo $WaterPaid=='.5'?'selected':'';?> onchange="dChge(this);">1/2</option>
	<option value="1" <?php echo $WaterPaid=='1'?'selected':'';?> onchange="dChge(this);">Yes</option>
</select>
</label>
<br />
<label>
<input type="checkbox" name="TrashPaid" id="TrashPaid" class="th1" value="1" <?php echo read_logical($TrashPaid)?'checked':'';?> onchange="dChge(this);"/>
Trash Paid </label>
<br />
Cable provider:
<select name="CableProvider" id="CableProvider" class="th1">
	<option value="Open" <?php echo $CableProvider=='Open'?'selected':'';?> onchange="dChge(this);">Open</option>
	<option value="Grande" <?php echo $CableProvider=='Grande'?'selected':'';?> onchange="dChge(this);">Grande</option>
	<option value="Time Warner" <?php echo $CableProvider=='Time Warner'?'selected':'';?> onchange="dChge(this);">Time Warner</option>
</select>
<br />
Internet provider:
<input type="text" name="InternetProvider" id="InternetProvider" class="th1" value="<?php echo $InternetProvider?>" onchange="dChge(this);"/>
<?php
get_contents_tabsection('prUtilities');
?>
To manage pictures for individual units, click on the Available Units tab, click [edit] for the unit you desire to manage the pictures for, and then click the "More Details" button at the bottom.<br />

<?php require('components/comp_301_propertyimages.php');?>
<?php
get_contents_tabsection('prPictures');


require('components/comp_321_propertyfiles.php');

get_contents_tabsection('prBrochures');

require($MASTER_COMPONENT_ROOT.'/comp_tabs_v220.php');
?>
</div>


</div>
</div>
<div id="footer">
&nbsp;
</div>
</form>
<?php if(!$hideCtrlSection){ ?>
<div id="showTester" title="Javascript Tester" onclick="g('tester').style.display='block';">&nbsp;</div>
<div id="tester" >
	<a href="#" onclick="g('ctrlSection').style.display='block';return false;">Show Control Section</a><br />
	<textarea name="test" cols="65" rows="4" id="test">clear_form();</textarea><br />
	<input type="button" name="button" value="Test" onclick="jsEval(g('test').value);"><br />
	<textarea id="result" name="result" cols="65" rows="3" ></textarea>
</div>
<div id="ctrlSection" style="display:<?php echo $testModeC ? 'block':'none'?>">
	<iframe name="w1" src="/Library/js/blank.htm"></iframe>
	<iframe name="w2" src="/Library/js/blank.htm"></iframe>
	<iframe name="w3" src="/Library/js/blank.htm"></iframe>
	<iframe name="w4" src="/Library/js/blank.htm"></iframe>
</div>
<?php } ?>
</body>
</html><?php page_end();?>