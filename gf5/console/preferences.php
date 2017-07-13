<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='focusview';
$localSys['pageType']='Properties Window';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

$hideCtrlSection=false;

//--------------------------------------------------
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $AcctCompanyName?> - My Preferences</title>



<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style>
/** CSS Declarations for this page **/
.data1 td{
	background-color:#d0dbe6;
	}
.data1 thead{
	background-color:darkgreen;
	}
.data1 a{
	color:DARKRED;
	}
.data1 th, .data1 th a{
	color:#FFF;
	font-size:119%;
	font-weight:400;
	}

.leftMain{
	background-color:papayawhip;
	padding:5px;
	margin-top:-5px;
	width:115px;
	}
.sendChain, .sendSpecific, .sendTemplate{
	padding:5px;
	margin-bottom:15px;
	background-color:#eee;
	clear:both;
	}
.sendObject{
	margin-bottom:30px;
	}
.permTable th{
	background-color:navajowhite;
	border-bottom:1px solid #000;
	font-weight:400;
	text-align:center;
	padding:2px 4px 1px 7px;
	}
.permTable td{
	border:1px solid #777;
	text-align:center;
	padding:2px 4px 1px 7px;
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
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';
function alerttitle(o){
	alert(o.getAttribute('title'));
}
function refreshList(){
	window.location+='';
}
<?php 
//js var user settings
js_userSettings();
?>

</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<?php 
$link='/site-local/gl_extension_'.$GCUserName.'.css';
if(file_exists($_SERVER['DOCUMENT_ROOT'].$link)){ ?>
<link id="cssExtension" rel="stylesheet" type="text/css" href="<?php echo $link?>" />
<?php } ?>
</head>

<body>
<div id="mainWrap">
	<?php require($_SERVER['DOCUMENT_ROOT'].'/gf5/console/gf_header_login_002.php');?>
	
	<div id="mainBody">
	

<h3>My Preferences </h3>
<?php
if(false){
	
}else{
	if(true){
		?><form name="form1" method="post" action="resources/bais_01_exe.php" target="w2">
		<div class="fr">
			<input type="submit" name="Submit" value="Update Preferences" />
		</div>
		<?php
		if(minroles()<=ROLE_DBADMIN){
			if($a=q("SELECT varnode, varkey, varvalue FROM bais_settings WHERE UserName='".sun()."' AND vargroup='settings.agent'", O_ARRAY)){
				foreach($a as $n=>$v)$agentSettings[$v['varnode']][$v['varkey']]=$v['varvalue'];
				extract($agentSettings);
			}
			//DATABASE ADMIN FEATURES
			?><fieldset><legend>Admin Settings</legend>
			<!--
			<?PHP if(false){ ?>
			<h3>Search Options</h3>
			By default, 
			<select name="search[hideCompChart]" id="search[hideCompChart]" onchange="dChge(this);">
				<option value="0">show</option>
				<option value="1" <?php echo $search['hideCompChart']==1?'selected':''?>>don't show</option>
			</select> the comparison chart on search detail<br />
	
			By default, 
			<select name="search[hideCosts]" id="search[hideCosts]" onchange="dChge(this);">
				<option value="0">show</option>
				<option value="1" <?php echo $search['hideCosts']==1?'selected':''?>>don't show</option>
			</select> the cost comparisons on the chart<br />
	
			<div class="fl" style="width:200px;">
			My default search citi(es) are:<br />
			<span class="gray">Hold down the ctrl key to select multiple cities</span>
			</div>
			<div class="fl">
			<?php
			if($a=$search['defaultCities']){
				$PropertyCity=explode(',',$a);
			}else{
				$PropertyCity=array();
			}
			
			?>	 
			<select name="search[defaultCities][]" size="10" multiple="multiple" id="search[defaultCities]" onchange="dChge(this);">
				<option value="" style="font-style:italic;">(no preference)</option>
				<?php
				$Cities=q("SELECT DISTINCT PropertyCity FROM _v_properties_master_list GROUP BY PropertyCity ORDER BY IF(PropertyCity IN('".implode("','",$localCities)."'),1,2), IF(PropertyCity='San Marcos',1,2), PropertyCity", O_COL);
				foreach($Cities as $v){
				?><option value="<?php echo h($v);?>" <?php if(@in_array($v, $PropertyCity) || (empty($PropertyCity) && strtolower($v)=='san marcos'))echo 'selected';?>><?php echo h($v);?></option><?php
				}
				?>
			</select>
			</div>
			<?php } ?>
			-->
			<div class="cb"> </div>
			<h3>Bulletin Options</h3>
			<p class="gray">Bulletins are the communication system Home Base uses to send messages between staff, agents and even Owners/Property Management Companies. You can control what level of importance you want to receive on bulletins by this setting</p>
			Email me bulletins that are 
			<select name="bulletins[importance]" id="bulletins[importance]" onchange="dChge(this);">
			  <option value="Normal" <?php echo $bulletins['importance']=='Normal'?'selected':''?>>Normal</option>
			  <option value="High" <?php echo $bulletins['importance']=='High'?'selected':''?>>High</option>
			  <option value="Critical" <?php echo $bulletins['importance']=='Critical'?'selected':''?>>Critical</option>
			</select> or greater in importance.<br />
			<?php
			//DATABASE ADMIN FEATURES
			if(count($adminSettings[ROLE_DBADMIN]['sendObjects']))
			foreach($adminSettings[ROLE_DBADMIN]['sendObjects'] as $sendObject=>$v){
				?>
				<div id="sendObject_<?php echo $sendObject?>" class="sendObject">
					<h3 class="fl nullTop nullBottom" style="font-weight:bold;"><?php echo $v['label']?></h3><p class="gray" style="margin-top:7px;"> - notification emails sent out when <?php echo $v['when']?></p>
					<div class="sendChain">
						<div class="fl leftMain">
						<strong>General notices:</strong>						</div>
						<input name="_adminSettings[<?php echo ROLE_DBADMIN;?>][sendObjects][<?php echo $sendObject?>][notifyOfficeLevel]" type="hidden" value="0" />
						<label>
						<input name="_adminSettings[<?php echo ROLE_DBADMIN;?>][sendObjects][<?php echo $sendObject?>][notifyOfficeLevel]" type="checkbox" value="1" id="_adminSettings[<?php echo ROLE_DBADMIN;?>][sendObjects][<?php echo $sendObject?>][notifyOfficeLevel]" <?php echo $v['notifyOfficeLevel'] ? 'checked' : '';?> onchange="dChge(this);" />
						 Notify all in office with these responsibilities: </label>
						<div class="fr">
						  <table class="permTable">
							<thead>
							  <tr>
							  <!-- for Simple fostercare 
								<th scope="col">Med./Dental</th>
								<th scope="col">Ther./Clinical</th>
								-->
								<th scope="col">Admin.</th>
								<th scope="col">Finan./Billing</th>
								<!--
								<th scope="col">Clerical</th>
								-->
							  </tr>
							</thead>
							<tbody>
							  <tr>
							  <!--
								<td><?php echo $v['permissions'] & PERM_MEDICAL ? 
							'<img src="/images/i/check1.png" width="16" height="16" alt="Yes" />' : '&nbsp;';?></td>
								<td><?php echo $v['permissions'] & PERM_CLINICAL ? 
							'<img src="/images/i/check1.png" width="16" height="16" alt="Yes" />' : '&nbsp;';?></td>
								-->
								<td><?php echo $v['permissions'] & PERM_ADMINISTRATIVE ? 
							'<img src="/images/i/check1.png" width="16" height="16" alt="Yes" />' : '&nbsp;';?></td>
								<td><?php echo $v['permissions'] & PERM_FINANCIAL ? 
							'<img src="/images/i/check1.png" width="16" height="16" alt="Yes" />' : '&nbsp;';?></td>
								<!--
								<td><?php echo $v['permissions'] & PERM_CLERICAL ? 
							'<img src="/images/i/check1.png" width="16" height="16" alt="Yes" />' : '&nbsp;';?></td>
								-->
							  </tr>
							</tbody>
						  </table>
					  </div>
						<br />
						<input name="_adminSettings[<?php echo ROLE_DBADMIN;?>][sendObjects][<?php echo $sendObject?>][notifyUpOneLevel]" type="hidden" value="0" />
						 <label>
						<input name="_adminSettings[<?php echo ROLE_DBADMIN;?>][sendObjects][<?php echo $sendObject?>][notifyUpOneLevel]" type="checkbox" value="1" id="_adminSettings[<?php echo ROLE_DBADMIN;?>][sendObjects][<?php echo $sendObject?>][notifyUpOneLevel]" <?php echo $v['notifyUpOneLevel'] ? 'checked' : '';?> onchange="dChge(this);" />
						 Go up one level</label><br />
						<div class="cb"> </div>
					</div>
					<div class="sendSpecific">
						<div class="fl leftMain">
						<strong>Specific notices:</strong>						</div>
						<div class="fl">Also send emails to: </div>
						<div class="fl">
						<select name="_adminSettings[<?php echo ROLE_DBADMIN;?>][sendObjects][<?php echo $sendObject?>][specificSends][]" multiple="multiple" size="7" id="_adminSettings_<?php echo ROLE_DBADMIN;?>_sendObjects_<?php echo $sendObject?>" onchange="dChge(this);newOption(this, 'directors.php', 'l1_staff', '800,700');" cbtable="bais_staff">
						<option value="">&lt; None &gt;</option>
						<?php
						
						
						$mailStaffList=q(
						/* include staff grandfathered and with permission overlap; note sort brings selected to top */
						"SELECT
						un_username, un_firstname, un_lastname, un_email, IF(so_permissions IS NOT NULL, so_permissions, sr_permissions) AS permissions
						FROM 
						bais_universal u 
						LEFT JOIN bais_staff s ON u.un_username=s.st_unusername
						/* first option: staff in an office = area or regional */
						LEFT JOIN gf_StaffOffices so ON s.st_unusername=so.so_stusername AND (so.so_permissions & ".$v['permissions'].")>0
						/* second option: senior */
						LEFT JOIN bais_StaffRoles sr ON s.st_unusername=sr.sr_stusername AND (sr.sr_permissions & ".$v['permissions'].")>0
						WHERE 
						st_active=1 
						AND 1 /*
						(so.so_stusername IS NOT NULL OR sr.sr_stusername IS NOT NULL OR un_username IN('".str_replace('bais_staff:','',implode("','",$v['specificSends']))."')) */
						GROUP BY u.un_username					
						ORDER BY 
						IF(un_username IN('".str_replace('bais_staff:','',implode("','",$v['specificSends']))."'),1,2),
						un_lastname, 
						un_firstname", O_ARRAY_ASSOC);
						
						$mailStaffList=q("SELECT
						un_username, u.* FROM bais_universal u ORDER BY 
						IF(un_username IN('".str_replace('bais_staff:','',implode("','",$v['specificSends']))."'),1,2),
						un_lastname, 
						un_firstname", O_ARRAY_ASSOC);
						
						if($mailStaffList)foreach($mailStaffList as $o=>$w){
							?><option value="bais_staff:<?php echo $o?>" <?php 
							if(in_array('bais_staff:'.$o, $v['specificSends']))echo 'selected';
							?>><?php echo h($w['un_lastname'] . ', '.$w['un_firstname']. ' ('.($w['un_email']?$w['un_email']:'no email').', username='.$o.')');?></option><?php
						}
						if(minroles()<=ROLE_FOUNDATION_DIRECTOR){
							?><option value="{RBADDNEW}" style="background-color:thistle;">&lt; Add staff.. &gt;</option><?php
						}
						?>
						</select>
						</div>
						<div class="fl" style="width:200px;">
						NOTE: Only staff with at least one of the responsibilities shown above are listed here.  <a href="pd_offices_new.php">Click here to modify staff permissions</a>.					</div>
						<div class="cb"> </div>
					</div>
					<div class="sendTemplate">
						<div class="fl leftMain">email template: </div>
						<select name="_adminSettings[<?php echo ROLE_DBADMIN;?>][sendObjects][<?php echo $sendObject?>][template]" id="_adminSettings_<?php echo ROLE_DBADMIN;?>_sendObjects_<?php echo $sendObject?>_template">
						<option value="">&lt; Select.. &gt;</option>
						<?php
						foreach($adminSettings[ROLE_DBADMIN]['registeredTemplates'] as $o=>$w){
							?><option value="<?php echo $o?>" <?php echo $v['template']==$o?'selected':''?>><?php echo h($o . ' ('.$w['file'].')')?></option><?php
						}
						?>
						</select>
					
						<div class="cb"> </div>
					</div>
				</div><?php
			}
			?></fieldset><?php
		}
		?>
		<input name="mode" type="hidden" id="mode" value="updatePreferences" />
		<div class="fr">
			<input type="submit" name="Submit" value="Update Preferences" />
		</div>
		</form><?php
	}
	?>
	<fieldset class="mainGroups"><legend>Products</legend>
	<form id="form1" name="form1" method="post" action="resources/bais_01_exe.php" target="w2">
	  Merchant UPC Code:<br />
	  <input type="text" name="varvalue" value="<?php echo h(q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='items' AND varnode='settings' AND varkey='merchantUPCCode'", O_VALUE));?>" />
	  <input type="submit" name="Submit" value="Update" />
	  <input name="mode" type="hidden" id="mode" value="updateParameter" />
	  <input name="vargroup" type="hidden" value="items" />
	  <input name="varnode" type="hidden" value="settings" />
	  <input name="varkey" type="hidden" value="merchantUPCCode" />
	</form>

	<form id="form2" name="form2" method="post" action="resources/bais_01_exe.php" target="w2">
	<br />
	<br />
	  <strong>Additional Regions (besides US States and Canadian Provinces):</strong>
	  <p class="gray">Place each option on a single line.  Format is Region Label:RL where RL is the abbreviation (2-3 characters). <span class="red">Do not use duplicate state or province abbreviations like CA, AK, AL or TX!</span></p>
	  <textarea name="varvalue" id="varvalue" rows="5" cols="55"><?php echo h(q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='items' AND varnode='settings' AND varkey='additionalRegions'", O_VALUE));?></textarea><br />

	  <input type="submit" name="Submit" value="Update" />
	  <input name="mode" type="hidden" id="mode" value="updateParameter" />
	  <input name="vargroup" type="hidden" value="items" />
	  <input name="varnode" type="hidden" value="settings" />
	  <input name="varkey" type="hidden" value="additionalRegions" />
	</form>
	</fieldset>

	<br />
	<fieldset class="mainGroups">
	<legend>Emailing and Other Settings </legend>
	<form id="form1" name="form1" method="post" action="resources/bais_01_exe.php" target="w2">
	  Max file size to add to zip <span class="gray">(in Megabytes)</span>:<br />
  <input type="text" name="varvalue" value="<?php echo h(q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='items' AND varnode='settings' AND varkey='maxFileSizeToZip'", O_VALUE));?>" />
  <input type="submit" name="Submit" value="Update" />
  <input name="mode" type="hidden" id="mode" value="updateParameter" />
  <input name="vargroup" type="hidden" value="items" />
  <input name="varnode" type="hidden" value="settings" />
  <input name="varkey" type="hidden" value="maxFileSizeToZip" />
    </form>
	<br />
	<?php
	if($zipSplitSize=q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='items' AND varnode='settings' AND varkey='zipSplitSize'", O_VALUE)){
		//OK
	}else{
		$zipSplitSize=30;
	}
	?>
	<form id="form1" name="form1" method="post" action="resources/bais_01_exe.php" target="w2">
	Max number of images to include in export zip:<br />
	<input type="text" name="varvalue" value="<?php echo h($zipSplitSize);?>" />
	<input type="submit" name="Submit" value="Update" />
	<input name="mode" type="hidden" id="mode" value="updateParameter" />
	<input name="vargroup" type="hidden" value="items" />
	<input name="varnode" type="hidden" value="settings" />
	<input name="varkey" type="hidden" value="zipSplitSize" />
	</form>
    </fieldset>
	<fieldset class="mainGroups">
    <legend>Data Management</legend>
    <form id="form1" name="form1" method="post" action="resources/bais_01_exe.php" target="w2">
      Tables which may be managed by the systementry tool:<br />
	<?php
	$a=q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='admin' AND varnode='' AND varkey='managableTables'", O_VALUE);
	$a=explode(',',$a);
	$t=q("SHOW TABLES", O_ARRAY);
	foreach($t as $n=>$v){
		$t[$n]=$v['Tables_in_'.$MASTER_DATABASE];
		ob_start();
		q("SHOW CREATE VIEW ".$t[$n], O_ROW, ERR_ECHO);
		$err=ob_get_contents();
		ob_end_clean();
		if(!$err)unset($t[$n]);
	}
	sort($t);
	?>
  <select name="varvalue[]" multiple="multiple" size="10">
  <option value="">&lt;none&gt;</option>
  <?php
	foreach($t as $n=>$v){
		if(in_array($v,$a)){
			$haveSelected=true;
			?><option value="<?php echo $v;?>" selected="selected"><?php echo $v;?></option><?php
			unset($t[$n]);
		}
	}
	if($haveSelected){ ?><option value="" disabled="disabled" onclick="return false;">_________________</option><?php }
	if(count($t))
	foreach($t as $v){
		?><option value="<?php echo $v;?>" <?php echo in_array($v,$a)?'selected':'';?>><?php echo $v;?></option><?php
	}
  ?>
  </select>
  <input type="submit" name="Submit" value="Update" />
  <input name="mode" type="hidden" id="mode" value="updateParameter" />
  <input name="vargroup" type="hidden" value="admin" />
  <input name="varnode" type="hidden" value="" />
  <input name="varkey" type="hidden" value="managableTables" />
    </form>
    </fieldset>
	<fieldset class="mainGroups">
	<legend>Data Management</legend>
	<form id="form1" name="form1" method="post" action="resources/bais_01_exe.php" target="w2">
	Export view fields which CANNOT be deleted:<br />
	(separate by a new line or comma)<br />
	<?php
	$restrictedFields=q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='admin' AND varnode='' AND varkey='restrictedFields'", O_VALUE);
	?>
	<textarea name="varvalue" cols="45" rows="6" id="varvalue"><?php echo h($restrictedFields);?></textarea>
	<br />
	<input type="submit" name="Submit" value="Update" />
	<input name="mode" type="hidden" id="mode" value="updateParameter" />
	<input name="vargroup" type="hidden" value="admin" />
	<input name="varnode" type="hidden" value="" />
	<input name="varkey" type="hidden" value="restrictedFields" />
	</form>
	</fieldset>
	<?php
}
?>

	</div>
	<div id="footer">
	<div id="footer">
<p>Home Base&reg; Management DB ver. 5.0 - All rights reserved &copy;2006-<?php echo date('Y');?></p>
</div>
<div id="ctrlSection" style="display:<?php echo $testModeC?'block':'none'?>;">
<iframe name="w0"></iframe>
<iframe name="w1"></iframe>
<iframe name="w2"></iframe>
<iframe name="w3"></iframe>
</div>

	</div>
	<?php if(!$hideCtrlSection){ ?>
	<div id="showTester" title="Javascript Tester" onclick="g('tester').style.display='block';">&nbsp;</div>
	<div id="tester" >
		<a href="#" onclick="g('ctrlSection').style.display=op[g('ctrlSection').style.display]; return false;">iframes</a><br />
		<textarea name="test" cols="65" rows="4" id="test">g('field').value</textarea><br />
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
</div>
</body>
</html><?php page_end()?>