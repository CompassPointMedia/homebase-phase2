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


//------------------------ Navbuttons head coding v1.43 -----------------------------
//change these first vars and the queries for each instance
$object='Clients_ID';
$recordPKField='ID'; //primary key field
$navObject='Clients_ID';
$updateMode='updateClient';
$insertMode='insertClient';
$deleteMode='deleteClient';
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
$ids=q("SELECT ID FROM finan_clients WHERE ResourceType IS NOT NULL ORDER BY ClientName",O_COL);
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
if(strlen($$object) || $Clients_ID=q("SELECT ID FROM finan_clients WHERE ResourceToken!='' AND ResourceToken='$ResourceToken' AND ResourceType IS NOT NULL", O_VALUE)){
	//get the record for the object
	if($a=q("SELECT a.*, c.LastName, c.FirstName, c.MiddleName, c.Email, c.HomeMobile FROM finan_clients a, finan_ClientsContacts cc, addr_contacts c WHERE a.ID=cc.Clients_ID AND cc.Contacts_ID=c.ID AND cc.Type='Primary' AND a.ID='".$$object."'",O_ROW)){
		unset($a['Clients_ID']);
		$mode=$updateMode;
		@extract($a);
	}else{
		//object may have been deleted by another user, least perplexing approach is to present insert mode
		$mode=$insertMode;
		unset($$object);
		$nullAbs=$nullCount+1;
	}
}else{
	$mode=$insertMode;
	$$object=$ID=quasi_resource_generic($MASTER_DATABASE, 'finan_clients', $ResourceToken, $typeField='ResourceType', $sessionKeyField='sessionKey', $resourceTokenField='ResourceToken', $primary='ID', $creatorField='Creator', $createDateField='CreateDate' /*, C_DEFAULT, $options */);

	$nullAbs=$nullCount+1; //where we actually are right then
}
//--------------------------- end coding --------------------------------


$hideCtrlSection=false;

if(!$Country)$Country='USA';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Manage Client '.($mode==$insertMode?'(new client)':h($ClientName));?></title>



<link rel="stylesheet" type="text/css" href="/Library/css/cssreset01.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<?php
//------- tabs coding --------
$tabPrefix='clientOptions';
$cg[$tabPrefix]['CGLayers']=array(
	'Properties'		=>'cloProperties',
	'Invoices'			=>'cloInvoices',
);
//added 2010-11-09
if(false)$cg[$tabPrefix]['tabInitialClasses']['cloInvoices']='alertStatus';
//-----------------------------
?>
<style type="text/css">
.publicChecked .ab, .publicChecked .ib{
	background-image:url('/images/i/go_04.jpg');
	background-repeat:no-repeat;
	padding-left:23px;
	}

.used{
	/* visible */
	}
.unused{
	-moz-opacity:.50;
	opacity:.50;
	}
#plWrap{
	background-color:white;
	}
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
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

function togglePropertyVisibility(c){
	g('propertyMashup').className=(c?'unused':'used');
}
</script>

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
	<input name="mode" type="hidden" id="mode" value="<?php echo $mode?>" />
	<input name="componentID" type="hidden" id="componentID" value="<?php echo $localSys['componentID']?>" />
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
	<h3><?php echo $mode==$insertMode ? 'Client Management - Add New Client' : 'Client Info - '.$CompanyName;?></h3>
	<p>
	  Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
        </p>
</div>

</div>
<div id="mainBody">
<div class="suite1">
<p>Client name: 
	<input name="ClientName" type="text" class="th1" id="ClientName" onchange="dChge(this);" value="<?php echo h($ClientName)?>" size="45" maxlength="75" onblur="if(g('PropertyName').value=='')g('PropertyName').value=this.value;" />
	<br />
	Classification: 
	<select name="Category" id="Category" onchange="dChge(this);">
	<option value="">&lt;Select..&gt;</option>
	<option value="Individual" <?php echo strtolower($Category)=='individual'?'selected':''?>>Individual</option>
	<option value="Individual-Multi" <?php echo strtolower($Category)=='individual-multi'?'selected':''?>>Individual with multiple properties</option>
	<option value="Property Management Company" <?php echo strtolower($Category)=='property management company'?'selected':''?>>Property Management Company/Holding/Investor</option>
	</select>
	<br />
	Website: 
	<input name="WebPage" type="text" class="th1" id="WebPage" onchange="dChge(this);" value="<?php echo h($WebPage)?>" size="45" maxlength="75" />
	<em>(optional)</em><br />
	<?php if($mode==$insertMode){ ?>
	Contact name: 
	<input name="FirstName" type="text" class="th1" id="FirstName" onchange="dChge(this);" value="<?php echo h($FirstName)?>" size="17" />
	&nbsp;
	<input name="MiddleName" type="text" class="th1" id="MiddleName" onchange="dChge(this);" value="<?php echo h($MiddleName)?>" size="4" />
	&nbsp;
	<input name="LastName" type="text" class="th1" id="LastName" onchange="dChge(this);" value="<?php echo h($LastName)?>" size="17" />
	<br />
	Cell phone: 
	<input name="HomeMobile" type="text" class="th1" id="HomeMobile" onchange="dChge(this);" value="<?php echo h($HomeMobile)?>" />
	<br />
	Email: 
	<input name="Email" type="text" class="th1" id="Email" onchange="dChge(this);" value="<?php echo h($Email)?>" />
	<?php }else{ ?>
	Contact name: <?php echo h($FirstName.' '.$MiddleName.' '.$LastName)?><br />
	Cell phone: <?php echo h($HomeMobile)?><br />
	Email: <a href="mailto:<?php echo $Email;?>"><?php echo h($Email)?></a><br />
	<?php } ?>
	<br />
    <br />
	Owner Tax ID: 
	<input name="GLF_OwnerTaxID" type="text" class="th1" id="GLF_OwnerTaxID" onchange="dChge(this);" value="<?php echo h($GLF_OwnerTaxID);?>" />
	<br />
	Office phone: 
	<input name="Phone" type="text" class="th1" id="Phone" onchange="dChge(this);" value="<?php echo h($Phone);?>" />
	<br />
	Office fax:
	<input name="Fax" type="text" class="th1" id="Fax" onchange="dChge(this);" value="<?php echo h($Fax);?>" />
</p>

<fieldset>
<legend>Office/Billing address</legend>
	Address:
	<input name="Address1" type="text" class="th1" id="Address1" onchange="dChge(this);" value="<?php echo h($Address1)?>" />
	<br />
	City:
	<input name="City" type="text" class="th1" id="City" onchange="dChge(this);" value="<?php echo h($City)?>" />
	<br />
	State:
	<select name="State" class="th1" id="State" style="width:125px;" onchange="dChge(this);countryInterlock('State','State','Country');">
	  <option value="" class="ghost"> &lt;Select..&gt; </option>
	  <?php
						$gotState=false;
						foreach($states as $n=>$v){
							?>
	  <option value="<?php echo $n?>" <?php
							if($State==$n){
								$gotState=true;
								echo 'selected';
							}
							?>><?php echo h($v)?></option>
	  <?php
						}
						if(!$gotState && $State!=''){
							?>
	  <option value="<?php echo h($State)?>" style="background-color:tomato;" selected="selected"><?php echo $State?></option>
	  <?php
						}
						?>
	</select>
	Zip:
	<input name="Zip" type="text" class="th1" id="Zip" onchange="dChge(this);" value="<?php echo h($Zip)?>" size="7" />
	<br />
	Country:
	<select tabindex="-1" name="Country" class="th1" id="Country" style="width:175px;" onchange="dChge(this);countryInterlock('Country','State','Country');">
	  <option value="" class="ghost"> &lt;Select..&gt; </option>
	  <?php
						$gotCountry=false;
						foreach($countries as $n=>$v){
							?>
	  <option value="<?php echo $n?>" <?php
							if($Country==$n){
								$gotCountry=true;
								echo 'selected';
							}
							?>><?php echo h($v)?></option>
	  <?php
						}
						if(!$gotCountry && $Country!=''){
							?>
	  <option value="<?php echo h($Country)?>" style="background-color:tomato;" selected="selected"><?php echo $Country?></option>
	  <?php
						}
						?>
	</select>
</fieldset>
<br />
<?php if($mode==$insertMode){ ?>
<br />
<label>
<input tabindex="-1" name="skipPropertyInfo" type="checkbox" id="skipPropertyInfo" value="1" onclick="togglePropertyVisibility(this.checked);" checked="checked" />
Do not enter property information at this time
</label>
<table id="propertyMashup" class="used" width="100%" border="0" cellspacing="0">
  <tr>
    <td><h2>Property Information</h2>
      <p>
      Name of Property: 
      <input name="PropertyName" type="text" class="th1" id="PropertyName" onchange="dChge(this);" value="<?php echo h($PropertyName)?>" size="35" />
      <br />
      Abbreviated Property Name: 
      <input name="PropertyNameShort" type="text" class="th1" id="PropertyNameShort" onchange="dChge(this);" value="<?php echo h($PropertyNameShort)?>" size="20" />
      <br />
      Address:
        <input name="PropertyAddress" type="text" class="th1" id="PropertyAddress" onchange="dChge(this);" value="<?php echo h($PropertyAddress)?>" />
        <br />
City:
<input name="PropertyCity" type="text" class="th1" id="PropertyCity" onchange="dChge(this);" value="<?php echo h($PropertyCity)?>" />
<br />
State:
<select name="PropertyState" class="th1" id="PropertyState" style="width:125px;" onchange="dChge(this);countryInterlock('State','State','Country');">
  <option value="" class="ghost"> &lt;Select..&gt; </option>
  <?php
					$gotState=false;
					foreach($states as $n=>$v){
						?>
  <option value="<?php echo $n?>" <?php
						if($State==$n){
							$gotState=true;
							echo 'selected';
						}
						?>><?php echo h($v)?></option>
  <?php
					}
					if(!$gotState && $State!=''){
						?>
  <option value="<?php echo h($State)?>" style="background-color:tomato;" selected="selected"><?php echo $State?></option>
  <?php
					}
					?>
</select>
Zip:
<input name="PropertyZip" type="text" class="th1" id="PropertyZip" onchange="dChge(this);" value="<?php echo h($PropertyZip)?>" size="7" />
</p>
      <p>Number of <u>same</u> units in this group: 
        <select name="Quantity" class="th1" id="Quantity" onchange="dChge(this);">
          <option value="" class="ghost"> &lt;Select..&gt; </option>
          <?php
		  for($i=1;$i<=200;$i++){
		  	?><option value="<?php echo $i?>" <?php echo $Quantity==$i?'selected':''?>><?php echo $i?></option><?php
		  }
		  ?>
        </select>
        <br />
        Unit(s) type: 
        <select name="Type" class="th1" id="Type" onchange="dChge(this);">
		<?php if(false){ ?>
          <option value="" class="ghost"> &lt;Select..&gt; </option>
		<?php } ?>
		  <option value="Apt" <?php echo strtolower($Type)=='apt'?'selected':''?>>Apartments</option>
		  <option value="Townhouse" <?php echo strtolower($Type)=='townhouse'?'selected':''?>>Townhouse or Condo</option>
		  <option value="SFR" <?php echo strtolower($Type)=='sfr'?'selected':''?>>Single Family Residence</option>
		  <option value="Duplex" <?php echo strtolower($Type)=='duplex'?'selected':''?>>Duplex</option>
		  <option value="Multi" <?php echo strtolower($Type)=='multi'?'selected':''?>>Triplex or multi</option>
		  <option value="Other" <?php echo strtolower($Type)=='other'?'selected':''?>>Other type (use carefully)</option>
        </select>
        <br />
        <em>(you can add more unit groups later)</em><br />
      Bedrooms: 
      <select name="Bedrooms" id="Bedrooms">
	  <option value="">&lt;select..&gt;</option>
	  <?php
	  for($i=1; $i<=5; $i+=.5){
	  	?><option value="<?php echo $i?>" <?php echo $i==$Bedrooms?'selected':''?>><?php echo $i?></option><?php
	  }
	  ?>
      </select>
      <br />
      Bathrooms: 
      <select name="Bathrooms" id="Bathrooms">
	  <option value="">&lt;select..&gt;</option>
	  <?php
	  for($i=1; $i<=5; $i+=.5){
	  	?><option value="<?php echo $i?>" <?php echo $i==$Bathrooms?'selected':''?>><?php echo $i?></option><?php
	  }
	  ?>
      </select>
      <br />
      Square feet: 
      <input name="SquareFeet" type="text" class="th1" id="SquareFeet" onchange="dChge(this);" value="<?php if($SquareFeet)echo h($SquareFeet)?>" size="10" />      
      <br />
      Specials/amenities: <br />
<textarea name="OtherAmenities" cols="40" rows="4" id="OtherAmenities"><?php echo h($OtherAmenities)?></textarea>
      <br />
      </p>      </td>
    <td><h2>Rental Info</h2>
      <p>
      Rent: 
      <input name="Rent" type="text" class="th1" id="Rent" onchange="dChge(this);" value="<?php if($Rent)echo number_format($Rent,2)?>" size="6" />	
      <br />
      Deposit: 
      <input name="Deposit" type="text" class="th1" id="Deposit" onchange="dChge(this);" value="<?php if($Deposit)echo number_format($Deposit,2)?>" size="10" />
      <br />

      Commission:      
        <input name="Commission" type="text" class="th1" id="Commission" onchange="dChge(this);" value="<?php if($Commission>0) echo ($Commission*100).'%';?>" />
        <br />
        Lease period desired: 
        <select name="LeaseDesired" class="th1" id="LeaseDesired" onchange="dChge(this);">
          <option value="" class="ghost"> &lt;Select..&gt; </option>
          <?php
		  for($i=1;$i<=12;$i++){
		  	?>
          <option value="<?php echo $i?>" <?php echo $Quantity==$i || (!$Quantity && $i==12)?'selected':''?>><?php echo ($i==1 ? 'Month-to-month' : ($i==12 ? '1 year' : $i .' months'))?></option>
          <?php
		  }
		  ?>
        </select>
        <br />
      Minimum lease accepted: 
      <select name="LeaseAllowed" class="th1" id="LeaseAllowed" onchange="dChge(this);">
        <option value="" class="ghost"> &lt;Select..&gt; </option>
        <?php
		  for($i=1;$i<=12;$i++){
		  	?>
        <option value="<?php echo $i?>" <?php echo $Quantity==$i || (!$Quantity && $i==12)?'selected':''?>><?php echo ($i==1 ? 'Month-to-month' : ($i==12 ? '1 year' : $i .' months'))?></option>
        <?php
		  }
		  ?>
      </select>
      <br />
      <br />
      Comments:<br />
<textarea name="Notes" cols="40" rows="4" id="Notes"><?php echo h($Notes);?></textarea>
      <br />
	</p>
	</td>
  </tr>
</table>
<?php } ?>
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