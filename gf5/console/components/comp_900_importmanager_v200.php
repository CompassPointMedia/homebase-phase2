<?php if(false){ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>

<body>
<?php } ?>
<?php
if($display=='form'){
	if($step==1){
		?><div>
		select a file or datasource to import<br />
		say something: 
		<input name="something1" type="text" id="something1" value="<?php echo h($_SESSION['special']['processes'][$idx]['data'][$step]['something1']);?>" onchange="dChge(this);" />
	</div>
<?php
	}else if($step==2){
		?><div>
		select import table(s) or location<br />
		say something: 
		<input name="something2" type="text" id="something2" value="<?php echo h($_SESSION['special']['processes'][$idx]['data'][$step]['something2']);?>" onchange="dChge(this);" />
		</div><?php
	}else if($step==3){
		?><div>
		map the data!<br />
		say something: 
		<input name="something3" type="text" id="something3" value="<?php echo h($_SESSION['special']['processes'][$idx]['data'][$step]['something3']);?>" onchange="dChge(this);" />
		</div><?php
	}else if($step==4){
		?><div>
		advanced settings<br />
		say something: 
		<input name="something4" type="text" id="something4" value="<?php echo h($_SESSION['special']['processes'][$idx]['data'][$step]['something4']);?>" onchange="dChge(this);" />
		</div><?php
	}else if($step==5){
		?><div>
		finished!<br />

		</div><?php
	}
}
?>
<?php if(false){ ?>
</body>
</html>
<?php } ?>