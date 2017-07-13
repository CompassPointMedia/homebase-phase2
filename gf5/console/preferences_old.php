<?php 
//identify this script/GUI
$localSys['scriptID']='mg_fosterparents';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');

$hideCtrlSection=false;
//--------------------------------------------------
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $AcctCompanyName?> FCM ver2.0 - My Preferences</title>



<link id="cssUndoHTML" rel="stylesheet" href="/site-local/undohtml2.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
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
if(minroles() >= ROLE_CASE_MANAGER){
	?>
	<div class="balloon1">
	You must be a Database Administrator, <?php echo $wordFoundationDirector?><?php echo $apSettings['implementRegionalDirector'] ? ', '.$wordRegionalDirector.', ' : ', or '?> <?php echo $wordProgramDirector?> to change these settings
	</div><?php
	
}else{
	?><form name="form1" method="post" action="resources/bais_01_exe.php" target="w2">
	<div class="fr">
		<input type="submit" name="Submit" value="Update Preferences" />
	</div>
	<?php
	if(minroles() < ROLE_FOUNDATION_DIRECTOR){
		//DATABASE ADMIN FEATURES
		?><fieldset><legend>DB Administrator Settings</legend>
		<?php
		if(count($adminSettings['dbadmin']['sendObjects']))
		foreach($adminSettings['dbadmin']['sendObjects'] as $sendObject=>$v){
			?>
			<div id="sendObject_<?php echo $sendObject?>" class="sendObject">
				<h3 class="fl nullTop nullBottom"><?php echo $v['label']?></h3><p style="margin-top:7px;"> - notification emails sent out when <?php echo $v['when']?></p>
				<div class="sendChain">
					<div class="fl leftMain">
					<strong>General notices:</strong>
					</div>
					<input name="_adminSettings[dbadmin][sendObjects][<?php echo $sendObject?>][notifyOfficeLevel]" type="hidden" value="0" />
					<label>
					<input name="_adminSettings[dbadmin][sendObjects][<?php echo $sendObject?>][notifyOfficeLevel]" type="checkbox" value="1" id="_adminSettings[dbadmin][sendObjects][<?php echo $sendObject?>][notifyOfficeLevel]" <?php echo $v['notifyOfficeLevel'] ? 'checked' : '';?> onchange="dChge(this);" />
					 Notify all in office with these responsibilities: </label>
					<div class="fr">
                      <table class="permTable">
                        <thead>
                          <tr>
                            <th scope="col">Med./Dental</th>
                            <th scope="col">Ther./Clinical</th>
                            <th scope="col">Admin.</th>
                            <th scope="col">Finan./Billing</th>
                            <th scope="col">Clerical</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td><?php echo $v['permissions'] & PERM_MEDICAL ? 
						'<img src="/images/i/check1.png" width="16" height="16" alt="Yes" />' : '&nbsp;';?></td>
                            <td><?php echo $v['permissions'] & PERM_CLINICAL ? 
						'<img src="/images/i/check1.png" width="16" height="16" alt="Yes" />' : '&nbsp;';?></td>
                            <td><?php echo $v['permissions'] & PERM_ADMINISTRATIVE ? 
						'<img src="/images/i/check1.png" width="16" height="16" alt="Yes" />' : '&nbsp;';?></td>
                            <td><?php echo $v['permissions'] & PERM_CLERICAL ? 
						'<img src="/images/i/check1.png" width="16" height="16" alt="Yes" />' : '&nbsp;';?></td>
                            <td><?php echo $v['permissions'] & PERM_FINANCIAL ? 
						'<img src="/images/i/check1.png" width="16" height="16" alt="Yes" />' : '&nbsp;';?></td>
                          </tr>
                        </tbody>
                      </table>
				  </div>
					<br />
					<input name="_adminSettings[dbadmin][sendObjects][<?php echo $sendObject?>][notifyUpOneLevel]" type="hidden" value="0" />
					 <label>
					<input name="_adminSettings[dbadmin][sendObjects][<?php echo $sendObject?>][notifyUpOneLevel]" type="checkbox" value="1" id="_adminSettings[dbadmin][sendObjects][<?php echo $sendObject?>][notifyUpOneLevel]" <?php echo $v['notifyUpOneLevel'] ? 'checked' : '';?> onchange="dChge(this);" />
					 Go up one level</label><br />
					<div class="cb"> </div>
				</div>
				<div class="sendSpecific">
					<div class="fl leftMain">
					<strong>Specific notices:</strong>
					</div>
					<div class="fl">Also send emails to: </div>
					<div class="fl">
					<select name="_adminSettings[dbadmin][sendObjects][<?php echo $sendObject?>][specificSends][]" multiple="multiple" size="7" id="_adminSettings_dbadmin_sendObjects_<?php echo $sendObject?>" onchange="dChge(this);newOption(this, 'directors.php', 'l1_staff', '800,700');" cbtable="bais_staff">
					<option value="">&lt; None &gt;</option>
					<?php
					
					
					$oldQueryDeleteBy2010_09_30="SELECT
					un_username, un_firstname, un_lastname, un_email, so_permissions
					FROM bais_universal, bais_staff, gf_StaffOffices 
					WHERE un_username=st_unusername AND st_active=1 AND st_unusername=so_stusername 
					ORDER BY un_lastname, un_firstname";
					
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
					st_active=1 AND
					(so.so_stusername IS NOT NULL OR sr.sr_stusername IS NOT NULL OR un_username IN('".str_replace('bais_staff:','',implode("','",$v['specificSends']))."'))
					GROUP BY u.un_username					
					ORDER BY 
					IF(un_username IN('".str_replace('bais_staff:','',implode("','",$v['specificSends']))."'),1,2),
					un_lastname, 
					un_firstname", O_ARRAY_ASSOC);
					if($mailStaffList)foreach($mailStaffList as $o=>$w){
						?><option value="bais_staff:<?php echo $o?>" <?php 
						if(in_array('bais_staff:'.$o, $v['specificSends']))echo 'selected';
						?>><?php echo h($w['un_lastname'] . ', '.$w['un_firstname']. ' ('.$w['un_email'].')');?></option><?php
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
					<select name="_adminSettings[dbadmin][sendObjects][<?php echo $sendObject?>][template]" id="_adminSettings_dbadmin_sendObjects_<?php echo $sendObject?>_template">
					<option value="">&lt; Select.. &gt;</option>
					<?php
					foreach($adminSettings['dbadmin']['registeredTemplates'] as $o=>$w){
						?><option value="<?php echo $o?>" <?php echo $v['template']==$o?'selected':''?>><?php echo h($o . ' ('.$w['file'].')')?></option><?php
					}
					?>
					</select>
				
					<div class="cb"> </div>
				</div>
			</div><?php
		}
		?>


			
		</fieldset><?php
	}
	if(minroles() < ROLE_REGIONAL_DIRECTOR){
		//FOUNDATION DIRECTOR SETTINGS
		?><fieldset><legend><?php echo $wordFoundationDirector;?> Settings</legend>
		
		Equivalent Titles:<br>
		<table border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td>Great Locations Title </td>
			<td>Your Equivalent Title </td>
			<td>Abbeviated Title </td>
			<td>Acronym</td>
		  </tr>
		  <tr>
			<td>Foundation Director </td>
			<td><input name="_adminSettings[foundation][wordFoundationDirector]" type="text" id="_adminSettings[foundation][wordFoundationDirector]" value="<?php echo $adminSettings['foundation']['wordFoundationDirector'];?>" onchange="dChge(this);" /></td>
			<td><input name="_adminSettings[foundation][wordShortFoundationDirector]" type="text" id="_adminSettings[foundation][wordShortFoundationDirector]" onchange="dChge(this);" value="<?php echo $adminSettings['foundation']['wordShortFoundationDirector'];?>" size="12" /></td>
			<td><input name="_adminSettings[foundation][wordAcroFoundationDirector]" type="text" id="_adminSettings[foundation][wordAcroFoundationDirector]" onchange="dChge(this);" value="<?php echo $adminSettings['foundation']['wordAcroFoundationDirector'];?>" size="5" /></td>
		  </tr>
		  <tr>
			<td>Regional Director </td>
			<td><input name="_adminSettings[foundation][wordRegionalDirector]" type="text" id="_adminSettings[foundation][wordRegionalDirector]" value="<?php echo $adminSettings['foundation']['wordRegionalDirector'];?>" onchange="dChge(this);" /></td>
			<td><input name="_adminSettings[foundation][wordShortRegionalDirector]" type="text" id="_adminSettings[foundation][wordShortRegionalDirector]" onchange="dChge(this);" value="<?php echo $adminSettings['foundation']['wordShortRegionalDirector'];?>" size="12" /></td>
			<td><input name="_adminSettings[foundation][wordAcroRegionalDirector]" type="text" id="_adminSettings[foundation][wordAcroRegionalDirector]" onchange="dChge(this);" value="<?php echo $adminSettings['foundation']['wordAcroRegionalDirector'];?>" size="5" /></td>
		  </tr>
		  <tr>
			<td>Program Director </td>
			<td><input name="_adminSettings[foundation][wordProgramDirector]" type="text" id="_adminSettings[foundation][wordProgramDirector]" value="<?php echo $adminSettings['foundation']['wordProgramDirector'];?>" onchange="dChge(this);" /></td>
			<td><input name="_adminSettings[foundation][wordShortProgramDirector]" type="text" id="_adminSettings[foundation][wordShortProgramDirector]" onchange="dChge(this);" value="<?php echo $adminSettings['foundation']['wordShortProgramDirector'];?>" size="12" /></td>
			<td><input name="_adminSettings[foundation][wordAcroProgramDirector]" type="text" id="_adminSettings[foundation][wordAcroProgramDirector]" onchange="dChge(this);" value="<?php echo $adminSettings['foundation']['wordAcroProgramDirector'];?>" size="5" /></td>
		  </tr>
		  <tr>
			<td>Case Manager </td>
			<td><input name="_adminSettings[foundation][wordCaseManager]" type="text" id="_adminSettings[foundation][wordCaseManager]" value="<?php echo $adminSettings['foundation']['wordCaseManager'];?>" onchange="dChge(this);" /></td>
			<td><input name="_adminSettings[foundation][wordShortCaseManager]" type="text" id="_adminSettings[foundation][wordShortCaseManager]" onchange="dChge(this);" value="<?php echo $adminSettings['foundation']['wordShortCaseManager'];?>" size="12" /></td>
			<td><input name="_adminSettings[foundation][wordAcroCaseManager]" type="text" id="_adminSettings[foundation][wordAcroCaseManager]" onchange="dChge(this);" value="<?php echo $adminSettings['foundation']['wordAcroCaseManager'];?>" size="5" /></td>
		  </tr>
		</table> 
		<br>
		<br>
		A program director will normally manage a branch or office representing a geographic region. A regional director need not be assigned to a physical office but will have charge over all Program Directors under the same branch or office. 		
		</fieldset><?php
	}
	if($apSettings['implementRegionalDirector'] && minroles() < ROLE_PROGRAM_DIRECTOR){
		//REGIONAL DIRECTOR SETTINGS
		?><fieldset><legend><?php echo $wordRegionalDirector;?> Settings</legend>
		
		<em>(No <?php echo strtolower($wordRegionalDirector);?> settings currently available)</em>
		
		</fieldset><?php
	}
	if(minroles() < ROLE_CASE_MANAGER){
		//PROGRAM MANAGER
		?><fieldset><legend><?php echo $wordProgramDirector;?> Settings</legend>
		
		<em>(No <?php echo strtolower($wordProgramDirector);?> settings currently available)</em>
		
		</fieldset><?php
	}
	?>
	<input name="mode" type="hidden" id="mode" value="updatePreferences" />
	<div class="fr">
		<input type="submit" name="Submit" value="Update Preferences" />
	</div>
	</form>
	<?php
}
?>


</div>
	<div id="footer">
	<div id="footer">
	<p>[<a href="/gf5/console/root_clients.php">Clients</a>]  </p>
	<p> Great Locations&reg; Management DB ver. 5.0 - All rights reserved &copy;2006-<?php echo date('Y');?>
        </p>
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