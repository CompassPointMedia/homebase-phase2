<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>Export Manager - Maps of the Past LLC</title>



<link id="cssUndoHTML" rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="../../site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="../../Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
</style>

<script language="javascript" type="text/javascript" src="/Library/js/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="../../site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle coding */
var thispage='exportmanager.php';
var thisfolder='console';
var browser='Moz';
var ctime='1346874962';
var PHPSESSID='2eifgkui9l4drequmradkn8q47';
//for nav feature
var count='';
var ab='';

var isEscapable=1;

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already
</script>


</head>

<body id="report">
<div id="header">

&nbsp;

</div>
<div id="mainBody">

<style type="text/css">
	.lateNight{
		border-collapse:collapse;
		}
	.lateNight td{
		border:1px solid #666;
		padding:2px 10px;
		}	
	.scroll{
		border:2px solid #99BD0C;
		overflow:scroll;
		width:80%;
		height:300px;
		}
	.scroll:focus{
		border:2px solid burlywood;
		}
	.unused{
		background-color:#eee;
		color:#888;
		}
	.unused input{
		color:#666;
		}
	td.leftNav{
		padding:0px;
		width:140px;
		}
	.leftNav ul{
		list-style:none;
		padding:0px;
		margin:0px;
		width:100%;
		}
	.leftNav li{
		border-bottom:1px dotted #666;
		padding:2px 7px;
		}
	#fieldGrid thead tr{
		border:1px solid Gold;
		border-bottom:none;
		}
	#fieldGrid th{
		background-color:LightSteelBlue;
		vertical-align:bottom;
		padding:2px 10px;
		color:white;
		}
	#RecordBatchWrap{
		float:right;
		margin-top:7px;
		}
	</style>
	<script language="javascript" type="text/javascript">
	function toggleNewProfile(o){
		window.location='exportmanager.php?view=profiles&Tables_ID='+o.value;
	}
	function toggleProfiles(n,t){
		window.location='exportmanager.php?view=profiles&Profiles_ID='+n+'&Tables_ID='+t;
	}
	function toggleRow(o){
		g('r_'+o.id.replace(/[^0-9]+/g,'')).className=(o.checked?'':'unused');
	}
	function regTable(o){
		if(!o.value)return
		return ow('exportmanager.php?view=tables&subview=managetable&table='+o.value,'l2_regtable','600,700');
	}
	</script><table width="100%" cellpadding="0" class="lateNight">
  <tr>
    <td colspan="100%" class="tar"><strong>Welcome sam; you are a DB Administrator</strong></td>
  </tr>
  <tr>
    <td class="leftNav">
	<ul>
		<li><a href="/gf5/console/exportmanager.php?view=tables">Tables</a></li>
		<li><a href="/gf5/console/exportmanager.php?view=profiles">Profiles</a></li>
		<li>--</li>
		<li><a href="http://en.wikipedia.org/wiki/Web_Colors#X11_color_names" title="View web colors" onclick="return ow(this.href,'l1_colors','750,750');">Colors</a></li>
	</ul>
	</td>
    <td>
	<form name="form1" id="form1" target="w2" method="post">	<div class="fr">
	<input type="submit" name="Submit" value="Save Profile Changes" onclick="g('submode').value='';g('suppressPrintEnv').value=0;" />&nbsp;
    <input type="submit" name="Submit" value="Export Now" onclick="g('submode').value='exportprofile';g('suppressPrintEnv').value=1;" /> 
	<br />
	<label id="RecordBatchWrap" onclick=""><input name="RecordBatch" type="checkbox" id="RecordBatch" value="1" checked="checked" onchange="dChge(this);"  /> Record this export in export history for this profile </label>

	</div>
	<h2><a href="exportmanager.php?view=profiles" title="Main profile list">Profiles</a> : Edit and Export</h2>
	<h3>Primary table: <strong>maps</strong> <span class="gray">(finan_items)</span></h3>
	Available profiles: 
	<select name="_profiles_" id="_profiles" onchange="toggleProfiles(this.value,1);">
		<option value="">&lt;Create a new profile..&gt;</option>
	<option value="2" selected>Items:Kangaroo - First 1755</option>	</select>
	<br />
	<br />
	<p>Profile name: 
	  <input name="Name" type="text" id="Name" onchange="dChge(this);" value="Items:Kangaroo - First 1755" size="60" />
	  <br />
	Description:<br />
	<textarea name="Description" id="Description" rows="2" cols="65" onchange="dChge(this);">1st test of profile</textarea>
	<br />


		<style type="text/css">
	#tabWrap{
		position:relative;
		margin-top:35px;
		}
	#tabWrap a:hover{
		text-decoration:none;
		}
	.tabon, .taboff{
		float:left;
		margin-right:5px;
		background-color:#fff;
		border-left:1px solid #444;
		border-right:1px solid #444;
		border-top:1px solid #444;
		-moz-border-radius: 4px 4px 0px 0px;
		border-radius: 4px 4px 0px 0px;
		cursor:pointer;
		}
	.tabon{
		padding:3px 5px 8px 5px;
		margin-top:5px;
		border-bottom:1px solid white;
		}
	.taboff{
		padding:3px 5px;
		margin-top:10px;
		}
	.lowerline{
		border-top:1px solid #444;
		clear:both;
		margin-top:-1px;
		background-color:#99CCFF;
		}
	.tabRaise{
		position:absolute;
		top:-33px;
		left:15px;
		}
	.tabSectionStyleIII{
		padding:15px;
		border-left:1px solid #000;
		border-right:1px solid #000;
		border-bottom:1px solid #000;
		margin-bottom:10px;
		min-height:250px;
		}
	</style>
	<script language="javascript" type="text/javascript">
	tabGroup='default';
	var tabSections={'section_fields':'section_fields', 'section_criteria':'section_criteria', 'section_format':'section_format', 'section_export':'section_export', 'section_schedule':'section_schedule', 'section_logs':'section_logs', 'section_help':'section_help'};
	function tabon(o){
		if(o.className=='tabon')return false;
		for(var i in tabSections){
			g('tab_'+tabSections[i]).className='taboff';
			g(tabSections[i]).style.display='none';
		}
		sCookie('tenhanced_'+tabGroup,o.id.replace('tab_',''));
		o.className='tabon';
		g(o.id.replace('tab_','')).style.display='block';
	}
	</script>
	<div id="tabWrap">
		<div class="lowerline"> </div>
		<div class="tabRaise">
							<div id="tab_section_fields" class="taboff"><a href="#" onclick="return tabon(this.parentNode);">Fields</a></div>
								<div id="tab_section_criteria" class="taboff"><a href="#" onclick="return tabon(this.parentNode);">Criteria</a></div>
								<div id="tab_section_format" class="taboff"><a href="#" onclick="return tabon(this.parentNode);">Format</a></div>
								<div id="tab_section_export" class="taboff"><a href="#" onclick="return tabon(this.parentNode);">Export</a></div>
								<div id="tab_section_schedule" class="taboff"><a href="#" onclick="return tabon(this.parentNode);">Schedule</a></div>
								<div id="tab_section_logs" class="tabon"><a href="#" onclick="return tabon(this.parentNode);">Logs</a></div>
								<div id="tab_section_help" class="taboff"><a href="#" onclick="return tabon(this.parentNode);">Help</a></div>
						</div>
	</div>
	
<div id="section_fields" class="tabSectionStyleIII" style="display:none;">
	<table id="fieldGrid" class="yat">
	<thead>
      <tr>
        <th><h2 class="nullBottom">Fields</h2></th>
        <th valign="bottom">type</th>
        <th valign="bottom" class="tac">u</th>
        <th valign="bottom">label</th>
        <th valign="bottom">value</th>
        <th valign="bottom">format</th>
      </tr>
	</thead>
	<tr id="r_1" class="unused">
	  <td><label title="ID">
		<input type="checkbox" name="data[use][id]" id="use1" value="1"  onchange="dChge(this);toggleRow(this)" />          
		ID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">y</div></td>
		<td><input name="data[label][id]" id="label1" type="text" value="ID" onchange="dChge(this);" /></td>
		<td><input name="data[value][id]" id="value1" type="text" value="%ID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_2" class="">
	  <td><label title="CreateDate">
		<input type="checkbox" name="data[use][createdate]" id="use2" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		CreateDate		</label>
	  </td>
		<td>Date/time</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][createdate]" id="label2" type="text" value="CreateDate" onchange="dChge(this);" /></td>
		<td><input name="data[value][createdate]" id="value2" type="text" value="%CreateDate%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_3" class="">
	  <td><label title="Creator">
		<input type="checkbox" name="data[use][creator]" id="use3" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Creator		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][creator]" id="label3" type="text" value="Creator" onchange="dChge(this);" /></td>
		<td><input name="data[value][creator]" id="value3" type="text" value="%Creator%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_4" class="">
	  <td><label title="EditDate">
		<input type="checkbox" name="data[use][editdate]" id="use4" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		EditDate		</label>
	  </td>
		<td>Date/time</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][editdate]" id="label4" type="text" value="EditDate" onchange="dChge(this);" /></td>
		<td><input name="data[value][editdate]" id="value4" type="text" value="%EditDate%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_5" class="unused">
	  <td><label title="realtime">
		<input type="checkbox" name="data[use][realtime]" id="use5" value="1"  onchange="dChge(this);toggleRow(this)" />          
		realtime		</label>
	  </td>
		<td>Date/time</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][realtime]" id="label5" type="text" value="realtime" onchange="dChge(this);" /></td>
		<td><input name="data[value][realtime]" id="value5" type="text" value="%realtime%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_6" class="">
	  <td><label title="EditDateBuffer">
		<input type="checkbox" name="data[use][editdatebuffer]" id="use6" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		EditDateBuffer		</label>
	  </td>
		<td>Date/time</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][editdatebuffer]" id="label6" type="text" value="EditDateBuffer" onchange="dChge(this);" /></td>
		<td><input name="data[value][editdatebuffer]" id="value6" type="text" value="%EditDateBuffer%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_7" class="">
	  <td><label title="Editor">
		<input type="checkbox" name="data[use][editor]" id="use7" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Editor		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][editor]" id="label7" type="text" value="Editor" onchange="dChge(this);" /></td>
		<td><input name="data[value][editor]" id="value7" type="text" value="%Editor%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_8" class="">
	  <td><label title="IsLocked">
		<input type="checkbox" name="data[use][islocked]" id="use8" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		IsLocked		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][islocked]" id="label8" type="text" value="IsLocked" onchange="dChge(this);" /></td>
		<td><input name="data[value][islocked]" id="value8" type="text" value="%IsLocked%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_9" class="">
	  <td><label title="IgnoreFile">
		<input type="checkbox" name="data[use][ignorefile]" id="use9" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		IgnoreFile		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][ignorefile]" id="label9" type="text" value="IgnoreFile" onchange="dChge(this);" /></td>
		<td><input name="data[value][ignorefile]" id="value9" type="text" value="%IgnoreFile%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_10" class="">
	  <td><label title="Active">
		<input type="checkbox" name="data[use][active]" id="use10" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Active		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][active]" id="label10" type="text" value="Active" onchange="dChge(this);" /></td>
		<td><input name="data[value][active]" id="value10" type="text" value="%Active%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_11" class="">
	  <td><label title="OutOfStock">
		<input type="checkbox" name="data[use][outofstock]" id="use11" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		OutOfStock		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][outofstock]" id="label11" type="text" value="OutOfStock" onchange="dChge(this);" /></td>
		<td><input name="data[value][outofstock]" id="value11" type="text" value="%OutOfStock%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_12" class="">
	  <td><label title="ToBeExported">
		<input type="checkbox" name="data[use][tobeexported]" id="use12" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		ToBeExported		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][tobeexported]" id="label12" type="text" value="ToBeExported" onchange="dChge(this);" /></td>
		<td><input name="data[value][tobeexported]" id="value12" type="text" value="%ToBeExported%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_13" class="">
	  <td><label title="SHOPSITE_ToBeExported">
		<input type="checkbox" name="data[use][shopsite_tobeexported]" id="use13" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_ToBeExported		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_tobeexported]" id="label13" type="text" value="SHOPSITE_ToBeExported" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_tobeexported]" id="value13" type="text" value="%SHOPSITE_ToBeExported%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_14" class="">
	  <td><label title="MIVA_ToBeExported">
		<input type="checkbox" name="data[use][miva_tobeexported]" id="use14" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		MIVA_ToBeExported		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][miva_tobeexported]" id="label14" type="text" value="MIVA_ToBeExported" onchange="dChge(this);" /></td>
		<td><input name="data[value][miva_tobeexported]" id="value14" type="text" value="%MIVA_ToBeExported%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_15" class="">
	  <td><label title="AMAZON_ToBeExported">
		<input type="checkbox" name="data[use][amazon_tobeexported]" id="use15" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		AMAZON_ToBeExported		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][amazon_tobeexported]" id="label15" type="text" value="AMAZON_ToBeExported" onchange="dChge(this);" /></td>
		<td><input name="data[value][amazon_tobeexported]" id="value15" type="text" value="%AMAZON_ToBeExported%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_16" class="">
	  <td><label title="EBAY_ToBeExported">
		<input type="checkbox" name="data[use][ebay_tobeexported]" id="use16" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		EBAY_ToBeExported		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][ebay_tobeexported]" id="label16" type="text" value="EBAY_ToBeExported" onchange="dChge(this);" /></td>
		<td><input name="data[value][ebay_tobeexported]" id="value16" type="text" value="%EBAY_ToBeExported%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_17" class="">
	  <td><label title="ExportDate">
		<input type="checkbox" name="data[use][exportdate]" id="use17" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		ExportDate		</label>
	  </td>
		<td>Date/time</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][exportdate]" id="label17" type="text" value="ExportDate" onchange="dChge(this);" /></td>
		<td><input name="data[value][exportdate]" id="value17" type="text" value="%ExportDate%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_18" class="">
	  <td><label title="Exporter">
		<input type="checkbox" name="data[use][exporter]" id="use18" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Exporter		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][exporter]" id="label18" type="text" value="Exporter" onchange="dChge(this);" /></td>
		<td><input name="data[value][exporter]" id="value18" type="text" value="%Exporter%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_19" class="">
	  <td><label title="InStock">
		<input type="checkbox" name="data[use][instock]" id="use19" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		InStock		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][instock]" id="label19" type="text" value="InStock" onchange="dChge(this);" /></td>
		<td><input name="data[value][instock]" id="value19" type="text" value="%InStock%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_20" class="">
	  <td><label title="ReorderPt">
		<input type="checkbox" name="data[use][reorderpt]" id="use20" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		ReorderPt		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][reorderpt]" id="label20" type="text" value="ReorderPt" onchange="dChge(this);" /></td>
		<td><input name="data[value][reorderpt]" id="value20" type="text" value="%ReorderPt%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_21" class="">
	  <td><label title="Ourgroup_ID">
		<input type="checkbox" name="data[use][ourgroup_id]" id="use21" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Ourgroup_ID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][ourgroup_id]" id="label21" type="text" value="Ourgroup_ID" onchange="dChge(this);" /></td>
		<td><input name="data[value][ourgroup_id]" id="value21" type="text" value="%Ourgroup_ID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_22" class="">
	  <td><label title="Schemas_ID">
		<input type="checkbox" name="data[use][schemas_id]" id="use22" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Schemas_ID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][schemas_id]" id="label22" type="text" value="Schemas_ID" onchange="dChge(this);" /></td>
		<td><input name="data[value][schemas_id]" id="value22" type="text" value="%Schemas_ID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_23" class="">
	  <td><label title="GroupLeader">
		<input type="checkbox" name="data[use][groupleader]" id="use23" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		GroupLeader		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][groupleader]" id="label23" type="text" value="GroupLeader" onchange="dChge(this);" /></td>
		<td><input name="data[value][groupleader]" id="value23" type="text" value="%GroupLeader%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_24" class="">
	  <td><label title="SuperSKU">
		<input type="checkbox" name="data[use][supersku]" id="use24" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		SuperSKU		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][supersku]" id="label24" type="text" value="SuperSKU" onchange="dChge(this);" /></td>
		<td><input name="data[value][supersku]" id="value24" type="text" value="%SuperSKU%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_25" class="">
	  <td><label title="Model">
		<input type="checkbox" name="data[use][model]" id="use25" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Model		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][model]" id="label25" type="text" value="Model" onchange="dChge(this);" /></td>
		<td><input name="data[value][model]" id="value25" type="text" value="%Model%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_26" class="">
	  <td><label title="SKU">
		<input type="checkbox" name="data[use][sku]" id="use26" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		SKU		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">y</div></td>
		<td><input name="data[label][sku]" id="label26" type="text" value="SKU" onchange="dChge(this);" /></td>
		<td><input name="data[value][sku]" id="value26" type="text" value="%SKU%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_27" class="">
	  <td><label title="ManufacturerSKU">
		<input type="checkbox" name="data[use][manufacturersku]" id="use27" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		ManufacturerSKU		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][manufacturersku]" id="label27" type="text" value="ManufacturerSKU" onchange="dChge(this);" /></td>
		<td><input name="data[value][manufacturersku]" id="value27" type="text" value="%ManufacturerSKU%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_28" class="">
	  <td><label title="UPC">
		<input type="checkbox" name="data[use][upc]" id="use28" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		UPC		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][upc]" id="label28" type="text" value="UPC" onchange="dChge(this);" /></td>
		<td><input name="data[value][upc]" id="value28" type="text" value="%UPC%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_29" class="unused">
	  <td><label title="HMR_UPCCheckDigit">
		<input type="checkbox" name="data[use][hmr_upccheckdigit]" id="use29" value="1"  onchange="dChge(this);toggleRow(this)" />          
		HMR_UPCCheckDigit		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][hmr_upccheckdigit]" id="label29" type="text" value="HMR_UPCCheckDigit" onchange="dChge(this);" /></td>
		<td><input name="data[value][hmr_upccheckdigit]" id="value29" type="text" value="%HMR_UPCCheckDigit%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_30" class="">
	  <td><label title="RWB">
		<input type="checkbox" name="data[use][rwb]" id="use30" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		RWB		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][rwb]" id="label30" type="text" value="RWB" onchange="dChge(this);" /></td>
		<td><input name="data[value][rwb]" id="value30" type="text" value="%RWB%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_31" class="">
	  <td><label title="Priority">
		<input type="checkbox" name="data[use][priority]" id="use31" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Priority		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][priority]" id="label31" type="text" value="Priority" onchange="dChge(this);" /></td>
		<td><input name="data[value][priority]" id="value31" type="text" value="%Priority%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_32" class="">
	  <td><label title="Category">
		<input type="checkbox" name="data[use][category]" id="use32" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Category		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][category]" id="label32" type="text" value="Category" onchange="dChge(this);" /></td>
		<td><input name="data[value][category]" id="value32" type="text" value="%Category%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_33" class="">
	  <td><label title="SubCategory">
		<input type="checkbox" name="data[use][subcategory]" id="use33" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		SubCategory		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][subcategory]" id="label33" type="text" value="SubCategory" onchange="dChge(this);" /></td>
		<td><input name="data[value][subcategory]" id="value33" type="text" value="%SubCategory%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_34" class="">
	  <td><label title="PrimaryRegion">
		<input type="checkbox" name="data[use][primaryregion]" id="use34" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		PrimaryRegion		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][primaryregion]" id="label34" type="text" value="PrimaryRegion" onchange="dChge(this);" /></td>
		<td><input name="data[value][primaryregion]" id="value34" type="text" value="%PrimaryRegion%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_35" class="">
	  <td><label title="New_SubCategory">
		<input type="checkbox" name="data[use][new_subcategory]" id="use35" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		New_SubCategory		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][new_subcategory]" id="label35" type="text" value="New_SubCategory" onchange="dChge(this);" /></td>
		<td><input name="data[value][new_subcategory]" id="value35" type="text" value="%New_SubCategory%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_36" class="">
	  <td><label title="del">
		<input type="checkbox" name="data[use][del]" id="use36" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		del		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][del]" id="label36" type="text" value="del" onchange="dChge(this);" /></td>
		<td><input name="data[value][del]" id="value36" type="text" value="%del%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_37" class="">
	  <td><label title="Name">
		<input type="checkbox" name="data[use][name]" id="use37" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Name		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][name]" id="label37" type="text" value="Name" onchange="dChge(this);" /></td>
		<td><input name="data[value][name]" id="value37" type="text" value="%Name%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_38" class="">
	  <td><label title="Caption">
		<input type="checkbox" name="data[use][caption]" id="use38" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Caption		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][caption]" id="label38" type="text" value="Caption" onchange="dChge(this);" /></td>
		<td><input name="data[value][caption]" id="value38" type="text" value="%Caption%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_39" class="">
	  <td><label title="Manufacturers_ID">
		<input type="checkbox" name="data[use][manufacturers_id]" id="use39" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Manufacturers_ID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][manufacturers_id]" id="label39" type="text" value="Manufacturers_ID" onchange="dChge(this);" /></td>
		<td><input name="data[value][manufacturers_id]" id="value39" type="text" value="%Manufacturers_ID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_40" class="">
	  <td><label title="Manufacturer">
		<input type="checkbox" name="data[use][manufacturer]" id="use40" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Manufacturer		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][manufacturer]" id="label40" type="text" value="Manufacturer" onchange="dChge(this);" /></td>
		<td><input name="data[value][manufacturer]" id="value40" type="text" value="%Manufacturer%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_41" class="">
	  <td><label title="Brand">
		<input type="checkbox" name="data[use][brand]" id="use41" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Brand		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][brand]" id="label41" type="text" value="Brand" onchange="dChge(this);" /></td>
		<td><input name="data[value][brand]" id="value41" type="text" value="%Brand%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_42" class="">
	  <td><label title="Vendors_ID">
		<input type="checkbox" name="data[use][vendors_id]" id="use42" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Vendors_ID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][vendors_id]" id="label42" type="text" value="Vendors_ID" onchange="dChge(this);" /></td>
		<td><input name="data[value][vendors_id]" id="value42" type="text" value="%Vendors_ID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_43" class="">
	  <td><label title="Vendor">
		<input type="checkbox" name="data[use][vendor]" id="use43" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Vendor		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][vendor]" id="label43" type="text" value="Vendor" onchange="dChge(this);" /></td>
		<td><input name="data[value][vendor]" id="value43" type="text" value="%Vendor%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_44" class="">
	  <td><label title="Description">
		<input type="checkbox" name="data[use][description]" id="use44" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Description		</label>
	  </td>
		<td>Long text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][description]" id="label44" type="text" value="Description" onchange="dChge(this);" /></td>
		<td><input name="data[value][description]" id="value44" type="text" value="%Description%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_45" class="">
	  <td><label title="LongDescription">
		<input type="checkbox" name="data[use][longdescription]" id="use45" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		LongDescription		</label>
	  </td>
		<td>Long text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][longdescription]" id="label45" type="text" value="LongDescription" onchange="dChge(this);" /></td>
		<td><input name="data[value][longdescription]" id="value45" type="text" value="%LongDescription%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_46" class="">
	  <td><label title="Function">
		<input type="checkbox" name="data[use][function]" id="use46" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Function		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][function]" id="label46" type="text" value="Function" onchange="dChge(this);" /></td>
		<td><input name="data[value][function]" id="value46" type="text" value="%Function%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_47" class="">
	  <td><label title="SubFunction">
		<input type="checkbox" name="data[use][subfunction]" id="use47" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		SubFunction		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][subfunction]" id="label47" type="text" value="SubFunction" onchange="dChge(this);" /></td>
		<td><input name="data[value][subfunction]" id="value47" type="text" value="%SubFunction%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_48" class="">
	  <td><label title="ItemFootnote">
		<input type="checkbox" name="data[use][itemfootnote]" id="use48" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		ItemFootnote		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][itemfootnote]" id="label48" type="text" value="ItemFootnote" onchange="dChge(this);" /></td>
		<td><input name="data[value][itemfootnote]" id="value48" type="text" value="%ItemFootnote%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_49" class="">
	  <td><label title="Notes">
		<input type="checkbox" name="data[use][notes]" id="use49" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Notes		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][notes]" id="label49" type="text" value="Notes" onchange="dChge(this);" /></td>
		<td><input name="data[value][notes]" id="value49" type="text" value="%Notes%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_50" class="">
	  <td><label title="PurchasePrice">
		<input type="checkbox" name="data[use][purchaseprice]" id="use50" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		PurchasePrice		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][purchaseprice]" id="label50" type="text" value="PurchasePrice" onchange="dChge(this);" /></td>
		<td><input name="data[value][purchaseprice]" id="value50" type="text" value="%PurchasePrice%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_51" class="">
	  <td><label title="WholesalePrice">
		<input type="checkbox" name="data[use][wholesaleprice]" id="use51" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		WholesalePrice		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][wholesaleprice]" id="label51" type="text" value="WholesalePrice" onchange="dChge(this);" /></td>
		<td><input name="data[value][wholesaleprice]" id="value51" type="text" value="%WholesalePrice%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_52" class="">
	  <td><label title="UnitPrice">
		<input type="checkbox" name="data[use][unitprice]" id="use52" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		UnitPrice		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][unitprice]" id="label52" type="text" value="UnitPrice" onchange="dChge(this);" /></td>
		<td><input name="data[value][unitprice]" id="value52" type="text" value="%UnitPrice%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_53" class="">
	  <td><label title="UnitPrice2">
		<input type="checkbox" name="data[use][unitprice2]" id="use53" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		UnitPrice2		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">y</div></td>
		<td><input name="data[label][unitprice2]" id="label53" type="text" value="UnitPrice2" onchange="dChge(this);" /></td>
		<td><input name="data[value][unitprice2]" id="value53" type="text" value="%UnitPrice2%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_54" class="">
	  <td><label title="Taxable">
		<input type="checkbox" name="data[use][taxable]" id="use54" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Taxable		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][taxable]" id="label54" type="text" value="Taxable" onchange="dChge(this);" /></td>
		<td><input name="data[value][taxable]" id="value54" type="text" value="%Taxable%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_55" class="">
	  <td><label title="Taxable2">
		<input type="checkbox" name="data[use][taxable2]" id="use55" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Taxable2		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">y</div></td>
		<td><input name="data[label][taxable2]" id="label55" type="text" value="Taxable2" onchange="dChge(this);" /></td>
		<td><input name="data[value][taxable2]" id="value55" type="text" value="%Taxable2%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_56" class="">
	  <td><label title="Special">
		<input type="checkbox" name="data[use][special]" id="use56" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Special		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][special]" id="label56" type="text" value="Special" onchange="dChge(this);" /></td>
		<td><input name="data[value][special]" id="value56" type="text" value="%Special%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_57" class="">
	  <td><label title="Featured">
		<input type="checkbox" name="data[use][featured]" id="use57" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Featured		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][featured]" id="label57" type="text" value="Featured" onchange="dChge(this);" /></td>
		<td><input name="data[value][featured]" id="value57" type="text" value="%Featured%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_58" class="">
	  <td><label title="ListPrice">
		<input type="checkbox" name="data[use][listprice]" id="use58" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		ListPrice		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][listprice]" id="label58" type="text" value="ListPrice" onchange="dChge(this);" /></td>
		<td><input name="data[value][listprice]" id="value58" type="text" value="%ListPrice%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_59" class="">
	  <td><label title="Length">
		<input type="checkbox" name="data[use][length]" id="use59" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Length		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][length]" id="label59" type="text" value="Length" onchange="dChge(this);" /></td>
		<td><input name="data[value][length]" id="value59" type="text" value="%Length%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_60" class="">
	  <td><label title="Width">
		<input type="checkbox" name="data[use][width]" id="use60" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Width		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][width]" id="label60" type="text" value="Width" onchange="dChge(this);" /></td>
		<td><input name="data[value][width]" id="value60" type="text" value="%Width%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_61" class="">
	  <td><label title="Depth">
		<input type="checkbox" name="data[use][depth]" id="use61" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Depth		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][depth]" id="label61" type="text" value="Depth" onchange="dChge(this);" /></td>
		<td><input name="data[value][depth]" id="value61" type="text" value="%Depth%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_62" class="">
	  <td><label title="Weight">
		<input type="checkbox" name="data[use][weight]" id="use62" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Weight		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][weight]" id="label62" type="text" value="Weight" onchange="dChge(this);" /></td>
		<td><input name="data[value][weight]" id="value62" type="text" value="%Weight%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_63" class="">
	  <td><label title="Shippers_ID">
		<input type="checkbox" name="data[use][shippers_id]" id="use63" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Shippers_ID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shippers_id]" id="label63" type="text" value="Shippers_ID" onchange="dChge(this);" /></td>
		<td><input name="data[value][shippers_id]" id="value63" type="text" value="%Shippers_ID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_64" class="">
	  <td><label title="Accounts_ID">
		<input type="checkbox" name="data[use][accounts_id]" id="use64" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Accounts_ID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][accounts_id]" id="label64" type="text" value="Accounts_ID" onchange="dChge(this);" /></td>
		<td><input name="data[value][accounts_id]" id="value64" type="text" value="%Accounts_ID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_65" class="">
	  <td><label title="AssetAccounts_ID">
		<input type="checkbox" name="data[use][assetaccounts_id]" id="use65" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		AssetAccounts_ID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][assetaccounts_id]" id="label65" type="text" value="AssetAccounts_ID" onchange="dChge(this);" /></td>
		<td><input name="data[value][assetaccounts_id]" id="value65" type="text" value="%AssetAccounts_ID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_66" class="">
	  <td><label title="COGSAccounts_ID">
		<input type="checkbox" name="data[use][cogsaccounts_id]" id="use66" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		COGSAccounts_ID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][cogsaccounts_id]" id="label66" type="text" value="COGSAccounts_ID" onchange="dChge(this);" /></td>
		<td><input name="data[value][cogsaccounts_id]" id="value66" type="text" value="%COGSAccounts_ID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_67" class="">
	  <td><label title="Classes_ID">
		<input type="checkbox" name="data[use][classes_id]" id="use67" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Classes_ID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][classes_id]" id="label67" type="text" value="Classes_ID" onchange="dChge(this);" /></td>
		<td><input name="data[value][classes_id]" id="value67" type="text" value="%Classes_ID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_68" class="">
	  <td><label title="Type">
		<input type="checkbox" name="data[use][type]" id="use68" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Type		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][type]" id="label68" type="text" value="Type" onchange="dChge(this);" /></td>
		<td><input name="data[value][type]" id="value68" type="text" value="%Type%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_69" class="">
	  <td><label title="IsPassedThrough">
		<input type="checkbox" name="data[use][ispassedthrough]" id="use69" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		IsPassedThrough		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][ispassedthrough]" id="label69" type="text" value="IsPassedThrough" onchange="dChge(this);" /></td>
		<td><input name="data[value][ispassedthrough]" id="value69" type="text" value="%IsPassedThrough%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_70" class="">
	  <td><label title="Items_ID">
		<input type="checkbox" name="data[use][items_id]" id="use70" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Items_ID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][items_id]" id="label70" type="text" value="Items_ID" onchange="dChge(this);" /></td>
		<td><input name="data[value][items_id]" id="value70" type="text" value="%Items_ID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_71" class="">
	  <td><label title="Keywords">
		<input type="checkbox" name="data[use][keywords]" id="use71" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Keywords		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][keywords]" id="label71" type="text" value="Keywords" onchange="dChge(this);" /></td>
		<td><input name="data[value][keywords]" id="value71" type="text" value="%Keywords%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_72" class="">
	  <td><label title="ExpirationDate">
		<input type="checkbox" name="data[use][expirationdate]" id="use72" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		ExpirationDate		</label>
	  </td>
		<td>Date</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][expirationdate]" id="label72" type="text" value="ExpirationDate" onchange="dChge(this);" /></td>
		<td><input name="data[value][expirationdate]" id="value72" type="text" value="%ExpirationDate%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_73" class="">
	  <td><label title="Edition">
		<input type="checkbox" name="data[use][edition]" id="use73" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Edition		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][edition]" id="label73" type="text" value="Edition" onchange="dChge(this);" /></td>
		<td><input name="data[value][edition]" id="value73" type="text" value="%Edition%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_74" class="">
	  <td><label title="Theme">
		<input type="checkbox" name="data[use][theme]" id="use74" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Theme		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][theme]" id="label74" type="text" value="Theme" onchange="dChge(this);" /></td>
		<td><input name="data[value][theme]" id="value74" type="text" value="%Theme%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_75" class="">
	  <td><label title="SubTheme">
		<input type="checkbox" name="data[use][subtheme]" id="use75" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		SubTheme		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][subtheme]" id="label75" type="text" value="SubTheme" onchange="dChge(this);" /></td>
		<td><input name="data[value][subtheme]" id="value75" type="text" value="%SubTheme%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_76" class="">
	  <td><label title="BreakPrice">
		<input type="checkbox" name="data[use][breakprice]" id="use76" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		BreakPrice		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][breakprice]" id="label76" type="text" value="BreakPrice" onchange="dChge(this);" /></td>
		<td><input name="data[value][breakprice]" id="value76" type="text" value="%BreakPrice%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_77" class="">
	  <td><label title="UM">
		<input type="checkbox" name="data[use][um]" id="use77" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		UM		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][um]" id="label77" type="text" value="UM" onchange="dChge(this);" /></td>
		<td><input name="data[value][um]" id="value77" type="text" value="%UM%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_78" class="">
	  <td><label title="PK">
		<input type="checkbox" name="data[use][pk]" id="use78" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		PK		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][pk]" id="label78" type="text" value="PK" onchange="dChge(this);" /></td>
		<td><input name="data[value][pk]" id="value78" type="text" value="%PK%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_79" class="">
	  <td><label title="ResourceType">
		<input type="checkbox" name="data[use][resourcetype]" id="use79" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		ResourceType		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][resourcetype]" id="label79" type="text" value="ResourceType" onchange="dChge(this);" /></td>
		<td><input name="data[value][resourcetype]" id="value79" type="text" value="%ResourceType%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_80" class="">
	  <td><label title="ResourceToken">
		<input type="checkbox" name="data[use][resourcetoken]" id="use80" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		ResourceToken		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">y</div></td>
		<td><input name="data[label][resourcetoken]" id="label80" type="text" value="ResourceToken" onchange="dChge(this);" /></td>
		<td><input name="data[value][resourcetoken]" id="value80" type="text" value="%ResourceToken%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_81" class="">
	  <td><label title="SessionKey">
		<input type="checkbox" name="data[use][sessionkey]" id="use81" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		SessionKey		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][sessionkey]" id="label81" type="text" value="SessionKey" onchange="dChge(this);" /></td>
		<td><input name="data[value][sessionkey]" id="value81" type="text" value="%SessionKey%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_82" class="">
	  <td><label title="Source">
		<input type="checkbox" name="data[use][source]" id="use82" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Source		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][source]" id="label82" type="text" value="Source" onchange="dChge(this);" /></td>
		<td><input name="data[value][source]" id="value82" type="text" value="%Source%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_83" class="">
	  <td><label title="RefNbr">
		<input type="checkbox" name="data[use][refnbr]" id="use83" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		RefNbr		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][refnbr]" id="label83" type="text" value="RefNbr" onchange="dChge(this);" /></td>
		<td><input name="data[value][refnbr]" id="value83" type="text" value="%RefNbr%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_84" class="">
	  <td><label title="VAT">
		<input type="checkbox" name="data[use][vat]" id="use84" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		VAT		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][vat]" id="label84" type="text" value="VAT" onchange="dChge(this);" /></td>
		<td><input name="data[value][vat]" id="value84" type="text" value="%VAT%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_85" class="unused">
	  <td><label title="SHOPSITE_ProductImageSize">
		<input type="checkbox" name="data[use][shopsite_productimagesize]" id="use85" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_ProductImageSize		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_productimagesize]" id="label85" type="text" value="SHOPSITE_ProductImageSize" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_productimagesize]" id="value85" type="text" value="%SHOPSITE_ProductImageSize%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_86" class="unused">
	  <td><label title="SHOPSITE_ProductOnPages">
		<input type="checkbox" name="data[use][shopsite_productonpages]" id="use86" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_ProductOnPages		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_productonpages]" id="label86" type="text" value="SHOPSITE_ProductOnPages" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_productonpages]" id="value86" type="text" value="%SHOPSITE_ProductOnPages%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_87" class="unused">
	  <td><label title="SHOPSITE_AddToPages">
		<input type="checkbox" name="data[use][shopsite_addtopages]" id="use87" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_AddToPages		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_addtopages]" id="label87" type="text" value="SHOPSITE_AddToPages" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_addtopages]" id="value87" type="text" value="%SHOPSITE_AddToPages%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_88" class="unused">
	  <td><label title="SHOPSITE_MoreInformationImageSize">
		<input type="checkbox" name="data[use][shopsite_moreinformationimagesize]" id="use88" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_MoreInformationImageSiz..		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_moreinformationimagesize]" id="label88" type="text" value="SHOPSITE_MoreInformationImageSize" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_moreinformationimagesize]" id="value88" type="text" value="%SHOPSITE_MoreInformationImageSize%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_89" class="">
	  <td><label title="MetaTitle">
		<input type="checkbox" name="data[use][metatitle]" id="use89" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		MetaTitle		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][metatitle]" id="label89" type="text" value="MetaTitle" onchange="dChge(this);" /></td>
		<td><input name="data[value][metatitle]" id="value89" type="text" value="%MetaTitle%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_90" class="">
	  <td><label title="MetaKeywords">
		<input type="checkbox" name="data[use][metakeywords]" id="use90" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		MetaKeywords		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][metakeywords]" id="label90" type="text" value="MetaKeywords" onchange="dChge(this);" /></td>
		<td><input name="data[value][metakeywords]" id="value90" type="text" value="%MetaKeywords%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_91" class="">
	  <td><label title="MetaDescription">
		<input type="checkbox" name="data[use][metadescription]" id="use91" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		MetaDescription		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][metadescription]" id="label91" type="text" value="MetaDescription" onchange="dChge(this);" /></td>
		<td><input name="data[value][metadescription]" id="value91" type="text" value="%MetaDescription%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_92" class="">
	  <td><label title="SEO_ArchiveFilePath">
		<input type="checkbox" name="data[use][seo_archivefilepath]" id="use92" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		SEO_ArchiveFilePath		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][seo_archivefilepath]" id="label92" type="text" value="SEO_ArchiveFilePath" onchange="dChge(this);" /></td>
		<td><input name="data[value][seo_archivefilepath]" id="value92" type="text" value="%SEO_ArchiveFilePath%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_93" class="">
	  <td><label title="SEO_Filename">
		<input type="checkbox" name="data[use][seo_filename]" id="use93" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		SEO_Filename		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">y</div></td>
		<td><input name="data[label][seo_filename]" id="label93" type="text" value="SEO_Filename" onchange="dChge(this);" /></td>
		<td><input name="data[value][seo_filename]" id="value93" type="text" value="%SEO_Filename%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_94" class="unused">
	  <td><label title="SHOPSITE_MoreInformationProductCrossSell">
		<input type="checkbox" name="data[use][shopsite_moreinformationproductcrosssell]" id="use94" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_MoreInformationProductC..		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_moreinformationproductcrosssell]" id="label94" type="text" value="SHOPSITE_MoreInformationProductCrossSell" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_moreinformationproductcrosssell]" id="value94" type="text" value="%SHOPSITE_MoreInformationProductCrossSell%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_95" class="unused">
	  <td><label title="SHOPSITE_MoreInformationGlobalCrossSell">
		<input type="checkbox" name="data[use][shopsite_moreinformationglobalcrosssell]" id="use95" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_MoreInformationGlobalCr..		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_moreinformationglobalcrosssell]" id="label95" type="text" value="SHOPSITE_MoreInformationGlobalCrossSell" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_moreinformationglobalcrosssell]" id="value95" type="text" value="%SHOPSITE_MoreInformationGlobalCrossSell%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_96" class="unused">
	  <td><label title="SHOPSITE_DisplaySKU">
		<input type="checkbox" name="data[use][shopsite_displaysku]" id="use96" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_DisplaySKU		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_displaysku]" id="label96" type="text" value="SHOPSITE_DisplaySKU" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_displaysku]" id="value96" type="text" value="%SHOPSITE_DisplaySKU%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_97" class="unused">
	  <td><label title="SHOPSITE_DisplayOrderQuantity">
		<input type="checkbox" name="data[use][shopsite_displayorderquantity]" id="use97" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_DisplayOrderQuantity		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_displayorderquantity]" id="label97" type="text" value="SHOPSITE_DisplayOrderQuantity" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_displayorderquantity]" id="value97" type="text" value="%SHOPSITE_DisplayOrderQuantity%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_98" class="unused">
	  <td><label title="SHOPSITE_DisplayOrderingOptions">
		<input type="checkbox" name="data[use][shopsite_displayorderingoptions]" id="use98" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_DisplayOrderingOptions		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_displayorderingoptions]" id="label98" type="text" value="SHOPSITE_DisplayOrderingOptions" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_displayorderingoptions]" id="value98" type="text" value="%SHOPSITE_DisplayOrderingOptions%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_99" class="unused">
	  <td><label title="SHOPSITE_UseAddtoCartImage">
		<input type="checkbox" name="data[use][shopsite_useaddtocartimage]" id="use99" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_UseAddtoCartImage		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_useaddtocartimage]" id="label99" type="text" value="SHOPSITE_UseAddtoCartImage" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_useaddtocartimage]" id="value99" type="text" value="%SHOPSITE_UseAddtoCartImage%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_100" class="unused">
	  <td><label title="SHOPSITE_AddtoCartImage">
		<input type="checkbox" name="data[use][shopsite_addtocartimage]" id="use100" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_AddtoCartImage		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_addtocartimage]" id="label100" type="text" value="SHOPSITE_AddtoCartImage" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_addtocartimage]" id="value100" type="text" value="%SHOPSITE_AddtoCartImage%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_101" class="unused">
	  <td><label title="SHOPSITE_UseViewCartImage">
		<input type="checkbox" name="data[use][shopsite_useviewcartimage]" id="use101" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_UseViewCartImage		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_useviewcartimage]" id="label101" type="text" value="SHOPSITE_UseViewCartImage" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_useviewcartimage]" id="value101" type="text" value="%SHOPSITE_UseViewCartImage%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_102" class="unused">
	  <td><label title="SHOPSITE_ViewCartImage">
		<input type="checkbox" name="data[use][shopsite_viewcartimage]" id="use102" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_ViewCartImage		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_viewcartimage]" id="label102" type="text" value="SHOPSITE_ViewCartImage" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_viewcartimage]" id="value102" type="text" value="%SHOPSITE_ViewCartImage%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_103" class="unused">
	  <td><label title="SHOPSITE_QtyPricingNumberPriceBreaks">
		<input type="checkbox" name="data[use][shopsite_qtypricingnumberpricebreaks]" id="use103" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_QtyPricingNumberPriceBr..		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_qtypricingnumberpricebreaks]" id="label103" type="text" value="SHOPSITE_QtyPricingNumberPriceBreaks" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_qtypricingnumberpricebreaks]" id="value103" type="text" value="%SHOPSITE_QtyPricingNumberPriceBreaks%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_104" class="unused">
	  <td><label title="SHOPSITE_GoogleProductType">
		<input type="checkbox" name="data[use][shopsite_googleproducttype]" id="use104" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_GoogleProductType		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_googleproducttype]" id="label104" type="text" value="SHOPSITE_GoogleProductType" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_googleproducttype]" id="value104" type="text" value="%SHOPSITE_GoogleProductType%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_105" class="unused">
	  <td><label title="SHOPSITE_Subproducts">
		<input type="checkbox" name="data[use][shopsite_subproducts]" id="use105" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_Subproducts		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_subproducts]" id="label105" type="text" value="SHOPSITE_Subproducts" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_subproducts]" id="value105" type="text" value="%SHOPSITE_Subproducts%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_106" class="unused">
	  <td><label title="SHOPSITE_ProductField3">
		<input type="checkbox" name="data[use][shopsite_productfield3]" id="use106" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_ProductField3		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_productfield3]" id="label106" type="text" value="SHOPSITE_ProductField3" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_productfield3]" id="value106" type="text" value="%SHOPSITE_ProductField3%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_107" class="unused">
	  <td><label title="SHOPSITE_ProductField10">
		<input type="checkbox" name="data[use][shopsite_productfield10]" id="use107" value="1"  onchange="dChge(this);toggleRow(this)" />          
		SHOPSITE_ProductField10		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][shopsite_productfield10]" id="label107" type="text" value="SHOPSITE_ProductField10" onchange="dChge(this);" /></td>
		<td><input name="data[value][shopsite_productfield10]" id="value107" type="text" value="%SHOPSITE_ProductField10%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_108" class="">
	  <td><label title="HMR_OldSKU">
		<input type="checkbox" name="data[use][hmr_oldsku]" id="use108" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		HMR_OldSKU		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][hmr_oldsku]" id="label108" type="text" value="HMR_OldSKU" onchange="dChge(this);" /></td>
		<td><input name="data[value][hmr_oldsku]" id="value108" type="text" value="%HMR_OldSKU%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_109" class="">
	  <td><label title="HMR_OldFileName">
		<input type="checkbox" name="data[use][hmr_oldfilename]" id="use109" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		HMR_OldFileName		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">y</div></td>
		<td><input name="data[label][hmr_oldfilename]" id="label109" type="text" value="HMR_OldFileName" onchange="dChge(this);" /></td>
		<td><input name="data[value][hmr_oldfilename]" id="value109" type="text" value="%HMR_OldFileName%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_110" class="">
	  <td><label title="HMR_Year">
		<input type="checkbox" name="data[use][hmr_year]" id="use110" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		HMR_Year		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][hmr_year]" id="label110" type="text" value="HMR_Year" onchange="dChge(this);" /></td>
		<td><input name="data[value][hmr_year]" id="value110" type="text" value="%HMR_Year%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_111" class="">
	  <td><label title="HMR_YearEstimated">
		<input type="checkbox" name="data[use][hmr_yearestimated]" id="use111" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		HMR_YearEstimated		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][hmr_yearestimated]" id="label111" type="text" value="HMR_YearEstimated" onchange="dChge(this);" /></td>
		<td><input name="data[value][hmr_yearestimated]" id="value111" type="text" value="%HMR_YearEstimated%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_112" class="">
	  <td><label title="HMR_Price1">
		<input type="checkbox" name="data[use][hmr_price1]" id="use112" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		HMR_Price1		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][hmr_price1]" id="label112" type="text" value="HMR_Price1" onchange="dChge(this);" /></td>
		<td><input name="data[value][hmr_price1]" id="value112" type="text" value="%HMR_Price1%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_113" class="">
	  <td><label title="HMR_Price2">
		<input type="checkbox" name="data[use][hmr_price2]" id="use113" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		HMR_Price2		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][hmr_price2]" id="label113" type="text" value="HMR_Price2" onchange="dChge(this);" /></td>
		<td><input name="data[value][hmr_price2]" id="value113" type="text" value="%HMR_Price2%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_114" class="">
	  <td><label title="HMR_Price3">
		<input type="checkbox" name="data[use][hmr_price3]" id="use114" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		HMR_Price3		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][hmr_price3]" id="label114" type="text" value="HMR_Price3" onchange="dChge(this);" /></td>
		<td><input name="data[value][hmr_price3]" id="value114" type="text" value="%HMR_Price3%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_115" class="">
	  <td><label title="HMR_Price4">
		<input type="checkbox" name="data[use][hmr_price4]" id="use115" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		HMR_Price4		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][hmr_price4]" id="label115" type="text" value="HMR_Price4" onchange="dChge(this);" /></td>
		<td><input name="data[value][hmr_price4]" id="value115" type="text" value="%HMR_Price4%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_116" class="">
	  <td><label title="FileSize">
		<input type="checkbox" name="data[use][filesize]" id="use116" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		FileSize		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">y</div></td>
		<td><input name="data[label][filesize]" id="label116" type="text" value="FileSize" onchange="dChge(this);" /></td>
		<td><input name="data[value][filesize]" id="value116" type="text" value="%FileSize%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_117" class="">
	  <td><label title="Width1">
		<input type="checkbox" name="data[use][width1]" id="use117" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Width1		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][width1]" id="label117" type="text" value="Width1" onchange="dChge(this);" /></td>
		<td><input name="data[value][width1]" id="value117" type="text" value="%Width1%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_118" class="">
	  <td><label title="Height1">
		<input type="checkbox" name="data[use][height1]" id="use118" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		Height1		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][height1]" id="label118" type="text" value="Height1" onchange="dChge(this);" /></td>
		<td><input name="data[value][height1]" id="value118" type="text" value="%Height1%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_119" class="">
	  <td><label title="DPI1">
		<input type="checkbox" name="data[use][dpi1]" id="use119" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		DPI1		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][dpi1]" id="label119" type="text" value="DPI1" onchange="dChge(this);" /></td>
		<td><input name="data[value][dpi1]" id="value119" type="text" value="%DPI1%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_120" class="">
	  <td><label title="ThumbData">
		<input type="checkbox" name="data[use][thumbdata]" id="use120" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		ThumbData		</label>
	  </td>
		<td>Long text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][thumbdata]" id="label120" type="text" value="ThumbData" onchange="dChge(this);" /></td>
		<td><input name="data[value][thumbdata]" id="value120" type="text" value="%ThumbData%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_121" class="unused">
	  <td><label title="Lat1">
		<input type="checkbox" name="data[use][lat1]" id="use121" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lat1		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lat1]" id="label121" type="text" value="Lat1" onchange="dChge(this);" /></td>
		<td><input name="data[value][lat1]" id="value121" type="text" value="%Lat1%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_122" class="unused">
	  <td><label title="Lon1">
		<input type="checkbox" name="data[use][lon1]" id="use122" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lon1		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lon1]" id="label122" type="text" value="Lon1" onchange="dChge(this);" /></td>
		<td><input name="data[value][lon1]" id="value122" type="text" value="%Lon1%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_123" class="unused">
	  <td><label title="Lat2">
		<input type="checkbox" name="data[use][lat2]" id="use123" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lat2		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lat2]" id="label123" type="text" value="Lat2" onchange="dChge(this);" /></td>
		<td><input name="data[value][lat2]" id="value123" type="text" value="%Lat2%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_124" class="unused">
	  <td><label title="Lon2">
		<input type="checkbox" name="data[use][lon2]" id="use124" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lon2		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lon2]" id="label124" type="text" value="Lon2" onchange="dChge(this);" /></td>
		<td><input name="data[value][lon2]" id="value124" type="text" value="%Lon2%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_125" class="unused">
	  <td><label title="Lat3">
		<input type="checkbox" name="data[use][lat3]" id="use125" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lat3		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lat3]" id="label125" type="text" value="Lat3" onchange="dChge(this);" /></td>
		<td><input name="data[value][lat3]" id="value125" type="text" value="%Lat3%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_126" class="unused">
	  <td><label title="Lon3">
		<input type="checkbox" name="data[use][lon3]" id="use126" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lon3		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lon3]" id="label126" type="text" value="Lon3" onchange="dChge(this);" /></td>
		<td><input name="data[value][lon3]" id="value126" type="text" value="%Lon3%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_127" class="unused">
	  <td><label title="Lat4">
		<input type="checkbox" name="data[use][lat4]" id="use127" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lat4		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lat4]" id="label127" type="text" value="Lat4" onchange="dChge(this);" /></td>
		<td><input name="data[value][lat4]" id="value127" type="text" value="%Lat4%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_128" class="unused">
	  <td><label title="Lon4">
		<input type="checkbox" name="data[use][lon4]" id="use128" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lon4		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lon4]" id="label128" type="text" value="Lon4" onchange="dChge(this);" /></td>
		<td><input name="data[value][lon4]" id="value128" type="text" value="%Lon4%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_129" class="unused">
	  <td><label title="Lat5">
		<input type="checkbox" name="data[use][lat5]" id="use129" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lat5		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lat5]" id="label129" type="text" value="Lat5" onchange="dChge(this);" /></td>
		<td><input name="data[value][lat5]" id="value129" type="text" value="%Lat5%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_130" class="unused">
	  <td><label title="Lon5">
		<input type="checkbox" name="data[use][lon5]" id="use130" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lon5		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lon5]" id="label130" type="text" value="Lon5" onchange="dChge(this);" /></td>
		<td><input name="data[value][lon5]" id="value130" type="text" value="%Lon5%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_131" class="unused">
	  <td><label title="Lat6">
		<input type="checkbox" name="data[use][lat6]" id="use131" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lat6		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lat6]" id="label131" type="text" value="Lat6" onchange="dChge(this);" /></td>
		<td><input name="data[value][lat6]" id="value131" type="text" value="%Lat6%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_132" class="unused">
	  <td><label title="Lon6">
		<input type="checkbox" name="data[use][lon6]" id="use132" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lon6		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lon6]" id="label132" type="text" value="Lon6" onchange="dChge(this);" /></td>
		<td><input name="data[value][lon6]" id="value132" type="text" value="%Lon6%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_133" class="unused">
	  <td><label title="Lat7">
		<input type="checkbox" name="data[use][lat7]" id="use133" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lat7		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lat7]" id="label133" type="text" value="Lat7" onchange="dChge(this);" /></td>
		<td><input name="data[value][lat7]" id="value133" type="text" value="%Lat7%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_134" class="unused">
	  <td><label title="Lon7">
		<input type="checkbox" name="data[use][lon7]" id="use134" value="1"  onchange="dChge(this);toggleRow(this)" />          
		Lon7		</label>
	  </td>
		<td>Decimal</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][lon7]" id="label134" type="text" value="Lon7" onchange="dChge(this);" /></td>
		<td><input name="data[value][lon7]" id="value134" type="text" value="%Lon7%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_135" class="">
	  <td><label title="OverflowType">
		<input type="checkbox" name="data[use][overflowtype]" id="use135" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		OverflowType		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][overflowtype]" id="label135" type="text" value="OverflowType" onchange="dChge(this);" /></td>
		<td><input name="data[value][overflowtype]" id="value135" type="text" value="%OverflowType%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_136" class="">
	  <td><label title="EBAY_ItemID">
		<input type="checkbox" name="data[use][ebay_itemid]" id="use136" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		EBAY_ItemID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][ebay_itemid]" id="label136" type="text" value="EBAY_ItemID" onchange="dChge(this);" /></td>
		<td><input name="data[value][ebay_itemid]" id="value136" type="text" value="%EBAY_ItemID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_137" class="">
	  <td><label title="EBAY_QuantityAvailable">
		<input type="checkbox" name="data[use][ebay_quantityavailable]" id="use137" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		EBAY_QuantityAvailable		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][ebay_quantityavailable]" id="label137" type="text" value="EBAY_QuantityAvailable" onchange="dChge(this);" /></td>
		<td><input name="data[value][ebay_quantityavailable]" id="value137" type="text" value="%EBAY_QuantityAvailable%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_138" class="">
	  <td><label title="EBAY_Purchases">
		<input type="checkbox" name="data[use][ebay_purchases]" id="use138" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		EBAY_Purchases		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][ebay_purchases]" id="label138" type="text" value="EBAY_Purchases" onchange="dChge(this);" /></td>
		<td><input name="data[value][ebay_purchases]" id="value138" type="text" value="%EBAY_Purchases%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_139" class="">
	  <td><label title="EBAY_Bids">
		<input type="checkbox" name="data[use][ebay_bids]" id="use139" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		EBAY_Bids		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][ebay_bids]" id="label139" type="text" value="EBAY_Bids" onchange="dChge(this);" /></td>
		<td><input name="data[value][ebay_bids]" id="value139" type="text" value="%EBAY_Bids%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_140" class="">
	  <td><label title="EBAY_Type">
		<input type="checkbox" name="data[use][ebay_type]" id="use140" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		EBAY_Type		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][ebay_type]" id="label140" type="text" value="EBAY_Type" onchange="dChge(this);" /></td>
		<td><input name="data[value][ebay_type]" id="value140" type="text" value="%EBAY_Type%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_141" class="">
	  <td><label title="EBAY_CategoryLeafName">
		<input type="checkbox" name="data[use][ebay_categoryleafname]" id="use141" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		EBAY_CategoryLeafName		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][ebay_categoryleafname]" id="label141" type="text" value="EBAY_CategoryLeafName" onchange="dChge(this);" /></td>
		<td><input name="data[value][ebay_categoryleafname]" id="value141" type="text" value="%EBAY_CategoryLeafName%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_142" class="">
	  <td><label title="EBAY_CategoryNumber">
		<input type="checkbox" name="data[use][ebay_categorynumber]" id="use142" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		EBAY_CategoryNumber		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][ebay_categorynumber]" id="label142" type="text" value="EBAY_CategoryNumber" onchange="dChge(this);" /></td>
		<td><input name="data[value][ebay_categorynumber]" id="value142" type="text" value="%EBAY_CategoryNumber%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_143" class="">
	  <td><label title="AMAZON_Text1">
		<input type="checkbox" name="data[use][amazon_text1]" id="use143" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		AMAZON_Text1		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][amazon_text1]" id="label143" type="text" value="AMAZON_Text1" onchange="dChge(this);" /></td>
		<td><input name="data[value][amazon_text1]" id="value143" type="text" value="%AMAZON_Text1%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_144" class="">
	  <td><label title="AMAZON_Text2">
		<input type="checkbox" name="data[use][amazon_text2]" id="use144" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		AMAZON_Text2		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][amazon_text2]" id="label144" type="text" value="AMAZON_Text2" onchange="dChge(this);" /></td>
		<td><input name="data[value][amazon_text2]" id="value144" type="text" value="%AMAZON_Text2%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_145" class="">
	  <td><label title="AMAZON_listingid">
		<input type="checkbox" name="data[use][amazon_listingid]" id="use145" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		AMAZON_listingid		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][amazon_listingid]" id="label145" type="text" value="AMAZON_listingid" onchange="dChge(this);" /></td>
		<td><input name="data[value][amazon_listingid]" id="value145" type="text" value="%AMAZON_listingid%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_146" class="">
	  <td><label title="AMAZON_Quantity">
		<input type="checkbox" name="data[use][amazon_quantity]" id="use146" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		AMAZON_Quantity		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][amazon_quantity]" id="label146" type="text" value="AMAZON_Quantity" onchange="dChge(this);" /></td>
		<td><input name="data[value][amazon_quantity]" id="value146" type="text" value="%AMAZON_Quantity%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_147" class="">
	  <td><label title="AMAZON_OpenDate">
		<input type="checkbox" name="data[use][amazon_opendate]" id="use147" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		AMAZON_OpenDate		</label>
	  </td>
		<td>Date/time</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][amazon_opendate]" id="label147" type="text" value="AMAZON_OpenDate" onchange="dChge(this);" /></td>
		<td><input name="data[value][amazon_opendate]" id="value147" type="text" value="%AMAZON_OpenDate%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_148" class="">
	  <td><label title="AMAZON_itemismarketplace">
		<input type="checkbox" name="data[use][amazon_itemismarketplace]" id="use148" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		AMAZON_itemismarketplace		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][amazon_itemismarketplace]" id="label148" type="text" value="AMAZON_itemismarketplace" onchange="dChge(this);" /></td>
		<td><input name="data[value][amazon_itemismarketplace]" id="value148" type="text" value="%AMAZON_itemismarketplace%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_149" class="">
	  <td><label title="AMAZON_productidtype">
		<input type="checkbox" name="data[use][amazon_productidtype]" id="use149" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		AMAZON_productidtype		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][amazon_productidtype]" id="label149" type="text" value="AMAZON_productidtype" onchange="dChge(this);" /></td>
		<td><input name="data[value][amazon_productidtype]" id="value149" type="text" value="%AMAZON_productidtype%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_150" class="">
	  <td><label title="AMAZON_itemcondition">
		<input type="checkbox" name="data[use][amazon_itemcondition]" id="use150" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		AMAZON_itemcondition		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][amazon_itemcondition]" id="label150" type="text" value="AMAZON_itemcondition" onchange="dChge(this);" /></td>
		<td><input name="data[value][amazon_itemcondition]" id="value150" type="text" value="%AMAZON_itemcondition%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_151" class="">
	  <td><label title="AMAZON_asin1">
		<input type="checkbox" name="data[use][amazon_asin1]" id="use151" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		AMAZON_asin1		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][amazon_asin1]" id="label151" type="text" value="AMAZON_asin1" onchange="dChge(this);" /></td>
		<td><input name="data[value][amazon_asin1]" id="value151" type="text" value="%AMAZON_asin1%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_152" class="">
	  <td><label title="SCOM_LIID">
		<input type="checkbox" name="data[use][scom_liid]" id="use152" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		SCOM_LIID		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][scom_liid]" id="label152" type="text" value="SCOM_LIID" onchange="dChge(this);" /></td>
		<td><input name="data[value][scom_liid]" id="value152" type="text" value="%SCOM_LIID%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_153" class="">
	  <td><label title="SCOM_LDSKU">
		<input type="checkbox" name="data[use][scom_ldsku]" id="use153" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		SCOM_LDSKU		</label>
	  </td>
		<td>Integer</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][scom_ldsku]" id="label153" type="text" value="SCOM_LDSKU" onchange="dChge(this);" /></td>
		<td><input name="data[value][scom_ldsku]" id="value153" type="text" value="%SCOM_LDSKU%" onchange="dChge(this);" /></td>

		<td class="tc">string</td>
	</tr><tr id="r_154" class="">
	  <td><label title="PRODUCT_CODE">
		<input type="checkbox" name="data[use][product_code]" id="use154" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		PRODUCT_CODE		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][product_code]" id="label154" type="text" value="PRODUCT_CODE" onchange="dChge(this);" /></td>
		<td><input name="data[value][product_code]" id="value154" type="text" value="%PRODUCT_CODE%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr id="r_155" class="">
	  <td><label title="CATEGORY_CODES">
		<input type="checkbox" name="data[use][category_codes]" id="use155" value="1" checked onchange="dChge(this);toggleRow(this)" />          
		CATEGORY_CODES		</label>
	  </td>
		<td>Text</td>
		<td><div align="center">&nbsp;</div></td>
		<td><input name="data[label][category_codes]" id="label155" type="text" value="CATEGORY_CODES" onchange="dChge(this);" /></td>
		<td><input name="data[value][category_codes]" id="value155" type="text" value="%CATEGORY_CODES%" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><tr>
		<td colspan="100%"><h2>Expressions</h2></td>
		</tr><tr id="r_156" class="">
				<td><label><input type="checkbox" name="data[useexpr][catsubcat]" value="1" checked onchange="dChge(this);toggleRow(this);" id="use156" />
				  catsubcat</label></td>
				<td>expression <a title="click to see expression" href="#" onclick="alert('concat(category,\':\',subcategory)');return false;">[i]</a> </td>
				<td class="na">&nbsp;</td>
				<td><input name="data[exprcol][catsubcat]" type="text" value="catsubcat" onchange="dChge(this);" /></td>
				<td><input name="data[exprval][catsubcat]" type="text" value="%catsubcat%" onchange="dChge(this);" /></td>
				<td class="tc">string</td>
		</tr>	<tr>
	<td colspan="3">&nbsp;</td>
	<td colspan="3"><h2>Additional export columns:</h2></td>
	</tr>
	<tr>
			<td colspan="3">&nbsp;</td>
			<td><input type="text" name="data[additionalexpr][1]" value="" onchange="dChge(this);" /></td>
			<td><input type="text" name="data[additionalexprval][1]" value="" onchange="dChge(this);" /></td>
			<td><select name="data[additionalexprdisp][1]" onchange="dChge(this);">
			<option value="string" >string</option>
			<option value="mysqlExpression" >mySQL expr.</option>
			</select>
			</td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td><input type="text" name="data[additionalexpr][2]" value="" onchange="dChge(this);" /></td>
			<td><input type="text" name="data[additionalexprval][2]" value="" onchange="dChge(this);" /></td>
			<td><select name="data[additionalexprdisp][2]" onchange="dChge(this);">
			<option value="string" >string</option>
			<option value="mysqlExpression" >mySQL expr.</option>
			</select>
			</td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td><input type="text" name="data[additionalexpr][3]" value="" onchange="dChge(this);" /></td>
			<td><input type="text" name="data[additionalexprval][3]" value="" onchange="dChge(this);" /></td>
			<td><select name="data[additionalexprdisp][3]" onchange="dChge(this);">
			<option value="string" >string</option>
			<option value="mysqlExpression" >mySQL expr.</option>
			</select>
			</td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td><input type="text" name="data[additionalexpr][4]" value="" onchange="dChge(this);" /></td>
			<td><input type="text" name="data[additionalexprval][4]" value="" onchange="dChge(this);" /></td>
			<td><select name="data[additionalexprdisp][4]" onchange="dChge(this);">
			<option value="string" >string</option>
			<option value="mysqlExpression" >mySQL expr.</option>
			</select>
			</td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td><input type="text" name="data[additionalexpr][5]" value="" onchange="dChge(this);" /></td>
			<td><input type="text" name="data[additionalexprval][5]" value="" onchange="dChge(this);" /></td>
			<td><select name="data[additionalexprdisp][5]" onchange="dChge(this);">
			<option value="string" >string</option>
			<option value="mysqlExpression" >mySQL expr.</option>
			</select>
			</td>
		</tr>
		    </table>
	</div>

<div id="section_criteria" class="tabSectionStyleIII" style="display:none;">
	Filter (&quot;where&quot; clause of mySQL query) :<br />
	<textarea name="data[filter]" cols="45" rows="2" id="filter">ID BETWEEN 1 AND 3645</textarea>

	</div>

<div id="section_format" class="tabSectionStyleIII" style="display:none;">
	Delimiter: 
	<select name="data[delimiter]" id="data[delimiter]" onchange="dChge(this);">
	<option value="," selected>Comma</option>
	<option value="	" >Tab</option>
	<option value="|" >Pipe (|)</option>
	<option value="^" >Caret (^)</option>
	</select>
	
	</div>

<div id="section_export" class="tabSectionStyleIII" style="display:none;">
	<label>
	<input name="data[ExportToEmail]" type="checkbox" id="data[ExportToEmail]" value="1" checked onchange="dChge(this);" /> 
	export to email:</label>
	<input name="data[ExportToEmailAddress]" type="text" id="data[ExportToEmailAddress]" value="samuelf@compasspoint-sw.com,sales@amazingcity.com" size="50" class="" onfocus="if(this.value=='(email addresses, separate multiple by commas)'){this.value='';this.className='';}" onblur="if(this.value==''){this.value='(email addresses, separate multiple by commas)';this.className='gray';}" onchange="dChge(this);" />
	<em class="gray">(separate multiple by commas)</em>
	<br />
	<label>
	<input name="data[ExportAsAttachment]" type="checkbox" id="data[ExportAsAttachment]" value="1" onchange="dChge(this);" />
	export as attachment (right now)</label>
	<br />
	<br />
	Export file name: 
	<input name="data[ExportFileName]" type="text" id="data[ExportFileName]" onchange="dChge(this);" value="kangaroo_%d:Ymd_His%_[%c%].csv" size="70" />
	<br />
	[<a href="javascript:alert('%c% = record count\n%t% = primary table\n%d:Ymd_His% = PHP date variables\n%u% = your user name\n\nFor a list of PHP date variables to to http://php.net/date');">click to see filename wildcard values</a>]<br />
	</p>
	</div>

<div id="section_schedule" class="tabSectionStyleIII" style="display:none;">
	schedule
	
	</div>

<div id="section_logs" class="tabSectionStyleIII" style="display:block;">
	logs
	
	</div>

<div id="section_help" class="tabSectionStyleIII" style="display:none;">
	<pre>tSettings</pre><pre>Array
(
  [expressions] =&gt; Array
    (
      [catsubcat] =&gt; concat(category,':',subcategory)
    )
)
</pre><pre>pSettings</pre><pre>Array
(
  [data] =&gt; Array
    (
      [ExportToEmail] =&gt; 1
      [ExportToEmailAddress] =&gt; sfullman@compasspoint-sw.com,sales@amazingcity.com
      [ExportAsAttachment] =&gt; 1
      [delimiter] =&gt; ,
      [ExportFileName] =&gt; kangaroo_%d:Ymd_His%_[%c%].csv
      [label] =&gt; Array
        (
          [id] =&gt; ID
          [createdate] =&gt; CreateDate
          [creator] =&gt; Creator
          [editdate] =&gt; EditDate
          [editdatebuffer] =&gt; EditDateBuffer
          [editor] =&gt; Editor
          [islocked] =&gt; IsLocked
          [ignorefile] =&gt; IgnoreFile
          [active] =&gt; Active
          [outofstock] =&gt; OutOfStock
          [tobeexported] =&gt; ToBeExported
          [shopsite_tobeexported] =&gt; SHOPSITE_ToBeExported
          [miva_tobeexported] =&gt; MIVA_ToBeExported
          [amazon_tobeexported] =&gt; AMAZON_ToBeExported
          [ebay_tobeexported] =&gt; EBAY_ToBeExported
          [exportdate] =&gt; ExportDate
          [exporter] =&gt; Exporter
          [instock] =&gt; InStock
          [reorderpt] =&gt; ReorderPt
          [ourgroup_id] =&gt; Ourgroup_ID
          [schemas_id] =&gt; Schemas_ID
          [groupleader] =&gt; GroupLeader
          [supersku] =&gt; SuperSKU
          [model] =&gt; Model
          [sku] =&gt; SKU
          [manufacturersku] =&gt; ManufacturerSKU
          [upc] =&gt; UPC
          [rwb] =&gt; RWB
          [priority] =&gt; Priority
          [category] =&gt; Category
          [subcategory] =&gt; SubCategory
          [primaryregion] =&gt; PrimaryRegion
          [new_subcategory] =&gt; New_SubCategory
          [del] =&gt; del
          [name] =&gt; Name
          [caption] =&gt; Caption
          [manufacturers_id] =&gt; Manufacturers_ID
          [manufacturer] =&gt; Manufacturer
          [brand] =&gt; Brand
          [vendors_id] =&gt; Vendors_ID
          [vendor] =&gt; Vendor
          [description] =&gt; Description
          [longdescription] =&gt; LongDescription
          [function] =&gt; Function
          [subfunction] =&gt; SubFunction
          [itemfootnote] =&gt; ItemFootnote
          [notes] =&gt; Notes
          [purchaseprice] =&gt; PurchasePrice
          [wholesaleprice] =&gt; WholesalePrice
          [unitprice] =&gt; UnitPrice
          [unitprice2] =&gt; UnitPrice2
          [taxable] =&gt; Taxable
          [taxable2] =&gt; Taxable2
          [special] =&gt; Special
          [featured] =&gt; Featured
          [listprice] =&gt; ListPrice
          [length] =&gt; Length
          [width] =&gt; Width
          [depth] =&gt; Depth
          [weight] =&gt; Weight
          [shippers_id] =&gt; Shippers_ID
          [accounts_id] =&gt; Accounts_ID
          [assetaccounts_id] =&gt; AssetAccounts_ID
          [cogsaccounts_id] =&gt; COGSAccounts_ID
          [classes_id] =&gt; Classes_ID
          [type] =&gt; Type
          [ispassedthrough] =&gt; IsPassedThrough
          [items_id] =&gt; Items_ID
          [keywords] =&gt; Keywords
          [expirationdate] =&gt; ExpirationDate
          [edition] =&gt; Edition
          [theme] =&gt; Theme
          [subtheme] =&gt; SubTheme
          [breakprice] =&gt; BreakPrice
          [um] =&gt; UM
          [pk] =&gt; PK
          [resourcetype] =&gt; ResourceType
          [resourcetoken] =&gt; ResourceToken
          [sessionkey] =&gt; SessionKey
          [source] =&gt; Source
          [refnbr] =&gt; RefNbr
          [vat] =&gt; VAT
          [shopsite_productimagesize] =&gt; SHOPSITE_ProductImageSize
          [shopsite_productonpages] =&gt; SHOPSITE_ProductOnPages
          [shopsite_addtopages] =&gt; SHOPSITE_AddToPages
          [shopsite_moreinformationimagesize] =&gt; SHOPSITE_MoreInformationImageSize
          [metatitle] =&gt; MetaTitle
          [metakeywords] =&gt; MetaKeywords
          [metadescription] =&gt; MetaDescription
          [seo_archivefilepath] =&gt; SEO_ArchiveFilePath
          [seo_filename] =&gt; SEO_Filename
          [shopsite_moreinformationproductcrosssell] =&gt; SHOPSITE_MoreInformationProductCrossSell
          [shopsite_moreinformationglobalcrosssell] =&gt; SHOPSITE_MoreInformationGlobalCrossSell
          [shopsite_displaysku] =&gt; SHOPSITE_DisplaySKU
          [shopsite_displayorderquantity] =&gt; SHOPSITE_DisplayOrderQuantity
          [shopsite_displayorderingoptions] =&gt; SHOPSITE_DisplayOrderingOptions
          [shopsite_useaddtocartimage] =&gt; SHOPSITE_UseAddtoCartImage
          [shopsite_addtocartimage] =&gt; SHOPSITE_AddtoCartImage
          [shopsite_useviewcartimage] =&gt; SHOPSITE_UseViewCartImage
          [shopsite_viewcartimage] =&gt; SHOPSITE_ViewCartImage
          [shopsite_qtypricingnumberpricebreaks] =&gt; SHOPSITE_QtyPricingNumberPriceBreaks
          [shopsite_googleproducttype] =&gt; SHOPSITE_GoogleProductType
          [shopsite_subproducts] =&gt; SHOPSITE_Subproducts
          [shopsite_productfield3] =&gt; SHOPSITE_ProductField3
          [shopsite_productfield10] =&gt; SHOPSITE_ProductField10
          [hmr_oldsku] =&gt; HMR_OldSKU
          [hmr_oldfilename] =&gt; HMR_OldFileName
          [hmr_year] =&gt; HMR_Year
          [hmr_yearestimated] =&gt; HMR_YearEstimated
          [hmr_price1] =&gt; HMR_Price1
          [hmr_price2] =&gt; HMR_Price2
          [hmr_price3] =&gt; HMR_Price3
          [hmr_price4] =&gt; HMR_Price4
          [filesize] =&gt; FileSize
          [width1] =&gt; Width1
          [height1] =&gt; Height1
          [dpi1] =&gt; DPI1
          [thumbdata] =&gt; ThumbData
          [lat1] =&gt; Lat1
          [lon1] =&gt; Lon1
          [lat2] =&gt; Lat2
          [lon2] =&gt; Lon2
          [lat3] =&gt; Lat3
          [lon3] =&gt; Lon3
          [lat4] =&gt; Lat4
          [lon4] =&gt; Lon4
          [lat5] =&gt; Lat5
          [lon5] =&gt; Lon5
          [lat6] =&gt; Lat6
          [lon6] =&gt; Lon6
          [lat7] =&gt; Lat7
          [lon7] =&gt; Lon7
          [overflowtype] =&gt; OverflowType
          [ebay_itemid] =&gt; EBAY_ItemID
          [ebay_quantityavailable] =&gt; EBAY_QuantityAvailable
          [ebay_purchases] =&gt; EBAY_Purchases
          [ebay_bids] =&gt; EBAY_Bids
          [ebay_type] =&gt; EBAY_Type
          [ebay_categoryleafname] =&gt; EBAY_CategoryLeafName
          [ebay_categorynumber] =&gt; EBAY_CategoryNumber
          [amazon_text1] =&gt; AMAZON_Text1
          [amazon_text2] =&gt; AMAZON_Text2
          [amazon_listingid] =&gt; AMAZON_listingid
          [amazon_quantity] =&gt; AMAZON_Quantity
          [amazon_opendate] =&gt; AMAZON_OpenDate
          [amazon_itemismarketplace] =&gt; AMAZON_itemismarketplace
          [amazon_productidtype] =&gt; AMAZON_productidtype
          [amazon_itemcondition] =&gt; AMAZON_itemcondition
          [amazon_asin1] =&gt; AMAZON_asin1
          [scom_liid] =&gt; SCOM_LIID
          [scom_ldsku] =&gt; SCOM_LDSKU
          [product_code] =&gt; PRODUCT_CODE
          [category_codes] =&gt; CATEGORY_CODES
        )
      [value] =&gt; Array
        (
          [id] =&gt; %ID%
          [createdate] =&gt; %CreateDate%
          [creator] =&gt; %Creator%
          [editdate] =&gt; %EditDate%
          [editdatebuffer] =&gt; %EditDateBuffer%
          [editor] =&gt; %Editor%
          [islocked] =&gt; %IsLocked%
          [ignorefile] =&gt; %IgnoreFile%
          [active] =&gt; %Active%
          [outofstock] =&gt; %OutOfStock%
          [tobeexported] =&gt; %ToBeExported%
          [shopsite_tobeexported] =&gt; %SHOPSITE_ToBeExported%
          [miva_tobeexported] =&gt; %MIVA_ToBeExported%
          [amazon_tobeexported] =&gt; %AMAZON_ToBeExported%
          [ebay_tobeexported] =&gt; %EBAY_ToBeExported%
          [exportdate] =&gt; %ExportDate%
          [exporter] =&gt; %Exporter%
          [instock] =&gt; %InStock%
          [reorderpt] =&gt; %ReorderPt%
          [ourgroup_id] =&gt; %Ourgroup_ID%
          [schemas_id] =&gt; %Schemas_ID%
          [groupleader] =&gt; %GroupLeader%
          [supersku] =&gt; %SuperSKU%
          [model] =&gt; %Model%
          [sku] =&gt; %SKU%
          [manufacturersku] =&gt; %ManufacturerSKU%
          [upc] =&gt; %UPC%
          [rwb] =&gt; %RWB%
          [priority] =&gt; %Priority%
          [category] =&gt; %Category%
          [subcategory] =&gt; %SubCategory%
          [primaryregion] =&gt; %PrimaryRegion%
          [new_subcategory] =&gt; %New_SubCategory%
          [del] =&gt; %del%
          [name] =&gt; %Name%
          [caption] =&gt; %Caption%
          [manufacturers_id] =&gt; %Manufacturers_ID%
          [manufacturer] =&gt; %Manufacturer%
          [brand] =&gt; %Brand%
          [vendors_id] =&gt; %Vendors_ID%
          [vendor] =&gt; %Vendor%
          [description] =&gt; %Description%
          [longdescription] =&gt; %LongDescription%
          [function] =&gt; %Function%
          [subfunction] =&gt; %SubFunction%
          [itemfootnote] =&gt; %ItemFootnote%
          [notes] =&gt; %Notes%
          [purchaseprice] =&gt; %PurchasePrice%
          [wholesaleprice] =&gt; %WholesalePrice%
          [unitprice] =&gt; %UnitPrice%
          [unitprice2] =&gt; %UnitPrice2%
          [taxable] =&gt; %Taxable%
          [taxable2] =&gt; %Taxable2%
          [special] =&gt; %Special%
          [featured] =&gt; %Featured%
          [listprice] =&gt; %ListPrice%
          [length] =&gt; %Length%
          [width] =&gt; %Width%
          [depth] =&gt; %Depth%
          [weight] =&gt; %Weight%
          [shippers_id] =&gt; %Shippers_ID%
          [accounts_id] =&gt; %Accounts_ID%
          [assetaccounts_id] =&gt; %AssetAccounts_ID%
          [cogsaccounts_id] =&gt; %COGSAccounts_ID%
          [classes_id] =&gt; %Classes_ID%
          [type] =&gt; %Type%
          [ispassedthrough] =&gt; %IsPassedThrough%
          [items_id] =&gt; %Items_ID%
          [keywords] =&gt; %Keywords%
          [expirationdate] =&gt; %ExpirationDate%
          [edition] =&gt; %Edition%
          [theme] =&gt; %Theme%
          [subtheme] =&gt; %SubTheme%
          [breakprice] =&gt; %BreakPrice%
          [um] =&gt; %UM%
          [pk] =&gt; %PK%
          [resourcetype] =&gt; %ResourceType%
          [resourcetoken] =&gt; %ResourceToken%
          [sessionkey] =&gt; %SessionKey%
          [source] =&gt; %Source%
          [refnbr] =&gt; %RefNbr%
          [vat] =&gt; %VAT%
          [shopsite_productimagesize] =&gt; %SHOPSITE_ProductImageSize%
          [shopsite_productonpages] =&gt; %SHOPSITE_ProductOnPages%
          [shopsite_addtopages] =&gt; %SHOPSITE_AddToPages%
          [shopsite_moreinformationimagesize] =&gt; %SHOPSITE_MoreInformationImageSize%
          [metatitle] =&gt; %MetaTitle%
          [metakeywords] =&gt; %MetaKeywords%
          [metadescription] =&gt; %MetaDescription%
          [seo_archivefilepath] =&gt; %SEO_ArchiveFilePath%
          [seo_filename] =&gt; %SEO_Filename%
          [shopsite_moreinformationproductcrosssell] =&gt; %SHOPSITE_MoreInformationProductCrossSell%
          [shopsite_moreinformationglobalcrosssell] =&gt; %SHOPSITE_MoreInformationGlobalCrossSell%
          [shopsite_displaysku] =&gt; %SHOPSITE_DisplaySKU%
          [shopsite_displayorderquantity] =&gt; %SHOPSITE_DisplayOrderQuantity%
          [shopsite_displayorderingoptions] =&gt; %SHOPSITE_DisplayOrderingOptions%
          [shopsite_useaddtocartimage] =&gt; %SHOPSITE_UseAddtoCartImage%
          [shopsite_addtocartimage] =&gt; %SHOPSITE_AddtoCartImage%
          [shopsite_useviewcartimage] =&gt; %SHOPSITE_UseViewCartImage%
          [shopsite_viewcartimage] =&gt; %SHOPSITE_ViewCartImage%
          [shopsite_qtypricingnumberpricebreaks] =&gt; %SHOPSITE_QtyPricingNumberPriceBreaks%
          [shopsite_googleproducttype] =&gt; %SHOPSITE_GoogleProductType%
          [shopsite_subproducts] =&gt; %SHOPSITE_Subproducts%
          [shopsite_productfield3] =&gt; %SHOPSITE_ProductField3%
          [shopsite_productfield10] =&gt; %SHOPSITE_ProductField10%
          [hmr_oldsku] =&gt; %HMR_OldSKU%
          [hmr_oldfilename] =&gt; %HMR_OldFileName%
          [hmr_year] =&gt; %HMR_Year%
          [hmr_yearestimated] =&gt; %HMR_YearEstimated%
          [hmr_price1] =&gt; %HMR_Price1%
          [hmr_price2] =&gt; %HMR_Price2%
          [hmr_price3] =&gt; %HMR_Price3%
          [hmr_price4] =&gt; %HMR_Price4%
          [filesize] =&gt; %FileSize%
          [width1] =&gt; %Width1%
          [height1] =&gt; %Height1%
          [dpi1] =&gt; %DPI1%
          [thumbdata] =&gt; %ThumbData%
          [lat1] =&gt; %Lat1%
          [lon1] =&gt; %Lon1%
          [lat2] =&gt; %Lat2%
          [lon2] =&gt; %Lon2%
          [lat3] =&gt; %Lat3%
          [lon3] =&gt; %Lon3%
          [lat4] =&gt; %Lat4%
          [lon4] =&gt; %Lon4%
          [lat5] =&gt; %Lat5%
          [lon5] =&gt; %Lon5%
          [lat6] =&gt; %Lat6%
          [lon6] =&gt; %Lon6%
          [lat7] =&gt; %Lat7%
          [lon7] =&gt; %Lon7%
          [overflowtype] =&gt; %OverflowType%
          [ebay_itemid] =&gt; %EBAY_ItemID%
          [ebay_quantityavailable] =&gt; %EBAY_QuantityAvailable%
          [ebay_purchases] =&gt; %EBAY_Purchases%
          [ebay_bids] =&gt; %EBAY_Bids%
          [ebay_type] =&gt; %EBAY_Type%
          [ebay_categoryleafname] =&gt; %EBAY_CategoryLeafName%
          [ebay_categorynumber] =&gt; %EBAY_CategoryNumber%
          [amazon_text1] =&gt; %AMAZON_Text1%
          [amazon_text2] =&gt; %AMAZON_Text2%
          [amazon_listingid] =&gt; %AMAZON_listingid%
          [amazon_quantity] =&gt; %AMAZON_Quantity%
          [amazon_opendate] =&gt; %AMAZON_OpenDate%
          [amazon_itemismarketplace] =&gt; %AMAZON_itemismarketplace%
          [amazon_productidtype] =&gt; %AMAZON_productidtype%
          [amazon_itemcondition] =&gt; %AMAZON_itemcondition%
          [amazon_asin1] =&gt; %AMAZON_asin1%
          [scom_liid] =&gt; %SCOM_LIID%
          [scom_ldsku] =&gt; %SCOM_LDSKU%
          [product_code] =&gt; %PRODUCT_CODE%
          [category_codes] =&gt; %CATEGORY_CODES%
        )
      [use] =&gt; Array
        (
          [createdate] =&gt; 1
          [creator] =&gt; 1
          [editdate] =&gt; 1
          [editdatebuffer] =&gt; 1
          [editor] =&gt; 1
          [islocked] =&gt; 1
          [ignorefile] =&gt; 1
          [active] =&gt; 1
          [outofstock] =&gt; 1
          [tobeexported] =&gt; 1
          [shopsite_tobeexported] =&gt; 1
          [miva_tobeexported] =&gt; 1
          [amazon_tobeexported] =&gt; 1
          [ebay_tobeexported] =&gt; 1
          [exportdate] =&gt; 1
          [exporter] =&gt; 1
          [instock] =&gt; 1
          [reorderpt] =&gt; 1
          [ourgroup_id] =&gt; 1
          [schemas_id] =&gt; 1
          [groupleader] =&gt; 1
          [supersku] =&gt; 1
          [model] =&gt; 1
          [sku] =&gt; 1
          [manufacturersku] =&gt; 1
          [upc] =&gt; 1
          [rwb] =&gt; 1
          [priority] =&gt; 1
          [category] =&gt; 1
          [subcategory] =&gt; 1
          [primaryregion] =&gt; 1
          [new_subcategory] =&gt; 1
          [del] =&gt; 1
          [name] =&gt; 1
          [caption] =&gt; 1
          [manufacturers_id] =&gt; 1
          [manufacturer] =&gt; 1
          [brand] =&gt; 1
          [vendors_id] =&gt; 1
          [vendor] =&gt; 1
          [description] =&gt; 1
          [longdescription] =&gt; 1
          [function] =&gt; 1
          [subfunction] =&gt; 1
          [itemfootnote] =&gt; 1
          [notes] =&gt; 1
          [purchaseprice] =&gt; 1
          [wholesaleprice] =&gt; 1
          [unitprice] =&gt; 1
          [unitprice2] =&gt; 1
          [taxable] =&gt; 1
          [taxable2] =&gt; 1
          [special] =&gt; 1
          [featured] =&gt; 1
          [listprice] =&gt; 1
          [length] =&gt; 1
          [width] =&gt; 1
          [depth] =&gt; 1
          [weight] =&gt; 1
          [shippers_id] =&gt; 1
          [accounts_id] =&gt; 1
          [assetaccounts_id] =&gt; 1
          [cogsaccounts_id] =&gt; 1
          [classes_id] =&gt; 1
          [type] =&gt; 1
          [ispassedthrough] =&gt; 1
          [items_id] =&gt; 1
          [keywords] =&gt; 1
          [expirationdate] =&gt; 1
          [edition] =&gt; 1
          [theme] =&gt; 1
          [subtheme] =&gt; 1
          [breakprice] =&gt; 1
          [um] =&gt; 1
          [pk] =&gt; 1
          [resourcetype] =&gt; 1
          [resourcetoken] =&gt; 1
          [sessionkey] =&gt; 1
          [source] =&gt; 1
          [refnbr] =&gt; 1
          [vat] =&gt; 1
          [metatitle] =&gt; 1
          [metakeywords] =&gt; 1
          [metadescription] =&gt; 1
          [seo_archivefilepath] =&gt; 1
          [seo_filename] =&gt; 1
          [hmr_oldsku] =&gt; 1
          [hmr_oldfilename] =&gt; 1
          [hmr_year] =&gt; 1
          [hmr_yearestimated] =&gt; 1
          [hmr_price1] =&gt; 1
          [hmr_price2] =&gt; 1
          [hmr_price3] =&gt; 1
          [hmr_price4] =&gt; 1
          [filesize] =&gt; 1
          [width1] =&gt; 1
          [height1] =&gt; 1
          [dpi1] =&gt; 1
          [thumbdata] =&gt; 1
          [overflowtype] =&gt; 1
          [ebay_itemid] =&gt; 1
          [ebay_quantityavailable] =&gt; 1
          [ebay_purchases] =&gt; 1
          [ebay_bids] =&gt; 1
          [ebay_type] =&gt; 1
          [ebay_categoryleafname] =&gt; 1
          [ebay_categorynumber] =&gt; 1
          [amazon_text1] =&gt; 1
          [amazon_text2] =&gt; 1
          [amazon_listingid] =&gt; 1
          [amazon_quantity] =&gt; 1
          [amazon_opendate] =&gt; 1
          [amazon_itemismarketplace] =&gt; 1
          [amazon_productidtype] =&gt; 1
          [amazon_itemcondition] =&gt; 1
          [amazon_asin1] =&gt; 1
          [scom_liid] =&gt; 1
          [scom_ldsku] =&gt; 1
          [product_code] =&gt; 1
          [category_codes] =&gt; 1
        )
      [useexpr] =&gt; Array
        (
          [catsubcat] =&gt; 1
        )
      [exprcol] =&gt; Array
        (
          [catsubcat] =&gt; catsubcat
        )
      [exprval] =&gt; Array
        (
          [catsubcat] =&gt; %catsubcat%
        )
      [additionalexpr] =&gt; Array
        (
        )
      [additionalexprval] =&gt; Array
        (
        )
      [additionalexprdisp] =&gt; Array
        (
        )
      [filter] =&gt; ID BETWEEN 1 AND 3645
    )
)
</pre>	
	</div>


	<br />
	<input name="mode" type="hidden" id="mode" value="updateProfile" />
	<input name="insertMode" type="hidden" value="insertProfile" />
	<input name="updateMode" type="hidden" value="updateProfile" />
	<input name="deleteMode" type="hidden" value="deleteProfile" />
	<input name="submode" type="hidden" id="submode" value="" />
	<input name="version" type="hidden" id="version" value="1.0" />
	<input name="Type" type="hidden" id="Type" value="Export" />
	<input name="Tables_ID" type="hidden" id="Tables_ID" value="1" />
	<input name="ID" type="hidden" id="ID" value="2" />
	<input name="suppressPrintEnv" type="hidden" id="suppressPrintEnv" value="" />
	</form>
	</td>
  </tr>
</table>

</div>
<div id="footer">
&nbsp;&nbsp;
</div>
<div id="showTester" title="Javascript Tester" onClick="g('tester').style.display='block';">&nbsp;</div>
<div id="tester" >
	<a href="#" onClick="g('ctrlSection').style.display='block';return false;">Show Control Section</a><br />
	<textarea name="test" cols="65" rows="4" id="test">clear_form();</textarea><br />
	<input type="button" name="button" value="Test" onClick="jsEval(g('test').value);"><br />
	<textarea id="result" name="result" cols="65" rows="3" ></textarea>
</div>
<div id="ctrlSection" style="display:none">
	<iframe name="w1" src="/Library/js/blank.htm"></iframe>
	<iframe name="w2" src="/Library/js/blank.htm"></iframe>
	<iframe name="w3" src="/Library/js/blank.htm"></iframe>
	<iframe name="w4" src="/Library/js/blank.htm"></iframe>
</div>
</body>
</html>