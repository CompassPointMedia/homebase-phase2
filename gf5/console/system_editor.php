<?php 
/*
2012-08-26:
----------
I did some simple editable tables in Gr eatLo cationsF ranchise but not much generic progress at that point.  This page system_editor.php introduces a query builder and generic settings/parameters to store this in a database; we are a long way from the old RelateBase tables but boy that was so cumbersome anyway, so rebuilding this.

Pay special attention to dataobjects' coding so it's unified as much as possible with what I develop here

Here is a sample sequence: [2012-08-27]
--------------------------
pass a table= in the query string
tables have views, no view is passed
based on this combo, we go into system_tables which is the same as is used for exports, and we create a table and/or we create a view
views by default analyze table and identify primary key, field values at time of check, uniqueness and primary key, also enum/set
- this provides sensible error checking
just like that, we have a table/view registry
all tables' default view is named just that - (default)
saving the default view (mode=updateView), uniquely, will copy the new view over and you must give it a name to do this
the fields are clickable; there is also a hidden section with a down tick, and opening it up gives you the table/view and the root params
you should be able to export any configuration you are showing
I can sort but if I've changed things warns me I'll lose changes

*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');


//2012-08-27: settings
if($table=='aux_ebaystorenumbers'){
	$recordPKField='ID';
	$comments='The Category field of this table must match (case sensitive) with the category of the products page to be useful.  Changing a category name here will NOT change the corresponding category in the products table';
	$delete=true; //can delete records
	$queryData=array(
		'builds'=>array(
			array(
				'table'=>'aux_ebaystorenumbers',
				'alias'=>'e',
				'select'=>'',
				'groups'=>array('id'),
			),
			array(
				'table'=>'finan_items',
				'join'=>'LEFT JOIN',
				'using'=>'Category',
				/* for example; 'Category' implicit first table, this-> implicit this table, else must refer to an explicitly defined alias */
				'on'=>array('Category','this->Category'),
			),
		),
		'fields'=>array(
			array(
				'Field'=>'ID',
			),
			array(
				'Field'=>'Category',
			),
			array(
				'Field'=>'StartLetter',
			),
			array(
				'Field'=>'EndLetter',
			),
		),
	);
}else{
	exit('unrecognized table, unable to show data');
}
function build_query($queryData){
	global $queryDatax;
	$build=current($queryData['builds']);
	if(!$build['alias']){
		$t=explode('_',trim($build['table'],'_'));
		//back build information
		$build['alias'] = $queryData['builds'][0]['alias'] = substr($t[count($t)>1?1:0],0,1);
		$aliases[$build['alias']]++;
	}
	$select=($build['select']?$build['select']:$build['alias'].'.*');
	$from=$build['table'].' '. $build['alias'];
	if($build['groups'])foreach($build['groups'] as $v)$group[]=$build['alias'].'.'.$v;
	$main=$build;
	
	if(count($queryData['builds'])>1){
		$i=0;
		foreach($queryData['builds'] as $build){
			$i++;
			if($i==1)continue;
			if(!$build['join'])$build['join'] = $queryData['builds'][$i-1]['join'] = 'JOIN';
			//alias
			if(!$build['alias']){
				$t=explode('_',trim($build['table'],'_'));
				$build['alias']=substr($t[count($t)>1?1:0],0,1);
				if($aliases[$build['alias']]){
					$build['alias'].=count($aliases[$build['alias']])+1; //e.g. 2,3,4..
					$queryData['builds'][$i-1]['alias'] = $build['alias'];
					$aliases[$build['alias']]++;
				}
			}
			$from.=' '.$build['join'].' '.$build['table'].' '.$build['alias'];
			if($build['on_phrase']){
				$from.=' ON '.$build['on_phrase'];
			}else if($build['using']){
				$from.=' USING ('.$build['using'].') ';
			}else if($build['on']){
				//punt :)
				$from.=' ON ';
			}else{
				exit('error in building query');
			}
		}
	}
	return "SELECT\n".$select."\nFROM\n".$from."\nWHERE 1\n".($group ? "\nGROUP BY\n".implode(',',$group) : '');
}

if($mode=='errors'){
	if($e=$_SESSION['special']['tableUpdateErrs'][$sessionKey]){
		?>
		<style type="text/css">
		.data1 td{
			border-collapse:collapse;
			padding:1px 4px;
			border-bottom:1px dotted #666;
			}
		</style>
		<table class="data1">
		<thead>
		<th>line</th>
		<?php foreach(current($e) as $n=>$v){ ?>
		<th><?php echo $n;?></th>
		<?php } ?>
		</thead>
		<?php
		foreach($e as $n=>$v){
			?><tr><td><?php echo $n;?></td><?php
			foreach($v as $o=>$w){
				?><td><?php echo $w;?></td><?php
			}
		}
		?></tr>		
		</table>
		<input type="button" name="close" value="Close" onclick="window.close();" /><?php
	}else{
		prn('unable to find error report, your session may have expired');
	}
	eOK();
}else if($mode=='updateTable'){
	if(minroles()>ROLE_ADMIN)error_alert('You do not have access to this page');
	$data=array_transpose($data);
	unset($additionPerformed);
	$sessionKey=substr(md5(time().rand(1,1000000)),0,8);
	foreach($data as $n=>$v){
		if(!$v['__system_change__']){
			unset($data[$n]);
			continue;
		}
		prn($n);		
		//modify data
		if($n==0)$additionPerformed=true;
		$action=($n==0?'inserting':'updating');
		$sql=($n==0?'INSERT INTO':'UPDATE')." $table SET ";
		foreach($v as $o=>$w){
			if($o=='__system_change__')continue;
			$sql.=$prefix[$o].$o.'=\''.$w.'\',';
		}

		$sql=rtrim($sql,',');
		if($n>0)$sql.=' WHERE '.$recordPKField.'=\''.$n.'\'';
		prn($sql);
		
		//make the query
		$queries++;
		ob_start();
		q($sql, ERR_ECHO);
		$err=ob_get_contents();
		ob_end_clean();
		if($qr['system_err']){
			if($action=='inserting')$insertError=true;
			$e++;
			$_SESSION['special']['tableUpdateErrs'][$sessionKey][$e]=array(
				'key'=>$v[$recordPKField],
				'err'=>$qr['system_err'],
				'errno'=>$qr['system_errno'],
			);
		}
		?><script language="javascript" type="text/javascript">
		window.parent.g('c_<?php echo $n;?>').value='';
		</script><?php
	}
	?><script language="javascript" type="text/javascript">
	<?php if($_SESSION['special']['tableUpdateErrs'][$sessionKey]){ ?>
	if(confirm('There were <?php echo count($_SESSION['special']['tableUpdateErrs'][$sessionKey]);?> error<?php echo count($_SESSION['special']['tableUpdateErrs'][$sessionKey])==1?'':'s';?> in your quer(ies).  Would you like to see them now?'))window.parent.ow('<?php echo $_SERVER['PHP_SELF'];?>?mode=errors&table=<?php echo $table;?>&sessionKey=<?php echo $sessionKey;?>','l2_errors','700,700');
	<?php } ?>
	window.parent.detectChange=0;
	<?php if($additionPerformed && !$insertError){ ?>
	alert('New record added OK');
	var l =window.parent.location+'';
	window.parent.location=l+'';
	<?php } ?>
	</script><?php
}else if($mode=='deleteRow'){
	q("DELETE FROM $table WHERE ID=$ID");
	?><script language="javascript" type="text/javascript">
	window.parent.g('r_<?php echo $ID?>').style.display='none';
	</script><?php
	eOK(__LINE__);
}


$PageTitle='edit table data';

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

var isEscapable=2;

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already
</script>
<?php ob_start(); ?>

</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onSubmit="return beginSubmit();">
<?php }?>
<div id="header">
&nbsp;
<?php 
$out=ob_get_contents();
ob_end_clean();
echo str_replace('resources/bais_01_exe.php','',$out);
?>

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
	window.close();
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
$a=q(build_query($queryData), O_ARRAY);
?>
<h1>Table: <?php echo $table;?></h1>
<p class="gray"><?php echo $comments;?></p>
<h3><?php echo count($a);?> Records</h3>
<table id="allclients" class="yat">
<thead>
<tr>
	<th>&nbsp;</th><?php
	if(!$queryData['fields']){
		//build from the builds data of this array, perpetuate it in the database, then available for edits esp. hides, data types and ec etc.
	}
	$j=0;
	foreach($queryData['fields'] as $v){
		$j++;
		?><th><?php echo $v['Field'];?></th><?php
	}
	?>
</tr>
</thead>
<tbody>
<?php
if($a){
	$i=0;
	foreach($a as $n=>$v){
		$i++;
		extract($v);
		echo "\n\n";
		?><tr id="r_<?php echo $i;?>" class="<?php echo !fmod($i,2)?'alt':''?>">
		<?php if($delete){ ?>
		<td><a href="<?php echo $_SERVER['PHP_SELF'].'?table='.$table.'&mode=deleteRow&ID='.$ID;?>" target="w2" onclick="return confirm('Are you sure you want to delete this record?');" tabindex="-1"><img src="/images/i/del2.gif" alt="delete" /></a> &nbsp;&nbsp;</td>
		<?php } ?>
		<?php
		$j=0;
		foreach($queryData['fields'] as $w){
			$j++;
			$field=$w['Field'];
			?><td><?php
			if($j==1){
				?><input type="hidden" id="c_<?php echo $ID?>" name="data[__system_change__][<?php echo $ID;?>]" value="" /><?php
			}
			?><input type="text" name="data[<?php echo $field;?>][<?php echo $ID;?>]" onchange="dC(<?php echo $v['ID'];?>)" value="<?php echo h($v[$field]);?>" /></td><?php
		}
		?></tr><?php
	}
	echo "\n\n";
	?><tr><td colspan="100%"><h2>Add a new record:</h2></td></tr>
	<tr><td>&nbsp;</td>
	<?php 
	$j=0;
	foreach($queryData['fields'] as $w){
		$j++;
		$field=$w['Field'];
		?><td><?php
		if($j==1){
				?><input type="hidden" id="c_0" name="data[__system_change__][0]" value="" /><?php
		}
		?><input type="text" name="data[<?php echo $field;?>][0]" onchange="dC(0)" value="" /></td><?php
	}
	?></tr><?php
}
?>
</tbody>
</table>
<br />
<input name="mode" type="hidden" id="mode" value="updateTable" />
<input name="table" type="hidden" id="table" value="<?php echo $table;?>" />
<input type="submit" name="Submit" value="Update" onsubmit="return btns('submit');" />
&nbsp;
<input type="button" name="Button" value="Close" onClick="return btns('close');" />
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