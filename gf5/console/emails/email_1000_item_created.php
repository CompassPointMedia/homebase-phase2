<?php
/*

default object array = $recipient => Email, UN
default ID => $ID

2010-06-01: this is a shell email designed to be sent out with sendObject logic.  Modify as little as possible and document any changes which could be modular.  See /gf5/console/components/comp_00_sendlogic_v101.php for more information

*/

$systemEmail['content_disposition']='html';
$systemEmail['subject']='New Map created by '.sun('fnln');
$systemEmail['from']='do-not-reply@'.$_SERVER['HTTP_HOST'];
$systemEmail['to']=$recipient['un_email'];

$linkBase=($apSettings['secureProtocolPresent'] ? 'https' : 'http').'://'.$GCUserName.'.fantasticshop.com/gf5/console/login/?UN='.$recipient['UN'].'&src='.urlencode('../root_drr.php?');
$link=$linkBase.urlencode('ID='.$ID);
if(!$office){
	//get the default office for the system for right now
	$office=q("SELECT * FROM bais_offices ORDER BY IF(PrimaryOffice=1,1,2) LIMIT 1", O_ROW);
}

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
<div id="header">
	<h2>
	<?php if(file_exists($_SERVER['DOCUMENT_ROOT'].'/images/logos/'.$GCUserName.'.gif')){?>
	<img src="http://<?php echo $GCUserName?>.fantasticshop.com/images/logos/<?php echo $GCUserName?>.gif" alt="logo" /> 
	<?php } ?>
	<?php echo $AcctCompanyName;?>
	</h2>
	<?php
	//get the office
	echo '<strong>';
	echo $office['Address'] . '<br />';
	echo $office['City'].', '.$office['State']. '  '.$office['Zip'] . '<br />';
	echo '</strong>';
	echo $office['WorkPhone'] . ' (p)<br />';
	if($office['Fax'])echo $office['Fax'] . ' (f)';
	?>
</div>



<?php 
if($sendObjectThisIsMe){
}else{
	?><p>
	Dear <?php echo $recipient['FirstName'] .' ' . $recipient['LastName'] ?>,<br />
	</p><?php
}
?>
<p>
<?php echo $sendObjectThisIsMe ? 'You have submitted a new item ' : 'A new item has been submitted by '.sun('fnln');?>: <strong><?php echo stripslashes($Name);?></strong><br />
<br />
SKU: <?php echo stripslashes($SKU);?><br />
Category: <?php echo stripslashes($Category);?><br />
Description: <?php echo stripslashes($Description);?><br />
Long Description: <?php echo stripslashes($LongDescription);?><br />
<br />
To access this directly <a href="<?php echo $link;?>">click on this link</a> <br />
<p>Have a nice day,<br />
<?php echo ($emailClosingTitle ? $emailClosingTitle : ($systemEmail['closingTitle'] ? $systemEmail['closingTitle'] : $AcctCompanyName));?><br />
<br />
Tech support: <a href="mailto:help@<?php echo $_SERVER['SERVER_NAME'];?>">help@<?php echo $_SERVER['SERVER_NAME'];?></a><br />
<em class="gray">(<?php $stat=stat(__FILE__);
echo str_replace('.php','',end(explode('/',__FILE__))) . ' - last revised '.date('F jS \a\t g:iA',$stat['mtime']);?>)</em><br />
</p>
</body>
</html><?php
$out=ob_get_contents();
ob_end_clean();
$out=str_replace('src="/','src="'.($apSettings['secureProtocolPresent'] ? 'https':'http').'://'.$_SERVER['HTTP_HOST'].'/',$out);
$out=str_replace('src="../','src="'.($apSettings['secureProtocolPresent'] ? 'https':'http').'://'.$_SERVER['HTTP_HOST'].'/',$out);
echo $out;
?>