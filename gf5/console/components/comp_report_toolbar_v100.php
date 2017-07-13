<div id="toolbar1" class="printhide">
	Show Invoices 
	<select name="targetDate" id="targetDate">
		<option value="CreateDate" <?php echo $targetDate=='CreateDate'?'selected':''?>>entered</option>
		<option value="LeaseSignDate" <?php echo $targetDate=='LeaseSignDate'?'selected':''?>>signed on</option>
		<option value="LeaseStartDate" <?php echo $targetDate=='LeaseStartDate'?'selected':''?>>move-in date</option>
	</select>
	from: <img align="absbottom" onclick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input name="ReportDateFrom" type="text" id="ReportDateFrom" value="<?php echo date('m/d/Y',strtotime($ReportDateFrom));?>" size="14" />
	to
	<img align="absbottom" onClick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="/images/i/calendar1.png"/><input name="ReportDateTo" type="text" id="ReportDateTo" value="<?php echo date('m/d/Y',strtotime($ReportDateTo));?>" size="14" />
	
	<input type="button" name="button" id="button1" value="Update" onClick="g('form1').setAttribute('method','get');g('form1').setAttribute('target','');g('form1').action='';g('form1').submit();return false;" /> &nbsp;
	<input type="button" name="button" id="button2" value="Print" onClick="window.print();" /> &nbsp;
	<input type="button" name="button" id="button3" value="Export" onClick="window.open('resources/bais_01_exe.php?mode=refreshComponent&component=aparcombined&suppressPrintEnv=1&submode=exportDataset&ReportDateFrom=<?php echo $ReportDateFrom;?>&ReportDateTo=<?php echo $ReportDateTo?>','w2');" /> &nbsp;
	<input type="button" name="button" id="button4" value="Close" onClick="window.close();" />&nbsp;	
</div>
<h2 class="screenhide">Report date from <?php echo date('m/d/Y',strtotime($ReportDateFrom));?> to <?php echo date('m/d/Y',strtotime($ReportDateTo));?></h2>
