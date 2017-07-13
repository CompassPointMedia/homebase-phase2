<?php
/*
from email_08_submitbulletin.php in Sim pleFost ercare
*/

$systemEmail['content_disposition']='html';
$systemEmail['subject']='specific notice from '.$AcctCompanyAbbr;
$systemEmail['from']='do-not-reply@'.$_SERVER['HTTP_HOST'];
$systemEmail['to']=($Shunt ? $ShuntEmail : $v['Email']);

$linkBase=($apSettings['secureProtocolPresent'] ? 'https' : 'http').'://'.$GCUserName.'.fantasticshop.com/gf5/console/login/?UN='.$recipient['UN'].'&src='.urlencode('../root_drr.php?');


ob_start();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
body{
	padding:10px 20px;
	}
body, td{
	font-family:Arial, Helvetica, sans-serif;
	font-size:13px;
	}
#header{
	font-family:Georgia, "Times New Roman", Times, serif;
	}
.comment{
	background-color:oldlace;
	border:1px solid #333;
	padding:10px;
	}
.fr{
	float:right;
	margin:0px 0px 5px 10px;
	}
.fl{
	float:left;
	margin:0px 10px 5px 0px;
	}
.cb{
	clear:both;
	height:0px;
	}
.gray{
	color:#888;
	}
th{
	text-align:left;
	}
.yat{
	border-collapse:collapse;
	}
.yat th{
	padding:3px 5px 1px 2px;
	}
.yat td{
	padding:3px 5px 1px 2px;
	}
.yat td, .yat td.printhide{
	padding:3px 5px 1px 2px;
	border-bottom:1px solid #ddd;
	}
.yat .alt td{
	background-color:whitesmoke;
	}
.yat .nobo td{
	border-bottom:none;
	}
.yat .topborder td{
	border-top:1px solid #000;
	}
.balloon1{
	border:1px dotted darkgreen;
	background-color:papayawhip;
	padding:15px;
	font-weight:400;
	font-family:Georgia, "Times New Roman", Times, serif;
	}
</style>
<title><?php echo $emailSubj ? $emailSubj : $systemEmail['subject']?></title>
</head>

<body class="email">


<?php if(!$SendCC){ ?>Hi <?php echo $v['FirstName'] . ' ' . $v['LastName']?>,<?php }?><br /><br />

<?php echo $_SESSION['admin']['firstName'] . ' ' . $_SESSION['admin']['lastName']?> has submitted a bulletin and has invited you to read it.
<br />
<br />
Importance: <strong><?php echo $Importance;?></strong><br />
<br />
Title: <strong><?php echo stripslashes($Title)?></strong><br />
<?php if($Description && strtolower($Description)!==strtolower($Title)){ ?>Summary: <?php echo stripslashes($Description)?><br /><br />
<?php } ?>
<?php if($EmailContents){ ?>
<?php echo stripslashes($Contents); ?>
<?php } ?>
<br />
<br />
<?php
if($Shunt){
	//no link - no id to link to
}else{
	?>
	You may sign in by clicking the following link:<br />
	<a href="<?php echo $linkBase . urlencode('node=read_bulletins&Bulletins_ID='.$ID);?>"><?php echo $linkBase . urlencode('&node=read_bulletins&Bulletins_ID='.$ID);?></a>
	<?php
}
?>

<br />
<br />
Tech support: <a href="mailto:help@<?php echo $_SERVER['SERVER_NAME'];?>">help@<?php echo $_SERVER['SERVER_NAME'];?></a><br />
<em class="gray">(<?php $stat=stat(__FILE__);
echo str_replace('.php','',end(explode('/',__FILE__))) . ' - last revised '.date('F jS \a\t g:iA',$stat['mtime']);?>)</em><br />
</body>
</html><?php
$out=ob_get_contents();
ob_end_clean();
$out=str_replace('src="/','src="'.($apSettings['secureProtocolPresent'] ? 'https':'http').'://'.$_SERVER['HTTP_HOST'].'/',$out);
$out=str_replace('src="../','src="'.($apSettings['secureProtocolPresent'] ? 'https':'http').'://'.$_SERVER['HTTP_HOST'].'/',$out);
echo $out;
?>