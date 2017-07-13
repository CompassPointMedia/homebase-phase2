<?php
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='generic';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');


$hideCtrlSection=false;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Home Page - '.$AcctCompanyName;?></title>



<link rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
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
<script language="JavaScript" type="text/javascript" src="/Library/js/jq/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/jq/numeric.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle .02 coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
var ResourceToken='<?php echo $ResourceToken;?>';

var talks=1; //whether this page broadcasts overall state changes which other pages listen for
var listens=0;
<?php 
//js var user settings
js_userSettings();
?>
</script>

<style type="text/css">
.startNewRow td{
	border-top:2px solid #333;
	}
</style>

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
//universal items worth being at top
$bulletinsSuppressByMe=true;
require('components/comp_401_bulletins_v100.php');

if(minroles()<=ROLE_ADMIN){
	$exportDisableCancel=true;
	?>
	<br />
	<br />

	<div class="cb"> </div><?php
	require('components/comp_exportmanager_v101.php');
	?><div class="cb"> </div>
	<br />
	<h2>Last 50 new records</h2>
	<?php
	$a=q("SELECT * FROM finan_items WHERE ResourceType IS NOT NULL ORDER BY CreateDate DESC LIMIT 51", O_ARRAY);
	?>
	<table id="hcTable1" class="yat">
	<thead>
	<tr>
		<th>Created</th>
		<th>Edited</th>
		<th>by..</th>
		<th>SKU</th>
		<th>Category</th>
		<th>Name</th>
		<th>Thumb</th>
		<th>Loc.</th>
		<th colspan="3">Exported</th>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>MIVA</th>
		<th>EBAY</th>
		<th>AMAZON</th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($a){
		foreach($a as $n=>$v){
			$j++;
			?><tr class="<?php echo !fmod($j,2)?'alt':''?>">
				<td><?php echo date('n/j \a\t g:iA',strtotime($v['CreateDate']));?></td>
				<td><?php if(abs(strtotime($v['CreateDate'])-strtotime($v['EditDate']))>30)echo date('n/j \a\t g:iA',strtotime($v['EditDate']));?></td>
				<td><?php echo $v['Creator'];?></td>
				<td><a href="products.php?Items_ID=<?php echo $v['ID']?>" title="View/edit this product" onclick="return ow(this.href,'l1_products','850,700');"><?php echo $v['SKU'];?></a></td>
				<td><?php echo $v['Category'];?></td>
				<td><?php echo $v['Name'];?></td>
				<td><?php
				if(strlen($v['ThumbData'])){
					$t=unserialize(base64_decode($v['ThumbData']));
					echo round($t['thumbWidth'],0) . 'x'.round($t['thumbHeight'],0);
				}else{
					?>&nbsp;<?php
				}
				?></td>
				<td><?php
				if(strlen($v['Lat1'])){
					?><span style="cursor:pointer;" title="this number represents number of points on the polygon" class="gray"><?php echo strlen($v['Lat1'])-strlen(str_replace('|','',$v['Lat1']))+1;?></span><?php
				}else{
					?>&nbsp;<?php
				}
				?></td>
				<td class="tac"><?php echo $v['MIVA_ToBeExported']?'&nbsp;':'Y';?></td>
				<td class="tac"><?php echo $v['EBAY_ToBeExported']?'&nbsp;':'Y';?></td>
				<td class="tac"><?php echo $v['AMAZON_ToBeExported']?'&nbsp;':'Y';?></td>
			</tr><?php
		}
	}else{
		?><tr>
		<td colspan="102"><em class="gray">No records found for that criteria</em></td>
		</tr><?php
	}
	?>
	</tbody>
	</table>
	<br />
	<br />
	<?php
	/* list only the filesize attribute in a view */
	q("CREATE OR REPLACE VIEW _v_finan_items_duplicate_file_sizes AS
	SELECT FileSize, COUNT(*) FROM finan_items GROUP BY FileSize HAVING COUNT(*) > 1");
	/* now show all items that have two or more records with the same file size */
	if($a=q("SELECT ID, Name, SKU, Category, SubCategory, i.FileSize, Width1,Height1, Description, CreateDate FROM finan_items i JOIN _v_finan_items_duplicate_file_sizes d ON i.FileSize=d.FileSize WHERE i.FileSize>0 ORDER BY i.FileSize", O_ARRAY)){
		?>
		<h2>Filesize Duplicates</h2>
		<p class="gray">Following listed items have exact duplicate file sizes.  This is very rare, so you should be suspicious that they are duplicate records using the same file.  For help on managing this contact an administrator.</p>
		
		<table id="hcTable2" class="yat">
		<thead>
		<tr>
			<th>Filesize</th>
			<th>Width</th>
			<th>Height</th>
			<th>SKU</th>
			<th>Name</th>
			<th>Category</th>
			<th>Situated</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$j=0;
		foreach($a as $v){
			extract($v);
			if($FileSize!==$buffer){
				$buffer=$FileSize;
				$startNewRow='startNewRow';
			}else{
				$startNewRow='';
			}
			$j++;
			?><tr class="<?php echo $startNewRow;?>">
				<td><?php echo number_format($FileSize,0);?></td>
				<td><?php echo $Width1.'px';?></td>
				<td><?php echo $Height1.'px';?></td>
				<td><a href="products.php?Items_ID=<?php echo $ID;?>" onclick="return ow(this.href,'l1_items','850,700');" title="View this product"><?php echo $SKU;?></a></td>
				<td><?php echo $Name;?></td>
				<td><?php echo $Category.($SubCategory?' - '.$SubCategory:'');?></td>
				<td><?php echo $Description;?></td>
			</tr><?php
		}
		?>
		</tbody>
		</table>
		<?php	
	}
	
	//records with no master file present
	$f=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/master');
	if(count($f))
	foreach($f as $n=>$v){
		unset($f[$n]);
		$g[current(explode('.',$n))]=$v;
	}
	if($missing=q("SELECT
		i.ID, i.SKU, i.Name, i.Category, i.SubCategory
		FROM finan_items i LEFT JOIN finan_items i2 ON i.ID=i2.ID AND i2.SKU IN('".@implode("','",array_keys($g))."')
		WHERE i.ResourceType IS NOT NULL AND i2.ID IS NULL AND !i.IgnoreFile", O_ARRAY)){
		?>
		<style type="text/css">
		.box1{
			border:1px dotted #666;
			margin-bottom:7px;
			margin-right:7px;
			padding:5px;
			max-width:250px;
			float:left;
			}
		#missingFiles{
			border:1px solid darkred;
			padding:15px;
			position:relative;
			}
		#missingFilesToggle{
			position:absolute;
			top:0px;
			right:0px;
			background-color:darkred;
			color:white;
			width:80px;
			height:20px;
			text-align:center;
			cursor:pointer;
			}
		#hcTable3 td{
			border-bottom:1px solid #ccc;
			padding:2px 4px 1px 5px;
			}
		</style>
		<script language="javascript" type="text/javascript">
		function toggleMissingFiles(o){
			var n=o.innerHTML;
			if(n=='expand..'){
				g('missingFiles').style.overflow='visible';
				g('missingFiles').style.height='inherit';
				o.innerHTML='collapse..';
			}else{
				g('missingFiles').style.overflow='scroll';
				g('missingFiles').style.height='275px';
				o.innerHTML='expand..';
			}
		}
		</script>
		<h2>Records without master file (<?php echo count($missing);?>)</h2>
		<p class="gray">The following records have no master file associated with them.  The master file must be in images/documentation/<?php echo $GCUserName?>/master.</p>
		<div id="missingFiles" style="overflow:scroll;height:275px;">
		<div id="missingFilesToggle" onclick="toggleMissingFiles(this)">expand..</div>
		<?php
		foreach($missing as $v){
			extract($v);
			?><div class="box1"><a href="products.php?Items_ID=<?php echo $ID;?>" onclick="return ow(this.href,'l1_items','850,700');"><?php echo $v['SKU'];?></a> - <?php echo $Name;?></div>
			<?php 
		}
		?>
		<div class="cb"> </div>
		</div>
		<?php
	}
	require('components/comp_200_products_missing_filedata_or_price.php');
	
	
}else if(minroles()==ROLE_MANAGER){

}else if(minroles()==ROLE_AGENT){
	
}
?>
	
	</div>
	<div id="footer">
	<div id="footer">
<p>GL Franchise&reg; Management DB ver. 5.0 - All rights reserved &copy;2006-<?php echo date('Y');?></p>
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
