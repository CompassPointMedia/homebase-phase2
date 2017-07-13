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

      function initialize() {
        var myLatLng = new google.maps.LatLng(24.886436490787712, -70.2685546875);
        var mapOptions = {
          zoom: 4,
          center: myLatLng,
          mapTypeId: google.maps.MapTypeId.TERRAIN
        };

        var bermudaTriangle;

        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        var triangleCoords = [
            new google.maps.LatLng(25.774252, -80.190262),
            new google.maps.LatLng(18.466465, -66.118292),
            new google.maps.LatLng(32.321384, -64.75737),
           /* new google.maps.LatLng(25.774252, -80.190262)*/
        ];


        // Construct the polygon
        bermudaTriangle = new google.maps.Polygon({
          paths: triangleCoords,
          strokeColor: '#FF0000',
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: '#FF0000',
          fillOpacity: 0.35,
		  editable: true
        });

        bermudaTriangle.setMap(map);

		google.maps.event.addListener(bermudaTriangle,'click',samShow);

      }
      google.maps.event.addDomListener(window, 'load', initialize);

function samShow(event){

  // Since this Polygon only has one path, we can call getPath()
  // to return the MVCArray of LatLngs
  var vertices = this.getPath();

  var contentString = "<b>Bermuda Triangle Polygon</b><br />";
  contentString += "Clicked Location: <br />" + event.latLng.lat() + "," + event.latLng.lng() + "<br />";

  // Iterate over the vertices.
  for (var i =0; i < vertices.length; i++) {
    var xy = vertices.getAt(i);
    contentString += "<br />" + "Coordinate: " + i + "<br />" + xy.lat() +"," + xy.lng();
  }
  // Replace our Info Window's content and position
  say(contentString);

        path = this.getPath();
        for(i=0;i<path.length;i++){
            if( event.latLng == path.getAt(i)){
                 path.removeAt(i);
            }
        }

}
var saymode='test';
function say(text){
	if(saymode!=='test')return;
	if(typeof this.i=='undefined')this.i=0;
	this.i++;
	$('#console').prepend('<div>'+new Date()+' ['+i+'] '+text+'</div>');
}
 
 
    </script>


  </head>
  <body>
  <table>
  <tr>
  <td>
    <div id="map-canvas"/>
  </td>
  <td>
	<div id="console">here we go</div>
  </td>
  </tr>
  </table>
  </body>
</html>