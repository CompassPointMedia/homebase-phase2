<?php 
//identify this script/GUI
$localSys['scriptID']='staffroles';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';


require('../systeam/php/config.php');

require('../resources/bais_00_includes.php');

require('../systeam/php/auth_i2_v100.php');

$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Permissions Table</title>


<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />

<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
.largerTreb{
	font-size:136%; font-weight:800
	}
.fldSet1{
	padding:20px 20px 5px 10px;
	margin-left:10px;
	}
body, .objectWrapper{
	background-color:#93A2B2;
	}
#searchGradCell{
	height:<?php echo $htFix?>px;
	}
.xbc_{
	font-size:104%
	cursor:pointer;
	}
.bc_{
	font-size:104%
	cursor:pointer;
	}
.colHlt{
	background-color:CORNSILK;
	border-left:1px solid #CCC;
	border-right:1px solid #CCC;
	}
.noColHlt{
	}
.cbox{
	margin:0px;
	height:10px;
	}
.data1 thead{
	background-color:STEELBLUE;
	color:white;
	font-weight:400;
	}
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="../../site-local/local.js"></script>
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

isEscapable=1;
hl_bg['usr']='THISTLE';
hl_txt['usr']='#333';
hl_bg['role']='#BBC';
hl_txt['role']='#000';

var bufferCol='';
var bufferColor='';
var colIdx='';
function colHlt(o){
	var reg=/[a-z_]/gi
	colIdx=parseInt(o.id.replace(reg,''));
	if(bufferCol)bufferCol.className='noColHlt';
	g('col_'+colIdx).className='colHlt';
	bufferCol=g('col_'+colIdx);
}
function colHide(idx,x){
	g('col_'+idx).style.display=(x==0?'none':'block');
	y=g('hiddenCols').innerHTML;
	if(x==0){
		//add to hidden columns
		if(y=='' || y=='&nbsp;'){
			g('hiddenCols').innerHTML='Hidden Columns: <a href="#" onclick="colHide('+idx+',1);return false;">' + roleCols[idx]+ '</a>';
		}else{
			g('hiddenCols').innerHTML+=', <a href="#" onclick="colHide('+idx+',1);return false;">'+roleCols[idx]+'</a>';
		}
	}else{
		//remove from hidden columns
		eval( 'var reg=/(, )*<[^>]+>'+roleCols[idx]+'<[^>]+>/i' );
		g('hiddenCols').innerHTML=g('hiddenCols').innerHTML.replace(reg,'');
	}
}
function role_perms(mode){
}
function admin_perms(mode){
}
function role(mode){
	var roid;
	for(j in hl_grp['role']) roid=j.replace('ro_','');
	switch(true){
		case mode=='remove':
			if(confirm('Are you sure you want to delete this role?')){
				window.open('../resources/bais_01_exe.php?mode=deleteRole&ro_id='+roid,'w2');
			}
		break;
		case mode=='open':
			window.open('../roles/index.php?ro_id='+roid+'&cb=1','l2_roles','width=350,height=500,resizable,scrollbars,menubar,status');
		break;
	}
}
function admin(mode){
}
function assign_role(o,usr,roid){
	var action=(o.checked ? '1' : '0');
	window.open('../resources/bais_01_exe.php?mode=assignRole&sr_stusername='+usr+'&ro_id='+roid+'&action='+action,'w2');
}
</script>
<?php
if($tabbedMenus || true){
	?><link rel="stylesheet" href="/Library/css/layers/layer_engine_v301.css" type="text/css" /><?php
	$cg[1]['CGPrefix']="staffRoles";
	$cg[1]['CGLayers']=array('permissionstable', 'help');
	$cg[1]['defaultLayer']='permissionstable';
	$cg[1]['layerScheme']=2; //thin tabs vs old blocky tabs (1)
	$cg[1]['schemeVersion']=3.01;
	//$activeHelpSystem='3.01';
	//this will generate JavaScript, all instructions are found in this file
	?><?php
	require($_SERVER['DOCUMENT_ROOT'].'/Library/css/layers/layer_engine_v301.php');
	?><?php
}
?>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="../../resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">

<div id="headerBar1">
	<div id="btns140" style="float:right;">
	<input name="<?php echo $recordPKField?>" type="hidden" id="<?php echo $recordPKField?>" value="<?php echo $$object;?>">
	<input name="navVer" type="hidden" id="navVer" value="<?php echo $navVer?>">
	<input name="navObject" type="hidden" id="navObject" value="<?php echo $navObject?>">
	<input name="nav" type="hidden" id="nav">
	<input name="navMode" type="hidden" id="navMode" value="">
	<input name="count" type="hidden" id="count" value="<?php echo $nullCount?>">
	<input name="abs" type="hidden" id="abs" value="<?php echo $nullAbs?>">
	<input name="insertMode" type="hidden" id="insertMode" value="<?php echo $insertMode?>">
	<input name="updateMode" type="hidden" id="updateMode" value="<?php echo $updateMode?>">
	<input name="mode" type="hidden" id="mode" value="<?php echo $mode?>">
	<input name="componentID" type="hidden" id="componentID" value="<?php echo $localSys['componentID']?>">
	<?php
	if(count($_REQUEST)){
		foreach($_REQUEST as $n=>$v){
			if(substr($n,0,2)=='cb'){
				if(!$setCBPresent){
					$setCBPresent=true;
					?><!-- callback fields automatically generated --><?php
					echo "\n";
					?><input name="cbPresent" id="cbPresent" value="1" type="hidden" /><?php
					echo "\n";
				}
				if(is_array($v)){
					foreach($v as $o=>$w){
						echo "\t\t";
						?><input name="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" id="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" type="hidden" value="<?php echo htmlentities(stripslashes($w))?>" /><?php
						echo "\n";
					}
				}else{
					echo "\t\t";
					?><input name="<?php echo $n?>" id="<?php echo $n?>" type="hidden" value="<?php echo htmlentities(stripslashes($v))?>" /><?php
					echo "\n";
				}
			}
		}
	}
	?>
	</div>
	<span class="lg1">Staff Permissions Table</span>
	<br />
	Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</div>

</div>
<div id="mainBody">

<div class="objectWrapper">
	<div class="tabs">
		<table cellpadding="0" cellspacing="0"><tr>
			<td>
				<div id="staffRoles_a_permissionstable" class="ab tShow">Permissions Table</div>
				<div id="staffRoles_i_permissionstable" class="ib tHide" onclick="hl_1('staffRoles',staffRoles,'permissionstable');">Permissions Table</div>					</td><td>
				<div id="staffRoles_a_help" class="ab tHide">Help</div>
				<div id="staffRoles_i_help" class="ib tShow" onclick="hl_1('staffRoles',staffRoles,'help');">Help</div>
			</td></tr>
		</table>
	</div>
	<div id="staffRoles_permissionstable" class="aArea tShow">
		<?php
		require('./staffroles_01_staffrolepivot.php');
		?>
		<div id="hiddenCols">&nbsp;</div>
	</div>
	<div id="staffRoles_help" class="aArea tHide">
		<div style="overflow:scroll; height:350px; width:89%; background-color:#FFF;padding:12px;">
			<h3>Using Permission Tables</h3>
			
			<!--
			<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Vivamus hendrerit sagittis nulla. Duis id nulla. Fusce in augue eu purus imperdiet adipiscing. Donec auctor. Nullam ipsum orci, pharetra quis, lobortis pretium, rhoncus a, nunc. Mauris suscipit lorem a est. Vestibulum imperdiet pede tempor nunc. Nam pede. Etiam et diam id ante vestibulum sollicitudin. Cras iaculis purus ac orci. Aliquam sapien. Fusce ornare. Sed nonummy, tellus in mattis convallis, dui odio faucibus arcu, id ultricies eros nulla at augue. </p>
			<p>Quisque eget elit. Morbi pulvinar iaculis orci. Donec elementum dolor non quam rhoncus molestie. Sed lacus turpis, facilisis at, facilisis non, blandit sit amet, tellus. Pellentesque quis purus at leo tristique luctus. Duis mollis nulla nec nisl. Sed placerat lacinia pede. Donec nec arcu. Aenean eleifend imperdiet tellus. Integer fermentum eros sit amet massa. Quisque laoreet nisl quis nibh dapibus viverra. In nec elit. Fusce mi lectus, elementum eget, sodales quis, varius in, lectus. </p>
			<p>Praesent semper feugiat eros. Nullam est. Suspendisse augue. Fusce non turpis. Cras et orci. Vestibulum vel turpis. Praesent pellentesque diam at dolor. Proin pulvinar, odio eu elementum placerat, tellus arcu sollicitudin dui, at pellentesque sem massa non mauris. In hac habitasse platea dictumst. Curabitur magna. </p>
			<p>Vestibulum accumsan. Donec ut ipsum. Morbi tempor. Vestibulum viverra. Praesent suscipit, nunc dictum vulputate pellentesque, metus libero lobortis ligula, pharetra tristique ipsum turpis quis turpis. Integer a dolor. Cras placerat. Nullam velit wisi, gravida sit amet, dapibus in, varius eget, orci. Cras elementum luctus velit. Nam suscipit facilisis sem. Ut quis risus. Sed a ipsum. Nulla luctus porttitor wisi. Donec nisl. Ut ut massa. Duis at est non sapien convallis sagittis. </p>
			<p>Donec eget eros ac urna fringilla elementum. Donec euismod, pede vel venenatis dignissim, mi ligula auctor velit, in gravida purus magna non quam. Duis neque erat, luctus eu, luctus gravida, ullamcorper ultrices, turpis. Nulla id odio. Vestibulum hendrerit vulputate risus. Mauris faucibus semper metus. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec vestibulum libero et lorem. Nullam ipsum augue, consectetuer sed, tristique auctor, sodales ac, justo. Sed ultricies bibendum risus. In egestas dolor at nibh. Nulla pretium erat ut wisi.<br />
			</p>						
			-->
		</div>
	</div>	
	<input type="hidden" name="staffRoles_status" id="staffRoles_status" value="<?php echo $staffRoles_status?>" />
	<input type="submit" id="ctrlCXL" name="nullSub2" value="&nbsp;Close" onclick="window.close()">	
</div>
</div>
<div id="footer">

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
</html><?php page_end()?>