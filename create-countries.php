<?

$conn = mysqli_connect("localhost","username","password");
mysqli_select_db($conn, "dbName");

$string = "Afghanistan,
Albania,
Algeria,
Andorra,
Angola,
Antigua and Barbuda,
Argentina,
Armenia,
Australia,
Austria,
Azerbaijan,
Bahamas,
Bahrain,
Bangladesh,
Barbados,
Belarus,
Belgium,
Belize,
Benin,
Bhutan,
Bolivia,
Bosnia and Herzegovina,
Botswana,
Brazil,
Brunei,
Bulgaria,
Burkina Faso,
Burundi,
Cambodia,
Cameroon,
Canada,
Cape Verde,
Central African Republic,
Chad,
Chile,
China,
Colombia,
Comoros,
Congo,
Costa Rica,
Cote d'Ivoire,
Croatia,
Cuba,
Cyprus,
Czech Republic,
Denmark,
Djibouti,
Dominica,
Dominican Republic,
East Timor (Timor Timur),
Ecuador,
Egypt,
El Salvador,
Equatorial Guinea,
Eritrea,
Estonia,
Ethiopia,
Fiji,
Finland,
France,
Gabon,
Gambia, The,
Georgia,
Germany,
Ghana,
Greece,
Grenada,
Guatemala,
Guinea,
Guinea-Bissau,
Guyana,
Haiti,
Honduras,
Hungary,
Iceland,
India,
Indonesia,
Iran,
Iraq,
Ireland,
Israel,
Italy,
Jamaica,
Japan,
Jordan,
Kazakhstan,
Kenya,
Kiribati,
Korea, North,
Korea, South,
Kuwait,
Kyrgyzstan,
Laos,
Latvia,
Lebanon,
Lesotho,
Liberia,
Libya,
Liechtenstein,
Lithuania,
Luxembourg,
Macedonia,
Madagascar,
Malawi,
Malaysia,
Maldives,
Mali,
Malta,
Marshall Islands,
Mauritania,
Mauritius,
Mexico,
Micronesia,
Moldova,
Monaco,
Mongolia,
Morocco,
Mozambique,
Myanmar,
Namibia,
Nauru,
Nepal,
Netherlands,
New Zealand,
Nicaragua,
Niger,
Nigeria,
Norway,
Oman,
Pakistan,
Palau,
Panama,
Papua New Guinea,
Paraguay,
Peru,
Philippines,
Poland,
Portugal,
Qatar,
Romania,
Russia,
Rwanda,
Saint Kitts and Nevis,
Saint Lucia,
Saint Vincent and The Grenadines,
Samoa,
San Marino,
Sao Tome and Principe,
Saudi Arabia,
Senegal,
Serbia and Montenegro,
Seychelles,
Sierra Leone,
Singapore,
Slovakia,
Slovenia,
Solomon Islands,
Somalia,
South Africa,
Spain,
Sri Lanka,
Sudan,
Suriname,
Swaziland,
Sweden,
Switzerland,
Syria,
Taiwan,
Tajikistan,
Tanzania,
Thailand,
Togo,
Tonga,
Trinidad and Tobago,
Tunisia,
Turkey,
Turkmenistan,
Tuvalu,
Uganda,
Ukraine,
United Arab Emirates,
United Kingdom,
United States,
Uruguay,
Uzbekistan,
Vanuatu,
Vatican City,
Venezuela,
Vietnam,
Western Sahara,
Yemen,
Zambia,
Zimbabwe";



mysql_query("delete from ajax_countries");	// Just in case this script is executed a second time.
mysql_query("alter table ajax_countries add index(countryName(2))") or die(mysqli_error());
$countries = explode(",",$string);
for($no=0;$no<count($countries);$no++){
	$countries[$no] = str_replace("'","&#039;",$countries[$no]);
	$countries[$no] = trim($countries[$no]);
	mysql_query("insert into ajax_countries(countryName)values('".$countries[$no]."')") or die(mysqli_error());
	
}

?>