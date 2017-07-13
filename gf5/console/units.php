<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');



if(!($a=q("SELECT * FROM _v_properties_master_list WHERE ID=$Units_ID", O_ROW))){
	exit('unable to find unit information');
}
extract($a);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo 'Edit Unit Info'.' - '.$AcctCompanyName?></title>



<link id="cssUndoHTML" rel="stylesheet" href="/site-local/undohtml2.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
/* local CSS styles */
body{
	background-color:#EDE7DC;
	}
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle coding */
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

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onSubmit="return beginSubmit();">
<?php }?>
<div id="header">

<?php
if(!$refreshComponentOnly){
	?><style type="text/css">
	input{
		margin-right:7px;
	}
	</style>
	<script language="javascript" type="text/javascript">
	</script><?php
}
?>
</div>
<div id="mainBody">

<h1>Property: <?php echo $PropertyName;?></h1>
<div class="fr">
	<style type="text/css">
	#imgWidget{
		cursor:pointer;
		position:relative;
		float:left;
		}
	#imgWidgetOptions{
		position:absolute;
		background-color:papayawhip;
		left:63px;
		top:0px;
		width:200px;
		float:left;
		}
	.iwnormal{
		cursor:pointer;
		background-color:silver;
		}
	.iwhl{
		cursor:pointer;
		background-color:gold;
		}
	</style>
	<?php
	$imgWidgetImgPath=tree_id_to_path($Tree_ID=q("SELECT Tree_ID FROM gl_ObjectsTree WHERE ObjectName='gl_properties_units' AND Objects_ID=$Units_ID AND Category='Floor Plan'", O_VALUE));	
	?>
	<script language="javascript" type="text/javascript">
	setTimeout("g('imgWidgetTargetImg').setAttribute('picture',<?php 
	if( $mode==$updateMode &&
		$imgWidgetImgPath &&
		file_exists($_SERVER['DOCUMENT_ROOT'].$imgWidgetImgPath)){
		$Key=end(explode('/',$imgWidgetImgPath));
		preg_match('/^[a-f0-9]+_/i',$Key,$a);
		$Key=$a[0];
		echo $picture=1;
		$g=getimagesize($_SERVER['DOCUMENT_ROOT'].$imgWidgetImgPath);
	}else{
		echo $picture=0;
		$Key='';
	}
	?>);",500);
	function imgWidget(n,o,p){
		if(n<2){
			g('imgWidgetOptions').style.display=(n==1 ? 'block' : 'none');
		}else if(n<4){
			o.className=(n==3?'iwnormal':'iwhl');
		}else if(n==5){
			/*we need to tell the uploader that 
			1. the only acceptable format is an image
			2. we need a crop done AND store the original
			*/
			ow('file_loader.php?CategoryGroup=PropertyDocumentCategories&Category=Floor+Plan&submode=floorPlan&Units_ID=<?php echo $Units_ID?>&_cb_passedBoundingBoxWidth=300&_cb_passedBoundingBoxHeight=300&_cb_passedBoxMethod=2&_cb_copyAs=1&cbFunction=imgWidget&cbParam[]=fixed:10&cbParam[]=handle&cbParam[]=Tree_ID','l1_loader','500,500');
		}else if(n==6 || n==60){
			//remove profile picture - i.e. no picture
			if(n==6 && !confirm('Are you sure you want to remove this profile picture?'))return;
			g('ProfileTree_ID').value='-1';
			g('ProfileKey').value='';
			g('imgWidgetTargetImg').src='/images/i-local/floorplanneeded.png';
			killResizeImg=true;
			g('imgWidgetTargetImg').width=300;
			g('imgWidgetTargetImg').height=300;
			g('imgWidgetTargetImg').setAttribute('picture',0);
			g('iw1').innerHTML='Add';
			detectChange=1;
		}else if(n==7){
			
		}else if(n==10){
			g('ProfileTree_ID').value=p;
			g('ProfileKey').value=o;
			g('imgWidgetTargetImg').src='/images/reader.php?Tree_ID='+p+'&Key='+o;
			killResizeImg=false;
			I=new Image();
			I.src='/images/reader.php?Tree_ID='+p+'&Key='+o;
			resizeImg();
			g('imgWidgetTargetImg').setAttribute('picture',1);
			g('iw1').innerHTML='Change';
			detectChange=1;
		}else if(n==11){
			if(g('imgWidgetTargetImg').getAttribute('picture')==1)return;
			g('imgWidgetTargetImg').src='/images/i-local/floorplanneeded.png';
			g('imgWidgetTargetImg').width=300;
			g('imgWidgetTargetImg').height=300;
		}
	}
	function resizeImg(){
		g('imgWidgetTargetImg').height=(killResizeImg ? 300 : I.height);
		g('imgWidgetTargetImg').width=(killResizeImg ? 300 : I.width);
		if(killResizeImg)return;
		setTimeout('resizeImg()',1000);
	}
	</script>
	<input name="ProfileTree_ID" type="hidden" id="ProfileTree_ID" />
	<input name="ProfileKey" type="hidden" id="ProfileKey" />
	<div id="imgWidget" onMouseOver="imgWidget(1);" onMouseOut="imgWidget(0);">
	<img id="imgWidgetTargetImg" src="<?php
	if($picture){
		echo '/images/reader.php?Tree_ID='.$Tree_ID.'&Key='.$Key.'&disposition=300x300';
	}else{
		echo '/images/i-local/floorplanneeded.png';
	}
	?>"
	<?php
	if($picture){
		if(max($g[0],$g[1])>300){
			$g[0]=round($g[0] * (300 / max($g[0],$g[1])));
			$g[1]=round($g[1] * (300 / max($g[0],$g[1])));
		}
		/* not working: width="<?php echo $g[0];?>" height="<?php echo $g[1]?>" */
		?><?php
	}else{
		echo 'width="300" height="300"';
	}
	?> />
	<div id="imgWidgetOptions" style="display:none;">
		<div onMouseOver="imgWidget(2,this);" onMouseOut="imgWidget(3,this);" class="iwnormal" onClick="imgWidget(5)"><span id="iw1"><?php echo $picture?'Change':'Add'?></span> photo</div>
		<div onMouseOver="imgWidget(2,this);" onMouseOut="imgWidget(3,this);" class="iwnormal" onClick="imgWidget(6)">Remove photo</div>		
	</div>
	</div>



</div>
<p><?php echo $PropertyAddress;?><br />
  <?php echo $PropertyCity. ', '.$PropertyState. ' ' .$PropertyZip;?><br />
  Contact: <?php echo $ContactFirstName . ' '. $ContactLastName;?><br />
  Email:  <a href="mailto:<?php echo $ContactEmail;?>"><?php echo $ContactEmail;?></a><br />
  Phone:  <?php echo $Phone;?>
  <br />
</p>
<p>Rent: <strong><?php echo $Rent;?></strong> <?php history('Rent');?><br>
  Square footage: <strong><?php echo $SquareFeet;?></strong><br />
  Quantity of this type: <?php echo $Quantity;?><br />
  Bedrooms: <?php echo $Bedrooms;?><br />
  Bathrooms: <?php echo $Bathrooms;?><br />
  Specials for this unit type:<br /> 
  <input name="Specials" type="text" id="Specials" value="<?php echo h($Specials);?>" size="45" maxlength="64000" />
  <br />
</p>
<table>
  <tr>
    <td><label><input name="WalkInClosets" type="checkbox" id="WalkInClosets" value="1" <?php echo $WalkInClosets?'checked':''?> onChange="dChge(this);" />Walk-in Closets</label></td>
  </tr>
  <tr>
    <td>Washer-Dryer: 
      <select name="WasherDryer" id="WasherDryer" onChange="dChge(this);">
	  <option value="0">NO</option>
	  <option value="1" <?php echo $WasherDryer==1?'selected':''?>>Connection available</option>
	  <option value="2" <?php echo $WasherDryer==2?'selected':''?>>Yes</option>
	  <?php
	  if(strlen($WasherDryer) && !preg_match('/^[0-2]$/',$WasherDryer)){
	  	?><option value="<?php echo h($WasherDryer);?>" selected="selected"><?php echo h($WasherDryer);?></option><?php
	  }
	  ?>
      </select>      </td>
  </tr>
  <tr>
    <td><label><input name="Furnished" type="checkbox" id="Furnished" value="1" <?php echo $Furnished?'checked':''?> onChange="dChge(this);" />Furnished</label></td>
  </tr>
  <tr>
    <td><label><input name="Storage" type="checkbox" id="Storage" value="1" <?php echo $Storage?'checked':''?> onChange="dChge(this);" />Storage</label></td>
  </tr>
  <tr>
    <td><label><input name="Fireplace" type="checkbox" id="Fireplace" value="1" <?php echo $Fireplace?'checked':''?> onChange="dChge(this);" />Fireplace</label></td>
  </tr>
  <tr>
    <td><label><input name="VaultedCeilings" type="checkbox" id="VaultedCeilings" value="1" <?php echo $VaultedCeilings?'checked':''?> onChange="dChge(this);" />Vaulted Ceilings</label> </td>
  </tr>
  <tr>
    <td><label><input name="PrivateBalcony" type="checkbox" id="PrivateBalcony" value="1" <?php echo $PrivateBalcony?'checked':''?> onChange="dChge(this);" />Private Balcony</label> </td>
  </tr>
  <tr>
    <td><label><input name="Dishwasher" type="checkbox" id="Dishwasher" value="1" <?php echo $Dishwasher?'checked':''?> onChange="dChge(this);" />Dishwasher</label></td>
  </tr>
  <tr>
    <td><label><input name="IceMaker" type="checkbox" id="IceMaker" value="1" <?php echo $IceMaker?'checked':''?> onChange="dChge(this);" />Ice Maker</label> </td>
  </tr>
  <tr>
    <td><label><input name="Microwave" type="checkbox" id="Microwave" value="1" <?php echo $Microwave?'checked':''?> onChange="dChge(this);" />Microwave</label></td>
  </tr>
</table>
<p>Additional details:</p>
<p>
<textarea name="Additional" cols="65" rows="5" id="Additional"><?php echo h($Additional);?></textarea>
</p>
<p>
  <input name="mode" type="hidden" id="mode" value="updateUnit" />
  <input name="Units_ID" type="hidden" id="Units_ID" value="<?php echo $Units_ID;?>" />
  <input type="submit" name="Submit" value="Update Unit Info" /> 
&nbsp;&nbsp;
<input type="button" name="Submit2" value="Cancel" onClick="if(detectChange && !confirm('You have made changes.  Continue?'))return false; window.close();" />  
&nbsp;<br />
</p>
<?php
$section='gl_properties_units';
require('components/comp_301_propertyimages.php');
?>
</div>
<div id="footer">
&nbsp;&nbsp;
</div>
<?php if(!$suppressForm){ ?>
</form>
<?php }?>
<?php if(!$hideCtrlSection){ ?>
<div id="showTester" title="Javascript Tester" onClick="g('tester').style.display='block';">&nbsp;</div>
<div id="tester" >
	<a href="#" onClick="g('ctrlSection').style.display='block';return false;">Show Control Section</a><br />
	<textarea name="test" cols="65" rows="4" id="test">clear_form();</textarea><br />
	<input type="button" name="button" value="Test" onClick="jsEval(g('test').value);"><br />
	<textarea id="result" name="result" cols="65" rows="3" ></textarea>
</div>
<div id="ctrlSection" style="display:<?php echo $testModeC ? 'block':'none'?>">
	<iframe name="w1" src="/Library/js/blank.htm"></iframe>
	<iframe name="w2" src="/Library/js/blank.htm"></iframe>
	<iframe name="w3" src="/Library/js/blank.htm"></iframe>
	<iframe name="w4" src="/Library/js/blank.htm"></iframe>
</div>
<?php } ?>
</body>
</html><?php page_end();?>