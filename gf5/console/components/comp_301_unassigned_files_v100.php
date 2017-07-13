<?php
$workableHeight=1200;
$pendingMasterFolder='pending_master';
$pendingWorkingFolder='pending';
$disposition='x212';

if($a=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$pendingWorkingFolder, array('positiveFilters'=>'\.(jpg|gif|png|svg)$',))){
	foreach($a as $n=>$v){
		//any other operations
	}
}else{
	$a=array();
}
if($b=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$pendingMasterFolder, array('positiveFilters'=>'\.(jpg|gif|png|svg)$',))){
	foreach($b as $n=>$v){
		if(!$a[$n]['name']){
			//continue;
			if(!$showPending){
				$showPending=true;
				?><div id="showPending">
				<img src="/images/i/ani/ani-fb-orange.gif" width="16" height="11" alt="processing.." /> processing a few new images, this may take a few seconds..
				</div><?php
			}
			
			$source=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$pendingMasterFolder.'/'.$v['name'];
			$target=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$pendingWorkingFolder.'/'.$v['name'];

			$g=getimagesize($source);
			$g=($w=$g[0]).'x'.($h=$g[1]);
			$mult=$h/$workableHeight;
			$newW=round($w/$mult,0);
			$newH=$workableHeight;
			$g2=$newW.'x'.$newH;			
			$str="convert -size $g2 \"$source\" -resize $g2 +profile '*' \"$target\"";			
			$result=`$str`;
			$a[$n]=$b[$n];
			$a[$n]['width']=$newW;
			$a[$n]['height']=$newH;
			$a[$n]['area']=$newW*$newH;
			$a[$n]['size']=filesize($target);
		}
	}	
}
if($showPending){
	$showPending=false;
	?><script language="javascript" type="text/javascript">
	g('showPending').style.display='none';
	</script><?php
}

?>
<h2>Currently <?php echo count($a);?> maps available</h2>

Click on a map to categorize it into the products list<br />
<style>
#container{
	/* width:85%; */
	height:400px;
	overflow:scroll; 
	border:1px solid darkred;
	padding:5px;
	}
.img{
	float:left; 
	border:1px solid #ccc; 
	padding:5px; 
	margin:0px 15px 15px 0px;
	}
#container a h3{
	color:darkgreen;
	}
#container a{
	color:#000;
	}
</style>

<div id="container">
<?php
ksort($a);
foreach($a as $v){
	$disposition='250x';
	?><div id="r_<?php echo md5($v['name']);?>" class="img" style="width:265px;">
	<a href="products.php?FileName=<?php echo urlencode($v['name']);?>" onclick="return ow(this.href,'l1_items','750,700',true);">
	<?php tree_image('images/documentation/'.$GCUserName.'/pending/'.$v['name']);?><br />

	<h3 class="nullTop nullBottom"><?php echo $v['name'];?></h3>
	<?php echo $v['width']. 'x'.$v['height'];?><br />
	</a>
	
	</div><?php
}
?>
<div class="cb"> </div>
</div>
