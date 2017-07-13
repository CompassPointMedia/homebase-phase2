<?php 
/*
Created 2010-11-24 SF

*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';

$getStates=true;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');


if(minroles()>ROLE_AGENT){
	//clients cannot use this interface
	header('Location: leases_print.php?'.$_SERVER['QUERY_STRING']);
	exit;
}
if($Invoices_ID){
	$Leases_ID=q("SELECT
	lt.Leases_ID
	FROM finan_transactions t, gl_LeasesTransactions lt
	WHERE t.Headers_ID='$Invoices_ID' AND t.ID=lt.Transactions_ID", O_VALUE);
}

//------------------------ Navbuttons head coding v1.43 -----------------------------
//change these first vars and the queries for each instance
$object='Leases_ID';
$recordPKField='ID'; //primary key field
$navObject='Leases_ID';
$updateMode='updateLease';
$insertMode='insertLease';
$deleteMode='deleteLease';
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
$ids=q("SELECT ID FROM gl_leases WHERE 1 ORDER BY LeaseStartDate",O_COL);
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
	if($a=q("SELECT l.*, 
	IF(GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY), 1, 0) AS Late 
	FROM _v_leases_master l WHERE ID=$Leases_ID",O_ROW)){
		$mode=$updateMode;
		extract($a);
		if(!($header=q("SELECT * FROM _v_x_finan_headers_master WHERE ID=$Headers_ID", O_ROW))){
			exit('unable to locate lease header info');
		}
	}else{
		prn($qr);
		//object may have been deleted by another user, least perplexing approach is to present insert mode
		$mode=$insertMode;
		unset($$object);
		$nullAbs=$nullCount+1;
	}
}else{
	$mode=$insertMode;
	$nullAbs=$nullCount+1; //where we actually are right then
}
//--------------------------- end coding --------------------------------
$hideCtrlSection=false;

if($mode==$insertMode){
	//get property info from here
	if($property=q("SELECT p.ID AS Properties_ID, 
	u.LeaseDesired,
	u.Rent,
	p.EscortCommission,
	p.SendCommission,
	PropertyName, PropertyAddress, PropertyCity, PropertyState, PropertyZip, Bedrooms, Bathrooms, SquareFeet, Type FROM gl_properties p, gl_properties_units u WHERE p.ID=u.Properties_ID AND u.ID='$Units_ID'", O_ROW)){
		//OK
		extract($property);
		$LeaseLength=$LeaseDesired;
	}else{
		//2011-02-16: we now allow in-page selection -- exit('page called improperly');
	}
}
//a bit late but filter out Agents
if(minroles()>ROLE_MANAGER && $Leases_ID){
	if(@!in_array($Headers_ID,list_invoices()))exit('You do not have access to this invoice (#'.$HeaderNumber.').  Please contact an Administrator for assistance');
}

$topProducerQuantity=25;
$agents=q("SELECT
IF(COUNT(DISTINCT l.ID)>=$topProducerQuantity,'yes','no') AS TopProducer, COUNT(DISTINCT l.ID) AS Count, un_username, un_firstname, un_middlename, un_lastname
FROM
bais_universal u 
JOIN bais_staff s ON un_username=st_unusername 
LEFT JOIN bais_StaffRoles sr ON s.st_unusername=sr_stusername AND sr_roid>=10 
LEFT JOIN gl_leases l ON l.Agents_username=un_username AND l.CreateDate > DATE_SUB(CURDATE(), INTERVAL 6 MONTH)

WHERE (st_active=1 AND sr_roid IS NOT NULL) ".($Agents_username ? " OR un_username='$Agents_username'":'')."
GROUP BY un_username
ORDER BY IF(COUNT(DISTINCT l.ID)>=$topProducerQuantity,1,2), un_lastname, un_firstname", O_ARRAY);

if(false){
	prn($qr);
	prn($agents,1);
}
ob_start();
$send=($SendCommission>2 ? number_format($SendCommission,2) : number_format($SendCommission * $Rent,2));
$escort=($EscortCommission>2 ? number_format($EscortCommission,2) : number_format($EscortCommission * $Rent,2));
if($Units_ID){
	if($SendCommission<=0 && $EscortCommission<=0){
		?><div style="color:darkred;">Commissions not set up properly! <a href="properties<?php echo strtolower($Type)=='sfr'?2:3?>.php?Units_ID=<?php echo $Units_ID?>" onClick="return ow(this.href,'l1_properties','700,700');">Click to edit property info</a></div>
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
}
$commissionHTML=ob_get_contents();
ob_end_clean();


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Lease Info '.($mode==$insertMode?' - (new lease)': '');?></title>



<link rel="stylesheet" type="text/css" href="/Library/css/undohtml3.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
#properties h2{
	border-bottom:1px solid #777;
	color:#333;
	font-family:Arial, Helvetica, sans-serif;
	padding-bottom:3px;
	margin-left:-10px;
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
	display:none;
	}
#FullAddress{
	border: 1px solid darkred;
	padding:7px;
	}
.gray{
	color:#999;
	}
/* for voiding feature */
#mainBody{
	position:relative;
	}
#headerFlag{
	position:absolute;
	top:100px;
	left:300px;
	opacity:.65;
	filter:alpha(opacity=65);
	}
.t1{
	/*font-size:smaller;*/
	}
.t1 th{
	color:white;
	background-color:darkolivegreen;
	font-weight:400;
	}
.t1 td{
	border-bottom:none;
	}
#voidReason{
	position:absolute;
	top:30px;
	left:-150px;
	border:1px dotted darkred;
	background-color:cornsilk;
	color:white;
	width:250px;
	height:40px;
	padding:5px;
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

var Headers_ID='<?php echo $ID?>';
var HeaderStatus='<?php echo $HeaderStatus;?>';
var datasetObject='invoice';

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already
AddOnkeypressCommand('RFM_KeyPress(e)'); //if not declared already
var isDeletable=true; //required to fire off
var customDeleteHandler='deleteObject()'; //optional; default submits to bais_01_exe.php?mode=(deleteMode)

var isEscapable=1; //1 means confirm if detectChange, 2 means escape regardless of changes

var __send__=<?php echo $SendCommission ? $SendCommission : '0.00'?>;
var __escort__=<?php echo $EscortCommission ? $EscortCommission : '0.00'?>;
function setCommission(n){
	//only happens on insertMode, let's change each time; if(g('Extension').value!=='')return;
	var rent=parseFloat(g('Rent').value);
	if(n==1 || n==2){
		if(__send__>2){
			g('Extension').value=__send__;
		}else if(rent>0 && __send__){
			g('Extension').value=__send__ * rent;
		}
	}else if(n==3){
		if(__escort__>2){
			g('Extension').value=__escort__;
		}else if(rent>0 && __escort__){
			g('Extension').value=__escort__ * rent;
		}
	}
}
function setCommission2(n){
	g('EscortOther').style.visibility=(n==2?'visible':'hidden');
	if(n==2)g('EscortOther').focus();
}
function savePrint(){
	g('printAfter').value='1';
	g('form1').submit();
	g('printAfter').value='';
}
function voidInvoice(n){
	switch(true){
		case n==1:
			g('voidReason').style.visibility='visible';
			g('GLF_VoidReasons_ID').focus();
		break;
		case n==2: //selected, go for the void
		case n==3: //cancel
			if(n==2 && confirm('Are you sure you want to void this invoice? The amount will be set to zero'))window.open('resources/bais_01_exe.php?mode=setVoid&setVoid=1&Leases_ID=<?php echo $Leases_ID;?>&GLF_VoidReasons_ID='+g('GLF_VoidReasons_ID').value,'w2');
			g('GLF_VoidReasons_ID').value='';
			g('voidReason').style.visibility='hidden';
		break;
		case n==4:
			if(confirm('This will unvoid the invoice (however the amount will remain zero). Continue?'))window.open('resources/bais_01_exe.php?mode=setVoid&setVoid=0&Leases_ID=<?php echo $Leases_ID;?>','w2');
	
	}
}
</script>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
	<div class="fr" style="position:relative;">
	<?php if($mode==$updateMode && minroles()<ROLE_CLIENT){ ?>
	<?php
	if(minroles()<ROLE_MANAGER){
		?>
		<input type="button" class="th1b" name="Button" value="<?php echo $GLF_VoidReasons_ID?'Unvoid':'Void';?> Invoice" onClick="voidInvoice(<?php echo $GLF_VoidReasons_ID?4:1;?>)" />
		<div id="voidReason" style="visibility:hidden;">
		<select name="GLF_VoidReasons_ID" id="GLF_VoidReasons_ID" onChange="voidInvoice(2);" class="th1" style="width:175px;">
			<option value="">&lt;Select Reason..&gt;</option>
			<?php
			foreach(q("SELECT ID, Name FROM gl_voidreasons", O_COL_ASSOC) as $n=>$v){
				?><option value="<?php echo $n?>" <?php echo $GLF_VoidReasons_ID==$n?'selected':''?>><?php echo h($v);?></option><?php
			}
			?>
		</select>
		<input type="button" name="Button" value="x" class="th1b" onClick="voidInvoice(3);" />
		</div>
		<?php
	}
	?>
	  <input type="button" class="th1b" name="Button" value="Print Invoice" onClick="ow('leases_print.php?Leases_ID=<?php echo $ID?>', 'l2_print','700,700');" />
	<?php } ?>
	</div>
	<h3><?php echo $PageTitle?></h3>
	<p>
	  Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
    </p>
</div>

</div>
<div id="mainBody">
<div class="fr">
<label> 
<input name="ToBeBilled" type="checkbox" id="ToBeBilled" value="1" <?php echo $ToBeBilled || $mode==$insertMode ? 'checked' : ''?> onClick="if(<?php echo $mode==$insertMode?'true':'false'?> && !confirm('This is a new invoice.  Unchecking this box will list it as a billed invoice.  Are you sure about this?'))this.checked=true;" onChange="dChge(this);" /> 
Needs to be billed</label>
<?php if($mode==$updateMode){ ?>
<br />
<span class="gray">Created: <strong><?php echo str_replace('at 12:00AM','',date('n/j/Y \a\t g:iA',strtotime($CreateDate)));?></strong></span>
<?php
if(($GLF_DiscrepancyDate !='0000-00-00' && $GLF_DiscrepancyDate!='') || $GLF_DiscrepancyReason){
	?><br />
	Discrepancy <?php echo date('n/j/Y', strtotime($GLF_DiscrepancyDate));?>: <span class="red"><?php echo $GLF_DiscrepancyReason;?></span>
	<br />
	<?php
}
if($billed=q("SELECT lb.*, l.CreateDate, l.Creator, l.Quantity, u.un_firstname FROM gl_LeasesBatches lb, gl_batches l LEFT JOIN bais_universal u ON l.Creator=u.un_username WHERE l.Type='Billing' AND lb.Batches_ID=l.ID AND lb.Leases_ID=$Leases_ID ORDER BY lb.EditDate", O_ARRAY)){
	if(count($billed)==1){
		$billed=$billed[1];
		unset($via);
		if($billed['SendMethod'] & 1)$via[]='Print/Mail';
		if($billed['SendMethod'] & 2)$via[]='Auto-fax';
		if($billed['SendMethod'] & 4)$via[]='Email';
		$via=@implode(', ',$via);
		?><br />
		<span title="Billed <?php echo $billed['Quantity']>1?'with '.($billed['Quantity']-1).' others':'by itself';?>"><?php echo 'Billed '. date('n/j \a\t g:iA',strtotime($billed['CreateDate'])).' by '.($billed['un_firstname'] ? $billed['un_firstname'] : $billed['Creator']) . ' via '.$via?></span><br />
		<?php
	}else{
		?><table class="yat t1">
		<thead>
		<tr>
			<th>Billed on</th>
			<th>by</th>
			<th>via</th>
		</tr>
		</thead><?php
		foreach($billed as $v){
			unset($via);
			if($v['SendMethod'] & 1)$via[]='Print/Mail';
			if($v['SendMethod'] & 2)$via[]='Auto-fax';
			if($v['SendMethod'] & 4)$via[]='Email';
			$via=@implode(', ',$via);
			?><tr>
			<td><span title="Billed <?php echo $v['Quantity']>1?'with '.($v['Quantity']-1).' others':'by itself';?>"><?php echo date('n/j \a\t g:iA',strtotime($v['CreateDate']));?></span></td>
			<td><?php echo ($v['un_firstname'] ? $v['un_firstname'] : $v['Creator']);?></td>
			<td><?php echo $via;?></td>
			</tr><?php
		}
		?></table><?php
	}
}
?>
<?php } ?>
</div>
<?php
if($mode==$updateMode){
	if(strtolower($HeaderStatus)=='void'){ 
		$showStatus='void';
	}else if(abs($header['OriginalTotal'])<=abs($header['AmountApplied'])){
		$showStatus='paid';
	}else if($Late){
		$showStatus='pastdue';
	}
	if($showStatus){
		?>
		<style type="text/css">
		</style>
		<div id="headerFlag">
		<img src="/images/i-local/stamp-<?php echo $showStatus;?>.png" alt="status" /><br />
		<?php if($showStatus=='void'){ ?>
		<div style="background-color:cornsilk;">
		<h3 style="font-family:Georgia, 'Times New Roman', Times, serif;color:darkred;">Reason: <?php echo q("SELECT Name FROM gl_voidreasons WHERE ID='$GLF_VoidReasons_ID'", O_VALUE);?></h3>
		<!-- is this a real field? 
		<textarea name="HeaderStatusNotes" cols="50" rows="3" id="HeaderStatusNotes" style="border:none;background-color:transparent;color:darkred;" onChange="dChge(this);"><?php echo h($HeaderStatusNotes);?></textarea>
		-->
		</div>
		<?php } ?>
		</div>
		<?php
	}
}
?>

<div class="suite1">
<div id="topSection">
	<h2>Tenant(s):</h2>
	<?php if($mode==$insertMode){ ?>
	<?php
	//-------------------------------- contacts populator tool --------------------------
	$xrand=rand(1,1000000);
	?>
	<style type="text/css">
	/* Big box with list of options */
	#ajax_listOfOptions{
		position:absolute;	/* Never change this one */
		width:175px;	/* Width of box */
		height:250px;	/* Height of box */
		overflow:auto;	/* Scrolling features */
		border:1px solid #317082;	/* Dark green border */
		background-color:#FFF;	/* White background color */
		text-align:left;
		font-size:0.9em;
		z-index:100;
	}
	#ajax_listOfOptions div{	/* General rule for both .optionDiv and .optionDivSelected */
		margin:0px;		
		padding:1px;
		cursor:pointer;
		/* font-size:0.9em; */
	}
	#ajax_listOfOptions .optionDiv{	/* Div for each item in list */
		
	}
	#ajax_listOfOptions .optionDivSelected{ /* Selected item in the list */
		background-color:#3A4E97;
		color:#FFF;
	}
	#ajax_listOfOptions .optionDivSelected .highlighted{
		background-color:#5667a5;
		font-weight:900;
		color:#fff;
	}
	#ajax_listOfOptions .optionDiv .highlighted{
		background-color:#d9deea;
		font-weight:900;
	}
	#ajax_listOfOptions_iframe{
		background-color:#F00;
		position:absolute;
		z-index:5;
	}
	.highlighted{
		background-color:aliceblue;
		color:#444;
		}
	form{
		display:inline;
	}
	
	.cancellableItem{
		float:left;
		border:1px solid #999;
		padding:4px;
		background-color:#d9deea;
		margin-right:5px;
		}
	.cancel{
		float:right;
		cursor:pointer;
		background-color:darkblue;
		color:white;
		padding:2px;
		margin:-2px -2px -2px 5px;
		}
	.cancellableItem, .cancel{
		-moz-border-radius:4px;
		}
		
	#addContact{
		position:absolute;
		border:1px solid darkblue;
		padding:7px;
		width:500px;
		height:250px;
		background-color:white;
		}
	#contactsBox{
		width:450px;
		border:1px solid darkblue;
		padding:5px;
		}
	#contactList_legend{
		clear:both;
		background-color:#e5e5e5;
		padding:4px;
		margin-top:4px;
		}
	</style>
	<script language="javascript" type="text/javascript">
	var ajax_setValue_function='contactSetValue';
	//var ajax_tab_key_populates=false;
	function contactSetValue(e,inputObj,newID, newLabel){
		//adds contact to visual list and adds to Contacts_ID
		if(newID && newLabel){
			//OK
			var ID=newID;
			var Label=newLabel;
		}else{
			if(!inputObj)inputObj=this;
			var ID=inputObj.id;
			var Label=inputObj.innerHTML;
			Label=Label.replace('<br','|||');
			Label=Label.split('|||')[0];
			Label=Label.replace('<span class="highlighted">','');
			Label=Label.replace('</span>','');
		}

		if(g('Contacts_ID').value.indexOf('|'+ID+'|')>-1){
			//they have already selected this person
		}else{
			//add the person visually
			var str='<div id="c_'+ID+'" class="cancellableItem">';
			str+=Label+'<div class="cancel" onclick="contactCancel(this)">x</div>';
			str+='</div>';
			g('contactList').innerHTML+=str;
			g('Contacts_ID').value+=ID+'|';
		}
		
	
		//hide the list
		ajax_options_hide();
				
		//clear the input and focus again on it
		g('contact<?php echo $xrand?>').value='';
		g('contact<?php echo $xrand?>').focus();
	}
	function contactAddPrepare(){
		//prepares form and sets focus
		o=g('contact<?php echo $xrand?>');
		if(!o.value)return;
		var fn,ln;
		var e=o.value.split(' ');
		if(e.length==2){
			fn=e[0];
			ln=e[1];
		}else if(e.length==1){
			fn=e[0];
		}
		g('addContact').style.visibility='visible';
		g('FirstName').value=(typeof fn=='undefined'?'':fn);
		g('LastName').value=(typeof ln=='undefined'?'':ln);
		g('personName').innerHTML=(typeof fn!=='undefined' || typeof ln!=='undefined' ? ', ':'')+(typeof fn!=='undefined' ? fn : '')+' '+(typeof ln!=='undefined' ? ln : '');
		g('FirstName').focus();
		
		//ajax_options_hide();
	}
	function contactAddCancel(){
		//close and clear form
		g('contact<?php echo $xrand?>').value=''; 
		g('addContact').style.visibility='hidden'; 
		for(i in {'FirstName':1,'LastName':2,'MiddleName':3,'HomeAddress':4,'HomeCity':5,'HomeZip':6,'HomeMobile':7,'HomePhone':8})g(i).value='';
		g('HomeState').selectedIndex=0;
		g('personName').innerHTML=' ';
		g('contact<?php echo $xrand?>').focus();
		return false;
	}
	function contactCancel(o){
		//removes contact from visual list and from Contacts_ID
		var reg=/c_[0-9]/
		var el=getParentMatching(o,reg);
		var Contacts_ID=el.id.replace('c_','')
		g('Contacts_ID').value=g('Contacts_ID').value.replace('|'+Contacts_ID+'|','|');
		el.style.display='none';
		g('contact<?php echo $xrand?>').focus();
	}
	function contactAdd(o){
		//submits new contact
		var buffer1=g('mode').value;
		var buffer2=g('submode').value;
		g('mode').value='QuickAddContact';
		g('submode').value='populateContactList';
		g('form1').submit();
		g('mode').value=buffer1;
		g('submode').value=buffer2;
		return false;
	}
	</script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/ajax-dynamic-list.js"></script>
	<div id="contactsBox">
		<span id="contactList"><?php
		if($Contacts_ID=preg_replace('/[^,0-9]/','',$Contacts_ID)){
			$Contacts_ID=(strstr($Contacts_ID,',') ? explode(',',$Contacts_ID) : array($Contacts_ID));
			if($contacts=q("SELECT ID, FirstName, LastName, MiddleName FROM addr_contacts WHERE ID IN('".implode("','",$Contacts_ID)."') ORDER BY IF(ID='".current($Contacts_ID)."',1,2)", O_ARRAY)){
				foreach($contacts as $v){
					?><div id="c_<?php echo $v['ID'];?>" class="cancellableItem"><?php echo $v['FirstName'] . ' '.$v['LastName']?><div class="cancel" onClick="contactCancel(this)">x</div></div><?php
				}
			}
		}
		?> </span>
		<input type="text" id="contact<?php echo $xrand?>" name="contact<?php echo $xrand?>" value="" onKeyUp="ajax_showOptions(this,'getContactsByLetters',event)" onBlur="setTimeout('contactAddPrepare();',400)" class="th1" />
		<script language="javascript" type="text/javascript">
		g('contact<?php echo $xrand?>').focus();
		</script>
		<input type="hidden" name="Contacts_ID" id="Contacts_ID" value="|<?php if(!empty($Contacts_ID))echo implode('|',$Contacts_ID).'|';?>" />
		<div id="contactList_legend"><?php 
		prn($Contacts_ID);
		?>
		Select tenant(s) by typing their first or last name. The  first tenant entered will be identified as the Primary Tenant of this  invoice.</div>
	</div>
	<div id="addContact" style="visibility:hidden;">
		<div id="addContactHeader">Add New Tenant..</div>
		<div>This person<span id="personName"> </span>is not recognized.  Add them now</div>
		First name: <input name="FirstName" type="text" class="th1" id="FirstName" onChange="dChge(this);" size="12" /> 
		Middle: 
		<input name="MiddleName" type="text" class="th1" id="MiddleName" onChange="dChge(this);" size="5" /> 
		Last: 
		<input name="LastName" type="text" class="th1" id="LastName" onChange="dChge(this);" size="15" />
		<br />
		Email: 
		<input class="th1" name="Email" type="text" id="Email" onChange="dChge(this);" /> 
		<em>(required)</em><br />
		
		Cell phone: 
		<input class="th1" name="HomeMobile" type="text" id="HomeMobile" onChange="dChge(this);" />
		<br />
		
		Other phone: 
		<input class="th1" name="HomePhone" type="text" id="HomePhone" onChange="dChge(this);" />
		<br />
		
		<div class="fl">Permanent address:<br />
		</div>
		<div class="fl">
		  <input class="th1" name="HomeAddress" type="text" id="HomeAddress" onChange="dChge(this);" />
		  <em>(optional)</em><br />
		  <input class="th1" name="HomeCity" type="text" id="HomeCity" onChange="dChge(this);" />
		  , 
		  <select name="HomeState" id="HomeState" onChange="dChge(this);" style="width:125px;" class="th1">
		  <option value="">&lt;Select..&gt;</option>
		  <?php
		  foreach($states as $n=>$v){
			?><option value="<?php echo $n?>"><?php echo h($v);?></option><?php
		  }
		  ?>
		  </select>
		  <input name="HomeZip" type="text" class="th1" id="HomeZip" onChange="dChge(this);" size="7" />
		</div>
		<div class="fr">
		  <input type="button" name="Button" value="Cancel" class="th1b" onClick="return contactAddCancel();" tabindex="-1" />
		</div>
		<div class="fr">
		  <input type="submit" name="Submit2" value="Add Contact" class="th1b" onClick="return contactAdd();" />
		</div>

		<div class="cb"> </div>
	</div>
	<?php }else{ ?>
	<?php
	if($leaseContacts=q("SELECT * FROM
		gl_LeasesContacts lc LEFT JOIN addr_contacts c ON lc.Contacts_ID=c.ID
		WHERE 
		lc.Leases_ID='$ID' ORDER BY IF(lc.Type='Primary',1,2)", O_ARRAY)){
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
			?><a href="contacts.php?Contacts_ID=<?php echo $v['Contacts_ID']?>" title="view this tenant's information<?php if(strtolower($v['Type'])=='primary')echo ' (this is the primary tenant)';?>" onClick="return ow(this.href, 'l2_contacts','1050,450');" <?php if(strtolower($v['Type'])=='primary')echo 'class="primary"'?>><?php echo $v['FirstName']. ' '.$v['LastName'].(strtolower($v['Type'])=='primary' ? '*':'')?></a><?php
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
	<?php } ?>


	<script language="javascript" type="text/javascript">
	function changeProperty(o){
		if(o.value=='')return;
		if(o.value=='{RBADDNEW}'){
			newOption(o, '/gf5/console/properties2.php', 'l1_properties', '750,750', 'disposition=sfr');
			return false;
		}
		g('pullPropertyPending').style.display='inline';
		window.open('/gf5/console/resources/bais_01_exe.php?mode=pullProperty&Units_ID='+o.value,'w2');
	}
	</script>
	<h2 class="nullBottom">Property:</h2>
	<?php
	if($mode==$insertMode || minroles()<ROLE_MANAGER){
		?>
		<select class="th1" id="Units_ID" name="Units_ID" onChange="changeProperty(this);dChge(this);" cbtable="finan_clients">
			<option value="">&lt;Select Property..&gt;</option>
			<option value="{RBADDNEW}" style="background-color:thistle;">&lt;Add new non-apartment..&gt;</option>
			<?php
			if($units=q("SELECT 
				ID, PropertyName, Bedrooms, Bathrooms, Type, Rent, SquareFeet
				FROM _v_properties_master_list p
				WHERE 1 ".($Clients_ID ? "AND p.Clients_ID=$Clients_ID" : '')." ORDER BY Type, ClientName, PropertyName, Bedrooms DESC", O_ARRAY)){
				$i=0;
				foreach($units as $v){
					if(!$v['ID'])continue;
					$i++;
					if(strtolower($v['Type'])!==$buffer){
						if($i>1)echo '</optgroup>';
						$buffer=strtolower($v['Type']);
						?><optgroup label="<?php echo $typeLabels[$buffer]?>"><?php
					}
					?><option value="<?php echo $v['ID']?>" <?php echo $Units_ID==$v['ID']?'selected':''?>><?php 
					echo $v['PropertyName'] . ' - '.
					($v['Bedrooms']?$v['Bedrooms']:'0').'/'.
					($v['Bathrooms']?$v['Bathrooms']:'0').
					($v['SquareFeet']>0? ', '.$v['SquareFeet'].' sqft':'').
					($v['Rent']?', $'.number_format($v['Rent'],2):'unspecified rent');
					?></option><?php
				}
				?></optgroup><?php
			}
			
			?>
		</select> <span id="pullPropertyPending" style="display:none;"><img src="/images/i/ani/ani-gif-bars-ltgreen.gif" width="43" height="11" alt="processing.." /></span><br />
		<p class="gray">Don't see the property being leased? Invoicing an apartment requires that the property be set up in the Properties section of this database. Or, <a href="properties3.php" title="Add/set up a new apartment complex" onClick="return ow(this.href,'l1_properties','750,700',true);">click here</a> to add an apartment, or <a href="properties2.php" title="Add a new single property" onClick="return ow(this.href,'l1_properties','750,700',true);">here</a> to add a single property </p>
		<?php
	}else{
		?>
		: <?php echo $PropertyName?> <span style="font-size:13px;">[<a href="properties<?php echo strtolower($Type)=='sfr' ? 2 : 3?>.php?Units_ID=<?php echo $Units_ID?>" onClick="return ow(this.href,'l1_properties','700,700');" title="Edit this property info">edit</a>]</span>
		<input type="hidden" name="Units_ID" id="Units_ID" value="<?php echo $Units_ID?>" />
		<?php
	}
	?>
	<div id="FullAddress" style="display:<?php echo $Units_ID || $mode==$updateMode?'block':'none'?>;">
	<?php echo $PropertyAddress?><br />
	<?php echo $PropertyCity.', '.$PropertyState. '&nbsp;&nbsp;'.$PropertyZip?>
	</div>
	

	<h2>Lease Info:</h2>
	<div id="unitInfo">
	<?php 
	if($Units_ID && (!$Bedrooms || !$Bathrooms)){
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
	<div class="fr">
	Address or Unit #:
    <input name="UnitNumber" type="text" class="th1" id="UnitNumber" onChange="dChge(this);" value="<?php echo h($UnitNumber);?>" size="7" /><br />
	<span class="gray" style="font-size:smaller;">Leave blank if you don't know</span>
    <br />
    Locator Ref #: 
    <input name="LRN" type="text" class="th1" id="LRN" onChange="dChge(this);" value="<?php echo h($LRN);?>" size="7" />
	</div>
	<p>
	Date signed: 
	  <input name="LeaseSignDate" type="text" class="th1" id="LeaseSignDate" onChange="dChge(this);if(g('HeaderDate').value=='')g('HeaderDate').value=this.value;" value="<?php echo t($LeaseSignDate, f_qbks);?>" size="12" />
	  <br />
	Move-in date: 
	<input name="LeaseStartDate" type="text" class="th1" id="LeaseStartDate" onChange="dChge(this);if(g('HeaderDate').value=='')g('HeaderDate').value=this.value;" value="<?php echo t($LeaseStartDate, f_qbks);?>" size="12" />
    <em>(Start of lease)</em><br />
End of lease:
<input name="LeaseEndDate" type="text" class="th1" id="LeaseEndDate" onChange="dChge(this);" value="<?php echo t($LeaseEndDate, f_qbks);?>" size="12" />
<br />
Rent amount: 
	  <input name="Rent" type="text" class="th1" id="Rent" onChange="dChge(this);" value="<?php if($Rent)echo number_format($Rent,2);?>" size="7" />
	&nbsp;&nbsp;&nbsp;&nbsp;
	<label>
    <input name="IndividualLease" type="checkbox" id="IndividualLease" value="1" <?php echo $IndividualLease?'checked':''?> />
 Individual Lease</label>
	<br />
	Length of lease: 
	<select class="th1" name="LeaseLength" id="LeaseLength" style="width:125px;" onChange="dChge(this);">
		<option value="">&lt;Select..&gt;</option>
		<option value="1" <?php echo $LeaseLength==1?'selected':''?>>month-to-month</option>
		<option value="2" <?php echo $LeaseLength==2?'selected':''?>>2 mo.</option>
		<option value="3" <?php echo $LeaseLength==3?'selected':''?>>3 mo.</option>
		<option value="4" <?php echo $LeaseLength==4?'selected':''?>>4 mo.</option>
		<option value="5" <?php echo $LeaseLength==5?'selected':''?>>5 mo.</option>
		<option style="background-color:silver;" value="6" <?php echo $LeaseLength==6?'selected':''?>>6 months</option>
		<option value="7" <?php echo $LeaseLength==7?'selected':''?>>7 mo.</option>
		<option value="8" <?php echo $LeaseLength==8?'selected':''?>>8 mo.</option>
		<option value="9" <?php echo $LeaseLength==9?'selected':''?>>9 mo.</option>
		<option value="10" <?php echo $LeaseLength==10?'selected':''?>>10 mo.</option>
		<option value="11" <?php echo $LeaseLength==11?'selected':''?>>11 mo.</option>
		<option style="background-color:silver;" value="12" <?php echo $LeaseLength==12 || !$LeaseLength?'selected':''?>>One year</option>
		<option value="13" <?php echo $LeaseLength==13?'selected':''?>>13 mo.</option>
		<option value="14" <?php echo $LeaseLength==14?'selected':''?>>14 mo.</option>
		<option value="15" <?php echo $LeaseLength==15?'selected':''?>>15 mo.</option>
		<option value="16" <?php echo $LeaseLength==16?'selected':''?>>16 mo.</option>
		<option value="17" <?php echo $LeaseLength==17?'selected':''?>>17 mo.</option>
		<option value="18" <?php echo $LeaseLength==18?'selected':''?>>18 mo.</option>
	</select>
	<br />
	Tenant was:
	<select name="Escort" id="Escort" class="th1" onChange="dChge(this);<?php if($mode==$insertMode && ($send || $escort)){ ?>setCommission(this.value);<?php } ?>setCommission2(this.value);">
		<option value="">&lt;Select..&gt;</option>
		<option value="3" <?php echo ($Escort)=='3'?'selected':''?>>Escorted to property</option>
		<option value="2" <?php echo ($Escort)=='2'?'selected':''?>>Escorted elsewhere but not to this property</option>
		<option value="1" <?php echo ($Escort)=='1'?'selected':''?>>Sent (not escorted)</option>
		<option value="0" <?php echo ($Escort)==='0'?'selected':''?>>&lt;Unknown&gt;</option>
	</select>
	<span id="EscortOther" style="visibility:<?php echo $Escort==2?'visible':'hidden';?>">
	specify..
	<input class="th1" type="text" name="EscortOther" id="EscortOther" onChange="dChge(this);" value="<?php echo h($EscortOther);?>" /> 
	</span>
	<br />
	Escort Date: 
	<input name="EscortDate" type="text" class="th1<?php echo $mode==$insertMode?' gray':''?>" id="EscortDate" onChange="dChge(this)" value="<?php echo $mode==$insertMode ? 'N/A' : t($EscortDate, f_qbks);?>" size="12" onFocus="if(this.value==('N'+'/A')){this.className='th1';this.value='';}" />
	<br />
	<br />
	<?php if($mode==$updateMode){ ?>
	Early Termination Date: 
	<input style="background-color:lightpink;" name="LeaseTerminationDate" type="text" class="th1" id="LeaseTerminationDate" onChange="dChge(this);" value="<?php echo t($LeaseTerminationDate, f_qbks);?>" size="12" /> 
	<?php }?>
	</p>
	<h2>Invoice Info <?php if($mode==$updateMode){ ?>(Invoice #<?php echo q("SELECT HeaderNumber FROM finan_headers WHERE ID=$Headers_ID", O_VALUE)?> for <?php echo number_format(-$Extension,2)?>)<?php } ?></h2>
	<div id="commissionInfo">
	<?php if($mode==$insertMode){ ?>
		<?php echo $commissionHTML;?>
	<?php }else{ ?>
		<strong>Amount: <?php echo number_format(-$Extension,2);?></strong>
	<?php } ?>
	</div>
	<p>
	Invoice Number <input class="th1" name="HeaderNumber" type="text" id="HeaderNumber" onChange="dChge(this);" value="<?php echo $mode==$insertMode ? q("SELECT MAX(CAST(HeaderNumber AS UNSIGNED)) FROM finan_headers WHERE HeaderType='Invoice'", O_VALUE)+1 : $HeaderNumber;?>" size="6" <?php if($mode==$insertMode){ ?>disabled="disabled"<?php } ?> />
	&nbsp;&nbsp;&nbsp;
	<?php if($mode==$insertMode && minroles()<ROLE_AGENT){ ?>
	<label><input name="AutoCreate" type="checkbox" id="AutoCreate" onClick="g('HeaderNumber').disabled=this.checked;if(!this.checked)g('HeaderNumber').focus();" value="1" checked="checked" tabindex="-1" /><em>(auto-create)</em>
	</label>
	<?php } ?>
	<br />
	
	
	Date  on invoice: 
	<input class="th1" name="HeaderDate" type="text" id="HeaderDate" onChange="dChge(this);" value="<?php if($mode==$updateMode)echo date('n/j/Y',strtotime($HeaderDate));?>" size="12" <?php if(minroles()>ROLE_MANAGER){ ?> readonly="readonly" tabindex="-1" <?php } ?> />
	<br />
	Invoice Amount:
	<input class="th1" onChange="dChge(this);" name="Extension" type="text" id="Extension" value="<?php 
	if($mode==$insertMode && $send && ($send==$escort)){
		echo $send;
	}else if($Extension)echo number_format(-$Extension,2);?>" size="5" />
	
	<h2 class="nullBottom">Commissions:</h2>
	By agent:
	<?php
	if(minroles()<=ROLE_AGENT){
		?><select class="th1" name="Agents_username" id="Agents_username" onChange="dChge(this);">
		  <option value="">&lt;Select..&gt;</option>
			<?php
			$i=0;
			if($agents)
			foreach($agents as $v){
				$i++;
				if($v['TopProducer']!=$buffer){
					if($i>1)echo '</optgroup>';
					$buffer=$v['TopProducer'];
					?><optgroup label="<?php echo $v['TopProducer']=='yes'?'Top Producers':'Other'?>"><?php
				}
				?><option value="<?php echo $v['un_username']?>" <?php echo $ha=(
		
				$Agents_username==$v['un_username'] ? 'selected' : (($mode==$insertMode && $_SESSION['admin']['roles'][ROLE_AGENT] && minroles()==ROLE_AGENT && sun()==$v['un_username']) ? 'selected ': '')
				
				);
				if($ha)$haveAgent=true;
				
				?>><?php echo h($v['un_lastname'] . ($v['un_lastname'] && $v['un_firstname'] ?  ', ':''). $v['un_firstname'].($v['TopProducer']=='yes'?' ('.$v['Count'].')':''))?></option> <?php
			}
			if($Agents_username && !$haveAgent){
				mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='no match for agent name in leases.php query'),$fromHdrBugs);
				?><option selected="selected" value="<?php echo $Agents_username?>"><?php echo $Agents_username;?></option><?php
			}
			?>
			</optgroup>
		</select><?php
	}else{
		?>
		<strong><?php echo $mode==$insertMode ? $_SESSION['admin']['firstName']. ' ' . $_SESSION['admin']['lastName'] : ($Agents_username ? q("SELECT CONCAT(un_firstname, ' ', un_lastname) FROM bais_universal WHERE un_username='$Agents_username'", O_VALUE) : '<em>(not selected)</em>');?></strong>
		<input type="hidden" name="Agents_username" id="Agents_username" value="<?php echo $Agents_username?>" />
		<?php
	}
	?>
	<br />
	    Additional agent split:
	    <select class="th1" name="SubSplit" id="SubSplit" onFocus="buffer=this.value;" onChange="dChge(this); dollarAmt(this,1,'SubSplit');">
	      <option value="">&lt;none&gt;</option>
	      <?php
	  for($i=.05; $i<=1.001; $i+=.05){
	  	?>
	      <option value="<?php echo $i?>" <?php echo abs($SubSplit-$i)<.001 || ($mode==$insertMode && $i==.5)?'selected':''?>><?php echo round($i*100).'%'?></option>
	      <?php
	  }
	  ?>
      <option value="{RBADDNEW}" <?php if($SubSplit>2)echo 'selected';?>>Specific $ amount..</option>
      </select>
	  <input name="SubSplit_RBADDNEW" type="text" class="th1" id="SubSplit_RBADDNEW" value="<?php if($SubSplit>2)echo number_format($SubSplit,2);?>" size="7" style="visibility:<?php echo $SubSplit>2?'visible':'hidden'?>;" />
  &nbsp;&nbsp;&nbsp;<br />
  who for:
  <select class="th1" name="SubAgents_username" id="SubAgents_username" onChange="dChge(this);">
    <option value="">&nbsp;</option>
    <?php
		if($agents){
			foreach($agents as $v){
				?>
    <option value="<?php echo $v['un_username']?>" <?php echo $SubAgents_username==$v['un_username']?'selected':''?>><?php echo h($v['un_lastname'] . ', '. $v['un_firstname'])?></option>
    <?php
			}
		}
		?>
  </select><br />
  <script language="javascript" type="text/javascript">
  function GCInterlock(o){
  	var node=(o.id).replace(/[a-zA-Z]/gi,'');
	g('gcDetail'+node).style.display=(o.value?'block':'none');
  }
  </script>
  <style type="text/css">
  #gcDetail1, #gcDetail2{
  	border:1px solid #000;
	background-color:#f5f5f5;
	padding:7px;
	margin:-5px;
	}
  </style>
  <?php if($mode==$updateMode && minroles()<ROLE_AGENT){ ?>
  <script language="javascript" type="text/javascript">
  function gc(o){
  	var i=o.getAttribute('initialstate');
	switch(true){
		case o.checked && i=='1': 
		case !o.checked && i=='0':
			//no action needed
		break;
		case o.checked && i=='0': 
			if(!confirm('This will mark this (or these) gift cards as paid and place it in a single batch; continue?'))o.checked=false;
		break;
		case !o.checked && i=='1': 
			if(!confirm('This will mark this (or these) gift cards as unpaid and remove it from the batch; continue?'))o.checked=true;
		break;
			
	}
  }
  </script>
  <input type="hidden" name="GiftCardInitialState" value="<?php echo $GCBatch1;?>" />
  <label><input name="GiftCardPaid" type="checkbox" id="GiftCardPaid" value="1" <?php echo $GCBatch1?'checked':''?> onChange="dChge(this);" onClick="gc(this)" initialstate="<?php echo $GCBatch1 ? 1 : 0;?>" /> 
  Gift cards are paid</label> <span class="gray"><?php
  if($GCBatch1){
  	$gc=q("SELECT * FROM gl_batches WHERE ID=$GCBatch1", O_ROW);
	echo 'Batch '.$gc['ID'].' by '. $gc['Creator']. ' on '.str_replace('at 12:00AM','',date('m/d/Y \a\t g:iA',strtotime($gc['CreateDate'])));
  }
  ?></span>
  <?php } ?>
  <table width="100%" border="0" cellspacing="5" cellpadding="5">
	<?php
	/*
	$a=array(
		'Name'=>'Mary O\'Milner',
		'Business'=>'3rd Business',
	);
	$x=(serialize($a));
	$x2=(($GCData2));
	prn("|$x|");
	prn("|$x2|");
	prn(strlen($x) . ':' . strlen($x2));
	prn(unserialize($x.''));
	prn(unserialize($x2.''));
	for($i=0; $i<strlen($x); $i++){
		if($x[$i]!=$x2[$i])echo 'error on '.($i+1).'<br />';
	}
	exit;
	*/
	if(strlen($GCData1))$GCData1=unserialize(($GCData1));
	if(strlen($GCData2))$GCData2=unserialize(($GCData2));
	?>
    <tr>
      <td valign="top" style="width:50%">Gift card 1:
        <select class="th1" name="GiftCard1" id="GiftCard1" onChange="dChge(this);GCInterlock(this);">
          <option value="">(None)</option>
          <option value="10" <?php echo $GiftCard1==10?'selected':''?>>$10</option>
          <option value="25" <?php echo $GiftCard1==25?'selected':''?>>$25</option>
          <option value="50" <?php echo $GiftCard1==50?'selected':''?>>$50</option>
        </select><br />


		<div id="gcDetail1" style="display:<?php echo strlen($GCData1) || $GiftCard1 ? 'block':'none'?>">
	    Agent: 
        <select name="GCData1Agent" id="GCData1Agent" onChange="dChge(this);simpleNew(this,event);" class="th1">
		<option value="">&lt;Select..&gt;</option>
		<option style="background-color:thistle;" value="{RBADDNEW}">&lt;Add new..&gt;</option>
		<?php
		if($_agent=q("SELECT Name FROM aux_gl_agent ORDER BY Name", O_COL))
		foreach($_agent as $v){
			?><option value="<?php echo h($v)?>" <?php echo $v==$GCData1['Agent']?'selected':''?>><?php echo h($v);?></option><?php
		}
		?>
        </select><?php
		/* new field value */
		?><input name="GCData1Agent_RBADDNEW" type="text" id="GCData1Agent_RBADDNEW" style="display:none;margin:4px 0px 4px 0px;" onBlur="simpleNew(this);if(this.value!=='')dChge(this);" onKeyUp="simpleNew(this,event);" value="" class="th1"  /><?php
		/* cancel */
		?><input id="GCData1Agent_RBADDNEWCXL" type="button" onClick="simpleNew(this);" value="X" title="Cancel new entry for this field" style="display:none;" class="cancelNewEntryButton" />
        <br />
		
        Business: 
        <select name="GCData1Business" id="GCData1Business" onChange="dChge(this);simpleNew(this,event);" class="th1">
		<option value="">&lt;Select..&gt;</option>
		<option style="background-color:thistle;" value="{RBADDNEW}">&lt;Add new..&gt;</option>
		<?php
		if($_business=q("SELECT Name FROM aux_gl_business ORDER BY Name", O_COL))
		foreach($_business as $v){
			?><option value="<?php echo h($v)?>" <?php echo $v==$GCData1['Business']?'selected':''?>><?php echo h($v);?></option><?php
		}
		?>
        </select><?php
		/* new field value */
		?><input name="GCData1Business_RBADDNEW" type="text" id="GCData1Business_RBADDNEW" style="display:none;margin:4px 0px 4px 0px;" onBlur="simpleNew(this);if(this.value!=='')dChge(this);" onKeyUp="simpleNew(this,event);" value="" class="th1"  /><?php
		/* cancel */
		?><input id="GCData1Business_RBADDNEWCXL" type="button" onClick="simpleNew(this);" value="X" title="Cancel new entry for this field" style="display:none;" class="cancelNewEntryButton" />
        <br />
        
		Organization: 
        <select name="GCData1Organization" id="GCData1Organization" onChange="dChge(this);simpleNew(this,event);" class="th1">
		<option value="">&lt;Select..&gt;</option>
		<option style="background-color:thistle;" value="{RBADDNEW}">&lt;Add new..&gt;</option>
		<?php
		if($_organization=q("SELECT Name FROM aux_gl_organization ORDER BY Name", O_COL))
		foreach($_organization as $v){
			?><option value="<?php echo h($v)?>" <?php echo $v==$GCData1['Organization']?'selected':''?>><?php echo h($v);?></option><?php
		}
		?>
        </select><?php
		/* new field value */
		?><input name="GCData1Organization_RBADDNEW" type="text" id="GCData1Organization_RBADDNEW" style="display:none;margin:4px 0px 4px 0px;" onBlur="simpleNew(this);if(this.value!=='')dChge(this);" onKeyUp="simpleNew(this,event);" value="" class="th1"  /><?php
		/* cancel */
		?><input id="GCData1Organization_RBADDNEWCXL" type="button" onClick="simpleNew(this);" value="X" title="Cancel new entry for this field" style="display:none;" class="cancelNewEntryButton" />
        <br />
		
        Name: 
        <input name="GCData1[Name]" type="text" id="GCData1[Name]" onChange="dChge(this);" class="th1" value="<?php echo h($GCData1['Name']);?>" />
        <br />
        Phone: 
        <input name="GCData1[Phone]" type="text" id="GCData1[Phone]" onChange="dChge(this);" class="th1" value="<?php echo h($GCData1['Phone']);?>" />
        <br />
        Email: 
        <input name="GCData1[Email]" type="text" id="GCData1[Email]" onChange="dChge(this);" class="th1" value="<?php echo h($GCData1['Email']);?>" />
        <br />
	  </div>


	  </td>
      <td valign="top" style="width:50%">Gift card 2:
        <select class="th1" name="GiftCard2" id="GiftCard2" onChange="dChge(this);GCInterlock(this);">
          <option value="">(None)</option>
          <option value="10" <?php echo $GiftCard2==10?'selected':''?>>$10</option>
          <option value="25" <?php echo $GiftCard2==25?'selected':''?>>$25</option>
          <option value="50" <?php echo $GiftCard2==50?'selected':''?>>$50</option>
        </select><br />
		<div id="gcDetail2" style="display:<?php echo strlen($GCData2) || $GiftCard2 ? 'block':'none'?>">
	    Agent: 
        <select name="GCData2Agent" id="GCData2Agent" onChange="dChge(this);simpleNew(this,event);" class="th1">
		<option value="">&lt;Select..&gt;</option>
		<option style="background-color:thistle;" value="{RBADDNEW}">&lt;Add new..&gt;</option>
		<?php
		if($_agent)
		foreach($_agent as $v){
			?><option value="<?php echo h($v)?>" <?php echo $v==$GCData2['Agent']?'selected':''?>><?php echo h($v);?></option><?php
		}
		?>
        </select><?php
		/* new field value */
		?><input name="GCData2Agent_RBADDNEW" type="text" id="GCData2Agent_RBADDNEW" style="display:none;margin:4px 0px 4px 0px;" onBlur="simpleNew(this);if(this.value!=='')dChge(this);" onKeyUp="simpleNew(this,event);" value="" class="th1"  /><?php
		/* cancel */
		?><input id="GCData2Agent_RBADDNEWCXL" type="button" onClick="simpleNew(this);" value="X" title="Cancel new entry for this field" style="display:none;" class="cancelNewEntryButton" />
        <br />
		
        Business: 
        <select name="GCData2Business" id="GCData2Business" onChange="dChge(this);simpleNew(this,event);" class="th1">
		<option value="">&lt;Select..&gt;</option>
		<option style="background-color:thistle;" value="{RBADDNEW}">&lt;Add new..&gt;</option>
		<?php
		if($_business)
		foreach($_business as $v){
			?><option value="<?php echo h($v)?>" <?php echo $v==$GCData2['Business']?'selected':''?>><?php echo h($v);?></option><?php
		}
		?>
        </select><?php
		/* new field value */
		?><input name="GCData2Business_RBADDNEW" type="text" id="GCData2Business_RBADDNEW" style="display:none;margin:4px 0px 4px 0px;" onBlur="simpleNew(this);if(this.value!=='')dChge(this);" onKeyUp="simpleNew(this,event);" value="" class="th1"  /><?php
		/* cancel */
		?><input id="GCData2Business_RBADDNEWCXL" type="button" onClick="simpleNew(this);" value="X" title="Cancel new entry for this field" style="display:none;" class="cancelNewEntryButton" />
        <br />
        
		Organization: 
        <select name="GCData2Organization" id="GCData2Organization" onChange="dChge(this);simpleNew(this,event);" class="th1">
		<option value="">&lt;Select..&gt;</option>
		<option style="background-color:thistle;" value="{RBADDNEW}">&lt;Add new..&gt;</option>
		<?php
		if($_organization)
		foreach($_organization as $v){
			?><option value="<?php echo h($v)?>" <?php echo $v==$GCData2['Organization']?'selected':''?>><?php echo h($v);?></option><?php
		}
		?>
        </select><?php
		/* new field value */
		?><input name="GCData2Organization_RBADDNEW" type="text" id="GCData2Organization_RBADDNEW" style="display:none;margin:4px 0px 4px 0px;" onBlur="simpleNew(this);if(this.value!=='')dChge(this);" onKeyUp="simpleNew(this,event);" value="" class="th1" /><?php
		/* cancel */
		?><input id="GCData2Organization_RBADDNEWCXL" type="button" onClick="simpleNew(this);" value="X" title="Cancel new entry for this field" style="display:none;" class="cancelNewEntryButton" />
        <br />
		
        Name: 
        <input name="GCData2[Name]" type="text" id="GCData2[Name]" onChange="dChge(this);" class="th1" value="<?php echo h($GCData2['Name']);?>" />
        <br />
        Phone: 
        <input name="GCData2[Phone]" type="text" id="GCData2[Phone]" onChange="dChge(this);" class="th1" value="<?php echo h($GCData2['Phone']);?>" />
        <br />
        Email: 
        <input name="GCData2[Email]" type="text" id="GCData2[Email]" onChange="dChge(this);" class="th1" value="<?php echo h($GCData2['Email']);?>" />
        <br />
	  </div>
      </td>
    </tr>
  </table>
  

	<input name="GCData1Agent_RBADDNEWMODIFICATION" type="hidden" id="GCData1Agent_RBADDNEWMODIFICATION" value="distinct" />
	<input name="GCData1Business_RBADDNEWMODIFICATION" type="hidden" id="GCData1Business_RBADDNEWMODIFICATION" value="distinct" />
	<input name="GCData1Organization_RBADDNEWMODIFICATION" type="hidden" id="GCData1Organization_RBADDNEWMODIFICATION" value="distinct" />
	<input name="GCData2Agent_RBADDNEWMODIFICATION" type="hidden" id="GCData2Agent_RBADDNEWMODIFICATION" value="distinct" />
	<input name="GCData2Business_RBADDNEWMODIFICATION" type="hidden" id="GCData2Business_RBADDNEWMODIFICATION" value="distinct" />
	<input name="GCData2Organization_RBADDNEWMODIFICATION" type="hidden" id="GCData2Organization_RBADDNEWMODIFICATION" value="distinct" />


	<h2 class="nullBottom">How did you hear about us:</h2>
	<select class="th1" name="Referral" id="Referral" onChange="g('ReferralOther').style.visibility=(this.value=='Other' || this.value=='Referral-To Get Gift Card'?'visible':'hidden');if(this.value=='Other' || this.value=='Referral-To Get Gift Card')g('ReferralOther').focus();dChge(this);">
		<option value="">&lt;Select..&gt;</option>
		<option value="Internet" <?php echo strtolower($Referral)==strtolower('Internet')?'selected':''?>>Internet</option>
		<option value="SM Daily Record" <?php echo strtolower($Referral)==strtolower('SM Daily Record')?'selected':''?>>SM Daily Record</option>
		<option value="University Star" <?php echo strtolower($Referral)==strtolower('University Star')?'selected':''?>>University Star</option>
		<option value="Sign/Walk-In" <?php echo strtolower($Referral)==strtolower('Sign/Walk-In')?'selected':''?>>Sign/Walk-In</option>
		<option value="Flyer" <?php echo strtolower($Referral)==strtolower('Flyer')?'selected':''?>>Flyer</option>
		<option value="Study Breaks Magazine" <?php echo strtolower($Referral)==strtolower('Study Breaks Magazine')?'selected':''?>>Study Breaks Magazine</option>
		<option value="TV Commercial" <?php echo strtolower($Referral)==strtolower('TV Commercial')?'selected':''?>>TV Commercial</option>
		<option value="Shirts" <?php echo strtolower($Referral)==strtolower('Shirts')?'selected':''?>>Shirts</option>
		<option value="Return Visit" <?php echo strtolower($Referral)==strtolower('Return Visit')?'selected':''?>>Return Visit</option>
		<option value="Dancing Guy" <?php echo strtolower($Referral)==strtolower('Dancing Guy')?'selected':''?>>Dancing Guy</option>
		<option value="Marketing on Campus" <?php echo strtolower($Referral)==strtolower('Marketing on Campus')?'selected':''?>>Marketing on Campus</option>
		<option value="Referral-To Get Gift Card" <?php echo strtolower($Referral)==strtolower('Referral-To Get Gift Card')?'selected':''?>>Referral-To Get Gift Card</option>
		<option value="Bobcat Fans Magazine" <?php echo strtolower($Referral)==strtolower('Bobcat Fans Magazine')?'selected':''?>>Bobcat Fans Magazine</option>
		<option value="Other Newspaper" <?php echo strtolower($Referral)==strtolower('Other Newspaper')?'selected':''?>>Other Newspaper</option>
		<option value="Craigslist" <?php echo strtolower($Referral)==strtolower('Craigslist')?'selected':''?>>Craigslist</option>
		<option value="Other" <?php echo strtolower($Referral)==strtolower('Other')?'selected':''?>>Other</option>
	</select>
	&nbsp;&nbsp;
	<input class="th1" type="text" name="ReferralOther" id="ReferralOther" onChange="dChge(this);" style="visibility:<?php echo strtolower($Referral)=='other'?'visible':'hidden'?>" value="<?php echo h($ReferralOther);?>" />



	<h2>Verification:</h2>
	  <?php
	  if(strlen($Verification_ID)){
	  	$verifications=explode(',',trim($Verification_ID));
	  }else{
	  	$verifications=array();
	  }
	  foreach(q("SELECT ID, Name FROM gl_verification", O_COL_ASSOC) as $n=>$v){
	  	?><label>
		<input type="checkbox" name="Verification_ID[<?php echo $n?>]" id="Verification<?php echo $n?>" onChange="dChge(this);" value="<?php echo $n?>" <?php if(in_array($n,$verifications))echo 'checked';?> /> <?php echo $v?>
		</label><br />
		<?php
	  }
	  ?>
	  Additional details if necessary: 
	  <input name="VerificationDetails" type="text" id="VerificationDetails" onChange="dChge(this);" value="<?php echo h($VerificationDetails);?>" size="50" maxlength="255" class="th1" />

	  <h2>Lease Details and Misc.: </h2>
	  <label>
	  <input name="Pets" type="checkbox" class="th1" id="Pets" value="1" <?php echo $Pets?'checked':''?> onChange="dChge(this);" />
	    Pets	  </label>
&nbsp;&nbsp;
	  ..description:
      <input name="PetsDescription" class="th1" onChange="dChge(this);" type="text" id="PetsDescription" value="<?php echo h($PetsDescription);?>" size="30" />
	  <br />
	  Comments:
	    <textarea name="Comments" cols="45" rows="2" class="th1" id="Comments" onChange="dChge(this);"><?php echo h($Comments);?></textarea>
 
 </div>
</div>

<?php 
/* 2011-02-17: first use of comp_navbuttons_v141a.php */
$navbuttonClass='th1b';
$navbuttonOverrideLabel=array(
	'Cancel'		=>'Cancel',
	'SaveAndNew'	=>'Add & Next',
	'SaveAndClose'	=>'Add & Close',
	'OK'			=>'Update Lease',
);
$navbuttonHide=array(
	'Previous'=>true,
	'Next'=>true,
);
$componentRewrite=true;
require($MASTER_COMPONENT_ROOT.'/comp_navbuttons_v141a.php'); 
$navbuttons_out=str_replace(
	'<input id="SaveAndClose"',
	(minroles()<ROLE_AGENT || true ? '<input type="button" value="Add &amp; Print" id="SavePrint" name="SavePrint" onclick="savePrint()" class="th1b" />&nbsp;' : '').
	'<input id="SaveAndClose"',
	$navbuttons_out
);
echo $navbuttons_out;
?>
<input type="hidden" id="printAfter" name="printAfter" value="" />
<p>
An invoice number will automatically be assigned when you select an "Add" option
</p>
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