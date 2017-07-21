<?php
require('config.php');

require('config.master.php');



if($logout=='1'){
	unset($_SESSION['special'][$SUPER_MASTER_DATABASE]['adminMode']);
	header('Location: '.stripslashes($src));
	?>
	redirecting..
	<script>
	window.location='<?php echo stripslashes($src)?>';
	</script><?php
	exit;
}else if($UN==$SUPER_MASTER_USERNAME && $PW==$SUPER_MASTER_PASSWORD){
	$_SESSION['special'][$SUPER_MASTER_DATABASE]['adminMode']=1;
	$location=($src ? stripslashes($src) : 'index.php');
	header('Location: '.$location);
	?><script>window.location='<?php echo $location?>'</script><?php
	exit;
}else if(strlen($UN.$PW)){
	$error=true;
}


$hideCtrlSection=true;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>GL Franchise; Admin Page</title>



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

		
		<h2 class="chamberheader">Administrative Access</h2>
		
		<p>This feature is for Great Locations Staff administrators.
			If	you	have any questions, please contact the webmaster or Great Locations Staff.</p>
		<p>&nbsp;</p>
		<?php if($error){ ?>
		<div style="color:red;font-weight:bold;">Your username or password is incorrect</div>
		<?php } ?>
		<p>&nbsp;</p>
		<p>Enter your username and password:</p>
		<form name="form1" id="form1" method="post" action="">
			<input name="UN" type="text" id="UN" />
			<br />
			<input name="PW" type="password" id="PW" />
			<br />
			<input type="submit" name="Submit" value="Submit" />
			<input type="hidden" name="src" id="src" value="<?php echo stripslashes($src)?>" />
		</form>
		

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
?>