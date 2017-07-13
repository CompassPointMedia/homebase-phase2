<?php
if(!$refreshComponentOnly){
	?><style type="text/css">
	.spacing td{
		padding:5px;
		}
	#whoForBox{
		width:40%;
		}
	#whoFor{
		padding:15px;
		background-color:#EDE7DC;
		border:1px dotted #666;
		height:270px;
		}
	#myRecentBox{
		width:50%;
	}
	#myRecent{
		border:1px dotted darkolivegreen;
		overflow-x:hidden;
		overflow-y:scroll;
		height:300px;
		}
	.mR{
		margin-left:5px;
		}
	.mR td{
		border-bottom:1px dotted #666;
		padding:1px 4px;
		}
	#formOptions{
		width:800px;
		}
	#Types{
		 width:275px;
		 border:1px dotted #777;
		 padding:5px;
		 }
	</style>
	<script language="javascript" type="text/javascript">
	function clearCustomer(){
		g('ID').value='';
		g('LastName').value='';
		g('FirstName').value='';
		g('Email').value='';
		g('HomeMobile').value='';
		g('GLF_MoveInDate').value='';
		g('ReferralSource').value='';
		g('ReferralSourceOther').value='';
		g('ReferralSourceOther').style.visibility='hidden';
	}
	</script><?php
}
if($mode=='rentalSearch'){
	$FirstName=str_replace('first name','',$FirstName);
	$LastName=str_replace('last name','',$LastName);
	if(!$GLF_CallIn)$GLF_CallIn=0;
	if(!$GLF_PetInfo)$GLF_PetInfo=0;
	if($ID){
		//existing contact
		
	}else if($FirstName || $LastName || $Email || $HomeMobile){
		if(!$FirstName || !$LastName)error_alert('Enter a first AND last name, or just leave the contact info blank if you want to do a search without a prospect');
		if(!$Email)error_alert('You must enter an email address to do a search for a prospect');
		if(!$HomeMobile)error_alert('You must enter a phone number to do a search for a prospect');
		if(strlen($GLF_MoveInDate) && strtotime($GLF_MoveInDate)==false)error_alert('Enter a valid move-in date for this person');
		if(!$GLF_MoveInDate)$GLF_MoveInDate='today';
		
		$ID=q("INSERT INTO addr_contacts SET
		Creator='".sun()."', 
		CreateDate=NOW(),
		ReferralSource='$ReferralSource',
		FirstName='$FirstName',
		LastName='$LastName',
		Email='$Email',
		HomeMobile='$HomeMobile',
		Notes='$Notes',
		GLF_SendIntentions='$GLF_SendIntentions',
		GLF_MoveInDate='".date('Y-m-d',strtotime($GLF_MoveInDate))."',
		GLF_CallIn='".$GLF_CallIn."',
		GLF_PetInfo='".$GLF_PetInfo."'", O_INSERTID);
	}
	$Contacts_ID=$ID;
	
	//make sure search looks for something besides just "all"
	
	
	
	//error checking
	if(!strtotime($GLF_MoveInDate))error_alert('Select a correct expected move-in date for this search');
	
	if(!count($Type))error_alert('Select at least one type of property to search for');
	
	//logic for rooms, bath etc.
	if(trim($Bedrooms)){
		if(preg_match('/^([.0-9]+)\s*([+-])*\s*([.0-9]+)*$/',$Bedrooms,$a)){
			$l=$a[1];
			$m=$a[2];
			$r=$a[3];
			if($m=='<')$m='-';
			if($m=='>')$m='+';
			switch(true){
				case $l && !$m && !$r:
					$BedroomString=' AND Bedrooms='.$l;
					$BedroomText=$l; 
				break;
				case $l && $m=='-' && !strlen($r):
					$BedroomString=' AND Bedrooms <= '.$l;
					$BedroomText=($r).' or less'; 
				break;
				case strlen($l) && $m=='-' && $r:
					$BedroomString=' AND Bedrooms BETWEEN '.$l.' AND '.$r;
					$BedroomText='between '.$l.' and '.$r;
				break;
				case strlen($l) && $m=='+' && !$r:
					$BedroomString=' AND Bedrooms >= '.$l;
					$BedroomText=$l.' or more';
				break;
				default:
					$err=true;
			}
		}else{
			$err=true;
		}
		if($err)error_alert('Bedrooms not in proper format.  OK examples are: 2; 2+ (two or more); 2-4; 3- (three or less);');
	}
	if(trim($Bathrooms)){
		if(preg_match('/^([.0-9]+)\s*([+-])*\s*([.0-9]+)*$/',$Bathrooms,$a)){
			$l=$a[1];
			$m=$a[2];
			$r=$a[3];
			if($m=='<')$m='-';
			if($m=='>')$m='+';
			switch(true){
				case $l && !$m && !$r:
					$BathroomString=' AND Bathrooms='.$l;
					$BathroomText=$l;
				break;
				case $l && $m=='-' && !strlen($r):
					$BathroomString=' AND Bathrooms <= '.$l;
					$BathroomText=($r).' or less';
				break;
				case strlen($l) && $m=='-' && $r:
					$BathroomString=' AND Bathrooms BETWEEN '.$l.' AND '.$r;
					$BathroomText='between '.$l.' and '.$r;
				 break;
				case strlen($l) && $m=='+' && !$r:
					$BathroomString=' AND Bathrooms >= '.$l;
					$BathroomText=$l.' or more';
				 break;
				default:
					$err=true;
			}
		}else{
			$err=true;
		}
		if($err)error_alert('Bathrooms not in proper format.  OK examples are: 2; 2+ (two or more); 2-4; 3- (three or less);');
	}
	if(preg_match('/([0-9]+)\s*(to|[-]|and)\s*([0-9]+)/i',$PriceRange,$a)){
		$RentString='AND m.Rent BETWEEN '.$a[1].' AND '.$a[3];
		$RentText='between '.number_format($a[1],2).' and '.number_format($a[3],2);
	}else if(preg_match('/^([0-9]+)\s*([+-])*$/',$PriceRange,$a)){
		if($a[2]){
			$RentString=($a[2]=='+' ? 'AND m.Rent >= '.$a[1] : 'AND m.Rent <= '.$a[1]);
			$RentText=($a[2]=='+' ? 'rent is '.number_format($a[1],2).' or more' : 'rent is '.number_format($a[1],2).' or less');
		}else{
			$RentString='AND m.Rent BETWEEN '.($a[1]-400) . ' AND '.($a[1]+125);
			$RentText='between '.number_format($a[1]-400,2) . ' and '.number_format($a[1]+125,2);
		}
	}else if(strlen($PriceRange)){
		error_alert('You entered an invalid price range.  OK examples are: 700 (will get between 300 and 825); 450-575; 700- (700 or less); 450+ (450 or more');
	}
	if($n=count($Properties_ID)){
		if($n==1 && $Properties_ID[0]==''){
			//nothing
		}else{
			$PropertiesString='AND Properties_ID IN(\''.implode("','",$Properties_ID).'\')';
			$PropertiesText=implode(', ',q("SELECT PropertyName FROM gl_properties WHERE ID IN(".implode(', ',$Properties_ID).")", O_COL));
		}
	}
	if($n=count($PropertyCity)){
		if($n==1 && $PropertyCity[0]==''){
			//nothing
		}else{
			$CitiesString='AND PropertyCity IN(\''.implode("','",$PropertyCity).'\')';
			$CitiesText=implode(', ',$PropertyCity);
		}
	}

	//run the search for count only
	if(!$orderBy)$orderBy='m.Rent ASC';
	$sql="SELECT [fieldlist]

	/* number of leases that show present 
	COUNT(DISTINCT l.ID) AS Leases,*/

	FROM
	_v_properties_master_list m LEFT JOIN gl_leases l ON m.ID=l.Units_ID AND 
	/* the unit/house/etc is leased AS OF THE INTENDED MOVE-IN DAY OF THE SEARCH - we filter this out outside of the left join .. */
	'".date('Y-m-d',strtotime($GLF_MoveInDate))."' BETWEEN l.LeaseStartDate AND IF(l.LeaseTerminationDate, l.LeaseTerminationDate, IF(l.LeaseEndDate, l.LeaseEndDate, '2199-12-31'))
	WHERE
	m.Active=1 AND 
	Type IN('".implode("','",$Type)."') 
	$BedroomString
	$BathroomString
	$RentString
	$CitiesString
	$PropertiesString
	AND (
		1
		".($Amenities['PetsAllowed'] ? 'AND PetsAllowed=1':'')."
		".($Amenities['InternetPaid'] ? 'AND InternetPaid=1':'')."
		".($Amenities['FitnessCenter'] ? 'AND FitnessCenter=1':'')."
		".($Amenities['Pool'] ? 'AND Pool=1':'')."
		".($Amenities['HotTub'] ? 'AND HotTub=1':'')."
		".($Amenities['Basketball'] ? 'AND Basketball=1':'')."
		".($Amenities['Furnished'] ? 'AND Furnished=1':'')."
		".($Amenities['WasherDryer'] ? 'AND WasherDryer>='.$Amenities['WasherDryer']:'')."
		".($Amenities['Microwave'] ? 'AND Microwave=1':'')."
		".($Amenities['Dishwasher'] ? 'AND Dishwasher=1':'')."
		".($Amenities['IceMaker'] ? 'AND IceMaker=1':'')."
		".($Amenities['WalkInClosets'] ? 'AND WalkInClosets=1':'')."
	)
	GROUP BY m.ID
	ORDER BY $orderBy";
	prn($sql);
	if(!($count=q(str_replace('[fieldlist]','COUNT(*)',$sql), O_VALUE)))error_alert('This search did not return any matches');
	$_GET['sql']=$sql;
	$_GET['RentText']=$RentText;
	$_GET['BedroomText']=$BedroomText;
	$_GET['BathroomText']=$BathroomText;
	$_GET['CitiesText']=$CitiesText;
	$_GET['PropertiesText']=$PropertiesText;
	
	//store the search and the results at that time
	$str=base64_encode(serialize(stripslashes_deep($_GET)));
	if($Searches_ID){
		q("UPDATE gl_searches SET Contacts_ID='$Contacts_ID', SearchCriteria='$str', FormVersion='$FormVersion', InitialCount='$count', EditDate=NOW(), Editor='".sun()."' WHERE ID='$Searches_ID'");
	}else{
		$Searches_ID=q("INSERT INTO gl_searches SET Contacts_ID='$Contacts_ID', SearchCriteria='$str', FormVersion='$FormVersion', InitialCount='$count', CreateDate=NOW(), Creator='".sun()."'", O_INSERTID);
	}
	//open the page
	?><script language="javascript" type="text/javascript">
	window.parent.ow('/gf5/console/search_result_popup.php?Searches_ID=<?php echo $Searches_ID?>&count=<?php echo $count?>','l1_search','800,800');;
	</script><?php
	$assumeErrorState=false;
	exit;
}

if(!$GLF_MoveInDate)$GLF_MoveInDate='today';
?>
<h1>Property and Apartment Search</h1>

<div id="whoForBox" class="fl">
<h3>New Prospect Search:</h3>
	<div id="whoFor">
		Name:
		  <input name="FirstName" type="text" id="FirstName" value="<?php echo h($FirstName ? $FirstName : 'first name');?>" size="10" onfocus="if(this.value=='first name'){this.value=''; this.className='';}" onblur="if(this.value==''){this.value='first name'; this.className='gray';}" <?php echo $FirstName?'':'class="gray"'?> /> 
		<input name="LastName" type="text" id="LastName" value="<?php echo h($LastName ? $LastName : 'last name');?>" size="10" onfocus="if(this.value=='last name'){this.value=''; this.className='';}" onblur="if(this.value==''){this.value='last name'; this.className='gray';}" <?php echo $LastName?'':'class="gray"'?> />
		[<a title="Get a previous contact" onclick="return ow(this.href+'&lastname='+g('LastName').value.replace('last name',''),'l1_contact','850,400');" href="contacts_alpha.php?cbFunction=searchBy" tabindex="-1">search..</a>] 
		[<a href="#" onclick="return clearCustomer();" tabindex="-1">clear</a>]
		<input name="ID" type="hidden" id="ID" value="<?php echo $ID;?>" />
		<br />
		Email: 
		<input name="Email" type="text" id="Email" value="<?php echo h($Email);?>" />
		<br />
		(Cell) Phone: 
		<input name="HomeMobile" type="text" id="HomeMobile" value="<?php echo h($HomeMobile);?>" />
		<br />
		Target date for lease start: 
		<input name="GLF_MoveInDate" type="text" id="GLF_MoveInDate" value="<?php echo h($GLF_MoveInDate);?>" size="14">
		<br />
		<div class="fr">
		<label><input name="GLF_CallIn" type="checkbox" id="GLF_CallIn" value="1" <?php echo $GLF_CallIn?'checked':'';?> />
		Call-in</label>
		<br />
		<label><input name="GLF_PetInfo" type="checkbox" id="GLF_PetInfo" value="1" <?php echo $GLF_PetInfo?'checked':'';?> onclick="g('PetsAllowed').checked=this.checked;" />
		Show pet info</label><br />
		</div>
		I intend to 
		<select name="EscortIntentions" id="EscortIntentions">
		  <option value="Send" <?php echo $EscortIntentions=='Send'?'selected':''?>>Send</option>
		  <option value="Escort" <?php echo $EscortIntentions=='Escort'?'selected':''?>>Escort</option>
      </select>
	  this prospect
		<br />
		How heard about<?php echo $acctCompanyName;?>:<br />

		<select class="th1" name="ReferralSource" id="ReferralSource" onchange="g('ReferralSourceOther').style.visibility=(this.value=='Other' || this.value=='Referral-To Get Gift Card'?'visible':'hidden');if(this.value=='Other' || this.value=='Referral-To Get Gift Card')g('ReferralSourceOther').focus();dChge(this);">
			<option value="">&lt;Select..&gt;</option>
			<option value="Internet" <?php echo strtolower($ReferralSource)==strtolower('Internet')?'selected':''?>>Internet</option>
			<option value="SM Daily Record" <?php echo strtolower($ReferralSource)==strtolower('SM Daily Record')?'selected':''?>>SM Daily Record</option>
			<option value="University Star" <?php echo strtolower($ReferralSource)==strtolower('University Star')?'selected':''?>>University Star</option>
			<option value="Sign/Walk-In" <?php echo strtolower($ReferralSource)==strtolower('Sign/Walk-In')?'selected':''?>>Sign/Walk-In</option>
			<option value="Flyer" <?php echo strtolower($ReferralSource)==strtolower('Flyer')?'selected':''?>>Flyer</option>
			<option value="Study Breaks Magazine" <?php echo strtolower($ReferralSource)==strtolower('Study Breaks Magazine')?'selected':''?>>Study Breaks Magazine</option>
			<option value="TV Commercial" <?php echo strtolower($ReferralSource)==strtolower('TV Commercial')?'selected':''?>>TV Commercial</option>
			<option value="Shirts" <?php echo strtolower($ReferralSource)==strtolower('Shirts')?'selected':''?>>Shirts</option>
			<option value="Return Visit" <?php echo strtolower($ReferralSource)==strtolower('Return Visit')?'selected':''?>>Return Visit</option>
			<option value="Dancing Guy" <?php echo strtolower($ReferralSource)==strtolower('Dancing Guy')?'selected':''?>>Dancing Guy</option>
			<option value="Marketing on Campus" <?php echo strtolower($ReferralSource)==strtolower('Marketing on Campus')?'selected':''?>>Marketing on Campus</option>
			<option value="Referral-To Get Gift Card" <?php echo strtolower($ReferralSource)==strtolower('Referral-To Get Gift Card')?'selected':''?>>Referral-To Get Gift Card</option>
			<option value="Bobcat Fans Magazine" <?php echo strtolower($ReferralSource)==strtolower('Bobcat Fans Magazine')?'selected':''?>>Bobcat Fans Magazine</option>
			<option value="Other Newspaper" <?php echo strtolower($ReferralSource)==strtolower('Other Newspaper')?'selected':''?>>Other Newspaper</option>
			<option value="Craigslist" <?php echo strtolower($ReferralSource)==strtolower('Craigslist')?'selected':''?>>Craigslist</option>
			<option value="Other" <?php echo strtolower($ReferralSource)==strtolower('Other')?'selected':''?>>Other</option>
		</select>
		&nbsp;&nbsp;
		<input class="th1" type="text" name="ReferralSourceOther" id="ReferralSourceOther" onChange="dChge(this);" style="visibility:<?php echo strtolower($ReferralSource)=='other' || strtolower($ReferralSource)=='referral-to get gift card'?'visible':'hidden'?>" value="<?php echo h($ReferralSourceOther);?>" />
		
		<br />
		<div class="fl">Notes:</div>
		<div class="fl">
		<textarea name="Notes" id="Notes" cols="35" rows="3" onchange="dChge(this);"><?php echo h($Notes);?></textarea>
		</div>
	</div>
</div>
<div id="myRecentBox" class="fl">
	<h3>Recent Prospect Searches:</h3>
	<div id="myRecent">
	<?php 
	if($a=q("SELECT * FROM _v_searches WHERE Creator='".sun()."' AND DATE_ADD(NOW(), INTERVAL -30 DAY) < CreateDate ORDER BY ID DESC", O_ARRAY)){
		?><table class="mR" width="100%">
		<tr>
		<th>Date</th>
		<th>&nbsp;</th>
		<th>Name</th>
		<th>Summary</th>
		<th>&nbsp;</th>
		</tr>
		<?php
		foreach($a as $n=>$v){
			?><tr id="s_<?php echo $v['ID'];?>">
			<td><?php 
			if(!function_exists('t_date_human')){
				function t_date_human($n,$options=array()){
					$Y=date('Y');
					$today=floor(time()/(24*3600));
					$d=floor(strtotime($n)/(24*3600));
					switch(true){
						case $d - $today > 1:
							$in='future';
							return str_replace('/'.$Y,'', date('n/j/Y',strtotime($n)));
						case $d - $today==1:
							return 'tomorrow';
						case $d==$today:
							return 'today';
						case $today - $d ==1:
							return 'yesterday';
						case $today - $d < 7:
							return date('l',strtotime($n));
						default:
							return str_replace('/'.$Y,'', date('n/j/Y',strtotime($n)));
					}
				}
			}
			echo t_date_human($v['CreateDate']) . ' ' . date('g:iA',strtotime($v['CreateDate']));?></td>
			<td><?php
			if($v['Leases_ID']){
				//icon for a lease
				?><a href="leases.php?Leases_ID=<?php echo $v['Leases_ID'];?>" title="View this lease/invoice" onclick="return ow(this.href,'l1_leases','700,700');"><img src="/images/i/note01.gif" width="8" height="10" alt="leased" /></a><?php
			}else echo '&nbsp;';
			?></td>
			<td><?php
			if($v['Contacts_ID']){ 
				?><a href="contacts.php?Contacts_ID=<?php echo $v['Contacts_ID']?>" title="See full information about this prospect/contact" onclick="return ow(this.href,'l1_contacts','700,700');"><?php echo $v['FirstName'] . ' ' . $v['LastName'];?></a><?php
			}else{
				?><em class="gray">(nobody)</em><?php
			}?></td>
			<td><?php
			//extract
			$b=unserialize(base64_decode($v['SearchCriteria']));
			//extract($b);
			ob_start();
			if($b['PriceRange'])echo 'Price range: '.$b['PriceRange'].'; ';
			if($b['Bedrooms'])echo 'Beds: '.$b['Bedrooms'].'; ';
			if($b['Bathrooms'])echo 'Baths: '.$b['Bathrooms'].'; ';
			$out=ob_get_contents();
			ob_end_clean();
			echo rtrim($out,'; ');
			
			?></td>
			<td>[<a href="search_result_popup.php?Searches_ID=<?php echo $v['ID']?>" title="Load this search" onclick="return ow(this.href,'l1_search','750,700');">load</a>]&nbsp;[<a href="resources/bais_01_exe.php?mode=deleteSearch&Searches_ID=<?php echo $v['ID'];?>" onclick="if(!confirm('Delete this search?'))return false" target="w2">x</a>]</td>
			</tr><?php
		}
		?>
		</table><?php
	}else{
		?>
		<em class="gray">No searches within the last 30 days.  Be sure and enter the prospect's name and either email or cell number when you do a search!</em>
		<?php
	}
	?>
	</div>
</div>

<div class="cb" style="height:15px;"> </div>
<div id="formOptions">
<div id="Types" class="fr">
	<h3 class="nullBottom">Search for:</h3>
	<table>
      <tr>
        <td><label>
          <input name="Type[]" type="checkbox" id="Type1" value="APT" <?php echo @in_array('APT',$Type) || !isset($Type) ? 'checked' : ''?> />
Apartments</label>
          <br />
          <label>
          <input name="Type[]" type="checkbox" id="Type2" value="DUPLEX" <?php echo @in_array('DUPLEX',$Type) ? 'checked' : ''?> />
Duplexes</label>
          <br />
          <label>
          <input name="Type[]" type="checkbox" id="Type3" value="MULTI" <?php echo @in_array('MULTI',$Type) ? 'checked' : ''?> />
Triplex/Multi</label></td>
        <td><label>
          <input name="Type[]" type="checkbox" id="Type4" value="SFR" <?php echo @in_array('SFR',$Type) ? 'checked' : ''?> />
Homes</label>
          <br />
          <label>
          <input name="Type[]" type="checkbox" id="Type5" value="TOWN" <?php echo @in_array('TOWN',$Type) ? 'checked' : ''?> />
Townhouse</label>
          <br />
          <label>
          <input name="Type[]" type="checkbox" id="Type6" value="OTH" <?php echo @in_array('OTH',$Type) ? 'checked' : ''?> />
Other</label></td>
      </tr>
    </table>
</div>
<div class="fl" style="width:450px;">
	Price range: <input name="PriceRange" type="text" id="PriceRange" value="<?php echo h($PriceRange);?>" size="12" />
	<em class="gray">(whole dollars)</em>
	<input name="mode" type="hidden" id="mode" value="rentalSearch">
	<br />
	Bedrooms: <input name="Bedrooms" type="text" id="Bedrooms" value="<?php echo h($Bedrooms);?>" size="7" />
	<em class="gray">(example 1, 2, 3, 2+, 3-, 2-3)</em><br />
	Bathrooms: <input name="Bathrooms" type="text" id="Bathrooms" value="<?php echo h($Bathrooms);?>" size="7" />
	<p> Show sub-leases:
	<input name="ShowSubleases" type="radio" value="0" <?php echo $ShowSubleases==='0'?'checked':''?> />
	No
	&nbsp;&nbsp;
	<input name="ShowSubleases" type="radio" value="1" <?php echo $ShowSubleases==1 || !isset($ShowSubleases) ? 'checked':''?> />
	Yes </p>
</div>
<div class="cb"> </div>
<br />
<br />

<div class="fr" style="width:35%;">
 <h3 class="nullBottom">Required Amenities and features:</h3>
  <table border="0" cellspacing="0" cellpadding="4">
    <tr>
      <td><label>
        <input name="Amenities[PetsAllowed]" type="checkbox" id="PetsAllowed" value="1" <?php echo $Amenities['PetsAllowed']?'checked':''?> />
        Pets allowed</label>
          <br />
          <label>
          <input name="Amenities[InternetPaid]" type="checkbox" id="InternetPaid" value="1" <?php echo $Amenities['InternetPaid']?'checked':''?> />
            Internet paid</label>
          <br />
          <label>
          <input name="Amenities[FitnessCenter]" type="checkbox" id="FitnessCenter" value="1" <?php echo $Amenities['FitnessCenter']?'checked':''?> />
            Fitness Center</label>
          <br />
          <label>
          <input name="Amenities[Pool]" type="checkbox" id="Pool" value="1" <?php echo $Amenities['Pool']?'checked':''?> />
            Pool</label>
          <br />
          <label>
          <input name="Amenities[HotTub]" type="checkbox" id="HotTub" value="1" <?php echo $Amenities['HotTub']?'checked':''?> />
            Hot Tub</label>
          <br />
          <label>
          <input name="Amenities[Basketball]" type="checkbox" id="Basketball" value="1" <?php echo $Amenities['Basketball']?'checked':''?> />
            Basketball</label>
          <br />
          <label>
          <input name="Amenities[IndividualLeaseOption]" type="checkbox" id="IndividualLeaseOption" value="1" <?php echo $Amenities['IndividualLeaseOption']?'checked':''?> />
            Individual lease option</label></td>
      <td><input name="Amenities[Furnished]" type="checkbox" id="Furnished" value="1" <?php echo $Amenities['Furnished']?'checked':''?> />
        Furnished<br />
        Washer/Dryer: 
		<select name="Amenities[WasherDryer]" id="WasherDryer">
		<option value="0" class="gray">(doesn't matter)</option>
		<option value="1" <?php echo $Amenities['WasherDryer']==1?'selected':''?>>Connection</option>
		<option value="2" <?php echo $Amenities['WasherDryer']==2?'selected':''?>>Present</option>
		</select><br />
        <input name="Amenities[Microwave]" type="checkbox" id="Microwave" value="1" <?php echo $Amenities['Microwave']?'checked':''?> />
        Microwave<br />
        <input name="Amenities[Dishwasher]" type="checkbox" id="Dishwasher" value="1" <?php echo $Amenities['Dishwasher']?'checked':''?> />
        Dishwasher<br />
        <input name="Amenities[IceMaker]" type="checkbox" id="IceMaker" value="1" <?php echo $Amenities['IceMaker']?'checked':''?> />
        Ice maker<br />
        <input name="Amenities[WalkInClosets]" type="checkbox" id="WalkInClosets" value="1" <?php echo $Amenities['WalkInClosets']?'checked':''?> />
        Walk-in closets<br /></td>
    </tr>
  </table>
  <em class="gray">(NOTE: checking too many of these options may severely limit your search results)</em></div>
<table border="0" cellspacing="0" class="spacing">
  <tr>
    <td><p>Specific Apartments:<br />
	<?php
	if(!$Properties_ID)$Properties_ID=array();
	?>
	<select name="Properties_ID[]" size="10" multiple="multiple" id="Properties_ID[]">
		<option value="" style="font-style:italic;" <?php echo empty($Properties_ID)?'selected':''?>>(no preference)</option>
		<?php
		if($a=q("SELECT
			Properties_ID, Type, PropertyName, PropertyCity
			FROM _v_properties_master_list WHERE Type IN('Apt','Multi') AND Active=1 GROUP BY Properties_ID ORDER BY PropertyCity, PropertyName", O_ARRAY)){
			$i=0;
			foreach($a as $n=>$v){
			if($v['Properties_ID']=='')continue;
			$i++;
			if($i==1 || strtolower($buffer)!==strtolower($v['PropertyCity'])){
			$buffer=$v['PropertyCity'];
			if($i>1)echo '</optgroup>';
				?><optgroup label="<?php echo $buffer?>"><?php
			}
			?><option value="<?php echo $v['Properties_ID']?>" <?php if($v['Properties_ID']!='' && in_array($v['Properties_ID'],$Properties_ID))echo 'selected';?>><?php echo h($v['PropertyName']);?></option><?php
			}
			?>
			</optgroup>
			<?php
		}
		?>
	</select>
    </p></td>
    <td><p>Specific Cities:<br />
	<?php 
	if($a=$userSettings['search:defaultCities']){
		$PropertyCity=explode(',',$a);
	}	
	?>
	<select name="PropertyCity[]" size="10" multiple="multiple" id="PropertyCity[]">
	<option value="" style="font-style:italic;">(no preference)</option>
	<?php
	$Cities=q("SELECT DISTINCT PropertyCity FROM _v_properties_master_list GROUP BY PropertyCity ORDER BY IF(PropertyCity IN('".implode("','",$localCities)."'),1,2), IF(PropertyCity='San Marcos',1,2), PropertyCity", O_COL);
	foreach($Cities as $v){
		?><option value="<?php echo h($v);?>" <?php if(@in_array($v, $PropertyCity) || (empty($PropertyCity) && strtolower($v)=='san marcos'))echo 'selected';?>><?php echo h($v);?></option><?php
	}
	?>
	</select>
	</p></td>
  </tr>
</table>
<div class="cb"> </div>
<input type="submit" name="Submit" value="Begin Search" />
</div>
