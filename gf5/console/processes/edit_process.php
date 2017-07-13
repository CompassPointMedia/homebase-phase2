<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
/*><script>*/
/******** NOTES ON THIS PAGE ***************** 
LAST EDIT DATE 2004-06-08 BY SAM FULLMAN	changed RelateBase over to Reasons to Believe layout.
This is the page which 
1. you must change sys.scriptID to the relevant value
2. eventually I want to have a single security include file
3. there is no layout control needed for this page, when created it will be named properties_i1_v100.css
4. make sure the proper .js pages are included or excluded
*********************************************/
session_start();
# Identify this script
$localSys['scriptID']='mgprocesses';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='editprocs';

//main configuration file
require('../systeam/php/config.php');
//Verify access to this page
require_once('../console/systeam/php/auth_i2_v100.php');
if(!$roleAccessPresent){
	?>
<script>alert('You do not have permission to do this task');</script><?php
	exit('This page allows access through a specific role only.  Please see your administrator');
}
$db_cnx=mysqli_connect($MASTER_HOSTNAME, $MASTER_USERNAME, $MASTER_PASSWORD);
mysqli_select_db($db_cnx, $MASTER_DATABASE);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Edit Administrative Processes</title>
<link rel="stylesheet" href="/Library/css/layers/layer_engine_v200.css" type="text/css"/>

<?php
$cg[1][CGPrefix]="editProcesses";
$cg[1][CGLayers]=Array('editProc', 'helpPage');
$cg[1][defaultLayer]="editProc";
$cg[1][layerScheme]=1;
$cg[1][schemeVersion]=2.1;
//this will generate JavaScript, all instructions are found in this file
require('../../Library/css/layers/layer_engine_v201.php');
?>
<link rel="stylesheet" href="/Library/css/properties/properties_i2(wi)_v100.css" type="text/css"/>
<link rel="stylesheet" href="/Library/css/layers/layer_engine_v100.css" type="text/css"/>

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

isEscapable=1;
</script>



<link rel="stylesheet" type="text/css" href="../../../site-local/undohtml2.css" />
<link rel="stylesheet" type="text/css" href="../../../site-local/smaac_simple.css" />
<style type="text/css">
/* local CSS styles */
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="../../../site-local/local.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
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

<?php if($tabbedMenus){
	?>
	<link rel="stylesheet" href="/Library/css/layer_engine_v200.css" type="text/css"/>
	<?php
	$cg[1]['CGPrefix']="groupname";
	$cg[1]['CGLayers']=array('tab1', 'tab2');
	$cg[1]['defaultLayer']='tab1';
	$cg[1]['layerScheme']=1;
	$cg[1]['schemeVersion']=2.1;
	$activeHelpSystem=true;
	//this will generate JavaScript, all instructions are found in this file
	?><?php
	require('../devteam/php/snippets/layer_engine_v211.php');
	?><?php
}
?>

</head>

<body>
<form id="form1" name="form1" target="w2" method="post" action="../resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
top nav
</div>
<div id="mainBody"><div id="mainBody"><div class="objectWrapper">
<div id="winTitle">Edit Administrative Processes</div>
<form action="manage_processes_02_exe.php" method="post" name="form1" target="w3" id="form1">
			<table width="432" border="0" cellspacing="0" cellpadding="0">
      <tr> 
		  <!-- this is a patch, won't have a consistent white top w/o this -->
        <td width="50">
          <table class="menu" border="0" cellspacing="0" cellpadding="0">
            <tr valign="bottom"> 
              <td> 
                <div id="editProcesses_i_null" style="border-bottom:1px solid white;<?php echo cg('ib','editProcesses','null');?>"> 
                  <table border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td class="xbc_">&nbsp;</td>
                    </tr>
                  </table>
                </div>
              </td>
				  <td> 
 <div id="editProcesses_a_editProc" style="<?php echo cg('ab','editProcesses','editProc',1);?>"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif" /></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif" /></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="xbc_" nowrap="nowrap">Edit Process</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
 <div id="editProcesses_i_editProc" style="border-bottom:1px solid white;<?php echo cg('ib','editProcesses','editProc',1);?>" onclick="hl_1('editProcesses',editProcesses,'editProc');"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif" /></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif" /></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="bc_" nowrap="nowrap">Edit Process</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
</td><td> 
 <div id="editProcesses_a_helpPage" style="<?php echo cg('ab','editProcesses','helpPage');?>"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif" /></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif" /></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="xbc_" nowrap="nowrap">Help Page</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
 <div id="editProcesses_i_helpPage" style="border-bottom:1px solid white;<?php echo cg('ib','editProcesses','helpPage');?>" onclick="hl_1('editProcesses',editProcesses,'helpPage');"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif" /></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif" /></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="bc_" nowrap="nowrap">Help Page</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
</td>
              <td> 
                <div id="editProcesses_i_null2" style="border-bottom:1px solid white;"> 
                  <table border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td class="xbc_">&nbsp;&nbsp;&nbsp;</td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
          </table>
        </td>
        <td valign="bottom"> 
          <div style="border-bottom:1px solid white;">&nbsp;</div>
        </td>
      </tr>
    </table>
<div id="editProcesses_editProc" class="aArea" style="height:200;width:432;<?php echo cg('l','editProcesses','editProc',1)?>">
<?php 
$sql="SELECT * FROM bais_processes WHERE pr_id='$pr_id'";
$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
$r=mysqli_fetch_array($result);
extract($r);

?>
			
			<input name="pr_id" type="hidden" id="pr_id" value="<?php echo $pr_id?>" />
			<input name="_case" type="hidden" id="_case" value="update" />
	      <table border="0" cellspacing="0" cellpadding="2">
         			<col align="right" />
         			<col />
         			<tr>
         						<td>Category:</td>
         						<td><select name="pr_category" onchange="d.ctrlOK.disabled=true" id="pr_category"><?php
$sql="SELECT * from _bais_processes_categories ORDER BY prc_name";
$result=mysqli_query($db_cnx, $sql) or die(mysqli_error($db_cnx));
while($r1=mysqli_fetch_array($result)){
	extract($r1);
	?>
         							<option value="<?php echo htmlentities($prc_name);?>"><?php
	echo htmlentities($prc_name);
}
									?></option>
         						</select><script>d.pr_category.value='<?php echo $pr_category?>';</script>
							</td>
					</tr>
         			<tr>
         						<td>Name:</td>
         						<td><input name="pr_name" value="<?php echo htmlentities($pr_name)?>" type="text" id="pr_name_new" onchange="form1.ctrlOK.disabled=false;" size="45" maxlength="80" />
							</td>
					</tr>
         			<tr>
         						<td>Handle:</td>
         						<td><input name="pr_handle" value="<?php echo htmlentities($pr_handle)?>" type="text" id="pr_handle" onchange="form1.ctrlOK.disabled=false;" maxlength="24" />
							</td>
					</tr>
         			<tr valign="top">
         						<td>Description:</td>
         						<td><textarea name="pr_description" cols="37" rows="4" id="pr_description" onchange="form1.ctrlOK.disabled=false;" onblur="if(this.value.length&gt;255){alert('Description can be 255 characters maximum');this.select();}" ><?php echo htmlentities($pr_description)?></textarea>
							</td>
					</tr>
         			<tr>
         						<td>URL (from root):</td>
         						<td><input name="pr_url" value="<?php echo htmlentities($pr_url)?>" type="text" onchange="form1.ctrlOK.disabled=false;" id="pr_url" size="35" maxlength="255" />
							</td>
					</tr>
         			<tr>
         						<td>Version:</td>
         						<td><input name="pr_version" value="<?php echo htmlentities($pr_version)?>" type="text" id="pr_version" onchange="form1.ctrlOK.disabled=false;" size="7" maxlength="4" />
							</td>
					</tr>
         			<tr>
         						<td height="28">Page Opens:</td>
         						<td><select name="pr_windowtype" id="pr_windowtype" onchange="form1.ctrlOK.disabled=false;" >
         												<option value="standard">in the same window         												</option>
         												<option value="properties">in a properties window         												</option>
         												<option value="balloon">in a ballon window         												</option>
         						</select><script>d.pr_windowtype.value='<?php echo $pr_windowtype;?>'</script>
							</td>
					</tr>
       			</table>
</div>

<div id="editProcesses_helpPage" class="aArea" style="height:200;width:432;<?php echo cg('l','editProcesses','helpPage')?>"> 
	Help Page
</div>
<!-- must be present for cg values to stick on post -->
<input id="editProcesses_status" type="hidden" name="nulleditProcesses_status" value="<?php echo isset($_POST[nulleditProcesses_status])?$_POST[nulleditProcesses_status]:'editProc';?>" />
<div id="propertiesCtrl" align="right" style="width:432">
	<input name="nullSub1" type="submit" id="ctrlOK" value="&nbsp;&nbsp;&nbsp;Change..&nbsp;&nbsp;&nbsp;" onclick="form1._case.value='update';" />
<script>d.ctrlOK.disabled=true</script>&nbsp;&nbsp;

<input type="submit" id="ctrlCXL" name="nullSub2" value="&nbsp;Cancel&nbsp;" onclick="w_close();return false;" />
&nbsp;&nbsp;


</div>
</form></div></div>

footer
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
</html><?php page_end()?>