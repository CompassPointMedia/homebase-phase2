<?php
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='generic';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');


$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>move files</title>

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
</script>

</head>

<body>
<div id="mainBody">
<span id="pending"><img src="/images/i/ani/ani-fb-orange.gif" width="16" height="11" /> Please be patient and do not close this window while export is processing.  You will be notified when the process is complete..</span>

<?php
set_time_limit(2*3600);//two hours
gmicrotime('exportstart');
ob_start();
if($a=q("SHOW TABLES IN cpm180_".$GCUserName, O_ARRAY)){
	foreach($a as $n=>$v){
		$table=$v['Tables_in_cpm180_'.$GCUserName];
		foreach(q("EXPLAIN ".$table,O_ARRAY) as $w){
			$data[$table]['fields'][$w['Field']]=$w['Field'];
		}
		echo '----------------'."\n";
		echo 'Fields for '.$table.': '."\n";
		echo '----------------'."\n";
		echo implode(', ',$data[$table]['fields'])."\n";
		if($b=q("SELECT * FROM $table LIMIT 100", O_ARRAY)){
			foreach($b as $w){
				foreach($w as $p=>$x){
					if(is_null($x))$w[$p]='NULL';
					if(preg_match('/[,\r\n]/',$x))$w[$p]='"'.str_replace('"','""',$p).'"';
				}
				echo implode(',',$w)."\n";
			}
		}
		
	}
}
gmicrotime('exportend');

$out=ob_get_contents();
ob_end_clean();
mail($_SESSION['admin']['email'],'Export on '.date('n/j/Y \a\t g:iA'),$out,'From: donotreply@fantasticshop.com');

?>
<script language="javascript" type="text/javascript">
g('pending').style.display='none';
</script>
<p>Export took <?php echo round($gmicrotime['exportend'] - $gmicrotime['exportstart'],2);?> seconds</p>
<p>Total size: <?php echo round(strlen($out)/1024,2);?>KB</p>
<p>Sent to: <?php echo $_SESSION['admin']['firstName'].' '.$_SESSION['admin']['lastName'].' ('.$_SESSION['admin']['email'].')';?> <br>
  <input type="button" name="Button" value="Close" onclick="window.close();" />
</p>
</div>

</body>
</html>
