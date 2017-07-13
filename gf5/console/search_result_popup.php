<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');



if(!isset($showAgentInfo))$showAgentInfo=0;

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
$sort='PropertyName';
if($sort){
	$sql=explode('ORDER BY',$sql);
	$sql=$sql[0].' ORDER BY ';
	if($sort=='PropertyName')$sql.='PropertyName, m.Rent';
}
if($a=q($sql, O_ARRAY_ASSOC)){
	foreach($a as $v){
		$keys[]=$v['ID'];
	}
}

if($passedPropertyCity){
	foreach($a as $v){
		$cities[$v['PropertyCity']]=true;
	}
}
//--------------------------- code block 009019 ---------------------------

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Property Search'.($searchForm ? ' for '.stripslashes($searchFor):'').' - '.$AcctCompanyName?></title>



<link id="cssUndoHTML" rel="stylesheet" href="../../site-local/undohtml2.css" type="text/css" />
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

</script>


</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onsubmit="return beginSubmit();">
<?php }?>
<div id="header">

<?php
if(!$refreshComponentOnly){
	?><style type="text/css">
	.unfilter{
		background-color:tomato;
		color:white;
		}
	.hl td{
		background-color:aliceblue;
		}
	.ILO{
		cursor:pointer;
		background-color:snow;
		}
	</style>
	<script language="javascript" type="text/javascript">
	function filterSelected(action){
		var str='';
		if(action=='filter'){
			for(var i in boxKeys){
				if(typeof g('c_'+boxKeys[i])=='undefined')continue;
				if(g('c_'+boxKeys[i]).checked)str+=g('c_'+boxKeys[i]).value+',';
			}
			if(!str){
				alert('no properties checked!');
			}else{
				var w=window.location+'';
				window.location=w.replace(/&*filterSelected=[0-9,]+/,'')+'&filterSelected='+str;
			}
		}else{
			var w=window.location+'';
			window.location=w.replace(/&*filterSelected=[0-9,]+/,'');
		}
	}
	function showDetails(){
		var str='';
		for(var i in boxKeys){
			if(typeof g('c_'+boxKeys[i])=='undefined')continue;
			if(g('c_'+boxKeys[i]).checked)str+=g('c_'+boxKeys[i]).value+',';
		}
		if(!str){
			alert('Select at least one property for the details!');
			return;
		}
		return ow('search_result_detail.php?Searches_ID=<?php echo $Searches_ID;?>&Properties_ID='+str+'&hideCompChart='+(g('showCompChart').checked?0:1)+'&hideCosts='+(g('showCosts').checked?0:1),'l1_detail','910,700');
	}
	function showAgentInfo(){
		var w=window.location+'';
		window.location=w.replace(/&*showAgentInfo=[01]/,'') + '&showAgentInfo=<?php echo $showAgentInfo?'0':'1';?>';
	}
	function toggleClick(o){
		var n=o.id.replace('r_','');
		var c=g('c_'+n).checked;
		g('c_'+n).checked=!c;
	}
	function allNone(n){
		for(var i in boxKeys){
			g('c_'+boxKeys[i]).checked=(n=='All');
		}
		g('allNone').innerHTML=(n=='All'?'None':'All');
		return false;
	}
	var boxKeys=[<?php echo implode(',',$keys);?>];
	</script><?php
}
?>
<div class="printhide fr">
	<input type="button" name="Button" value="<?php echo $filterSelected?'Remove Filter':'Filter Selected';?>" onclick="filterSelected('<?php echo $filterSelected?'unfilter':'filter'?>');" <?php if($filterSelected)echo 'class="unfilter"';?> />
	&nbsp;	
	<input type="button" name="Button" value="Toggle Agent Info" onclick="showAgentInfo();" />
	&nbsp;	
	<input type="button" name="Button" value="Details" onclick="showDetails();" />
	&nbsp;	
	<input type="button" name="Button" value="Print" onclick="alert('Note: this will print the listings as shown on this page.  You may need to change the print orientation to &quot;landscape&quot; for best results.  To view details, click the Details button to the left of this.');window.print();" />
	&nbsp;
	<input type="button" name="Button" value="Close" onclick="window.close();" /><br />
	<label>
	<input name="showCompChart" type="checkbox" id="showCompChart" value="1" <?php echo $userSettings['search:hideCompChart']==1?'':'checked';?> />
	Show search comp chart on detail view 
	</label> &nbsp;&nbsp;
	<label>
	<input name="showCosts" type="checkbox" id="showCosts" value="1" <?php echo $userSettings['search:hideCosts']==1?'':'checked';?> />
	Show cost comparisons 
	</label>
</div>
</div>
<div id="mainBody">
<?php
$FirstName=trim(preg_replace('/first name/i','',$FirstName));
$LastName=trim(preg_replace('/last name/i','',$LastName));
if($FirstName || $Email || $HomeMobile){
	?><div class="prospect">
	<h2>Search for: <?php echo $FirstName . ' '. $LastName;?></h2>
	Phone: <?php echo $HomeMobile;?><br />
	Email: <a href="mailto:<?php echo $Email;?>"><?php echo $Email;?></a>
	</div><?php
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


<h3><?php echo count($a) . ' record'.(count($a)>1?'s':'').' found';?></h3>
<span class="gray">Search results as of <?php echo date('n/j \a\t g:iA');?> <span class="printhide">Note that phone numbers appear when printing out this list</span></span><br />

<table class="yat">
<thead>
<tr>
	<th class="printhide tac" style="padding:3px 5px 1px 2px;">
	<a title="Click to check/uncheck all properties" href="#" style="font-size:smaller;" id="allNone" onclick="return allNone(this.innerHTML);">All</a>
	
	</th>
	<th>Type</th>
	<th>Property Name</th>
	<th>Address</th>
	<th class="screenhide">Phones</th>
	<th>Layout</th>
	<th>Square<br />
	Feet</th>
	<th>F.P.</th>
	<th class="tac">Map</th>
	<th>Rent</th>
	<?php if($n=$Amenities['WasherDryer']){ ?>
	<th>W/D</th>
	<?php } ?>
	<?php if($Amenities['PetsAllowed']){ ?>
	<th>Pet Info</th>
	<?php } ?>
	<?php if($showAgentInfo){ ?>
	<th>Agent Info</th>
	<th>Escort <br />
	  Comm.</th>
	<th>Send <br />
	  Comm.</th>
	<?php } ?>
	<?php if($data['Contacts_ID']){	?>
	<th>&nbsp;</th>
	<?php } ?>
</tr>
</thead>
<?php
$i=0;
if($a){
	$fps=q("SELECT
	ot.Objects_ID, ot.Tree_ID, t.Name
	FROM gl_ObjectsTree ot, relatebase_tree t
	WHERE ot.Objects_ID IN(".implode(',',array_keys($a)).") AND ObjectName='gl_properties_units' AND ot.Tree_ID=t.ID", O_ARRAY_ASSOC);
	foreach($a as $v){
		extract($v);
		$i++;
		?><tr id="r_<?php echo $ID?>" class="<?php echo !fmod($i,2)?'alt':''?>" onmouseover="this.className='hl';" onmouseout="this.className='<?php echo !fmod($i,2)?'alt':''?>';" onclick="toggleClick(this);">
		
		<td class="printhide tac" style="padding:1px 12px;"><input class="ctrl" name="Units_ID[]" type="checkbox" id="c_<?php echo $ID;?>" value="<?php echo $ID;?>" onclick="toggleClick(g('r_<?php echo $ID;?>'));" /></td>
		<td><?php
		echo $Type.($IndividualLeaseOption?'<span class="ILO" title="ILO stands for Individual Lease Option">-ILO</span>':'');
		?></td>
		<td><a href="properties3.php?Units_ID=<?php echo $ID;?>" title="view/edit this property" onclick="return ow(this.href,'l1_properties','700,700');"><?php echo $PropertyName;?></a></td>
		<td><?php echo $PropertyAddress;
		if(count($cities)>1)echo '<br />'.$PropertyCity;
		?></td>
		<td class="screenhide"><?php
		if($Phone)echo $Phone.' (p)';
		if($Fax)echo '<br />'.$Fax.' (f)';
		?></td>
		<td class="tac"><?php echo $Bedrooms . '/'.$Bathrooms?></td>
		<td><?php echo $SquareFeet;?></td>
		<td class="tac"><?php
		if($f=$fps[$ID]){
			?><a href="/images/reader.php?Key=<?php echo substr($f['Name'],0,6);?>&Tree_ID=<?php echo $f['Tree_ID'];?>" onclick="return ow(this.href,'l1_pic','700,700');" title="View floor plan"><img src="/images/i-local/floorplanmini.png" /></a><?php
		}
		?></td>
		<td class="tac"><?php
		if(!$staffOffices){
			$staffOffices=array(
				array(
					'Address'=>$AcctAddress,
					'City'=>'San Marcos',
					'State'=>'TX',
					'Zip'=>'78666',
				),
			);
		}
		$offc=$staffOffices[0];
		$googleURL='http://maps.google.com/maps?f=d&source=s_d&saddr='.urlencode($offc['Address']).',+'.urlencode($offc['City']).',+'.urlencode($offc['State']).'+'.urlencode($offc['Zip']).'&daddr='.urlencode($PropertyAddress).',+'.urlencode($PropertyCity).',+'.urlencode($PropertyState).'+'.urlencode($PropertyZip).',+United+States&hl=en&mra=ls&g='.urlencode($PropertyAddress).',+'.urlencode($PropertyCity).',+'.urlencode($PropertyState).'+'.urlencode($PropertyZip).',+United+States&ie=UTF8&z=12';
		?><a title="Click here for a Google Map" onclick="return ow(this.href, 'l1_maps','750,600');" href="<?php echo $googleURL?>"><img src="/images/i/findicons.com-map.png" width="16" height="16" alt="map" /></a>		</td>
		<td class="tar"><?php if($Rent>0)echo '$'. number_format($Rent,2);?></td>
		<?php if($Amenities['WasherDryer']){ ?>
		<td><?php
		echo $WasherDryer==2?'Y':($WasherDryer==1?'Conn.':'');
		?></td>
		<?php } ?>
		<?php if($Amenities['PetsAllowed']){ ?>
		<td><?php 
		ob_start();
		if($PetRestrictions)echo '<span class="red">restrictions:</span> '.trim($PetRestrictions).'; ';
		if($PetWeightLimit)echo '<span class="red">wt.limit:</span> '.trim($PetWeightLimit).'; ';
		if($PetDeposit)echo '<span class="red">deposit:</span> '.trim($PetDeposit).'; ';
		if($PetPolicies)echo '<span class="red">policy:</span> '.trim($PetPolicies).'; ';
		$out=ob_get_contents();
		ob_end_clean();
		echo trim($out,'; ');
		?></td>
		<?php } ?>
		<?php if($showAgentInfo){ ?>
		<td><?php echo $AgentInfo;?></td>
		<td><?php echo calculate_commission($EscortCommission, array('showIndicators'=>true));?></td>
		<td><?php echo calculate_commission($SendCommission, array('showIndicators'=>true));?></td>
		<?php } ?>
		<?php if($data['Contacts_ID']){	?>
		<td nowrap="nowrap">
		<a href="leases.php?Units_ID=<?php echo $ID?>&Contacts_ID=<?php echo $data['Contacts_ID']?>" onclick="return ow(this.href,'l1_leases','750,800');" title="lease this property to this prospect">lease this</a>
		</td>
		<?php } ?>
		</tr><?php
	}
}
?>
</table>



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
</html><?php page_end();?>