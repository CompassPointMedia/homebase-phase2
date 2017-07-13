<?php 
/*
Created 2010-11-24 SF

*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');


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
	if($units=q("SELECT a.* FROM _v_properties_master_list a WHERE Properties_ID=$Properties_ID",O_ARRAY)){
		$mode=$updateMode;
		@extract($units[1]);
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


$hideCtrlSection=false;

$states=q("SELECT st_code, st_name FROM aux_states",O_COL_ASSOC, $public_cnx);
$countries=q("SELECT ct_code, ct_name FROM aux_countries",O_COL_ASSOC, $public_cnx);
if(!$Country)$Country='USA';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Manage Property '.($mode==$insertMode?'(new property)':h($PropertyName));?></title>



<link rel="stylesheet" type="text/css" href="/site-local/undohtml2.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
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
</script>
<?php
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

<style type="text/css">
.tabWrapper {
	background-color:blanchedalmond;
	padding:5px 2px;
	min-height:400px;
}
</style>

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
	<h3>Property Management </h3>
	<p>
	  Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
        </p>
</div>

</div>
<div id="mainBody">
<div class="suite1">
<h1><?php echo $PageTitle?></h1>
<?php
if(!$disposition)$disposition='apt';

if($disposition=='apt'){
	?>
	<h3>[<?php echo $disposition?>] <input type="text" name="PropertyName" id="PropertyName" class="PropertyName" value="<?php echo $PropertyName?>"  onchange="dChge(this);"/></h3>
	<p><?php echo $PropertyAddress?><br />
	   	<?php echo $PropertyCity?>,<?php echo $PropertyState?> <?php echo $PropertyZip?><br />
	    <?php
	if($GoogleMapLink){
		?>
	  <a title="google map link" target="_blank" href="<?php echo $GoogleMapLink?>">Google Map Link</a>
	  <?php
	}
	?>
	  [file brochure]<br />
	  Description/overview: goes here<br />
	  Owner Tax ID: 
	  <input type="text" name="textfield" class="PropertyName" onchange="dChge(this);" />
	  <br />
	  Address: 
	  <input name="PropertyAddress" type="text" id="PropertyAddress" class="PropertyAddress" value="<?php echo h($PropertyAddress);?>" onchange="dChge(this);"/>
	  <br />
	  <input name="PropertyCity" type="text" id="PropertyCity" class="PropertyCity" value="<?php echo h($PropertyCity);?>" onchange="dChge(this);"/>
		<select id="PropertyState" class="PropertyName" name="PropertyState">
			<?php 
			foreach($states as $n=>$v){
				?>
				<option onchange="dChge(this);" value="<?php echo $n?>" <?php echo $PropertyState==$n?'selected':'';?>><?php echo $v?></option>
				<?php
			}
			?>
		</select>
	  <input name="PropertyZip" type="text" id="PropertyZip" class="PropertyZip" value="<?php echo h($PropertyZip);?>" size="7" onchange="dChge(this);"/>
	  <br />
	  Year built: 
	  <input name="YearBuilt" type="text" id="YearBuilt" class="YearBuilt" value="<?php echo h($YearBuilt);?>" size="7" onchange="dChge(this);"/>
	  <br />
	  <br />
	</p>
	<div class="tabWrapper">
      <p>
  <?php
//------------- call tabs --------------
require($MASTER_COMPONENT_ROOT.'/comp_tabs_v210.php');
?>
  <input type="checkbox" name="PetsAllowed" id="PetsAllowed" class="PetsAllowed" value="1" <?php echo read_logical($PetsAllowed)?'checked':'';?> onchange="dChge(this);"/>
        Pets allowed<br />
        Pet restrictions: 
  <input name="PetRestrictions" type="text" id="PetRestrictions" class="PetRestrictions" value="<?php echo h($PetRestrictions);?>" onchange="dChge(this);"/>
  <br />
        Pet weight limit: 
  <input name="PetWeightLimit" type="text" id="PetWeightLimit" class="PetWeightLimit" value="<?php echo h($PetWeightLimit);?>" onchange="dChge(this);"/>
  <br />
        Pet deposit: 
  <input name="PetDeposit" type="text" id="PetDeposit" class="PetDeposit" value="<?php echo h($PetDeposit);?>" onchange="dChge(this);"/>
  <br />
        Pet Policy(s):<br />
  <textarea name="PetPolicies" cols="35" rows="3" id="PetPolicies" class="PetPolicies" onchange="dChge(this);"><?php echo h($PetPolicies)?></textarea>
      </p>
      <h4>Recreational Features</h4>
      <table border="0" cellspacing="0">
        <tr>
          <td><label>
            <input type="checkbox" name="GameRoom" id="GameRoom" class="GameRoom" value="1" <?php echo read_logical($GameRoom)?'checked':'';?> onchange="dChge(this);"/>
Game Room </label>
            <br />
            <label>
            <input type="checkbox" name="FitnessCenter" id="FitnessCenter" class="FitnessCenter" value="1" <?php echo read_logical($FitnessCenter)?'checked':'';?> onchange="dChge(this);"/>
Fitness Center </label>
            <br />
            <label>
            <input type="checkbox" name="BusinessCenter" id="BusinessCenter" class="BusinessCenter" value="1" <?php echo read_logical($BusinessCenter)?'checked':'';?> onchange="dChge(this);"/>
Business Center </label>
            <br />
            <label>
            <input type="checkbox" name="Basketball" id="Basketball" class="Basketball" value="1" <?php echo read_logical($Basketball)?'checked':'';?> onchange="dChge(this);"/>
Basketball </label></td>
          <td><label>
            <input type="checkbox" name="Volleyball" id="Volleyball" class="Volleyball" value="1" <?php echo read_logical($Volleyball)?'checked':'';?> onchange="dChge(this);"/>
Volleyball </label>
            <br />
            <label>
            <input type="checkbox" name="Pool" id="Pool" class="Pool" value="1" <?php echo read_logical($Pool)?'checked':'';?> onchange="dChge(this);"/>
Pool </label>
            <br />
            <label>
            <input type="checkbox" name="HotTub" id="HotTub" class="HotTub" value="1" <?php echo read_logical($HotTub)?'checked':'';?> onchange="dChge(this);"/>
Hot tub</label></td>
        </tr>
      </table>
<p>
  Parking: 
  <select name="Parking" class="Parking" id="Parking">
  	<option onchange="dChge(this);" value="Lot" <?php echo $Parking=='Lot'?'selected':''?>>Lot</option>
  	<option onchange="dChge(this);" value="Covered" <?php echo $Parking=='Covered'?'selected':''?>>Covered</option>
  	<option onchange="dChge(this);" value="One Car Garage" <?php echo $Parking=='One Car Garage'?'selected':''?>>One Car Garage</option>
  	<option onchange="dChge(this);" value="Two Car Garage" <?php echo $Parking=='Two Car Garage'?'selected':''?>>Two Car Garage</option>
  </select>
  <br />
  <label>
  <input type="checkbox" name="SecurityGates" id="SecurityGates" class="SecurityGates" value="1" <?php echo read_logical($SecurityGates)?'checked':'';?> onchange="dChge(this);"/>
Security Gates</label>
</p>
<h4>Utilities</h4>
<p>
  <input type="checkbox" name="PhonePaid" id="PhonePaid" class="PhonePaid" value="1" <?php echo read_logical($PhonePaid)?'checked':'';?> onchange="dChge(this);"/>
  Phone paid<br />
  <input type="checkbox" name="InternetPaid" id="InternetPaid" class="InternetPaid" value="1" <?php echo read_logical($InternetPaid)?'checked':'';?> onchange="dChge(this);"/>
  Internet paid<br />
  <label>
    <input type="checkbox" name="Cable" id="Cable" class="Cable" value="1" <?php echo read_logical($Cable)?'checked':'';?> onchange="dChge(this);"/>
    Cable paid </label>
  <br />
  <label>
    <input type="checkbox" name="Gas" id="Gas" class="Gas" value="1" <?php echo read_logical($Gas)?'checked':'';?> onchange="dChge(this);"/>
    Gas paid </label>
  - applies to:
  <select name="GasPaid" id="GasPaid" class="GasPaid" onchange="dChge(this);">
  	<option value="None" <?php echo $GasPaid=='None'?'selected':'';?>>None</option>
   	<option value="Hot Water, Stove, Heat" <?php echo $GasPaid=='Hot Water, Stove, Heat'?'selected':'';?>>Hot Water, Stove, Heat</option>
  	<option value="Hot Water, Heat" <?php echo $GasPaid=='Hot Water, Heat'?'selected':'';?>>Hot Water, Heat</option>
  	<option value="Heat" <?php echo $GasPaid=='Heat'?'selected':'';?>>Heat</option>
  	<option value="Hot Water" <?php echo $GasPaid=='Hot Water'?'selected':'';?>>Hot Water</option>
  </select>
  <br />
  <label>
    <input type="checkbox" name="ElectricPaid" id="ElectricPaid" class="ElectricPaid" value="1" <?php echo read_logical($ElectricPaid)?'checked':'';?> onchange="dChge(this);"/>
    Electric Paid </label>
  <br />
  <label>
  Water Paid:
  <select name="WaterPaid" id="WaterPaid" class="WaterPaid">
  	<option value="0" <?php echo $WaterPaid=='0'?'selected':'';?> onchange="dChge(this);">No</option>
  	<option value=".5" <?php echo $WaterPaid=='.5'?'selected':'';?> onchange="dChge(this);">1/2</option>
  	<option value="1" <?php echo $WaterPaid=='1'?'selected':'';?> onchange="dChge(this);">Yes</option>
  </select>
  </label>
  <br />
  <label>
    <input type="checkbox" name="TrashPaid" id="TrashPaid" class="TrashPaid" value="1" <?php echo read_logical($TrashPaid)?'checked':'';?> onchange="dChge(this);"/>
    Trash Paid </label>
  <br />
  Cable provider:
  <select name="CableProvider" id="CableProvider" class="CableProvider">
  	<option value="Open" <?php echo $CableProvider=='Open'?'selected':'';?> onchange="dChge(this);">Open</option>
	<option value="Grande" <?php echo $CableProvider=='Grande'?'selected':'';?> onchange="dChge(this);">Grande</option>
	<option value="Time Warner" <?php echo $CableProvider=='Time Warner'?'selected':'';?> onchange="dChge(this);">Time Warner</option>
  </select>
  <br />
  Internet provider:
  <input type="text" name="InternetProvider" id="InternetProvider" class="InternetProvider" value="<?php echo $InternetProvider?>" onchange="dChge(this);"/>
  <br />
  <br />  
  <br />
  <br />
  
  <?php
/* ------------------ */ get_contents_layer('prAmenities'); /* ------------------ */ 
?>
</p>
<pre>


Inventory
1 [lease] [stake out] [primary person name (if a duplex unit or sfr)
2 [lease] [stake out]
3 [lease] [stake out]

</pre>
<p>
  <?php
/* ------------------ */ get_contents_layer('prInventory'); /* ------------------ */ 
?>
  Leases &amp; Tenants
  <?php
/* ------------------ */ get_contents_layer('prLeases'); /* ------------------ */ 
?>
  Billing
  <?php
/* ------------------ */ get_contents_layer('prBilling'); /* ------------------ */ 
$tabAction='layerOutput';
$layerWidth=700;
require($MASTER_COMPONENT_ROOT.'/comp_tabs_v210.php');
?>
</p>
	</div>
	
	
	<?php
}else{
	?><?php
}
?>
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