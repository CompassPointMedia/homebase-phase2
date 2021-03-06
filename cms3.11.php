<?php
/*
TODO/requests
-------------
would be nice to have a search and replace tab to replace say 310-701-3129 with 512-938-9018, esp. by regular expression; also with parameter of how many versions back or how far back to go, and what languages to include

CMS Bridge version 3.11 - forked off 1/11/12 - multi-language support


cms3.11.php - 
2012-01-11
* hidden field "lang" now present and passes to CMSBUpdate()
* added lang column to history for clarity


*/

#require('./config.php'); //for all
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';

require('gf5/console/systeam/php/config.php');
require('gf5/console/resources/bais_00_includes.php');
if(minroles()<ROLE_AGENT)$adminMode=1;

$qx['defCnxMethod']=C_MASTER;


switch($mode){
	case 'emailEmergency':
		mail($developerEmail,'error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
		$assumeErrorState=false;
	break;
	case 'CMSBEdit':		ob_start();
		prn($_SERVER['QUERY_STRING']);
		prn($_POST);
		
		// new shutdown coding
		$assumeErrorState=true;
		register_shutdown_function('iframe_shutdown');
		ob_start('store_html_output');
		$excludePageFromStats=true;

		/* 2009-01-08: added the login feature */
		if($logout){
			unset($_SESSION['special'][$MASTER_DATABASE]['adminMode']);
			?><div id="loginSection">
				<h3>CMSB Editor Sign-in</h3>
				<input name="UN" type="text" id="UN" /><br />
				<input name="PW" type="password" id="PW" /><br />
				<input type="submit" name="Submit" value="Sign In" />
				&nbsp;&nbsp;
				<input type="button" name="Button" value="Cancel" onClick="window.close();" />
			</div>
			<script language="javascript" type="text/javascript">
			window.parent.g('loginSection').innerHTML=document.getElementById('loginSection').innerHTML;
			window.parent.g('loginSection').style.display='block';
			window.parent.g('CMSBSection').style.display='none';
			</script><?php
		}else if(isset($UN)){
			if(strlen($UN) && strlen($PW) && strtolower($UN)==strtolower($MASTER_USERNAME) && stripslashes($PW)==$MASTER_PASSWORD){
				$_SESSION['special'][$MASTER_DATABASE]['adminMode']=1;
				?><script language="javascript" type="text/javascript">
				window.parent.g('loginSection').innerHTML='';
				window.parent.g('loginSection').style.display='none';
				window.parent.g('CMSBSection').style.display='block';
				window.parent.g('logoutLink').style.visibility='visible'
				window.parent.CMSBLoad();
				</script><?php
			}else{
				error_alert('Your user name and password is not correct');
			}
		}else{
			//2011-06-13 CMSBUpdate was not being called with the same parameters as the window popup, just blank.  this is addressing that
			CMSBUpdate();
		}
		$assumeErrorState=false;
		exit;
}

if($logout=='1'){
	unset($_SESSION['special'][$MASTER_DATABASE]['adminMode']);
	header('Location: '.stripslashes($src));
	?>
	redirecting..
	<script>
	window.location='<?php echo stripslashes($src)?>';
	</script><?php
	exit;
}else if($UN==$MASTER_USERNAME && $PW==$MASTER_PASSWORD){
	$_SESSION['special'][$MASTER_DATABASE]['adminMode']=1;
	$location=($src ? stripslashes($src) : '/');
	header('Location: '.$location);
	?><script>window.location='<?php echo $location?>'</script><?php
	exit;
}else if(strlen($UN.$PW)){
	$error=true;
}
if($method=='static:default'){
	//get the record
	$static=q("SELECT ID, Options, EditNotes FROM cmsb_sections WHERE ThisFolder='".$_GET['thisfolder']."' AND ThisPage='".$_GET['thispage']."' ORDER BY ID DESC LIMIT 1", O_ROW);
	if(strlen($static['Options'])){
		$Options=unserialize(base64_decode($static['Options']));
	}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>CMS Editor <?php echo !$adminMode ? ' : Sign In':''?></title>

<style type="text/css">
body{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	background-image:none;
	margin:5px 20px;
	min-width:600px;
	}
img{
	border:none;
	}
#slideshow{
	float:right;
	padding:0px 0px 15px 15px;
	}
#logoutLink{
	display:none;
	}
<?php if($adminMode){ ?>
#CMSBSection{
	display:block;
	}
#loginSection{
	display:none;
	}
<?php }else{ ?>
#CMSBSection{
	display:none;
	}
#loginSection{
	display:block;
	margin:0px auto;
	}
<?php } ?>
#tabs{
	border-bottom:1px solid #444;
	background-color:gold;
	}
#tabs a{
	color:inherit;
	text-decoration:none;
	line-height:115%;
	}
#tabs li{
	display:inline;
	padding:0px 15px 0px 8px;
	}
.tabactive{
	background-color:#CCC;
	border:1px solid darkred;
	cursor:default;
	}
.tabinactive{
	background-color:#FFF;
	border:1px solid #444;
	cursor:pointer;
	}
.tabactive a{
	cursor:default;
	}
.contentTab{
	padding:15px 25px;
	}
#optionsBox{
	position:absolute;
	bottom:25px;
	right:0px;
	background-color:white;
	border:1px solid #333;
	width:200px;
	height:200px;
	padding:12px;
	}
.rollbacks{
	border-collapse:collapse;
	}
.rollbacks th{
	text-align:left;
	background-color:#ccc;
	border-bottom:1px solid #000;
	}
.rollbacks th, .rollbacks td{
	padding:1px 4px 2px 7px;
	vertical-align:top;
	}
.rollbacks td{
	border-bottom:1px dotted #ccc;
	}
.rollbacks .alt{
	background-color:whitesmoke;
	}
</style>
<script id="jsglobal" language="JavaScript" type="text/javascript" src="Library/js/global_04_i1.js"></script>
<script id="jscommon" language="JavaScript" type="text/javascript" src="Library/js/common_04_i1.js"></script>
<script id="jscommon" language="JavaScript" type="text/javascript" src="Library/js/forms_04_i1.js"></script>

<link href="/Library/ckeditor_3.4/_samples/sample.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Library/ckeditor_3.4/ckeditor.js"></script>

<script language="javascript" type="text/javascript">
//source for editing
var cmsfolder=<?php echo $_GET['thisfolder'] ? "'".$_GET['thisfolder']."'" : 'window.opener.thisfolder'?>;
var cmspage=<?php echo $_GET['thispage'] ? "'".strtolower($_GET['thispage'])."'" : 'window.opener.thispage'?>;
if(cmspage)cmspage==cmspage.toLowerCase();
var cmsquery=<?php echo $_GET['cmsquery'] ? "'".$_GET['cmsquery']."'" : 'window.opener.location+\'\''?>;
cmsquery=cmsquery.toLowerCase();
var cmsquerypassed=<?php echo $_GET['cmsquery'] ? 'true' : 'false'?>;
var cmssection='<?php echo $_GET['thissection']; 
//2012-08-08
$setStaticParamters=true;
if($primaryParameter){
	echo strtolower('-'.$primaryParameter.':'.stripslashes($primaryValue));
	if($secondaryParameter=='null' || !$secondaryParameter){
		unset($secondaryParameter,$secondaryValue);
	}else{
		echo strtolower(':'.$secondaryParameter.':'.stripslashes($secondaryValue));
	}
}
?>';
var cmsOriginalPagePresent=true;

//CKEDITOR.config.contentsCss=['/site-local/fit_04.css'];
o=window.parent.opener.document.getElementsByTagName('head')[0].innerHTML;
o=o.split("\n");
CKEDITOR.config.contentsCss=[];
for(i=0; i<o.length; i++){
	t=o[i];
	if(t.indexOf('<link')<0)continue;
	start=t.indexOf('href="')+6;
	t=t.substring(start,1000);
	end=t.indexOf('"');
	t=t.substring(0,end);
	//convert
	t=t.replace(/(\.\.\/)*/g,'');
	t=t.replace(/^\//,'');
	t='/'+t;
	CKEDITOR.config.contentsCss[CKEDITOR.config.contentsCss.length]=t;
}
//in case any CSS in the native stylesheets does something wierd, include this file in site-local to override it
CKEDITOR.config.contentsCss[CKEDITOR.config.contentsCss.length]='/Library/css/CMSB_CSS_override.css';

var CMSBGetNativeStyleSheets='popup';
var HTML='';
var editorCreated=false;
var editorEmailsSent=0;
function createEditor(field, container){
	if(editorCreated)return;
	/*
	var fck = new FCKeditor(field);
	var sBasePath= '/Library/fck6/';
	fck.BasePath= sBasePath ;
	fck.Value=g(container).innerHTML;
	fck.ToolbarSet = "xTransitional";
	fck.Config[ 'ToolbarLocation' ] = 'Out:xToolbar' ;
	fck.Height = 350 ;
	g(container).innerHTML = fck.CreateHtml();
	g(container).style.visibility='visible';
	*/
	editorCreated=true;
	return;
	
	//old version of the function
	if(editorCreated)return;
	var fck = new FCKeditor(field);
	var sBasePath= '/Library/fck6/';
	fck.BasePath= sBasePath ;
	fck.Value=g(container).innerHTML;
	fck.ToolbarSet = "xTransitional";
	fck.Config[ 'ToolbarLocation' ] = 'Out:xToolbar' ;
	fck.Height = 350 ;
	g(container).innerHTML = fck.CreateHtml();
	g(container).style.visibility='visible';
	editorCreated=true;
}
function CMSBLoad(){
	/*
	coding for Kenai resort - shut down the slideshow
	*/
	try{
		// [no slideshows in editor right now] if(window.opener.running)window.opener.startStop();
	}catch(e){}

	
	//THIS IS THE INNERHTML METHOD - BUT I CAN'T SKIP NODES I WANT TO GRAPHICALLY REPRESENT ANOTHER WAY
	HTML=window.opener.g(cmssection).innerHTML;
	HTML=HTML.replace(/src="images\//g,'src="/images/');
	SetContents('CMS',HTML);
	window.status=('Editor API Running');
	setTimeout('CMSUpdater()',1000);
}
var oEditor=null;
function CMSUpdater(){
	var oEditor = CKEDITOR.instances['CMS'];
	if(oEditor.checkDirty()){
		detectChange=1; 
		g('CMSBUpdate').disabled=false;
		try{ //------------------------
		comparepage=(window.opener.thispage ? window.opener.thispage.toLowerCase() : '');
		if(cmspage==comparepage && cmsfolder==window.opener.thisfolder && cmsquery==(cmsquerypassed ? window.opener.cmsquery : (window.opener.location+'').toLowerCase())){
			cmsOriginalPagePresent=true;
			window.opener.g(cmssection).innerHTML=(oEditor.getData());
		}else{
			cmsOriginalPagePresent=false;
		}
		}catch(e){ } //-----------------
	}
	setTimeout('CMSUpdater()',350);
}
function CMSBClose(){
	var oEditor = CKEDITOR.instances['CMS'];
	if(oEditor.checkDirty() && !confirm('You have made change and clicking OK will lose those changes. Continue?')){
		return false;
	}
	//try{
		window.opener.g(cmssection).innerHTML=HTML;
		oEditor.resetDirty();
	//}catch(e){ window.open('cms3.11.php?mode=emailEmergency&src='+escape(window.location),'w4'); }
	// -- 2009-03-16: enabling not working so we are just having it constantly enabled g('CMSBUpdate').disabled=true;
	window.close();
}

//do I need this anymore - part of 2.x
//window.onload=CMSBLoad;

//page functionality
function jsToggle(o){
	if(g('tester').style.display=='block'){
		o.src='/images/i/blue_tri_desc.gif';
		g('tester').style.display='none';
	}else{
		o.src='/images/i/blue_tri_asc.gif';
		g('tester').style.display='block';
	}
}
function toggleOptions(){
	g('optionsBox').style.display=op[g('optionsBox').style.display];
}
var tabsList=['editor','history','settings','help','html','source'];
function tabs(n){
	for(i in tabsList){
		try{ g('tab_'+tabsList[i]).style.display=(n==tabsList[i]?'block':'none');		}catch(e){ }
		try{ g('b_'+tabsList[i]).className=(n==tabsList[i]?'tabactive':'tabinactive');	}catch(e){ }
	}
	return false;
}
</script>
<script language="JavaScript" type="text/javascript">
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
<form name="form1" id="form1" action="" method="post" target="w2" onSubmit="return beginSubmit();">
	<div id="header">
		<div style="float:right;">
			<div id="logoutLink"><a href="<?php echo $_SERVER['PHP_SELF']?>?mode=CMSBEdit&logout=1" title="Log out of CMSB Editor" onClick="try{ if(oEditor.IsDirty())return confirm('This will log you out of CMSB editor and you will lose your changes. Continue?'); }catch(e){ }" target="w2">Logout</a></div>
			<?php if(!$adminMode){ ?>
			<script language="javascript" type="text/javascript">g('logoutLink').style.visibility='hidden';</script>
			<?php } ?>
		</div>
		<a title="click for CMSB Help" href="http://www.compasspoint-sw.com/mediawiki-1.13.2/index.php?title=CMS_Bridge_Public_Documentation" onClick="return ow(this.href,'l2_CMSBHelp','800,700');"><img src="/images/i/CMSB-logo.gif" width="195" height="69" align="CMSB Logo" /></a>
	</div>
	<?php
	if($adminMode && $disposition=='history'){
		if($a=q("SELECT * FROM cmsb_sections WHERE ID=$ID", O_ROW)){
			?><div id="tabs">
				<ul>
					<li id="b_html" class="tabactive"><a href="#" onClick="return tabs('html');">HTML</a></li>
					<li id="b_source" class="tabinactive"><a href="#" onClick="return tabs('source');">Source Code</a></li>
				</ul>
			</div>
			<div id="tab_html" style="display:block;">
				<h3><?php echo $a['EditNotes']?></h3>
				<?php echo $a['Content']?>
			</div>
			<div id="tab_source" style="display:none;">
				<h3><?php echo $a['EditNotes']?></h3>
				<textarea name="Content" id="Content" rows="25" cols="65"><?php echo h($a['Content']);?></textarea>
			</div><?php
		}else{
			?><h2>Unable to find this section in the history; contact the site administrator</h2><?php
		}
		echo '</body></html>';
		$assumeErrorState=false;
		exit;
	}
	?>
	<div id="CMSBSection">
		<div id="tabs">
			<ul>
				<li id="b_editor" class="tabactive"><a href="#" onClick="return tabs('editor');">Editor</a></li>
				<li id="b_history" class="tabinactive"><a href="#" onClick="return tabs('history');">History</a></li>
				<li id="b_settings" class="tabinactive"><a href="#" onClick="return tabs('settings');">My Settings</a></li>
				<li id="b_help" class="tabinactive"><a href="#" onClick="return tabs('help');">Help & Credits</a></li>
			</ul>
		</div>
		<div id="tab_editor" style="display:block;">
			<textarea cols="80" id="CMS" name="CMS" rows="10"><?php
			//this is easy
			echo h(trim($CMS) ? $CMS : '<p></p>');
			?></textarea>
			<script type="text/javascript">
			var editor = CKEDITOR.replace( 'CMS' );
			CMSBLoad();
			</script>
			<div id="showTester" title="Javascript Tester" onClick="g('tester').style.display='block';">&nbsp;</div>
			<input type="hidden" name="mode" id="mode" value="CMSBEdit" />
			<input type="hidden" name="lang" id="lang" value="<?php echo $lang?$lang:'en';?>" />
			<!-- query string passed fields -->
			<?php
			foreach($_GET as $n=>$v){
				?><input type="hidden" name="<?php echo h($n);?>" id="<?php echo h($n);?>" value="<?php echo h($v);?>" /><?php
				echo "\n";
			}
			?>
			<?php if($method=='static:default'){ ?>
			<div style="position:relative;float:right;margin-right:35px;">
				<input type="button" name="Button" value="Options.." onClick="toggleOptions();" />
				<div id="optionsBox" style="display:none;">
					<label> Make this page a slideshow with 
					<select name="Options[MakePageSlide]" id="Options[MakePageSlide]">
					<option value="">(n/a)</option>
					<?php for($i=1;$i<=8;$i++){ ?><option value="<?php echo $i?>" <?php echo $Options['MakePageSlide']==$i?'selected':''?>><?php echo $i?></option><?php } ?>
					</select> 
					pictures per page</label>
					<br />
					(TEXT BEFORE THE FIRST PICTURE WILL BE REPEATED)
				</div>
			</div>
			<?php } ?>
			<input type="submit" name="Submit" id="CMSBUpdate" value="Update" disabled="disabled" />
			&nbsp;&nbsp;
			<input type="button" name="Button" value="Close" onClick="CMSBClose();" />
			&nbsp;&nbsp;
			<input type="button" name="Button" value="View page.." onClick="window.opener.focus();" />
			&nbsp;&nbsp;
			<input type="button" name="Button" value="Images" onClick="return ow('/admin/file_explorer/?uid=CMSB&view=fullfolder','l2_images','750,700');" />
			<?php if($method=='static:default'){ ?>
			<br />
			Editor's notes: 
			<input name="EditNotes" type="text" id="EditNotes" size="55" maxlength="255" />
			<?php } ?>

		</div>
		<div id="tab_history" class="contentTab" style="display:<?php echo 'none'?>;">
		<?php if($adminMode && $method=='static:default'){ ?>
			Each time you click "update" for your content, the previous version of the page is stored in this history section.  It's a good idea to include editor's comments on your updates.<br />
<br />
			To "roll back" to a previous version, click the version below, and either copy the entire source code from the window that is opened, or use as necessary to make changes to the source code of the existing version.<br />
<br />
			<table class="rollbacks">
			<thead><tr>
			<th valign="top">&nbsp;</th>
			<th valign="top">Date</th>
			<th valign="top">lang</th>
			<th valign="top">Editor</th>
			<th valign="top">Notes</th>
			<th>Content</th>
			</tr></thead>
			<?php
			$snippetLength=35;
			$titleLength=200;
			if($a=q("SELECT ID, lang, Content, EditNotes, EditDate, Editor FROM cmsb_sections WHERE thisfolder='".$_REQUEST['thisfolder']."' AND (thispage='".$_REQUEST['thispage']."' ".(!$_REQUEST['thispage'] ? "OR thispage='index.php' OR thispage='index.htm' OR thispage='index.html'" : '').") AND section='".$_REQUEST['thissection']."' ORDER BY ID DESC", O_ARRAY)){
				?><tbody><?php
				$k++;
				foreach($a as $v){
					?><tr class="<?php if(!fmod($k,2))echo 'alt';?>">
					<td><a title="View source code for this history" href="cms3.11.php?disposition=history&method=static:default&ID=<?php echo $v['ID']?>" onClick="return ow(this.href,'l2_cmsb','700,700');"><img src="/images/i/edit2.gif" /></a></td>
					<td nowrap="nowrap"><?php echo date('m/d @g:iA',strtotime($v['EditDate']));?></td>
					<td><?php echo $v['lang']?></td>
					<td><?php echo strtolower($v['Editor'])=='administrator' ? 'admin' : $v['Editor'];?></td>
					<td><?php echo $v['EditNotes']?></td>
					<?php
					$Content=strip_tags($v['Content'],'<b><i><u>');
					$b=preg_split('/\s+/',$Content);
					$str='';
					$title='';
					for($j=1; $j<=1; $j++){ //---------- start break
					for($i=0; $i<=min(count($b)-1, $titleLength); $i++){
						if($i<=$snippetLength)$str.=$b[$i].' ';
						$title.=$b[$i].' ';
						if($i==$titleLength)break(2);
					}
					$str.='...';
					} //--------- end break
					$title=preg_replace('/&nbsp;/i',' ',$title);
					$title=str_replace('"','&quot;',$title);
					?><td title="<?php echo h($title);?>"><?php 
					echo $str;
					?></td>
					</tr><?php
				}
				?></tbody><?php
			}else{
				?><tr><td colspan="100%">No history for this section</td></tr><?php
			}
			?>
			</table>
		<?php }else{ ?>
			
		<?php mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs); ?>
		
		<?php }?>
		</div>
		<div id="tab_settings" class="contentTab" style="display:none;">
			<label><input name="settings[CopySizedDownImages]" type="checkbox" id="settings[CopySizedDownImages]" value="1" <?php echo $settings['CopySizedDownImages'] || !isset($settings['CopySizedDownImages']) ? 'checked': ''?> /> 
			Copy sized-down pictures (will be stored in a folder named images/pieces)</label><br />
			
			<label><input name="settings[RemoveMSWordMarkup]" type="checkbox" id="settings[RemoveMSWordMarkup]" value="1" <?php echo $settings['RemoveMSWordMarkup'] || !isset($settings['RemoveMSWordMarkup']) ? 'checked': ''?> /> 
			Strip extra coding from Microsoft Word (recommended) (<a href="http://www.compasspoint-sw.com/mediawiki-1.13.2/index.php?title=Microsoft_Word_Markup" target="help">help</a>)</label>
			
			
			<br />

		</div>
		<div id="tab_help" class="contentTab" style="display:none;">
			<h3>Help and Credits</h3>
			<p>CMS Bridge is brought to you by the folks at <a href="http://www.compasspoint-sw.com/" target="credits">Compass Point Media</a>, we'd like to thank the following individuals for their tireless help and support making CMSB the most intuitive content management system available:<br />
				<br />
				<strong>Seema Nikam</strong> - coder and fearless testing Ninja<br />
				<strong>Josh Higgins</strong> - 
		graphic designer and &quot;skin&quot; guy [<a href="mailto:josh@compasspoint-sw.com">email</a>] <br />
			and <a href="mailto:samuelf@compasspoint-sw.com">Samuel Fullman</a>, who has an occasional development and improvement idea </p>
			<p>For help (itself a work in progress), go to the <a href="http://www.compasspoint-sw.com/mediawiki-1.13.2/index.php?title=CMS_Bridge_Public_Documentation" target="help">CMSB Wiki Section</a><br />
				For assistance and suggestions contact us at the emails above
</p>
			<p>&nbsp;</p>
		</div>
	</div>
	<div id="loginSection">
		<h3>CMSB Editor Sign-in</h3>
		<input name="UN" type="text" id="UN" /><br />
		<input name="PW" type="password" id="PW" /><br />
		<input type="submit" name="Submit" value="Sign In" />
		&nbsp;&nbsp;
		<input type="button" name="Button" value="Cancel" onClick="window.close();" />
	</div>
</form>
<?php
if($adminMode){
	?><script language="javascript" type="text/javascript">
	g('loginSection').innerHTML='';
	</script><?php
}
?>
<br />
<br />
<img src="/images/i/blue_tri_desc.gif" title="show Javascript tester" width="18" height="14" onClick="jsToggle(this);" style="cursor:pointer;" />
<div id="tester" style="display:none;">
	<span style="font-size:large;">Javascript Code Executer</span> (type in javascript and click eval; you can declare functions for this page, or even window.opener.newFunction=function(){ /* code here */ } for the parent if you want!)<br />
	<textarea name="test" cols="65" rows="4" id="test" onFocus="if(this.value=='/* javascript code here */')this.value='';this.select();">g('ctrlSection').style.display='block';</textarea><br />
	<input type="button" name="button" value="Execute Code" onClick="jsEval(g('test').value);"><br />
</div>
<div id="ctrlSection" style="display:<?php echo $testModeC?'block':'none'?>">
	<iframe name="w1" src="Library/js/blank.htm"></iframe>
	<iframe name="w2" src="Library/js/blank.htm"></iframe>
	<iframe name="w3" src="Library/js/blank.htm"></iframe>
	<iframe name="w4" src="Library/js/blank.htm"></iframe>
</div>

</body>
</html>