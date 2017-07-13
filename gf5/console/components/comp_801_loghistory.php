<?php
$historyToShow=50;

if(!$refreshComponentOnly){
	?><style type="text/css">
	
	</style>
	<script language="javascript" type="text/javascript">
	hl_bg['histopt']='#6c7093';
	hl_baseclass['histopt']='normal';
	hl_class['histopt']='hlrow';
	ogrp['histopt']=new Array();
	ogrp['histopt']['sort']='';
	ogrp['histopt']['rowId']='';
	ogrp['histopt']['highlightGroup']='histopt';
	
	function openHistory(){
		for(var j in hl_grp['histopt'])j=j.replace('r_','');
		ow('report_pagehistory.php?Logs_ID='+j,'l1_history','700,700');
	}
	</script><?php
}
?>
<div id="logHistory" refreshparams="un_username">
<table class="complexData" cellpadding="0" cellspacing="0">
<thead>
<tr>
	<th>&nbsp;</th>
	<th>Signed In</th>
	<th>Signed Out</th>
	<th>Pages Viewed</th>
	<th>IP Address&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
</tr>
</thead>
<tbody style="overflow-y:scroll;overflow-x:hidden;height:250px;">
<?php
if($logs=q("SELECT
	a.lg_ID, a.lg_masterlogin, a.lg_entertime, a.lg_exittime, a.lg_logouttime, a.lg_ipaddress, COUNT(*) AS PagesViewed
	FROM bais_logs a LEFT JOIN bais_logs_history b ON a.lg_id=b.Logs_ID AND b.Type='View' WHERE a.lg_stusername='$un_username'  GROUP BY a.lg_id ORDER BY lg_entertime DESC LIMIT ".($historyToShow+1), O_ARRAY)){
	for($i=1; $i<= (count($logs) > $historyToShow ? $historyToShow : count($logs)); $i++){
		extract($logs[$i]);
		?><tr class="normal<?php echo fmod($i,2)?' alt':''?>" id="r_<?php echo $lg_ID?>" onclick="h(this,'histopt',0,0,event);" ondblclick="h(this,'histopt',0,0,event);openHistory();" oncontextmenu="h(this,'histopt',0,1,event);">
		<td><?php echo $lg_masterlogin ? '*' : ''?></td>
		<td><?php echo t($lg_entertime, thisyear, formatDateSpaceShortTime); ?></td>
		<td><?php echo t($lg_logouttime, thisyear, formatDateSpaceShortTime);?></td>
		<td><?php echo $PagesViewed?></td>
		<td><?php echo $lg_ipaddress?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		</tr><?php
	}
	if(count($logs)>$historyToShow){
		?><tr>
		<td colspan="100%">
			<a href="javascript:alert('Not developed; only the last 50 logins are displayed');">view more..</a>		</td>
		</tr><?php
	}
}

?>
</tbody>
</table>

</div>