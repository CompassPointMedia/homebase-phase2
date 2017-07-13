<?php
/******** --------------------------------------------------------
2004-06-15 by Sam
This is the first page to deal with hideHeaders systematically.
If a page is standalone we'll need to connect and show headers, however if the page is text in another page, we do not show <html>, <head>, etc. nor include javascript or css because it would (should) be found in the parent.
The system below is pretty hacker-proof because trying to call page.php?hideHeaders=1 would shut down the file, and calling page.php?hideHeaders=0 would require authorization and a connection (which is done in the parent if it's an include).

-------------------------------------------------------- *******/
if($hideHeaders){
	substr(__FILE__,-(strlen($_SERVER['PHP_SELF'])))==$_SERVER['PHP_SELF']? exit('-include page locked'):'';
}
if(!$hideHeaders){
	//begin session and identify script, include main configs
	//note this is a weakness because this script will work a process.  It has its own id, the parent script does too -- only one can be present at a time. 2004-06-16, I made the name the same for now, but note the componentID
	session_start();
	$localSys['scriptID']='mgroles';
	$localSys['scriptVersion']='4.0';
$localSys['componentID']='01';
	require('../systeam/php/config.php');
	//include authorization here	
	require('../../console/systeam/php/auth_i2_v100.php');
	//include needed files here
	
	//connect -- e.g. no data pulled if we try to hack the page
if(!$roleAccessPresent){
	?><script>alert('You do not have permission to do this task');</script><?php
	exit('This page allows access through a specific role only.  Please see your administrator');
}
	
}

if(!$hideHeaders){
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Process List</title>

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

var ownPage=1;
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
</head>
<body><?php }else{
	?><script>var ownPage=0;</script><?php }?>
<script>
//for context menus
if (document.all && window.print) {
	document.oncontextmenu = showmenuie5;
	document.body.onclick= hidemenuie5;
}
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
//key data
var pr_id='';
var pr_description='';
function highlight_select(x, grp){
	if(cxl_hlt==1){cxl_hlt=0;return false;}
	if(typeof grp=='undefined')grp=1;
	if(hl_grp[grp]['id']!=='' && hl_grp[grp]['id']!==null){
		//restore original color schema
		eval( hl_grp[grp]['id']+'.style.backgroundColor="'+hl_grp[grp]['bg']+'";' );
		eval( hl_grp[grp]['id']+'.style.color="'+hl_grp[grp]['color']+'";' );
	}
	//buffer selected row schema
	hl_grp[grp]['id']=x.id;
	hl_grp[grp]['color']=x.style.color;
	hl_grp[grp]['bg']=x.style.backgroundColor;
	eval( hl_grp[grp]['id']+'.style.backgroundColor="highlight";' );
	eval( hl_grp[grp]['id']+'.style.color="#FFFFFF";' );
	//enter the current selected item
	sel_id[grp]=x;
	
	//custom coding here
	if(!ownPage){
		var reg=/[a-z_]*/gi;
		pr_id=form1.pr_id.value=parseInt(x.id.replace(reg,''));
		pr_description=form1.pr_name.value=form1.pr_display.value=x.label;
		d.ctrlOK.disabled=false;
	}
}
</script>
<style>
.listImage1{
	list-style-image:url(/DynamicForms/images/file_icon_gen.gif);
	height:15px;
}
</style>
<?php
$sql="SELECT 
*
FROM bais_roles a LEFT JOIN bais_RolesProcesses b
ON ro_id=rp_roid 
LEFT JOIN bais_processes c
ON rp_prid = pr_id
WHERE 1
ORDER BY ro_name, pr_name";
$result=mysql_query($sql) or die(mysqli_error());
$buffer='';
/****
Table layout library item 02
This is a list of processes and the objective is to mirror a file list in appearance, clicking a row highlights and selects the object, and double clicking selects the object and completes a asecondary process in this case.
There are two key parameters, the primary key of the process (file selected), and its plain english name as a secondary.
****/

//
$sql="SELECT * FROM bais_processes ORDER BY pr_name ASC";
$result=mysql_query($sql) or die(mysqli_error());

//NOTE that in some cases the sorting would need to be handled by other than SQL and ^> we should work with arrays in the future
while($r=mysqli_fetch_array($result)){
	extract($r);
	$pl++;
	if($pl==1){
		?><ul id="pr_list"><?php
	}
	//determine image icon and sorting order
	?><li id="pr_<?php echo $pr_id?>" label="<?php echo htmlentities($pr_name)?>" onClick="highlight_select(this);" class="listImage1"><?php echo $pr_name?></li><?
}
//close list out
if($pl>0){?></ul><?php }
?>
<?php if(!$hideHeaders){?></body>
</html><?php }?>