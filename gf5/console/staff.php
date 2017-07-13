<?php 
/*
Created 2010-11-24 SF

*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

$sql="SELECT un_username FROM bais_universal bu,
bais_staff bs LEFT JOIN bais_StaffRoles sr 
ON st_unusername=sr_stusername AND sr_roid IN (".ROLE_ADMIN.','.ROLE_MANAGER.','.ROLE_AGENT.")
WHERE
un_username=st_unusername AND
st_unusername=sr_stusername AND
sr_roid IN(".ROLE_ADMIN.','.ROLE_MANAGER.','.ROLE_AGENT.")
GROUP BY un_username
ORDER BY un_lastname, un_firstname";

//------------------------ Navbuttons head coding v1.41 -----------------------------
//change these first vars and the queries for each instance
$object='un_username';
$recordPKField='ID'; //primary key field
$navObject='un_username';
$updateMode='updateStaff';
$insertMode='insertStaff';
$insertType=1; //1=Save&New and Save&Close; 2 = Save and Save&New
#set these to 'disabled' if desired
$saveInitiallyDisabled='';
$saveAndNewInitiallyDisabled='';
$saveAndCloseInitiallyDisabled='';
//v1.4 change - some information about the coding
$navVer='1.41';
//v1.3 change - declare this function if you need to add parameters to the query string
$navQueryFunction=''; //nav_query_add()
//v1.3 change - deny transiting from Next to New mode (shutting off ability to insert)
$denyNextToNew=false;
//declare the query to get the idSet or subset, ordered by desired sort order - note that if you're using quasi resources, then be sure and filter them out.
$ids=q($sql,O_COL);
/*
(another good example more complex)
$ids=q("SELECT ID FROM `$cc`.finan_invoices WHERE Accounts_ID='$Accounts_ID' ORDER BY InvoiceDate, CAST(InvoiceNumber AS UNSIGNED)",O_COL);
*/

$nullCount=count($ids);
$j=0;
if($nullCount){
	foreach($ids as $v){
		$j++; //starting value=1
		if($j==$abs+$nav || (isset($$object) && $$object==$v)){
			$nullAbs=$j;
			//get actual primary key if passage by abs+nav
			if(!$$object) $$object=$v;
			break;
		}
	}
}else{
	$nullAbs=1;
}
//note the coding to on ResourceToken - this will allow a submitted page to come up again if the user Refreshes the browser
if(strlen($$object)){
	//get the record for the object
	if($a=q("SELECT
		un_id,
		un_createdate,
		un_creator,
		un_editdate,
		un_editor,
		un_username,
		un_firstname AS FirstName,
		un_lastname AS LastName,
		un_middlename AS MiddleName,
		un_email AS Email,
		Address,
		City,
		State,
		Zip,
		Country,
		Phone,
		WorkPhone,
		PagerVoice,
		Cell,
		Gender,
		Race,
		BirthDate,
		SocSecurityNumber,
		MisctextStaffnotes,
		GLF_Recruiter, GLF_TransactionFee, GLF_EOFee,
		bais_staff.st_status AS HasUsage,
		bais_staff.st_active,
		st_hiredate,
		st_dischargedate,
		st_dischargereason
		FROM
		bais_universal, bais_staff
		WHERE
		un_username='$un_username' AND
		un_username=st_unusername",O_ROW)){
		$mode=$updateMode;
		@extract($a);
	}else{
		//object may have been deleted by another user, least perplexing approach is to present insert mode
		$mode=$insertMode;
		unset($$object);
		$nullAbs=$nullCount+1;
	}
}else{
	$mode=$insertMode;
	$nullAbs=$nullCount+1; //where we actually are right then
}
//--------------------------- end coding --------------------------------

foreach($blankFills as $n=>$v){
	if(!trim($a[$n]) && !isset($$n)){
		$a[$n]=h($v);
	}
}
@extract($a);
$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Manage Staff Records';?></title>



<link rel="stylesheet" type="text/css" href="/site-local/undohtml2.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
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
var isEscapable=0;
var isDeletable=0;
var isModal=0;
var talks=0; //whether this page broadcasts overall state changes which other pages listen for
var listens=0;
</script>

<style type="text/css">
</style>

<?php
$tabPrefix='staffMain';
$cg[$tabPrefix]['CGLayers']=array(
    'Key Info'    =>'stKeyInfo',
    'Access'    =>'stAccess',
	'Log History'=>'stHistory'
);

if($x=$setTab[$tabPrefix] && in_array($setTab[$tabPrefix], $cg[$tabPrefix]['CGLayers'])){
	$cg[$tabPrefix]['defaultLayer']=$x;
}else if($x=$_COOKIE['tabs'.$tabPrefix]){
	$cg[$tabPrefix]['defaultLayer']=$x;
}else if(!isset($cg[$tabPrefix]['defaultLayer'])){
    $cg[$tabPrefix]['defaultLayer']=current($cg[$tabPrefix]['CGLayers']);
}
$cg[$tabPrefix]['layerScheme']=2; //thin tabs vs old Microsoft tabs
$cg[$tabPrefix]['schemeVersion']=3.01;
$layerMinHeight=200;
?>

</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
<div id="btns140" style="float:right;">
<!--
Navbuttons version 1.41. Last edited 2008-01-21.
This button set came from devteam/php/snippets
Now used in a bunch of RelateBase interfaces and also client components. Useful for interfaces where sub-records are present and being worked on.
-->
<?php
//Things to do to install this button set:
#1. install contents of this div tag (btns140)
#2. the coding above needs to go in the head of the document, change as needed to connect to the specific table(s) or get the resource in a different way
#3. must declare the following vars in javascript:
// var thispage='whatever.php';
// var thisfolder='myfolder';
// var count='[php:echo $nullCount]';
// var ab='[php:echo $nullAbs]';
#4. need js functions focus_nav() and focus_nav_cxl() in place
?>
<input id="Previous" type="button" name="Submit" value="Previous" onclick="focus_nav(-1, '<?php echo ($mode==$insertMode?'insert':'update')?>', <?php echo $mode==$insertMode?1:0?>, 0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs<=1?'disabled':''?> >
<?php
//Handle display of all buttons besides the Previous button
if($mode==$insertMode){
	if($insertType==2 /** advanced mode **/){
		//save
		?><input id="Save" type="button" name="Save" value="Save" onclick="focus_nav(0,'insert',1,2<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveInitiallyDisabled?>><?php
	}
	//save and new - common to both modes
	?><input id="SaveAndNew" type="button" name="SaveAndNew" value="Save &amp; New" onclick="focus_nav(0,'insert', 1,1<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndNewInitiallyDisabled?>><?php
	if($insertType==1 /** basic mode **/){
		//save and close
		?><input id="SaveAndClose" type="button" name="SaveAndClose" value="Save &amp; Close" onclick="focus_nav(0,'insert', 1,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $saveAndCloseInitiallyDisabled?>><?php
	}
	?><input id="CancelInsert" type="button" name="CancelInsert" value="Cancel" onclick="focus_nav_cxl('insert');"><?php
}else{
	//OK, and appropriate [next] button
	?><input id="OK" type="button" name="ActionOK" value="OK" onclick="focus_nav(0,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);">
	<input id="Next" type="button" name="Next" value="Next" onclick="focus_nav(1,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ? ", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs>$nullCount || ($denyNextToNew && $nullAbs==$nullCount) ?'disabled':''?>><?php
}
// *note that we could go back to the same page the 'New Record' click appeared on, but there's major issues programmatically on whether it would shift becuase of the placement of the new record.
// *note that the primary key field is now included here to save time
?>
<input name="ID" type="hidden" id="ID" value="<?php echo $ID?>">
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
<input name="submode" type="hidden" id="submode">
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
					?><input name="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" id="<?php echo $n?>[<?php echo is_numeric($o)? '': $o?>]" type="hidden" value="<?php echo stripslashes($w);?>" /><?php
					echo "\n";
				}
			}else{
				echo "\t\t";
				?><input name="<?php echo $n?>" id="<?php echo $n?>" type="hidden" value="<?php echo stripslashes($v);?>" /><?php
				echo "\n";
			}
		}
	}
}
?><!-- end navbuttons 1.41 --></div>
<h3>Update Staff Information</h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>
</div>

</div>
<div id="mainBody">





<style type="text/css">
#imgWidget{
	cursor:pointer;
	position:relative;
	}
#imgWidgetOptions{
	position:absolute;
	background-color:papayawhip;
	left:63px;
	top:0px;
	width:200px;
	float:left;
	}
.iwnormal{
	cursor:pointer;
	background-color:silver;
	}
.iwhl{
	cursor:pointer;
	background-color:gold;
	}
</style>
<?php
$Tree_ID=q("SELECT c.Tree_ID FROM
gf_objects a, gf_objects b, gl_ObjectsTree c
WHERE 
a.ID=b.Objects_ID AND b.ID=c.Objects_ID AND
a.ParentObject='bais_staff' AND
a.Objects_ID='$un_username' AND
a.Relationship='Photo Gallery' AND
b.Relationship='Profile Picture Default'", O_VALUE);
$profilePicturePath=tree_id_to_path($Tree_ID);	
?>
<script language="javascript" type="text/javascript">
var _cb_passedBoundingBoxWidth=250;
var _cb_passedBoundingBoxHeight=250;

setTimeout("g('genderImg').setAttribute('picture',<?php 
if( $mode==$updateMode &&
	$profilePicturePath &&
	file_exists($_SERVER['DOCUMENT_ROOT'].$profilePicturePath)){
	$Key=end(explode('/',$profilePicturePath));
	preg_match('/^[a-f0-9]+_/i',$Key,$a);
	$Key=$a[0];
	echo $picture=1;
	$g=getimagesize($_SERVER['DOCUMENT_ROOT'].$profilePicturePath);
}else{
	echo $picture=0;
	$Key='';
}
?>);",500);
function imgWidget(n,o,p){
	if(n<2){
		g('imgWidgetOptions').style.display=(n==1 ? 'block' : 'none');
	}else if(n<4){
		o.className=(n==3?'iwnormal':'iwhl');
	}else if(n==5){
		/*we need to tell the uploader that 
		1. the only acceptable format is an image
		2. we need a crop done AND store the original
		*/
		ow('file_loader.php?_cb_passedBoundingBoxWidth='+_cb_passedBoundingBoxWidth+'&_cb_passedBoundingBoxHeight='+_cb_passedBoundingBoxHeight+'&_cb_passedBoxMethod=2&_cb_copyAs=1&cbFunction=imgWidget&cbParam[]=fixed:10&cbParam[]=handle&cbParam[]=Tree_ID','l1_loader','500,500');
	}else if(n==6 || n==60){
		//remove profile picture - i.e. no picture
		if(n==6 && !confirm('Are you sure you want to remove this profile picture?'))return;
		var gender=g('Gender').value.substring(0,1).toLowerCase();
		g('ProfileTree_ID').value='-1';
		g('ProfileKey').value='';
		g('genderImg').src='/images/i/deleket/'+(gender=='f'?'Office-Girl-64x64.png':'Preppy-64x64.png');
		killResizeImg=true;
		g('genderImg').width=64;
		g('genderImg').height=64;
		g('genderImg').setAttribute('picture',0);
		g('iw1').innerHTML='Add';
		detectChange=1;
	}else if(n==7){
		
	}else if(n==10){
		g('ProfileTree_ID').value=p;
		g('ProfileKey').value=o;
		g('genderImg').src='/images/reader.php?Tree_ID='+p+'&Key='+o;
		killResizeImg=false;
		I=new Image();
		I.src='/images/reader.php?Tree_ID='+p+'&Key='+o;
		resizeImg();
		g('genderImg').setAttribute('picture',1);
		g('iw1').innerHTML='Change';
		detectChange=1;
	}else if(n==11){
		var gender=g('Gender').value.substring(0,1).toLowerCase();
		if(g('genderImg').getAttribute('picture')==1)return;
		g('genderImg').src='/images/i/deleket/'+(gender=='f'?'Office-Girl-64x64.png':'Preppy-64x64.png');
		g('genderImg').width=64;
		g('genderImg').height=64;
	}
	
}
function resizeImg(){
	g('genderImg').height=(killResizeImg ? 64 : I.height);
	g('genderImg').width=(killResizeImg ? 64 : I.width);
	if(killResizeImg)return;
	setTimeout('resizeImg()',1000);
}
</script>
<input name="ProfileTree_ID" type="hidden" id="ProfileTree_ID" />
<input name="ProfileKey" type="hidden" id="ProfileKey" />
<div id="imgWidget" onMouseOver="imgWidget(1);" onMouseOut="imgWidget(0);">
<img id="genderImg" src="<?php
if($picture){
	echo '/images/reader.php?Tree_ID='.$Tree_ID.'&Key='.$Key;
}else{
	echo '/images/i/deleket/'.(preg_match('/^f/i',$Gender)?'Office-Girl-64x64.png':'Preppy-64x64.png');
}
?>" width="<?php echo $g[0]?$g[0]:64?>" height="<?php echo $g[1]?$g[1]:64?>">
<div id="imgWidgetOptions" style="display:none;">
	<div onMouseOver="imgWidget(2,this);" onMouseOut="imgWidget(3,this);" class="iwnormal" onClick="imgWidget(5)"><span id="iw1"><?php echo $picture?'Change':'Add'?></span> photo</div>
	<div onMouseOver="imgWidget(2,this);" onMouseOut="imgWidget(3,this);" class="iwnormal" onClick="imgWidget(6)">Remove photo</div>		
</div>
</div>







<?php
if($mode==$updateMode){
	?>
	<div id="un_usernameText" class="hdrTreb01">
		<span style="font-size:147%;font-weight:900;">Staff: <?php echo $un_username;?>
		<input name="un_username" type="hidden" id="un_username" value="<?php echo $un_username?>" />
		</span>
	</div>
	<?php
}else{
	?>
	<div id="un_usernameText" style="font-size:147%;font-weight:900;">
		<img src="/images/i/person1_28x30.gif" width="28" height="30">&nbsp;Adding new staff..
	</div>
	<table width="40%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>Type in a password:</td>
			<td><input name="un_password" type="password" id="un_password" size="15" maxlength="32" /></td>
		    <td nowrap="nowrap">
			&nbsp;
			<label><input name="temporaryPassword" type="checkbox" id="temporaryPassword" value="1" />
		      Make this a temporary password			</label>
			</td>
		</tr>
		<tr>
			<td>Retype the password:</td>
			<td><input name="nullun_password" type="password" id="nullun_password" size="15" maxlength="32" /></td>
		    <td>&nbsp;</td>
		</tr>
	</table>
	<?php
}
?>

<fieldset style="width:500px;"><legend style="font-size:147%;font-weight:900;">Primary Information</legend>
	<div class="fr">
		<label><input type="checkbox" id="st_active" name="st_active" value="1" <?php echo $mode==$insertMode || $st_active ? 'checked' : ''?> onChange="mgeChge(this)" /> Active Staff Member</label>
	</div>
	<?php if(strlen(preg_replace('/^ User Name$/','',$un_username))){ ?><div style="float:right;width:150px;clear:both;">
	<input type="button" name="Submit" value="Change Password.." onclick="return ow('change_password.php?un_username=<?php echo $un_username?>','l2_changepassword','300,450');" style="width:140px;">
	</div>
	
	<?php }?>Email:
	<input name="Email" type="text" class="ghost" id="Email" onBlur="fill(' Email',this,0)"  onFocus="fill(' Email',this,1)" value="<?php echo $Email?>" onChange="mgeChge(this)">
	<br />
	<br />
	<input name="FirstName" type="text" class="ghost" id="FirstName" onblur="fill(' First Name',this,0)" onfocus="fill(' First Name',this,1)" value="<?php echo $FirstName?>" onchange="mgeChge(this)" />
	<input name="MiddleName" type="text" class="ghost" id="MiddleName" onFocus="fill(' M.N.',this,1)" onBlur="fill(' M.N.',this,0)" value="<?php echo $MiddleName?>" size="5" onChange="mgeChge(this)">
<input name="LastName" type="text" class="ghost" id="LastName" onBlur="fill(' Last Name',this,0)"  onFocus="fill(' Last Name',this,1)" value="<?php echo $LastName?>" onChange="mgeChge(this)">
<br />
<textarea name="Address" cols="35" rows="2" id="Address" class="ghost" onBlur="fill(' Address',this,0)"  onFocus="fill(' Address',this,1)" onChange="mgeChge(this)"><?php echo $Address?></textarea>
<br />
<input name="City" type="text" class="ghost" id="City" onFocus="fill(' City',this,1)" onBlur="fill(' City',this,0)" onChange="mgeChge(this)" value="<?php echo $City?>" size="19">
<select name="State" id="State" onChange="countryInterlock('State','State','Country');mgeChge(this)" style="width:150px;">
	<option value="" class="ghost"> State <?php 
	$states=q("SELECT st_code, st_name FROM aux_states",$public_cnx,O_COL_ASSOC);
	foreach($states as $n=>$v){
		?><option value="<?php echo $n?>" <?php
		if($State==$n){
			$gotState=true;
			echo 'selected';
		}
		?>><?php echo htmlentities($v)?><?php
	}
	if(!$gotState && $State!=''){
		?><option value="<?php echo $State?>" style="background-color:tomato;" selected><?php echo $State?></option><?php
	}
?></select>
<input name="Zip" type="text" class="ghost" id="Zip" onFocus="fill(' Zip',this,1)" onBlur="fill(' Zip',this,0)" onChange="mgeChge(this)" value="<?php echo $Zip?>" size="10" maxlength="10">
<br />
<select name="Country" id="Country" onChange="countryInterlock('Zip','State','Zip');mgeChge(this)">
	<option value="" class="ghost"> Country<?php 
	$countries=q("SELECT ct_code, ct_name FROM aux_countries",$public_cnx,O_COL_ASSOC);
	foreach($countries as $n=>$v){
			?><option value="<?php echo $n?>" <?php
			if($Country==$n){
				$gotCountry=true;
				echo 'selected';
			}
			?>><?php echo htmlentities($v)?><?php
	}
	if(!$gotCountry && $Country!=''){
		?><option value="<?php echo $Country?>" style="background-color:tomato;" selected><?php echo $Country?></option><?php
	}
?></select>
<br />
H:
<input name="Phone" type="text" class="ghost" id="Phone" onFocus="fill(' Phone',this,1)" onBlur="fill(' Phone',this,0)" value="<?php echo $Phone?>" size="18" onChange="mgeChge(this)">
&nbsp;W:
<input name="WorkPhone" type="text" class="ghost" id="WorkPhone" onFocus="fill(' Work Phone',this,1)" onBlur="fill(' Work Phone',this,0)" value="<?php echo $WorkPhone?>" size="18" onChange="mgeChge(this)">
&nbsp;P/V:
<input name="PagerVoice" type="text" class="ghost" id="PagerVoice" onFocus="fill(' Pager/Voice',this,1)" onBlur="fill(' Pager/Voice',this,0)" value="<?php echo $PagerVoice?>" size="18" onChange="mgeChge(this)">
<br />
Mobile #
<input name="Cell" type="text" id="Cell" value="<?php echo $Cell?>" onChange="mgeChge(this)">
</fieldset><br />
<!-- end account_focus.php copy -->

<div class="objectWrapper">
<?php
//directorsMain
$layerWidth=490;
require($MASTER_COMPONENT_ROOT.'/comp_tabs_v200.php');
//-------------------------------- first tab --------------------------
ob_start();
?>
<div class="fr tr"> Gender:
  <select name="Gender" id="Gender" onchange="imgWidget(11);dChge(this)">
	<?php if($mode==$insertMode || ($mode==$updateMode && !preg_match('/^(m|f)/i',$Gender))){ ?>
	<option value="" class="ghost">&lt;Select&gt;</option>
	<?php } ?>
	  <option value="M" <?php echo strtoupper(substr($Gender,0,1))=='M'?'selected':''?>> Male </option>
	  <option value="F" <?php echo strtoupper(substr($Gender,0,1))=='F'?'selected':''?>> Female </option>
	</select>
  <br />
  <!--
  Race:
  <select name="Race" onchange="dChge(this);">
	<option value="" class="ghost">&lt;Select..&gt;</option>
	 <?php foreach($races as $v) {
		$sel = ($Race==$v?'selected':'');
		echo "<option value='".h($v)."' $sel>$v</option>";
		}
	 ?>
	</select>
  -->
</div>
<?php if($mode==$insertMode || 
	(minroles()<=ROLE_ADMIN && q("SELECT sr_permissions FROM bais_StaffRoles WHERE sr_stusername='".sun()."' AND sr_roid=".ROLE_ADMIN." AND (sr_permissions & ".(PERM_ADMINISTRATIVE + PERM_CLERICAL).")>0", O_VALUE))){ 
	?>
Birthdate: 
<img src="/images/i/calendar1.png" class="calIcon" alt="select date" title="click this to select a date" onclick="show_cal(this,this.nextSibling.id);" align="absbottom" /><input name="BirthDate" type="text" id="BirthDate" value="<?php echo t($BirthDate, f_qbks);?>" onchange="dChge(this)" date_box="1" />
<br />
Soc. Security #:
<input name="SocSecurityNumber" type="text" id="SocSecurityNumber" value="<?php $SocSecurityNumber=preg_replace('/[^0-9]*/','',$SocSecurityNumber); echo $SocSecurityNumber ? substr($SocSecurityNumber,0,3).'-'.substr($SocSecurityNumber,3,2).'-'.substr($SocSecurityNumber,5,4):'' ?>" onchange="dChge(this)" />
<br />
<?php } ?>
<br />					
  Hire Date: <img src="/images/i/calendar1.png" class="calIcon" alt="select date" title="click this to select a date" onclick="show_cal(this,this.nextSibling.id);" align="absbottom" /><input name="st_hiredate" type="text" id="st_hiredate" value="<?php echo t($st_hiredate,f_qbks);?>" onChange="mgeChge(this)" /> <br />
	Discharge Date: <img src="/images/i/calendar1.png" class="calIcon" alt="select date" title="click this to select a date" onclick="show_cal(this,this.nextSibling.id);" align="absbottom" /><input name="st_dischargedate" type="text" id="st_dischargedate" value="<?php echo t($st_dischargedate,f_qbks);?>" onBlur="if(this.value!==''){ g('st_dischargereason').disabled=false; g('st_dischargereason').focus(); }" onChange="mgeChge(this)" /> <br />
	Discharge Reason: <input name="st_dischargereason" type="text" id="st_dischargereason" value="<?php echo $st_dischargereason;?>" size="45" <?php echo $st_dischargedate=='' || $st_dischargedate=='0000-00-00' ? 'disabled' :''?> onChange="mgeChge(this)" /> <br />
	<textarea name="MisctextStaffnotes" cols="45" rows="5" class="ghost" id="MisctextStaffnotes" onFocus="fill(' Notes',this,1)" onBlur="fill(' Notes',this,0)" onChange="mgeChge(this)"><?php echo $MisctextStaffnotes?></textarea>
	
	<br />
	<br />
	Recruiter: <span style="width:500px;">
	<input name="GLF_Recruiter" type="text" id="GLF_Recruiter" onchange="mgeChge(this)" value="<?php echo h($GLF_Recruiter);?>" maxlength="35" />
	</span><br />
	Transaction Fee:<span style="width:500px;">
	<input name="GLF_TransactionFee" type="text" id="GLF_TransactionFee" onchange="mgeChge(this)" value="<?php echo h($GLF_TransactionFee);?>" size="15" />
	</span><br />
	E&amp;O Fee: 
	<span style="width:500px;">
	<input name="GLF_EOFee" type="text" id="GLF_EOFee" onchange="mgeChge(this)" value="<?php echo h($GLF_EOFee);?>" size="15" />
	</span><br />
	
<?php
//-------------------------------- store tab --------------------------
get_contents_layer('stKeyInfo');
?>
<label><h3><input name="st_status" type="checkbox" id="st_status" value="1" <?php echo $HasUsage || !isset($HasUsage) ? 'checked':''?> onChange="mgeChge(this)"> Allow usage of the database</h3></label>
Revoking usage will not change permissions but will prevent login<br />
<br />
<!-- 2010-02-06: moved roles and offices to here -->
<div style="border:1px dotted #333;padding:0px 5px 12px 15px;width:60%;margin-bottom:15px;"> <strong>Select at least one role for this staff member</strong><br />
<?php
//--------------------------------------------------------------------------------------
$staffRoles=array(ROLE_ADMIN, ROLE_MANAGER, ROLE_AGENT);
if($mode==$updateMode) $thisStaffRoles=q("SELECT REPLACE(sr_roid,'.0','') FROM bais_StaffRoles WHERE sr_stusername='".$un_username."'",O_COL);
foreach($userType as $key=>$name){
	if(!in_array($key,$staffRoles))continue;
	$canAssignRole=(minroles()<=$key ? true : false);
	?>
	<label> <input type="checkbox" name="roles[<?php echo $key?>]" id="roles[<?php echo $key?>]" <?php
	if($mode==$insertMode){
		if($key==ROLE_AGENT)echo 'checked';
	}else{
		if(@in_array($key, $thisStaffRoles))echo 'checked';
	}
	?> value="1" onchange="mgeChge(this)" <?php if(!$canAssignRole)echo 'disabled';?> /> <?php echo $name?></label> <br /><?php
}

?>
<?php if(false){ ?>
<hr size="1" noshade="noshade" /><?php
$canAssignOffice=false;
if($mode==$updateMode) $thisStaffOffices=q("SELECT so_unusername, so_unusername FROM bais_StaffOffices WHERE so_stusername='".$un_username."'
UNION
SELECT os_unusername, os_unusername FROM bais_OfficesStaff WHERE os_stusername='".$un_username."'", O_COL_ASSOC);
if($offices=list_offices('keys')){
	?><strong><?php if($mode==$insertMode){
		?>Select at least one office for this staff<?php
	}else{
		?>Regions this staff is in (must be assigned at least one)<?php
	}?></strong><br />
	<?php
	foreach($offices as $key=>$name){
		$canAssignOffice=true;
		?><label> <input type="checkbox" name="offices[<?php echo $key?>]" id="offices[<?php echo $key?>]" value="1" onchange="mgeChge(this)" <?php if($thisStaffOffices[$key])echo 'checked';?> /> <?php echo q("SELECT oa_businessname FROM bais_orgaliases WHERE oa_unusername='$name'", O_VALUE);?></label> <br /><?php
	}
}else{
	?>
	You do not have access to any programs (offices).  Please contact <?php echo $AcctCompanyName?> director for assistance.<br />
	<input type="button" name="Button" value="  Cancel  " onclick="window.close();" />
	<?php
	exit;
}
?>
<?php } ?>
</div>
<?php
//-------------------------------- store tab --------------------------
get_contents_layer('stAccess');

require('components/comp_801_loghistory.php');
get_contents_layer('stHistory');

//-------------------------------- end tabs --------------------------
$tabAction='layerOutput';
require($MASTER_COMPONENT_ROOT.'/comp_tabs_v200.php');
?>
</div>


</div>
<div id="footer">
<script>
darken();
</script>
&nbsp;
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
</html><?php page_end();?>