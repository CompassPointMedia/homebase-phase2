<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='focusview';
$localSys['pageType']='Properties Window';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');



if(minroles()>ROLE_ADMIN)exit('You do not have access to this page');

if(!$display)$display='cleanEmails';
if(!$cleanTable)$cleanTable='addr_contacts';

if($mode=='mergeLastNames'){
	foreach($merges as $ID=>$lastname){
		$lastnames[strtolower($lastname)][]=$ID;
	}
	foreach($lastnames as $lastname=>$group){
		$target=array_pop($group);
		if(count($group))
		foreach($group as $ID){
			//reroute invoice
			q("UPDATE gl_LeasesContacts SET Contacts_ID=$target WHERE Contacts_ID=$ID");
			//get critical information
			$a=q("SELECT Email, HomePhone, HomeMobile FROM $cleanTable WHERE ID=$ID", O_ROW);
			if($a['Email']!=='')q("UPDATE $cleanTable SET Email='".addslashes($a['Email'])."' WHERE ID=$target");
			if($a['HomePhone']!=='')q("UPDATE $cleanTable SET HomePhone='".addslashes($a['HomePhone'])."' WHERE ID=$target");
			if($a['HomeMobile']!=='')q("UPDATE $cleanTable SET HomeMobile='".addslashes($a['HomeMobile'])."' WHERE ID=$target");
			//now delete
			q("DELETE FROM $cleanTable WHERE ID=$ID");
		}
	}
	header('Location: tools_merge.php?display='.$display);
	exit;
}else if($mode=='cleanEmails'){
	foreach($merges as $email){
		q("UPDATE $cleanTable SET Email='' WHERE trim(Email)='".trim($email)."'");
	}
	header('Location: tools_merge.php?display='.$display);
	exit;
}else if($mode=='cleanHomePhones'){
	foreach($merges as $homephone){
		q("UPDATE $cleanTable SET HomePhone='' WHERE trim(HomePhone)='".trim($homephone)."'");
	}
	header('Location: tools_merge.php?display='.$display);
	exit;
}else if($mode=='cleanMobilePhones'){
	foreach($merges as $homemobile){
		q("UPDATE $cleanTable SET homemobile='' WHERE trim(homemobile)='".trim($homemobile)."'");
	}
	header('Location: tools_merge.php?display='.$display);
	exit;
}


$hideCtrlSection=false;
//--------------------------------------------------
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
 
<title><?php echo $PageTitle='Merge Tools - '.$AcctCompanyName?></title>



<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style>
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
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
function alerttitle(o){
	alert(o.getAttribute('title'));
}
function refreshList(){
	window.location+='';
}
<?php 
//js var user settings
js_userSettings();
?>

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

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
	



Select action: <select name="display" id="display" onchange="window.location='tools_merge.php?display='+this.value;">
	<option value="mergeLastNames" <?php echo $display=='mergeLastNames'?'selected':''?>>Merge Contacts by Last Name</option>
	<option value="cleanEmails" <?php echo $display=='cleanEmails'?'selected':''?>>Clean Emails</option>
	<option value="cleanHomePhones" <?php echo $display=='cleanHomePhones'?'selected':''?>>Clean Phone Numbers</option>
	<option value="cleanMobilePhones" <?php echo $display=='cleanMobilePhones'?'selected':''?>>Clean Cell Numbers</option>
</select>
<br />
<br />
<br>
<form name="form1" method="post" action="">
<input name="mode" type="hidden" id="mode" value="<?php echo $display;?>">
<?php

for($i=65; $i<=65+25; $i++){
	?><a href="#_<?php echo chr($i);?>"><?php echo chr($i);?></a> | <?php
}
$i=64;

if($display=='mergeLastNames'){
	$a=q("SELECT c.ID, firstname, lastname, email, homephone, homemobile FROM $cleanTable c where firstname!='' and lastname!='' order by lastname, firstname", O_ARRAY_ASSOC);
	?>
	<h1>Merge Contacts by Last Name</h1>
	<p class="gray">Check records you want to merge and click submit at bottom.  ONLY DO ONE SET OF THE SAME LAST NAME AT A TIME! Otherwise all similar last names you select will be merged.</p>
	<br>
	<table class="yat" cellspacing="0">
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th>Lastname</th>
		<th>Firstname</th>
		<th>Email</th>
		<th>Phone</th>
		<th>Mobile</th>
	</tr>
	</thead>
	<?php
	foreach($a as $id=>$v){
		$j++;
		if(strtolower(substr($v['lastname'],0,1))!==$buffer){
			$buffer=strtolower(substr($v['lastname'],0,1));
			$i++;
			?><tr>
			<td colspan="100%"><a name="_<?php echo chr($i);?>"></a><h3><?php echo chr($i);?></h3></td>
			</tr><?php
		}
		?>
		<tr class="<?php echo !fmod($j,2)?'alt':''?>" ><td><input name="merges[<?php echo $id;?>]" type="checkbox" id="merges[<?php echo $id;?>]" value="<?php echo strtolower($v['lastname']);?>"></td>
			<td><?php echo $v['lastname'];?></td>
			<td><?php echo $v['firstname'];?></td>
			<td><?php echo $v['email'];?></td>
			<td><?php echo $v['homephone'];?></td>
			<td><?php echo $v['homemobile'];?></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
}else if($display=='cleanEmails'){
	$a=q("SELECT email, COUNT(*) AS count FROM $cleanTable c WHERE TRIM(email)!='' AND email not regexp '^[a-z]{2,3}[0-9]{3,5}@txstate.edu' GROUP BY email order by IF(count(*)>1,2,1), email", O_ARRAY);
	?>
	<br>
	<h1>Remove Junk Emails</h1>
	<h3>total <?php echo count($a);?></h3>
	<p class="gray">Check the email addresses you want to remove and click submit at bottom. <span class="red">MULTIPLE USE EMAILS</span> ARE AT THE BOTTOM.  Valid TSU emails are not showing.</p>
	<table class="yat" cellspacing="0">
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th>Email</th>
		<th># used</th>
	</tr>
	</thead>
	<?php
	$j=0;
	foreach($a as $v){
		$j++;
		if($v['count']<3 && preg_match('/^[a-z]/i',substr($v['email'],0,1)) && strtolower(substr($v['email'],0,1))!==$buffer){
			$buffer=strtolower(substr($v['email'],0,1));
			$i++;
			if(preg_match('/[A-Z]/',chr($i))){
				?><tr>
				<td colspan="100%"><a name="<?php echo chr($i);?>"></a><h3><?php echo chr($i);?></h3></td>
				</tr><?php
			}
		}
		?>
		<tr class="<?php echo !fmod($j,2)?'alt':''?>" ><td><input name="merges[<?php echo $j;?>]" type="checkbox" value="<?php echo strtolower($v['email']);?>"></td>
			<td><?php echo $v['email'];?></td>
			<td><?php echo $v['count'];?></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
}else if($display=='cleanHomePhones'){
	$a=q("SELECT homephone, COUNT(*) AS count FROM $cleanTable c WHERE TRIM(homephone)!=''  GROUP BY homephone order by IF(count(*)>1,2,1), homephone", O_ARRAY);
	?>
	<br>
	<h1>Remove Junk Phone Numbers</h1>
	<h3>total <?php echo count($a);?></h3>
	<p class="gray">Check the phone numbers you want to remove and click submit at bottom. <span class="red">MULTIPLE USE PHONES</span> ARE AT THE BOTTOM.</p>
	<table class="yat" cellspacing="0">
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th>Home Phone</th>
		<th># used</th>
	</tr>
	</thead>
	<?php
	$j=0;
	foreach($a as $v){
		$j++;
		?>
		<tr class="<?php echo !fmod($j,2)?'alt':''?>" ><td><input name="merges[<?php echo $j;?>]" type="checkbox" value="<?php echo strtolower($v['homephone']);?>"></td>
			<td><?php echo $v['homephone'];?></td>
			<td><?php echo $v['count'];?></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
}else if($display=='cleanMobilePhones'){
	$a=q("SELECT homemobile, COUNT(*) AS count FROM $cleanTable c WHERE TRIM(homemobile)!=''  GROUP BY homemobile order by IF(count(*)>1,2,1), homemobile", O_ARRAY);
	?>
	<br>
	<h1>Remove Junk Cell Numbers</h1>
	<h3>total <?php echo count($a);?></h3>
	<p class="gray">Check the cell numbers you want to remove and click submit at bottom. <span class="red">MULTIPLE USE PHONES</span> ARE AT THE BOTTOM.</p>
	<table class="yat" cellspacing="0">
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th>Home Phone</th>
		<th># used</th>
	</tr>
	</thead>
	<?php
	$j=0;
	foreach($a as $v){
		$j++;
		?>
		<tr class="<?php echo !fmod($j,2)?'alt':''?>" ><td><input name="merges[<?php echo $j;?>]" type="checkbox" value="<?php echo strtolower($v['homemobile']);?>"></td>
			<td><?php echo $v['homemobile'];?></td>
			<td><?php echo $v['count'];?></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
}
?>
  <br>
  <input type="submit" name="Submit" value="Submit">
</form>




	
	</div>
	<div id="footer">
	<div id="footer">
<p>Home Base&reg; Management DB ver. 5.0 - All rights reserved &copy;2006-<?php echo date('Y');?></p>
</div>
<div id="ctrlSection" style="display:<?php echo $testModeC?'block':'none'?>;">
<iframe name="w0"></iframe>
<iframe name="w1"></iframe>
<iframe name="w2"></iframe>
<iframe name="w3"></iframe>
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