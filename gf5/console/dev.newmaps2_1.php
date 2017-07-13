<?php
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']=4.0;
require('systeam/php/config.php');
require('resources/bais_00_includes.php');

/*
this works as copied over
*/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
 Copyright 2008 Google Inc. 
 Licensed under the Apache License, Version 2.0: 
 http://www.apache.org/licenses/LICENSE-2.0 
 -->
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>Google Maps JavaScript API Example: Editable Polylines</title>
<script src="dev.newmaps2_1_files/maps" type="text/javascript"></script>
<script src="dev.newmaps2_1_files/main.js" type="text/javascript"></script>
<style type="text/css">
body {
  font-family: Arial, sans serif;
  font-size: 11px;
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
    <script type="text/javascript">
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
  document.getElementById("shape_b").className="unselected";
  document.getElementById("line_b").className="unselected";
  document.getElementById("placemark_b").className="unselected";
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
    map.setCenter(new GLatLng(37.4419, -122.1419), 13);
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    map.clearOverlays();
    featureTable_ = document.getElementById("featuretbody");
    select("hand_b");
  }
}
    </script>
  <script src="dev.newmaps2_1_files/mod_dragmod_ctrapi.js" charset="UTF-8" type="text/javascript"></script><script src="dev.newmaps2_1_files/mod_polymod_mspe.js" charset="UTF-8" type="text/javascript"></script></head>
<body onload="initialize()" onunload="GUnload">

<table><tbody><tr style="vertical-align:top">
  <td style="width:15em">

<table><tbody><tr>
<td><div class="selected" id="hand_b" onclick="stopEditing()"></div></td>
<td><div class="unselected" id="placemark_b" onclick="placeMarker()"></div></td>
<td><div class="unselected" id="line_b" onclick="startLine()"></div></td>
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
    <div id="map" style="width: 300px; height: 300px; position: relative; background-color: rgb(229, 227, 223);"></div>
  </td>
</tr></tbody></table>


</body></html>