<?php

$file = explode('/',trim($_SERVER['DOCUMENT_ROOT'],'/'));
$null = array_pop($file);
require_once('/'.implode('/',$file).'/private/config.php');
$conn = mysqli_connect($MASTER_HOSTNAME, $MASTER_USERNAME, $MASTER_PASSWORD) or die(mysqli_error($conn));
mysqli_select_db($conn, $MASTER_DATABASE);

if(true || (isset($_GET['getCountriesByLetters']) && isset($_GET['letters']))){
	$letters = $_GET['letters'];
	$letters = preg_replace("/[^a-z0-9 ]/si","",$letters);
	$res = mysql_query("select ID,countryName from ajax_countries where countryName like '".$letters."%'") or die(mysqli_error());
	#echo "1###select ID,countryName from ajax_countries where countryName like '".$letters."%'|";
	while($inf = mysqli_fetch_array($res)){
		echo $inf["ID"]."###".$inf["countryName"]."|";
	}	
}

