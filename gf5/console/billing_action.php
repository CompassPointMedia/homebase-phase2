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

$hideCtrlSection=false;


$updateMode='updateBillingSent';
$mode=$updateMode;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Manage Billing</title>



<link rel="stylesheet" type="text/css" href="/site-local/undohtml2.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
.invoiceBox{
	float:left;
	border:1px solid #333;
	padding:20px;
	width:350px;
	-moz-border-radius: 15px;
	border-radius: 15px;
	background-image:url("images/i/grad/v-ffffffff-ffffff00-oso128.png");
	background-repeat:repeat-x;
	background-position:top left;
	}
.discrepancy{
	width:225px;
	border:1px dotted #665;
	margin-left:15px;
	padding:10px;
	}
.colored{
	background-color:ivory;
	}
.noncolored{
	opacity:.50;
	}
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/jq/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/jq/numeric.js"></script>
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

function billInterlock(n){
	return false;
	var a=g('print'+n+'print').checked
	var b=g('print'+n+'email').checked;
	var c=g('print'+n+'fax').checked;
	g('invoice'+n).className=(a || b || c?'invoiceBox colored':'invoiceBox noncolored');
}
function toggleDiscrepancy(n,o){
	window.open('/gf5/console/resources/bais_01_exe.php?mode=toggleDiscrepancy&DiscrepancyDate='+escape(g('ddate'+n).value)+'&DiscrepancyReason='+escape(g('dreason'+n).value)+'&Invoices_ID='+n+'&setTo='+(o.checked?1:0),'w2');
}
</script>


</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
<div id="btns140" class="fr">
<input name="<?php echo $recordPKField?>" type="hidden" id="<?php echo $recordPKField?>" value="<?php echo $$object;?>" />
<input name="navVer" type="hidden" id="navVer" value="<?php echo $navVer?>" />
<input name="navObject" type="hidden" id="navObject" value="<?php echo $navObject?>" />
<input name="nav" type="hidden" id="nav" />
<input name="navMode" type="hidden" id="navMode" value="" />
<input name="count" type="hidden" id="count" value="<?php echo $nullCount?>" />
<input name="abs" type="hidden" id="abs" value="<?php echo $nullAbs?>" />
<input name="insertMode" type="hidden" id="insertMode" value="<?php echo $insertMode?>" />
<input name="updateMode" type="hidden" id="updateMode" value="<?php echo $updateMode?>" />
<input name="deleteMode" type="hidden" id="deleteMode" value="<?php echo $deleteMode?>" />
<input name="mode" type="hidden" id="mode" value="<?php echo $mode?>" />
<input name="submode" type="hidden" id="submode" value="" />
<input name="componentID" type="hidden" id="componentID" value="<?php echo $localSys['componentID']?>" />
<?php
if(count($_REQUEST)){
	foreach($_REQUEST as $n=>$v){
		if(substr($n,0,2)=='cb'){
			if(!$setCBPresent){
				$setCBPresent=true;
				?><!-- callback fields automatically generated --><?php
				echo "\n";
				?><input name="cbPresent" id="cbPresent" value="1" type="hidden" /><?php
				echo "\n";
			}
			if(is_array($v)){
				foreach($v as $o=>$w){
					echo "\t\t";
					?><input name="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" id="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" type="hidden" value="<?php echo stripslashes($w)?>" /><?php
					echo "\n";
				}
			}else{
				echo "\t\t";
				?><input name="<?php echo $n?>" id="<?php echo $n?>" type="hidden" value="<?php echo stripslashes($v)?>" /><?php
				echo "\n";
			}
		}
	}
}
?><!-- end navbuttons 1.43 --></div>
<h3>Unbilled Invoices</h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>
</div>

</div>
<div id="mainBody">
<?php
$a=q("SELECT
l.ID AS Leases_ID, l.LeaseStartDate, h.ID AS Invoices_ID, l.Agents_username,
h.HeaderNumber, h.HeaderFlag, h.GLF_DiscrepancyDate, h.GLF_DiscrepancyReason,  
c.ClientName, l.Agents_username, l.UnitNumber, l.Rent, COUNT(lb.Batches_ID) AS TimesBilled,
IF(p.Type='APT', p.GLF_BillingMethod, c.GLF_BillingMethod) AS BillingMethod,
p.ID AS Properties_ID,
p.PropertyName,
p.PropertyAddress,
p.PropertyCity,
p.PropertyState,
p.PropertyZip,
UCASE(p.Type) AS Type,
t.Extension * -1 AS Extension,
ct.FirstName,
ct.MiddleName, 
ct.LastName
 
FROM
gl_leases l LEFT JOIN gl_LeasesBatches lb ON l.ID=lb.Leases_ID,
gl_properties_units u,
gl_properties p,

gl_LeasesContacts lc,
addr_contacts ct,

gl_LeasesTransactions lt,
finan_transactions t,
finan_headers h,
finan_clients c

WHERE 
l.ID=lt.Leases_ID AND
l.Units_ID=u.ID AND
u.Properties_ID=p.ID AND
l.ID=lc.Leases_ID AND
lc.Contacts_ID=ct.ID AND
lt.Transactions_ID=t.ID AND
t.Headers_ID=h.ID AND
h.Clients_ID=c.ID AND
h.HeaderStatus='Current'
".($hideDiscrepancies ? "AND h.HeaderFlag!='Discrepancy'" : '')."
GROUP BY l.ID
/*main criteria*/
HAVING SUM(l.ToBeBilled)>0 /* OR !COUNT(lb.Batches_ID) */ AND AVG(l.LeaseStartDate)<=CURDATE()
ORDER BY h.HeaderNumber /* IF(p.Type='APT',1,2), c.ClientName, h.HeaderDate DESC*/", O_ARRAY);
?>
<div class="suite1">
<div class="fr">
<a href="billing_action.php?hideDiscrepancies=<?php echo $hideDiscrepancies ? 0 : 1;?>"><strong><?php echo $hideDiscrepancies ? 'Show':'Hide'?> discrepancies</strong></a>
</div>
<h1>Unbilled Invoices</h1>
<div class="fr">
<h3>Statement Date: <?php echo date('n/j/Y');?></h3>
</div>
<p class="gray">
Unselect invoices which you do not wish to bill for this statement date.  To manage emailing and fax settings, click on the property name.
</p>

<?php
if($a){
	?><p>Showing <?php echo count($a);?> invoices<?php echo $hideDiscrepancies ? ' (<a href="billing_action.php?hideDiscrepancies=0" title="Show discrepancy invoices">discrepancies hidden</a>)':''?></p><?php
	foreach($a as $v){
		?><table>
		<tr>
			<td>
			<div id="invoice<?php echo $v['Leases_ID'];?>" class="invoiceBox colored">
			<div class="fr">
			Amount: <?php echo number_format($v['Extension'],2);?><br />
			Tenant: <?php echo $v['FirstName'] . ' ' . $v['LastName'];?><br />
			Agent: <strong><?php echo $v['Agents_username'];?></strong><br />
			</div>
			<h3 class="nullTop"><a href="leases.php?Leases_ID=<?php echo $v['Leases_ID']?>" title="View/edit this invoice" onclick="return ow(this.href,'l1_leases','700,700');">Invoice #<?php echo $v['HeaderNumber'];?></a></h3>
			<a href="properties3.php?Properties_ID=<?php echo $v['Properties_ID'];?>" tabindex="View/edit this property's settings" onclick="return ow(this.href,'l1_properties','700,700');"><?php 
			if($v['Type']=='APT'){
				echo $v['PropertyName'];
			}else{
				echo $v['ClientName'];
			}
			?></a><br />
			<?php
			if($v['Type']=='APT'){
				if(preg_match('/^(tbd|tba|unk|unknown)$/i',$v['UnitNumber'])){
					?><em class="gray">(unknown unit number)</em><br /><?php
				}else{
					echo $v['UnitNumber'].'<br />';
				}
			}else{
				echo $v['PropertyAddress'].', '.$v['PropertyCity'].'<br />';
			}
			//client billing settings
			$billMethods=$v['BillingMethod'];
			?>
			<label style="cursor:pointer;">
			<input name="print[<?php echo $v['Leases_ID']?>][1]" type="checkbox" id="print<?php echo $v['Leases_ID']?>_1" value="1" <?php echo $billMethods & 1  || !$billMethods ? 'checked' : ''; ?> onchange="dChge(this);billInterlock(<?php echo $v['Leases_ID'];?>);" />
			print this invoice</label><br />
			<label style="cursor:pointer;">
			<input name="print[<?php echo $v['Leases_ID']?>][2]" type="checkbox" id="print<?php echo $v['Leases_ID']?>_2" value="2" onchange="dChge(this);billInterlock(<?php echo $v['Leases_ID'];?>);" <?php echo $billMethods & 2 ? 'checked' : ''; ?> /> 
			send by email</label><br />
			<label style="cursor:pointer;">
			<input name="print[<?php echo $v['Leases_ID']?>][4]" type="checkbox" id="print<?php echo $v['Leases_ID']?>_4" value="4" onchange="dChge(this);billInterlock(<?php echo $v['Leases_ID'];?>);" <?php echo $billMethods & 4 ? 'checked' : ''; ?> /> 
			send by fax </label>
			</div>
			<div class="discrepancy fl">
			<label>
			<input name="discrepancy[<?php echo $v['Invoices_ID']?>]" type="checkbox" id="disc<?php echo $v['Invoices_ID']?>" value="1" <?php echo strtolower($v['HeaderFlag'])=='discrepancy' ? 'checked' : ''?> onclick="toggleDiscrepancy(<?php echo $v['Invoices_ID']?>,this);" />
			 Discrepancy</label>
			<br />
			Date: 
			<input name="GLF_DiscrepancyDate[<?php echo $v['Invoices_ID'];?>" type="text" id="ddate<?php echo $v['Invoices_ID'];?>" value="<?php echo t($v['GLF_DiscrepancyDate'], f_qbks);?>" size="12" onchange="toggleDiscrepancy(<?php echo $v['Invoices_ID']?>, g('disc<?php echo $v['Invoices_ID'];?>'));" />
			<br />
			Reason: <br />
			<input name="GLF_DiscrepancyReason[<?php echo $v['Invoices_ID'];?>" type="text" id="dreason<?php echo $v['Invoices_ID'];?>" value="<?php echo h(trim($v['GLF_DiscrepancyReason']));?>" size="32" onchange="toggleDiscrepancy(<?php echo $v['Invoices_ID']?>, g('disc<?php echo $v['Invoices_ID'];?>'));" />
			<br />
			</div>
			</td>
		</tr><?php
	}
	?></table>
	
	<input name="Submit" type="submit" id="Submit" value="Commit to Billing" onclick="if(!confirm('This will set these invoices as no longer to be billed.  Continue?'))return false;" />
	&nbsp;&nbsp;
	<input type="button" name="Submit" value="Cancel" onclick="if(detectChange && !confirm('You will lose your edits/changes.  Continue?'))return false; window.close();" />
	<?php
}else{
	?>
	<p>No invoices currently ready for billing</p>
	<?php
}

?>

</div>

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