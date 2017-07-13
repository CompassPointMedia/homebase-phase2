<?php
if(!$refreshComponentOnly){
	?><style type="text/css">
	.spacing td{
		padding:5px 25px;
		}
	</style>
	<script language="javascript" type="text/javascript">
	
	</script><?php
}
if($mode=='rentalSearch'){
	//make sure search looks for something besides just "all"

	//error checking
	if(!strtotime($LeaseStart))error_alert('Select a correct expected move-in date for this search');
	
	//logic for rooms, bath etc.
	if(preg_match('/^([.0-9]+)\s*([+-])*$/',$Bedrooms,$a)){
		if($a[2]){
			$BedroomString=($a[2]=='+' ? 'AND Bedrooms >= '.$a[1] : 'AND Bedrooms <= '.$a[1]);
		}else{
			$BedroomString='AND Bedrooms='.$a[1];
		}
	}else if(preg_match('/([.0-9]+)\s*(to|-|and)\s*([.0-9]+)/i',$Bedrooms,$a)){
		$BedroomString='AND Bedrooms BETWEEN '.$a[1].' AND '.$a[2];
	}
	if($BedroomBathroomMatch){
		$BathroomString=str_replace('Bedrooms','Bathrooms',$BedroomString);
	}else if(preg_match('/^([.0-9]+)\s*([+-])*$/',$Bathrooms,$a)){
		if($a[2]){
			$BathroomString=($a[2]=='+' ? 'AND Bathrooms >= '.$a[1] : 'AND Bathrooms <= '.$a[1]);
		}else{
			$BathroomString='AND Bathrooms='.$a[1];
		}
	}else if(preg_match('/([.0-9]+)\s*(to|-|and)\s*([.0-9]+)/i',$Bathrooms,$a)){
		$BathroomString='AND Bathrooms BETWEEN '.$a[1].' AND '.$a[2];
	}
	if(preg_match('/^([.0-9]+)\s*([+-])*$/',$PriceRange,$a)){
		if($a[2]){
			$RentString=($a[2]=='+' ? 'AND Rent >= '.$a[1] : 'AND Rent <= '.$a[1]);
		}else{
			$RentString='AND Rent='.$a[1];
		}
	}else if(preg_match('/([.0-9]+)\s*(to|[-]|and)\s*([.0-9]+)/i',$Rent,$a)){
		$RentString='AND Rent BETWEEN '.$a[1].' AND '.$a[2];
	}
	if($n=count($Properties_ID)){
		if($n==1 && $Properties_ID[0]==''){
			//nothing
		}else{
			$PropertiesString='AND Properties_ID IN(\''.implode("','",$Properties_ID).'\')';
		}
	}
	if($n=count($PropertyCity)){
		if($n==1 && $PropertyCity[0]==''){
			//nothing
		}else{
			$CitiesString='AND PropertyCity IN(\''.implode("','",$PropertyCity).'\')';
		}
	}

	//run the search for count only
	$sql="SELECT [fieldlist]

	/* number of leases that show present 
	COUNT(DISTINCT l.ID) AS Leases,*/

	FROM
	_v_properties_master_list m LEFT JOIN gl_leases l ON m.ID=l.Units_ID AND 
	/* the unit/house/etc is leased AS OF THE INTENDED MOVE-IN DAY OF THE SEARCH - we filter this out outside of the left join .. */
	'".date('Y-m-d',strtotime($LeaseStart))."' BETWEEN l.LeaseStartDate AND IF(l.LeaseTerminationDate, l.LeaseTerminationDate, IF(l.LeaseEndDate, l.LeaseEndDate, '2199-12-31'))
	WHERE
	m.Active=1 AND 
	Type IN('".implode("','",$Type)."') 
	$BedroomString
	$BathroomString
	$RentString
	$CitiesString
	".($BedroomsSameSize ? 'AND BedroomsSameSize=1':'')."
	$PropertiesString
	AND (
		1
		".($PetsAllowed ? 'AND PetsAllowed=1':'')."
		".($InternetPaid ? 'AND InternetPaid=1':'')."
		".($FitnessCenter ? 'AND FitnessCenter=1':'')."
		".($Pool ? 'AND Pool=1':'')."
		".($HotTub ? 'AND HotTub=1':'')."
		".($Basketball ? 'AND Basketball=1':'')."
		".($Furnished ? 'AND Furnished=1':'')."
		".($WasherDryer ? 'AND WasherDryer=1':'')."
		".($Microwave ? 'AND Microwave=1':'')."
		".($Dishwasher ? 'AND Dishwasher=1':'')."
		".($IceMaker ? 'AND IceMaker=1':'')."
		".($WalkInClosets ? 'AND WalkInClosets=1':'')."
	)
	GROUP BY m.ID";
	prn($sql);
	exit;
	if(!($count=q(str_replace('[fieldlist]','COUNT(*)',$sql), O_VALUE)))error_alert('This search did not return any matches');
	$_GET['sql']=$sql;
	//store the search and the results at that time
	$str=base64_encode(serialize(stripslashes_deep($_GET)));
	if($Searches_ID){
		q("UPDATE gl_searches SET SearchCriteria='$str', FormVersion='$FormVersion', InitialCount='$count' WHERE ID='$Searches_ID'");
	}else{
		$Searches_ID=q("INSERT INTO gl_searches SET SearchCriteria='$str', FormVersion='$FormVersion', InitialCount='$count'", O_INSERTID);
	}
	//open the page
	?><script language="javascript" type="text/javascript">
	window.parent.location='/gf5/console/search_result.php?Searches_ID=<?php echo $Searches_ID?>&count=<?php echo $count?>';
	</script><?php
}

if(!$LeaseStart)$LeaseStart='today';
?>
<h1>Property and Apartment Search</h1>
<p>This search is for (name - optional):
  <input name="SearchFor" type="text" id="SearchFor" value="<?php echo h($SearchFor);?>" />
  <br />
  Email: 
  <input name="Email" type="text" id="Email" value="<?php echo h($Email);?>" />
  <br />
  Phone: 
  <input name="Phone" type="text" id="Phone" value="<?php echo h($Phone);?>" />
  <br />
  <br />
  <br />
  Search for :  
  <label>
  <input name="Type[]" type="checkbox" id="Type[]" value="Apt" checked="checked" />
Apartments</label>
  &nbsp;
  <label>
  <input name="Type[]" type="checkbox" id="Type[]" value="Duplex" />
Duplexes</label>
  &nbsp;&nbsp;
  <label>
  <input name="Type[]" type="checkbox" id="Type[]" value="Multi" />
Triplex/Multi</label>
  &nbsp;&nbsp;
  <label>
  <input name="Type[]" type="checkbox" id="Type[]" value="SFR" />
Homes</label>
  &nbsp;&nbsp;
  <label>
<input name="Type[]" type="checkbox" id="Type[]" value="Townhouse">
Townhouse</label>
  &nbsp;&nbsp;
  <label>
  <input name="Type[]" type="checkbox" id="Type[]" value="Other" />
Other</label>
  <br>
  <br />
  Target date for lease start: 
<input name="LeaseStart" type="text" id="LeaseStart" value="<?php echo h($LeaseStart);?>" size="14">
<br>
Price range:
<input name="PriceRange" type="text" id="PriceRange" value="<?php echo h($PriceRange);?>" />
<em>(whole dollars)</em>
<input name="mode" type="hidden" id="mode" value="rentalSearch">
</p>
<div class="fr" style="width:40%;">
  <h2 class="nullBottom">Required Amenities and features:</h2>
  <table border="0" cellspacing="0" cellpadding="4">
  <tr>
	<td><label>
	<input name="PetsAllowed" type="checkbox" id="PetsAllowed" value="1" />
Pets allowed</label>
	  <br />
	  <label>
	  <input name="InternetPaid" type="checkbox" id="InternetPaid" value="1" />
Internet paid</label>
	  <br />
	  <label>
	  <input name="FitnessCenter" type="checkbox" id="FitnessCenter" value="1" />
Fitness Center</label>
	  <br />
	  <label>
	  <input name="Pool" type="checkbox" id="Pool" value="1" />
Pool</label>
	  <br />
	  <label>
	  <input name="HotTub" type="checkbox" id="HotTub" value="1" />
Hot Tub</label>
	  <br />
	  <label>
	  <input name="Basketball" type="checkbox" id="Basketball" value="1" />
Basketball</label>
	  <br />
	  <label>
	  <input name="IndividualLeaseOption" type="checkbox" id="IndividualLeaseOption" value="1" />
Individual lease option</label></td>
	<td><input name="Furnished" type="checkbox" id="Furnished" value="1" />
	  Furnished<br />
		<input name="WasherDryer" type="checkbox" id="WasherDryer" value="1" />
	  Washer/Dryer<br />
	  <input name="Microwave" type="checkbox" id="Microwave" value="1" />
	  Microwave<br />
	  <input name="Dishwasher" type="checkbox" id="Dishwasher" value="1" />
	  Dishwasher<br />
	  <input name="IceMaker" type="checkbox" id="IceMaker" value="1" />
	  Ice maker<br />
	  <input name="WalkInClosets" type="checkbox" id="WalkInClosets" value="1" />
	  Walk-in closets<br /></td>
  </tr>
</table>
<em>(NOTE: checking too many of these options may severely limit your search results)</em> </div>
<p><br />
Show sub-leases:
<input name="ShowSubleases" type="radio" value="0" <?php echo $ShowSubleases==='0'?'checked':''?> />
No
&nbsp;&nbsp;
<input name="ShowSubleases" type="radio" value="1" <?php echo $ShowSubleases==1 || !isset($ShowSubleases) ? 'checked':''?> />
Yes
<br />
<br />
Number bedrooms:
<input name="Bedrooms" type="text" id="Bedrooms" value="<?php echo h($Bedrooms);?>" size="15" />
<em>(example 1, 2, 3, 2+, 3-, 2-3)</em><br />
Number bathrooms: 
<input name="Bathrooms" type="text" id="Bathrooms" value="<?php echo h($Bathrooms);?>" size="15" />
</p>
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
ID, Type, PropertyName, PropertyCity
FROM _v_properties_master_list WHERE Type IN('Apt','Multi') GROUP BY Properties_ID ORDER BY PropertyCity, PropertyName", O_ARRAY)){
	$i=0;
	foreach($a as $n=>$v){
		$i++;
		if($i==1 || strtolower($buffer)!==strtolower($v['PropertyCity'])){
			$buffer=$v['PropertyCity'];
			if($i>1)echo '</optgroup>';
			?>
            <optgroup label="<?php echo $buffer?>">
              <?php
		}
		?>
              <option value="<?php echo $v['ID']?>" <?php if(in_array($v['ID'],$Properties_ID))echo 'selected';?>><?php echo h($v['PropertyName']);?></option>
              <?php
	}
	?>
            </optgroup>
            <?php
}
?>
          </select>
    </p></td>
    <td><p>Specific Cities:<br />
	<select name="PropertyCity[]" size="10" multiple="multiple" id="PropertyCity[]">
	<option value="" style="font-style:italic;">(no preference)</option>
	<?php
	$Cities=q("SELECT DISTINCT PropertyCity FROM _v_properties_master_list GROUP BY PropertyCity ORDER BY IF(PropertyCity='San Marcos',1,2)", O_COL);
	foreach($Cities as $v){
		?><option value="<?php echo h($v);?>" <?php if(@in_array($v, $PropertyCity) || (empty($PropertyCity) && strtolower($v)=='san marcos'))echo 'selected';?>><?php echo h($v);?></option><?php
	}
	?>
	</select>
	</p></td>
  </tr>
</table>
<input type="submit" name="Submit" value="Begin Search" />
<p>&nbsp;</p>
