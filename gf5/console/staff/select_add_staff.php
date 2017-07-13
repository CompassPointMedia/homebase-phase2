<?php
//identify this script/GUI
$localSys['scriptID']='ecommerce';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';

require('../systeam/php/config.php');

require('../resources/bais_00_includes.php');

require('../systeam/php/auth_i2_v100.php');

$hideCtrlSection=false;


//--------------------------------------------------
$db_cnx=mysqli_connect($MASTER_HOSTNAME, $MASTER_USERNAME, $MASTER_PASSWORD);
mysqli_select_db($db_cnx, $MASTER_DATABASE);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $AcctCompanyName?> :: Select or Add Staff</title>
<link rel="stylesheet" href="/Library/css/layers/layer_engine_v200.css" type="text/css"/>

<?php
$cg[1][CGPrefix]="seladdstaff";
$cg[1][CGLayers]=Array('existingStaff', 'newStaff');
$cg[1][defaultLayer]="existingStaff";
$cg[1][layerScheme]=1;
$cg[1][schemeVersion]=2.1;
//this will generate JavaScript, all instructions are found in this file
require($_SERVER['DOCUMENT_ROOT'] . "/Library/css/layers/layer_engine_v201.php");
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
function reset_password(){
	var reg =/^u_/;
	var un_username=(sel_id[1].id.replace(reg,''));
	if( (x=prompt('Enter Password','')) == (y=prompt('Confirm Password',''))){
		return ow('manage_staff_02_exe.php?_case=resetpassword&un_username='+un_username+'&string='+x,'w3');
	}else	alert("Passwords did not match");
	return false;
}
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
	//------------------ not used here ------------------------
	//call the process requested from the parent page
	if(typeof cb!=='undefined'){
		//note: is this order improper?  Want to close win as soon as possible.
		//window.close();
		alert(cb);
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
rMenuMap['^staffOptions']='mainStaffOptionsMenu';
rMenuMap['^u_[a-z0-9]+$']='subStaffOptionsMenu';

//this is the id of the div containing the menu, set initially to blank
var menuIDName='';

//Under Version 1.0 -- hidemenu-cancel (hmcxl) this field is used to prevent hidemenu from being called twice when it would cause problems
var hm_cxl=0;
var hm_cxlseq=0;
var option_hm_cxl=0;
//this determines the alignment from the source element.  Must correspond to either menuMap or rMenuMap.  Options under development are 'mouse','topleftalign','bottomleftalign', 'rightalign', and there will be more -- these are not all developed yet.
//NOTE: default is 'mouse'
var menuAlign= new Array();
menuAlign['^staffOptions']='bottomleftalign';
menuAlign['^u_[a-z0-9]+$']='mouse';

//holds the status message during mouseovers, initially set to blank
var statusBuffer='';
var ownPage=1;
//------------- end menu javascript -----------------------------
</script>
<script>
var curGrp='hl_grp';
var cxl_hlt=0;
var hl_grp=new Array();
var sel_id=new Array();
//each of these groups should also be declared
hl_grp[1]=new Array();
hl_grp[1]['id']='';
sel_id[1]='';
//key data, initial values
var system_username='';
var system_fullname='';
function highlight_select(x, grp){
	if(cxl_hlt==1){cxl_hlt=0;return false;}
	if(typeof grp=='undefined')grp=1;
	try{
		if(hl_grp[grp]['id']!=='' && hl_grp[grp]['id']!==null){
			//restore original color schema
			eval( hl_grp[grp]['id']+'.style.backgroundColor="'+hl_grp[grp]['bg']+'";' );
			eval( hl_grp[grp]['id']+'.style.color="'+hl_grp[grp]['color']+'";' );
		}
	}catch(e){
		//not developed
	}
	//buffer selected row schema -- 
	if(x!==''){
		hl_grp[grp]['id']=x.id;
		hl_grp[grp]['color']=x.style.color;
		hl_grp[grp]['bg']=x.style.backgroundColor;
		eval( hl_grp[grp]['id']+'.style.backgroundColor="highlight";' );
		eval( hl_grp[grp]['id']+'.style.color="#FFFFFF";' );
		//enter the current selected item
		sel_id[grp]=x;
	}
	
	//custom coding here
	if(x!==''){
		var reg=/^u_/;
		system_username=x.id.replace(reg,'');
		system_fullname=x.label;
		d.staffOptions.disabled=false;
		d.ctrlOK.disabled=false;
	}else{
		//call with a blank value, clear primary values
		system_username='';
		system_fullname='';
	}
}
function remove_from_usage(){
	if(system_username==''){
		alert('Please select a user first');
		return false;
	}else if(confirm(system_fullname+' ('+system_username+')\n\nThis will revoke all privileges, and the Staff member will no longer have access to administrative features.  Continue?')){
		w3.location='manage_staff_02_exe.php?_case=removefromusage&st_unusername='+system_username;
	}
	return false;
}
function remove_entirely(){
	if(system_username==''){
		alert('Please select a user first');
		return false;
	}else if(confirm(system_fullname+' ('+system_username+')\n\nThis will revoke all privileges AND remove the Staff member from the Admin Database.\nContinue?')){
		w3.location='manage_staff_02_exe.php?_case=remove&st_unusername='+system_username;
	}
	return false;
}
function install_usage(){
	if(system_username==''){
		alert('Please select a user first');
		return false;
	}else if(true){
		w3.location='manage_staff_02_exe.php?_case=installusage&st_unusername='+system_username;
	}
	return false;
}
</script>



<link rel="stylesheet" type="text/css" href="../../../site-local/undohtml2.css" />
<link rel="stylesheet" type="text/css" href="../../../site-local/smaac_simple.css" />
<style type="text/css">
/* local CSS styles */
</style>

<script language="javascript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="../../../site-local/local.js"></script>
<script language="javascript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="javascript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="javascript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="javascript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="javascript" type="text/javascript">
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
<div id="mainBody"><div id="mainBody">
<script>
//for context menus, MUST be declared after the <body> tag
if (document.all && window.print) {
	document.oncontextmenu = showmenuie5;
	document.body.onclick= hidemenuie5;
}
</script>
<div class="objectWrapper">
<div id="winTitle">Select or Add Staff</div>
<form action="manage_staff_02_exe.php" method="post" name="form1" target="w3">
			<table width="550" border="0" cellspacing="0" cellpadding="0">
      <tr> 
		  <!-- this is a patch, won't have a consistent white top w/o this -->
        <td width="50">
          <table class="menu" border="0" cellspacing="0" cellpadding="0">
            <tr valign="bottom"> 
              <td> 
                <div id="seladdstaff_i_null" style="border-bottom:1px solid white;<?php echo cg('ib','seladdstaff','null');?>"> 
                  <table border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td class="xbc_">&nbsp;</td>
                    </tr>
                  </table>
                </div>
              </td>
				  <td> 
 <div id="seladdstaff_a_existingStaff" style="<?php echo cg('ab','seladdstaff','existingStaff',1);?>"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif"></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif"></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="xbc_" nowrap>Existing Staff</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
 <div id="seladdstaff_i_existingStaff" style="border-bottom:1px solid white;<?php echo cg('ib','seladdstaff','existingStaff',1);?>" onclick="hl_1('seladdstaff',seladdstaff,'existingStaff');"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif"></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif"></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="bc_" nowrap>Existing Staff</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
</td><td> 
 <div id="seladdstaff_a_newStaff" style="<?php echo cg('ab','seladdstaff','newStaff');?>"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif"></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif"></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="xbc_" nowrap>New Staff</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
 <div id="seladdstaff_i_newStaff" style="border-bottom:1px solid white;<?php echo cg('ib','seladdstaff','newStaff');?>" onclick="hl_1('seladdstaff',seladdstaff,'newStaff');"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif"></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif"></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="bc_" nowrap>New Staff</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
</td>
              <td> 
                <div id="seladdstaff_i_null2" style="border-bottom:1px solid white;"> 
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
<div id="seladdstaff_existingStaff" class="aArea" style="height:225;width:550;<?php echo cg('l','seladdstaff','existingStaff',1)?>"> 
	<input type="button" id="staffOptions" value="Staff Options.." style=margin-bottom:5px onclick="hm_cxlseq=2;document.oncontextmenu();return false;" >
	<div id="processList" class="overflowInset1" style="height:200px;"> 
	<?php
	//this hides the headers
	$hideHeaders=1;
	include('manage_staff_01_stafflist.php');
	$hideHeaders=0;
	?>
	</div>
</div>

<div id="seladdstaff_newStaff" class="aArea" style="height:225;width:550;<?php echo cg('l','seladdstaff','newStaff')?>"> 
	      <p>Add New Staff
	      			<!-- note these are set before the insert new; submissions for a new value will be overridden -->
                  <input name="_case" type="hidden">
	      </p>
	      <table border="0" cellspacing="0" cellpadding="2">
         			<col align="right">
         			<col>
         			<tr>
         						<td><div align="right">
     									<input name="grantUsage" type="checkbox" value="1" checked>
  									</div></td>
         						<td nowrap>Grant Access to the Administration System</td>
					</tr>
         			<tr>
         						<td>First Name:</td>
     						<td><input name="un_firstname" type="text" onKeyUp="if(this.value.length>0){form1.submitNew.disabled=false;highlight_select('');}" size="20" maxlength="30"></td>
					</tr>
         			<tr>
                  			<td>Last Name:</td>
                  			<td><input name="un_lastname" type="text" onKeyUp="if(this.value.length>0){form1.submitNew.disabled=false;highlight_select('');}" size="20" maxlength="30">                  						</td>
					</tr>
         			<tr>
         						<td>Email:</td>
         						<td><input name="st_email" type="text" onKeyUp="if(this.value.length>0){form1.submitNew.disabled=false;highlight_select('');}" size="28" maxlength="85">							</td>
					</tr>
         			<tr>
         						<td>User Name:</td>
         						<td><input name="st_unusername" type="text" size="18" maxlength="20">							</td>
					</tr>
         			<tr>
         						<td><p>Password:</p>							</td>
         						<td><input name="un_password" type="password" size="18" maxlength="30">							</td>
					</tr>
         			<tr>
         						<td height="28">&nbsp;</td>
         						<td><input name="submitNew" type="submit" disabled="true" value="Add Staff" onclick="form1._case.value='insert';system_username=system_fullname='';">							</td>
					</tr>
         			<tr>
         						<td height="28">&nbsp;</td>
         						<td><input name="addAfter" type="checkbox" value="1">
									Add another staff member afterward</td>
					</tr>
         			</table>
	      <p>&nbsp;</p>
</div>
<!-- must be present for cg values to stick on post -->
<input id="seladdstaff_status" type="hidden" name="nullseladdstaff_status" value="<?php echo isset($_POST[nullseladdstaff_status])?$_POST[nullseladdstaff_status]:'existingStaff';?>">
<div id="propertiesCtrl" align="right" style="width:550">
<input type="submit" id="ctrlOK" name="nullSub1" disabled value="&nbsp;&nbsp;&nbsp;Select Staff&nbsp;&nbsp;&nbsp;" onclick="yaf();return false;">&nbsp;&nbsp;
<input type="submit" id="ctrlCXL" name="nullSub2" value="&nbsp;Cancel&nbsp;" onclick="window.close();return false;">&nbsp;&nbsp;
</div>
</form></div>
<!-- mainStaffOptionsMenu-->
<div id="mainStaffOptionsMenu" class="menuskin1" onMouseOver="highlightie5()" onMouseOut="lowlightie5()" onclick="executemenuie5();" precalculate="alert();"> 
<div class="menuitems" command="install_usage()" status="Grant administrative usage">Grant Usage</div>
<div class="menuitems" command="remove_from_usage()" status="Remove from administrative usage">Remove From Usage</div>
<div class="menuitems" status="Remove Staff Member entirely" command="alert('This feature only authorized for users sfullman and glenz');remove_entirely()">Remove Entirely</div>
<div class="menuitems" status="Add new Staff Member" command="hl_1('seladdstaff',seladdstaff,'newStaff');">New Staff..</div>
<hr class="mhr"/>
<div class="menuitems" status="Information on Staff Member Record" command="alert('Not developed');">Properties</div>
</div>
<!-- subStaffOptionsMenu-->
<div id="subStaffOptionsMenu" class="menuskin1" onMouseOver="highlightie5()" onMouseOut="lowlightie5()" onclick="executemenuie5();"> 
<div class="menuitems" command="install_usage()" status="Grant administrative usage">Grant Usage</div>
<div class="menuitems" command="remove_from_usage()" status="Remove from administrative usage">Remove From Usage</div>
<div class="menuitems" status="Reset password for this user (be sure to notify user)" command="reset_password()">Reset Password</div>
<div class="menuitems" status="Remove Staff Member entirely" command="alert('This feature only authorized for users sfullman and glenz');remove_entirely()">Remove Entirely</div>
<hr class="mhr"/>
<div class="menuitems" status="Information on Staff Member Record" command="alert('Not developed');">Properties</div>
</div>
<!-- Process Menu -->
<div id="processMenu" class="menuskin1" onMouseOver="highlightie5()" onMouseOut="lowlightie5()" onclick="executemenuie5();"> 
<div class="menuitems" command="alert('undeveloped')" status="Information about Process">Info..</div>
<hr class="mhr">
<div class="menuitems" status="Remove this process from the selected role" command="hide_tr(ro_id,pr_id);return ow('../processes/manage_processes_02_exe.php?ro_id='+ro_id+'&pr_id='+pr_id+'&_case=removeroleprocess','w2');">Remove Process</div>
<div class="menuitems" status="Go to selected process" command="alert('undeveloped');">Go to Process</div>
</div>
</div>

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