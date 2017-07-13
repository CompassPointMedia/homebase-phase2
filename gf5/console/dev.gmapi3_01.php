<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>jquery gmap api3 I hope</title>
</head>

<body>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5vTwnNiqmAZnx8OmDXusVB-C3vGxLBVM&sensor=true">
    </script>


<script language="javascript" type="text/javascript" src="/Library/js/jquery.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function () {
	alert('test');
    var map = new google.maps.Map(document.getElementById('map'), { center: new google.maps.LatLng(21.17, -86.66), zoom: 9, mapTypeId: google.maps.MapTypeId.HYBRID, scaleControl: true });
    var isClosed = false;
    var poly = new google.maps.Polyline({ map: map, path: [], strokeColor: "#FF0000", strokeOpacity: 1.0, strokeWeight: 2 });
    google.maps.event.addListener(map, 'click', function (clickEvent) {
        if (isClosed)
            return;
        var markerIndex = poly.getPath().length;
        var isFirstMarker = markerIndex === 0;
        var marker = new google.maps.Marker({ map: map, position: clickEvent.latLng, draggable: true });
        if (isFirstMarker) {
            google.maps.event.addListener(marker, 'click', function () {
                if (isClosed)
                    return;
                var path = poly.getPath();
                poly.setMap(null);
                poly = new google.maps.Polygon({ map: map, path: path, strokeColor: "#FF0000", strokeOpacity: 0.8, strokeWeight: 2, fillColor: "#FF0000", fillOpacity: 0.35 });
                isClosed = true;
            });
        }
        google.maps.event.addListener(marker, 'drag', function (dragEvent) {
            poly.getPath().setAt(markerIndex, dragEvent.latLng);
        });
        poly.getPath().push(clickEvent.latLng);
    });
});
</script>
<div id="map"></div>
</body>
</html>