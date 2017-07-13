<?php 
/*
*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

if(minroles()>ROLE_ADMIN)exit('You do not have access to this');
$suppressForm=true;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Themes - '.$AcctCompanyName?></title>



<link id="cssUndoHTML" rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="../../site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="../../Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
#mainBody{
	float:none;
	}
#leftNav{
	display:none;
	}
.grayed{
	color:#ccc;
	}
.grayed input{
	color:#ccc;
	}
.createBox{
	border:1px solid darkblue;
	padding:5px 10px;
	background-color:cornsilk;
	width:600px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	}
.highlighted{
	border: 1px dotted darkgreen;
	color:white;
	padding:0px 5px;
	background-color:darkslateblue;
	}
@media screen{
.createBox{
	overflow:scroll;
	height:125px;
	}
}
@media print{
.createBox{
	overflow:auto;
	height:inherit;
	}
input{
	display:none;
	}
}
textarea {
    background-color: #F4EEDF;
    border: 1px solid #999999;
    padding: 5px;
	}
</style>

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
</script>


</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onSubmit="return beginSubmit();">
<?php }?>
<div id="header">
&nbsp;
</div>
<div id="mainBody">




<?php echo '<form id="form1" name="form1" method="get">';?>
<h3>Search Table and View Structure</h3>
Databases <em>(multiple OK)</em>:<br />
<select name="databases[]" size="10" multiple="multiple" id="databases[]">
	<option value="">&lt;Select..&gt;</option>
	<?php
	$a=q("SHOW DATABASES", O_ARRAY);
	foreach($a as $v){
		$dbs[]=$v['Database'];
	}
	foreach($dbs as $db){
		if(@in_array($db,$databases)){
			if(!$optgroups){
				$optgroups=true;
				?><optgroup label="Selected databases"><?php
			}
			?><option value="<?php echo $db?>" <?php echo @in_array($db,$databases)?'selected':''?> ><?php echo $db?></option><?php
		}else{
			ob_start();
			?><option value="<?php echo $db?>" <?php echo @in_array($db,$databases)?'selected':''?> ><?php echo $db?></option><?php
			$options.=ob_get_contents();
			ob_end_clean();
		}
	}
	if($optgroups){
		?></optgroup><optgroup label="Others"><?php
	}
	echo $options;
	if($optgroups){
		?></optgroup><?php
	}
	?>
</select><br />

Search string:
<input name="q" type="text" id="q" value="<?php echo h(stripslashes($q));?>" />
<input type="hidden" name="mode" id="mode" value="dbSearchStructure" />
<input type="submit" name="Submit" value="Submit" />
<input type="button" name="Button" value="Cancel" onclick="window.close();" />
<br />
<br />
<span class="gray">Use regular expressions.  To view all structure just enter *.  To view fields with &quot;username&quot; in the field name, enter <strong>[_a-z]username</strong> for example.</span>
<?php echo '</form>';?>


<?php
if($mode=='dbSearchStructure'){
	if(!count($databases))exit('no database(s) selected');
	foreach($databases as $n=>$database){
		unset($table, $tables, $matches);
		?><h1><?php echo $database?>: "<?php echo stripslashes($q);?>"</h1><?php
		if(!count($databases))$databases[]=$database;
		$a=q("SHOW TABLES IN $database", O_ARRAY);
		foreach($a as $v){
			$table=$v['Tables_in_'.$database];
			ob_start();
			///".(preg_match('/^_v_/',$table) ? 'VIEW':'TABLE')."
			$b=q("SHOW CREATE TABLE $database.$table", O_ARRAY,ERR_ECHO);
			$err=ob_get_contents();
			ob_end_clean();
			if(!$err){
				if($tables[$table]['create']=$b[1]['Create View']){
					//$tables[$table]['create']=str_replace('`','',$tables[$table]['create']);
					$tables[$table]['type']='View';
					$tables[$table]['create']=preg_replace('/\s+AS\s+([_a-z0-9]+),/i',' AS $1,'."<br />",$tables[$table]['create']);
					$tables[$table]['create']=preg_replace('/\s+FROM\s+/i',"<br />FROM ",$tables[$table]['create']);
					$tables[$table]['create']=preg_replace('/\s+SELECT\s+/i'," SELECT<br />",$tables[$table]['create']);
					$tables[$table]['create']=preg_replace('/\s+GROUP BY\s+/i',"<br />GROUP BY ",$tables[$table]['create']);
					$tables[$table]['create']=preg_replace('/\s+ORDER BY\s+/i',"<br />ORDER BY ",$tables[$table]['create']);
					$tables[$table]['create']=str_replace($database.'.','',$tables[$table]['create']);
					
				}else{
					$tables[$table]['create']=$b[1]['Create Table'];
					$tables[$table]['create']=str_replace('`','',$tables[$table]['create']);
					$tables[$table]['create']=nl2br($tables[$table]['create']);
					$tables[$table]['type']='Table';
				}
			}else{
				$errs[]=$err;
			}
			if(preg_match_all('/'.stripslashes($q).'/'.(!$sensitive?'i':''),$tables[$table]['create'],$m)){
				//prn($m);
				$tables[$table]['count']=count($m[0]);
				$tables[$table]['create']=preg_replace('/('.stripslashes($q).')/'.(!$sensitive?'i':''),'<span class="highlighted">$1</span>',$tables[$table]['create']);
				$matches[$table]['count']=count($m[0]);
			}
		}
		if($matches){
			?><strong><?php echo count($matches)==1 ? count($matches).' structure match' : count($matches).' structure matches';?></strong><?php
			$matches=subkey_sort($matches,'count',$options=array(
				'sort'=>'desc',
			));
			foreach($matches as $table=>$v){
				?><h3><a href="http://relatebase-rfm.com:2086/3rdparty/phpMyAdmin/tbl_structure.php?db=<?php echo $database?>&token=d26ff94cdaa3230d723bce332cb34e54&table=<?php echo $table?>&goto=tbl_structure.php" onclick="return ow(this.href,'l1_pma','700,700');" title="View/edit in phpMyAdmin"><?php echo $table?></a> (<?php echo $tables[$table]['count']?> match<?php echo $tables[$table]['count']>1?'es':''?>)</h3>
				<div>
				<div class="createBox">
				<?php echo $tables[$table]['create']?>			</div>
				</div>
				<?php
			}
		}else{
			?><h2>No matches for "<?php echo stripslashes($q)?>"</h2><?php
		}
	}
}
?>


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