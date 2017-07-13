<?php
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

$Y=date('Y');

if(minroles()>ROLE_AGENT)exit('You do not have access to this');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $PageTitle='History of Changes for: '.preg_replace('/([a-z])([A-Z])/','$1 $2',$SubObjectName);?></title>

<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />


<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle .02 coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
var ResourceToken='<?php echo $ResourceToken;?>';

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already

var isEscapable=1; //1 means confirm if detectChange, 2 means escape regardless of changes

</script>

</head>

<body>
<div id="mainBody">
<h1><?php echo $PageTitle;?></h1>
<p class="gray">Does not include the current value; change history shows from newest (top) to oldest (bottom)</p>
<?php
if($a=q("SELECT m.*, un_firstname, un_lastname, un_username, un_email, st_unusername FROM gf_modifications m LEFT JOIN bais_universal u ON m.Creator=u.un_username LEFT JOIN bais_staff s ON u.un_username=s.st_unusername WHERE Objects_ID=$Objects_ID AND SubObjectName='$SubObjectName' ORDER BY EditDate DESC", O_ARRAY)){
	?><table class="yat">
	<thead>
	<tr>
		<th>Date</th>
		<th>Value</th>
		<th>Done by</th>
		<th>Notes</th>
	</tr>
	</thead>
	<?php
	foreach($a as $v){
		extract($v);
		?><tr>
		<td><?php echo str_replace("/$Y",'', date('n/j/Y \a\t g:iA',strtotime($EditDate)));?></td>
		<td class="tar"><?php echo $Value;?></td>
		<td><?php echo $un_username && $un_firstname ? $un_firstname . ' ' . $un_lastname : $Creator;?></td>
		<td><?php echo $Notes;?>&nbsp;</td>
		</tr><?php
	}
	?>
	</table><?php
}else{
	mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='Unable to find history for field'),$fromHdrBugs);
	?><p>Unable to find history for that value</p><?php
}
?>
	<br>
	<input type="button" name="Button" value="Close" onclick="window.close();" />
	<br />
</div>
</body>
</html>
