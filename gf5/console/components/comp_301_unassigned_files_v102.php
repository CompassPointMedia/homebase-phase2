<?php
$tzDifference=-1;
$maxFileProcessSize=170; //MB
$workableHeight=2000; //was 1300
$pendingMasterFolder='pending_master';
$pendingWorkingFolder='pending';

if($p_w_f=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$pendingWorkingFolder, array('positiveFilters'=>'\.(jpg|gif|png|svg)$',))){
	foreach($p_w_f as $n=>$v){
		//any other operations
	}
}else{
	$p_w_f=array();
}
$p_m=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$pendingMasterFolder, array('positiveFilters'=>'\.(jpg|gif|png|svg)$',));

if(count($p_m)){
	foreach($p_m as $n=>$v){
		if(!$p_w_f[$n]){
			if($v['size'] > $maxFileProcessSize*1024*1024){
				?><span class="red">File <?php echo $v['name'];?> is over max processing size of <?php echo $maxFileProcessSize;?>MB</span><br /><?php
				continue;
			}
			$p_m[$n]['process']=$needToProcess=true;
		}
	}
}
if(count($p_w_f)){
	foreach($p_w_f as $n=>$v){
		if(!$p_m[$n]){
			$pwfSkip++;
			$p_w_f[$n]['skip']=true;
			?><span class="red">Error: file &lt;<?php echo $v['name'];?>&gt; is not in pending_master folder!</span><br /><?php
		}
	}
}
if($needToProcess){

	//in case this takes a while
	?><div id="showPending">
	<span id="showPendingStatus">
	<img src="/images/i/ani/ani-fb-orange.gif" width="16" height="11" alt="processing.." /> processing a few new images, this may take a few seconds..</span><br />
	<?php
	flush();
	foreach($p_m as $n=>$v){
		//already processed
		if(!$v['process'])continue;

		if(!$p_w_f[$n]['name']){
			echo('processing file '.$v['name'].'...<br />');
			if(!$gmicrotime['startprocess'])gmicrotime('startprocess');

			//prn('processing '.$v['name'].'('.$v['size'].')');
			//2012-07-14 here we were copying new files in pending_master -> pending(workable).  However we want to now first rename any file that is already in the database and named as such


			//------------------------ begin interlock checking -----------------------------
			/*
			2012-07-15 the concept here is that the user COULD upload a file like Alaska.jpg with the same name.  Also they could upload a file with the same byte size regardless of the name.  In the latter case an email is sent to an administrator on this matter.[1]  So, ON INITIAL COPY-OVER FROM PENDING_MASTER TO PENDING (the working image folder), the file is renamed if that file name is already in the products database
			
			
			*/
			if($dupe=q("SELECT ID, SKU, Name FROM finan_items WHERE HBS_OriginalFileName='".addslashes($v['name'])."'",O_ROW)){
				//mail admin of possible duplicate
				prn($qr);
				if(!$mailed2)$mailed2=mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals('duplicate file name on the unassigned maps page'),$fromHdrBugs);
				?><span class="red">Duplicate filename for map <a href="products.php?Items_ID=<?php echo $dupe['ID'];?>" onclick="return ow(this.href,'l1_items','850,700');" title="view this map"><?php echo $dupe['SKU'];?></a>.  File <?php echo $v['name']?> not processed</span><br /><?php
				continue;
			}else if($dupe=q("SELECT ID, SKU, Name FROM finan_items WHERE FileSize='".round($v['size']*1024,0)."'", O_ROW)){
				//the byte size of this file is in the database
				//mail admin of this with a link back
				if(!$mailed1)$mailed1=mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals('duplicate file size on the unassigned maps page'),$fromHdrBugs);
				?><span class="red">Duplicate filesize (<?php echo round($v['size'],3);?>K) for map <a href="products.php?Items_ID=<?php echo $dupe['ID'];?>" onclick="return ow(this.href,'l1_items','850,700');" title="view this map"><?php echo $dupe['SKU'];?></a>.  File <?php echo $v['name']?> not processed</span><br /><?php
				continue;
			}
			
			//------------------------ end interlock checking -----------------------------
			
			$source=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$pendingMasterFolder.'/'.$v['name'];
			$target=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$pendingWorkingFolder.'/'.$v['name'];
			/*
			$mult=$v['height']/$workableHeight;
			$newW=round($v['width']/$mult,0);
			$newH=$workableHeight;
			$g2=$newW.'x'.$newH;
			*/
			
			$dim=($v['height']>$v['width']?'height':'width');
			if($v[$dim]<=$workableHeight){
				$g2=$v['width'].'x'.$v['height'];
			}else{
				$newD=$workableHeight;
				$newOD=round($v[$dim=='height'?'width':'height'] * ($workableHeight /$v[$dim]));
				$g2=($dim=='height'? $newOD.'x'.$newD : $newD.'x'.$newOD);
			}
			$str="convert -size $g2 \"$source\" -resize $g2 +profile '*' \"$target\"";	
			$newFiles[]=$v['name'];
			if(!($command=`man convert`))exit('<strong>linux convert command NOT present</strong>');
			$result=`$str`;

			$p_w_f[$n]=$p_m[$n];
			$p_w_f[$n]['width']=$newW;
			$p_w_f[$n]['height']=$newH;
			$p_w_f[$n]['area']=$newW*$newH;
			$p_w_f[$n]['size']=filesize($target);
		}else{
			//should not happen..
			mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='inconceivable!'),$fromHdrBugs);
		}
	}
	?></div>
	<?php
	gmicrotime('afterprocess');
	sleep(1);
	?><script language="javascript" type="text/javascript">
	g('showPendingStatus').style.display='none';
	</script><?php 
}
?>
<h2>Currently <?php echo count($p_w_f)-$pwfSkip;?> maps available</h2>
<?php if(count($newFiles)){ ?><h3 class="red"><?php echo count($newFiles) . ' new file'.(count($newFiles)==1?'':'s').' available:';?>
<br />
Total time to process: <?php echo number_format($gmicrotime['afterprocess'] - $gmicrotime['startprocess'],4).' seconds';?><br />
<?php
echo implode('<br />',$newFiles);
?></h3>
<?php } ?>
Click on a map to categorize it into the database <br />
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
.inProcess{
	border:1px dashed #400;
	background-color:cornsilk;
	padding-bottom:2px;
	margin:2px 0px;
	}
.inProcess a{
	padding:2px 5px;
	}
.delInProcess{
	background-color:darkred;
	color:white;
	float:right;
	width:16px;
	cursor:pointer;
	margin:1px;
	text-align:center;
	}
</style>
<script language="javascript" type="text/javascript">
function deleteMap(n){
	if(!confirm('This will permanently delete this map.  Continue?'))return false;
	g('deleteMapPending_'+n).innerHTML='<img src="/images/i/ani/ani-fb-orange.gif" width="16" height="11" /> Processing request..';
	g('deleteMapPending_'+n).style.display='block';
}
function delInProcess(ResourceToken,isme){
	if(!confirm('This will remove '+(isme?'your work on this map':'this user\'s work on this map')+' permanently. Are you sure?'))return false;
	window.open('resources/bais_01_exe.php?mode=delInProcess&ResourceToken='+ResourceToken,'w2');
}
</script>
<?php ob_start();?>
<div id="container">
<?php
ksort($p_w_f);
foreach($p_w_f as $n=>$v){
	
	if($v['skip'])continue;
	
	$disposition='250x';
	if($a=q("SELECT ID AS Items_ID, ResourceType, ResourceToken, Creator FROM finan_items WHERE HBS_OriginalFileName='".addslashes($v['name'])."'", O_ROW)){
		//currently being processed
		extract($a);
		//the url and onclick are different
		$url='products.php?ResourceToken='.$ResourceToken;
		$onclick='return ow(this.href,\'l1_items\',\'850,700\');';
		if(minroles()>ROLE_ADMIN && sun()!=$Creator)$onclick="alert('This file is being processed by $Creator; you can view but will not be able to edit their record'); ".$onclick;
		$p_w_f[$n]['creator']=$Creator;
		$p_w_f[$n]['resourcetoken']=$ResourceToken;
	}else{
		$url='products.php?FileName='.urlencode($v['name']);
		$onclick='return ow(this.href,\'l1_items\',\'850,700\',true);';
	}

	
	?><div id="r_<?php echo md5($v['name']);?>" class="img" style="width:265px;">
	<a href="<?php echo $url;?>" onclick="<?php echo $onclick;?>">
	<?php $p_w_f[$n]['img']=tree_image('images/documentation/'.$GCUserName.'/pending/'.$v['name']);?>
	</a><br />
	<h3 class="nullTop nullBottom"><a href="<?php echo $url;?>" onclick="<?php echo $onclick;?>"><?php echo $v['name'];?></a></h3>
	<?php 
	ob_start();
	$p_w_f[$n]['size']=$f=filesize($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending_master/'.$v['name']);
	$err=ob_get_contents();
	ob_end_clean();
	if($err){
		mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err),$fromHdrBugs);
		echo '<span class="red" title="'.h($err).'">error!</span>';
	}
	if($f){
		echo '<span title="'.$f.' bytes total">'.round($f/1024/1024,3).'M</span>';
	}
	?><br />
	<span class="gray">Added to list <?php $e=(stat($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/pending/'.$v['name']));
	$p_w_f[$n]['ctime']=$e['ctime']+(3600*$tzDifference);
	echo date('n/j/Y g:i:s A',$p_w_f[$n]['ctime']);
	?></span>
	<?php echo $p_m[strtolower($v['name'])]['width']. 'x'.$p_m[strtolower($v['name'])]['height'];?>&nbsp;&nbsp;&nbsp;
	<?php if(minroles()<=ROLE_ADMIN){ ?>
	[<a href="/gf5/console/resources/bais_01_exe.php?mode=deleteMapPending&FileName=<?php echo urlencode($v['name']);?>" target="w2" title="Delete this physical file from the pending folder" onclick="return deleteMap('<?php echo md5($v['name']);?>');">delete</a>]
	<br />
	<?php } ?>
	<div id="deleteMapPending_<?php echo md5($v['name']);?>" style="display:none;"></div>
	<?php if($a){ ?>
	<div id="p_<?php echo $ResourceToken;?>" class="inProcess">
	<?php if($Creator==sun() || minroles()<=ROLE_ADMIN){ ?><div class="delInProcess" onclick="return delInProcess('<?php echo $ResourceToken;?>', <?php echo $Creator==sun()?1:0?>);" title="Be careful! Click here to remove the work being done on this map">x</div><?php }?>
	<a style="color:darkred;" href="<?php echo $url;?>" title="View this record" onclick="<?php echo $onclick;?>">Currently being processed by <?php echo $Creator;?></a> 
	</div>
	<?php } ?>
	</div><?php
}
?>
<div class="cb"> </div>
</div>
<?php get_contents_tabsection('maps');?>
<div id="container2">
<?php
if(count($p_w_f)){
	$p_w_f=subkey_sort($p_w_f,'ctime','desc');
	?>
	<p class="gray">Maps shown are from newest to oldest.  </p>
	<table class="yat">
	<thead>
	<tr>
		<th>Date Added</th>
		<th>Name</th>
		<th>Size</th>
		<th>Dimensions</th>
		<th>Processed by</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($p_w_f as $n=>$v){ ?>
	<tr>
		<td><?php echo date('n/j/Y \a\t g:iA',$v['ctime']);?></td>
		<td><a href="<?php echo str_replace('&disposition=250x','',$v['img']['src']);?>" title="View this image" onclick="return ow(this.href,'l1_image','1000,700');"><?php echo $v['name'];?></a></td>
		<td class="tar"><?php echo number_format($v['size'],0);?></td>
		<td><?php echo $p_m[strtolower($v['name'])]['width']. 'x'.$p_m[strtolower($v['name'])]['height'];?></td>
		<td><?php 
		if($v['creator']){
			?><a href="products.php?ResourceToken=<?php echo $v['resourcetoken'];?>" onclick="return ow(this.href,'l1_items','850,700');" title="View this map"><?php echo $v['creator'];?></a><?php
		}else{
			echo '<em class="gray">nobody</em>';
		}
		?></td>
	</tr>
	<?php } ?>
	</tbody>
	</table><?php
}
?>
</div>
<?php get_contents_tabsection('list');
if(false){
	?><div style="display:none;"><?php
}
?>
<h2>How Unassigned Maps Works</h2>
<p> take a look at the image below</p>
<img src="/images/i-local/image_flow.jpg" width="900" height="1172" />
<p>
Steps
<ul>
<li>(1) 
You upload files to a folder called <code>{your_account}/pending_master</code>, labeled file1 and file2. in this example {your_accunt} is <?php echo $GCUserName;?>. </li>

<li>(2) When you call up this page (Products &gt; Show Unassigned Maps). This copies any of the new files you uploaded to pending_master over to pending. The system does several checks to make sure that the files in pending_master and pending are in synch. It will not let you process a file in pending_master which is already in pending with the same name, or with the same name or exact file size in the products database. If this is discovered, notices will be given at the head of this page until you correct this.</li>
<li>(3) click on any file/map to begin working on it. In this case above, file1.jpg is reserved in the database. If a file is reserved it will show as reserved to prevent two people from working on it at the same time.</li>
<li>(4) the new product is submitted/completed. When this is done, several things happen:
  <ul>
    <li>the file in pending is moved to your root folder. This is the &quot;working copy&quot; of the master file which is often huge</li>
    <li>the master file in pending_master is moved to a folder called &quot;master&quot;, but is renamed to the SKU # of the product you created</li>
    <li>the file size and original file name of that map is stored for that product in the database to prevent duplicate file sizes and names from being processed (i.e the same map twice)  </li>
  </ul>
</li>
</ul>
</p>
<?php
if(false){
	?></div><?php
}
get_contents_tabsection('help');

tabs_enhanced(array(
	'maps'=>array(
		'label'=>'Maps'
	),
	'list'=>array(
		'label'=>'List'
	),
	'help'=>array(
		'label'=>'Help'
	),
));
?>