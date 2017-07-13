<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      #map-canvas { width:400px; height:400px; }
    </style>
	<script language="javascript" type="text/javascript" src="/Library/js/jquery.js"></script>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5vTwnNiqmAZnx8OmDXusVB-C3vGxLBVM&sensor=true">
    </script>
<script>
var op;

function initialize() {
	var myLatLng = new google.maps.LatLng(24.886436490787712, -70.2685546875);
	var mapOptions = {
		zoom: 4,
		center: myLatLng,
		mapTypeId: google.maps.MapTypeId.TERRAIN,
		draggableCursor:"crosshair"
	};

	var onePolygon;

	var map = new google.maps.Map(document.getElementById('map-canvas'),
		mapOptions);

	var triangleCoords = [
		new google.maps.LatLng(25.774252, -80.190262),
		new google.maps.LatLng(18.466465, -66.118292),
		new google.maps.LatLng(32.321384, -64.75737),
	   /* new google.maps.LatLng(25.774252, -80.190262)*/
	];

	// Construct the polygon
	onePolygon = new google.maps.Polygon({
	paths: triangleCoords,
	strokeColor: '#FF0000',
	strokeOpacity: 0.8,
	strokeWeight: 2,
	fillColor: '#FF0000',
	fillOpacity: 0.35,
	editable: true
	});

	var op=onePolygon.setMap(map);

	google.maps.event.addListener(onePolygon,'click',samShow);
	
	google.maps.event.addListener(map, 'mousemove', function(e){
	var lat=parseFloat(e.latLng.lat());
	lat=parseInt(lat*=10000)/10000;
	var lon=parseFloat(e.latLng.lng());
	lon=parseInt(lon*=10000)/10000;
	
	$('#positionA').html(lat);
	$('#positionB').html(lon);
	});

}
google.maps.event.addDomListener(window, 'load', initialize);

function samShow(event){
	// Since this Polygon only has one path, we can call getPath()
	// to return the MVCArray of LatLngs
	var idx=-1;
	var vertices = this.getPath();

  var contentString = "<b>Bermuda Triangle Polygon</b><br />";
  contentString += "Clicked Location: <br />" + event.latLng.lat() + "," + event.latLng.lng() + "<br />";

  // Iterate over the vertices.
  for (var i =0; i < vertices.length; i++) {
    var xy = vertices.getAt(i);
    contentString += "<br />" + "Coordinate: " + i + "<br />" + xy.lat() +"," + xy.lng();
  }
  // Replace our Info Window's content and position
  //say(contentString);

	path=this.getPath();
	for(i=0;i<path.length;i++)if(event.latLng == path.getAt(i))idx=i;
	if(idx>-1){
		if(path.length<4){
			alert('You cannot delete the last three points.  Click the delete button to remove the entire shape instead, and start over');
			return;
		}
		path.removeAt(idx);
	}else{
		alert('click');
	}
}
var saymode='test';
function say(text){
	if(saymode!=='test')return;
	if(typeof this.i=='undefined')this.i=0;
	this.i++;
	$('#console').prepend('<div>'+new Date()+' ['+i+'] '+text+'</div>');
}
$(document).ready(function(){
	$('#deletePolygon').click(function(){
		alert(op);
		google.maps.event.addListener(this,'click',onePolygon.setMap(null));
	});
}); 
 
    </script>


  </head>
  <body>
  <table>
  <tr>
  <td>
    <div id="map-canvas"/>
  </td>
  <td>
  	<table><tr><td width="75" id="positionA"> </td><td width="75" id="positionB"> </td></tr></table>
	<br />
	<input type="button" name="Button" value="Delete" id="deletePolygon" />
  	<div id="position"> </div>
	<div id="console">here we go</div>
  </td>
  </tr>
  </table>
  </body>
</html>