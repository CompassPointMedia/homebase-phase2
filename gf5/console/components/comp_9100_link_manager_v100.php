<?php
if(!function_exists('hmr_link_manager'))require($FUNCTION_ROOT.'/function_hmr_link_manager_v101.php');
if($mode=='clearLinkManager'){
	q("UPDATE bais_universal_downloads SET Status=0 WHERE UserName='".sun()."'");
}
if(!$refreshComponentOnly){
	?><style type="text/css">
	.linkManagerBox{
		padding:5px;margin:45px;border:1px solid DARKRED;background-color:THISTLE;
		}
	.linkManagerBox td{
		border-bottom:1px dotted #666;
		}
	.linkManagerBox .highlighted td{
		background-color:lightgreen;
		}
	</style>
	<script language="javascript" type="text/javascript">
	
	</script><?php
}
?>
<div id="linkManager">
	<h1>Link Manager</h1>
	<input type="button" name="button" value="Clear Links" onClick="window.open('/gf5/console/resources/bais_01_exe.php?mode=clearLinkManager', 'w2');" />
	&nbsp;&nbsp;
	<input type="button" name="Button" value="Home Page" onclick="window.location='home.php';" />

	<table class="yat" width="100%">
	<?php
	if($links=q("SELECT * FROM bais_universal_downloads WHERE Status=1 AND UserName='".sun()."' ORDER BY IF(ID='$Downloads_ID',1,2), ID DESC", O_ARRAY)){
		foreach($links as $n=>$v){
			$p=end(explode('?',$v['Source']));
			
			hmr_link_manager($p);
			if($hmr_link_manager['error'])continue;
			if($v['ID']==$Downloads_ID){
				$currentRequest=array(
					'url'=>$url,
					'window'=>$window,
					'button'=>$button,
					'size'=>$size,
				);
			}
			if(!$linkManagerBox){
				$linkManagerBox=true;
				?><tr>
				<td colspan="100%" style="border-bottom:none;">
					<div class="linkManagerBox">
					If the window does not open, please enable popups for this site<br />
				You may also click the button to open. <br>
					</div>
				</td>
				</tr><?php
			}
			?><tr class="<?php echo $v['ID']==$Downloads_ID?'highlighted':''?>">
			<td>
			<div class="fr" style="margin:0px; padding:0px;">
			Date: <strong><?php echo date('M jS Y \a\t g:iA', strtotime($v['EditDate']));?></strong>
			</div>
			<input id="popup<?php echo $v['ID']?>" type="button" name="Button" value="<?php echo strtolower($button)=='progress note'?'Child Status Report':$button;?>" onClick="ow('<?php echo $url?>','<?php echo $window?>','<?php echo $size ? $size : '700,700'?>');" /> <?php if($v['ID']==$Downloads_ID){ ?> (this link should open automatically)<?php } ?>
			</td>
			</tr><?php
		}
	}else{
		?><tr>
		<td colspan="100%" class="gray">
		You currently have no email links avaiable
		</td>
		</tr><?php
	}
	?>
	</table>
</div>
<?php
if(!$refreshComponentOnly && $currentRequest){
	?><script language="javascript" type="text/javascript">
	ow('<?php echo $currentRequest['url']?>','<?php echo $currentRequest['window']?>','<?php echo $currentRequest['size'] ? $currentRequest['size'] : '700,700'?>');
	</script>
	<?php
}
?>