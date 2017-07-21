<?php
require('config.php');

require('config.master.php');




$hideCtrlSection=true;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>GL Franchise; Page not found</title>



<link id="undohtmlCSS" rel="stylesheet" type="text/css" href="site-local/undohtml2.css" />
<link rel="stylesheet" href="site-local/gc_simple.css" type="text/css" />
<style type="text/css">
</style>

<script src="Library/js/global_04_i1.js" id="jsglobal" language="JavaScript" type="text/javascript"></script>
<script src="Library/js/common_04_i1.js" id="jscommon" language="JavaScript" type="text/javascript"></script>
<script src="Library/js/forms_04_i1.js" id="jsforms" language="JavaScript" type="text/javascript"></script>
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

<body>
<div id="mainWrap">
	<div id="topRegion"><div id="signin">
	<?php
	$local=$_SESSION['cnx'][$rd['MASTER_DATABASE']];
	if($local){
		//say hello
		?>
		hello <?php echo $_SESSION['admin']['firstName'] . ' ' . $_SESSION['admin']['lastName']?> [<a href="/gf5/console/login/index.php?logout=1">sign-out</a>]
		<?php
	}else{
		//ask them to sign in
		?>
		<a href="Library/signin.php">member sign-in</a>
		<?php
	}
	?>
</div>
<h2><a href="Library/index.php"><?php echo $AcctCompanyName;?></a></h2>
<div id="glNav">
<a href="Library/greatlocations-signup.php">Sign Up</a> | <a href="Library/greatlocations-about.php">About GL Franchise</a> | <a href="Library/greatlocations-techsupport-main.php">Tech Support</a> | <a href="Library/greatlocations-contact.php">Contact</a>
</div></div>
	<div id="mainBody">

		
		404 not found under development 
		

	</div>
	<div id="footer"><?php
if(!$hideSiteEditorLink){
	if($siteEditorLinkType=='cgi'){
		$link=(stristr($_SERVER['SERVER_NAME'],'relatebase-rfm.com') ? '/~'.$MASTER_DATABASE : '').'/cgi/login.php?'.($adminMode ? 'logout=1&' : '').'src='.urlencode($REQUEST_URI);
	}else if($siteEditorLinkType=='console'){
		$link=(stristr($_SERVER['SERVER_NAME'],'relatebase-rfm.com') ? '/~'.$MASTER_DATABASE : '').'/console/admin.php?'.($adminMode ? 'logout=1&' : '').'src='.urlencode($REQUEST_URI);
	}else{
		$link=(stristr($_SERVER['SERVER_NAME'],'relatebase-rfm.com') ? '/~'.$MASTER_DATABASE : '').'/admin.php?'.($adminMode ? 'logout=1&' : '').'src='.urlencode($REQUEST_URI);
	}
?>
<span class="editor">[<a href="<?php echo $link?>" title="<?php echo $siteName?> real-time site editor"><?php echo $adminMode?'Leave ':''?>Site Editor</a>]</span>
<?php } ?>
<br />
Great Locations &copy;2009 - Developed by <a href="http://www.compasspoint-sw.com">Compass Point Media</a></div>
</div>
</body>
</html><?php
//end code to come
