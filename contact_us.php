<?php

require('config.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Contact Us</title>

<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/glf_basic.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle .02 coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
var ResourceToken='<?php echo $ResourceToken;?>';

var talks=1; //whether this page broadcasts overall state changes which other pages listen for
var listens=0;
</script>




</head>

<body>
<div id="mainBody">
	<div id="header">	
		<a href="/"><img src="images/assets/homebaselogo.png" width="424" height="150" /></a>
		<br />
		<a href="/">home</a> | <a href="/what_is_homebase.php">what is home base</a> | <a href="/features.php">features</a> | <a href="/sign_up.php">sign up</a> | <a href="/contact_us.php">contact us</a>
  </div>
	<div id="mainSection">

	  <p>For now please contact us here:</p>
	  <table width="50%" cellpadding="3">
        <tr>
          <td>&nbsp;</td>
          <td>Samuel Fullman<br>
            Compass Point Media </td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td><?php js_email_encryptor('sfullman@compasspointmedia.com');?>
          <br />
          (512) 754-7927<br />
          (512) 938-9018 Mobile </td>
        </tr>
      </table>
	  <p>&nbsp;</p>
	  <p>&nbsp; </p>
	  <p></p>

	  <p>&nbsp;</p>

	</div>
	<div id="footer">

	  copyright 2009-<?php echo date('Y');?>

	</div>
</div>
</body>
</html>
