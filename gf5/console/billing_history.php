<?php
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='generic';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
if($test==5)prn($_SESSION);

require('systeam/php/auth_i2_v100.php');


$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Billing History - '.$AcctCompanyName;?></title>



<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
#toBeBilled{
	background-color:#EDE7DC;
	border:1px dotted #333;
	padding:18px;
	margin-bottom:15px;
	width:450px;
	}
.newBatch{
	border-bottom:1px solid #333;
	padding-top:25px;
	}
.bbtm th{
	border-bottom:1px solid #666;
	}
</style>

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
	
<h1>Billing History </h1>
<?php
if($a=q("SELECT
	l.ID AS Leases_ID, h.HeaderNumber, h.ID, COUNT(lb.Batches_ID) AS TimesBilled, 
	ToBeBilled

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
	GROUP BY l.ID
	/*main criteria*/
	HAVING (SUM(l.ToBeBilled)>0  /*OR !COUNT(lb.Batches_ID)*/)  AND 
	
	AVG(l.LeaseStartDate)<=CURDATE()
	
	ORDER BY h.HeaderNumber /* IF(p.Type='APT',1,2), c.ClientName, h.HeaderDate DESC */", O_ARRAY)){
	if($test==17)prn($qr['time']);
	?>
	<div id="toBeBilled">
	Total of <?php echo count($a);?> invoices to be billed:<br />
	<p>
	<?php
	ob_start();
	foreach($a as $v){
		extract($v);
		/*if($TimesBilled)continue;
		echo '|';
		$b=q("SELECT * FROM _v_leases_master WHERE ID=$Leases_ID", O_ROW);
		if(abs($b['AmountApplied'])==abs($b['OriginalBalance']))continue;
		*/

		?><a href="leases.php?Leases_ID=<?php echo $Leases_ID?>" title="view/edit this lease" onclick="return ow(this.href,'l1_leases','700,700');"><?php echo $HeaderNumber?><?php if($asterisk=$TimesBilled)echo '*';?></a>, <?php
	}
	$out=rtrim(ob_get_contents(), ', ');
	ob_end_clean();
	echo $out;
	?></p>
	<br />
	<input type="button" name="Button" value="Run Billing Batch Now" onclick="return ow('billing_action.php','l1_billingaction','900,700');" />
	<?php
	if($asterisk){
		?><span class="asterisk">*</span><span class="gray">marked invoices have been billed previously</span><br /><?php
	}
	?></div><?php
}else{
	?>
	<em>Currently, no invoices are marked as to be billed</em>
	<?php
}
?>
<?php
$batch=0;
$maxShow=15;
$maxBatches=10;
$batches=q("SELECT * FROM gl_batches WHERE Type='Billing' ORDER BY CreateDate DESC LIMIT $maxBatches", O_ARRAY_ASSOC);

?>
<h3>Previous Batches</h3>
<table border="0" cellpadding="3" cellspacing="0">
<?php
if($batches)
foreach($batches as $Batches_ID=>$v){
	$batch++;
	if($batch>$maxBatches){
		?><tr>
		<td colspan="100%">
		  <a href="javascript:alert('this will load older batches; will be seldom used but sme concept as FaceBook');">more batches..</a>		</td>
		</tr><?php
		break;
	}
	$a=q("SELECT
	l.ID AS Leases_ID, l.LeaseStartDate, h.ID AS Invoices_ID, h.HeaderFlag, h.GLF_DiscrepancyDate, h.GLF_DiscrepancyReason, h.HeaderNumber, c.ClientName, l.Agents_username, l.UnitNumber, l.Rent, lb.SendMethod,
	p.PropertyAddress,
	p.PropertyCity,
	p.PropertyState,
	p.PropertyZip,
	p.Type
	 
	FROM
	gl_LeasesBatches lb,
	gl_leases l,
	gl_properties_units u,
	gl_properties p,
	gl_LeasesTransactions lt,
	finan_transactions t,
	finan_headers h,
	finan_clients c
	
	WHERE 
	Batches_ID=$Batches_ID AND
	lb.Leases_ID=l.ID AND
	l.ID=lt.Leases_ID AND
	l.Units_ID=u.ID AND
	u.Properties_ID=p.ID AND
	lt.Transactions_ID=t.ID AND
	t.Headers_ID=h.ID AND
	h.Clients_ID=c.ID
	GROUP BY l.ID
	ORDER BY IF(p.Type='APT',1,2), c.ClientName, h.HeaderDate DESC
	LIMIT $maxShow", O_ARRAY);
	?>
	<tr>
		<th class="newBatch" colspan="100%"><div style="float:right;padding-left:75px;"> <?php echo $v['Quantity']?> Invoices  </div>
		<?php if(minroles()<ROLE_ADMIN){ ?>
		
		<a target="w2" href="resources/bais_01_exe.php?mode=deleteBillingSent&Batches_ID=<?php echo $Batches_ID;?>" title="Delete this batch" onclick="if(!confirm('You are about to permanently remove this billing history for these invoice(s).  Are you sure?'))return false;"><img src="/images/i/del2.gif" alt="delete" style="opacity:.6;" /></a>&nbsp;&nbsp;
		<?php } ?>
		
		
		Date run: <?php if($test==17)echo '('.$Batches_ID.') ';?><?php echo date('n/j/Y \a\t g:iA',strtotime($v['CreateDate']));?> by <?php echo $v['Creator'];?>
		  <?php if($test==17)prn($qr['time']);?>
		</th>
	</tr>
	<tr>
		<td>Client</td>
		<td>Inv#</td>
		<td>Move-in</td>
		<td>Sent by </td>
	</tr>
	<?php
	$invoice=0;
	$sfr=0;
	foreach($a as $o=>$w){
		$invoice++;
		if(strtoupper($w['Type'])=='SFR' && !$sfr){
			$sfr=1;
			?><tr class="bbtm"><th colspan="100%"> Non-Apartments </th></tr><?php
		}
		?>
		<tr>
			<td nowrap="nowrap"><?php echo $w['ClientName'];?></td>
			<td><a href="leases.php?Leases_ID=<?php echo $w['Leases_ID']?>" title="View/edit this invoice" onclick="return ow(this.href,'l1_leases','700,700');"><?php echo $w['HeaderNumber'];?></a></td>
			<td><?php echo date('n/j/Y',strtotime($w['LeaseStartDate']));?></td>
			<td><?php
			$m=array();
			if($w['SendMethod'] & 1)$m[]='print';
			if($w['SendMethod'] & 2)$m[]='fax';
			if($w['SendMethod'] & 4)$m[]='email';
			echo implode('/',$m);
			?></td>
		</tr>
		<?php
	}
	if($v['Quantity']>$maxShow){
		?><tr>
		<td colspan="100%"><a href="javascript:alert('This will load the full list of invoices under this billing batch, not developed yet');">more..</a></td>
		</tr><?php
	}
}
?>
</table>
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