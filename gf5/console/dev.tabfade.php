<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>tab fade</title>
<script language="javascript" type="text/javascript" src="/Library/js/jquery.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$('.tabRaise a').click(function(){
		if($(this).hasClass('current'))return false;
		if($('#overallWrap').find(":animated").length>0)return false;
		var newList=$(this).attr('href').substring(1);
		//get id of current layer
		var currentList=$('.tabRaise .current').attr('href').substring(1);
		//fade out current layer
		$('#'+currentList).fadeOut(1000, function(){
			//fade in clicked layer
			$('#'+newList).fadeIn(1000, function(){

				// Remove highlighting - Add to just-clicked tab
				$('#tab_'+currentList+' a').removeClass('current');
				$('#tab_'+newList+' a').addClass('current');

				
			});
		});
		return false;
	});
});
</script>
</head>

<body>
<style type="text/css">
.cb{
	clear:both;
	height:0px;
	font-size:1px;
	}
.tabRaise div{
	float:left;
	border:1px solid #333;
	padding:2px 7px;
	border-bottom:none;
	margin-right:15px;
	}
#layerWrap{
	border:1px solid #333;
	padding:10px 15px;
	width:600px;
	clear:both;
	}
.current{
	background-color:cornsilk;
	}
a{
	color:peru;
	text-decoration:none;
	}
a:hover{
	text-decoration:underline;
	}
#tabWrap a:hover{
	text-decoration:none;
	}

</style>
<div id="overallWrap">
	<div id="tabWrap">
		<div class="tabRaise">
			<div id="tab_English">
				<a href="#English" class="current">english</a>
			</div>
			<div id="tab_French">
				<a href="#French">french</a>
			</div>
			<div id="tab_Spanish">
				<a href="#Spanish">spanish</a>
			</div>
		</div>
	</div>
	<div id="layerWrap">
		<div id="English">

          <h3>All The Laws of Physics in One Machine </h3>
          <h3>February 22, 2013</h3>
          <p><em>All the major laws of physics can be found in your lowly Cessna 150 or Piper Cherokee</em></p>
          <div>
            <p>This article covers the laws of physics embodied in aircraft flight controls and instrumentation and communication</p>
          </div>
          <p> Sorry to be brief in this article - and if you want to see more written   on this, please bug me or at least send me some links to others who   have developed this thought.</p>
          <p> In my&nbsp; Cessna 150 are about 15 different instruments and devices;   however, in these devices are embodied all of the major laws of   physics.&nbsp; I thought I'd cover this just briefly:</p>
          <p> <strong>Gyroscopic Precession</strong>: Attitude Indicator, Turn   Coordinator, and Directional Gyro (Gyro-compass).&nbsp; One of the most   interesting parts of Newtonian mechanics.&nbsp; Without this you don't fly in   the clouds very well.&nbsp; Get to the moon without this? Forget it.</p>
          <p> <strong>Fluid Dynamics</strong>: Vertical Speed Indicator (VSI), as   well as the flight controls themselves.&nbsp; A VSI allows us to measure how   fast air rushes into (or out of) a finely-calibrated &quot;leak&quot; into a rigid   container, and translate that into how fast we are climbing or   descending.&nbsp; A handy thing to know on approach and landing, and takeoff   as well.&nbsp; And of course, the wings, elevator and rudder ALL operate on <a href="http://en.wikipedia.org/wiki/Bernoulli%27s_principle">Bernoulli's principle</a></p>
          <p> <strong>Temperature and Pressure</strong>: Altitude Indicator.&nbsp; A   glorified barometer, the AI allows us to know how high we are - IF we   know what the atmospheric pressure is outside.</p>
          <p> <strong>Electromagnetism</strong>: Radios, ADF's and VOR's - here we   learn not only about frequencies but a little bit about line-of-sight   range and other concepts.</p>
          <p> And let's not forget about the engine, whose performance parameters   teach me about leaning mixtures (air-fuel ratios) and performance at   altitue, and horsepower.&nbsp; In addition, aviation weather provides   additional insight into the world we live in.</p>
		  
		</div>
		<div id="French" style="display:none;">

          <span id="result_box" lang="hi" xml:lang="hi">&#2319;&#2325; &#2350;&#2358;&#2368;&#2344; &#2350;&#2375;&#2306; &#2360;&#2349;&#2368; &#2349;&#2380;&#2340;&#2367;&#2325; &#2357;&#2367;&#2332;&#2381;&#2334;&#2366;&#2344; &#2325;&#2375; &#2344;&#2367;&#2351;&#2350;&#2379;&#2306;<br />
          22 &#2347;&#2352;&#2357;&#2352;&#2368;, 2013<br />
          <br />
          &#2349;&#2380;&#2340;&#2367;&#2325;&#2368; &#2325;&#2375; &#2360;&#2349;&#2368; &#2346;&#2381;&#2352;&#2350;&#2369;&#2326; &#2325;&#2366;&#2344;&#2370;&#2344;&#2379;&#2306; &#2309;&#2346;&#2344;&#2368; &#2344;&#2368;&#2330; &#2360;&#2375;&#2360;&#2344;&#2366; 150 &#2351;&#2366; &#2350;&#2369;&#2352;&#2354;&#2368;&#2357;&#2366;&#2354;&#2366; &#2330;&#2375;&#2352;&#2379;&#2325;&#2368; &#2350;&#2375;&#2306; &#2346;&#2366;&#2351;&#2366; &#2332;&#2366; &#2360;&#2325;&#2340;&#2366; &#2361;&#2376;<br />
          <br />
          &#2311;&#2360; &#2309;&#2344;&#2369;&#2330;&#2381;&#2331;&#2375;&#2342; &#2325;&#2375; &#2357;&#2367;&#2350;&#2366;&#2344; &#2313;&#2337;&#2364;&#2366;&#2344; &#2344;&#2367;&#2351;&#2306;&#2340;&#2381;&#2352;&#2339; &#2324;&#2352; &#2311;&#2306;&#2360;&#2381;&#2335;&#2381;&#2352;&#2370;&#2350;&#2375;&#2306;&#2335;&#2375;&#2358;&#2344; &#2324;&#2352; &#2360;&#2306;&#2330;&#2366;&#2352; &#2350;&#2375;&#2306; &#2360;&#2344;&#2381;&#2344;&#2367;&#2361;&#2367;&#2340; &#2349;&#2380;&#2340;&#2367;&#2325;&#2368; &#2325;&#2375; &#2325;&#2366;&#2344;&#2370;&#2344;&#2379;&#2306; &#2325;&#2379; &#2358;&#2366;&#2350;&#2367;&#2354; &#2325;&#2367;&#2351;&#2366; &#2327;&#2351;&#2366;<br />
          <br />
          &#2311;&#2360; &#2354;&#2375;&#2326; &#2350;&#2375;&#2306; &#2326;&#2375;&#2342; &#2360;&#2306;&#2325;&#2381;&#2359;&#2367;&#2346;&#2381;&#2340; &#2361;&#2379;&#2344;&#2375; &#2325;&#2375; &#2354;&#2367;&#2319; - &#2324;&#2352; &#2309;&#2327;&#2352; &#2310;&#2346; &#2325;&#2379; &#2342;&#2375;&#2326;&#2344;&#2375; &#2324;&#2352; &#2311;&#2360; &#2346;&#2352; &#2354;&#2367;&#2326;&#2366; &#2330;&#2366;&#2361;&#2340;&#2375; &#2361;&#2376;&#2306;, &#2325;&#2371;&#2346;&#2351;&#2366; &#2350;&#2369;&#2333;&#2375; &#2348;&#2327; &#2351;&#2366; &#2325;&#2350; &#2360;&#2375; &#2325;&#2350; &#2350;&#2369;&#2333;&#2375; &#2342;&#2370;&#2360;&#2352;&#2379;&#2306; &#2325;&#2379;, &#2332;&#2379; &#2351;&#2361; &#2360;&#2379;&#2330;&#2366; &#2341;&#2366; &#2325;&#2367; &#2357;&#2367;&#2325;&#2360;&#2367;&#2340; &#2325;&#2367;&#2351;&#2366; &#2361;&#2376; &#2325;&#2375; &#2354;&#2367;&#2319; &#2325;&#2369;&#2331; &#2354;&#2367;&#2306;&#2325; &#2349;&#2375;&#2332;.<br />
          <br />
          &#2350;&#2375;&#2352;&#2375; &#2360;&#2375;&#2360;&#2344;&#2366; 150 &#2350;&#2375;&#2306; 15 &#2357;&#2367;&#2349;&#2367;&#2344;&#2381;&#2344; &#2313;&#2346;&#2325;&#2352;&#2339;&#2379;&#2306; &#2324;&#2352; &#2313;&#2346;&#2325;&#2352;&#2339;&#2379;&#2306; &#2325;&#2375; &#2348;&#2366;&#2352;&#2375; &#2350;&#2375;&#2306; &#2325;&#2352; &#2352;&#2361;&#2375; &#2361;&#2376;&#2306;, &#2354;&#2375;&#2325;&#2367;&#2344; &#2311;&#2344; &#2313;&#2346;&#2325;&#2352;&#2339;&#2379;&#2306; &#2350;&#2375;&#2306; &#2349;&#2380;&#2340;&#2367;&#2325;&#2368; &#2325;&#2375; &#2346;&#2381;&#2352;&#2350;&#2369;&#2326; &#2325;&#2366;&#2344;&#2370;&#2344; &#2325;&#2375; &#2360;&#2349;&#2368; &#2360;&#2344;&#2381;&#2344;&#2367;&#2361;&#2367;&#2340;. &#2350;&#2376;&#2306;&#2344;&#2375; &#2360;&#2379;&#2330;&#2366; &#2325;&#2367; &#2350;&#2376;&#2306; &#2311;&#2360; &#2348;&#2360; &#2360;&#2306;&#2325;&#2381;&#2359;&#2375;&#2346; &#2350;&#2375;&#2306; &#2325;&#2357;&#2352; &#2330;&#2366;&#2361;&#2340;&#2375; &#2361;&#2376;&#2306;:<br />
          </span>
			
		</div>
		<div id="Spanish" style="display:none;">

          <span id="result_box" lang="es" xml:lang="es">Todas las leyes de la f&iacute;sica en una sola m&aacute;quina<br />
          22 de febrero 2013<br />
          <br />
          Todas las principales leyes de la f&iacute;sica se pueden encontrar en su humilde Cessna 150 o Piper Cherokee<br />
          <br />
          Este art&iacute;culo trata de las leyes de la f&iacute;sica consagrados en los controles de vuelo de las aeronaves y de instrumentaci&oacute;n y comunicaci&oacute;n<br />
          </span>
			
		</div>
	</div>
</div>
</body>
</html>
