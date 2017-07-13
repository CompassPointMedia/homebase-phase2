<?php
/* Created 2011-02-04 by Samuel
this is the AGENT payment interface and is first use of a check in the finan_ accounting system 2.0.
how it works
	insert mode: we are showing invoices/leases that have   received a payment but have not been paid out to the agent
	update mode: we are showing transactions specifically related to that Checks_ID


*/
if(!$refreshComponentOnly){
	?><style type="text/css">
	
	</style>
	<script language="javascript" type="text/javascript">
	
	</script><?php
}
?>
<div id="agentPayment">
<div class="fr" style="width:350px;">
Sales from 2/1 including below: $4,399.00<br />
Percent tier: 
<select name="select">
  <option value=".50">50%</option>
</select>
</div>
<h1>Nathan Flaga</h1>
<p>2/1/2011 to 2/7/2011 
  <input type="submit" name="Submit" value="Update dates.." />
  <br />
  Check #
  <input name="HeaderNumber" type="text" id="HeaderNumber" size="7" />
&nbsp;&nbsp;Date:
<input name="HeaderDate" type="text" id="HeaderDate" size="12" />
</p>
<table width="100%" border="0" cellspacing="0">
  <tr>
    <th>Inv#</th>
    <th>Property</th>
    <th>Check#</th>
    <th>Inv.Amt.</th>
    <th>Amt.Paid</th>
    <th>Bal</th>
    <th>Commission</th>
    <th>Agent</th>
    <th>Notes</th>
  </tr>
  <tr>
    <td><a href="leases.php?Leases_ID=<?php echo $Leases_ID?>">116805</a></td>
    <td><a href="properties2/3.php?Units_ID=<?php echo $Units_ID?>">Cabana Beach</a> </td>
    <td><a href="payments.php?Payments_ID=<?php echo $Payments_ID?>">34842</a></td>
    <td class="tar">685.83</td>
    <td class="tar">600.00</td>
    <td><div align="center"><span class="style1">Y</span></div></td>
    <td class="tar"><input name="Commission[<?php echo $Invoices_ID?>]" type="text" id="Commission[<?php echo $Invoices_ID?>]" size="12" /></td><td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>same</td>
    <td>same</td>
    <td>same</td>
    <td>same</td>
    <td>same</td>
    <td>opt</td>
    <td>paid ck.#4194 </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tar">25,912.33</td>
    <td class="tar">24,039.11</td>
    <td>&nbsp;</td>
    <td class="tar">Total: 
    <input name="Total" type="text" id="Total" size="12" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="100%"><h3>Splits</h3></td>
  </tr>
  <tr>
    <td>Inv#</td>
    <td>Property</td>
    <td>Check# </td>
    <td class="tar">Inv.Amt.</td>
    <td class="tar">Amt.Paid</td>
    <td>Bal</td>
    <td class="tar"><input name="Commission[<?php echo $Invoices_ID?>]" type="text" id="Commission[<?php echo $Invoices_ID?>]" size="12" /></td>
    <td>Jill Hathaway </td>
    <td><span class="tar">(25%)</span> | $25.00</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tar">&nbsp;</td>
    <td class="tar">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="tar">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>