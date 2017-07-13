<?php
require('config.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Home Base Home Page</title>

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

		<div class="fr" style="border:1px solid #666; padding:15px; margin:0px 0px 15px 15px; background-color:cornsilk;">
		<form id="form1" name="form1" action="<?php 
		$a=explode('.',$_SERVER['SERVER_NAME']);
		echo $sn= $a[count($a)-2].'.'.$a[count($a)-1];
		?>/gf5/console/login" method="post">
			<script language="javascript" type="text/javascript">
			function formSub(){
				var acct=g('acct').value.toLowerCase();
                if(acct!='hmr' && acct!='art'){
                    alert('That is not a valid account name');
                    return false;
                }
                var url = 'http://'+acct+'.<?php echo $sn;?>/gf5/console/login/';
                console.log(url);
				sCookie('_acct_',acct);
				g('form1').setAttribute('action',url);
				return true;
			}
			</script>
		  <p> Staff Sign-in</p>
		  <p> Home Base user account:
            <input name="acct" type="text" id="acct" value="<?php echo $_COOKIE['_acct_'] ? $_COOKIE['_acct_'] : current(explode('.',str_replace('www.','',$_SERVER['SERVER_NAME'])));?>" />
		    <br />
		    <br />
		    Your username or email: 
		    <input name="UN" type="text" id="UN" />
		    <br />
		    Your password: 
		    <input name="PW" type="password" id="PW" />
		    <br />
		    <input type="submit" name="Submit" value="Sign In" onClick="return formSub()" />
		  </p>
		</form>
		</div>
	  <p>&nbsp;</p>
	  <p></p>

	  <p>&nbsp;</p>
    
	</div>
	<div id="footer">
		
	  copyright 2009-<?php echo date('Y');?>
    
	</div>
</div>
</body>
</html>
