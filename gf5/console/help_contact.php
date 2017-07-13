<?php
/*
2012-05-03: so this was taken from sim plefo stercare; but it was really not usable there.  here are the steps to making it usable:

1. as long as I get the email and it's understandable, there is some use to this
2. then I need a linkback to this so I can add administrative features
3. I need to be able to categorize this and tie into/use as a FAQ (generically for any platform I undergo)
4. I need to close these out and be able to reply to the person
5. need to list these so others can see them.

NOTE: when I am coming from an L0 page, this page can redirect it.  but not for an L1 page.
	

*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');



//------------------------ Navbuttons head coding v1.43 -----------------------------
//change these first vars and the queries for each instance
$object='Tickets_ID';
$recordPKField='ID'; //primary key field
$navObject='Tickets_ID';
$updateMode='updateSupportContact';
$insertMode='insertSupportContact';
$deleteMode='deleteSupportContact';
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
$ids=q("SELECT * FROM cpm180.gf_supporttickets ORDER BY Status, StatusDate",O_COL);

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
	if($a=q("SELECT * FROM cpm180.gf_supporttickets WHERE ID='".$$object."'",O_ROW)){
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
	$nullAbs=$nullCount+1; //where we actually are right then
}
//--------------------------- end coding --------------------------------

$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $Title ? h($Title) : 'Help or Support Request'?></title>
<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />

<style>
body{
	background-color:snow;
	}
.largerTreb{
	font-size:136%;
	font-weight:800
}
fieldset{
	margin-left:10px;
	padding:20px 20px 5px 10px;
}
#formContent{
	padding:15px 15px 20px 25px;
}
.ghost{
	color:#CCC;
}
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle .02 coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
var ResourceToken='<?php echo $ResourceToken;?>';
var Assns_ID='<?php echo $Assns_ID?>';

//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';
var pagekey='<?php echo $ID?>';
</script>
</head>
<body <?php if($UserAgentType=='Mobile')echo 'class="mobile"';?>>
	<form name="form1" action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" target="w2">
		<div id="headerBar1" style="background-color:DARKRED;">



		<div style="float:right;"><?php
		//Added to this page 2005-11-03
		//Things to do to install this button set:
		#1. need js functions focus_nav() and focus_nav_cxl()
		#2. change $mode target as necessary but leave the 'insert':'update' in place
		#3. nullAbs and nullCount need some type of value for updating and navigating existing records
		?>
		<table border="0" cellspacing="0" cellpadding="0">
			<tr valign="top">
				<td><input type="button" name="Submit" value="Previous" id="Previous" onclick="focus_nav(-1, '<?php echo ($mode==$insertMode?'insert':'update')?>', <?php echo $mode==$insertMode?1:0?>, 0<?php echo $navQueryFunction ?", '".$navQueryFunction . "'" :'';?>);" <?php echo $nullAbs<=1?'disabled':''?> >
				</td>

				<td><?php
				//Handle display of all buttons besides the Previous button
				if($mode==$insertMode){
					if(true){
						/***
						NOTE: AS OF 2005-09-15, 'rfnw' or multi-window mode is the only mode I'm supporting.  Below is code for single window mode if used in the future - need to bring in line with focus_nav() vs. pan_nav()
						***/
						//I am in multi window focus mode - save and new, save and close
						?>
						<input id="SaveAndNew" type="button" name="SaveAndNew" value="Save &amp; New" onclick="focus_nav(0,'insert', 1,1<?php echo $navQueryFunction ?", '".$navQueryFunction . "'" :'';?>);">
						<input id="SaveAndClose" type="button" name="SaveAndClose" value="Save &amp; Close" onclick="focus_nav(0,'insert', 1,0<?php echo $navQueryFunction ?", '".$navQueryFunction . "'" :'';?>);">
						<input id="CancelInsert" type="button" name="CancelInsert" value="Cancel" onclick="focus_nav_cxl('insert');"><?php
					}else{
						/************/
					}
				}else{
					//OK, and appropriate [next] button
					?><input id="ActionOK" type="button" name="ActionOK" value="OK" onclick="focus_nav(0,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ?", '".$navQueryFunction . "'" :'';?>);">
					<input id="Next" type="button" name="Submit" value="Next" onclick="focus_nav(1,'<?php echo $mode==$insertMode?'insert':'update'?>',0,0<?php echo $navQueryFunction ?", '".$navQueryFunction . "'" :'';?>);" <?php echo ($nullAbs>$nullCount?'disabled':'')?>><?php
				}
				// *note that we could go back to the same page the 'New Record' click appeared on, but there's major issues programmatically on whether it would shift becuase of the placement of the new record.
				?>
				</td>
			</tr>
		</table>
		</div>
		�
		<input name="ID" type="hidden" id="ID" value="<?php echo $ID?>">
		<input name="navVer" type="hidden" id="navVer" value="<?php echo $navVer?>">
		<input name="nav" type="hidden" id="nav" value="">
		<input name="navMode" type="hidden" id="navMode" value="">
		<input name="navObject" type="hidden" id="navObject" value="<?php echo $object?>">
		<input name="navQueryFunction" type="hidden" id="navQueryFunction" value="<?php echo $navQueryFunction?>">
		<input name="mode" type="hidden" id="mode" value="<?php echo $mode?>">
		<input name="insertMode" type="hidden" id="insertMode" value="<?php echo $insertMode?>">
		<input name="updateMode" type="hidden" id="updateMode" value="<?php echo $updateMode?>">
		<input name="count" type="hidden" id="count" value="<?php echo $nullCount?>">
		<input name="abs" type="hidden" id="abs" value="<?php echo $nullAbs?>">
		<input name="componentID" type="hidden" id="componentID" value="<?php echo $localSys['componentID']?>">
		<input name="ctime" type="hidden" id="ctime" value="<?php if($ctime)$ctime=time(); echo $ctime; ?>">
		Currently signed in: <strong><?php echo htmlentities($_SESSION['admin']['firstName'] . ' ' . $_SESSION['admin']['lastName'])?></strong>
		</div>
	  <div id="formContent">
			<h2>Help or Support Request </h2>
		  <p>
			This is a 
			<select name="RequestType" id="RequestType" onchange="dChge(this);">
			  <option value="">&lt;Select..&gt;</option>
			  <option value="How-to request" <?php echo $RequestType=='How-to request'?'selected':''?>>How-to request</option>
			  <option value="Feature Request" <?php echo $RequestType=='Feature Request'?'selected':''?>>Feature Request</option>
			  <option value="Bug Report" <?php echo $RequestType=='Bug Report'?'selected':''?>>Bug Report</option>
			  <option value="Security issue report" <?php echo $RequestType=='Security issue report'?'selected':''?>>Security issue report</option>
		    </select>
			<br />
			<br />Summary of the request/problem<br /> 
			  <input name="Summary" type="text" id="Summary" value="<?php echo $Summary?>" size="65" onchange="dChge(this);" />
			  <br />
			  Describe in detail <br />
			  <textarea name="Details" cols="70" rows="5" id="Details" onchange="dChge(this);"><?php echo $Details;?></textarea>
			  <br />
			  <br />
			  <?php $Notify=explode(',',$Notify);?>
			  Please direct this support request to: (
              <input name="Notify[]" type="checkbox" id="Notify[]" value="Best Match" <?php echo $mode==$insertMode || (in_array('Best Match',$Notify))?'checked':''?> onchange="dChge(this);" />
best match) <br />
<label>
<input name="Notify[]" type="checkbox" id="Notify[]" value="Developer" <?php echo in_array('Developer',$Notify)?'checked':''?> onchange="dChge(this);" />
Programmer&nbsp;&nbsp;&nbsp; </label>
<label>
<input name="Notify[]" type="checkbox" id="Notify[]" value="Staff" <?php echo in_array('Staff',$Notify)?'checked':''?> onchange="dChge(this);" />
Office Manager </label>
<br />
 Notify at this email also:
 <input name="NotifyEmail" type="text" id="NotifyEmail" value="<?php echo h($NotifyEmail);?>" onchange="dChge(this);" />
<br />
<br />
Best phone number to reach you if necessary:
<input name="ContactPhone" type="text" id="ContactPhone" value="<?php echo h($ContactPhone ? $ContactPhone : $_SESSION['admin']['contact_phones']);?>" onchange="dChge(this);" />
<br />
Best time to reach you:
<input name="ContactTime" type="text" id="ContactTime" value="<?php echo h($ContactTime);?>" onchange="dChge(this);" />
<br />
</p>
<?php if($mode==$updateMode){ ?>

<p> Status:
<?php if(minroles()<ROLE_MANAGER){ ?>
	Status: <select name="Status" id="Status" onchange="dChge(this);if(this.value!==g('OriginalStatus').value)g('Update').checked=true;">
		<option value="Submitted" <?php echo $Status=='Submitted'?'selected':''?>>Submitted</option>
		<option value="Deleted" <?php echo $Status=='Deleted'?'selected':''?>>Deleted</option>
		<option value="In Progress" <?php echo $Status=='In Progress'?'selected':''?>>In Progress</option>
		<option value="Solved" <?php echo $Status=='Solved'?'selected':''?>>Solved</option>
		<option value="Published" <?php echo $Status=='Published'?'selected':''?>>Published</option>
	</select>
	<input type="hidden" id="OriginalStatus" name="OriginalStatus" value="<?php echo $Status;?>" />
&nbsp;&nbsp;
<label>
<input name="Update" type="checkbox" id="Update" value="1" onchange="dChge(this);" />
Notify	subscriber(s) of updates</label>
<br />
	Resolution/solution:<br />
	<textarea name="Resolution" cols="55" rows="4" id="Resolution" onchange="dChge(this);"><?php echo h($Resolution);?></textarea><br />
	<?php if(minroles()<ROLE_ADMIN){ ?>
	Comments (internal):<br />
	<textarea name="Comments" cols="55" rows="2" id="Comments" onchange="dChge(this);"><?php echo h($Comments);?></textarea><br />
	<?php }?>
<?php }else{ ?>

	Status: <strong><?php echo $Status;?></strong>
	Resolution:<br />
	<div id="Resolution">
	<?php echo $Resolution;?>
	</div>
	<p class="gray"><?php if(minroles()<ROLE_ADMIN)echo $Comments;?></p>

<?php } ?><br />
</p>
<?php } ?>
		  <p>
		    <br />
		    <input name="Submit" type="submit" id="Submit" value="Submit" />
  &nbsp;&nbsp;
  <input type="button" name="Submit2" value="Cancel" onclick="window.close()" />
	      </p>
		  <?php if($mode==$insertMode){ ?>
		<p><strong>You came from: <?php echo end(explode('/',$GLOBALS['HTTP_REFERER']))?>
		</strong></p>
		<?php }else{ ?>
		<p>Request came from: <?php echo end(explode('/',$Referer))?><br />
		<?php } ?>
		<input name="Referer" type="hidden" id="Referer" value="<?php echo $GLOBALS['HTTP_REFERER']?>">
	  </div>
	<br />
	<br />
	<br />
	</form>
	<div style="background-color:#<?php echo '000'?>;width:5px;height:5px;" onclick="document.getElementById('tester').style.display='block';this.style.backgroundColor='#FFF';">
	<div id="tester" style="display:none;background-color:SILVER;border:1px solid #000;padding:5px;width:75%">
	<a href="#" onclick="document.getElementById('ctrlSection').style.display='block';return false;">Show Control Section</a><textarea name="test" cols="65" rows="4" id="test">clear_form();</textarea>
	<br /><input type="submit" name="Submit" value="Test" onclick="jsEval(g('test').value);">
	<br /><textarea name="result" cols="65" rows="3" id="result"></textarea>
	</div>
	</div>
	<div id="ctrlSection" style="display:<?php echo $testModeC?'block':'none'?>;"> 
		<iframe name="w0"></iframe>
		<iframe name="w1"></iframe>
		<iframe name="w2"></iframe>
		<iframe name="w3"></iframe>
</div>
</body>
</html><?php page_end()?>