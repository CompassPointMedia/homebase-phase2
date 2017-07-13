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

<title><?php echo $PageTitle='Data vs. files - '.$AcctCompanyName?></title>



<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style>
/** CSS Declarations for this page **/
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
	
<?php
if(!function_exists('get_image'))require($FUNCTION_ROOT.'/function_get_image_v220.php');
$root=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName, array('positiveFilters'=>'\.(jpg)$',));
$pending=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending', array('positiveFilters'=>'\.(jpg)$',));
$pending_master=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master', array('positiveFilters'=>'\.(jpg)$',));
$master=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/master', array('positiveFilters'=>'\.(jpg)$',));
?><style type="text/css">
.yat .sep{
	border-left:1px dotted #666;
	}
</style>
<table id="thisTable" class="yat">
<thead>
<tr>
	<th>Map SKU</th>
	<th>root</th>
	<th>master</th>
	<th>pending</th>
	<th>pending_master</th>
</tr>
</thead>
<?php
ob_start();
?>
<tbody>
<?php
if($a=q("SELECT ID, SKU, Name FROM finan_items WHERE ResourceType IS NOT NULL ORDER BY 
IF(ID<=3645,1,IF(SKU IN(

'CWAN0007',
'CWAN0008',
'CWAN0009',
'CWBE0002',
'CWBE0003',
'CWBE0004',
'CWBE0005',
'CWBR0001',
'CWBR0002',
'CWCH0005',
'CWCH0006',
'CWCH0007',
'CWCT0001',
'CWEC0001',
'CWEC0002',
'CWEC0003',
'CWEC0004',
'CWEC0005',
'CWEC0006',
'CWEC0007',
'CWFL0001',
'CWFR0002',
'CWGA0001',
'CWGA0002',
'CWGA0003',
'CWGA0004',
'CWGA0005',
'CWGA0006',
'CWGA0007',
'CWGA0008',
'CWGA0009',
'CWGA0010',
'CWGA0011',
'CWGA0012',
'CWGA0013',
'CWGC0001',
'CWGC0002',
'CWGC0003',
'CWGC0004',
'CWGC0005',
'CWGC0006',
'CWGE0003',
'CWGE0004',
'CWGE0005',
'CWGE0006',
'CWGE0007',
'CWGE0008',
'CWGE0009',
'CWGE0010',
'CWKY0001',
'CWLA0001',
'CWMD0001',
'CWMD0002',
'CWMO0002',
'CWMS0001',
'CWMS0002',
'CWNC0001',
'CWNC0002',
'CWOH0001',
'CWOH0002',
'CWOH0003',
'CWPA0002',
'CWSC0002',
'CWSC0003',
'CWSE0002',
'CWSE0003',
'CWSE0004',
'CWSE0005',
'CWTN0001',
'CWUS0001',
'CWUS0002',
'CWUS0003',
'CWUS0004',
'CWUS0005',
'CWUS0006',
'CWUS0007',
'CWUS0008',
'CWUS0009',
'CWUS0010',
'CWUS0011',
'CWUS0012',
'CWUS0013',
'CWUS0014',
'CWUS0015',
'CWUS0016',
'CWUS0017',
'CWUS0018',
'CWUS0019',
'CWUS0020',
'CWUS0021',
'CWUS0022',
'CWUS0023',
'CWUS0024',
'CWUS0025',
'CWUS0026',
'CWVA0004',
'CWVA0005',
'CWVA0006',
'CWVA0007',
'REWA0001',
'REWA0002',
'REWA0003',
'REWA0004',
'REWA0005',
'REWA0006',
'REWA0007',
'REWA0008',
'REWA0009',
'REWA0010',
'REWA0011',
'REWA0012',
'REWA0013',
'REWA0014',
'REWA0015',
'REWA0016',
'REWA0017',
'REWA0018',
'REWA0019',
'REWA0020',
'REWA0021',
'REWA0022',
'REWA0023',
'REWA0024',
'REWA0025',
'REWA0026',
'REWA0027',
'REWA0028',
'REWA0029',
'REWA0030',
'REWA0031',
'REWA0032',
'REWA0033',
'REWA0034',
'REWA0035',
'REWA0036',
'REWA0037',
'REWA0038',
'REWA0039',
'REWA0040',
'REWA0041',
'REWA0042',
'REWA0043',
'REWA0044',
'REWA0045',
'REWA0046',
'REWA0047',
'REWA0048',
'REWA0049',
'REWA0050',
'REWA0051',
'REWA0052',
'REWA0053',
'REWA0054',
'REWA0055',
'REWA0056',
'REWA0057',
'REWA0058',
'REWA0059',
'REWA0060',
'REWA0061',
'REWA0062',
'REWA0063',
'REWA0064',
'REWA0065',
'REWA0066',
'REWA0067',
'REWA0068',
'REWA0069',
'REWA0070',
'REWA0071',
'REWA0072',
'REWA0073',
'REWA0074',
'REWA0075',
'REWA0076',
'REWA0077',
'REWA0078',
'REWA0079',
'REWA0080',
'REWA0081',
'REWA0082',
'REWA0083',
'REWA0084',
'REWA0085',
'REWA0086',
'REWA0087',
'REWA0088',
'REWA0089',
'REWA0090',
'REWA0091',
'REWA0092',
'REWA0093',
'REWA0094',
'REWA0095',
'REWA0096',
'REWA0097',
'CWAL0002',
'CWAN0004',
'CWAN0005',
'CWAN0006'

), 2, 3)),SKU ASC", O_ARRAY)){
	$i=0;
	foreach($a as $n=>$v){
		$i++;
		if($i==1 || $i==1756 || $i== 1956){
			if($i>1 & false){
				//close previous
				?><tr>
				<td>subtotal 1</td>
				<td>subtotal 2</td>
				</tr><?php
			}
			?><tr>
			<td colspan="100%"><h2><?php echo $i==1 ? 1755 : ($i==1756 ? 200 : 65);?></h2></td>
			</tr>
			<tr>
			<td>ID</td>
			<td>Map SKU</td>
			<td>root</td>
			<td>master</td>
			<td>pending</td>
			<td>pending_master</td>
			</tr>
			<?php
		}
		?><tr>
			<td><?php echo $v['ID'];?></td>
			<td><a href="products.php?Items_ID=<?php echo $v['ID']?>" title="View/edit" onclick="return ow(this.href,'l1_items','800,700');"><?php echo $v['SKU'];?></a></td>
			<td class="tac sep"><?php
			if($b=get_image($v['SKU'],$root)){
				echo $b['width'].'x'.$b['height'];
			}else{
				echo '&nbsp;';
			}
			?></td>
			<td class="tac sep"><?php
			if($b=get_image($v['SKU'],$master)){
				echo $b['width'].'x'.$b['height'];
			}else{
				echo '&nbsp;';
			}
			?></td>
			<td class="tac sep"><?php
			if($b=get_image($v['SKU'],$pending)){
				echo $b['width'].'x'.$b['height'];
			}else{
				echo '&nbsp;';
			}
			?></td>
			<td class="tac sep"><?php
			if($b=get_image($v['SKU'],$pending_master)){
				echo $b['width'].'x'.$b['height'];
			}else{
				echo '&nbsp;';
			}
			?></td>
		</tr><?php
	}
}else{
	?><tr>
	<td colspan="100%"><em class="gray">No records found for that criteria</em></td>
	</tr><?php
}
?>
</tbody>
<?php
$body=ob_get_contents();
ob_end_clean();
if(false && 'use footer'){
	?>
	<tfoot>
	<tr>
		<td><?php echo $total1;?></td>
		<td><?php echo $total2;?></td>
	</tr>
	</tfoot>
	<?php
}
echo $body;
?>
</table>
      
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