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
		margin:35px 0px 0px 0px;
		font-weight:400;
		}
	.page-break{
		page-break-after:always;
		}
	.propTitle{
		margin-bottom:0px;
		padding-top:0px;
		padding-bottom:5px;
		border-bottom:1px dotted #666;
		font-family:Arial, Helvetica, sans-serif;
		font-size:139%;
		font-weight:400;
		color:#222;		
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
		border-top:2px solid #444;
		}
	.linkhead a{
		color:darkblue;
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
			?><img id="logoImg" src="/images/logos/<?php echo strtolower($GCUserName).'.gif'?>" width="<?php echo $gis[0]?>" height="<?php echo $gis[1]?>" alt="logo" />
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
	$FirstName=trim(preg_replace('/first name/i','',$FirstName));
	$LastName=trim(preg_replace('/last name/i','',$LastName));
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
	<?php 
	if($Notes){
		?><br />
		<span>Notes: <span class="gray"><?php echo stripslashes($Notes);?></span></span><?php
	}
	?>
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
    <h1 class="propTitle"><?php echo $PropertyName;?>
	<div style="float:right;">
	<?php echo $Bedrooms.'/'.$Bathrooms;?> - $<?php echo number_format($Rent,2)?>/month
	</div></h1>
	<div class="screenhide" style="font-size:12px;color:#666;">
	Please remember to write-in <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName'].' at '.$AcctCompanyName;?></strong> on all guest cards and applications.<br />
	<?php
	if($data['Contacts_ID'] && $apSettings['implementSearchCoupon']){
		$code=$Searches_ID.'-'.str_pad($ID,3,'0',STR_PAD_LEFT);
		?>
		Did you rent this property? Get a free $5.00 coupon by going to <u><?php echo $apSettings['searchCouponURL'];?></u> and entering code <strong class="red"><?php echo $code;?></strong>
		<br />
		<br />
		<?php
	}
	?>
	
	</div>
	<style type="text/css">
	.details{
		border-collapse:collapse;
		margin-top:5px;
		}
	.details td{
		margin:1px 2px;
		padding:1px 5px;
		border:1px dotted #ddd;
		}
	img.detailThumb{
		border:1px solid #aaa;
		opacity:.7;
		}
	img.detailThumb:hover{
		opacity:1.0;
		}
	.detailThumb{
		padding:2px;
		margin:0px 7px 0px 0px;
		}
	#mainImage, #mainData{
		float:left;
		}
	</style>
	<?php
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
		foreach($img as $n=>$v){
			
		}
	}
	
	?>
	<div id="mainImage">
	<?php
	if($img[1]){
		//we want the main property image
		?><span class="printhide"><a href="/images/reader.php" onclick="return ow((this.firstChild.src+'').replace('&disposition=<?php echo $imgDisposition?>',''),'l1_img','900,900');" title="view full-sized image"><img id="p<?php echo $ID;?>" class="detailInset" src="/images/reader.php?Tree_ID=<?php echo $img[1]['Tree_ID']?>&Key=<?php echo substr($img[1]['Name'],0,5);?>&disposition=<?php echo $imgDisposition?>" alt="Property" /></a></span>
		<span class="screenhide">
		<img id="p<?php echo $ID;?>_2" class="detailInset" src="/images/reader.php?Tree_ID=<?php echo $img[1]['Tree_ID']?>&Key=<?php echo substr($img[1]['Name'],0,5);?>&disposition=175x175" alt="Property" />
		</span><?php
	}
	?>
	</div>
	<div id="mainData">

<?php 
unset($params);
if($ElectricPaid)$params[]=array('Electric pd.:','Yes');
if($Gas)$params[]=array('Gas pd.:', ($GasPaid ? $GasPaid : 'Partial'));
if($WaterPaid>0)$params[]=array('Water pd.:',$WaterPaid==1?'Yes':'Half');
if($TrashPaid)$params[]=array('Trash pd.:', 'Yes');
$param=-1;
?>
		
		<table class="details" cellspacing="2">
		  <tr>
			<td colspan="2" rowspan="3"><?php echo $PropertyAddress;?><br />
			  <?php echo $PropertyCity;?>, <?php echo $PropertyState;?><br />
			  <?php echo $PropertyZip;?></td>
			<td>Bed/Bath:</td>
			<td valign="bottom" class="tar"><strong><?php echo preg_replace('/\.0+/','',$Bedrooms).'/'.preg_replace('/\.0+/','',$Bathrooms);?></strong></td>
		    <td valign="bottom" class="tar"><?php
			$param++;
			echo $params[$param] ? $params[$param][0] : '&nbsp;';
			?></td>
		    <td valign="bottom" class="tar"><?php
			echo $params[$param] ? $params[$param][1] : '&nbsp;';
			?></td>
		  </tr>
		  <tr>
			<td>Sq.Ft:</td>
			<td valign="bottom" class="tar"><?php echo $SquareFeet;?></td>
		    <td valign="bottom" class="tar"><?php
			$param++;
			echo $params[$param] ? $params[$param][0] : '&nbsp;';
			?></td>
		    <td valign="bottom" class="tar"><?php
			echo $params[$param] ? $params[$param][1] : '&nbsp;';
			?></td>
		  </tr>
		  <tr>
			<td>Rent:</td>
			<td valign="bottom" class="tar"><?php echo '$'.number_format($Rent,2);?></td>
		    <td valign="bottom" class="tar"><?php
			$param++;
			echo $params[$param] ? $params[$param][0] : '&nbsp;';
			?></td>
		    <td valign="bottom" class="tar"><?php
			echo $params[$param] ? $params[$param][1] : '&nbsp;';
			?></td>
		  </tr>
		  <tr>
			<td>Phone:</td>
			<td class="fade1"><?php echo $Phone;?></td>
			<td>Deposit:</td>
			<td valign="bottom" class="tar"><?php echo '$'.number_format($Deposit,2);?></td>
		    <td valign="bottom" class="tar"><?php
			$param++;
			echo $params[$param] ? $params[$param][0] : '&nbsp;';
			?></td>
		    <td valign="bottom" class="tar"><?php
			echo $params[$param] ? $params[$param][1] : '&nbsp;';
			?></td>
		  </tr>
		  <tr>
			<td>Fax:</td>
			<td class="fade1"><?php echo $Fax;?></td>
			<td>App Fee:</td>
			<td valign="bottom" class="tar"><?php if($ApplicationFee)echo '$'.number_format($ApplicationFee,2);?></td>
		    <td valign="bottom" class="tar">W/D: </td>
		    <td valign="bottom" class="tar"><?php echo $WasherDryer==2 ? 'Provided' : ($WasherDryer==1 ? 'connection' : 'No');?></td>
		  </tr>
		  <tr>
			<td rowspan="2">Office<br />
			  Hours:</td>
			<td class="fade1" rowspan="2" width="140"><?php echo $OfficeHours;?></td>
			<td>Co-Sign Fee:</td>
			<td valign="bottom" class="tar"><?php if($CosignerFee)echo '$'.number_format($CosignerFee,2);?></td>
		    <td valign="bottom" class="tar">Cable <strong><?php echo $Cable?'Yes':'No';?></strong></td>
		    <td valign="bottom" class="tar"><?php echo $CableProvider;?></td>
		  </tr>
		  <tr>
			<td>Admin Fee:</td>
			<td class="tar"><?php echo '$'.number_format($AdminFee,2);?></td>
		    <td class="tar">Internet <strong><?php echo $InternetPaid?'Yes':'No';?></strong></td>
		    <td class="tar"><?php echo $InternetProvider;?></td>
		  </tr>
		  <tr>
		    <td colspan="100%" style="padding-top:20px;">
			<div style="width:500px;">
			<?php if($PetsAllowed){ ?>
			<!-- pet icon here -->
			<?php if($PetPolicies){ ?>
			Pet Polices: <?php echo $PetPolicies; ?>
			<?php } ?>
			<?php
			if($PetDeposit){
				?> Pet deposit: $<?php echo number_format($PetDeposit,2);?>; <?php
			}
			if($PetExtra){
				?> Pet extra/month: $<?php echo number_format($PetExtra,2);?>; <?php
			}
			if($PetRestrictions){
				?> Restrictions: <?php echo $PetRestrictions;?>; <?php 
			}
			?>
			<?php } ?>
			<?php 
			if(false){
			if($DepositDescription){ ?>
			Deposit Description: <?php echo $DepositDescription;?>
			<?php } 
			}
			?>			
			<?php
			$amenTrans=array(
				'FitnessCenter'=>'Fitness Ctr.',
				'GameRoom'=>'Game Room',
				'Basketball'=>'Basketball Court',
				'Volleyball'=>'Volleyball',
				'Pool'=>'Pool',
				'HotTub'=>'Hot Tub',
				'Laundry'=>'Laundromat',
				'BusinessCenter'=>'Bus.Ctr.',
			);
			if($FitnessCenter || $GameRoom || $Basketball || $Volleyball || $Pool || $HotTub || $Laundry || $BusinessCenter){
				ob_start();
				?><br />
				Amenities: <span style="color:darkblue;"><?php
				foreach($amenTrans as $n=>$v){
					if($$n)echo $v.', ';
				}
				$out=ob_get_contents();
				ob_end_clean();
				echo rtrim($out,', ');
				?></span><?php				
			}
			?>
			</div>
			</td>
	      </tr>
		</table>
	</div>
	
	<div class="cb"> </div>
	
	
	<span class="printhide">
	<a href="properties3.php?Units_ID=<?php echo $ID;?>" title="Edit/view this property info" onclick="return ow(this.href,'l1_properties','800,700');"><img src="/images/i-local/sm-house.png" height="20" /></a>
	&nbsp;
	<a href="#" title="Edit/view this specific unit info" onclick="return ow('units.php?Units_ID=<?php echo $ID;?>','l1_units','700,700');"><img src="/images/i-local/sm-door.png" height="20" /></a>
	</span>

	<?php if(count($img)>1){ ?>
	<div class="details printhide">
	<?php 
	$j=0;
	foreach($img as $n=>$v){
		$j++;
		?>
		<a href="#" onclick="return slideshow(this.firstChild)" title="Click to make this the main picture"><img id="<?php echo $ID.'-'.$v['Tree_ID'].'-'.substr($v['Name'],0,5);?>" class="detailThumb" src="/images/reader.php?Tree_ID=<?php echo $v['Tree_ID']?>&Key=<?php echo substr($v['Name'],0,5);?>&disposition=60x60&boxMethod=2" alt="Property" /></a>
		<?php
		if($j==8)break;
	}
	?>
	</div>
	<?php } ?>
	
	<?php if(false){ ?>
	<div style="display:none;">
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
	</div>
	<?php } ?>


	<div style="clear:both;height:1px;font-size:1px;"> </div>
	</div>
	<?php
}


?>
<?php if(!$hideCompChart){ ?>
<div id="compare">
<?php

if($s=q("SELECT * FROM bais_settings WHERE UserName='{system:utilities}'", O_ARRAY)){
	foreach($s as $v){
		extract($v);
		$utilities[$vargroup][$varnode][$varkey]=$varvalue;
	}
}
$utilitiesMonthly[APARTMENT][UTILSUMMER]=($utilities['electric'][APARTMENT]['summer'] ? $utilities['electric'][APARTMENT]['summer'] : 65.00);
$utilitiesMonthly[APARTMENT][UTILWINTER]=($utilities['electric'][APARTMENT]['winter'] ? $utilities['electric'][APARTMENT]['winter'] : 75.00);
$utilitiesMonthly[APARTMENT][UTILMID]=($utilities['electric'][APARTMENT]['winter'] ? $utilities['electric'][APARTMENT]['spring'] : 35.00);
$utilitiesMonthly[NONAPARTMENT][UTILSUMMER]=($utilities['electric'][NONAPARTMENT]['summer'] ? $utilities['electric'][NONAPARTMENT]['summer'] : 125.00);
$utilitiesMonthly[NONAPARTMENT][UTILWINTER]=($utilities['electric'][NONAPARTMENT]['winter'] ? $utilities['electric'][NONAPARTMENT]['winter'] : 145.00);
$utilitiesMonthly[NONAPARTMENT][UTILMID]=($utilities['electric'][NONAPARTMENT]['winter'] ? $utilities['electric'][NONAPARTMENT]['spring'] : 85.00);
//misc
$utilitiesMonthly[APARTMENT][TRASH]=($utilities['trash'][APARTMENT]['all'] ? $utilities['trash'][APARTMENT]['all'] : 10.00);
$utilitiesMonthly[APARTMENT][WATER]=($utilities['water'][APARTMENT]['all'] ? $utilities['water'][APARTMENT]['all'] : 15.00);
$utilitiesMonthly[NONAPARTMENT][TRASH]=($utilities['trash'][NONAPARTMENT]['all'] ? $utilities['trash'][NONAPARTMENT]['all'] : 20.00);
$utilitiesMonthly[NONAPARTMENT][WATER]=($utilities['water'][NONAPARTMENT]['all'] ? $utilities['water'][NONAPARTMENT]['all'] : 15.00);


$logo=$_SERVER['DOCUMENT_ROOT'].'/images/logos/'.strtolower($GCUserName).'.gif';
if(file_exists($logo) && $gis=getimagesize($logo)){
	?><img id="logoImg" src="/images/logos/<?php echo strtolower($GCUserName).'.gif'?>" width="<?php echo $gis[0]?>" height="<?php echo $gis[1]?>" alt="logo" />
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
		?><th class="linkhead" scope="col"><a href="properties3.php?Units_ID=<?php echo $ID;?>" onclick="return ow(this.href,'l1_properties','800,700');" title="click to open this property"><?php echo $PropertyNameShort;?></a></th><?php
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
		if($ApplicationFee>0)$hasApplicationFee=true;
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
    <th scope="row">Gas </th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php 
		if($Gas){
			$g=array();
			$g[]='Yes';
			if($GasPaid)$g[]='<strong>'.$GasPaid.'</strong>';
			echo implode(' -<br />',$g);
		}else{
			echo 'N/A';
		}
		?></td>
	<?php
	}
	?>
  </tr>
    <tr>
      <th scope="row">Water Paid </th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $WaterPaid==1 ? 'Yes' : ($WaterPaid==0.5 ? '1/2':'No');?></td>
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
  <?php
  if($Amenities['PetsAllowed']){
  	?>
	<tr class="sectionStart">
	<th scope="row">Pet Deposit</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		if($PetDeposit>0)$hasPetDeposit=true;
		?><td class="tar">$<?php echo number_format($PetDeposit,2);?></td>
	<?php
	}
	?>
	</tr>
	<tr>
	<th scope="row">Pet Monthly</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		if($PetExtra>0)$hasPetExtra=true;
		?><td class="tar">$<?php echo number_format($PetExtra,2);?></td>
	<?php
	}
	?>
	</tr><?php
  }
  ?>
  <tr class="sectionStart">
    <th scope="row">Cable</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $Cable ? 'Yes' : 'No';?></td>
	<?php
	}
	?>
  </tr>
  <tr>
    <th scope="row">Internet</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo $InternetPaid ? 'Yes' : 'No';?></td>
	<?php
	}
	?>
  </tr>
  <tr>
    <th scope="row">Phone</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tc"><?php echo ($PhonePaid ? 'Yes' : 'No');?></td>
	<?php
	}
	?>
  </tr> 
  <?php if(!$hideCosts){ ?>
  <tr class="sectionStart">
  <th scope="row" colspan="100%">
  Projected Monthly Expenses *  <span class="gray" style="font-style:normal;">Our company accepts no liability for the content of projected monthly expenses and projected onetime expenses unless this information is confirmed by the property.  Prices subject to change without notice upon properties' discretion</span>
  </th>
  </tr>
  <tr class="highlight1">
    <th class="tr" scope="row">Rent:</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr">$<?php	echo number_format($Rent,2);?></td>
		<?php
	}
	?>
  </tr>
  <tr class="highlight1">
    <th class="tr" scope="row">Utilities: </th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr">$<?php	echo number_format($a[$n]['Utilities']=utilities_calc($v['Type']=='APT' ? APARTMENT : NONAPARTMENT),2);?></td>
		<?php
	}
	?>
  </tr>
  <tr class="highlight1">
    <th class="tr" scope="row">Cable/Inet:</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr">
		<?php
		if($Cable xor $InternetPaid){
			$a[$n]['CableInet']=49.95;
		}else if($Cable && $InternetPaid){
			$a[$n]['CableInet']=0.00;
		}else{
			$a[$n]['CableInet']=69.95;
		}
		echo '$'.number_format($a[$n]['CableInet'],2);
		?>
		</td><?php
	}
	?>
  </tr>
  <?php if($hasPetExtra){ ?>
  <tr class="highlight1">
    <th class="tr" scope="row">Pet Monthly:</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr"><?php
		echo '$'.number_format($PetExtra,2);
		?></td>
	<?php
	}
	?>
  </tr>
  <?php } ?>
  <tr>
    <th class="tr" scope="row">MONTHLY TOTAL:</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr"><?php
		echo '$'.number_format($Rent + $PetExtra + $Utilities + $CableInet,2);
		?></td>
	<?php
	}
	?>
  </tr>
  <tr class="sectionStart">
  <th scope="row" colspan="100%">
  Projected One-Time Expenses * <span class="gray" style="font-style:normal;">Our company accepts no liability for the content of projected monthly expenses and projected onetime expenses unless this information is confirmed by the property.  Prices subject to change without notice upon properties' discretion</span>
  </th>
  </tr>
  <tr class="highlight1">
    <th class="tr" scope="row">Deposit:</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr">$<?php	echo number_format($Deposit,2);?></td>
		<?php
	}
	?>
  </tr>
  <?php if($hasPetDeposit || true){ ?>
  <tr class="highlight1">
    <th class="tr" scope="row">Pet Deposit:</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr">$<?php	echo number_format($PetDeposit,2);?></td>
		<?php
	}
	?>
  </tr>
  <?php } ?>
  <?php if($hasApplicationFee){ ?>
  <tr class="highlight1">
    <th class="tr" scope="row">App Fee:</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr">$<?php	echo number_format($ApplicationFee,2);?></td>
		<?php
	}
	?>
  </tr>
  <?php } ?>
  <tr>
    <th class="tr" scope="row">ONE-TIME TOTAL:</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr"><?php
		echo '$'.number_format($Deposit + $PetDeposit + $ApplicationFee,2);
		?></td>
	<?php
	}
	?>
  </tr>
  <?php } ?>
  <tr class="sectionStart">
  <th scope="row" colspan="100%">
  Recreation
  &amp; Amenities </th>
  </tr>
  <tr>
    <th class="tr" scope="row">Pool</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr"><?php	echo $Pool ? 'Yes' : 'No';?></td>
		<?php
	}
	?>
  </tr>
  <tr>
    <th class="tr" scope="row">Hot Tub</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr"><?php	echo $HotTub ? 'Yes' : 'No';?></td>
		<?php
	}
	?>
  </tr>
  <?php
  foreach($a as $n=>$v){
  	extract($v);
  	if($Volleyball || $GameRoom || $FitnessCenter || $BusinessCenter || $Basketball){
		$hasRec=true;
		if($FitnessCenter)$a[$n]['Rec'][]='Fitness Ctr.';
		if($BusinessCenter)$a[$n]['Rec'][]='Bus. Ctr.';
		if($GameRoom)$a[$n]['Rec'][]='Game Room';
		if($Basketball)$a[$n]['Rec'][]='B\'Ball';
		if($Volleyball)$a[$n]['Rec'][]='V\'Ball';
		if($Laundry)$a[$n]['Rec'][]='Laundry';
	}
  }
  if($hasRec){
  ?>
  <tr>
    <th class="tr" scope="row">Other</th>
	<?php 
	foreach($a as $n=>$v){ 
		extract($v);
		?><td class="tr"><?php	echo implode(', ',$Rec);?></td>
		<?php
	}
	?>
  </tr>
  <?php } ?>
  <?php
  $amen=array(
  	'Dishwasher', 'WasherDryer', 'Furnished', 'WalkInClosets', 'PrivateBalcony','VaultedCeilings', 'Storage', 'Fireplace', 
  );
  foreach($amen as $w){
  	$show=false;
  	foreach($a as $v){
		if($w=='WasherDryer')continue;
		if(read_logical($v[$w]))$show=true;
	}
	if(!$show)continue;
  	?><tr>
		<th class="tr" scope="row"><?php echo preg_replace('/([a-z])([A-Z])/','$1 $2',$w);?></th>
		<?php 
		foreach($a as $v){ 
			?><td class="tr"><?php
			if($w=='WasherDryer'){
				echo $v[$w]==2 ? 'Yes' : ($v[$w]==1 ? 'Conn.' : '&nbsp;');
			}else{
				echo read_logical($v[$w]) ? 'Yes' : '&nbsp;';
			}
			?></td>
			<?php
		}
		?>
	  </tr>	
	<?php
  }
  ?>
  
  

</table>
	<?php if(!$hideCosts){ ?>
	<br />
	<span class="gray">* NOTE: This information is provided as a complimentary service to you.  Please confirm all rates with property.</span>
	<?php } ?>
</div>
<?php } ?>

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