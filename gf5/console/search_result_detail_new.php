<?php 
/*
todo:
logo at top on print
Great Locations
	address
	phone
	[if]agent phone number
find this text: Income and student restricted - and implement
main picture
floor plan
thumbnails
basically, IMAGES with a foundation for future usage


*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

$pageEndFunction='bufferForLink';
ob_start();

$imgDisposition='250x250';

$filterSelected=$Properties_ID;
//--------------------------- code block 009019 ---------------------------
if($data=q("SELECT * FROM gl_searches WHERE ID=$Searches_ID", O_ROW)){
	$data['SearchCriteria']=unserialize(base64_decode($data['SearchCriteria']));
	extract($data['SearchCriteria']);
}
if(!empty($PropertyCity)){
	foreach($PropertyCity as $n=>$v){
		if(!trim($v))unset($PropertyCity[$n]);
	}
	if(empty($PropertyCity))$passedPropertyCity=$PropertyCity;
}
$sql=str_replace('[fieldlist]','m.*',$sql);
if($filterSelected)$sql=str_replace(
	'GROUP BY m.ID',
	"AND m.ID IN(".rtrim($filterSelected,',').")\nGROUP BY m.ID",
	$sql
);
$sql=str_replace('ORDER BY m.Rent','ORDER BY PropertyName',$sql);
$a=q($sql, O_ARRAY_ASSOC);
if($passedPropertyCity){
	foreach($a as $v){
		$cities[$v['PropertyCity']]=true;
	}
}
if($a){
	foreach($a as $n=>$v){
		$PropertyNames[$v['PropertyName']]++;
	}
}
//--------------------------- code block 009019 ---------------------------

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Property Search Detail - '.$AcctCompanyName?></title>



<link id="cssUndoHTML" rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="../../site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="../../Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
/* local CSS styles */
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="../../site-local/local.js"></script>
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

function slideshow(o){
	var a=o.id.split('-');
	g('p'+a[0]).src='/images/reader.php?Tree_ID='+a[1]+'&Key='+a[2]+'&disposition=<?php echo $imgDisposition?>';
	return false;
}
function email(){
	g('emailStat').style.visibility='visible';
}
</script>


</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onsubmit="return beginSubmit();">
<?php }?>
<div id="header">

</div>
<div id="mainBody">



<?php
if(!$refreshComponentOnly){
	?><style type="text/css">
	.propertyDetail{
		margin:15px 0px 0px 0px;
		font-weight:400;
		}
	.page-break{
		page-break-after:always;
		}
	.propTitle{
		border-bottom:1px solid #999;
		margin-bottom:15px;
		padding-top:0px;
		padding-bottom:5px;
		font-family:Arial, Helvetica, sans-serif;
		font-size:139%;
		font-weight:400;
		color:#444;
		padding:20px 0px 15px 0px;
		
		}
	.fade1{
		background-color:whitesmoke;
		}
	.detailInset{
		padding:5px;
		border:1px solid #000;
		margin:0px 15px 10px 0px;
		}

	div#compare{
		margin-top:45px;
		}
	.compare table{
		border-collapse:collapse;
		}
	.compare th{
		text-align:left;
		padding:3px 4px 1px 5px;
		color:midnightblue;
		}
	.compare td{
		padding:3px 4px 1px 5px;
		}
	.compare .highlight1{
		background-color:whitesmoke;
		}
	.compare td{
		border:1px solid #ccc;
		}
	.compare .sectionStart{
		border-top:2px solid #000;
		}
	@media screen{
		.fls{
			float:left;
			}
	}
	input[type=submit], input[type=button]{
		padding:0px;
		}
	.spacer td{
		border-bottom:1px dotted #ccc;
		}
	.agent{
		padding:3px 5px;
		border: 1px solid #ccc;
		margin-right:1px;
		}
	#toolbar{
		background-color:aliceblue;
		padding:2px 5px;
		margin-bottom:15px;
		}
	#email{
		position:relative;
		float:right;
		width:75px;
		}
	#emailStat{
		opacity:.9;
		position:absolute;
		width:300px;
		height:200px;
		padding:20px;
		border:1px solid darkred;
		top:31px;
		right:0px;
		background-color:oldlace;
		
		}
	</style>
	<script language="javascript" type="text/javascript">
	</script><?php
}
//-------- don't show this bar for the output link ------------
ob_start();
?>
<div id="toolbar" class="printhide tar">
	<input type="button" name="Button" value="Print" onclick="window.print();" />
	&nbsp;
	<div id="email">
	<input type="button" name="Button" value="Email" onclick="email();" />
	<div id="emailStat" style="text-align:left; visibility:<?php echo 'hidden';?>;">
	Email to (name): 
	  <input name="FullName" type="text" id="FullName" size="25" />
	  <br />
	Email address: 
	<input name="Email" type="text" id="Email" />
	<br />
	Description (optional): <br />
	<textarea name="Description" cols="35" rows="4" id="Description"></textarea>
	<br />
	<input type="submit" name="Submit" value="Send" />
	&nbsp;
	<input name="Button" type="button" id="Submit" value="Cancel" onclick="g('emailStat').style.visibility='hidden';" />
	<input name="mode" type="hidden" id="mode" value="sendSearchResults" />
	<input name="count" type="hidden" id="count" value="<?php echo count($a);?>" />
	<input name="Searches_ID" type="hidden" id="Searches_ID" value="<?php echo $Searches_ID;?>" />
	</div>
	</div>
	�
	<input type="button" name="Button" value="Close" onclick="window.close();" />
</div>
<?php
$toolbar=ob_get_contents();
ob_end_clean();
echo $toolbar;
//---------------------------------------------------------------


if($FirstName || $Email || $HomeMobile){
	?><div class="fr agent">
		<?php 
		$staff=q("SELECT
		u.*, s.*
		FROM bais_universal u, bais_staff s WHERE u.un_username=s.st_unusername AND un_username='".sun()."'", O_ROW);
		$office=q("SELECT * FROM bais_orgaliases a, bais_offices b WHERE a.oa_unusername=of_oausername LIMIT 1", O_ROW);
		
		$Tree_ID=q("SELECT c.Tree_ID FROM
		gf_objects a, gf_objects b, gl_ObjectsTree c
		WHERE 
		a.ID=b.Objects_ID AND b.ID=c.Objects_ID AND
		a.ParentObject='bais_staff' AND
		a.Objects_ID='".sun()."' AND
		a.Relationship='Photo Gallery' AND
		b.Relationship='Profile Picture Default'", O_VALUE);
		?>
		<?php if($Tree_ID && $profilePicturePath=tree_id_to_path($Tree_ID)){ ?>
		<div class="fl" style="padding:0px;">
		<?php
		$Key=end(explode('/',$profilePicturePath));
		preg_match('/^[a-f0-9]+_/i',$Key,$_a_);
		$Key=$_a_[0];
		$picture=1;
		$g=getimagesize($_SERVER['DOCUMENT_ROOT'].$profilePicturePath);
		$src='/images/reader.php?Tree_ID='.$Tree_ID.'&Key='.$Key.'&disposition=200x250&boxMethod=2';
		?>
		<img src="<?php echo $src;?>" alt="Picture of <?php echo $_SESSION['admin']['firstName']. ' ' . $_SESSION['admin']['firstName'];?>" />
		</div>
		<?php } ?>
		<div style="float:left;">
		<?php
		$logo=$_SERVER['DOCUMENT_ROOT'].'/images/logos/'.strtolower($GCUserName).'.gif';
		if(file_exists($logo) && $gis=getimagesize($logo)){
			?><img id="logoImg" src="/images/logos/<?php echo strtolower($GCUserName).'.gif'?>" width="<?php echo $gis[0]?>" height="<?php echo $gis[1]?>" align="logo" class="printhide" />
			<br /><?php
		}
		?><br />
		<h3 class="nullTop nullBottom"><em><?php echo $_SESSION['admin']['firstName']  . ' ' . $_SESSION['admin']['lastName'];?></em></h3>
		<h4><?php echo $AcctCompanyName;?></h4>
		
		<table class="spacer">
		  <tr>
			<td>Office</td>
			<td><?php echo $office['WorkPhone'];?></td>
		  </tr>
		  <tr>
			<td>Cell</td>
			<td><?php echo $staff['Cell'];?></td>
		  </tr>
		  <?php if($office['Fax']){ ?>
		  <tr>
			<td>Fax</td>
			<td><?php echo $office['Fax'];?></td>
		  </tr>
		  <?php } ?>
		  <tr>
			<td colspan="100%">Email:<br />
			<a href="mailto:<?php echo $staff['un_email'];?>"><?php echo $staff['un_email'];?></a></td>
		  </tr>
		</table>
		</div>
		<div class="cb"> </div>
	</div><?php
}
?>

<div class="prospect fl">
	<?php
	if($FirstName || $Email || $HomeMobile){
		?>
		<h2 class="nullTop">Search for: <?php echo $FirstName . ' '. $LastName;?></h2>
		<p>
		Phone: <?php echo $HomeMobile;?><br />
		Email: <a href="mailto:<?php echo $Email;?>"><?php echo $Email;?></a>
		</p>
		<?php
	}
	?>
	You searched for:<br />
	Type(s): <?php echo implode(', ',$Type);?><br />
	<?php 
	$bb[]='Bedrooms: '.($BedroomText ? $BedroomText : '<em class="gray">(any)</em>');
	$bb[]='Bathrooms: '.($BathroomText ? $BathroomText : '<em class="gray">(any)</em>');
	echo implode(', ',$bb);
	?>
	<br />
	Price range: <?php echo $RentText ? $RentText : '<em class="gray">(any)</em>';?>
	<?php
	if($n=$CitiesText){
		?><br />Cities: <?php echo $n;?><?php
	}
	if($n=$PropertiesText){
		?><br />Properties: <?php echo $n;?><?php
	}
	?>
	<?php if(count($Amenities)){ ?>
	<br />
	Amenities: <?php echo implode(', ', array_keys($Amenities));?>
	<?php } ?>
</div>
<div class="cb"> </div>



<h3><?php echo count($a) . ' listing'.(count($a)>1?'s':'').' selected';?></h3>




<?php
$present=count($a);
$i=0;
foreach($a as $n=>$v){
	$i++;
	extract($v);
	extract(q("SELECT 
	Microwave, Dishwasher, IceMaker, WalkinClosets, SquareFeet, Quantity, WasherDryer, Fireplace, Storage, VaultedCeilings, PrivateBalcony, Furnished, Additional, Specials, DepositDescription
	FROM gl_properties_units WHERE ID=$ID", O_ROW));
	
	
	?><div class="propertyDetail<?php if(fmod($i,2) || $i==count($a) /* && $i!==count($a) */)echo ' page-break';?>">
    <h1 class="propTitle"><?php echo $PropertyName;?> <?php if($Bedrooms && $Bathrooms && $PropertyNames[$PropertyName]>1)echo '('.$Bedrooms . '/'.$Bathrooms.')';?></h1>
	<div class="screenhide" style="font-size:12px;color:#666;">
	Please remember to write-in <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName'].' at '.$AcctCompanyName;?></strong> on all guest cards and applications.<br />
	<?php
	if($data['Contacts_ID']){
		$code=$Searches_ID.'-'.str_pad($ID,3,'0',STR_PAD_LEFT);
		?>
		Did you rent this property? Get a free $5.00 coupon by going to <u>survey.mygreatlocations.com</u> and entering code <strong class="red"><?php echo $code;?></strong>
		<br />
		<br />
		<?php
	}
	?>
	
	</div>
	<style type="text/css">
	.details{
		border-collapse:collapse;
		}
	.details td{
		margin:1px 2px;
		padding:1px 5px;
		border:1px dotted #ddd;
		}
	img.detailThumb{
		border:1px solid #aaa;
		}
	.detailThumb{
		padding:3px;
		margin:0px 7px 0px 0px;
		}
	</style>
	<table class="details" width="100%" cellspacing="2">
	  <tr>
		<td valign="top" rowspan="7" style="padding:0px 10px 10px 0px;border-top:none; border-left:none; border-bottom:none;">
		&nbsp;&nbsp;&nbsp;<br />
		<?php
		//we want the main property image
		if($img=q("SELECT
		ot.Tree_ID, t.Name
		FROM 
		gl_properties_units u, gl_ObjectsTree ot, relatebase_tree t
		WHERE
		u.ID=$ID AND
		u.Properties_ID=ot.Objects_ID AND 
		ot.ObjectName='gl_properties' AND
		ot.Tree_ID=t.ID
		ORDER BY IF(ot.Category='Main Image', 1,2)", O_ARRAY)){
			?><a href="/images/reader.php" onclick="return ow((this.firstChild.src+'').replace('&disposition=<?php echo $imgDisposition?>',''),'l1_img','900,900');" title="view full-sized image"><img id="p<?php echo $ID;?>" class="detailInset" src="/images/reader.php?Tree_ID=<?php echo $img[1]['Tree_ID']?>&Key=<?php echo substr($img[1]['Name'],0,5);?>&disposition=<?php echo $imgDisposition?>" alt="Property" /></a><?php
		}
		
		?>
		</td>
		<td rowspan="3">Address:</td>
		<td rowspan="3" class="fade1" nowrap="nowrap"><?php echo $PropertyAddress;?><br />
		<?php echo $PropertyCity;?>, <?php echo $PropertyState;?><br />
		<?php echo $PropertyZip;?></td>
		<td><?php if($YearBuild!='0000' && $YearBuild>0){ ?>Year Built:<?php }else{ ?>&nbsp;<?php } ?></td>
		<td><?php if($YearBuild!='0000' && $YearBuild>0){ ?><?php echo $YearBuilt;?><?php }else{ ?>&nbsp;<?php } ?></td>
		<td>Bed/Bath:</td>
		<td valign="bottom" class="tar"><strong><?php echo preg_replace('/\.0+/','',$Bedrooms).'/'.preg_replace('/\.0+/','',$Bathrooms);?></strong></td>
	  </tr>
	  <tr>
		<td><?php if($Quantity>1){ ?>Nbr. of Units:<?php }else{ ?>&nbsp;<?php } ?></td>
		<td><?php if($Quantity>1){ ?><?php echo $Quantity;?><?php }else{ ?>&nbsp;<?php } ?></td>
		<td>Sq.Ft:</td>
		<td valign="bottom" class="tar"><?php echo $SquareFeet;?></td>
	  </tr>
	  <tr>
		<td>Pets Allowed:</td>
		<td valign="bottom"><strong><?php echo $PetsAllowed?'Yes':'No';?></strong></td>
		<td>Rent:</td>
		<td valign="bottom" class="tar"><?php echo '$'.number_format($Rent,2);?></td>
	  </tr>
	  <tr>
		<td>Phone:</td>
		<td class="fade1"><?php echo $Phone;?></td>
		<td>Pet Deposit:</td>
		<td valign="bottom"><?php if($PetDeposit)echo '$'.number_format($PetDeposit,2);?></td>
		<td>Deposit:</td>
		<td valign="bottom" class="tar"><?php echo '$'.number_format($Deposit,2);?></td>
	  </tr>
	  <tr>
		<td>Fax:</td>
		<td class="fade1"><?php echo $Fax;?></td>
		<td>Pet Restrictions:</td>
		<td valign="bottom"><?php echo $PetRestrictions;?></td>
		<td>App Fee:</td>
		<td valign="bottom" class="tar"><?php if($ApplicationFee)echo '$'.number_format($ApplicationFee,2);?></td>
	  </tr>
	  <tr>
		<td rowspan="2">Office<br />
		  Hours:</td>
		<td class="fade1" rowspan="2" width="140"><?php echo $OfficeHours;?></td>
		<td>Pet Weight Limit:</td>
		<td width="125" valign="bottom"><?php echo $PetWeightLimit;?></td>
		<td>Co-Sign Fee:</td>
		<td valign="bottom" class="tar"><?php if($CosignerFee)echo '$'.number_format($CosignerFee,2);?></td>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
		<td valign="bottom">&nbsp;</td>
		<td>Admin Fee:</td>
		<td class="tar"><?php echo '$'.number_format($AdminFee,2);?></td>
	  </tr>
	  <?php if($PetPolicies){ ?>
	  <tr>
		<td colspan="100%">Pet Polices: <?php echo $PetPolicies; ?></td>
	  </tr>
	  <?php } ?>
	  <?php if($DepositDescription){ ?>
	  <tr>
		<td colspan="100%">Deposit Description: <?php echo $DepositDescription;?></td>
		</tr>
	  <?php } ?>
	  <?php if(count($img)>1){ ?>
	  <tr>
		<td colspan="100%" style="padding-top:15px; border-left:none; border-right:none; border-bottom:none;">
		<?php 
		$j=0;
		foreach($img as $n=>$v){
			$j++;
			?>
			<a href="#" onclick="return slideshow(this.firstChild)" title="Click to make this the main picture"><img id="<?php echo $ID.'-'.$v['Tree_ID'].'-'.substr($v['Name'],0,5);?>" class="detailThumb" src="/images/reader.php?Tree_ID=<?php echo $v['Tree_ID']?>&Key=<?php echo substr($v['Name'],0,5);?>&disposition=75x75&boxMethod=2" alt="Property" /></a>
			<?php
			if($j==8)break;
		}
		?>
		</td>
	  </tr>
	  <?php } ?>
	</table>
	
	<?php if(false){ ?>
	<h3>Amenities</h3>
	<?php
	if(!$haveFps){
		$haveFps=true;
		$fps=q("SELECT
		ot.Objects_ID, ot.Tree_ID, t.Name
		FROM gl_ObjectsTree ot, relatebase_tree t
		WHERE ot.Objects_ID IN(".implode(',',array_keys($a)).") AND ObjectName='gl_properties_units' AND ot.Tree_ID=t.ID", O_ARRAY_ASSOC);
		//prn($fps,1);
	}
	if($f=$fps[$n]){
		?><div class="fr">
		<img src="/images/reader.php?Key=<?php echo substr($f['Name'],0,6);?>&Tree_ID=<?php echo $f['Tree_ID'];?>&disposition=<?php echo $imgDisposition?>" />
		</div><?php
	}
	?>
	<table border="0">
	  <tr>
		<td width="120">Individual Lease Option:</td>
		<td width="45"><strong><?php echo $IndividualLeaseOption?'Yes':'No'?></strong></td>
		<td width="65">Electric Paid:</td>
		<td width="45"><strong><?php echo $ElectricPaid?'Yes':'No'?></strong></td>
		<td width="81">Hot Tub:</td>
		<td width="45"><strong><?php echo $HotTub?'Yes':'No'?></strong></td>
		<td width="55">Ice Maker:</td>
		<td width="45"><strong><?php echo $IceMaker ? 'Yes':'No';?></strong></td>
	  </tr>
	  <tr>
		<td width="90">Income Restricted:</td>
		<td width="45">&nbsp;</td>
		<td>Fitness Center: </B></td>
		<td><strong><?php echo $FitnessCenter?'Yes':'No'?></strong></td>
		<td>Onsite Laundry: </td>
		<td><strong><?php echo $Laundry?'Yes':'No'?></strong></td>
		<td>Walk In Closets: </td>
		<td><strong><?php echo $WalkInClosets ? $WalkInClosets:'';?></strong></td>
	  </tr>
	  <tr>
		<td>Internet Paid</td>
		<td><strong><?php echo $InternetPaid?'Yes':'No'?></strong></td>
		<td>Business Center: </td>
		<td><strong><?php echo $BusinessCenter?'Yes':'No'?></strong></td>
		<td>Security Gates: </td>
		<td><strong><?php echo $SecurityGates?'Yes':'No'?></strong></td>
		<td>Fire Place: </td>
		<td><strong><?php echo $FirePlace?'Yes':'No'?></strong></td>
	  </tr>
	  <tr>
		<td>Cable Paid:</td>
		<td><strong><?php echo $Cable?'Yes':'No'?></strong></td>
		<td>Game Room: </td>
		<td><strong><?php echo $GameRoom?'Yes':'No'?></strong></td>
		<td>Microwave: </td>
		<td><strong><?php echo $Microwave ? 'Yes':'No';?></strong></td>
		<td>Private Balcony/Patio: </td>
		<td><strong><?php echo $PrivateBalcony ? 'Yes':'No';?></strong></td>
	  </tr>
	  <tr>
		<td>Cable Provider: </td>
		<td><?php echo $CableProvider;?></td>
		<td>Basketball: </td>
		<td><strong><?php echo $Basketball?'Yes':'No'?></strong></td>
		<td>Dish Washer: </td>
		<td><strong><?php echo $DishWasher ? 'Yes':'No';?></strong></td>
		<td>Vaulted Ceilings: </td>
		<td><strong><?php echo $VaultedCeilings ? 'Yes':'No';?></strong></td>
	  </tr>
	  <tr>
		<td>Water Paid: </td>
		<td><?php echo $WaterPaid;?></td>
		<td>Volleyball:</td>
		<td><strong><?php echo $Volleyball?'Yes':'No'?></strong></td>
		<td>Gas:</td>
		<td><strong><?php echo $Gas?'Yes':'No'?></strong></td>
		<td>Washer Dryer: </td>
		<td><?php echo $WasherDryer;?></td>
	  </tr>
	  <tr>
		<td>Trash Paid: </td>
		<td><?php echo $TrashPaid;?></td>
		<td>Pool: </B></td>
		<td><strong><?php echo $Pool?'Yes':'No'?></strong></td>
		<td>Parking: </td>
		<td><?php echo $Parking;?></td>
		<td>Storage:</td>
		<td><strong><?php echo $Storage ? 'Yes':'No';?></strong></td>
	  </tr>
	  <tr>
		<td>Gas Paid: </td>
		<td><?php echo $GasPaid;?></td>
		<td>Furnished:</td>
		<td><strong><?php echo $Furnished ? 'Yes':'No';?></strong></td>
		<td>Section 8</td>
		<td><strong><?php echo $Section8?'Yes':'No'?></strong></td>
		<td></td>
		<td></td>
	  </tr>
	</table>
	
	<?php if($Specials)echo '<br /><br />Specials: <strong>'.$Specials.'</strong>'; ?>
	
	<?php } ?>
	<div style="clear:both;height:1px;font-size:1px;"> </div>
	</div>
	<?php
}

?>
<div id="compare">
<?php
$logo=$_SERVER['DOCUMENT_ROOT'].'/images/logos/'.strtolower($GCUserName).'.gif';
if(file_exists($logo) && $gis=getimagesize($logo)){
	?><img id="logoImg" src="/images/logos/<?php echo strtolower($GCUserName).'.gif'?>" width="<?php echo $gis[0]?>" height="<?php echo $gis[1]?>" align="logo" class="printhide" />
	<br /><?php
}
?>
  <h2>Price and Features Comparison</h2>
  <table class="compare" width="75%">
  <tr class="top">
    <th scope="col">&nbsp;</th>
	<?php
	foreach($a as $n=>$v){
		extract($v);
		?><th scope="col"><?php echo $PropertyNameShort;?></th><?php
	}
	?>
  </tr>
  <tr>
    <th scope="row">Beds/Baths</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $Bedrooms . '/'. $Bathrooms;?></td>
	<?php
	}
	?>
  </tr>
  <tr>
    <th scope="row">Sq. Ft. </th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $SquareFeet;?></td>
	<?php
	}
	?>
  </tr>
  <tr class="sectionStart">
    <th scope="row">Rent</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr"><?php echo $Rent ? '$'.number_format($Rent,2) : '&nbsp;';?></td>
	<?php
	}
	?>
	</tr>
  <tr>
    <th scope="row">Deposit</th>
    <?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr"><?php echo $Deposit ? '$'.number_format($Deposit,2) : '&nbsp;';?></td>
    <?php
	}
	?>  
	</tr>
  <tr>
    <th scope="row">App Fee </th>
    <?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr"><?php echo $ApplicationFee ? '$'.number_format($ApplicationFee,2) : '&nbsp;';?></td>
    <?php
	}
	?>    
	</tr>
  <tr>
    <th scope="row">Co-sign Fee </th>
    <?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr"><?php echo $CosignerFee ? '$'.number_format($CosignerFee,2) : '&nbsp;';?></td>
    <?php
	}
	?>    
    </tr>
  <tr>
    <th scope="row">Electric Paid </th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $ElectricPaid ? 'Yes' : 'No';?></td>
	<?php
	}
	?>
  </tr>
  <tr>
    <th scope="row">Gas Paid </th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $Gas ? ($GasPaid ? 'Yes' : 'No') : 'N/A';?></td>
	<?php
	}
	?>
  </tr>
    <tr>
      <th scope="row">Water Paid </th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $WaterPaid;?></td>
	<?php
	}
	?>
  </tr>
  <tr>
    <th scope="row">Trash Paid </th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $TrashPaid ? 'Yes' : 'No';?></td>
	<?php
	}
	?>
  </tr>
  <tr class="sectionStart">
    <th scope="row">Cable</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $Cable ? ($CablePaid ? 'Yes' : 'No') : 'N/A';?></td>
	<?php
	}
	?>
  </tr>
  <tr>
    <th scope="row">Internet</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $Internet ? ($InternetPaid ? 'Yes' : 'No') : 'N/A';?></td>
	<?php
	}
	?>
  </tr>
  <tr>
    <th scope="row">Phone</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $Phone ? ($PhonePaid ? 'Yes' : 'No') : 'N/A';?></td>
	<?php
	}
	?>
  </tr>
  <tr class="highlight1">
    <th class="tr" scope="row"> Projected Monthly Cost: </th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr">&nbsp;</td>
	<?php
	}
	?>
  </tr>
  <tr class="highlight1">
    <th class="tr" scope="row"> Projected Up-Front Cost: </th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr">&nbsp;</td>
	<?php
	}
	?>
  </tr>
  <tr class="highlight1">
    <th class="tr" scope="row"> Projected Refund: </th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr">&nbsp;</td>
	<?php
	}
	?>
  </tr>
  <?php if(false){ ?>
  <tr class="sectionStart">
    <th scope="row">&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php } ?>
</table>

</div>

</div>
<div id="footer">
&nbsp;&nbsp;
</div>
<?php if(!$suppressForm){ ?>
</form>
<?php }?>
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
</html><?php
q("UPDATE gl_searches SET Output='".addslashes(str_replace($toolbar,'',ob_get_contents()))."' WHERE ID=$Searches_ID");
 page_end();?>