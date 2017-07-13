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

?><!DOCTYPE html>
<html>
  <head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
    <title>Drawing Tools</title>


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


<script language="javascript" type="text/javascript" src="/Library/js/jquery.js"></script>


<?php
/*
2013-03-24: this is the beginning of new map using g2 codebase, and much better control of polygon + perimeter and area figuring


todo:
commented out anon. cb function in startShape()=>startDrawing() - only handled area, return it

*/
if($Lat1){
	$coords=explode('|',$Lat1);
	foreach($coords as $i=>$v)$coords[$i]=explode(',',$v);
}
if(strstr($Lat2,',')){
	$center=explode(',',$Lat2);
	$center=array($center[1],$center[0]);
}
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=drawing"></script>
<style type="text/css">
#map{
	width:500px;
	height:450px;
	}
#delete-button {
	margin-top: 5px;
	}
</style>
<script type="text/javascript">
var initialized=false;
var drawingManager;
var selectedShape;

var color='#1E90FF';

var currentCoords;
<?php
if($coords){
?>//coordinates of existing map
var currentCoords=[];
<?php
foreach($coords as $n=>$v){
	?>currentCoords.push(new google.maps.LatLng(<?php echo implode(',',$v);?>));<?php echo "\n";
}
}
?>
function clearSelection() {
	if (selectedShape) {
	  selectedShape.setEditable(false);
	  selectedShape = null;
	}
}
function setSelection(shape) {
	clearSelection();
	selectedShape = shape;
	shape.setEditable(true);
	var polygonOptions = drawingManager.get('polygonOptions');
	polygonOptions.fillColor = color;
	drawingManager.set('polygonOptions', polygonOptions);
}
function deleteSelectedShape() {
	if (selectedShape) {
	  selectedShape.setMap(null);
	}
}
function runningLatLon(event){
	var lat=parseFloat(event.latLng.lat());
	lat=parseInt(lat*=10000000)/10000000;
	var lon=parseFloat(event.latLng.lng());
	lon=parseInt(lon*=10000000)/10000000;
	
	$('#positionA').html(lat);
	$('#positionB').html(lon);
}
function initialize() {
	if(initialized)return;
	initialized=true;
	var map = new google.maps.Map(document.getElementById('map'), {
		zoom: <?php echo $Lat3>0 ? $Lat3 : 10?>,
		center: new google.maps.LatLng(<?php echo $center?implode(',',$center):'29.883221,-97.941399';?>),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		disableDefaultUI: true,
		zoomControl: true,
		draggableCursor:'crosshair'
	});
	
	
	google.maps.event.addListener(map, 'mousemove', runningLatLon);
	
	
	if(currentCoords){
		// Construct the polygon
		onePolygon = new google.maps.Polygon({
		paths: currentCoords,
		strokeColor: color,
		strokeOpacity: 0.8,
		strokeWeight: 1,
		fillColor: color,
		fillOpacity: 0.35,
		editable: true
		});
		onePolygon.setMap(map);
		google.maps.event.addListener(onePolygon,'click',samShow);
		google.maps.event.addListener(onePolygon, 'mousemove', runningLatLon);
	}
	var polyOptions = {
		strokeWeight: 1,
		strokeColor: color,
		fillOpacity: 0.35,
		fillColor: color,
		editable: true
	};
	// Creates a drawing manager attached to the map that allows the user to draw
	// markers, lines, and shapes.
	drawingManager = new google.maps.drawing.DrawingManager({
	  /*drawingMode: google.maps.drawing.OverlayType.POLYGON,*/
	
	
	drawingModes: [
	  google.maps.drawing.OverlayType.POLYGON,
	],
	
	  polygonOptions: polyOptions,
	  map: map
	});
	
	google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
		
		// Switch back to non-drawing mode after drawing a shape.
		drawingManager.setDrawingMode(null);
	
		// Add an event listener that selects the newly-drawn shape when the user
		// mouses down on it.
		var newShape = e.overlay;
		newShape.type = e.type;
		google.maps.event.addListener(newShape, 'click', function() {
		  setSelection(newShape);
		});
		setSelection(newShape);
		google.maps.event.addListener(newShape, 'click', samShow);
		
	});
	
	// Clear the current selection when the drawing mode is changed, or when the
	// map is clicked.
	google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
	google.maps.event.addListener(map, 'click', clearSelection);
	google.maps.event.addDomListener(document.getElementById('delete-button'), 'click', deleteSelectedShape);
	
}

function samShow(event){
	// Since this Polygon only has one path, we can call getPath()
	// to return the MVCArray of LatLngs
	var idx=-1;
	var vertices = this.getPath();

	path=this.getPath();
	for(i=0;i<path.length;i++)if(event.latLng == path.getAt(i))idx=i;
	if(idx>-1){
		if(path.length<4){
			alert('You cannot delete the last three points.  Click the delete button to remove the entire shape instead, and start over');
			return;
		}
		path.removeAt(idx);
	}else{
		this.setMap(null);
	}
}

google.maps.event.addDomListener(window, 'load', initialize);

$(document).ready(function(){
	$('#ID').change(function(){
		window.location='<?php echo end(explode('/',__FILE__)).'?Items_ID=';?>'+$(this).attr('value');
	});
});

</script>
</head>
<body>


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
<style type="text/css">
.mtab{
	border-collapse:collapse;
	}
.mtab td{
	padding:7px;
	border:1px solid #ccc;
	}
</style>
<table class="mtab">
<tr>
	<td><div id="map"></div></td>
	<td>
	<input type="button" id="delete-button" value="Delete Selected Shape" />
	<br />
  	<table><tr><td class="tar" width="75" id="positionA"> </td><td class="tar" width="75" id="positionB"> </td></tr></table>
	<br>
	<br>
	<a href="javascript:initialized=false;initialize();">reload map</a>

	</td>
</tr>
</table>

</form>
</div>
</body>
</html>
