<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
/*><script>*/
/******** NOTES ON THIS PAGE ***************** 
LAST EDIT DATE 2004-06-08 BY SAM FULLMAN	changed RelateBase over to Reasons to Believe layout.

*********************************************/
session_start();
# Identify this script
$localSys['scriptID']='processes';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';

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

<title>Process Main Window</title>



<link rel="stylesheet" href="../../../site-local/undohtml2.css" type="text/css" />
<link rel="stylesheet" href="../../../site-local/gf5_simple.css" type="text/css" />
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

var thispage='template_page.php';
//------------- begin menu javascript -----------------------------
var menuLvl=new Array();
//type of menu matching. regexp uses rMenuMap, normal uses menuMap (see below)
var menuType='regexp'; //must be 'normal', or 'regexp'

//this menu map is the normal way, one-to-one correspondence between object and menu
var menuMap=new Array();

//this menu map uses regular expressions so that loc1,loc2,loc. all map =>amenu1
//if you use this make sure you don't get menus showing in unexpected locations!
var rMenuMap=new Array();
rMenuMap['^pr_[0-9]+$']='processRowContext';
rMenuMap['^co_[0-9]$']='compRowContext';

//this is the id of the div containing the menu, set initially to blank
var menuIDName='';

//Under Version 1.0 -- hidemenu-cancel (hmcxl) this field is used to prevent hidemenu from being called twice when it would cause problems
var hm_cxl=0;
var hm_cxlseq=0;
var option_hm_cxl=0;

//this determines the alignment from the source element.  Must correspond to either menuMap or rMenuMap.  Options under development are 'mouse','topleftalign','bottomleftalign', 'rightalign', and there will be more -- these are not all developed yet.
//NOTE: default is 'mouse'
var menuAlign= new Array();
menuAlign['^pr_[0-9]+$']='mouse';
menuAlign['^co_[0-9]$']='mouse';

//holds the status message during mouseovers, initially set to blank
var statusBuffer='';
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

hl_grp[2]=new Array();
hl_grp[2]['id']='';
sel_id[2]='';

//key data
var pr_id='';
var pr_description='';
var co_id='';
var co_description='';
function highlight_select(x, grp){
	if(cxl_hlt==1){cxl_hlt=0;return false;}
	if(typeof grp=='undefined')var grp=1;
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
	var reg1=/^pr_/;
	if(x.id.match(reg1)){
		pr_id=x.id.replace(reg1,'');
	}
	var reg2=/^co_/;
	if(x.id.match(reg2)){
		co_id=x.id.replace(reg2,'');
	}
	
}
function insert_comp(){
}
function edit_comp(){
	if(co_id!==''){
		var l2_proc=return ow('edit_component.php?co_id='+co_id+'&cb=refresh&vrq=0','l2_proc','450,550');
	}else{
		alert('Please select a component first');
		return false;
	}
}
function delete_comp(){

}
function insert_proc(){

}
function edit_proc(){
	if(pr_id!==''){
		var l2_proc=return ow('edit_process.php?pr_id='+pr_id+'&cb=refresh&vrq=0','l2_proc','450,550');
	}else{
		alert('Please select a process first');
		return false;
	}
}
function delete_proc(){

}
function properties(){


}

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
<p>
<script>
//for context menus
if (document.all && window.print) {
	document.oncontextmenu = showmenuie5;
	document.body.onclick= hidemenuie5;
}
</script>
<?php
$hideHeaders=1;
include('manage_processes_03_process_comp.php');
$hideHeaders=0;

?>
</p>
<div class="controlSection" id="ctrlSection" style="display:none;"> <iframe name="w1" id="w1"></iframe> 
  <iframe name="w2" id="w2"></iframe> <iframe name="w3" id="w3"></iframe> </div>
<!-- Process Row Context; generated 2004-06-28 12:41 In the main area, process is the top of the tree -->
<div id="processRowContext" class="menuskin1" onmouseover="highlightie5()" onmouseout="lowlightie5()" onclick="executemenuie5()">
<div class="menuitems" command="edit_proc()" status="Edit Highlighted Process">Edit Process</div>
<div class="menuitems" command="delete_proc()" status="Delete Highlighted Process and all components">Delete Process</div>
<hr class="mhr"/>
<div class="menuitems" command="insert_proc()" status="Add (register) a new process">New Process..</div>
<div class="menuitems" command="insert_comp()" status="Add (register) a new component">New Component..</div>
<hr class="mhr"/>
<div class="menuitems" command="properties()" status="Properties of selected process">Properties</div>
</div>
<!-- Component Row Context; generated 2004-06-28 12:41 Properties and methods for component row -->
<div id="compRowContext" class="menuskin1" onmouseover="highlightie5()" onmouseout="lowlightie5()" onclick="executemenuie5()">
<div class="menuitems" command="delete_comp()" status="Remove the highlighted component">Remove Component</div>
<div class="menuitems" command="edit_comp()" status="Edit highlighted component">Edit Component</div>
<hr class="mhr"/>
<div class="menuitems" command="properties()" status="Properties of selected component">Properties</div>
</div>
                			</div>
	<div id="footer">




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