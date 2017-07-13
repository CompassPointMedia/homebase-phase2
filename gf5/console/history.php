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

if(minroles()>ROLE_AGENT)exit('You do not have access to this page');

if($data=q("SELECT
h.ID, h.HeaderDate, h.HeaderNumber, h.HeaderType, ABS(SUM(t.Extension)) AS PaymentTotal, h2.HeaderNumber AS InvoiceHeaderNumber, h2.ID AS InvoiceID, COUNT(DISTINCT t2.Headers_ID) AS AppliedCount
FROM finan_headers h, finan_transactions t, finan_TransactionsTransactions tt, finan_transactions t2, finan_headers h2

WHERE 
h.ID=t.Headers_ID AND 
t.ID=tt.ParentTransactions_ID AND
tt.ChildTransactions_ID=t2.ID AND
t2.Headers_ID=h2.ID AND
t2.Headers_ID='$Headers_ID'
GROUP BY h.ID
ORDER BY h.HeaderDate

", O_ARRAY)){
	//OK
	$header=q("SELECT * FROM finan_headers WHERE ID='$Headers_ID'", O_ROW);
}else{
	exit('Unable to find payments for this invoice');
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Payment History for Invoice '.$header['HeaderNumber'];?></title>



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
var isEscapable=1;
var isDeletable=0;
var isModal=0;
var talks=0; //whether this page broadcasts overall state changes which other pages listen for
var listens=0;

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already

</script>

<link rel="stylesheet" type="text/css" href="/Library/css/DHTML/rich_calendar.css">
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar_lang_en.js"></script>

<style type="text/css">
</style>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
<h3>Payment History for Invoice <?php echo $header['HeaderNumber']?></h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>
</div>

</div>
<div id="mainBody">

<style type="text/css">
.yuck{
	margin-bottom:15px;
	}
.yuck td{
	border-bottom:1px solid #ccc;
	padding:2px 3px 2px 4px;
	}
.yuck td.clear{
	border-bottom:none;
	}
</style>
  <table border="0" cellspacing="0" class="spacer yuck">
    <tr>
      <th scope="col">&nbsp;</th>
      <th scope="col">Date</th>
      <th scope="col">Check #</th>
      <th scope="col">Amount</th>
      <th scope="col">Applied To</th>
    </tr>
	<?php 
	foreach($data as $v){
		extract($v);
		?>
		<tr>
		  <td class="clear">[<a href="payments.php?Payments_ID=<?php echo $ID?>" title="Open or edit this payment" onclick="if(g('close').checked)window.close(); return ow(this.href,'l1_payments','850,600');">open</a>]</td>
		  <td><?php echo t($HeaderDate);?></td>
		  <td class="tar"><?php echo $HeaderNumber?></td>
		  <td class="tar"><?php echo number_format($PaymentTotal,2);?></td>
		  <td class="tar"><a href="leases.php?Leases_ID=<?php echo q("SELECT lt.Leases_ID
		  FROM gl_LeasesTransactions lt, finan_transactions t WHERE lt.Transactions_ID=t.ID AND t.Headers_ID=$InvoiceID
		  ", O_VALUE);?>" title="View this invoice" onclick="if(g('close').checked)window.close(); return ow(this.href,'l1_leases','750,800');"><?php echo $InvoiceHeaderNumber?><?php if($AppliedCount>1)echo ' *';?></a></td>
		</tr>
		<?php
	}
	?>
  </table>

	<p>
    <label>
	<input name="close" type="checkbox" id="close" value="1" checked="checked" />
	Close this window when I select a payment or invoice
	</label>
	</p>
	
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