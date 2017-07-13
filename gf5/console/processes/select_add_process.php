<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
/*><script>*/
/******** NOTES ON THIS PAGE ***************** 
LAST EDIT DATE 2004-06-08 BY SAM FULLMAN	changed RelateBase over to Reasons to Believe layout.

*********************************************/
session_start();
# Identify this script
$localSys['scriptID']='mgprocesses';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='';

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

<title>Select or Add a Process</title>
<link rel="stylesheet" href="/Library/css/layers/layer_engine_v200.css" type="text/css"/>

<?php
$cg[1][CGPrefix]="seladdproc";
$cg[1][CGLayers]=Array('existingProc', 'newProc');
$cg[1][defaultLayer]="existingProc";
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

isEscapable=2;
<?php 
//this is a standard procedure for passing querystring values for talkbacks
$talkbackVars=array('cb'=>'','multiMode'=>0,'vrq'=>0,'sPrimary'=>null,'sSecondary'=>null,'sTertiary'=>null);
foreach($talkbackVars as $n=>$v){
	if(isset($$n)){
		is_numeric($$n)?$quot='':$quot="'";
		echo "var $n=".$quot.$$n.$quot.'; ';
	}else{
		if(is_null($v)){
			continue;
		}else if($v==''){
			echo "var $n=''; ";
		}else if(is_numeric($v)){
			echo "var $n=$v; ";
		}else{
			echo "var $n='".addslashes($v)."'; ";
		}
	}
}
?>
function yaf(){
	//yaf=yet another function :-)
	//set the primary and secondary values
	window.opener.pr_id=pr_id;
	window.opener.pr_description=pr_description;
	//call the process requested from the parent page
	if(typeof cb!=='undefined'){
		//note: is this order improper?  Want to close win as soon as possible.
		window.close();
		window.opener.evaluator(cb);
	} 
}
</script>
<script>
//------------- begin menu javascript -----------------------------
var menuLvl=new Array();
//type of menu matching. regexp uses rMenuMap, normal uses menuMap (see below)
var menuType='regexp'; //must be 'normal', or 'regexp'

//this menu map is the normal way, one-to-one correspondence between object and menu
var menuMap=new Array();

//this menu map uses regular expressions so that loc1,loc2,loc. all map =>amenu1
//if you use this make sure you don't get menus showing in unexpected locations!
var rMenuMap=new Array();
rMenuMap['^ro_[0-9]+_pr_[0-9]+$']='roleMenu';
rMenuMap['^pr_[0-9]$']='processMenu';

//this is the id of the div containing the menu, set initially to blank
var menuIDName='';

//Under Version 1.0 -- hidemenu-cancel (hmcxl) this field is used to prevent hidemenu from being called twice when it would cause problems
var hm_cxl=0;
var hm_cxlseq=0;
var option_hm_cxl=0;

//this determines the alignment from the source element.  Must correspond to either menuMap or rMenuMap.  Options under development are 'mouse','topleftalign','bottomleftalign', 'rightalign', and there will be more -- these are not all developed yet.
//NOTE: default is 'mouse'
var menuAlign= new Array();
menuAlign['^ro_[0-9]+_pr_[0-9]+$']='mouse';
menuAlign['^ro_[0-9]+$']='mouse';

//holds the status message during mouseovers, initially set to blank
var statusBuffer='';
//------------- end menu javascript -----------------------------
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
<div id="winTitle">Select or Add a Process</div>
<form action="manage_processes_02_exe.php" method="post" name="form1" target="w3" id="form1"><table width="450" border="0" cellspacing="0" cellpadding="0">
      <tr>
		  <!-- this is a patch, won't have a consistent white top w/o this -->
        <td width="50">
          <table class="menu" border="0" cellspacing="0" cellpadding="0">
            <tr valign="bottom"> 
              <td> 
                <div id="seladdproc_i_null" style="border-bottom:1px solid white;<?php echo cg('ib','seladdproc','null');?>"> 
                  <table border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td class="xbc_">&nbsp;</td>
                    </tr>
                  </table>
                </div>
              </td>
				  <td> 
 <div id="seladdproc_a_existingProc" style="<?php echo cg('ab','seladdproc','existingProc',1);?>"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif" /></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif" /></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="xbc_" nowrap="nowrap">Existing Processes</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
 <div id="seladdproc_i_existingProc" style="border-bottom:1px solid white;<?php echo cg('ib','seladdproc','existingProc',1);?>" onclick="hl_1('seladdproc',seladdproc,'existingProc');"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif" /></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif" /></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="bc_" nowrap="nowrap">Existing Processes</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
</td><td> 
 <div id="seladdproc_a_newProc" style="<?php echo cg('ab','seladdproc','newProc');?>"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif" /></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif" /></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="xbc_" nowrap="nowrap"><img src="/images/assets/new_folder.gif" width="18" height="17" />New Process</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
 <div id="seladdproc_i_newProc" style="border-bottom:1px solid white;<?php echo cg('ib','seladdproc','newProc');?>" onclick="hl_1('seladdproc',seladdproc,'newProc');"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif" /></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif" /></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="bc_" nowrap="nowrap"><img src="/images/assets/new_folder.gif" width="18" height="17" />New Process</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
</td>
              <td> 
                <div id="seladdproc_i_null2" style="border-bottom:1px solid white;"> 
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
<div id="seladdproc_existingProc" class="aArea" style="height:300;width:450;<?php echo cg('l','seladdproc','existingProc',1)?>"> 
	<img src="/images/assets/sample_locationbar.gif" onclick="alert('not enabled');" />
	<div id="processList" class="overflowInset1" style="height:200px;"> 
	<?php
	//this hides the headers
	$hideHeaders=1;
	include('manage_processes_01_processlist.php');
	$hideHeaders=0;
	?>
	</div>
	Process Name: <input name="pr_display" type="text" size="35" />
</div>

<div id="seladdproc_newProc" class="aArea" style="height:300;width:450;<?php echo cg('l','seladdproc','newProc')?>"> 
	Add a New Process
			   <!-- note these are set before the insert new; submissions for a new value will be overridden -->
				<input name="pr_id" type="hidden" id="pr_id" value="<?php echo $pr_id?>" />
            <input name="pr_name" type="hidden" id="pr_name" value="<?php echo htmlentities($pr_name)?>" />
            <input name="_case" type="hidden" id="_case" />
            <br />
            <br />
            <table border="0" cellspacing="0" cellpadding="2">
				<col align="right" />
				<col />
            			<tr>
            						<td>Category:</td>
            						<td><select name="pr_category" id="pr_category">
                              			<option value="Backend Highest">Backend Highest</option>
                              			<option value="Backend Administrative">Backend
                              			Administrative</option>
                              			<option value="Administrative">Administrative</option>
                              			<option value="Applicant Processes">Applicant
                              			Processes</option>
                              			</select></td>
					</tr>
            			<tr>
            						<td>Name:</td>
            						<td><input name="pr_name" type="text" id="pr_name_new" onkeyup="if(this.value.length&gt;0)form1.submitNew.disabled=false;" size="35" maxlength="80" /></td>
					</tr>
            			<tr>
            						<td>Handle:</td>
            						<td><input name="pr_handle" type="text" id="pr_handle" onkeyup="if(this.value.length&gt;0)form1.submitNew.disabled=false;" maxlength="24" /></td>
					</tr>
            			<tr valign="top">
            						<td>Description:</td>
            						<td><textarea name="pr_description" cols="30" rows="3" id="pr_description" onblur="if(this.value.length&gt;255){alert('Description can be 255 characters maximum');this.select();}" ></textarea></td>
					</tr>
            			<tr>
            						<td>URL (from root):</td>
            						<td><input name="pr_url" type="text" id="pr_url" size="35" maxlength="255" /></td>
					</tr>
            			<tr>
            						<td>Version:</td>
            						<td><input name="pr_version" type="text" id="pr_version" size="7" maxlength="4" /></td>
					</tr>
            			<tr>
            						<td height="28">Page Opens:</td>
            						<td><select name="pr_windowtype" id="select">
            						<option value="standard">in the same window            						</option>
            						<option value="properties">in a properties window            						</option>
            						<option value="balloon">in a ballon window	                              </option>
            						</select></td>
					</tr>
            			<tr>
            						<td height="28">&nbsp;</td>
            						<td><input name="submitNew" type="submit" disabled="disabled" id="submitNew" value="Add Process" onclick="form1.pr_id.value='';form1._case.value='insert';" /></td>
					</tr>
            			<tr>
            						<td height="28">&nbsp;</td>
            						<td><input name="addAfter" type="checkbox" id="addAfter" value="1" />
            									Add another process afterward</td>
					</tr>
     			</table>
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
</div>
<!-- must be present for cg values to stick on post -->
<input id="seladdproc_status" type="hidden" name="nullseladdproc_status" value="<?php echo isset($_POST[nullseladdproc_status])?$_POST[nullseladdproc_status]:'existingProc';?>" />
<div id="propertiesCtrl" align="right" style="width:450">
	<input type="submit" id="ctrlOK" name="nullSub1" value="&nbsp;&nbsp;&nbsp;OK&nbsp;&nbsp;&nbsp;" onclick="yaf();return false;" />
&nbsp;&nbsp;
<input type="submit" id="ctrlCXL" name="nullSub2" value="&nbsp;Cancel&nbsp;" onclick="window.close()" />
&nbsp;&nbsp;
<script>d.ctrlOK.disabled=true;</script>
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