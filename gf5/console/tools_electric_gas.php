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

if(minroles()>ROLE_ADMIN)exit('You do not have access to this');

		//pmc = r + e(gas) + gas + internet
		#does not cover phone; electric avg costs for a year
/* ######################################################################################################
		ELECTRIC
								with gas (heating)					without gas (heating)
		apartment				$summer|winter / $spring|fall		$summer|winter / $spring|fall
		house or duplex			$summer|winter / $spring|fall		$summer|winter / $spring|fall
		-----------------------------------------------------------------------------------------
		GAS
		apartment				$summer|winter / $spring|fall
		house or duplex			$summer|winter / $spring|fall
   ###################################################################################################### */


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Gas/Electric Price Schedule - '.$AcctCompanyName?></title>



<link id="cssUndoHTML" rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="../../site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="../../Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
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



<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</div>
<div id="mainBody">

<div id="utilities">
<?php
if(!$refreshComponentOnly){
	?>
  <style type="text/css">
	</style>
  <script language="javascript" type="text/javascript">
	</script>
  <?php
}
if($a=q("SELECT * FROM bais_settings WHERE UserName='{system:utilities}'", O_ARRAY)){
	foreach($a as $v){
		extract($v);
		$utilities[$vargroup][$varnode][$varkey]=$varvalue;
	}
}
?>
  <br />
  The first 6 fields are for monthly estimated costs of <strong>all-electric properties</strong> 
  in three separate periods. It is assumed that the air conditioner will be running in summer, the heater in winter, and with very little activity in the spring fall months (only 2 in this area).  The remaining four fields are for trash and water for both apartments and non-apartments (assumed to be the same
  year round).
  <table width="500">
    <tr>
      <th scope="row">&nbsp;</th>
      <th>Summer</th>
      <th>Spring/<br />
        Fall</th>
      <th>Winter</th>
      </tr>
    <tr>
      <th scope="row">Apartment</th>
      <td>$
        <input name="utilities[electric][<?php echo APARTMENT?>][summer]" type="text" id="utilities[electric][<?php echo APARTMENT?>][summer]" onchange="dChge(this);" value="<?php echo number_format($utilities['electric'][APARTMENT]['summer'],2);?>" size="4" /></td>
      <td>$
        <input name="utilities[electric][<?php echo APARTMENT?>][spring]" type="text" id="utilities[electric][<?php echo APARTMENT?>][spring]" onchange="dChge(this);" value="<?php echo number_format($utilities['electric'][APARTMENT]['spring'],2);?>" size="4" /></td>
      <td>$
        <input name="utilities[electric][<?php echo APARTMENT?>][winter]" type="text" id="utilities[electric][<?php echo APARTMENT?>][winter]" onchange="dChge(this);" value="<?php echo number_format($utilities['electric'][APARTMENT]['winter'],2);?>" size="4" /></td>
      </tr>
    <tr>
      <th scope="row">House/Duplex</th>
      <td>$
        <input name="utilities[electric][<?php echo NONAPARTMENT?>][summer]" type="text" id="utilities[electric][<?php echo NONAPARTMENT?>][summer]" onchange="dChge(this);" value="<?php echo number_format($utilities['electric'][NONAPARTMENT]['summer'],2);?>" size="4" /></td>
      <td>$
        <input name="utilities[electric][<?php echo NONAPARTMENT?>][spring]" type="text" id="utilities[electric][<?php echo NONAPARTMENT?>][spring]" onchange="dChge(this);" value="<?php echo number_format($utilities['electric'][NONAPARTMENT]['spring'],2);?>" size="4" /></td>
      <td>$
        <input name="utilities[electric][<?php echo NONAPARTMENT?>][winter]" type="text" id="utilities[electric][<?php echo NONAPARTMENT?>][winter]" onchange="dChge(this);" value="<?php echo number_format($utilities['electric'][NONAPARTMENT]['winter'],2);?>" size="4" /></td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <th>Trash</th>
      <th>Water</th>
      <td>&nbsp;</td>
      </tr>
    <tr>
      <th scope="row">Apartment</th>
      <td>$
        <input name="utilities[trash][<?php echo APARTMENT?>][all]" type="text" id="utilities[trash][<?php echo APARTMENT?>][all]" onchange="dChge(this);" value="<?php echo number_format($utilities['trash'][APARTMENT]['all'],2);?>" size="4" /></td>
      <td>$
        <input name="utilities[water][<?php echo APARTMENT?>][all]" type="text" id="utilities[water][<?php echo APARTMENT?>][all]" onchange="dChge(this);" value="<?php echo number_format($utilities['water'][APARTMENT]['all'],2);?>" size="4" /></td>
      <td>&nbsp;</td>
      </tr>
    <tr>
      <th scope="row">House/Duplex</th>
      <td>$
        <input name="utilities[trash][<?php echo NONAPARTMENT?>][all]" type="text" id="utilities[trash][<?php echo NONAPARTMENT?>][all]" onchange="dChge(this);" value="<?php echo number_format($utilities['trash'][NONAPARTMENT]['all'],2);?>" size="4" /></td>
      <td>$
        <input name="utilities[water][<?php echo NONAPARTMENT?>][all]" type="text" id="utilities[water][<?php echo NONAPARTMENT?>][all]" onchange="dChge(this);" value="<?php echo number_format($utilities['water'][NONAPARTMENT]['all'],2);?>" size="4" /></td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <br />
  <input type="submit" name="Submit" value="Update" />
  <input type="button" name="Button" value="Close" onclick="window.close();" />
  <input name="mode" type="hidden" id="mode" value="updateUtilities" />
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
</html><?php page_end();?>