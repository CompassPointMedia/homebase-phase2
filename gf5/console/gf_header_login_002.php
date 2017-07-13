<?php
if(!$hideHeader){ 
?><div id="headerContent1">
	<h2 class="mainHeader"><?php
	$logo=$_SERVER['DOCUMENT_ROOT'].'/images/logos/'.strtolower($GCUserName).'.gif';

	if(file_exists($logo) && $gis=getimagesize($logo)){
		?><img id="logoImg" src="/images/logos/<?php echo strtolower($GCUserName).'.gif'?>" width="<?php echo $gis[0]?>" height="<?php echo $gis[1]?>" align="logo" class="printhide" /><?php
	}
	?>
	<?php echo $AcctCompanyName?></h2>
	<div id="signinStatus"><?php
	if($_SESSION['admin']['userName']){
		?><span class="printhide" style="display:inline;">Hello</span> <?php echo htmlentities($_SESSION['admin']['firstName']. ' '. $_SESSION['admin']['lastName'])?>
		&nbsp;&nbsp;
		<script language="javascript" type="text/javascript">
		function switchAccounts(n){
			var l=window.location+'';
			l=l.replace('http://<?php echo $GCUserName;?>','http://'+n);
			if(l.indexOf('?')==-1)l+='?';
			l=l.replace(/&*UN=[^&]*/,'').replace(/&*authKey=[^&]*/,'').replace(/&*t=[^&]*/,'');
			l+=(l.substr(-1)!='?'?'&':'')+'UN=<?php echo sun();?>';
			l+='&authKey=<?php echo $_SESSION['special']['accountKeys']['authKey'];?>';
			l+='&t=<?php echo $_SESSION['special']['accountKeys']['t'];?>';
			l=l.replace(/\?&+/,'?');
			window.location=l;
			<?php /*
			var l=window.location+'';
			l2='/gf5/console/login/?passthrough=17&UN=<?php echo sun();?>&authKey=<?php echo $_SESSION['special']['accountKeys']['authKey'];?>&t=<?php echo $_SESSION['special']['accountKeys']['t'];?>&acct='+n+'&src='+escape(l);
			window.location=l2;
			*/
			?>
		}
		</script>
		<?php if(count($_SESSION['special']['accountList'])>1){ ?>
		<select onchange="switchAccounts(this.value);">
		<?php
		foreach($_SESSION['special']['accountList'] as $n=>$v){
			?><option value="<?php echo $n?>" <?php echo $GCUserName==$n?'selected':''?>><?php echo h($v);?></option><?php
		}
		?>
		</select>
		&nbsp;&nbsp;
		<?php } ?>
		<a class="printhide" href="/gf5/console/login/index.php?logout=1">Sign Out</a><?php
	}else{
		?><a class="printhide" href="/gf5/console/login/index.php?src=<?php echo urlencode($_SERVER['PHP_SELF'].($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''))?>"                >Sign In</a><?php
	}
	?><span class="screenhide">
	Page created <?php echo date('n/j/Y \a\t g:iA');?>
	</span></div>
	<div id="hdrClear"></div>
</div>
<?php if(!$hideHeader){ ?>
<div id="headerContent2" class="printhide"><div id="searchInset" class="fr">
<?php if(sun()){ ?>
<form name="searchForm" id="searchForm" method="get" action="/gf5/console/resources/bais_01_exe.php" target="w1">
	Current maps: <strong><?php echo q("SELECT count(*) FROM finan_items WHERE ResourceType IS NOT NULL", O_VALUE);?></strong>&nbsp;
	<input class="btnA" name="q" type="text" id="q" value="<?php echo h(stripslashes($q));?>">
	<input class="btnB" type="submit" name="button" id="button" value="Search">
	<input name="thispage" type="hidden" id="thispage" value="<?php echo $thispage?>">
	<input name="thisfolder" type="hidden" id="thisfolder" value="<?php echo $thisfolder?>">
	<input name="mode" type="hidden" id="mode" value="search">
</form>
<script language="javascript" type="text/javascript">g('q').focus();</script>
<?php } ?>
</div>
<div id="menuWrap1">
<?php if(false){
	?>
	<script language="javascript" type="text/javascript">
	/* 2010-11-01: this array works with SoThink menu creator; see function DHTMLMenu() in a_f and $DTHMLMenu array in auth_i2_v100.php */
	var _DHTMLMenuShow_=[<?php echo implode(',',DHTMLmenu())?><?php /*echo rtrim(str_repeat('1,',73),',')*/ ?>];
	var _DHTMLMenuIdx_=0;
	function DHMLMenuShow(){_DHTMLMenuIdx_++; return (_DHTMLMenuShow_[_DHTMLMenuIdx_-1] ? true : false);}
	</script>
	<script type="text/javascript" language="JavaScript1.2" src="/gf5/console/stm31.js"></script>
	<script type="text/javascript" language="JavaScript1.2" src="/gf5/console/stm31_output_<?php echo $GCUserName;?>.js"></script>
	<?php
}else{
	?><script language="javascript" type="text/javascript" src="/Library/js/jq/jquery.js"></script>
	<?php
	require($_SERVER['DOCUMENT_ROOT'] . '/components-juliet/menu_v260.php');
}
?>
</div>
</div>
<?php } //end hideHeader?>
<script language="javascript" type="text/javascript">
self.name='l0';
//undo sothink menu usurpation of onload event, then add its function to ours
window.onload=rb_onload;
AddOnloadCommand('st_onload()');
//call talk-listen center
AddOnloadCommand('talklisten()');
</script><?php
}
?>