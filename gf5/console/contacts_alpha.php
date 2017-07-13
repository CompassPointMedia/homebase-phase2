<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

$hideCtrlSection=false;
if(minroles()>ROLE_AGENT)exit('you do not have access to this page');
if($seed){
	//ok
}else if($lastname){
	$seed=substr(preg_replace('/[^a-z]/i','',$lastname),0,1);
}else{
	$seed='a';
}
$a=q("SELECT c.ID, c.FirstName, c.LastName, c.Email, c.HomePhone, c.HomeMobile, c.ReferralSource, c.ReferralSourceOther, IF(lc.Leases_ID IS NULL AND GLF_MoveInDate!='0000-00-00', GLF_MoveInDate,'') AS GLF_MoveInDate, count(distinct lc.Leases_ID) AS Invoices, un_username, IF(un_lastname IS NOT NULL, CONCAT(un_firstname,' ',un_lastname),'') AS Agent, IF(c.CreateDate, c.CreateDate,'') AS CreateDate
FROM addr_contacts c 
LEFT JOIN bais_universal u ON c.Creator=u.un_username
LEFT JOIN finan_ClientsContacts cc ON c.ID=cc.Contacts_ID 
LEFT JOIN gl_LeasesContacts lc ON c.ID=lc.Contacts_ID
WHERE cc.Clients_ID IS NULL AND LastName LIKE '$seed%' GROUP BY c.ID ORDER BY c.LastName, c.FirstName", O_ARRAY_ASSOC);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo ($PageTitle='Contacts List - '.$AcctCompanyName);?></title>



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

function selectCustomer(ID){
	//try{
	window.opener.g('ID').value=ID;
	window.opener.g('FirstName').value=g('FN'+ID).innerHTML;
	window.opener.g('LastName').value=g('LN'+ID).innerHTML;
	window.opener.g('Email').value=g('EM'+ID).innerHTML.replace('--','');
	var a=g('HM'+ID).innerHTML.replace('--','');
	var b=g('HP'+ID).innerHTML.replace('--','');
	window.opener.g('HomeMobile').value=(a!=''?a:b);
	var RS=g('RS'+ID).value;
	var RSO=g('RSO'+ID).value;
	var MID=g('MID'+ID).value;
	if(RS!='')window.opener.g('ReferralSource').value=RS;
	if(RSO!='')window.opener.g('ReferralSourceOther').value=RSO;
	window.opener.g('ReferralSourceOther').style.visibility=(RS.toLowerCase()=='other' || RS.toLowerCase()=='referral-to get gift card' ? 'visible' : 'hidden');
	if(MID!='')window.opener.g('GLF_MoveInDate').value=MID;
	window.opener.g('FirstName').className='';
	window.opener.g('LastName').className='';
	//}catch(e){ }
	window.close();
	return false;
}
</script>

<link rel="stylesheet" type="text/css" href="/Library/css/DHTML/rich_calendar.css">
<style type="text/css">
</style>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
<h3>Contacts Searcher</h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>
</div>

</div>
<div id="mainBody">


<div class="fr">
  <input type="button" name="Button" value="Cancel" onclick="window.close();" />
</div>
<?php
ob_start();
for($i=65; $i<=65+25; $i++){
	?><a href="contacts_alpha.php?seed=<?php echo chr($i);?>"><?php echo chr($i);?></a> | <?php
}
$out=ob_get_contents();
ob_end_clean();
echo rtrim($out,' |');
?>
<table class="yat" cellspacing="0">
<thead>
<tr>
	<th>&nbsp;</th>
	<th>Lastname</th>
	<th>Firstname</th>
	<th>Email</th>
	<th>Phone</th>
	<th>Mobile</th>
	<th>Invoices?</th>
	<th>Agent</th>
	<th>Mo/Yr</th>
</tr>
</thead>
<?php
if($a){
	foreach($a as $ID=>$v){
		$j++;
		$mine=(sun()==$v['un_username']);
		?>
		<tr class="<?php echo !fmod($j,2)?'alt':''?>" >
			<td>[<a href="#" onClick="return selectCustomer(<?php echo $ID?>);" title="click to select this customer">select</a>]</td>
			<td id="LN<?php echo $ID;?>"><?php echo $v['LastName'];?></td>
			<td id="FN<?php echo $ID;?>"><?php echo $v['FirstName'];?></td>
			<td id="EM<?php echo $ID;?>"><?php echo $v['Email'] ? ($mine ? $v['Email'] :'--') : '&nbsp;';?></td>
			<td nowrap="nowrap" id="HP<?php echo $ID;?>"><?php echo $v['HomePhone'] ? ($mine ? $v['HomePhone'] :'--') : '';?></td>
			<td id="HM<?php echo $ID;?>"><?php echo $v['HomeMobile'] ? ($mine ? $v['HomeMobile'] :'--') : '';?></td>
			<td id="HM<?php echo $ID;?>"><?php echo $mine ? $v['Invoices'] : '';?>&nbsp;
			  <input id="MID<?php echo $ID;?>" type="hidden" name="null" value="<?php if($v['GLF_MoveInDate'])echo date('n/j/Y',strtotime($v['GLF_MoveInDate']));?>" />
              <input id="RS<?php echo $ID;?>" type="hidden" name="null" value="<?php echo h($v['ReferralSource']);?>" />
              <input id="RSO<?php echo $ID;?>" type="hidden" name="null" value="<?php echo h($v['ReferralSourceOther']);?>" /></td>
			<td nowrap="nowrap">
			<?php
			echo $min?'you':($v['Agent'] ? $v['Agent'] : $v['un_username']);
			?>			</td>
			<td class="tac">
			<?php if($v['CreateDate']) echo date('m/y',strtotime($v['CreateDate']));?>
			</td>
		</tr>
		<?php
	}
}else{
	?><tr><td colspan="100%"><em class="gray">(No contacts for this letter; select another letter)</em></td></tr><?php
}
?>
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