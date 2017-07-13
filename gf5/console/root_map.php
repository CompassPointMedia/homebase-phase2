<?php 
exit('already done? think so');
/*
todo: make sure that there is a link to all maps in HTML version, OR on the page, that Google can spider.

*/
/*
this looks nice, implement this
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<title>Google Maps JavaScript API v3 Example: Info Window Simple</title>
<link href="/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
  function initialize() {
    var myLatlng = new google.maps.LatLng(-25.363882,131.044922);
    var myOptions = {
      zoom: 4,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    var contentString = '<div id="content">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<h1 id="firstHeading" class="firstHeading">Uluru</h1>'+
        '<div id="bodyContent">'+
        '<p><b>Uluru</b>, also referred to as <b>Ayers Rock</b>, is a large ' +
        'sandstone rock formation in the southern part of the '+
        'Northern Territory, central Australia. It lies 335&#160;km (208&#160;mi) '+
        'south west of the nearest large town, Alice Springs; 450&#160;km '+
        '(280&#160;mi) by road. Kata Tjuta and Uluru are the two major '+
        'features of the Uluru - Kata Tjuta National Park. Uluru is '+
        'sacred to the Pitjantjatjara and Yankunytjatjara, the '+
        'Aboriginal people of the area. It has many springs, waterholes, '+
        'rock caves and ancient paintings. Uluru is listed as a World '+
        'Heritage Site.</p>'+
        '<p>Attribution: Uluru, <a href="http://en.wikipedia.org/w/index.php?title=Uluru&oldid=297882194">'+
        'http://en.wikipedia.org/w/index.php?title=Uluru</a> '+
        '(last visited June 22, 2009).</p>'+
        '</div>'+
        '</div>';
        
    var infowindow = new google.maps.InfoWindow({
        content: contentString,
        maxWidth: 200
    });

    var marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        title: 'Uluru (Ayers Rock)'
    });
    google.maps.event.addListener(marker, 'click', function() {
      infowindow.open(map,marker);
    });
  }

</script>
</head>
<body onload="initialize()">
  <div id="map_canvas"></div>
</body>
</html>
*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='focusview';
$localSys['pageType']='Properties Window';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
function expconvert($n){
	$n=explode('E',$n);
	return $n[0] * pow(10,$n[1]);
}

if(false){
	q("update aux_counties set countyid=null, stateid=null");
	prn($qr['affected_rows'].' county/state set to null');
	
	$countyNames=array('borough', 'county', 'parish');
	$fp=fopen('co99_d00a.dat','r');
	while($r=fgetcsv($fp,4000)){
		if(!$skip){
			$skip=true;
			continue;
		}
		if(!in_array(strtolower($r[7]),$countyNames))continue;
		//only take first area
		if($r[8]>1)continue;
		q("UPDATE aux_counties SET PointPerimeterIndex='".$r[1]."',
		StateID='".$r[2]."',
		CountyID='".$r[3]."'
		WHERE co_state='".$r[4]."' AND 
		REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(co_name,'.',''),'-',''),'\'',''),'St ','Saint '),'Ste ','Sainte ')=
		
		REPLACE(REPLACE(REPLACE(REPLACE(REPLACE('".addslashes($r[5])."','.',''),'-',''),'\'',''),'St ','Saint '),'Ste ','Sainte ')");
		if(!$qr['affected_rows'])prn('no match for '.$r[5].', '.$r[4]);
	}
}

$str='';
$reg='/^([0-9]+)/';
$fp=fopen('co99_d00.dat','r');
$i=$j=0;
set_time_limit(60*30);
while($r=fgetcsv($fp,4000)){
	$i++;
	#if($i>1000000){ prn('end at '.($i-1)); break; }
	$r[0]=trim(trim($r[0],'"'));
	if(!strlen($r[0]))continue;
	$r=preg_split('/ +/',$r[0]);
	if(trim($r[0])=='END'){
		/*
		prn('---------------------------');
		prn($str);
		prn('---------------------------');
		*/
		if($inEnd)break;
		$inEnd=true;
		//place record in database
		$label=(q("SELECT concat(co_state,', ',co_name) from aux_counties where pointperimeterindex=$idx", O_VALUE));
		if(!$label){
			prn('no match ppi '.$idx);
			$str='';
			$inEnd=false;
			continue;
		}
		$j++;
		prn($label);
		q("UPDATE aux_counties SET Latitude='$latitude', Longitude='$longitude', PointPerimeter='".rtrim($str,',')."' WHERE PointPerimeterIndex='$idx'");
		prn(str_replace(rtrim($str,','),'blah blah blah',$qr['query']));
		#if($j>=50)exit;
		#if(!$qr['affected_rows'])prn('fail '.$idx);

		$str='';
		$inEnd=false;
		continue;
	}
	if(count($r)==3){
		//echo '. ';
		$idx=$r[0];
		$longitude=expconvert($r[1]);
		$latitude=expconvert($r[2]);
		$stats[]=(array($idx,$latitude,$longitude,($good[]=q("SELECT CONCAT(co_state,', ',co_name) FROM aux_counties WHERE PointPerimeterIndex=$idx", O_VALUE))));
		continue;
	}
	$str.='{"lng":'.expconvert($r[0]).',"lat":'.expconvert($r[1]).'},';
}
echo '<table>';
foreach($stats as $v){
	echo '<tr><td>'.implode('</td><td>',$v).'</td></tr>';
}

echo '</table>';
prn(count($good).' total checks');
foreach($good as $n=>$v){
	if(!trim($v)){
		$bad++;
		unset($good[$n]);
	}
}
if($bad)prn($bad.' non-matches');
sort($good);
prn($good);
exit;

if(false){
	//test; output point peri
	$a= q("SELECT PointPerimeter, Latitude, Longitude FROM aux_counties WHERE CONCAT(StateID,CountyID)='$FIPS'", O_ROW);
	$center='"center":{"lng":"'.$a['Longitude'].'","lat":"'.$a['Latitude'].'"}';
	echo '{"points":['.$a['PointPerimeter'].'],'.$center.',"zoom":10}';
	exit;
}
if(false){
	//--------------- used to in-process a Census Dept file -----------------
	$file=file('data.txt');
	foreach($file as $n=>$v){
		if(strstr($v,'END'))continue;
		$v=trim($v);
		if(preg_match('/^([0-9]+)\b/',$v,$m)){
			$l=$m[1];
			$v=preg_replace('/^[0-9]+ +/','',$v);
			$newcounty=true;
		}else{
			$newcounty=false;
		}
		$c=preg_split('/\s+/',$v);
		$c[0]=expconvert($c[0]);
		$c[1]=expconvert($c[1]);
		if($newcounty){
			$lng[$l]=$c[0];
			$lat[$l]=$c[1];
			$center[$l]='"center":{"lng":"'.$c[0].'","lat":"'.$c[1].'"}';
			continue;
		}
		$vals[$l][]='{"lng":"'.$c[0].'","lat":"'.$c[1].'"}';
	}
	foreach($vals as $l=>$v){
		q("UPDATE aux_counties SET PointPerimeter='".implode(',',$v)."', Latitude='".$lat[$l]."', Longitude='".$lng[$l]."' WHERE co_state='TX' AND PointPerimeterIndex=$l");
		//$vals[$l]='{"points":['.implode(',',$v).'],'.$center[$l].',"zoom":10}';
	}
	//echo $vals[$code];
	echo 'processed ok';
	exit;
	//-------------------------------------------------------------
}
exit;



$hideCtrlSection=false;

$rectangles=q("SELECT Category, SubCategory, HBS_Year, Name, Theme, SKU, Lat1, Lat2, Lat3, Lat4, Lat5, Lat6, Lat7 FROM finan_items WHERE Lat1!=0 AND Lat2!=0", O_ARRAY);
$baseColors=array('green','brown','gold','blue','red');
$i=0;
$Themes=q("SELECT LCASE(Name), Name FROM finan_items_themes", O_COL_ASSOC);
foreach($Themes as $n=>$v){
	$colors[$n]=$baseColors[$i++];
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<title>Historic Maps Restored Locations</title>
<link href="https://google-developers.appspot.com/maps/documentation/javascript/examples/default.css" rel="stylesheet" type="text/css" />
<style type="text/css">
body{
	font-family:Arial, Helvetica, sans-serif;
	font-size:13px;
	}
#toolbar1{
	position:fixed;
	z-index:1000;
	border-bottom:1px solid #000;
	background-color:rgba(0,0,0,.5);
	height:35px;
	color:white;
	width:100%;
	padding:4px 25px;
	bottom:0px;
	}
#toolbar1 td{
	padding-right:25px;
	}
.legend{
	float:left;
	width:15px;
	height:15px;
	}
.legendLabel{
	float:left;
	margin-right:25px;
	margin-left:5px;
	}
</style>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<?php ob_start(); //buffer?>
<script type="text/javascript">
  function initialize() {
    var myLatLng = new google.maps.LatLng(_MID_LAT_, _MID_LON_);
    var myOptions = {
      zoom: 7,
      center: myLatLng,
      mapTypeId: google.maps.MapTypeId.TERRAIN
    };

    var map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);
<?php 
foreach($rectangles as $n=>$v){ 
	extract($v);
	$lowLon=min($Lon1, isset($lowLon) ? $lowLon : 180);
	$highLon=max($Lon2, $highLon);
	$lowLat=min($Lat1, isset($lowLat) ? $lowLat : 180);
	$highLat=max($Lat2, $highLat);
	
	
	$s=preg_replace('/[^a-z0-9]/i','',$SKU)
	?>
    var SKU_<?php echo $s;?> = [
		<?php
		if($Lat3!=0.00){
			for($i=1;$i<=7;$i++){
				if($GLOBALS['Lat'.$i]==0.00)break;
				if($i>1)echo ', ';
				?>new google.maps.LatLng(<?php echo $GLOBALS['Lat'.$i]?>, <?php echo $GLOBALS['Lon'.$i];?>)<?php
			}
		}else{
			?>
			new google.maps.LatLng(<?php echo $Lat1?>, <?php echo $Lon1;?>),
			new google.maps.LatLng(<?php echo $Lat1?>, <?php echo $Lon2;?>),
			new google.maps.LatLng(<?php echo $Lat2?>, <?php echo $Lon2;?>),
			new google.maps.LatLng(<?php echo $Lat2?>, <?php echo $Lon1;?>)
			<?php
		}
		?>
    ];

    historicMaps_<?php echo $s;?> = new google.maps.Polygon({
      paths: SKU_<?php echo $s;?>,
      strokeColor: "<?php echo $colors[strtolower($Theme)] ? $colors[strtolower($Theme)] : '#FF0000';?>",
      strokeOpacity: 0.8,
      strokeWeight: 1,
      fillColor: "<?php echo $colors[strtolower($Theme)] ? $colors[strtolower($Theme)] : '#FF0000';?>",
      fillOpacity: 0.35
    });
	historicMaps_<?php echo $s;?>.parameters={
		'ID':'<?php echo $ID;?>',
		'SKU':'<?php echo $SKU;?>',
		'Name':'<?php echo str_replace('\'','\\\'',$Name);?>',
		'Year':'<?php echo $HBS_Year;?>',
		
	};

    historicMaps_<?php echo $s;?>.setMap(map);
	
	// Add a listener for the click event
	google.maps.event.addListener(historicMaps_<?php echo $s;?>,'click',showBalloon);
	
	infowindow=new google.maps.InfoWindow();
	
	<?php
}
?>
	function showBalloon(event){
		var contentString='<strong>'+this.parameters['Name'].replace(/(LATE )*[1][0-9]{3}s*$/,'')+'</strong><br />';
		contentString += 'Part number: <strong>'+this.parameters['SKU']+'</strong><br />';
		if(n=this.parameters['Year']){
			contentString+= 'Year: <strong>'+n+'</strong><br />';
		}
		
		infowindow.setContent(contentString);
		infowindow.setPosition(event.latLng);
		infowindow.open(map);
	}

  }
</script>
<?php
$out=ob_get_contents();
ob_end_clean();
$out=str_replace('_MID_LAT_',$lowLat+ abs($highLat - $lowLat)/2, $out);
$out=str_replace('_MID_LON_',$lowLon+ abs($highLon - $lowLon)/2, $out);
echo $out;
?>
</head>
<body onLoad="initialize()">
<div id="toolbar1">
<table>
<tr><td>
Historic Maps Restored
</td><td>
<?php
foreach($colors as $n=>$v){
	?><div class="legend" style="border:1px solid #000; background-color:<?php echo $v;?>;"> </div>
	<div class="legendLabel"><?php echo  $Themes[$n];?></div><?php
}
?>
</td><td>
Shown here: <select name="Items_ID" id="Items_ID">
<?php
foreach($rectangles as $v){
	?><option value="<?php echo $v['ID'];?>" <?php echo $v['ID']==$Items_ID?'selected':''?>><?php echo $v['SKU'].' - '.h($v['Name']);?></option><?php
}
?>
</select>
</td><td>

</td></tr>
</table>



</div>
  <div id="map_canvas"></div>
</body>
</html><?php page_end()?>