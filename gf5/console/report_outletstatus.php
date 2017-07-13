<?php 
/*
2012-11-10
this is a scanning script but takes a long time being inline.  Essentially, this is one site checking a primary sales site for page status and parameters on the page, in this case images.  It sends no information BACK to the page or server, but could as an if-condition (email someone, ftp the file over, trigger another process).

I have written a function to do this, but I have not defined the environment and output the funcion can provide.

*** Nor have I stored this process in a database ***

also the output is pretty specialized and not parameterized for any type of configurable report.


*/
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='main';
$localSys['pageLevel']=1;


require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require('systeam/php/auth_i2_v100.php');


$statuses=array(
	'404'=>'404:not found',
	'200'=>'OK',
	'unknown'=>'unknown',
	0=>'cnx error',
);
function page_and_image($recordKey,$options=array()){
	/*
	2012-11-10
	system needs it to return the following in an array
	status: 0=no response, bad connection
	*/
	global $page_and_image;
	unset($page_and_image);
	extract($options);
	if($outlet=='MIVA'){
		$fetchBaseURL='www.historicmapsrestored.com';
		$fetchExtension='html';
		$url=($fetchSecureProtocol ? 'https://' : 'http://').$fetchBaseURL.'/'.$recordKey.($fetchExtension?'.'.$fetchExtension : '');
		$out=`curl -i -A "HomeBase Reader version 1.0" $url`;
		if(strstr($out,'Could\'nt resolve host') || !trim($out))		return array('status'=>0);
		$out=preg_split('/[\n\r]{3,}/',$out);
		$head=$out[0];
		$out=implode("\n",$out);
		if(strstr($head,'404 Not Found'))								return array('status'=>'404');
		if(!preg_match('/HTTP\/[.0-9]+ 200 OK/',$head))					return array('status'=>'unknown');
		$r['status']='200';
		
		unset($images,$images_build,$temp);
		
		if(preg_match('/var image_data[0-9]+(.|\s)+?image_data[0-9]+/',$out,$m)){
			$m=preg_split('/[\n\r]+/',$m[0]);
			foreach($m as $v){
				if(preg_match('/\.(jpg|gif|png)/i',$v)){
					$images_build[]=trim(stripslashes($v),',"');
				}
			}
			//ugh
			foreach($images_build as $v){
				preg_match('/_[0-9]+x[0-9]+/',$v,$m);
				$temp[str_replace($m[0],'',$v)][]=$v;
			}
			/* nope, we can read the image without the _100x100 string 
			foreach($temp as $n=>$v){
				$offer=current($v);
				if(preg_match('/_[0-9]+x[0-9]+/',$offer)){
					$images[]=$offer;
				}else{
					$images[]=$n;
				}
			}
			instead..
			*/
			$images=array_keys($temp);
			foreach($images as $n=>$v)$images[$n]='mm5/'.$v;
		}
		
		/*
		but not all are like this..
		$regex='#graphics\\\\\\/00000001\\\\\\/([^\n]+)#';
		if(preg_match_all($regex,$out,$m)){
		*/
		if($images){
			foreach($images as $img){
				if(preg_match('/[0-9]{2,}x[0-9]{2,}/',$img))continue;
				$imgUrl=($fetchSecureProtocol ? 'https://' : 'http://').$fetchBaseURL.'/'.$img;
				$iout=`curl -I -A "HomeBase Reader version 1.0" $imgUrl`;
				$iout=explode("\n",trim($iout));
				$status='';
				foreach($iout as $n=>$v){
					if($n==0){
						if(strstr($v,'404 Not Found')){
							$r['images'][strtolower($img)]=array(
								'name'=>$img,
								'status'=>'404',
							);
							break;
						}
					}
					$a=explode(':',$v);
					$key=strtolower(trim($a[0]));
					unset($a[0]);
					$val=implode(':',$a);
					$iout[$key]=trim($val);
					unset($iout[$n]);
					$r['images'][strtolower($img)]=array(
						'name'=>$img,
						'status'=>'200',
						'size'=>($iout['content-length'] ? $iout['content-length'] : NULL),
						'last_modified'=>($iout['last-modified'] ? date('Y-m-d H:i:s',strtotime($iout['last-modified'])) : NULL),
					);
				}
			}
		}
	}else if(false){
	
	}
	return $r;
}
$selectedOutlets=array(
	'MIVA'=>array(
	
	),
	/*
	'EBAY'=>array(
	
	),
	*/
);

$PageTitle='Sales Outlet Online Status';

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo dynamic_title($PageTitle.' - '.$AcctCompanyName);?></title>



<link id="cssUndoHTML" rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="../../site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="../../Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
a.red{
	color:darkred;
	}
#wrong{
	color:darkred;
	background-color:cornsilk;
	outline:1px dotted darkred;
	}
</style>

<script language="JavaScript" type="text/javascript" src="/Library/js/jquery.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
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

var isEscapable=2;

AddOnkeypressCommand('PropKeyPress(e)'); //if not declared already
</script>
<?php ob_start();// mod form attributes?>

</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onSubmit="return beginSubmit();">
<?php }?>
<div id="header">
&nbsp;

</div>
<div id="mainBody">
<?php
$out=ob_get_contents();
ob_end_clean();
$out=str_replace('action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2"','method="get"',$out);
echo $out;
?>

<h2>Online Status: Historic Maps Restored</h2>
<label>
<input name="searchType" type="radio" value="batch" <?php echo !$searchType||$searchType=='batch'?'checked':''?> />
Batch number</label>
<select name="Batches_ID" id="Batches_ID">
<option value="">&lt;Select..&gt;</option>
<?php
if($a=q("SELECT ID, CreateDate, SubType, Quantity FROM gen_batches WHERE Type='Export' AND SubType IN('".implode("','",array_keys($exportTypes))."') ORDER BY StartTime DESC", O_ARRAY)){
	foreach($a as $v){
		?><option value="<?php echo $v['ID'];?>" <?php echo $Batches_ID==$v['ID']?'selected':''?>><?php 
		echo $v['ID'].' - '.date('m/d',strtotime($v['CreateDate'])).' - '.$v['SubType'].' ('.$v['Quantity'].')';
		?></option><?php
	}
}
?>
</select>
<br />
<label>
<input name="searchType" type="radio" value="category" <?php echo $searchType=='category'?'checked':''?> />
Category</label>
<select name="Category" id="Category">
<option value="">&lt;Select..&gt;</option>
<?php
if($a=q("SELECT DISTINCT Category FROM finan_items WHERE Category!='' ORDER BY Category", O_COL)){
	foreach($a as $v){
		?><option value="<?php echo h($v);?>" <?php echo strtolower($Category)==strtolower($v)?'selected':''?>><?php echo h($v);?></option><?php
	}
}
?>
</select>
<br />
<label>
<input name="searchType" type="radio" value="date" <?php echo $searchType=='date'?'checked':''?> />
Date Range from</label>
<input name="ReportDateFrom" type="text" id="ReportDateFrom" value="<?php if($ReportDateFrom)echo date('n/j/Y',strtotime($ReportDateFrom));?>" size="12" />
to
<input name="ReportDateTo" type="text" id="ReportDateTo" value="<?php if($ReportDateTo)echo date('n/j/Y',strtotime($ReportDateTo));?>" size="12" />
<br />
<label>
<input name="searchType" type="radio" value="sku" <?php echo $searchType=='sku'?'checked':''?> />
List of SKUs: <span class="gray">(separate by a comma)</span></label><br />
<textarea name="SKU" cols="50" rows="2" id="SKU"><?php echo $SKU;?></textarea>
<br />
<input type="submit" name="Submit" value="<?php echo $searchType?'Update':'Search';?>" />
<input type="button" name="Button" value="Close" onclick="window.close();" />
<?php
//shorten the form for now
echo '</form><form>'
;?>
<br />
<br />
<br />
<br />
<br />
<?php
if($searchType){
	unset($err);
	switch(true){
		case $searchType=='batch' && !$Batches_ID:
			$err[]='Select a batch number';
		break;
		case $searchType=='category' && !$Category:
			$err[]='Select a batch number';
		break;
		case $searchType=='date' && 
			(strtotime($ReportDateFrom)===false || strtotime($ReportDateTo)===false || !trim($ReportDateFrom) || !trim($ReportDateTo)):
			$err[]='Fill in both dates';
		break;
		case $searchType=='sku' && !trim($SKU):
			$err[]='Enter a least on SKU';
		break;
	}
	if($err){
		?><p class="red"><?php echo implode('<br />',$err);?></p><?php
	}else{
		switch(true){
			case $searchType=='batch':
				$sql="SELECT i.ID, i.SKU, i.CreateDate, i.EditDate, i.SEO_FileName, 'Y' AS Exported FROM finan_items i, gen_batches_entries e WHERE i.ResourceType IS NOT NULL AND e.Batches_ID=$Batches_ID AND i.ID=e.Objects_ID GROUP BY i.ID ORDER BY SKU";
			break;
			case $searchType=='category':
				$sql="SELECT i.ID, i.SKU, i.CreateDate, i.EditDate, i.SEO_FileName, IF(COUNT(e.ID)>0,'Y','') AS Exported FROM finan_items i LEFT JOIN gen_batches_entries e ON i.ID=e.Objects_ID AND e.ObjectName='finan_items' WHERE i.ResourceType IS NOT NULL AND i.Category='$Category' GROUP BY i.ID ORDER BY i.SKU";
			break;
			case $searchType=='date':
				$sql="SELECT i.ID, i.SKU, i.CreateDate, i.EditDate, i.SEO_FileName, IF(COUNT(e.ID)>0,'Y','') AS Exported FROM finan_items i LEFT JOIN gen_batches_entries e ON i.ID=e.Objects_ID AND e.ObjectName='finan_items' WHERE i.ResourceType IS NOT NULL AND i.CreateDate BETWEEN '".date('Y-m-d',strtotime($ReportDateFrom))."' AND '".date('Y-m-d',strtotime($ReportDateTo))."' GROUP BY i.ID ORDER BY i.SKU";
			break;
			case $searchType=='sku':
				$SKU=explode(',',trim($SKU));
				foreach($SKU as $n=>$v){
					if(!trim($v))unset($SKU[$n]);
					$SKU[$n]=trim($v);
				}
				$sql="SELECT i.ID, i.SKU, i.CreateDate, i.EditDate, i.SEO_FileName, IF(COUNT(e.ID)>0,'Y','') AS Exported FROM finan_items i LEFT JOIN gen_batches_entries e ON i.ID=e.Objects_ID AND e.ObjectName='finan_items' WHERE i.ResourceType IS NOT NULL AND i.SKU IN('".implode("','",$SKU)."') GROUP BY i.ID ORDER BY i.SKU";
			break;
		}
		gmicrotime('procstart');
		$a=q($sql, O_ARRAY);
		?>
		<strong>Records in database: <?php echo $qr['count'];?></strong><br />
		<div id="processing" class="fl"><img src="/images/i/ani/ani-fb-orange.gif" width="16" height="11" /> Processing, please wait (<span id="processingCount">0</span>)..</div>
		<div id="wrong" class="fl"> </div>
	<table id="result" class="yat cb">
	  <thead>
		<tr>
		  <th>ID</th>
			  <th>Exp.</th>
			  <th>SKU</th>
			  <th>File Name</th>
			  <?php foreach($selectedOutlets as $n=>$v){ ?>
			  <th>status</th>
			  <th>Images</th>
			  <?php }?>
		</tr>
	  </thead>
	  <tbody>
		<?php
		if($a){
			set_time_limit(60*30);
			$i=0;
			foreach($a as $n=>$v){
				extract($v);
				$j++;
				?>
		<tr class="<?php echo !fmod($j,2)?'alt':''?>">
		  <td><?php echo $ID;?>
		  <?php
		  if(!$wierd)$wierd=5;
		  $k++;
		  if(!fmod($k,$wierd)){
		  	$k=0;
		  	$wierd=rand(5,8);
			?><script language="javascript" type="text/javascript">
			g('processingCount').innerHTML=<?php echo $j;?>;
			</script><?php
		  }
		  ?></td>
			  <td class="tac"><?php echo $Exported;?>&nbsp;</td>
			  <td><a href="products.php?Items_ID=<?php echo $ID;?>" title="view/edit this map" onclick="return ow(this.href,'l1_items','800,700');"><?php echo $SKU;?></a></td>
			  <td><a target="_blank" <?php echo strlen($SEO_Filename)>50?'class="red"':''?> href="http://www.historicmapsrestored.com/<?php echo $SEO_FileName;?>.html"><?php echo $SEO_FileName;?></a></td>

			  <?php foreach($selectedOutlets as $n=>$v){ ?>
			  <td><?php
				$a=page_and_image($SEO_FileName,array(
				'outlet'=>$n,
				));
				echo $statuses[$a['status']];
				?></td>
				<td><?php
				if($a['status']=='200'){
					$i=0;
					foreach($a['images'] as $w){
						$i++;
						if($i>1)echo '<br />';
						?><a href="http://www.historicmapsrestored.com/<?php echo $w['name'];?>" onclick="return ow(this.href,'l1_img','600,600');"><?php
						echo end(explode('/',$w['name']));
						if($w['size'])echo ' ('.round($w['size']/1024,2).'k) '.$statuses[$w['status']];
						if($w['status']!=='200')$wrong++;
						?></a><?php
					}
				}else{
					$wrong++;
					?>&nbsp;<?php
				}
				?></td>
				<?php }?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				</tr><?php
			}
		}else{
			?><tr>
			  <td colspan="100%"><em class="gray">No records found for that criteria</em></td>
			</tr><?php
		}
		?>
		</tbody>
		</table>
		<?php
		gmicrotime('procstop');
		?>
		<script language="javascript" type="text/javascript">
		g('processing').innerHTML='Scan complete! (took <?php echo round($gmicrotime['procstop']-$gmicrotime['procstart'],2);?> seconds)';
		if(<?php echo $wrong?'true':'false';?>)g('wrong').innerHMTL='<?php echo $wrong.($wrong==1?' record ':' records ').' wrong or not found';?>';
		if(<?php echo $wrong?'true':'false';?>)g('wrong').visibility='visible';
		</script><?php
	}
}else{
	?><p class="gray">Select a search criteria above and click Search</p><?php	
}
?>
</div>
<div id="footer">
&nbsp;&nbsp;
</div>
<?php if(!$suppressForm){ ?>
</form>
<?php }?>
<?php if(!$hideCtrlSection){ ?>
<div id="showTester" title="Javascript Tester" onClick="g('tester').style.display='block';">&nbsp;</div>
<div id="tester" >
	<a href="#" onClick="g('ctrlSection').style.display='block';return false;">Show Control Section</a><br />
	<textarea name="test" cols="65" rows="4" id="test">clear_form();</textarea><br />
	<input type="button" name="button" value="Test" onClick="jsEval(g('test').value);"><br />
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