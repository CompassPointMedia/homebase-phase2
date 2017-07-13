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

<title><?php echo $PageTitle='Gift Cards - '.$AcctCompanyName;?></title>



<link rel="stylesheet" href="/site-local/undohtml2.css" type="text/css" />
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
	

	<h1>Gift Cards</h1>
<form name="form1" id="form1" action="resources/bais_01_exe.php" method="post" target="w2">
  <input name="mode" type="hidden" id="mode" value="commitGiftCardBatch" />
<style type="text/css">
td.leftIdx{
	background-color:cornsilk;
	padding:2px 10px;
	text-align:center;
	}
td.leftPad{
	padding-left:15px;
	}
td.header{
	border-bottom:1px solid #333;
	}
</style>
<script language="javascript" type="text/javascript">
var toggleDefaultClassName='toggleCheckbox';
function toggle(o){
	var t=o[0];
	var c=(o['className'] ? o['className'] : toggleDefaultClassName);
	if(a=document.getElementsByClassName(c)){
		for(var i in a){
			if(o['action']){
				//future actions or a callback function
			}else{
				try{ a[i].checked=(t?true:false); }catch(e){ }
			}
		}
	}
}
</script>
<?php

//for later
$batches=q("SELECT
b.*, SUM(GiftCard1 + GiftCard2) AS TotalCost
FROM gl_batches b, gl_leases l 
WHERE b.ID=l.GCBatch1
GROUP BY b.ID
ORDER BY b.CreateDate DESC, b.ID DESC", O_ARRAY_ASSOC);
	
if($Batches_ID){
	$a=q("SELECT
	l.*,
	un_firstname AS AgentFirstName,
	un_middlename AS AgentMiddleName,
	un_lastname AS AgentLastName,
	un_email AS AgentEmail
	FROM (_v_leases_master l JOIN gl_batches b ON l.GCBatch1=b.ID AND b.Type='Gift cards') LEFT JOIN bais_universal ON l.Agents_username=un_username
	WHERE GCBatch1=$Batches_ID
	ORDER BY Headers_ID DESC",O_ARRAY);
}else{
	if(!$displayMode)$displayMode='unpaid';
	$a=q("SELECT
	l.*,
	".($displayMode=='paid'?'b.ID AS Batches_ID, ':'')."
	un_firstname AS AgentFirstName,
	un_middlename AS AgentMiddleName,
	un_lastname AS AgentLastName,
	un_email AS AgentEmail
	FROM (_v_leases_master l ".($displayMode=='paid'?' JOIN gl_batches b ON l.GCBatch1=b.ID AND b.Type=\'Gift cards\'':'').") LEFT JOIN bais_universal ON l.Agents_username=un_username
	WHERE 
	(
	GiftCard1>0 OR GiftCard2>0
	) 
	AND ".($displayMode=='unpaid'?"(GCBatch1 IS NULL OR GCBatch1=0)":"GCBatch1>0")."
	ORDER BY ".($displayMode=='paid'?'b.CreateDate DESC, b.ID DESC, ':'')."Headers_ID DESC",O_ARRAY);
}
?>
<div class="fr">
<?php if($displayMode=='unpaid'){ ?>
<input type="submit" name="Submit" value="Commit Giftcards to Batch" onclick="if(!confirm('This will mark gift cards on the selected invoices as paid and give you the option to view/print the new batch.  Continue?'))return false;" class="th1b" />
<?php } ?>
&nbsp;
<input name="Button" type="button" id="Submit" value="Print" onclick="window.print();" />
</div>
<span class="gray" style=" font-size:smaller;">Query took <?php echo $qr['time'];?> seconds</span>
<br />
<br />
Total of <?php echo count($a).($displayMode=='unpaid'?' Unpaid':'');?> Invoices with Gift Cards Showing<br />
<br />
<a href="root_giftcards.php?displayMode=paid" onclick="return confirm('This will show ALL payment batches and may take time to load; continue?');">show paid</a> | <a href="root_giftcards.php?displayMode=unpaid">show unpaid</a>
<?php if($displayMode=='unpaid'){ ?>| <a href="javascript:toggle({0:1});">select all</a> | <a href="javascript:toggle({0:0});">select none</a><?php } ?><br />
<br />
.. or show specific batch: 
<select name="Batches_ID" id="Batches_ID" onchange="if(this.value!=='')window.location='root_giftcards.php?Batches_ID='+this.value;">
<option value="">&lt;Select..&gt;</option>
<?php
if($batches){
	foreach($batches as $n=>$batch){
		?><option value="<?php echo $n?>" <?php echo $n==$Batches_ID?'selected':''?>><?php echo 'Batch '.$n.' - '. date('m/d/Y',strtotime($batch['CreateDate'])).' by '.$batch['Creator'].' - ' . $batch['Quantity']. ' record'.($batch['Quantity']>1?'s':'').', $'.number_format($batch['TotalCost'],2).' total'; ?></option><?php
	}
}
?>
</select> 

<br />

<table class="yat">
<thead>
<tr>
	<?php if($displayMode=='unpaid'){ ?><th class="leftIdx">&nbsp;</th><?php } ?>
	<th>&nbsp;</th>
	<th>Date</th>
	<th>Inv.#</th>
	<th>Agent</th>
	<th>Property</th>
	<th>Customer</th>

	<th>Amt.</th>
	<th>Org.</th>
	<th>Name</th>
	<th>Phone</th>
	<th>Email</th>
	
	<th>&nbsp;</th>
</tr>
</thead><?php
$i=0;
if($a){
	foreach($a as $v){
		$i++;
		extract($v);
		if($Batches_ID || $displayMode=='paid'){
			if($buffer!=$Batches_ID){
				$buffer=$Batches_ID;
				$batch=$batches[$Batches_ID];
				?><tr><td class="header" colspan="100%"><h3 class="nullBottom"><?php echo 'Batch '.$batch['ID'].' - '. date('m/d/Y',strtotime($batch['CreateDate'])).' by '.$batch['Creator'].' - ' . $batch['Quantity']. ' record'.($batch['Quantity']>1?'s':'').', $'.number_format($batch['TotalCost'],2).' total';?></h3></td></tr><?php
			}
		}
		$importPrefix='2020';
		//convert string - only do this once
		if($GCData1=='run algorithm'){
			if(!$importPrefix){
				mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='this page needs to convert old database values into new ones for the referring person.  This page will not run until an administrator has modified this; an admin HAS BEEN emailed but call if this page is not working soon!'),$fromHdrBugs);
				exit($err);
			}
			//get data natively
			$GCData1=q("SELECT CONCAT(H_OnSite, H_Student_Organization) AS Organization, H_Referral AS Name FROM cpm180_hmr.__{$importPrefix}_Customers WHERE Customer_ID=$ID", O_ROW);
			$GCData1=serialize($GCData1);
			q("UPDATE gl_leases SET GCData1='".addslashes($GCData1)."' WHERE ID=$ID");
			//prn($GCData1);
		}
		if($GCData2=='run algorithm'){
			if(!$importPrefix){
				mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='this page needs to convert old database values into new ones for the referring person.  This page will not run until an administrator has modified this; an admin HAS BEEN emailed but call if this page is not working soon!'),$fromHdrBugs);
				exit($err);
			}
			//get data natively
			$GCData2=q("SELECT CONCAT(H_OnSite, H_Student_Organization) AS Organization, H_Referral AS Name FROM cpm180_hmr.__{$importPrefix}_Customers WHERE Customer_ID=$ID", O_ROW);
			$GCData2=serialize($GCData2);
			q("UPDATE gl_leases SET GCData2='".addslashes($GCData2)."' WHERE ID=$ID");
			//prn($GCData2);
		}
		if(trim($GCData1)){
			$GCData1=unserialize($GCData1);
		}else{
			unset($GCData1);
		}
		if(trim($GCData2)){
			$GCData2=unserialize($GCData2);
		}else{
			unset($GCData2);
		}
		
		?><tr>
		<?php if($displayMode=='unpaid'){ ?><td class="leftIdx nobo"><input type="checkbox" name="select[<?php echo $ID?>]" class="toggleCheckbox" value="1" /></td><?php } ?>
		<td class="leftPad nobo"><a href="leases.php?Leases_ID=<?php echo $ID?>" onclick="return ow(this.href,'l1_leases','700,700');" title="view/edit this lease"><img src="/images/i/edit2.gif" alt="edit" /></a></td>
		<td><?php echo str_replace('/'.date('Y'), '',date('n/j/Y',strtotime($HeaderDate)));?></td>
		<td><a href="leases.php?Leases_ID=<?php echo $ID?>" onclick="return ow(this.href,'l1_leases','700,700');" title="view/edit this lease"><?php echo $HeaderNumber;?></a><?php echo $GiftCard1>0 && $GiftCard2>0?'*':'';?></td>
		<td><?php echo $FirstName . ' '. $LastName;?></td>
		<td><a href="properties3.php?Properties_ID=<?php echo $Properties_ID;?>" title="view/edit this property" onclick="return ow(this.href,'l1_properties','700,700');"><?php echo $PropertyName;?></a></td>
		<td><a href="directors.php?un_username=<?php echo $un_username;?>" title="view/edit agent record" onclick="return ow(this.href,'l1_agents','700,700');"><?php echo $AgentFirstName . ' ' . $AgentLastName; ?></a></td>
	
		<td><?php echo number_format($GiftCard1,2);?></td>
		<td><?php echo $GCData1['Agent'].$GCData1['Business'].$GCData1['Organization']?>&nbsp;</td>
		<td><?php echo $GCData1['Name'];?></td>
		<td><?php echo $GCData1['Phone'];?></td>
		<td><?php echo $GCData1['Email'];?></td>
	
		<?php if($GiftCard2 > 0){ ?>
		<?php
		echo '</tr>';
		echo '<tr>'.($displayMode=='unpaid' ? '<td class="leftIdx">&nbsp;</td>':'').'<td colspan="6">&nbsp;</td>';
		?>
		<td><?php echo number_format($GiftCard2,2);?></td>
		<td><?php echo $GCData2['Agent'].$GCData2['Business'].$GCData2['Organization']?>&nbsp;</td>
		<td><?php echo $GCData2['Name'];?></td>
		<td><?php echo $GCData2['Phone'];?></td>
		<td><?php echo $GCData2['Email'];?>&nbsp;</td>
		<?php } ?>
		
		</tr><?php
	}
}else{
	?><tr><td colspan="100%"><span class="gray">No <?php echo !$_SERVER['QUERY_STRING'] || $displayMode=='unpaid'?'unpaid':'';?> gift cards found</span></td></tr><?php
}
?></table>
</form>	
	


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