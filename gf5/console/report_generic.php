<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

/* eventually stored in a database 
2012-12-03: look in Gre Atloca tions for a better implementation combining this and Sim pleF ostercare's reports into one, and componentizing the DateFrom and DateTo toolbar as well.

NOTE that if this is implemented, then the $searchtype=='location' logic below is clumsy

*/
$reports=array(
	'itemsquery'=>array(
		'component'=>'comp_itemsquery_v100.php', 
		'accesslevel'=>ROLE_AGENT,
		'title'=>'Product Search',
		'description'=>'Product Search Results',
		'select_disabled'=>true,
		'form'=>($searchtype=='location' ? 
			array(
				'implement'=>true,
				'checkboxes'=>'location_MIVAONLY',
				'mode'=>'exportSelectUpdate',
				'submode'=>'setRecordsExport',
				'submit'=>'<input type="submit" name="submit" value="Export Location Maps" id="exportLocationMaps" />',
			):array(
			
			)),
		'complete'=>true,
	),
	'catsubcat'=>array(
		'component'=>'comp_miscreports_v100.php', 
		'accesslevel'=>ROLE_AGENT,
		'title'=>'Category and Subcategory Listing',
		'description'=>'A listing of unique category/subcategory listings in the database currently with a count of each.  Click the count to get the products listed for that combination',
		'complete'=>true,
	),
	'catsubcateditor'=>array(
		'component'=>'comp_reports_catsubcatedit_v100.php', 
		'accesslevel'=>ROLE_AGENT,
		'title'=>'Bulk Category and Subcategory Updater',
		'description'=>'A listing of unique category/subcategory listings in the database currently with a count of each. Make bulk changes to category or subcategory or both',
		'complete'=>true,
	),
	'activitybydate'=>array(
		'component'=>'comp_miscreports_v100.php', 
		'accesslevel'=>ROLE_AGENT,
		'title'=>'Activity By Date',
		'description'=>'listings sorted from newest create date to to oldest',
		'complete'=>true,
	),
	'counties'=>array(
		'component'=>'comp_counties.php', 
		'accesslevel'=>ROLE_AGENT,
		'title'=>'Listing of Counties',
		'description'=>'Counties for specific states (all modern counties), and alphabetized',
		'complete'=>true,
	),
	'groupings'=>array(
		'component'=>'comp_miscreports_v100.php', 
		'accesslevel'=>ROLE_AGENT,
		'title'=>'Listing of Groupings (200-ct. batches)',
		'description'=>'New items are added to a group number which increments every 200 records',
		'complete'=>true,
	),
	'filesonserver'=>array(
		'component'=>'comp_miscreports_v100.php', 
		'accesslevel'=>ROLE_AGENT,
		'title'=>'Files on Server (master, pending_master, root)',
		'description'=>'Comparison of files and sizes in "root", master, and pending_master folders',
		'complete'=>true,
	),
);
if(!($reportSettings=$reports[$report]))exit('Unrecognized Report ID');
$PageTitle=$reportSettings['title'];
$form=$reportSettings['form'];

if(minroles()<=$reportSettings['accesslevel']){
	if(file_exists('components/'.$reportSettings['component'])){
		require('components/'.$reportSettings['component']);
	}else{
		$reportOutput='report not developed!';
	}
}else{
	exit('You do not have permission to access this report');
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo dynamic_title($PageTitle.' - '.$AcctCompanyName);?></title>



<link id="cssUndoHTML" rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="../../site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="../../Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
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

var isEscapable=2;

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already

$(document).ready(function(){
	$('#_report_').change(function(){
		window.location='report_generic.php?report='+$(this).val();
	});
});
</script>
<?php
echo $headerOutput;
?>

</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onSubmit="return beginSubmit();">
<?php }?>
<div id="header">
<div class="fr">
Select a report: <select id="_report_">
<?php
foreach($reports as $n=>$v){
	?><option value="<?php echo $n;?>" <?php echo $v['select_disabled']?'disabled':''?> <?php echo $report==$n?'selected':''?>><?php echo $v['title'];?></option><?php
}
?>
</select>
</div>

</div>
<div id="mainBody">

<?php
echo $reportOutput;
?>

<?php
if($form['implement']){
	$btnName=($form['submitLabel']?$form['submitLabel']:'Submit');
	?>
	<input type="hidden" name="report" value="<?php echo h(stripslashes($report));?>" id="report" />
	<input type="hidden" name="searchtype" value="<?php echo h(stripslashes($searchtype));?>" id="searchtype" />
	<input type="hidden" name="q" value="<?php echo h(stripslashes($q));?>" id="q" />
	<input type="hidden" name="mode" value="<?php echo $form['mode']?$form['mode']:'refreshComponent';?>" id="mode" />
	<input type="hidden" name="submode" value="<?php echo $form['submode'];?>" id="submode" />
	<input type="hidden" name="component" id="component" value="1" />
	<!-- 2012-11-02 are these correct, and what is the purpose of them -->
	<input type="hidden" name="componentFile" id="componentFile" value="<?php echo $reportSettings['component'];?>" />
	<input type="hidden" name="componentKey" id="componentKey" value="<?php echo md5($reportSettings['component'].$MASTER_PASSWORD);?>" />
	<?php
	if($form['fields'])
	foreach($form['fields'] as $n=>$v){
		?><input type="hidden" name="<?php echo $n;?>" id="<?php echo $n;?>" value="<?php echo h($v);?>" /><?php echo "\n";
	}
	if($n=$form['submit']){
		echo $n;
	}else{
		?><input type="submit" name="submit" value="<?php echo $btnName ? $btnName : 'Submit';?>" /><?php
	}
}

?>&nbsp;<input type="button" name="Button" value="Close" onClick="window.close()" />
</div>
<div id="footer">
&nbsp;&nbsp;
</div>
<?php if(!$suppressForm){ ?>
</form>
<?php }?>
<?php if(!$hideCtrlSection){ ?>
<div id="showTester" title="Javascript Tester" onClick="g('tester').style.display='block';">&nbsp;</div>
<div id="tester" >
	<a href="#" onClick="g('ctrlSection').style.display='block';return false;">Show Control Section</a><br />
	<textarea name="test" cols="65" rows="4" id="test">clear_form();</textarea><br />
	<input type="button" name="button" value="Test" onClick="jsEval(g('test').value);"><br />
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