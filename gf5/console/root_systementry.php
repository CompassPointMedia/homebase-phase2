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

$hideCtrlSection=false;
//--------------------------------------------------
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Table management - '.$AcctCompanyName?></title>



<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style>
/** CSS Declarations for this page **/
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
	


<h2>System Entry Tables</h2>
<p class="gray">
To appear here, there must be both an entry in <code>system_tables</code> and <code>system_profiles</code>.  This table is currently hard-coded but should eventually flex to showing the records/list view of any of the tables in this table itself, and also in views or complex joins of tables
</p>
<table id="thisTable" class="yat">
<thead>
<tr>
	<th>Table</th>
	<th>Records</th>
	<th>Last Entry </th>
	<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php
if($a=q("SELECT p.ID AS Profiles_ID, p.Settings, p.Identifier, t.*
FROM system_tables t, system_profiles p WHERE t.ID=p.tables_ID ORDER BY p.Identifier, t.Name", O_ARRAY)){
	$i=0;
	foreach($a as $n=>$v){
		extract($v);
		$i++;
		if($i==1 || $buffer !=$v['Identifier']){
			if(false && $i>1){
				//close previous
				?><tr>
				<td>subtotal 1</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>subtotal 2</td>
				</tr><?php
			}
			$j=0;
			$buffer=$v['Identifier'];
			?><tr>
			<td colspan="100%"><h2><?php echo $buffer;?></h2></td>
			</tr><?php
		}
		$j++;
		?><tr class="<?php echo !fmod($j,2)?'alt':''?>">
			<td><a href="system_entry.php?object=system_tables&identifier=default&system_tables_ID=<?php echo $ID;?>" title="Edit the table configuration" onclick="return ow(this.href,'l1_tables','700,700');"><img src="/images/i/note01.gif" width="8" height="10" alt="edit" /></a>&nbsp;<a href="system_entry.php?object=system_profiles&identifier=default&system_profiles_ID=<?php echo $Profiles_ID;?>" title="Edit this profile" onclick="return ow(this.href,'l1_profiles','700,700');"><?php echo $v['Name'];?></a><?php
			if($v['SystemName']!=$v['Name']){
				?><p class="gray" style="font-size:11px">(<?php echo $v['SystemName'];?>)</p><?php
			}
			?></td>
			<td class="tac"><?php echo q("SELECT COUNT(*) FROM $SystemName", O_VALUE, $MASTER_DATABASE);?></td>
			<td class="tac"><?php 
			echo date('n/j/Y \a\t g:iA',strtotime(q("SELECT UPDATE_TIME FROM information_schema.tables WHERE  TABLE_SCHEMA = '$MASTER_DATABASE' AND TABLE_NAME = '$SystemName'", O_VALUE)));
			?></td>
			<td>open list &nbsp;&nbsp;&nbsp;<a href="system_entry.php?object=<?php echo $SystemName;?>&identifier=<?php echo $Identifier;?>" onclick="return ow(this.href,'l1_<?php echo $SystemName;?>','700,700');">add new</a></td>
		</tr><?php
	}
}else{
	?><tr>
	<td colspan="100%"><em class="gray">No records found for that criteria</em></td>
	</tr><?php
}
?>
<tr>
<td colspan="100%">
<a href="system_entry.php?object=system_tables&identifier=default" title="Edit the table configuration" onclick="return ow(this.href,'l1_tables','700,700');">register new table</a>
&nbsp;
&nbsp;
<a href="system_entry.php?object=system_profiles&identifier=default" title="Edit this profile" onclick="return ow(this.href,'l1_profiles','700,700');">register new profile</a>
<p class="gray">Both links use the raw system entry system currently</p>
</td>
</tr>
</tbody>
</table>
<div class="cb"> </div>
      
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