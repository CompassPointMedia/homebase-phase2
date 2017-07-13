<?php
/* 2008-05-07 - this is a more user-friendly noaccess message including diagnosis, solution, help and contact */

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<style>
#main{
	background-color:ALICEBLUE;
	border:1px solid #444;
	margin:25px auto;
	width:60%;
	padding:35px;
	}
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<?php if(false){ ?>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<?php } ?>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $title?$title:'You do not have access to this page'?></title>
</head>

<body>
<div id="main">
<?php
if($component=='unspecified'){
	?>
	<h1>You do not have access to this page</h1>
	<input type="button" name="Button" value="Close" onclick="window.close();" />
	<?php 
}else if($component=='fosterhome'){
	?>
	<h2>You do not have access to this foster home</h2>
	<p>Reason: you are currently not assigned to the home.</p>
	<p>
	<input type="button" name="Button" value="Close" onclick="window.close();" />
	</p><?php
}else if($component=='parent'){
	?>
	<h2>You do not have access to this parent's record</h2>
	<p>Reason: If you are a case manager, you are not assigned to the home this parent is CURRENTLY in.  If you are a foster parent, you can only view your own record (you may not view a spouse's record as well).<br />
	If you are a <?php echo strtolower($wordProgramDirector);?>, you can only view a parent's full record if you are over a case manager who is assigned CURRENTLY assigned to that parent's foster home.<br />
	<br />
	Please contact staff if you have received this message in error.
	</p>
	<p>
	<input type="button" name="Button" value="Close" onclick="window.close();" />
	</p><?php
}else if($component=='incident report' || $component=='restraint report'){
	?>
	<h2>You do not have access to this report or progress note</h2>
	<p>Reason: If you are a case manager, you are not assigned to the home this child is CURRENTLY in.  If you are a foster parent, you are currently not assigned to the home the child is in.<br />
	If you are a <?php echo strtolower($wordProgramDirector);?>, you can only view a parent's full record if you are over a case manager who is assigned CURRENTLY assigned to that parent's foster home.<br />
	<br />
	Please contact staff if you have received this message in error.
	</p>
	<p>
	<input type="button" name="Button" value="Close" onclick="window.close();" />
	</p><?php
}else if($component=='loclist'){
	?>
	<h2>You Do Not Have Access to This Page</h2>
	<p>Reason: Only case manager and above have access to Level of Care contract information.  If you are able to you may sign out and sign back in as an administrator.  If you have any questions or if you have received this message in error, please contact staff.</p>
	<br  />
	<input type="button" name="Button" value="Close" onclick="window.close();" />
	<input type="button" name="Button" value="Close and Sign Out" onclick="window.close();window.opener.location='login/index.php?logout=1&scr='+escape(window.opener.location);" />
	
	<?php
}else if($component=='payoutprepreport'){
	?>
	<h2>You do not have access to this report and feature</h2>
	<p>Reason: If you are a parent or therapist, you are not allowed to view reports on child Levels of Care.<br />
	If you are a case manager or <?php echo strtolower($wordProgramDirector);?>, you should be able to see this report, but only for children in your care.<br />
	<br />
	Please contact staff if you have received this message in error.
	</p>
	<p>
	<input type="button" name="Button" value="Close" onclick="window.close();" />
	</p><?php
}
?>
</div>
</body>
</html><?php
//always exit
exit;
?>