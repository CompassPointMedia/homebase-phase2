<p id="propertyClientList">Client: 
<select class="th1" name="Clients_ID" id="Clients_ID" onchange="dChge(this); addNewClient(this);">
	<option value="">&lt;Select..&gt;</option>
	<option value="{RBADDNEW}" style="background-color:thistle;">&lt; Add New.. &gt;</option>
	<?php
	$clients=q("SELECT
	c.ID, c.ClientName, c.CompanyName, c.PrimaryFirstName, c.PrimaryLastName, 
	c.Address1,
	IF(c.City='','(city not listed)',c.City) AS City,
	c.State,
	c.Zip,
	IF(c.Country, c.Country, 'USA') AS Country
	
	FROM finan_clients c LEFT JOIN gl_properties p ON c.ID=p.Clients_ID
	GROUP BY c.ID 
	ORDER BY 
	IF(c.State!='' AND c.State!='".$apSettings['defaultState']."',2,1), 
	IF(c.City='' OR c.City NOT REGEXP('^[a-z]+'),2,1),
	c.City,
	c.ClientName", O_ARRAY);
	$i=0;
	foreach($clients as $n=>$v){
		$i++;
		if(strlen($v['State']) && strtolower($v['State'])!=strtolower($apSettings['defaultState']))($outOfState=true);
		
		if(strtolower($currentCity)!==strtolower($outOfState ? $v['State'] : $v['City'])){
			$currentCity=($outOfState ? $v['State'] : $v['City']);
			if($i==1)echo '</optgroup>';
			if($outOfState && !$outOfStatePrinted)echo $outOfStatePrinted='<optgroup label="Out of State"> </optgroup>';
			?><optgroup label="<?php echo ($outOfState ? $v['State'] : $v['City']);?>"><?php
		}
		
		?><option value="<?php echo $v['ID']?>" <?php echo $Clients_ID==$v['ID']?'selected':''?>><?php echo $v['ClientName']?></option><?php
	}
	?>
	</optgroup>
	<?php
	reset($clients);
	?>
	<optgroup label="(alphabetically)">
	<?php
	foreach($clients as $n=>$v){
		?><option value="<?php echo $v['ID']?>"><?php echo $v['ClientName']?></option><?php
	}
	?>
	</optgroup>
</select>
<a href="clients.php?Clients_ID=" title="View/edit client record" onclick="if(g('Clients_ID').value=='' || g('Clients_ID').value=='{RBADDNEW}'){ alert('Select a client first'); return false; } return ow(this.href+g('Clients_ID').value,'l1_clients','750,650');"><img src="/images/i/edit2.gif" alt="edit" /></a>
</p>