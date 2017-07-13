<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');


if($Units_ID)$Properties_ID=q("SELECT Properties_ID FROM gl_properties_units WHERE ID=$Units_ID", O_VALUE);
if(!in_array($Properties_ID, ($myProperties=list_properties())))exit('You do not have access to this property');

//------------------------ Navbuttons head coding v1.43 -----------------------------
//change these first vars and the queries for each instance
$object='Properties_ID';
$recordPKField='ID'; //primary key field
$navObject='Properties_ID';
$updateMode='updateProperty';
$insertMode='insertProperty';
$deleteMode='deleteProperty';
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
$ids=q("SELECT ID FROM gl_properties WHERE ResourceType IS NOT NULL AND ID IN(".implode(',',$myProperties).") ORDER BY PropertyName",O_COL);
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
if(strlen($$object)){
	//get the record for the object
	if($units=q("SELECT a.* FROM _v_properties_master_list a WHERE Properties_ID=$Properties_ID",O_ARRAY/*, O_TEST, O_TEST_CNX*/)){
		$mode=$updateMode;
		@extract($units[1]);
		
		/* important, redirect to correct page */
		if(false && strtolower($Type)!=='apt'){
			header('Location: ' .str_replace('properties3','properties2',$_SERVER['PHP_SELF']).($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
			exit;
		}
	}else{
		mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='Insert mode not allowed for this page'),$fromHdrBugs);
		exit('Insert mode not allowed for this page');
	}
}else{
	mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='Insert mode not allowed for this page'),$fromHdrBugs);
	exit('Insert mode not allowed for this page');
}
//--------------------------- end coding --------------------------------


$hideCtrlSection=false;



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo ($PageTitle='Client Property Management - '.$AcctCompanyName);?></title>



<link rel="stylesheet" type="text/css" href="/Library/css/cssreset01.css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/site-local/forms_suite.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';
var isEscapable=0;
var isDeletable=0;
var isModal=0;
var talks=0; //whether this page broadcasts overall state changes which other pages listen for
var listens=0;
</script>


</head>

<body id="properties">
<form id="form1" name="form1" target="w2" method="post" action="resources/bais_01_exe.php" onsubmit="return beginSubmit();">
<div id="header">
<div id="headerBar1">
<h3>Property: <?php echo $PropertyName;?></h3>
<p>
Currently signed in: <strong><?php echo $_SESSION['admin']['firstName'] . ' '. $_SESSION['admin']['lastName']?></strong>
</p>

	
	
</div>

</div>
<div id="mainBody">
<div class="suite1">

<div class="fl" style="width:38%;border:1px dotted #333;">
	<h3>Client Information</h3>
	<p class="gray">this is blah blah blah</p>
	<div>
		Client Name: <strong><?php echo $ClientName;?></strong><br />
		First name: 
		<input class="th1" name="PrimaryFirstName" type="text" id="PrimaryFirstName" value="<?php echo h($PrimaryFirstName);?>" size="12" maxlength="35" onchange="dChge(this);" />
		<br />
		Middle name: 
		<input class="th1" name="PrimaryMiddleName" type="text" id="PrimaryMiddleName" value="<?php echo h($PrimaryMiddleName);?>" size="16" maxlength="35" onchange="dChge(this);" />
		<br />
		Last name: 
		<input class="th1" name="PrimaryLastName" type="text" id="PrimaryLastName" value="<?php echo h($PrimaryLastName);?>" size="16" maxlength="35" onchange="dChge(this);" />
		<br />
		Phone: 
		<input class="th1" name="Phone" type="text" id="Phone" value="<?php echo h($Phone);?>" onchange="dChge(this);" />
		<br />
		Second phone (list toll free here): 
		<input class="th1" name="Phone2" type="text" id="Phone2" value="<?php echo h($Phone2);?>" onchange="dChge(this);" />
		<br />
		Fax: <input class="th1" name="Fax" type="text" id="Fax" value="<?php echo h($Fax);?>" onchange="dChge(this);" />
		<br />
		Email: <input class="th1" name="Email" type="text" id="Email" value="<?php echo h($Email);?>" onchange="dChge(this);" />
		<br />	
	</div>
</div>
	<div class="fr" style="width:47%;border:1px dotted #333;">
	<h3>Property Information</h3>
	<p class="gray">this is blah blah blah</p>
	<div>
		<div class="fl">Address:</div>
		<div class="fl">
		<?php echo $PropertyAddress;?><br />
		<?php echo $PropertyCity . ', ' . $PropertyState. '  '.$PropertyZip;?><br />
		</div>
		<div class="cb"> </div>
		Contact on Site:<br />
		<input name="PropertyContact" type="text" class="th1" id="PropertyContact" onchange="dChge(this);" value="<?php echo h($PropertyContact);?>" size="35" maxlength="255" /><br />
		<div class="cb"> </div>
		<div class="fl">Current office hours: </div>
		<div class="fl"><textarea name="OfficeHours" id="OfficeHours" rows="3" cols="40" onchange="dChge(this);" class="th1"><?php echo h($OfficeHours);?></textarea></div>
		<div class="cb"> </div>
		Send Commission:
		<select class="th1" name="SendCommission" id="SendCommission" onfocus="buffer=this.value;" onchange="dChge(this); dollarAmt(this,1,'SendCommission');">
		<option value="">&lt;Select..&gt;</option>
		<?php
		for($i=.20; $i<=1.25; $i+=.05){
			?><option value="<?php echo $i?>" <?php echo abs($SendCommission-$i)<.001 || ($mode==$insertMode && $i==.5)?'selected':''?>><?php echo round($i*100).'%'?></option>
			<?php
		}
		if($SendCommission >=1 && $SendCommission<=2){
			?><option value="<?php echo $SendCommission?>" selected="selected"><?php echo round($SendCommission*100,0).'%';?></option><?php
		}
		?>
		<option value="{RBADDNEW}" <?php if($SendCommission>2)echo 'selected';?>>Specific $ amount..</option>
		</select>
		&nbsp;&nbsp;
		<input name="SendCommission_RBADDNEW" type="text" class="th1" id="SendCommission_RBADDNEW" value="<?php if($SendCommission>2)echo number_format($SendCommission,2);?>" size="7" style="visibility:<?php echo $SendCommission>2 ? 'visible':'hidden'?>" />
		<br />
		Escort Commission:
		<select class="th1" name="EscortCommission" id="EscortCommission" onfocus="buffer=this.value;" onchange="dChge(this); dollarAmt(this,1,'EscortCommission');">
			<option value="">&lt;Select..&gt;</option>
			<?php
			for($i=.20; $i<=1.25; $i+=.05){
				?><option value="<?php echo $i?>" <?php echo abs($EscortCommission-$i)<.001 || ($mode==$insertMode && $i==.5)?'selected':''?>><?php echo round($i*100).'%'?></option><?php
			}
			if($EscortCommission >=1 && $EscortCommission<=2){
				?><option value="<?php echo $EscortCommission?>" selected="selected"><?php echo round($EscortCommission*100,0).'%';?></option><?php
			}
			?>
			<option value="{RBADDNEW}" <?php if($EscortCommission>2)echo 'selected';?>>Specific $ amount..</option>
		</select>
		&nbsp;&nbsp;
		<input name="EscortCommission_RBADDNEW" type="text" class="th1" id="EscortCommission_RBADDNEW" value="<?php if($EscortCommission>2)echo number_format($EscortCommission,2);?>" size="7" style="visibility:<?php echo $SendCommission>2 ? 'visible':'hidden'?>" />
		<br />
		Lease term (months):
		<select class="th1" name="LeaseDesired" id="LeaseDesired" style="width:125px;" onchange="dChge(this);">
		  <option value="">&lt;Select..&gt;</option>
		  <option value="1" <?php echo $LeaseDesired==1?'selected':''?>>month-to-month</option>
		  <option value="2" <?php echo $LeaseDesired==2?'selected':''?>>2 mo.</option>
		  <option value="3" <?php echo $LeaseDesired==3?'selected':''?>>3 mo.</option>
		  <option value="4" <?php echo $LeaseDesired==4?'selected':''?>>4 mo.</option>
		  <option value="5" <?php echo $LeaseDesired==5?'selected':''?>>5 mo.</option>
		  <option style="background-color:silver;" value="6" <?php echo $LeaseDesired==6?'selected':''?>>6 months</option>
		  <option value="7" <?php echo $LeaseDesired==7?'selected':''?>>7 mo.</option>
		  <option value="8" <?php echo $LeaseDesired==8?'selected':''?>>8 mo.</option>
		  <option value="9" <?php echo $LeaseDesired==9?'selected':''?>>9 mo.</option>
		  <option value="10" <?php echo $LeaseDesired==10?'selected':''?>>10 mo.</option>
		  <option value="11" <?php echo $LeaseDesired==11?'selected':''?>>11 mo.</option>
		  <option style="background-color:silver;" value="12" <?php echo $LeaseDesired==12?'selected':''?>>One year</option>
		  <option value="13" <?php echo $LeaseDesired==13?'selected':''?>>13 mo.</option>
		  <option value="14" <?php echo $LeaseDesired==14?'selected':''?>>14 mo.</option>
		  <option value="15" <?php echo $LeaseDesired==15?'selected':''?>>15 mo.</option>
		  <option value="16" <?php echo $LeaseDesired==16?'selected':''?>>16 mo.</option>
		  <option value="17" <?php echo $LeaseDesired==17?'selected':''?>>17 mo.</option>
		  <option value="18" <?php echo $LeaseDesired==18?'selected':''?>>18 mo.</option>
		</select>
		<br />
		Will accept lease term for:
		<select class="th1" name="LeaseAllowed" id="LeaseAllowed" style="width:125px;" onchange="dChge(this);">
		  <option value="">&lt;Select..&gt;</option>
		  <option value="1" <?php echo $LeaseAllowed==1?'selected':''?>>month-to-month</option>
		  <option value="2" <?php echo $LeaseAllowed==2?'selected':''?>>2 mo.</option>
		  <option value="3" <?php echo $LeaseAllowed==3?'selected':''?>>3 mo.</option>
		  <option value="4" <?php echo $LeaseAllowed==4?'selected':''?>>4 mo.</option>
		  <option value="5" <?php echo $LeaseAllowed==5?'selected':''?>>5 mo.</option>
		  <option style="background-color:silver;" value="6" <?php echo $LeaseAllowed==6?'selected':''?>>6 months</option>
		  <option value="7" <?php echo $LeaseAllowed==7?'selected':''?>>7 mo.</option>
		  <option value="8" <?php echo $LeaseAllowed==8?'selected':''?>>8 mo.</option>
		  <option value="9" <?php echo $LeaseAllowed==9?'selected':''?>>9 mo.</option>
		  <option value="10" <?php echo $LeaseAllowed==10?'selected':''?>>10 mo.</option>
		  <option value="11" <?php echo $LeaseAllowed==11?'selected':''?>>11 mo.</option>
		  <option style="background-color:silver;" value="12" <?php echo $LeaseAllowed==12?'selected':''?>>One year</option>
		  <option value="13" <?php echo $LeaseAllowed==13?'selected':''?>>13 mo.</option>
		  <option value="14" <?php echo $LeaseAllowed==14?'selected':''?>>14 mo.</option>
		  <option value="15" <?php echo $LeaseAllowed==15?'selected':''?>>15 mo.</option>
		  <option value="16" <?php echo $LeaseAllowed==16?'selected':''?>>16 mo.</option>
		  <option value="17" <?php echo $LeaseAllowed==17?'selected':''?>>17 mo.</option>
		  <option value="18" <?php echo $LeaseAllowed==18?'selected':''?>>18 mo.</option>
		</select>
	</div>
	
	</div>
</div>
<div class="cb"> </div>
<div>
<?php 
if(count($units)==1){
	
}else{
	//tabs and the two modification objects whoo hoooo!
	?>
	<style type="text/css">
	#topNav li{
		list-style:none;
		float:left;
		padding:5px 10px;
		}
	#topNav li:hover, #topNav li.active{
		background-color:aliceblue;
		}
	#topNav li{
		border-bottom:1px solid #000;
		cursor:pointer;
		}
	#topNav li.active{
		border:1px solid #000;
		border-bottom:none;
		cursor:auto;
		}
	#topNav li{
		font-size:109%;
		/*
		font-weight:900;
		letter-spacing:0.02em;
		*/
		}
	#topNav li a{
		text-decoration:none;
		color:#000;
		}
	
	</style>
	<script language="javascript" type="text/javascript">
	function tabOpt(o){
		g('_costsUtilities').style.display=(o.id=='costsUtilities'?'block':'none');
		g('_amenities').style.display=(o.id=='amenities'?'block':'none');
		g('costsUtilities').className='';
		g('amenities').className='';
		o.className='active';
	}
	
	</script>
	<ul id="topNav">
		<li id="costsUtilities" onclick="tabOpt(this);" class="active">Costs and Utilities</li>
		<li id="amenities" onclick="tabOpt(this);" class="">Features</li>
	</ul>
	<div class="cb"> </div>
	<br />

	<div id="_costsUtilities" style="display:block;">
		<?php require('components/comp_900_allunitsinfo.php');?>
	</div>
	<div id="_amenities" style="display:none;">
		here is table 2
	</div>
	
	<?php
}
?>
</div>


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