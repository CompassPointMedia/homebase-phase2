<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
 Copyright 2008 Google Inc. 
 Licensed under the Apache License, Version 2.0: 
 http://www.apache.org/licenses/LICENSE-2.0 
 -->
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Google Maps JavaScript API Example: Editable Polylines</title>
	<!--
	old: ABQIAAAA-O3c-Om9OcvXMOJXreXHAxQGj0PqsCtxKvarsoS-iqLdqZSKfxS27kJqGZajBjvuzOBLizi931BUow
	AIzaSyB5vTwnNiqmAZnx8OmDXusVB-C3vGxLBVM
	-->
<link rel="stylesheet" type="text/css" href="/Library/css/cssreset01.css" />
<style type="text/css">
body {
  font-family: Arial, sans serif;
  font-size: 11px;
}

h1, h2, h3, h4, h5 {
    font-family: Arial,Helvetica,sans-serif;
	}
h1 {
    font-size: 149%;
    margin: 20px 0 10px;
	}
h2 {
    font-size: 129%;
    margin: 15px 0 7px;
	}
h3 {
    font-size: 109%;
    margin: 10px 0 3px;
	}
h4 {
    font-size: 102%;
    margin: 4px 0;
	}
a {
    color: #735317;
	}
#properties {
	}
#headerBar1 {
    padding: 5px 10px 10px 12px;
	}
#mainContainer {
    margin: 10px auto;
    width: 1000px;
	}
#menuWrap1 {
    background-color: #EEEEEE;
	}
#showTester {
    background-color: #CCCCCC;
    cursor: pointer;
    font-size: 5px;
    height: 5px;
    width: 5px;
	}
#tester {
    background-color: #8A9887;
    border: 1px solid #000000;
    display: none;
    padding: 5px;
	}
#mainBody {
    border-bottom: 1px solid #DDDDDD;
    min-height: 325px;
    padding: 10px 20px 30px 10px;
	}
#footer {
    color: #777777;
    font-size: 11px;
	}
input[type="text"], input[type="checkbox"], input[type="password"], select, textarea {
    border-color: #99BD0C;
    border-width: 2px;
	}

#hand_b {
  width:31px;
  height:31px;
  background-image: url(http://google.com/mapfiles/ms/t/Bsu.png);
}
#hand_b.selected {
  background-image: url(http://google.com/mapfiles/ms/t/Bsd.png);
}

#placemark_b {
  width:31px;
  height:31px;
  background-image: url(http://google.com/mapfiles/ms/t/Bmu.png);
}
#placemark_b.selected {
  background-image: url(http://google.com/mapfiles/ms/t/Bmd.png);
}

#line_b {
  width:31px;
  height:31px;
  background-image: url(http://google.com/mapfiles/ms/t/Blu.png);
}
#line_b.selected {
  background-image: url(http://google.com/mapfiles/ms/t/Bld.png);
}

#shape_b {
  width:31px;
  height:31px;
  background-image: url(http://google.com/mapfiles/ms/t/Bpu.png);
}
#shape_b.selected {
  background-image: url(http://google.com/mapfiles/ms/t/Bpd.png);
}
</style>
<script src="/Library/js/global_04_i1.js" language="JavaScript" type="text/javascript"></script>
<script src="/Library/js/common_04_i1.js" language="JavaScript" type="text/javascript"></script>
<script language="javascript" type="text/javascript" src="/Library/js/jquery.js"></script>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=AIzaSyB5vTwnNiqmAZnx8OmDXusVB-C3vGxLBVM" type="text/javascript"></script>
<script type="text/javascript">
var saymode='test';
function say(text){
	if(saymode!=='test')return;
	if(typeof this.i=='undefined')this.i=0;
	this.i++;
	$('#console').prepend('<div>'+new Date()+' ['+i+'] '+text+'</div>');
}


var COLORS = [["red", "#ff0000"], ["orange", "#ff8800"], ["green","#008000"],
              ["blue", "#000080"], ["purple", "#800080"]];
var options = {};
var lineCounter_ = 0;
var shapeCounter_ = 0;
var markerCounter_ = 0;
var colorIndex_ = 0;
var featureTable_;
var map;

function select(buttonId) {
  document.getElementById("hand_b").className="unselected";
  document.getElementById("placemark_b").className="unselected";
  document.getElementById("line_b").className="unselected";
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

function startLine() {
  select("line_b");
  var color = getColor(false);
  var line = new GPolyline([], color);
  startDrawing(line, "Line " + (++lineCounter_), function() {
    var cell = this;
    var len = line.getLength();
    cell.innerHTML = (Math.round(len / 10) / 100) + "km";
  }, color);
}

function startShape() {
  select("shape_b");
  var color = getColor(false);
  var polyPoints = Array();
  <?php if(true){ ?>
  var polyPoint=new GLatLng(29.884625,-97.965817);
  polyPoints.push(polyPoint);
  var polyPoint=new GLatLng(29.884699,-97.957792);
  polyPoints.push(polyPoint);
  var polyPoint=new GLatLng(29.879341,-97.957621);
  polyPoints.push(polyPoint);
  <?php } ?>
  var polygon = new GPolygon(<?php echo true?'polyPoints':'[]';?>, color, 2, 0.7, color, 0.2);
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

function placeMarker() {
  select("placemark_b");
  var listener = GEvent.addListener(map, "click", function(overlay, latlng) {
    if (latlng) {
      select("hand_b");
      GEvent.removeListener(listener);
      var color = getColor(true);
      var marker = new GMarker(latlng, {icon: getIcon(color), draggable: true});
      map.addOverlay(marker);
      var cells = addFeatureEntry("Placemark " + (++markerCounter_), color);
      updateMarker(marker, cells);
      GEvent.addListener(marker, "dragend", function() {
        updateMarker(marker, cells);
      });
      GEvent.addListener(marker, "click", function() {
        updateMarker(marker, cells, true);
      });
    }
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
    map.setCenter(new GLatLng(29.881425, -97.962513), 13);
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    map.clearOverlays();
    featureTable_ = document.getElementById("featuretbody");
    select("hand_b");
  }
}
    </script>
  </head>
<body onload="initialize()" onunload="GUnload">

<table><tr style="vertical-align:top">
  <td style="width:15em">

<table><tr>
<td><div id="hand_b"
	 onclick="stopEditing()"/></td>
<td><div id="placemark_b"
	 onclick="placeMarker()"/></td>
<td><div id="line_b"
	 onclick="startLine()"/></td>
<td><div id="shape_b"
	onclick="startShape()"/></td>
</tr></table>

    <input type="hidden" id="featuredetails" rows=2>
    </input>
<p>To draw on the map, click on one of the buttons and then click on the map.  Double-click to stop drawing a line or shape. Click on an element to change color. To edit a line or shape, mouse over it and drag the points.  Click on a point to delete it.
</p>
     <table id ="featuretable">
     <tbody id="featuretbody"></tbody>
    </table>
	
<div id="showTester" title="Javascript Tester" onClick="g('tester').style.display='block';">&nbsp;</div>
<div id="tester" style="display:block;">
	<a href="#" onclick="g('ctrlSection').style.display=op[g('ctrlSection').style.display]; return false;">iframes</a><br />
	<textarea name="test" cols="65" rows="4" id="test">clear_form();</textarea><br />
	<input type="button" name="button" value="Test" onClick="jsEval(g('test').value);"><br />
	<textarea id="result" name="result" cols="65" rows="3" ></textarea>
</div>
<div id="ctrlSection" style="display:none">
	<iframe name="w1" src="/Library/js/blank.htm"></iframe>
</div>
  </td>
  <td>
    <!-- The frame used to measure the screen size -->
    <div id="frame"></div>
    <div id="map" style="width: 500px; height: 500px"></div>
  </td>
</tr></table>
<div id="console"></div>
</body>
</html>
