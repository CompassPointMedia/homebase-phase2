<?php
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']=4.0;
require('systeam/php/config.php');
require('resources/bais_00_includes.php');

if(!$Items_ID)$Items_ID=q("SELECT MAX(ID) FROM finan_items WHERE ResourceType IS NOT NULL", O_VALUE);

//------------------------ Navbuttons head coding v1.43 -----------------------------
//change these first vars and the queries for each instance
$object='Items_ID';
$recordPKField='ID'; //primary key field
$navObject='Items_ID';
$updateMode='updateItem';
$insertMode='insertItem';
$deleteMode='deleteItem';
$insertType=1; //1=Save&New and Save&Close; 2 = Save and Save&New
#set these to 'disabled' if desired
$saveInitiallyDisabled='';
$saveAndNewInitiallyDisabled='';
$saveAndCloseInitiallyDisabled='';
//v1.4 change - some information about the coding
$navVer='1.43';
//v1.3 change - declare this function if you need to add parameters to the query string
$navQueryFunction='nav_query_add()';
//v1.3 change - deny transiting from Next to New mode (shutting off ability to insert)
$denyNextToNew=false;
//declare the query to get the idSet or subset, ordered by desired sort order - note that if you're using quasi resources, then be sure and filter them out.
if($searchtype && $q){
	$searchtypeResult=searchtype();
	if($searchtypeResult['records'] && !$Items_ID){
		$a=current($searchtypeResult['records']);
		$Items_ID=$a['ID'];
	}
}
$ids=q("SELECT ID FROM finan_items WHERE ResourceType IS NOT NULL ORDER BY Name",O_COL);
/*
(another good example more complex)
$ids=q("SELECT ID FROM `$cc`.finan_invoices WHERE Accounts_ID='$Accounts_ID' ORDER BY InvoiceDate, CAST(InvoiceNumber AS UNSIGNED)",O_COL);
*/

$nullCount=count($ids);
$j=0;
if($nullCount){
	foreach($ids as $v){
		$j++; //starting value=1
		if($j==$abs+$nav || (isset($$object) && $$object==$v)){
			$nullAbs=$j;
			//get actual primary key if passage by abs+nav
			if(!$$object) $$object=$v;
			break;
		}
	}
}else{
	$nullAbs=1;
}
//note the coding to on ResourceToken - this will allow a submitted page to come up again if the user Refreshes the browser
if(strlen($$object) || $Items_ID=q("SELECT ID FROM finan_items WHERE ResourceToken='$ResourceToken'", O_VALUE)){
	//get the record for the object
	if($record=q("SELECT * FROM finan_items WHERE ID='".$$object."'",O_ROW)){
		unset($record['Items_ID']);
		@extract($record);
		$mode=($ResourceType ? $updateMode : $insertMode);
		if($mode==$insertMode && !$FileName){
			if(!($FileName=$HBS_OriginalFileName)){
				mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='unable to pull filename from query'),$fromHdrBugs);
				exit($err.', developer has been notified');
			}
		}
	}else{
		mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='insert recovery option disabled'),$fromHdrBugs);
		exit($err.', developer has been notified');

		//object may have been deleted by another user, least perplexing approach is to present insert mode
		$mode=$insertMode;
		unset($$object);
		$nullAbs=$nullCount+1;
	}
}else{
	$mode=$insertMode;
	if(!$FileName){
		?>
		<h1>Add Method Disabled</h1>
		<p>
		You are not able to add a product this way.  Please close this window, and on the main menu click <pre>Products > Show Unassigned Maps</pre>.  Click on any map file to begin the adding process.<br />
		<input type="button" name="close" value="Close" onClick="window.close();" />
		</p><?php
		exit;
	}
	if(!(@$FileSize=filesize($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master/'.stripslashes($FileName)))){
		$err=('Unable to get size of requested file; please close window and try again');
	}
	if(!($g=getimagesize($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master/'.stripslashes($FileName)))){
		$err=('Unable to get dimensions of requested file; please close window and try again');
	}
	if($err){
		mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err),$fromHdrBugs);
		exit($err);
	}
	$$object=$ID=quasi_resource_generic($MASTER_DATABASE, 'finan_items', $ResourceToken, $typeField='ResourceType', $sessionKeyField='sessionKey', $resourceTokenField='ResourceToken', $primary='ID', $creatorField='Creator', $createDateField='CreateDate', C_DEFAULT, $options=array(
		'insertFields' => array('HBS_OriginalFileName','FileSize','Width1','Height1'),
		'insertValues' => array($FileName, $FileSize, $g[0], $g[1])
	));
	$nullAbs=$nullCount+1; //where we actually are right then
}
if($mode==$insertMode){
	$record=q("EXPLAIN finan_items", O_ARRAY);
	foreach($record as $n=>$v){
		$record[$v['Field']]='';
		unset($record[$n]);
	}
	//but do not extract it

	if(@$g=getimagesize($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master/'.stripslashes($FileName))){
		$Width1=$g[0];
		$Height1=$g[1];
	}else{
		$triggerError['FileName']=true;
	}
}
//--------------------------- end coding --------------------------------

/*
02: goals are to pare down unneeded modes (point and line) and add the say functionality
also we have integrated the head coding (precoding) for items, and allowed to select and load maps

*/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
 Copyright 2008 Google Inc. 
 Licensed under the Apache License, Version 2.0: 
 http://www.apache.org/licenses/LICENSE-2.0 
 -->
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Polygon create and re-edit</title>


<link rel="stylesheet" type="text/css" href="/Library/css/cssreset01.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
body {
	font-family: Arial, sans serif;
	font-size: 11px;
	}
#map{
	 width: 600px; 
	 height: 600px; 
	 position: relative; 
	 }
#hand_b {
	width:31px;
	height:31px;
	background-image: url("http://google.com/mapfiles/ms/t/Bsu.png");
	}
#hand_b.selected {
	background-image: url("http://google.com/mapfiles/ms/t/Bsd.png");
	}
#shape_b {
	width:31px;
	height:31px;
	background-image: url("http://google.com/mapfiles/ms/t/Bpu.png");
	}
#shape_b.selected {
	background-image: url("http://google.com/mapfiles/ms/t/Bpd.png");
	}
</style>
<script src="/Library/js/jquery.js" language="javascript" type="text/javascript"></script>
<script src="dev.newmaps2_1_files/maps" type="text/javascript"></script>
<script src="dev.newmaps2_1_files/main.js" type="text/javascript"></script>
<script type="text/javascript">
var COLORS = [["red", "#ff0000"], ["orange", "#ff8800"], ["green","#008000"],
              ["blue", "#000080"], ["purple", "#800080"]];
var options = {};
var shapeCounter_ = 0;
var colorIndex_ = 0;
var featureTable_;
var map;

function select(buttonId) {
  document.getElementById("hand_b").className="unselected";
  document.getElementById("shape_b").className="unselected";
  document.getElementById(buttonId).className="selected";
}

function stopEditing() {
  select("hand_b");
}

function getColor(named) {
  return COLORS[(colorIndex_++) % COLORS.length][named ? 0 : 1];
}

function getIcon(color) {
  var icon = new GIcon();
  icon.image = "http://google.com/mapfiles/ms/micons/" + color + ".png";
  icon.iconSize = new GSize(32, 32);
  icon.iconAnchor = new GPoint(15, 32);
  return icon;
}

function startShape() {
  select("shape_b");
  var color = getColor(false);
  var polygon = new GPolygon([], color, 2, 0.7, color, 0.2);
  startDrawing(polygon, "Shape " + (++shapeCounter_), function() {
    var cell = this;
    var area = polygon.getArea();
    cell.innerHTML = (Math.round(area / 10000) / 100) + "km<sup>2</sup>";
  }, color);
}
function addFeatureEntry(name, color) {
  currentRow_ = document.createElement("tr");
  var colorCell = document.createElement("td");
  currentRow_.appendChild(colorCell);
  colorCell.style.backgroundColor = color;
  colorCell.style.width = "1em";
  var nameCell = document.createElement("td");
  currentRow_.appendChild(nameCell);
  nameCell.innerHTML = name;
  var descriptionCell = document.createElement("td");
  currentRow_.appendChild(descriptionCell);
  featureTable_.appendChild(currentRow_);
  return {desc: descriptionCell, color: colorCell};
}

function startDrawing(poly, name, onUpdate, color) {
  map.addOverlay(poly);
  poly.enableDrawing(options);
  poly.enableEditing({onEvent: "mouseover"});
  poly.disableEditing({onEvent: "mouseout"});
  GEvent.addListener(poly, "endline", function() {
    select("hand_b");
    var cells = addFeatureEntry(name, color);
    GEvent.bind(poly, "lineupdated", cells.desc, onUpdate);
    GEvent.addListener(poly, "click", function(latlng, index) {
      if (typeof index == "number") {
        poly.deleteVertex(index);
      } else {
        var newColor = getColor(false);
        cells.color.style.backgroundColor = newColor
        poly.setStrokeStyle({color: newColor, weight: 4});
      }
    });
  });
}

function updateMarker(marker, cells, opt_changeColor) {
  if (opt_changeColor) {
    var color = getColor(true);
    marker.setImage(getIcon(color).image);
    cells.color.style.backgroundColor = color;
  }
  var latlng = marker.getPoint();
  cells.desc.innerHTML = "(" + Math.round(latlng.y * 100) / 100 + ", " +
  Math.round(latlng.x * 100) / 100 + ")";
}


function initialize() {
  if (GBrowserIsCompatible()) {
    map = new GMap2(document.getElementById("map"));
    map.setCenter(new GLatLng(37.4419, -122.1419), 13);
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    map.clearOverlays();
    featureTable_ = document.getElementById("featuretbody");
    select("hand_b");
  }
}
function say(text){
	if(saymode!=='test')return;
	if(typeof this.i=='undefined')this.i=0;
	this.i++;
	$('#console').prepend('<div>'+new Date()+' ['+i+'] '+text+'</div>');
}
$(document).ready(function(){
	$('#ID').change(function(){
		window.location='<?php echo end(explode('/',__FILE__)).'?Items_ID=';?>'+$(this).attr('value');
	});
});
</script>
<script src="dev.newmaps2_1_files/mod_dragmod_ctrapi.js" charset="UTF-8" type="text/javascript"></script>
<script src="dev.newmaps2_1_files/mod_polymod_mspe.js" charset="UTF-8" type="text/javascript"></script></head>
<body onload="initialize()" onunload="GUnload">
<div id="mainBody">
<form name="form1" id="form1" method="post" action="resources/bais_01_exe.php" target="w2">

<input type="hidden" name="mode" value="<?php echo $mode;?>" />
select a map:
<select id="ID" name="ID">
<option value="">&lt;select..&gt;</option>
<?php
foreach(q("SELECT ID, Name, SKU, Creator FROM finan_items WHERE ResourceType IS NOT NULL AND DATE(CreateDate)>='2013-03-10' ORDER BY SKU", O_ARRAY_ASSOC) as $ID=>$v){
	?><option value="<?php echo $ID;?>" <?php echo $Items_ID==$ID?'selected':''?>><?php echo $v['SKU'].' - '.$v['Name'].' ('.$v['Creator'].')';?></option><?php
}

?>
</select>
<table><tbody><tr style="vertical-align:top">
  <td style="width:15em">

<table><tbody><tr>
<td><div class="selected" id="hand_b" onclick="stopEditing()"></div></td>
<td><div class="unselected" id="shape_b" onclick="startShape()"></div></td>
</tr></tbody></table>

	<input id="featuredetails" rows="2" type="hidden" />
	<table id ="featuretable">
		<tbody id="featuretbody"></tbody>
	</table>


  </td>
  <td>
    <!-- The frame used to measure the screen size -->
    <div id="frame"></div>
    <div id="map"></div>
  </td>
</tr></tbody></table>

<div id="console"></div>
</form>
</div>
</body></html>