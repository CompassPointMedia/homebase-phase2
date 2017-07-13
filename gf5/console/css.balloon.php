<?php
require($_SERVER['DOCUMENT_ROOT'].'/config.php');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Tools or Information Balloon</title>


<link id="cssUndoHTML" rel="stylesheet" href="/site-local/undohtml2.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />


<style type="text/css">
body{
	font-family:Arial;
	font-size:13px;
	}
#field{
	margin:50px;padding:5px 10px;background-color:bisque;border:1px solid sienna;width:500px;
	}
.wordDef{
	border-bottom:1px dashed darkgreen;
	cursor:pointer;
	background-color:oldlace;
	padding:1px 2px;
	}
</style>

<script id="jsglobal" language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script id="jscommon" language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script id="jsforms" language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script id="jsloader" language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script id="jscontextmenu" language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script id="jsdataobjects" language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script id="jssite" language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script id="jslocal" language="JavaScript" type="text/javascript">
/* periwinkle .02 coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
var ResourceToken='<?php echo $ResourceToken;?>';
var pobject=null;
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';
</script>

</head>

<body>
<?php 
//-------------------- menu creator ----------------------
ob_start();
?>

option1<br />
option2<br />
option3

<?php
$options=array(
	'type'=>'toddler',
	'objectRegex'=>'^word_',
	'menuID'=>'definitionTools',
	'precalculated'=>'getWordDef()'
);
write_menu($options);
//------------------ end menu creator ---------------------
?>



<script language="javascript" type="text/javascript">
function getWordDef(){
	//alert(rb_cm_lastObject.id);
}
</script>


<?php
function write_menu($options=array()){
	extract($options);
	if(!$type)$type='toddler';
	if(!$menuID)$menuID='definitionTools';

	if($type=='toddler'){
		if(!$alignment)$alignment='mouse,20,-40';
		if(!$inner)$inner=ob_get_contents();
		ob_end_clean();
		?><style type="text/css">
		.balloonWrap{
			width:50px;
			position:absolute;
			visibility:hidden;
			left:50px;
			top:50px;
			}
		.dropshadow{
			left:10px; /* offset to left */
			top:24px; /* height of tick mark - 1 to cover border + 10 for offset */
			width:250px;
			height:145px;
			background-color:#333333;
			opacity:.45;
			filter:alpha(opacity=45);
			position:absolute;
			z-index:499;
			}
		.indices{
			position:absolute;
			z-index:501;
			background-image:url('/images/i/arrows/indices-style01-up.png');
			background-position:top left;
			background-repeat:no-repeat;
			top:0px;
			right:0px;
			width:17px; /* actual size of the tick mark */
			height:15px;
			}
		.balloonContent{
			background-color:white;
			border:1px solid sienna;
			width:250px;
			height:145px;
			position:absolute;
			z-index:500;
			top:14px; /* height of tick mark minus 1 */
			}
		.balloonContent .spd{
			padding:5px 10px;
			}
		#balloonKill{
			float:right;
			text-align:center;
			width:15px;
			height:15px;
			color:white;
			background-color:darkred;
			font-size:10px;
			margin:1px;
			cursor:pointer;
			}
		</style>
		<script language="javascript" type="text/javascript">
		AssignMenu('<?php echo $objectRegex?$objectRegex:'none'?>','<?php echo $menuID?>', '<?php echo $alignment?>');
		</script>
		<div id="<?php echo $menuID;?>" class="balloonWrap" precalculated="<?php echo $precalculated?>">
			<div class="dropshadow">
			</div>
			<div class="balloonContent">
				<div id="balloonKill" onclick="hidemenuie5(event)">X</div>
				<div id="<?php echo $menuID?>_content" class="spd">
					<?php echo $inner;?>
				</div>
			</div>
			<div class="indices">
			</div>
		</div><?php
	}
}

?>
<div id="field">

	<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam <div class="wordDef" id="word_volupta"  onclick="hidemenuie5(event,1);showmenuie5(event,1)" oncontextmenu="return false;">voluptua</div>. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd <span class="wordDef" id="word_gubergren"  onclick="hidemenuie5(event,1);showmenuie5(event,1)" oncontextmenu="return false;">gubergren</span>, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.<br />
</p>
	<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.<br />
</p>
	<p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.<br />
</p>
	<p>Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.<br />
</p>
	<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis.<br />
	</p>

</div>

</body>
</html>
