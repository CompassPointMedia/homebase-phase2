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
//identify this script/GUI
if(!$mode){
	$localSys['scriptID']='gen_access1';
	$localSys['scriptVersion']='4.0';
	$localSys['componentID']='main';
	$localSys['pageLevel']=1;
}


require_once('systeam/php/config.php');

require_once('resources/bais_00_includes.php');

require_once('systeam/php/auth_i2_v100.php');

if(minroles()>ROLE_ADMIN)exit('You do not have access to this');

if($mode=='makeBackup'){
	if(!$makeTableCopy && !$emailCopy)error_alert('Select at least one place to back up the table');
	$y=substr(date('Y'),-1);
	$d=str_pad(date('z'),3,'0',STR_PAD_LEFT);
	$s=date('His');
	if($makeTableCopy)q("CREATE TABLE finan_items_bk$y$d".'_'."$s SELECT * FROM finan_items");
	if($emailCopy){
		$str=array_to_csv(q("SELECT * FROM finan_items", O_ARRAY));
		$name="finan_items_bk$y$d.csv";
		$path=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/tmp/'.$name;
		$fp=fopen($path,'w');
		fwrite($fp,$str,strlen($str));
		fclose($fp);
		$fileArray=$path;
		$emailTo=sun('e');
		$emailSource=$_SERVER['DOCUMENT_ROOT'].'/gf5/console/emails/email_9000_export_attach.php';
		require($_SERVER['DOCUMENT_ROOT'] . '/components/emailsender_03.php');
	}
	error_alert('Backup complete');
}
if($submode=='batchUpdate'){

	error_alert('here');
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Batch Updater - '.$AcctCompanyName?></title>



<link id="cssUndoHTML" rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="../../site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="../../Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
#backup{
	border:1px solid #ccc;
	padding:15px;
	border-radius:7px;
	background-color:cornsilk;
	margin:10px 0px;
	}
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/jquery.js"></script>
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

$(document).ready(function(){
	$('#beginBackup').click(function(){
		window.open('tools_batch_updater.php?mode=makeBackup&makeTableCopy='+g('makeTableCopy').value+'&emailCopy='+g('emailCopy').value,'w2');
	});
	$('#createBatch').click(function(){
		o=this;
		$('#vendors input[type=checkbox]').each(function(){
			this.disabled=!o.checked;
		});
	});
});
</script>


</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onsubmit="return beginSubmit();">
<?php }?>
<div id="header">


</div>
<div id="mainBody">

<div id="batchUpdate">

<?php
if(!$refreshComponentOnly){
	?>
	<style type="text/css">
	blockquote{
		border:none;
		font-family:inherit;
		font-size:100%;
		}
	</style>
	<script language="javascript" type="text/javascript">
	</script>
	<?php
}
?>
<h2>Batch Update Tool</h2>
<p class="gray">This is where you upload a CSV file to update mutliple records at a time. This page will accept a file in CSV format, update the records, and create a batch for each vendor you select.  Unselect all vendors if you wish to only perform a data update.<br />
  <br />
The CSV file MUST HAVE field names in the first row; the first row will be used to determine fields that match the items/products table.  If a column labeled &quot;ID&quot; is present, it will be used as the reference to update a record.  If not, a field named SKU (part number) will be used.  Updates will not run without at least one of these fields present.[1]<br />
</p>

<!--
    <p>1. call-now of a registered process of <br />
      2. pull down the SKU's in the gap<br />
      3. add a few maps, see what is going on</p>
    <p>4. this interface</p>
<ol>
  <li>make backup working</li>
  <li>upload file and walk through the process
    <ol>
      <li>we want to create multiple batches</li>
      <li>we want to tie into the existing system and redirect to it, and FOCUS on it i.e. highlight it in red</li>
      <li>batch report should include errors - this is just like a import, but there are rules
        <ol>
          <li>precedence on the primary key</li>
          <li>sku can not disrupt other products</li>
          <li>other fields can be flagged unique [internally]</li>
          <li>here's a good time to get errors and reporrts in place - for example data truncation        </li>
        </ol>
      </li>
      </ol>
  </li>
  </ol>
<p>&nbsp;  </p>
  <p>[hide these instructions] </p>
  <p>&nbsp;</p>
-->

<div id="backup">
<img src="/images/i/alert01.gif" width="50" height="50" class="fl" />
	<strong class="red">WARNING!</strong> You can do great damage to your data if you do not know what you are doing or if your CSV file is incorrect.  We STRONGLY RECOMMEND that you make a backup NOW.<br />
	<br class="cb" />
	<label><input name="makeTableCopy" type="checkbox" id="makeTableCopy" value="1" checked="checked" /> 
	make a table copy</label>
	&nbsp;&nbsp; 
	<label><input name="emailCopy" type="checkbox" id="emailCopy" value="1" checked="checked" /> 
	email me a copy</label><br /><br />
	<input name="beginBackup" type="button" id="beginBackup" value="Begin Product Backup" />

</div>



	<p>Select a file to upload: 
	<input name="uploadFile_1" type="file" id="uploadFile_1" />
	</p>
	<h2>Options</h2>
	<p>    <br />
	<label><input name="createBatch" type="checkbox" id="createBatch" value="1" checked="checked" /> 
	Create an export batch for the following vendors:</label>
	<blockquote id="vendors">
	<?php
	foreach($vendors['settings'] as $n=>$v){
		?><label>
		<input type="checkbox" name="vendors[<?php echo $n;?>]" id="vendors[<?php echo $n;?>]" value="<?php echo $n;?>" onchange="dChge(this);" checked="checked" /> <?php echo $n;?>
		</label><br />
		<?php
	}
	?>
	</blockquote>
	
	<label>
	<input type="checkbox" name="updateTimeStamp" id="updateTimeStamp" value="1" onchange="dChge(this);" checked="checked" /> Update the edit date (timestamp) of records that are changed</label>
	<br />
	<span class="gray">(If you check this option, the edit date will be the EditDate field you upload, OR the second you upload the file if EditDate is blank. You (<?php echo sun();?>) will also be listed as the last editor of that record, or the &quot;Editor&quot; field value if present)</span>
	<br />
	<label><input type="checkbox" name="updateTimeStamp" id="updateTimeStamp" value="1" onchange="dChge(this);" checked="checked" /> Email me </label>
	<blockquote>
	<label><input name="emailMe" type="radio" value="2" />upon completion</label> 
	&nbsp;&nbsp; 
	<label><input name="emailMe" type="radio" value="1" checked="checked" />only if errors are present</label>
	</blockquote>
	
		
	
    <input type="submit" name="Submit" value="Begin Update" />

    <input name="mode" type="hidden" id="mode" value="refreshComponent" />
    <input name="submode" type="hidden" id="submode" value="batchUpdate" />
    <input name="suppressPrintEnv" type="hidden" id="suppressPrintEnv" value="1" />
	<input type="hidden" name="component" value="<?php
	$loc='../tools_batch_updater.php';
	echo'batchUpdate:'.$loc.':'.md5($loc.$MASTER_PASSWORD);?>" />


    <br />
<br />
<p>[1]If you use an ID as the reference field, the system will not allow you to change a SKU if it matches a SKU from a different product.</p>
</div>

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