<?php
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='generic';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');


$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Invoice List - '.$AcctCompanyName;?></title>



<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />


<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle .02 coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
var ResourceToken='<?php echo $ResourceToken;?>';
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';

<?php 
//js var user settings
js_userSettings();
?>
</script>


<?php 
$link='/site-local/gl_extension_'.$GCUserName.'.css';
if(file_exists($_SERVER['DOCUMENT_ROOT'].$link)){ ?>
<link id="cssExtension" rel="stylesheet" type="text/css" href="<?php echo $link?>" />
<?php } ?>
</head>

<body>
<div id="mainWrap">

	<?php require($_SERVER['DOCUMENT_ROOT'].'/gf5/console/gf_header_login_002.php');?>

	
	<div id="mainBody">
	

	<h1>Edit Invoice</h1>
	<?php
	require('components/comp_invoices_251_(dataset_from_scratch_03).php');
	?>
	<div>
	Summary of Property's Current Invoices
	<style type="text/css">
	.lineBasic{
		border-collapse:collapse;
		}
	.lineBasic td, .lineBasic th{
		border:1px solid #777;
		padding:5px;
		}
	</style>
	  <table border="0" cellspacing="0" cellpadding="3" class="lineBasic">
        <tr>
          <th valign="top" scope="col">&nbsp;</th>
          <th valign="top" scope="col">Active</th>
          <th valign="top" scope="col">Due</th>
          <th valign="top" scope="col">Past-Due</th>
          <th valign="top" scope="col">FI<br />
			<span class="gray" style="font-weight:normal;">(Forecasted Invoices)</span>
			</th>
          <th valign="top" scope="col">PP<br />
			<span class="gray" style="font-weight:normal;">(Partially Paid)</span>
			</th>
          <th valign="top" scope="col">DIS<br />
			<span class="gray" style="font-weight:normal;">(Discrepancy Filed)</span>
			</th>
          <th valign="top" scope="col">Late Fees </th>
        </tr>
        <tr>
          <th scope="row"><div align="left">Number of Inv. </div></th>
          <td><?php echo count($allInvoices['ACTIVE']);?></td>
          <td><?php echo count($allInvoices['DUE']);?></td>
          <td><?php echo count($allInvoices['PASTD']);?></td>
          <td><?php echo count($allInvoices['FI']);?></td>
          <td><?php echo count($allInvoices['PP']);?></td>
          <td><?php echo count($allInvoices['DIS']);?></td>
          <td><?php echo count($allInvoices['LATEFEES']);?></td>
        </tr>
        <tr>
          <th scope="row"><div align="left">Dollar Amt. </div></th>
          <td><?php if(count($allInvoices['ACTIVE']))echo '$'.number_format(array_sum($allInvoices['ACTIVE']),2);?></td>
          <td><?php if(count($allInvoices['DUE']))echo '$'.number_format(array_sum($allInvoices['DUE']),2);?></td>
          <td><?php if(count($allInvoices['PASTD']))echo '$'.number_format(array_sum($allInvoices['PASTD']),2);?></td>
          <td><?php if(count($allInvoices['FI']))echo '$'.number_format(array_sum($allInvoices['FI']),2);?></td>
          <td><?php if(count($allInvoices['PP']))echo '$'.number_format(array_sum($allInvoices['PP']),2);?></td>
          <td><?php if(count($allInvoices['DIS']))echo '$'.number_format(array_sum($allInvoices['DIS']),2);?></td>
          <td><?php if(count($allInvoices['LATEFEES']))echo '$'.number_format(array_sum($allInvoices['LATEFEES']),2);?></td>
        </tr>
      </table>
	</div>


	</div>
	<div id="footer">
	<div id="footer">
<p>Home Base&reg; Management DB ver. 5.0 - All rights reserved &copy;2006-<?php echo date('Y');?></p>
</div>

	</div>
	<?php if(!$hideCtrlSection){ ?>
	<div id="showTester" title="Javascript Tester" onclick="g('tester').style.display='block';">&nbsp;</div>
	<div id="tester" >
		<a href="#" onclick="g('ctrlSection').style.display=op[g('ctrlSection').style.display]; return false;">iframes</a><br />
		<textarea name="test" cols="65" rows="4" id="test">g('field').value</textarea><br />
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
</div>
</body>
</html><?php page_end()?>