<?php
require('config.php');

require('config.master.php');

$hideCtrlSection=true;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $AcctCompanyName?></title>
<link rel="stylesheet" href="site-local/undohtml2.css" type="text/css">
<link rel="stylesheet" href="site-local/gf5_simple.css" type="text/css">
<link rel="stylesheet" href="site-local/local.css" type="text/css">
<style>
body{
	/* background-color:#502E6D; */
	}
#mainwrap{
	position:relative;
	margin:0px auto;
	/* background-image:url("images/assets/home_splash_04.jpg");
	background-repeat:no-repeat;
	background-position:center center; */
	width:900px;
	}
#login{
	position:absolute;
	left:103px;
	top:12px;
	height:60px;
	width:600px;
	background-color:#FFF;
	}
#login2{
	position:absolute;
	left:103px;
	top:275px;
	-moz-opacity:.9;
	width:600px;
	background-color:#FFF;
	}
form{
	background-color:oldlace;
	padding:25px;
	margin:25px;
	font-size:179%;
	}
input{
	font-size:129%;
	font-weight:400;
	}
</style>
<script id="jsglobal" type="text/javascript" language="javascript" src="Library/js/global_04_i1.js"></script>
<script id="jscommon" type="text/javascript" language="javascript" src="Library/js/common_04_i1.js"></script>
<script id="jslocal" type="text/javascript" language="javascript" src="site-local/local.js"></script>
<script>
var thispage='index.php';
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
	<div id="mainwrap">
		<div id="login">
	    
		<h2><?php echo $AcctCompanyName;?> Database - Log In for Account <?php echo $GCUserName?></h2>
			<div style="margin:-top:-5px;text-align:right;padding-right:20px;">
				<form name="form1" action="gf5/console/login/index.php" method="post">
					Username: 
					  <input name="UN" type="text" id="UN" size="12" />
					  <script language="javascript" type="text/javascript">
					  document.getElementById('UN').focus();
					  document.getElementById('UN').select();
					  </script> <br />
				<br />

				  &nbsp;&nbsp;Password: 
				  <input name="PW" type="password" id="PW" size="12" /> 
				  <input type="image" name="imageField" src="images/assets/go_04.jpg" />
				</form>
			</div>
		</div>
		<div id="login2">
	    <h3 style="padding-left:15px;">Support and Contact Resources</h3>
	    <br />
			<div style="overflow:scroll;height:250px;padding:0px 15px;border:1px dotted #272727;margin-top:7px;">
				<p><strong>You can call for support at any time! Contact Samuel Fullman at (512) 754-7927, or alternately (310) 701-3129, for assistance</strong>. My email is <a href="mailto:<?php echo $techSupportEmail?>"><?php echo $techSupportEmail?></a>. For anything short of emergencies, however, please use the contact form link to the right so we have something in writing to follow up on.
					</p>
				</p>
				<p>&nbsp;</p>
				<h3>Before Calling</h3>
				<p> However, you are encouraged to consult the available help being developed for the Great Locations system. Each main page has a Help &gt; With this Page link in the upper right as shown in the diagram. Pages that &quot;pop up&quot; normally have a help tab which is also shown in the diagram below. Great Locations Help is currently like Wikipedia, you may contribute to it yourself! So, help the foster care community out if a question you have has been solved.</p>
				<p>&nbsp;</p>
				<h3>What You'll Need to Have</h3>
				<p>The Great Locations database supports <a href="http://www.getfirefox.com"><strong>Mozilla Firefox</strong></a> only! Not all features may work with Internet Explorer. Also, if you call us, you will need <a href="http://www.teamviewer.com"><strong>Teamviewer</strong></a> installed on your computer for doing a remote desktop session with your computer; the best way to assist you is to do so hands-on. Finally, a fast internet connection is recommended but not required. If you encounter difficulties while using a Dial-up service, please let us know about it. </p>
				<p>&nbsp;</p>
				<h3>Upcoming Tutorials</h3>
				<p>Our tentative plans for the coming months includes creating screen-and-voice   capture tutorials for processes like setting up foster parents and children, creating progress notes and incident reports, and exporting data from the database. Please check back.</p>			
			</div>
		</div>
	</div>
</body>
</html>
