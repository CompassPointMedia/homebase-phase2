<?php
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='generic';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');



$a=q("explain finan_items", O_ARRAY);
foreach($a as $n=>$v){
	unset($a[$n]);
	$v['field']=strtolower($v['Field']);
	$a[strtolower($v['Field'])]=$v;
}
prn($a,1);

$i=0;
while($r=fgetcsv($fp,10000)){
	$i++;
	if($i==1){
		foreach($r as $n=>$v){
			
		}
	}
}


exit;
$db=q("SHOW TABLES", O_ARRAY);
foreach($db as $n=>$v){
	$table=$v['Tables_in_cpm180_hmr'];
	ob_start();
	$a=q("SHOW CREATE VIEW $table", O_ROW,ERR_ECHO);
	$err=ob_get_contents();
	ob_end_clean();
	if($err)continue;
	$str=($a['Create View']);
	if(strstr($str,'HMR_UPCCheckDigit') && !strstr($str,'`r`.`ID` + 1')){
		$str=str_replace('`i`.`HMR_UPCCheckDigit`,`r`.`ID`,1','`i`.`HMR_UPCCheckDigit`,`r`.`ID`+1,1',$str);
		$str=str_replace('ALGORITHM=UNDEFINED DEFINER=`cpm180`@`%` SQL SECURITY DEFINER ','OR REPLACE ',$str);
		prn($str.';');
	}
}
exit;
prn($db,1);



if($mode=='updateItem'){
	exit(here);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<style type="text/css">
.done{
	background-color:#ccc;
	}
</style>
<script language="javascript" type="text/javascript" src="/Library/js/jquery.js"></script>
<script language="javascript" type="text/javascript">
var armed=false;
var saymode='test';
function say(text){
	if(saymode!=='test')return;
	if(typeof this.i=='undefined')this.i=0;
	this.i++;
	$('#console').prepend('<div>'+new Date()+' ['+i+'] '+text+'</div>');
}
function autosave(){
	if(armed){
		say('submitting..');
		$('#autosave').val(1);
		$.ajax({
			url: 'dev.autosave.php',
			data: $('#form1').serialize(),
			method: 'POST',
		}).done(function(data){
			say('here: '+data);
			$('#cs').html('saved');
			armed=false;
		});
		$('#autosave').val('');
	}else{
		say('no changes');
	}
	setTimeout('autosave()',7000);
}
function arm(){
	armed=true;
	$('#cs').html('active');
}
function showAutosave(){
	say('autosaved');
}

$(document).ready(function(){
/*
clarify fields really well
*/
	$('input[type=text],textarea').keyup(arm);
	$('input[type=checkbox]').click(arm);
	$('select').change(arm);
	setTimeout('autosave()',7000);
});
</script>
</head>

<body>
<table cellpadding="0">
  <tr>
    <td>current status: <div id="cs">idle</div></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>form</td>
    <td>console</td>
  </tr>
  <tr>
    <td><form id="form1" name="form1" method="post" action="">
      checkbox 
      <input type="checkbox" name="checkbox" value="checkbox" />
      <br />
      input text 
      <input type="text" name="textfield" />
      <br />
      input textarea<br />
      <textarea name="textarea" cols="35" rows="5"></textarea>
      <br />
      <select name="select">
        <option value="1">option 1 of 2</option>
        <option value="2">option 2 of 2</option>
      </select>
      <br />
      <input type="submit" name="Submit" value="Submit" />
                        <input name="autosave" type="hidden" id="autosave" />
                        <input name="mode" type="hidden" id="mode" value="updateItem" />
    </form>    </td>
    <td valign="top"><div id="console"> </div></td>
  </tr>
</table>
</body>
</html>
