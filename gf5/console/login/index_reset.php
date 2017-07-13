<?php
//2003-09-20: lock out include page view by itself
!$_SESSION[testMode] && substr(__FILE__,-(strlen($_SERVER['PHP_SELF'])))==$_SERVER['PHP_SELF']?
exit('-include page locked'):'';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Reset Password</title>



<link rel="stylesheet" href="../../../site-local/undohtml2.css" type="text/css" />
<link rel="stylesheet" href="../../../site-local/gf5_simple.css" type="text/css" />
<style>
/** CSS Declarations for this page **/
#signinStatus{
	visibility:hidden;
	}
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle .02 coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
var ResourceToken='<?php echo $ResourceToken;?>';
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<?php 
$link='/site-local/gl_extension_'.$GCUserName.'.css';
if(file_exists($_SERVER['DOCUMENT_ROOT'].$link)){ ?>
<link id="cssExtension" rel="stylesheet" type="text/css" href="<?php echo $link?>" />
<?php } ?>
</head>

<body>
<div id="mainWrap">
	<?php require($_SERVER['DOCUMENT_ROOT'].'/gf5/console/gf_header_login_002.php');?>
	
	<div id="mainBody">
	<style type="text/css">
	.web20form1{
		background-color:oldlace;
		padding:20px;
		}
	.web20form1 .errbox{
		color:DARKRED;
		border:1px dotted darkred;
		padding:30px;
		margin:10px 15px;
		}
	.web20form1, .web20form1 .btn{
		font-size:109%;
		font-weight:400;
		}
	.web20form1 .box{
		font-size:109%;
		padding:4px;
		}
	.web20form1 td{
		vertical-align:middle;
		font-size:111%;
		}
	
	</style>
 
	<div class="web20form1">
	<h3>Password Reset</h3>
	<p>&nbsp;</p>
	<p>You need to reset your password before continuing</p>

	<?php
	if($err){
		?><div class="errbox"><?php echo $err;?></div><?php
	}
	?>

	<p>&nbsp; </p>
	<form action="index.php" method="post" name="form1" id="form1">
		  <table cellpadding="3" cellspacing="0">
			<tr> 
				<td>Enter new password:</td>
				<td>
				  <input type="password" name="PW" value="" class="box" /></td>
			</tr>
			<tr> 
				<td>Retype new password:</td>
				<td><input type="password" name="nullPW" value="" class="box" /></td>
			</tr>
			<tr> 
			  <td colspan="2">  
<input type="submit" name="Submit" value="Reset Password" class="btn" />
					&nbsp;&nbsp;
			    <input type="hidden" name="src" value="<?php echo h(stripslashes($src));?>" />
			    <input name="action" type="hidden" id="action" value="resetpassword" /></td>
			</tr>
		</table>
		<br />
	</form>
	</div>
	</div>
	<div id="footer">
	<div id="footer">
<p>Home Base&reg; Management DB ver. 5.0 - All rights reserved &copy;2006-<?php echo date('Y');?></p>
</div>

	</div>
	<?php if(!$hideCtrlSection){ ?>
	<div id="showTester" title="Javascript Tester" onclick="g('tester').style.display='block';">&nbsp;</div>
	<div id="tester" >
		<a href="#" onclick="g('ctrlSection').style.display=op[g('ctrlSection').style.display]; return false;">iframes</a><br />
		<textarea name="test" cols="65" rows="4" id="test">g('field').value</textarea><br />
		<input type="button" name="button" value="Test" onclick="jsEval(g('test').value);"><br />
		<textarea id="result" name="result" cols="65" rows="3" ></textarea>
	</div>
	<div id="ctrlSection" style="display:<?php echo $testModeC ? 'block':'none'?>">
		<iframe name="w1" src="/Library/js/blank.htm"></iframe>
		<iframe name="w2" src="/Library/js/blank.htm"></iframe>
		<iframe name="w3" src="/Library/js/blank.htm"></iframe>
		<iframe name="w4" src="/Library/js/blank.htm"></iframe>
	</div>
	<?php } ?>
</div>
</body>
</html><?php page_end()?>