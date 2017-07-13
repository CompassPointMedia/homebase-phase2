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

//if(!$Clients_ID)exit('Clients_ID not passed');
if($units=q("SELECT 
	p.*
	FROM _v_properties_master_list p
	WHERE 1 ".($Clients_ID ? "AND p.Clients_ID=$Clients_ID" : '')." ORDER BY ClientName, PropertyName, Bedrooms DESC", O_ARRAY)){
	//no need to select for only one unit - but we change this to be able to add a unit
	if(false && count($units)==1){
		header('Location: leases.php?Units_ID='.$units[1]['ID']);
		exit;
	}

}else{
	mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals($err='No properties listed for this client!'),$fromHdrBugs);
	exit($err);
}
$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Receive Payments';?></title>



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
</script>

<style type="text/css">
</style>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
<h3>Select a Property to Lease</h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>
</div>

</div>
<div id="mainBody">
<div class="suite1">
Select the property or unit you wish to enter a lease for:</div>
<table class="data0">
<thead>
	<th>&nbsp;</th>
	<th>Property</th>
	<th>Beds</th>
	<th>Baths</th>
	<th>Rent</th>
</thead>
<tbody>
	<?php
	$i=0;
	foreach($units as $n=>$v){
		$i++;
		?><tr class="<?php echo !fmod($i,2)?'alt':''?>">
		<td>[<a href="leases.php?Units_ID=<?php echo $v['ID']?>" title="Select this property or unit to lease">select</a>]</td>
		<td nowrap="nowrap"><?php echo $v['PropertyName'] ? $v['PropertyName'] : $v['ClientName']?> - <?php echo $v['PropertyAddress']?><br />
			<?php echo $v['PropertyCity']?></td>
		<td class="tac"><?php echo $v['Bedrooms']?></td>
		<td class="tac"><?php echo $v['Bathrooms']?></td>
		<td class="tar">$<?php echo number_format($v['Rent'],2);?></td>
	</tr><?php
	}
	?>
</tbody>
</thead>

</table>

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