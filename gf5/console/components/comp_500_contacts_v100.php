<?php
//------------------------ Navbuttons head coding v1.43 -----------------------------
//change these first vars and the queries for each instance
$object='Contacts_ID';
$recordPKField='ID'; //primary key field
$navObject='Contacts_ID';
$updateMode='updateContact';
$insertMode='insertContact';
$deleteMode='deleteContact';
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
$ids=q("SELECT ID FROM _v_contacts_master WHERE ".(minroles()<ROLE_AGENT ? '' : 'Creator="'.sun().'"')." ORDER BY CreateDate",O_COL);
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
	if($a=q("SELECT * FROM _v_contacts_master WHERE ID=$Contacts_ID",O_ROW)){
		if(minroles()>=ROLE_AGENT){
			//filter
			if($a['Creator']==sun() || in_array(sun(), q("SELECT Creator FROM gl_leases l, gl_LeasesContacts lc WHERE l.ID=lc.Leases_ID AND lc.Contacts_ID=$Contacts_ID", O_COL))){
				$mode=$updateMode;
				extract($a);
			}else{
				$mode=$insertMode;
				unset($$object);
				$nullAbs=$nullCount+1;
			}
		}else{
			$mode=$updateMode;
			extract($a);
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


//get other contacts from two different locations
$in=($LeasePrimary==1 ? $Leases_ID : ($LeasePrimary > 1 ? implode(',',q("SELECT DISTINCT Leases_ID FROM gl_LeasesContacts WHERE Contacts_ID=$Contacts_ID", O_COL)) : '0'));

if($Contacts_ID){
	$leases=q("SELECT * FROM _v_leases_master WHERE ID IN($in) ORDER BY LeaseStartDate DESC", O_ARRAY);
	if($LeaseSecondaryContacts){
		$contacts=q("SELECT c.ID, c.FirstName, c.LastName, c.Email, c.HomeMobile FROM addr_contacts c, gl_LeasesContacts lc WHERE c.ID=lc.Contacts_ID AND lc.Leases_ID IN($in) AND c.ID!=$Contacts_ID AND lc.Type!='Primary'", O_ARRAY);
	}
}
//prn($leases);
if(!$leases){
	$contacts=unserialize(base64_decode($GLF_Contacts));
}

if(!$refreshComponentOnly){
	?>
	<style type="text/css">
	#mainBody{
		width:1000px;
		border:1px solid #ccc;
		padding:10px;
		margin:10px;
		}
	#section1{
		background-color:cornsilk;
		border:1px dotted #333;
		}
	.secHead{
		font-size:100%;
		font-weight:normal;
		text-transform:uppercase;
		margin-left:-20px;
		font-family:Arial, Helvetica, sans-serif;
		}
	.section{
		padding:5px 10px 10px 25px;
		border:1px dotted #666;
		margin-bottom:10px;
		}
	.leftBox{
		width:600px;
		float:left;
		}
	.rightBox{
		width:320px;
		float:right;
		}
	</style>
	<script language="javascript" type="text/javascript">
	
	
	</script><?php
}
?>

<div id="section2" class="section <?php echo $leases?'rightBox':'leftBox';?>">
<h1 class="secHead">Advertising or Referral Source</h1>
<p>
	<select class="th1" name="Referral" id="Referral" onchange="g('ReferralOther').style.visibility=(this.value=='Other' || this.value=='Referral-To Get Gift Card'?'visible':'hidden');if(this.value=='Other' || this.value=='Referral-To Get Gift Card')g('ReferralOther').focus();dChge(this);">
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
</p>
</div>

<div id="section1" class="section leftBox">
<h1 class="secHead"><?php echo $leases ? 'Tenant(s)' : 'Prospect(s)';?></h1>
<table>
  <tr>
    <td>Primary contact: </td>
	<?php
	$normalclassName='th1';
	?>
    <td><input name="LastName" type="text" class="<?php echo $LastName?$normalclassName:'gray';?>" id="LastName" onfocus="if(this.value=='Last Name'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='Last Name';}" value="<?php echo h($LastName ? $LastName : 'Last Name');?>" size="11" /></td>
    <td><input name="FirstName" type="text" class="<?php echo $FirstName?$normalclassName:'gray';?>" id="FirstName" onfocus="if(this.value=='First Name'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='First Name';}" value="<?php echo h($FirstName ? $FirstName : 'First Name');?>" size="9" /></td>
    <td><input class="<?php echo $Email?$normalclassName:'gray';?>" name="Email" type="text" id="Email" value="<?php echo h($Email ? $Email : 'Email');?>" onfocus="if(this.value=='Email'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='Email';}" /></td>
    <td><input name="HomeMobile" type="text" class="<?php echo $normalclassName; echo $HomeMobile?'':' gray';?>" id="HomeMobile" onfocus="if(this.value=='Phone'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='<?php echo $normalclassName;?> gray';this.value='Phone';}" value="<?php echo h($HomeMobile ? $HomeMobile : 'Phone');?>" size="13" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Customer&nbsp;2:</td>
    <td><?php
	if($secondaryEditable){
		?>
      <input name="contacts[1][ID]" type="hidden" id="contacts[1][ID]" value="<?php echo $contacts[1]['ID'] ? $contacts[1]['ID'] : 1;?>" />
		<input name="contacts[1][LastName]" type="text" class="<?php echo $contacts[1]['LastName']?$normalclassName:'gray';?>" id="LastName1" onfocus="if(this.value=='Last Name'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='Last Name';}" value="<?php echo h($contacts[1]['LastName'] ? $contacts[1]['LastName'] : 'Last Name');?>" size="11" />
		<?php
	}else{
		echo $contacts[1]['LastName']. '&nbsp;';
	}
	?></td>
    <td><?php
	if($secondaryEditable){
		?>
		<input name="contacts[1][FirstName]" type="text" class="<?php echo $contacts[1]['FirstName']?$normalclassName:'gray';?>" id="FirstName1" onfocus="if(this.value=='First Name'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='First Name';}" value="<?php echo h($contacts[1]['FirstName'] ? $contacts[1]['FirstName'] : 'First Name');?>" size="9" />
		<?php
	}else{
		echo $contacts[1]['FirstName']. '&nbsp;';
	}
	?></td>
    <td><?php
	if($secondaryEditable){
		?>
		<input class="<?php echo $contacts[1]['Email']?$normalclassName:'gray';?>" name="contacts[1][Email]" type="text" id="Email1" value="<?php echo h($contacts[1]['Email'] ? $contacts[1]['Email'] : 'Email');?>" onfocus="if(this.value=='Email'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='Email';}" />
		<?php
	}else{
		echo $contacts[1]['Email']. '&nbsp;';
	}
	?></td>
    <td><?php
	if($secondaryEditable){
		?>
		<input name="contacts[1][HomeMobile]" type="text" class="<?php echo $contacts[1]['HomeMobile']?$normalclassName:'gray';?>" id="HomeMobile1" onfocus="if(this.value=='Phone'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='Phone';}" value="<?php echo h($contacts[1]['HomeMobile'] ? $contacts[1]['HomeMobile'] : 'Phone');?>" size="13" />
		<?php
	}else{
		echo $contacts[1]['HomeMobile']. '&nbsp;';
	}
	?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Customer&nbsp;3:</td>
    <td><?php
	if($secondaryEditable){
		?>
      <input name="contacts[2][ID]" type="hidden" id="contacts[2][ID]" value="<?php echo $contacts[2]['ID'] ? $contacts[2]['ID'] : 2;?>" />
		<input name="contacts[2][LastName]" type="text" class="<?php echo $contacts[2]['LastName']?$normalclassName:'gray';?>" id="LastName2" onfocus="if(this.value=='Last Name'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='Last Name';}" value="<?php echo h($contacts[2]['LastName'] ? $contacts[2]['LastName'] : 'Last Name');?>" size="11" />
		<?php
	}else{
		echo $contacts[2]['LastName']. '&nbsp;';
	}
	?></td>
    <td><?php
	if($secondaryEditable){
		?>
		<input name="contacts[2][FirstName]" type="text" class="<?php echo $contacts[2]['FirstName']?$normalclassName:'gray';?>" id="FirstName2" onfocus="if(this.value=='First Name'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='First Name';}" value="<?php echo h($contacts[2]['FirstName'] ? $contacts[2]['FirstName'] : 'First Name');?>" size="9" />
		<?php
	}else{
		echo $contacts[2]['FirstName']. '&nbsp;';
	}
	?></td>
    <td><?php
	if($secondaryEditable){
		?>
		<input class="<?php echo $contacts[2]['Email']?$normalclassName:'gray';?>" name="contacts[2][Email]" type="text" id="Email2" value="<?php echo h($contacts[2]['Email'] ? $contacts[2]['Email'] : 'Email');?>" onfocus="if(this.value=='Email'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='Email';}" />
		<?php
	}else{
		echo $contacts[2]['Email']. '&nbsp;';
	}
	?></td>
    <td><?php
	if($secondaryEditable){
		?>
		<input name="contacts[2][HomeMobile]" type="text" class="<?php echo $contacts[2]['HomeMobile']?$normalclassName:'gray';?>" id="HomeMobile2" onfocus="if(this.value=='Phone'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='Phone';}" value="<?php echo h($contacts[2]['HomeMobile'] ? $contacts[2]['HomeMobile'] : 'Phone');?>" size="13" />
		<?php
	}else{
		echo $contacts[2]['HomeMobile']. '&nbsp;';
	}
	?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Customer&nbsp;4:</td>
    <td><?php
	if($secondaryEditable){
		?>
      <input name="contacts[3][ID]" type="hidden" id="contacts[3][ID]" value="<?php echo $contacts[3]['ID'] ? $contacts[3]['ID'] : 2;?>" />
		<input name="contacts[3][LastName]" type="text" class="<?php echo $contacts[3]['LastName']?$normalclassName:'gray';?>" id="LastName3" onfocus="if(this.value=='Last Name'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='Last Name';}" value="<?php echo h($contacts[3]['LastName'] ? $contacts[3]['LastName'] : 'Last Name');?>" size="11" />
		<?php
	}else{
		echo $contacts[3]['LastName']. '&nbsp;';
	}
	?></td>
    <td><?php
	if($secondaryEditable){
		?>
		<input name="contacts[3][FirstName]" type="text" class="<?php echo $contacts[3]['FirstName']?$normalclassName:'gray';?>" id="FirstName3" onfocus="if(this.value=='First Name'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='First Name';}" value="<?php echo h($contacts[3]['FirstName'] ? $contacts[3]['FirstName'] : 'First Name');?>" size="9" />
		<?php
	}
	?></td>
    <td><?php
	if($secondaryEditable){
		?>
		<input class="<?php echo $contacts[3]['Email']?$normalclassName:'gray';?>" name="contacts[3][Email]" type="text" id="Email3" value="<?php echo h($contacts[3]['Email'] ? $contacts[3]['Email'] : 'Email');?>" onfocus="if(this.value=='Email'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='Email';}" />
		<?php
	}else{
		echo $contacts[3]['Email']. '&nbsp;';
	}
	?></td>
    <td><?php
	if($secondaryEditable){
		?>
		<input name="contacts[3][HomeMobile]" type="text" class="<?php echo $contacts[3]['HomeMobile']?$normalclassName:'gray';?>" id="HomeMobile3" onfocus="if(this.value=='Phone'){this.className='<?php echo $normalclassName;?>';this.value='';}" onblur="if(this.value==''){this.className='gray';this.value='Phone';}" value="<?php echo h($contacts[3]['HomeMobile'] ? $contacts[3]['HomeMobile'] : 'Phone');?>" size="13" />
		<?php
	}else{
		echo $contacts[3]['HomeMobile']. '&nbsp;';
	}
	?></td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>

<?php
$i=0;
if($leases)
foreach($leases as $n=>$v){
	$i++;
	?><div id="section1a-<?php echo $v['ID']?>" class="section leftBox">
	<h1 class="secHead nullBottom">Lease Type</h1>
	<?php echo $v['PropertyName'];?><br />
	<?php echo $v['PropertyAddress']. '  '.$v['PropertyCity'].', '.$v['PropertyState']. '  '.$v['PropertyZip'];?><br />
	
	
	<h1 class="secHead nullBottom">Lease Information</h1>
	<a href="leases.php?Leases_ID=<?php echo $v['ID'];?>" onclick="return ow(this.href,'l1_leases','700,750');" title="View this invoice">Lease #: <?php echo $v['HeaderNumber'];?></a><br />
	Date Signed: <?php echo date('n/j/Y',strtotime($v['LeaseSignDate']));?><br />
	Start of Lease: <?php echo date('n/j/Y',strtotime($v['LeaseStartDate']));?> <em class="gray">(Move-in date)</em><br />
	End of Lease: <?php  echo date('n/j/Y',strtotime($v['LeaseEndDate']));?><br />
	Rent Amount: $<?php echo number_format($v['Rent'],2);?><br />
	Invoice Amount: $<?php echo number_format(-$v['OriginalTotal'],2);?><br />
	
	Address or Unit #: <?php echo $v['UnitNumber'] ? $v['UnitNumber'] : '<em class="gray">(not given)</em>';?><br />
	<?php if(trim($v['LRN'])){ ?>
	Locator Reference: <?php echo $v['LRN'];?><br />
	<?php } ?>
	
	</div><?php
}
?>

<div id="section3-more" class="section leftBox">
	<h1 class="secHead">Desired Move-in Date <input class="th1" name="GLF_MoveInDate" type="text" id="GLF_MoveInDate" value="<?php echo t($GLF_MoveInDate);?>" onchange="dChge(this);" /></h1>
	<h1 class="secHead">Desired Rental Type(s)</h1>
	<?php 
	$GLF_Type=explode(',',$GLF_Type);
	if($GLF_Type)foreach($GLF_Type as $n=>$v)if(!$v)unset($GLF_Type[$n]);
	?>
	<label>
	<input name="Type[]" type="checkbox" id="Type[]" value="APT" <?php echo in_array('APT', $GLF_Type) || !count($GLF_Type) ? 'checked' : ''?> />
	Apartments</label>
	&nbsp;
	<label>
	<input name="GLF_Type[]" type="checkbox" id="Type1" value="DUPLEX" <?php echo in_array('DUPLEX', $GLF_Type) ? 'checked' : ''?> />
	Duplexes</label>
	&nbsp;
	<label>
	<input name="GLF_Type[]" type="checkbox" id="Type2" value="MULTI" <?php echo in_array('MULTI', $GLF_Type) ? 'checked' : ''?> />
	Triplex/Multi</label>
	&nbsp;
	<label>
	<input name="GLF_Type[]" type="checkbox" id="Type3" value="SFR" <?php echo in_array('SFR', $GLF_Type) ? 'checked' : ''?> />
	Homes</label>
	&nbsp;
	<label>
	<input name="GLF_Type[]" type="checkbox" id="Type4" value="TOWN" <?php echo in_array('TOWN', $GLF_Type) ? 'checked' : ''?> />
	Townhouse</label>
	&nbsp;
	<label>
	<input name="GLF_Type[]" type="checkbox" id="Type5" value="OTH" <?php echo in_array('OTH', $GLF_Type) ? 'checked' : ''?> />
	Other</label>
	
	<br />
	<br />
	
	Price range: 
	<input class="th1" name="GLF_PriceRange" type="text" id="GLF_PriceRange" value="<?php echo h($GLF_PriceRange);?>" onchange="dChge(this);" />
	<br />
	Number of bedrooms: 
	<input class="th1" name="GLF_Bedrooms" type="text" id="GLF_Bedrooms" value="<?php echo h($GLF_Bedrooms);?>" size="15" onchange="dChge(this);" />
	<br />
	Number of bathrooms: 
	<input class="th1" name="GLF_Bathrooms" type="text" id="GLF_Bathrooms" value="<?php echo h($GLF_Bathrooms);?>" size="15" onchange="dChge(this);" />
	<br />
	
	<h1 class="secHead">Pets</h1>
	Dog(s): 
	<input class="th1" name="GLF_Dogs" type="text" id="GLF_Dogs" value="<?php echo h($GLF_Dogs);?>" onchange="dChge(this);" />
	<br />
	Cat(s): 
	<input class="th1" name="GLF_Cats" type="text" id="GLF_Cats" value="<?php echo h($GLF_Cats);?>" onchange="dChge(this);" />
	<br />
	
	<h1 class="secHead">Washer &amp; Dryer Preference</h1>
	<label>
	<input class="th1" name="GLF_WD" type="radio" value="2" <?php echo $GLF_WD==2?'checked':''?> onchange="dChge(this);" /> 
	W/D Included</label>
	&nbsp;&nbsp;
	<label>
	<input class="th1" name="GLF_WD" type="radio" value="1" <?php echo $GLF_WD==1?'checked':''?> onchange="dChge(this);" /> 
	W/D Connections Needed</label>
	&nbsp;&nbsp;
	<label>
	<input class="th1" name="GLF_WD" type="radio" value="0" <?php echo $GLF_WD==='0' || !isset($GLF_WD) ? 'checked':''?> onchange="dChge(this);" /> 
	W/D Community Laundry Facility</label>
	
	 <h1 class="secHead">Other Preferences and Notes</h1>
	<textarea class="th1" name="GLF_Preferences" cols="35" rows="3" id="GLF_Preferences" onchange="dChge(this);"><?php echo h($GLF_Preferences);?></textarea>
	
</div>
<div class="cb"> </div>

