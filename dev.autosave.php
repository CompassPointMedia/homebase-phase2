<?php
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='generic';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');



if($mode=='updateItem'){
	exit(here);
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
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

$(document).ready(function(){
/*
clarify fields really well
*/
	
	function arm(){
		armed=true;
		$('#cs').html('active');
	}
	function autosave(){
		if(armed){
			$('#autosave').val(1);
			$.ajax({
				url: 'dev.autosave.php',
				data: $('#form1').serialize(),
				method: 'POST',
			}).done(function(data){
				alert('here');
			});
			$('#autosave').val('');
		}
		setTimeout('autosave()',7000);
	}
	function showAutosave(){
		say('autosaved');
	}
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
