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

<title><?php echo $PageTitle='Office Listing - '.$AcctCompanyName;?></title>



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

	<h3><img src="/images/i/addr_26x27.gif" width="26" height="27">&nbsp;List Offices</h3>
	<?php if(false){ ?>
	<ul id="staffOptions">
	<li <?php if($thispage=='root_staff.php')echo 'class="selected"';?>><a href="/gf5/console/root_staff.php">List Staff</a></li>
	<li <?php if($thispage=='root_offices.php')echo 'class="selected"';?>><a href="/gf5/console/root_offices.php">List Regions</a></li>
	<li <?php if($thispage=='pd_offices.php')echo 'class="selected"';?>><a href="/gf5/console/pd_offices.php">Staff Positions per Regions</a></li>
	<?php if(minroles()<ROLE_MANAGER){ ?>
	<li class="advanced"><a href="/gf5/console/StaffRoles/index.php" onclick="return ow(this.href,'l1_staffroles','l1_sr','800,500');">Advanced Features</a></li>
	<?php }?>
</ul>
	<?php } ?>
	
	<a href="root_staff.php">View Staff List</a>
	<br />

<style>
table.data_1{
	border-collapse:collapse;
	color:#FFF;
}
.data_1 th{
	text-align:left;
}
.data_1 td{
	border:1px solid #CCC;
	padding:2px 4px 2px 8px;
	background-color:TAN;
	color:#000;
}
.data_1 thead{
	background-color:#667;
}
table.data2{
	border-collapse:collapse;
	border:1px solid #666;
}
.data2 th{
	background-color:PURPLE;
	color:#FFF;
	text-align:left;
}
.data2 td{
	border-bottom:1px solid #ccc;
	background-color:TAN;
	color:#333;
}
</style>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="data_1">
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th>ID</th>
		<th>Location</th>
		<th>Staff(s)</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if($a=q("SELECT * FROM bais_orgaliases, bais_offices WHERE oa_unusername=of_oausername", O_ARRAY)){
		foreach($a as $v){
			extract($v);
			?>
			<tr id="r_<?php echo $oa_unusername?>" valign="top">
				<td width="75" nowrap>
				<a href="resources/bais_01_exe.php?mode=deleteOffice&oa_unusername=<?php echo $oa_unusername?>" title="Delete this Office" target="w3"onclick="return confirm('Are you sure you want to permanently delete this office?');"><img src="/images/i/del2.gif" alt="delete office" width="16" height="18" border="0" /></a>&nbsp;&nbsp;
				<a href="offices.php?oa_unusername=<?php echo $oa_unusername?>" onclick="return ow(this.href,'l1_offcs','600,600');"><img src="/images/i/edit2.gif" width="15" height="18" border="0"></a>			</td>
				<td><?php echo h($oa_org1);?></td>
				<td><?php echo h($oa_businessname);?></td>
				<td><?php
				if($b=q("SELECT un_username, un_firstname, un_lastname, un_email
				FROM bais_universal, bais_StaffOffices WHERE
				un_username=so_stusername AND so_unusername='$oa_unusername'", O_ARRAY)){
					$i=0;
					foreach($b as $w){
						$i++;
						if($i>1)echo ', ';
						?><a href="staff.php?un_username=<?php echo $w['un_username']?>" title="Edit this staff record" onclick="return ow(this.href,'l1_pds','700,600');"><?php echo h($w['un_firstname'] . ' ' . $w['un_lastname'])?></a><?php
					}
				}
				?></td>
			  </tr>
			<?php
		}
	}
	?>
	<tr valign="top">
		<td colspan="98"><a href="offices.php" onclick="return ow(this.href,'l1_offcs','600,600');"><img src="/images/i/add_32x32.gif" width="32" height="32">&nbsp;Add Office..</a></td>
	</tr>
	</tbody>
</table>
	
	<a href="staff.php" onclick="return ow(this.href,'l1_staff','700,700');">Add new..</a>	</div>
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