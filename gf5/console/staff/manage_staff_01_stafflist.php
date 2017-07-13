<?php
/******** --------------------------------------------------------
2004-06-15 by Sam
This is the first page to deal with hideHeaders systematically.
If a page is standalone we'll need to connect and show headers, however if the page is text in another page, we do not show <html>, <head>, etc. nor include javascript or css because it would (should) be found in the parent.
The system below is pretty hacker-proof because trying to call page.php?hideHeaders=1 would shut down the file, and calling page.php?hideHeaders=0 would require authorization and a connection (which is done in the parent if it's an include).

-------------------------------------------------------- *******/
if($hideHeaders){
	substr(__FILE__,-(strlen($_SERVER['PHP_SELF'])))==$_SERVER['PHP_SELF']? exit('-include page locked'):'';
}
if(!$hideHeaders){
	//begin session and identify script, include main configs
	//note this is a weakness because this script will work a process.  It has its own id, the parent script does too -- only one can be present at a time. 2004-06-16, I made the name the same for now, but note the componentID
	//identify this script/GUI
$localSys['scriptID']='ecommerce';
	$localSys['scriptVersion']='4.0';
	$localSys['componentID']='main';
	?><?php
	require('../systeam/php/config.php');
	?><?php
	require('../resources/bais_00_includes.php');
	?><?php
	require('../systeam/php/auth_i2_v100.php');
	?><?php
	$hideCtrlSection=false;
	
	
	//--------------------------------------------------

	$db_cnx=mysqli_connect($MASTER_HOSTNAME,$MASTER_USERNAME,$MASTER_PASSWORD);
	mysqli_select_db($db_cnx,$MASTER_DATABASE);
	
}

if(!$hideHeaders){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Staff List</title>
<!-- NOTE: THIS IS IDENTICAL TO THE PARENT PAGE -->
<link href="/Library/css/common/i1.css" rel="stylesheet" type="text/css">
<link href="/Library/css/tables/i1.css" rel="stylesheet" type="text/css">

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

//alert('The context menus are not installed in this page');
var ownPage=1;
//------------- begin menu javascript -----------------------------
var menuLvl=new Array();
//type of menu matching. regexp uses rMenuMap, normal uses menuMap (see below)
var menuType='regexp'; //must be 'normal', or 'regexp'

//this menu map is the normal way, one-to-one correspondence between object and menu
var menuMap=new Array();

//this menu map uses regular expressions so that loc1,loc2,loc. all map =>amenu1
//if you use this make sure you don't get menus showing in unexpected locations!
var rMenuMap=new Array();
rMenuMap['^ro_[0-9]+_pr_[0-9]+$']='roleMenu';
rMenuMap['^pr_[0-9]$']='processMenu';

//this is the id of the div containing the menu, set initially to blank
var menuIDName='';

//Under Version 1.0 -- hidemenu-cancel (hmcxl) this field is used to prevent hidemenu from being called twice when it would cause problems
var hm_cxl=0;
var hm_cxlseq=0;
var option_hm_cxl=0;
//this determines the alignment from the source element.  Must correspond to either menuMap or rMenuMap.  Options under development are 'mouse','topleftalign','bottomleftalign', 'rightalign', and there will be more -- these are not all developed yet.
//NOTE: default is 'mouse'
var menuAlign= new Array();
menuAlign['^ro_[0-9]+_pr_[0-9]+$']='mouse';
menuAlign['^ro_[0-9]+$']='mouse';

//holds the status message during mouseovers, initially set to blank
var statusBuffer='';
var ownPage=1;
//------------- end menu javascript -----------------------------
</script>
</head>
<body><?php }else{
	?><script>var ownPage=0;</script><?php } ?>

<?php
		//connection
		$db_cnx=mysqli_connect($MASTER_HOSTNAME,$MASTER_USERNAME,$MASTER_PASSWORD);
mysqli_select_db($db_cnx,$MASTER_DATABASE);
		$sql="SELECT 
		un_username,
		st_status AS Category, 
		CONCAT(un_lastname,', ',un_firstname) AS FullName, 
		un_username AS UserName, un_email AS Email, 
		st_status AS f29172, 
		CONCAT(un_lastname,', ',un_firstname) AS f39172, 
		st_unusername AS primaryKeyField 
		
		FROM bais_universal, bais_staff
		WHERE un_username=st_unusername 
		ORDER BY Category DESC, un_lastname, un_firstname";

		$result=mysql_query($sql) or die(mysqli_error());
		$rand=rand(1000,100000);
		?>
		<style>
		.data1_tableregion<?php echo $rand?>{
		background-color:STEELBLUE;
		}
		</style>
		<div id="staffList">
        <table class="data1" width="100%" border="0" cellspacing="0">
          <thead> 
          <tr class="data1_tableregion<?php echo $rand?>"> 
            <td></td>
<td>Name</td>
<td>Username</td>
<td>Email</td>

          </tr>
          </thead> 
			 <tbody>
          <?php
$tableRegion=0;
while($rd=mysqli_fetch_array($result,MYSQLI_ASSOC)){
	$tableRegion++;
	extract($rd)
	?>
	<tr id="u_<?php echo $un_username?>" bgcolor="<?php echo fmod($i,2)?'#FFFFFF':'IVORY'?>" onclick="highlight_select(this)" onDblClick="highlight_select(this)" onContextMenu="highlight_select(this)" > 
	<td><?php
	if($Category==1){
	  echo '<img src="/images/person.gif" alt="staff with usage" />';
	}
	?></td>
	<td nowrap><?php echo htmlentities($FullName)?></td>
	<td nowrap><?php echo $un_username?></td>
	<td nowrap><?php echo $Email?></td>
	</tr>
	<?php
}
if(!$tableRegion){
	?>
	</tbody>
	<tr> 
	<td colspan="5"><currently no records></td>
	</tr>
	<?php
}
?>
</table>
</div>
<?php
//--------------------------------------------------
if($srcregion && $tgtregion){
	?><script>window.parent.<?php echo $tgtregion?>.innerHTML=<?php echo $srcregion?>.innerHTML;</script><?php
}
if(!$hideHeaders){?></body>
</html><?php }?>