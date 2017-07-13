<?php 
/*
BAIS Login (for San Marcos Area Arts Council) version 2.0 - html template 
This is improved from the GF use of BAIS Login, and locations for js and css file locations have been moved closer to those for the Ecommerce Site version 4.0
*/
//identify this script/GUI
$localSys['scriptID']='generic';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');


//------------------------ Navbuttons head coding v1.43 -----------------------------
//change these first vars and the queries for each instance
$object='Assns_ID';
$recordPKField='ID'; //primary key field
$navObject='Assns_ID';
$updateMode='updateAssn';
$insertMode='insertAssn';
$deleteMode='deleteAssn';
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
$ids=q("SELECT ID FROM sma_assns WHERE ResourceType IS NOT NULL ORDER BY Name",O_COL);
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
if(strlen($$object) || $Assns_ID=q("SELECT ID FROM sma_assns WHERE ResourceToken!='' AND ResourceToken='$ResourceToken' AND ResourceType IS NOT NULL", O_VALUE)){
	//get the record for the object
	if($a=q("SELECT * FROM sma_assns WHERE ID='".$$object."'",O_ROW)){
		$mode=$updateMode;
		@extract($a);
	}else{
		//object may have been deleted by another user, least perplexing approach is to present insert mode
		$mode=$insertMode;
		unset($$object);
		$nullAbs=$nullCount+1;
	}
}else{
	$mode=$insertMode;
	$$object=$ID=quasi_resource_generic($MASTER_DATABASE, 'sma_assns', $ResourceToken, $typeField='ResourceType', $sessionKeyField='sessionKey', $resourceTokenField='ResourceToken', $primary='ID', $creatorField='Creator', $createDateField='CreateDate' /*, C_DEFAULT, $options */);

	$nullAbs=$nullCount+1; //where we actually are right then
}
//--------------------------- end coding --------------------------------


$hideCtrlSection=false;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Combo Agent/Property Invoice Due/Forecast Report - Great Locations Rental Locating</title>



<link rel="stylesheet" type="text/css" href="/Library/css/cssreset01.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
.yat .bottom td, .yat th{
	background-color:darkolivegreen;
	color:white;
	border:1px solid #000;
	}
.yat2 {
	border-collapse:collapse;
	margin-top:15px;
	}
.yat2 h2{
	margin:0px;
	}
.yat2 td{
	border:1px solid #000;
	padding:10px 10px;
	}
.void td{
	background-color:#eee;
	color:#444;
	}
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle coding */
var thispage='report_comboreport.php';
var thisfolder='console';
var browser='Moz';
var ctime='1336679330';
var PHPSESSID='f5250dbbe7d230df3b8ca530ed8783e9';
//for nav feature
var count='';
var ab='';
var isEscapable=2;
var isDeletable=0;
var isModal=0;
var talks=0; //whether this page broadcasts overall state changes which other pages listen for
var listens=0;

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already

function interlock1(n){
	g('agentList').style.display=(n=='agent'?'block':'none');
	g('propertyList').style.display=(n=='property'?'block':'none');
	g('agents').disabled=(n=='agent'?false:true);
	g('properties').disabled=(n=='property'?false:true);
}
function interlock2(n){
	g('dates').style.display=(n=='5' || n=='6'?'inline':'none');
}
function setForm(n){
	if(n==1){
		g('form1').action='';
		g('mode').value='sendMailing';
		g('form1').target='w2';
		g('form1').method='post';
		g('form1').submit();
		//revert
		g('form1').target='';
		g('form1').method='get';
		g('mode').value='';
		return false;
	}
}
</script>

<link rel="stylesheet" type="text/css" href="/Library/css/DHTML/rich_calendar.css">
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/rich_calendar_lang_en.js"></script>


</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
<h3>Combo Agent/Property Invoice Due/Forecast Report</h3>
<p>
Currently signed in: <strong>Melliza Martinez</strong>
</p>



	<div id="toolbar1" class="printhide">
	Summarize report by: 
	<select name="orderBy" id="orderBy" onChange="dChge(this);interlock1(this.value);">
		<option  value="agent">Agent</option>
		<option selected value="property">Property</option>
	</select>
	<br />


	<div id="agentList" style="display:none">
		<span class="gray">Select agent(s):</span><br />
	<select name="agents[]" size="10" multiple="multiple" id="agents" style="min-width:350px;" disabled>
	<option value="-1" selected>(All agents)</option>
	<optgroup label="Top Producers"><option value="ricky" >Clark, Ricky (63)</option> <option value="jenkins" >Jenkins, Tyler (40)</option> <option value="smccoy" >McCoy, Steven (43)</option> <option value="sparks" >Sparks, Jason (249)</option> <option value="stoker" >Stoker, Scott (55)</option> <option value="jordan" >Turcotte, Jordan (71)</option> <option value="mturcotte" >Turcotte, Mellissa (39)</option> </optgroup><optgroup label="Other"><option value="mark" >Mark</option> <option value="sblack" >Black, Steve</option> <option value="nathan" >Flaga, Nathan</option> <option value="sam" >Fullman, Samuel</option> <option value="mike" >Harper, Mike</option> <option value="jhutchins" >Hutchins, Julie</option> <option value="eric" >Keller, Eric</option> <option value="melliza" >Martinez, Melliza</option> <option value="robbyroden" >Roden, Robby</option> <option value="dsmith" >Smith, Debbie</option> <option value="swinney" >Swinney</option> <option value="sswinney" >Swinney, Scott</option> <option value="jason" >Tarr, Jason</option> <option value="torrey" >Torrey, Chris</option> <option value="vandine" >Vandine, Chris</option> <option value="dzalman" >Zalman, Danielle</option> </optgroup>	</select>
	</div>



	<div id="propertyList" style="display:block">
		<span class="gray">Select propertie(s):</span><br />
	<select name="properties[]" size="10" multiple="multiple" id="properties" onChange="dChge(this);" style="min-width:350px;" >
	<option value="-1" >(All properties)</option>
	<optgroup label="Apartments"><option value="2" selected>Autumn Chase/Caramel Partners (3)</option><option value="100" selected>Blanco River Duplexes (1)</option><option value="126" selected>Cabana Beach (2)</option><option value="10" selected>Cedars (2)</option><option value="13" >Clarewood (1)</option><option value="16" >Colony Square (1)</option><option value="21" >Country Oaks (1)</option><option value="391" >Encino Pointe (1)</option><option value="49" >Florencia Villas (1)</option><option value="57" selected>Hillside Ranch (2)</option><option value="58" >Langtry (1)</option><option value="101" >Palazzo (1)</option><option value="69" >Post Oak Villas (1)</option><option value="452" >Purgatory Creek (1)</option><option value="75" >River Oaks Villas (2)</option><option value="79" >Spring West (6)</option><option value="80" >Stadium View (1)</option><option value="93" >State Flats (1)</option><option value="46" >The Colony (1)</option><option value="27" >The Heights (42)</option><option value="28" >The Heights II of San Marcos (1)</option><option value="95" >The Lodge (3)</option><option value="88" >University Springs (1)</option><option value="331" >Vantage at Buda (1)</option><option value="318" >Vantage at San Marcos (7)</option><option value="51" >Villas at Willow Springs (1)</option><option value="48" >Westfield (1)</option></optgroup><optgroup label="Non-Apartments (SFR)"><option value="312" >Austin Landmark Prop. Services (1)</option><option value="447" >AustinTxHomeSales (1)</option><option value="428" >Gables Park Plaza (1)</option><option value="402" >Mission Hills-San Antonio (1)</option><option value="173" >Other (1)</option><option value="466" >Prime Property Management (1)</option><option value="202" >Recar &amp; Assoc / Statewide Prop (1)</option><option value="332" >Terri Wimmer/GMA Property (1)</option></optgroup><optgroup label="Apartments"><option value="473" >Ardent Estates</option><option value="144" >Asbury Place Apartments</option><option value="5" >Audrey Oaks</option><option value="232" >Augusta at Gruene</option><option value="6" >Avalon</option><option value="141" >Barton Creek Landing</option><option value="8" >Bishops Square</option><option value="437" >Blanco River Lodge</option><option value="234" >Braunfels Haus</option><option value="71" >Braunfels Place</option><option value="11" >Champions Crossing</option><option value="270" >Colonial Grand at Onion Creek</option><option value="43" >Cornerstone-VJE</option><option value="225" >Cotton Crossing</option><option value="23" >Cypress Gardens</option><option value="24" >Dakota Ranch</option><option value="140" >Deerfield</option><option value="86" >Endways-Twin Seville</option><option value="29" >Englebrook</option><option value="98" >Executive Townhouse</option><option value="228" >Grand Cypress</option><option value="229" >Gruenewood Villa</option><option value="52" >Herndon House</option><option value="55" >Highcrest</option><option value="135" >Highlands Hill Country</option><option value="56" >Hill Country</option><option value="407" >Iconic Village</option><option value="50" >Kelsea Place</option><option value="335" >La Sierra</option><option value="194" >La Vista</option><option value="216" >Landmark Garden Apts</option><option value="334" >Landmark Loft Apts</option><option value="231" >Langtry Village</option><option value="406" >Les Chateaux</option><option value="72" >Lindsey and Burleson St Apts</option><option value="400" >Logan Ridge</option><option value="60" >Metropolitan</option><option value="145" >Mill Bridge</option><option value="59" >Mill Street</option><option value="138" >Monarch Court</option><option value="64" >Mosscliff</option><option value="227" >Northwood Luxury Apartments</option><option value="63" >Outpost</option><option value="65" >Palazzo</option><option value="67" >Palm Square</option><option value="62" >Park Hill</option><option value="68" >Polo Club</option><option value="70" >Post Road Place</option><option value="74" >Ranch Duplex</option><option value="233" >River Park</option><option value="54" >Riverside Ranch</option><option value="76" >Saddle Creek</option><option value="149" >Sagewood Trail Duplexes</option><option value="99" >Sanctuary Lofts</option><option value="77" >Savannah Club</option><option value="137" >Skiles-Chestnut Place</option><option value="22" >Skiles-Courtyard</option><option value="112" >Stone Brook Seniors</option><option value="82" >Summit</option><option value="160" >The 605</option><option value="96" >The Edge</option><option value="142" >Torrey Place</option><option value="84" >Townwood</option><option value="85" >Treehouse</option><option value="87" >University Club</option><option value="393" >Vantage At Plum Creek</option><option value="89" >Verandah</option><option value="90" >Versailles</option><option value="92" >Village Green</option><option value="78" >Villagio</option><option value="444" >Vintage Pads</option><option value="230" >Waterford Place</option><option value="136" >Waters at Bluff Springs</option><option value="146" >Westshore Colony</option><option value="200" >Windmill Duplexes</option><option value="47" >Windmill Townhomes</option><option value="81" >zStonegate</option><option value="83" >zTimbers</option></optgroup><optgroup label="Non-Apartments (SFR)"><option value="457" >11811 Charing Cross</option><option value="190" >1211 Girard</option><option value="438" >1231 N. LBJ #7</option><option value="425" >1836 Property Management</option><option value="445" >2207 Jesse Owens Dr.</option><option value="276" >221 Lexington</option><option value="443" >2511 Keepsake</option><option value="222" >340 Donatello</option><option value="221" >340 Donatello</option><option value="433" >509 Alabama</option><option value="439" >9005 Sedgemoor #B</option><option value="205" >A-TX Management</option><option value="455" >A1 Vacation</option><option value="203" >Access Realty</option><option value="283" >Adina Mercer</option><option value="421" >Agent Realty</option><option value="365" >Ala Pourmahram</option><option value="289" >Allison Pflaum</option><option value="409" >Amli at Lantana Ridge</option><option value="256" >AMS</option><option value="348" >Amstar Properties</option><option value="219" >Amy &amp; John Bruce</option><option value="317" >Amy Young</option><option value="412" >Andrea Quindnez</option><option value="297" >Andrew Ewig</option><option value="220" >Apante Investments</option><option value="345" >April Realty Services, Inc.</option><option value="450" >Arboretum at Stone Lake</option><option value="120" >Assured Property Management</option><option value="240" >Athold Management</option><option value="398" >Atlas Realty</option><option value="255" >Austin City Lights</option><option value="349" >Austin Homes Realty</option><option value="113" >Austin Landmark Properties</option><option value="303" >Autumn Lapaglia</option><option value="163" >Barry Davidson</option><option value="301" >Beaver Real Estate</option><option value="294" >Belinda Reingold</option><option value="290" >Bell Property Services</option><option value="389" >Bella Vista</option><option value="152" >Bill Figol</option><option value="342" >Bill Herzog</option><option value="188" >Bob Azar</option><option value="324" >Bob Mathis</option><option value="319" >Bonds and Dotson Properties</option><option value="265" >Bonnie Heilig</option><option value="459" >Brazoz Ranch Apartments</option><option value="168" >Brenda Smith</option><option value="427" >Brian Barker</option><option value="226" >Bruanfels Place</option><option value="327" >Bryan East</option><option value="382" >Buda/Kyle Real Estate</option><option value="248" >Burt Stovall</option><option value="373" >C-Choice Properties Inc.</option><option value="458" >Camden-Gaines Ranch</option><option value="132" >Campus Lodge</option><option value="264" >CAnderson/Keller-Williams</option><option value="218" >Capital Area Realty</option><option value="238" >Capstone Property Management</option><option value="395" >Carol Dochen Realtors, Inc.</option><option value="291" >Carson Properties</option><option value="442" >Casa Grande Realty LLC</option><option value="214" >Casey Wenzel</option><option value="162" >Cathy Roach</option><option value="366" >CBI Management</option><option value="134" >CC Property Management</option><option value="193" >Centex Realty</option><option value="322" >Century 21 Action Realty</option><option value="242" >Century 21 Ripley (SW)</option><option value="355" >Century 21-Hill Country</option><option value="245" >Century 21-The Excell Team</option><option value="268" >CG Property Management</option><option value="362" >Charles Fuentes</option><option value="388" >Chesney Coker</option><option value="422" >Chris Jones</option><option value="295" >Christie O'Connell</option><option value="380" >Christine Faske</option><option value="343" >Christopher Crenshaw</option><option value="15" >Clear Springs</option><option value="169" >Cliff Lewis</option><option value="117" >Club at Summer Valley</option><option value="260" >Coldwell Banker-United Realtor</option><option value="187" >Comal River Condos</option><option value="375" >Conni Flora</option><option value="110" >Conway, Chris Hull Prop</option><option value="356" >Cottages-Austin</option><option value="184" >Country Affordable Homes</option><option value="129" >Courtney Ledsworth</option><option value="105" >Craddock</option><option value="423" >Curtis Watts</option><option value="307" >Cyndi Dullnig</option><option value="360" >Dana Warren</option><option value="462" >Dane, Scott and Michelle</option><option value="167" >Dante Feole</option><option value="296" >David Moore</option><option value="424" >David Sirgi</option><option value="125" >Dawn Loding</option><option value="275" >Deming Real Estate</option><option value="178" >Denise Cheney and Bill Pratt</option><option value="153" >Dennis Figol</option><option value="106" >Denver Dunlap</option><option value="292" >Don Moriarty</option><option value="26" >Durr Property Management</option><option value="284" >E. Scott Ross</option><option value="441" >Eanes Properties, Inc</option><option value="157" >Ed Holman</option><option value="417" >Edge Creek Condominiums</option><option value="302" >EFA Properties</option><option value="165" >Ehrich</option><option value="104" >Endways</option><option value="12" >Endways-Chelsea Villa</option><option value="288" >ERA Millenium-Kyle</option><option value="116" >ERA Millennium-Buda</option><option value="241" >Erickson &amp; Associates</option><option value="484" >Estancia</option><option value="378" >Estates at Southpark Meadows</option><option value="198" >ETM Realty</option><option value="311" >Exit Realty New Braunfels</option><option value="404" >Fairway Properties</option><option value="408" >First Source Realty Alliance</option><option value="111" >Forr Real LTD</option><option value="237" >Francisco Serna</option><option value="461" >Gardens</option><option value="370" >Gary Smith</option><option value="383" >Geary Louis Real Estate</option><option value="385" >Geronimo Palacios</option><option value="347" >Gold Key Real Estate, Inc.</option><option value="363" >Golden Realty Services</option><option value="281" >Goodwin Properties</option><option value="454" >Hal Davis</option><option value="251" >Harrison-Pearson Assoc</option><option value="387" >Hart Properties</option><option value="323" >Help-U-Sell Hill Country</option><option value="277" >Heriberto Hurtado</option><option value="102" >Heritage Square</option><option value="305" >Hidden Lakes Apts.</option><option value="53" >Hidden Village</option><option value="235" >Highland Realty</option><option value="262" >Home Simple Realty</option><option value="350" >Hunter Strickland</option><option value="413" >Iron Rock Ranch</option><option value="414" >Ivan Lim, Attn: Sam Wong</option><option value="166" >James Neurenberg</option><option value="286" >James Reed</option><option value="249" >Jason Howell</option><option value="208" >JB Goodwin</option><option value="300" >JB Goodwin Realtors</option><option value="207" >Jim Huffman</option><option value="269" >JK Properties LLC</option><option value="212" >Jodie &amp; Catherine Grayless</option><option value="121" >Joe Ciccarelo</option><option value="279" >Joe Davis / Centex Realty</option><option value="115" >Joel Barnard</option><option value="396" >John McKinnon</option><option value="384" >June Dowling</option><option value="196" >Karl Hime Co.</option><option value="315" >Keith Allison &amp; Kaci Price</option><option value="344" >Keller Williams Austin SW</option><option value="346" >Keller Williams, Steve Mallett</option><option value="468" >Kendrick, Odie</option><option value="201" >Kenwood Townhomes</option><option value="306" >KPS King Properties</option><option value="122" >Kyle homes</option><option value="263" >Kyle Premiere Realty</option><option value="313" >Kyle Real Estate</option><option value="224" >Landmark</option><option value="195" >Lara Melsha</option><option value="199" >Larkspur Townhomes</option><option value="287" >Larry Edgeman</option><option value="204" >Legacy At Western Oaks</option><option value="426" >Limestone Ranch</option><option value="369" >Lindsey Lofts</option><option value="368" >Lindsey Lofts</option><option value="397" >Loma Verde Realty</option><option value="337" >Lone Star Realty Group</option><option value="215" >LTD 1</option><option value="351" >Mandolin</option><option value="430" >Marathon RealEstate</option><option value="161" >Mark Brown</option><option value="272" >Marshall Dandridge</option><option value="432" >Matt Callaway</option><option value="280" >Mcginnis and Coker LLC</option><option value="435" >Michele Bauman</option><option value="392" >Mila Properties</option><option value="446" >Milestone Management</option><option value="440" >Mission Grace Woods</option><option value="420" >Mitch Moore</option><option value="431" >Monica Maldonado</option><option value="236" >Monterone at Steiner Ranch</option><option value="247" >Moran Property Management 264 Goliad Dr.</option><option value="254" >Myan Management Group</option><option value="170" >Mystic River</option><option value="285" >New Braunfels Leasing 802</option><option value="333" >New Braunfels Place</option><option value="177" >New Home Solutions Realty</option><option value="399" >New Homes Market Center</option><option value="434" >Nic Canwe</option><option value="257" >Olin &amp; Bobby Melton</option><option value="340" >ONG Management</option><option value="299" >Onion Creek Luxury Apartments</option><option value="250" >Opportunity Enterprises LLC</option><option value="358" >ParrRealty&amp;PropertyInvestment</option><option value="244" >Patton Property</option><option value="252" >Patton Property  Management</option><option value="131" >Paul Lyon</option><option value="183" >Paul White</option><option value="130" >Philip Donohoe</option><option value="364" >Place Properties</option><option value="310" >Predential Realty-Canyon Lake</option><option value="259" >Premier Team</option><option value="405" >Prestige Property Management</option><option value="253" >Prime Property AUSTIN</option><option value="304" >Prime Residential Properties</option><option value="246" >Property Mgmt Prof., Inc.</option><option value="309" >Prudential Classic Realty-NB</option><option value="325" >Prudential Texas Realty</option><option value="372" >Qualle Investments</option><option value="192" >Randall Morris &amp; Associates</option><option value="197" >Ray Vollette</option><option value="282" >Ready Real Estate</option><option value="223" >Realty World-June Z. Barnett</option><option value="258" >Regency Park Apartments</option><option value="185" >Reliable Property Management</option><option value="338" >REMAX Capital City</option><option value="243" >Remax Fortune</option><option value="209" >Remax Heart of Texas</option><option value="298" >REMAX Marvin Walker &amp; Assoc</option><option value="293" >Remington House Apartments</option><option value="189" >Reserve at Walnut Creek</option><option value="321" >Resource One</option><option value="410" >Ricardo Vargas</option><option value="379" >Richard Perez</option><option value="217" >Ridgeview Apartments</option><option value="191" >River Oaks Austin</option><option value="171" >River Place</option><option value="429" >RiverLodge</option><option value="401" >Riverside Place</option><option value="155" >Robbie Wiley</option><option value="179" >Robert Cotner</option><option value="308" >Robert Edmiston</option><option value="377" >Roberta Ryder</option><option value="266" >Robinson Co. Realtors</option><option value="436" >Robinson Company Realtors 195 Musgrav</option><option value="267" >Rollie Miller</option><option value="124" >Roy McMullin</option><option value="361" >Roy Mendoza</option><option value="328" >RPM Property Management</option><option value="419" >Ryan Burke</option><option value="448" >Ryan Kimbro</option><option value="128" >Sam Fuller</option><option value="213" >Samuel Fullman</option><option value="411" >Secant Realty</option><option value="123" >Sharon Peters</option><option value="159" >Sienna Properties</option><option value="108" >Skiles</option><option value="336" >Sky Realty</option><option value="353" >Southpark Meadows-Terraces</option><option value="330" >Spradling Properties</option><option value="386" >Steven Henry/229 CAM LLC</option><option value="154" >Stone Creek Ranch</option><option value="109" >Sublease</option><option value="180" >Terry Blackwell</option><option value="352" >Texas Income Property</option><option value="367" >Texas Star Realtors</option><option value="357" >Texas Urban Realty</option><option value="148" >The Acorn Group</option><option value="449" >The Ball Park at Austin</option><option value="359" >The Gables at the Terrace</option><option value="374" >The Grove</option><option value="278" >The Key Companies</option><option value="416" >The Lynn at Country Club</option><option value="174" >The Marquis at Iron Rock Ranch</option><option value="403" >The Monte Cristo</option><option value="341" >The Oaks - Skiles</option><option value="316" >The Property Connection</option><option value="354" >The Property Society</option><option value="486" >The Settlement Apartments</option><option value="239" >Tim Glynn</option><option value="172" >Tom Blackwell</option><option value="381" >Travis Kilpatrick</option><option value="371" >Trend Properties</option><option value="456" >Tuscany Apartment Homes</option><option value="394" >United Apartment Group</option><option value="186" >UT Realty</option><option value="139" >Vance J Elliott</option><option value="119" >Velma Dorea</option><option value="211" >Verde Shadow Brook</option><option value="181" >Village at Spring Town</option><option value="271" >Villages of Bella Vista</option><option value="376" >Villages of Sage Creek</option><option value="320" >Vintage Living LLC</option><option value="418" >Virgillio Altimirano</option><option value="451" >Water's Edge</option><option value="261" >Waterstone Apartments</option><option value="147" >Whilst Phew</option><option value="415" >Zip Realty</option></optgroup>	</select>
	</div>
	<br />
		Invoice types: 
		  <select name="Status" id="Status" onchange="dChge(this);interlock2(this.value);">
		  <option value="1" selected>Due &amp; Past Due Invoices</option>
		  <option value="2" >Due Invoices</option>
		  <option value="3" >Past Due Invoices</option>
		  <option value="4" >Partially Paid Invoices</option>
		  <option value="5" >Forecast Invoices</option>
		  <option value="6" >Voided Invoices</option>
		  <option value="7" >Late-entered Invoices</option>
	      </select>
		<span id="dates" style="display:none">
	    from: <img align="absbottom" onClick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input name="ReportDateFrom" type="text" id="ReportDateFrom" value="04/01/2012" size="14" />
		to
		<img align="absbottom" onClick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input name="ReportDateTo" type="text" id="textfield4" value="04/30/2012" size="14" />
		</span>

		<input type="button" name="button" id="button1" value="Update" onClick="g('form1').setAttribute('method','get');g('form1').setAttribute('target','');g('form1').action='';g('form1').submit();return false;" /> &nbsp;
		<input type="button" name="button" id="button2" value="Print" onClick="window.print();" /> 
		&nbsp;
		<input type="button" name="button" id="button4" value="Close" onClick="window.close();" />&nbsp;	
	  </div>
	</div>

</div>
<div id="mainBody">

<div class="suite1">




			<div class="header">
							<div class="fr">
				<label><input type="checkbox" name="mailing[2]" value="1" /> Select for mailing</label>
				</div>
							<h2><a href="properties2.php?Properties_ID=2" title="View/edit this property full record" onclick="return ow(this.href,'l1_properties','750,700');">Autumn Chase/Caramel Partners</a></h2>
			<p>
			1606 N IH 35<br />San Marcos, TX  78666<br />(512) 754-6144(p)<br />(512) 754-6153(f)<br /><span class="gray">M-F 9-5:30; Sat 10-5</span>			</p>
						</div>
						<table class="yat">
			<thead>
			<tr>
				<th>Inv. # </th>
				<th>Tenant Name</th>
				<th class="tar">Rental<br />
				  Amt.</th>
				<th class="tar">Invoice Amt.</th>
				<th class="tar">Late Fee</th>
				<th class="tar">Total<br />Due</th>
				<th class="tac">Move-in Date</th>
				<th class="tac">Unit #</th>
				<th>Invoice<br />
			    due by: </th>
				<th>Status</th>
								<th>p/d</th>
												<th>Agent Name</th>
											</tr>
			</thead>
			<tbody>
			<tr >
			<td><a title="View or edit this invoice" href="leases.php?Leases_ID=17449" onClick="return ow(this.href,'l1_leases','700,700');">15836</a></td>
			<td>Ben Hoffman</td>
			<td class="tar">655.00</td>
			<td class="tar">425.00</td>
			<td class="tar"><span class="red">10.00</span></td>
			<td class="tar">435.00</td>
			<td class="tac">8/15/2011</td>
			<td class="tac"></td>
			<td>9/14/2011</td>
			<td>PASTD</td>

						<td nowrap="nowrap">over 90</td>
									<td>Sparks, Jason</td>
								</tr>
		<tr >
			<td><a title="View or edit this invoice" href="leases.php?Leases_ID=18651" onClick="return ow(this.href,'l1_leases','700,700');">17034</a></td>
			<td>Emile Maroha</td>
			<td class="tar">675.00</td>
			<td class="tar">400.00</td>
			<td class="tar"><span class="red">10.00</span></td>
			<td class="tar">410.00</td>
			<td class="tac">3/28/2012</td>
			<td class="tac"></td>
			<td>4/29/2012</td>
			<td>PASTD</td>

						<td nowrap="nowrap">40</td>
									<td>Clark, Ricky</td>
								</tr>
		<tr >
			<td><a title="View or edit this invoice" href="leases.php?Leases_ID=18680" onClick="return ow(this.href,'l1_leases','700,700');">17063</a></td>
			<td>Anthony Shepherd</td>
			<td class="tar">680.00</td>
			<td class="tar">400.00</td>
			<td class="tar">&nbsp;</td>
			<td class="tar">400.00</td>
			<td class="tac">5/1/2012</td>
			<td class="tac"></td>
			<td>5/31/2012</td>
			<td>DUE</td>

						<td nowrap="nowrap">&nbsp;</td>
									<td>Jenkins, Tyler</td>
								</tr>
						<tr class="topborder nobo bottom">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td class="tar"><h3 class="nullBottom">$1,225.00</h3></td>
				<td class="tar"><h3 class="nullBottom">$20.00</h3></td>
				<td class="tar"><h3 class="nullBottom">$1,245.00</h3></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				</tr>
				</tbody></table>
							<div class="header">
							<div class="fr">
				<label><input type="checkbox" name="mailing[100]" value="1" /> Select for mailing</label>
				</div>
							<h2><a href="properties2.php?Properties_ID=100" title="View/edit this property full record" onclick="return ow(this.href,'l1_properties','750,700');">Blanco River Duplexes</a></h2>
			<p>
			River Road<br />San Marcos, TX  78666<br />(512) 327-5128(p)<br />(512) 692-9467(f)<br />			</p>
						</div>
						<table class="yat">
			<thead>
			<tr>
				<th>Inv. # </th>
				<th>Tenant Name</th>
				<th class="tar">Rental<br />
				  Amt.</th>
				<th class="tar">Invoice Amt.</th>
				<th class="tar">Late Fee</th>
				<th class="tar">Total<br />Due</th>
				<th class="tac">Move-in Date</th>
				<th class="tac">Unit #</th>
				<th>Invoice<br />
			    due by: </th>
				<th>Status</th>
								<th>p/d</th>
												<th>Agent Name</th>
											</tr>
			</thead>
			<tbody>
			<tr >
			<td><a title="View or edit this invoice" href="leases.php?Leases_ID=18883" onClick="return ow(this.href,'l1_leases','700,700');">17266</a></td>
			<td>Kelsey Morgan</td>
			<td class="tar">725.00</td>
			<td class="tar">362.50</td>
			<td class="tar">&nbsp;</td>
			<td class="tar">362.50</td>
			<td class="tac">5/2/2012</td>
			<td class="tac">1440</td>
			<td>6/2/2012</td>
			<td>DUE</td>

						<td nowrap="nowrap">&nbsp;</td>
									<td>McCoy, Steven</td>
								</tr>
						<tr class="topborder nobo bottom">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td class="tar"><h3 class="nullBottom">$362.50</h3></td>
				<td class="tar"><h3 class="nullBottom">$0.00</h3></td>
				<td class="tar"><h3 class="nullBottom">$362.50</h3></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				</tr>
				</tbody></table>
							<div class="header">
							<div class="fr">
				<label><input type="checkbox" name="mailing[126]" value="1" /> Select for mailing</label>
				</div>
							<h2><a href="properties2.php?Properties_ID=126" title="View/edit this property full record" onclick="return ow(this.href,'l1_properties','750,700');">Cabana Beach</a></h2>
			<p>
			1250 Sadler Drive<br />San Marcos, TX  78666<br />(512) 392-8115(p)<br />(512) 392-8116(f)<br /><span class="gray">M-F 10-6; Sat 12-5; Sun 1-4</span>			</p>
						</div>
						<table class="yat">
			<thead>
			<tr>
				<th>Inv. # </th>
				<th>Tenant Name</th>
				<th class="tar">Rental<br />
				  Amt.</th>
				<th class="tar">Invoice Amt.</th>
				<th class="tar">Late Fee</th>
				<th class="tar">Total<br />Due</th>
				<th class="tac">Move-in Date</th>
				<th class="tac">Unit #</th>
				<th>Invoice<br />
			    due by: </th>
				<th>Status</th>
								<th>p/d</th>
												<th>Agent Name</th>
											</tr>
			</thead>
			<tbody>
			<tr >
			<td><a title="View or edit this invoice" href="leases.php?Leases_ID=17716" onClick="return ow(this.href,'l1_leases','700,700');">16103</a></td>
			<td>Son Ae Gang</td>
			<td class="tar">695.00</td>
			<td class="tar">1,042.50</td>
			<td class="tar"><span class="red">10.00</span></td>
			<td class="tar">1,052.50</td>
			<td class="tac">8/15/2011</td>
			<td class="tac"></td>
			<td>9/14/2011</td>
			<td>PASTD</td>

						<td nowrap="nowrap">over 90</td>
									<td>Sparks, Jason</td>
								</tr>
		<tr >
			<td><a title="View or edit this invoice" href="leases.php?Leases_ID=18438" onClick="return ow(this.href,'l1_leases','700,700');">16825</a></td>
			<td>Tiffany Boudreaux</td>
			<td class="tar">487.00</td>
			<td class="tar">487.00</td>
			<td class="tar">&nbsp;</td>
			<td class="tar">487.00</td>
			<td class="tac">5/1/2012</td>
			<td class="tac"></td>
			<td>5/31/2012</td>
			<td>DUE</td>

						<td nowrap="nowrap">&nbsp;</td>
									<td>Sparks, Jason</td>
								</tr>
						<tr class="topborder nobo bottom">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td class="tar"><h3 class="nullBottom">$1,529.50</h3></td>
				<td class="tar"><h3 class="nullBottom">$10.00</h3></td>
				<td class="tar"><h3 class="nullBottom">$1,539.50</h3></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				</tr>
				</tbody></table>
							<div class="header">
							<div class="fr">
				<label><input type="checkbox" name="mailing[10]" value="1" /> Select for mailing</label>
				</div>
							<h2><a href="properties2.php?Properties_ID=10" title="View/edit this property full record" onclick="return ow(this.href,'l1_properties','750,700');">Cedars</a></h2>
			<p>
			1101 Leah Ave<br />San Marcos, TX  78666<br />(512) 396-8886(p)<br />(512) 396-8996(f)<br /><span class="gray">M-F 8:00-5:00</span>			</p>
						</div>
						<table class="yat">
			<thead>
			<tr>
				<th>Inv. # </th>
				<th>Tenant Name</th>
				<th class="tar">Rental<br />
				  Amt.</th>
				<th class="tar">Invoice Amt.</th>
				<th class="tar">Late Fee</th>
				<th class="tar">Total<br />Due</th>
				<th class="tac">Move-in Date</th>
				<th class="tac">Unit #</th>
				<th>Invoice<br />
			    due by: </th>
				<th>Status</th>
								<th>p/d</th>
												<th>Agent Name</th>
											</tr>
			</thead>
			<tbody>
			<tr >
			<td><a title="View or edit this invoice" href="leases.php?Leases_ID=18370" onClick="return ow(this.href,'l1_leases','700,700');">16757</a></td>
			<td>Anthony Ahrens</td>
			<td class="tar">790.00</td>
			<td class="tar">790.00</td>
			<td class="tar"><span class="red">10.00</span></td>
			<td class="tar">800.00</td>
			<td class="tac">1/4/2012</td>
			<td class="tac"></td>
			<td>2/29/2012</td>
			<td>PASTD</td>

						<td nowrap="nowrap">over 90</td>
									<td>Sparks, Jason</td>
								</tr>
		<tr >
			<td><a title="View or edit this invoice" href="leases.php?Leases_ID=18476" onClick="return ow(this.href,'l1_leases','700,700');">16863</a></td>
			<td>Ryan Rena</td>
			<td class="tar">790.00</td>
			<td class="tar">395.00</td>
			<td class="tar"><span class="red">10.00</span></td>
			<td class="tar">405.00</td>
			<td class="tac">12/1/2011</td>
			<td class="tac">205</td>
			<td>3/9/2012</td>
			<td>PASTD</td>

						<td nowrap="nowrap">over 90</td>
									<td>Jenkins, Tyler</td>
								</tr>
						<tr class="topborder nobo bottom">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td class="tar"><h3 class="nullBottom">$1,185.00</h3></td>
				<td class="tar"><h3 class="nullBottom">$20.00</h3></td>
				<td class="tar"><h3 class="nullBottom">$1,205.00</h3></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				</tr>
				</tbody></table>
							<div class="header">
							<div class="fr">
				<label><input type="checkbox" name="mailing[57]" value="1" /> Select for mailing</label>
				</div>
							<h2><a href="properties2.php?Properties_ID=57" title="View/edit this property full record" onclick="return ow(this.href,'l1_properties','750,700');">Hillside Ranch</a></h2>
			<p>
			1350 N LBJ Dr.<br />San Marcos, TX  78666<br />(512) 393-3222(p)<br />(512) 392-5564(f)<br /><span class="gray">M-Th 9-7;Fri 9-6; Sat 10-5; Sun 1-5</span>			</p>
						</div>
						<table class="yat">
			<thead>
			<tr>
				<th>Inv. # </th>
				<th>Tenant Name</th>
				<th class="tar">Rental<br />
				  Amt.</th>
				<th class="tar">Invoice Amt.</th>
				<th class="tar">Late Fee</th>
				<th class="tar">Total<br />Due</th>
				<th class="tac">Move-in Date</th>
				<th class="tac">Unit #</th>
				<th>Invoice<br />
			    due by: </th>
				<th>Status</th>
								<th>p/d</th>
												<th>Agent Name</th>
											</tr>
			</thead>
			<tbody>
			<tr >
			<td><a title="View or edit this invoice" href="leases.php?Leases_ID=18763" onClick="return ow(this.href,'l1_leases','700,700');">17146</a></td>
			<td>Kyle Graef</td>
			<td class="tar">580.00</td>
			<td class="tar">580.00</td>
			<td class="tar">&nbsp;</td>
			<td class="tar">580.00</td>
			<td class="tac">5/7/2012</td>
			<td class="tac">717B</td>
			<td>6/6/2012</td>
			<td>DUE</td>

						<td nowrap="nowrap">&nbsp;</td>
									<td>Turcotte, Jordan</td>
								</tr>
		<tr >
			<td><a title="View or edit this invoice" href="leases.php?Leases_ID=18764" onClick="return ow(this.href,'l1_leases','700,700');">17147</a></td>
			<td>Kevin Tschauner</td>
			<td class="tar">580.00</td>
			<td class="tar">580.00</td>
			<td class="tar">&nbsp;</td>
			<td class="tar">580.00</td>
			<td class="tac">5/7/2012</td>
			<td class="tac">717A</td>
			<td>6/6/2012</td>
			<td>DUE</td>

						<td nowrap="nowrap">&nbsp;</td>
									<td>Turcotte, Jordan</td>
								</tr>
			<tr class="topborder nobo bottom">
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td class="tar"><h3 class="nullBottom">$1,160.00</h3></td>
	<td class="tar"><h3 class="nullBottom">$0.00</h3></td>
	<td class="tar"><h3 class="nullBottom">$1,160.00</h3></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
		</tr>
		</tbody></table>
		<table class="yat2">
	  <tr>
		<td><h2>Invoices Total:</h2></td>
		<td class="tr"><h2>$5,462.00</h2></td>
	  </tr>
	  <tr>
		<td><h2>Late Fees:</h2></td>
		<td class="tr"><h2>$50.00</h2></td>
	  </tr>
	  <tr>
		<td><h2>Grand Total:</h2> </td>
		<td class="tr"><h2>$5,512.00</h2></td>
	  </tr>
	</table>
      	<input type="submit" name="Submit" value="Send Statements/Lists" onclick="return setForm(1);" />
	<input name="mode" type="hidden" id="mode" />
	</div>

</div>
<div id="footer">
&nbsp;
</div>
</form>
<?php if(!$hideCtrlSection){ ?>
<div id="showTester" title="Javascript Tester" onclick="g('tester').style.display='block';">&nbsp;</div>
<div id="tester" >
	<a href="#" onclick="g('ctrlSection').style.display='block';return false;">Show Control Section</a><br />
	<textarea name="test" cols="65" rows="4" id="test">clear_form();</textarea><br />
	<input type="button" name="button" value="Test" onclick="jsEval(g('test').value);"><br />
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