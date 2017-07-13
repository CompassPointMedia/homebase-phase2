<?php
/******** NOTES ON THIS PAGE ***************** 
LAST EDIT DATE 2004-06-08 BY SAM FULLMAN	changed RelateBase over to Reasons to Believe layout.
This is the page which 
1. you must change sys.scriptID to the relevant value
2. eventually I want to have a single security include file
3. there is no layout control needed for this page, when created it will be named properties_i1_v100.css
4. make sure the proper .js pages are included or excluded
*********************************************/
//identify this script/GUI
$localSys['scriptID']='staffroles';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';

require('../systeam/php/config.php');

require('../resources/bais_00_includes.php');

require('../systeam/php/auth_i2_v100.php');

$hideCtrlSection=false;


//--------------------------------------------------

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
 
<title>Add a New Role</title>
<link rel="stylesheet" href="/Library/css/layers/layer_engine_v200.css" type="text/css"/>

<?php
$cg[1][CGPrefix]="addnewrole";
$cg[1][CGLayers]=Array('roleNew', 'help');
$cg[1][defaultLayer]="roleNew";
$cg[1][layerScheme]=1;
$cg[1][schemeVersion]=2.1;
$activeHelpSystem=true;
//this will generate JavaScript, all instructions are found in this file
require($_SERVER['DOCUMENT_ROOT'].'/Library/css/layers/layer_engine_v211.php');
?>
<link rel="stylesheet" href="/Library/css/layers/layer_engine_v200.css" type="text/css"/>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="../../../site-local/local.js"></script>
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
?></script>



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
<div id="mainBody"><div id="mainBody"><div class="objectWrapper">
<div id="winTitle">Add a New Administrative Role</div>
<form name="form1" target="w3" action="roles_01_exe.php" method="post"><table width="275" border="0" cellspacing="0" cellpadding="0">
      <tr> 
		  <!-- this is a patch, won't have a consistent white top w/o this -->
        <td width="50">
          <table class="menu" border="0" cellspacing="0" cellpadding="0">
            <tr valign="bottom"> 
              <td> 
                <div id="addnewrole_i_null" style="border-bottom:1px solid white;<?php echo cg('ib','addnewrole','null');?>"> 
                  <table border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td class="xbc_">&nbsp;</td>
                    </tr>
                  </table>
                </div>
              </td>
				  <td> 
 <div id="addnewrole_a_roleNew" style="<?php echo cg('ab','addnewrole','roleNew',1);?>"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif"></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif"></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="xbc_" nowrap>New Role</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
 <div id="addnewrole_i_roleNew" style="border-bottom:1px solid white;<?php echo cg('ib','addnewrole','roleNew',1);?>" onclick="hl_1('addnewrole',addnewrole,'roleNew');"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif"></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif"></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="bc_" nowrap>New Role</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
</td><td> 
 <div id="addnewrole_a_help" style="<?php echo cg('ab','addnewrole','help');?>"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif"></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif"></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="xbc_" nowrap>Help</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
 <div id="addnewrole_i_help" style="border-bottom:1px solid white;<?php echo cg('ib','addnewrole','help');?>" onclick="hl_1('addnewrole',addnewrole,'help');"> 
	<table border="0" cellspacing="0" cellpadding="0">
	  <tr> 
		 <td class="bul_"><img src="/images/b/uli.gif"></td>
		 <td class="bt_"></td>
		 <td class="bur_"><img src="/images/b/uri.gif"></td>
	  </tr>
	  <tr> 
		 <td class="bl_"></td>
		 <td class="bc_" nowrap>Help</td>
		 <td class="br_"></td>
	  </tr>
	</table>
 </div>
</td>
              <td> 
                <div id="addnewrole_i_null2" style="border-bottom:1px solid white;"> 
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
<div id="addnewrole_roleNew" class="aArea" style="height:200;width:275;<?php echo cg('l','addnewrole','roleNew',1)?>"> 
	<table border="0" cellspacing="0" cellpadding="2">
            			<tr>
            						<td>Name:<br />        									<input name="ro_name" type="text" id="ro_name" onKeyUp="if(this.value.length>0){form1.ctrlOK.disabled=false;}" size="25" maxlength="40">
            									</td>
					</tr>
            			<tr valign="top">
            						<td>Description:<br />        									<textarea name="ro_description" cols="25" rows="5" id="ro_description" onKeyUp="if(this.value.length&gt;0){form1.ctrlOK.disabled=false;}"></textarea>
            									</td>
					</tr>
            			<tr>
            						<td nowrap>Rank this role below:
										<div id="refranking"><select name="refrank" id="refrank"><option value=''>-- select --<?php
										$sql="SELECT ro_rank, ro_name FROM bais_roles ORDER BY ro_rank ASC";
										$result=q($sql);
										while($r=mysqli_fetch_array($result,$db_cnx)){
											extract($r);
											echo "<option value=$ro_rank>".htmlentities($ro_name);
										}
										?></select></div>
</td>
					</tr>
            			<tr>
            						<td><input name="rankAtTop" type="checkbox" id="rankAtTop" value="1" onclick="if(this.checked){form1.refrank.disabled=true;}else{form1.refrank.disabled=false;}">
     									Rank at top</td>
					</tr>
            			<tr>
        						<td height="28"><input name="addAfter" type="checkbox" value="1">
  									Add another role afterward
										<input name="_case" type="hidden" id="_case" value="insert"></td>
					</tr>
            			</table>
</div>

<div id="addnewrole_help" class="aArea" style="height:200;width:275;<?php echo cg('l','addnewrole','help')?>"> 
	Help
</div>
<!-- must be present for cg values to stick on post -->
<input id="addnewrole_status" type="hidden" name="nulladdnewrole_status" value="<?php echo isset($_POST[nulladdnewrole_status])?$_POST[nulladdnewrole_status]:'roleNew';?>">
<div id="propertiesCtrl" align="right" style="width:275">
<input type="submit" id="ctrlOK" name="nullSub1" disabled value="&nbsp;&nbsp;&nbsp;OK&nbsp;&nbsp;&nbsp;">&nbsp;&nbsp;
<input type="submit" id="ctrlCXL" name="nullSub2" value="&nbsp;Cancel&nbsp;" onclick="if(ctrlOK.disabled==true || confirm('You have started adding a role.  Continue and lose changes?')){window.close();}">&nbsp;&nbsp;
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