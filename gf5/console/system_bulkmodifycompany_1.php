<?php 
/*
todo:
logo at top on print
Great Locations
	address
	phone
	[if]agent phone number
find this text: Income and student restricted - and implement
main picture
floor plan
thumbnails
basically, IMAGES with a foundation for future usage


*/
if($mode=='updateClientData'){
	$data=array_transpose($data);
	unset($errs);
	foreach($data as $Clients_ID=>$v){
		if(!$v['ID']){
			unset($data[$Clients_ID]);
			continue;
		}
		
		//modify data
		unset($r);
		if(!function_exists('parse_name'))require($FUNCTION_ROOT.'/function_parse_name_v101.php');
		if($v['action']=='parse_company'){
			$r=parse_name($v['CompanyName']);
		}else if($v['action']=='parse_lastname'){
			$r=parse_name($v['PrimaryLastName']);
		}
		if($r){
			$v['PrimaryFirstName']=$PrimaryFirstName=$r['FirstName'];
			$v['PrimaryLastName']=$PrimaryLastName=$r['LastName'];
			$v['PrimaryMiddleName']=$PrimaryMiddleName=$r['MiddleName'];
		}
		$sql="UPDATE finan_clients c SET ";
		foreach($v as $o=>$w){
			if($o=='ID' || $o=='action')continue;
			$sql.=$prefix[$o].$o.'=\''.$w.'\',';
		}

		$sql=rtrim($sql,',');
		$sql.=' WHERE c.ID='.$Clients_ID;
		
		//make the query
		//q($sql);
		q($sql);
		?><script language="javascript" type="text/javascript">
		window.parent.g('c_<?php echo $Clients_ID;?>').value='';
		</script><?php

	}
	?><script language="javascript" type="text/javascript">
	window.parent.detectChange=0;
	window.parent.location='/gf5/console/system_bulkmodifycompany_1.php?r=<?php echo rand(1,1000000);?>';
	</script><?php
	error_alert('Updated, reloading page');
	exit;
}
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

if(minroles()>ROLE_ADMIN)exit('You do not have access to this');


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Master PMC Client Updater - '.$AcctCompanyName?></title>



<link id="cssUndoHTML" rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="../../site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="../../Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="../../site-local/local.js"></script>
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


</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onsubmit="return beginSubmit();">
<?php }?>
<div id="header">

&nbsp;

</div>
<div id="mainBody">

<script language="javascript" type="text/javascript">
function dC(n){
	g('c_'+n).value=1;
	detectChange=1;
}
function btns(n){
	if(n=='close' && detectChange && !confirm('Are you sure you want to close this window? changes you have made will be lost.'))return false;
	if(n=='submit' && !detectChange){
		alert('You have not made changes to any fields; make changes before submitting');
		return false;
	}
}
</script>
<style type="text/css">
.alt td{
	background-color:dimgray;
	}
#allclients{
	margin:5px 20px;
	}
#allclients input[type="text"], #allclients select{
	padding:0px;
	font-size:12px;
	}
#allclients input[type="text"], #allclients select{
	padding:1px;
	font-size:12px;
	border-color:darkblue;
	}
	
#allclients td{
	padding:1px 3px 1px 3px;
	}
.br{ border-right:1px solid #666; }
.bl{ border-left:1px solid #666; }
.bb{ border-bottom:1px solid #666; }
</style>
<?php
$a=q("SELECT ID, IF(CompanyName, CompanyName, ClientName) AS CompanyName, UserName, PrimaryFirstName, PrimaryMiddleName, PrimaryLastName, Email, Notes FROM finan_clients WHERE ResourceType IS NOT NULL /* AND !(CompanyName!='' AND PrimaryFirstName!='' AND PrimaryLastName='') */ ORDER BY PrimaryLastName, PrimaryFirstName, IF(CompanyName, CompanyName, ClientName)", O_ARRAY);

?>
<h2><?php echo count($a);?> Records</h2>
<table id="allclients" class="yat">
<thead>
<tr>
  <th>ID</th>
	<th>Company</th>
	<th>Action</th>
	<th>FirstName</th>
	<th>MN</th>
	<th>LastName</th>
	<th>Email</th>
	<th>UserName</th>
	<th>Notes</th>
</tr>
</thead>
<tbody>
<?php
if($a){
	$i=0;
	foreach($a as $n=>$v){
		$i++;
		$j++;
		extract($v);
		?><tr class="<?php echo !fmod($j,2)?'alt':''?>">
  <td class="tar"><?php echo $ID;?></td>

			<td>
			<input type="hidden" id="c_<?php echo $ID?>" name="data[ID][<?php echo $ID;?>]" value="" />
			<input type="text" name="data[CompanyName][<?php echo $ID;?>]" value="<?php echo $CompanyName;?>" size="17" onchange="dC(<?php echo $ID;?>)" /></td>
			<td><select name="data[action][<?php echo $ID?>]" onchange="dC(<?php echo $ID;?>)" style="width:105px;">
			<option value="">none</option>
			<option value="parse_company">parse comp->name</option>
			<option value="parse_lastname">parse lname->name</option>
			</select></td>
			<td><input type="text" name="data[PrimaryFirstName][<?php echo $ID?>]" value="<?php echo $PrimaryFirstName;?>" onchange="dC(<?php echo $ID;?>)" size="10" /></td>
			<td><input type="text" name="data[PrimaryMiddleName][<?php echo $ID?>]" value="<?php echo $PrimaryMiddleName;?>" size="3" onchange="dC(<?php echo $ID;?>)" /></td>
			<td><input type="text" name="data[PrimaryLastName][<?php echo $ID?>]" value="<?php echo $PrimaryLastName;?>" onchange="dC(<?php echo $ID;?>)" /></td>
			<td><input type="text" name="data[Email][<?php echo $ID?>]" value="<?php echo $Email;?>" onchange="dC(<?php echo $ID;?>)" /></td>
			<td><input type="text" name="data[UserName][<?php echo $ID?>]" value="<?php echo $UserName;?>" onchange="dC(<?php echo $ID;?>)" size="11" /></td>
			<td><?php echo $Notes;?></td>
		</tr><?php
	}
}
?>
</tbody>
</table>
<br />
<input name="mode" type="hidden" id="mode" value="updateClientData" onchange="return btns('submit');" />
<input type="submit" name="Submit" value="Update" />
&nbsp;
<input type="button" name="Button" value="Close" onclick="return btns('close');" />
&nbsp;
<span id="submitStatus" style="visibility:hidden;">
<img src="/images/i/ani/ani-fb-orange.gif" width="16" height="11" alt="processing request.." />
</span>

</div>
<div id="footer">
&nbsp;&nbsp;
</div>
<?php if(!$suppressForm){ ?>
</form>
<?php }?>
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