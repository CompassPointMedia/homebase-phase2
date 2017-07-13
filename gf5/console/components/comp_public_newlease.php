<?php
/* 2012-05-12
this is unique; this is the first public form that can show on another server via writer.php

*/

?>
<link rel="stylesheet" href="//<?php echo $GCUserName?>.fantasticshop.com/Library/css/cssreset01.css" type="text/css" />
<script language="JavaScript" type="text/javascript" src="//<?php echo $GCUserName?>.fantasticshop.com/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="//<?php echo $GCUserName?>.fantasticshop.com/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="//<?php echo $GCUserName?>.fantasticshop.com/Library/js/forms_04_i1.js"></script>
<form name="form1" id="form1" method="post" target="w2" action="//<?php echo $GCUserName?>.fantasticshop.com/gf5/console/resources/bais_01_exe.php?mode=publicInsertLease">

<h1>Request to Lease a Property</h1>




<p class="gray"><?php echo $AcctCompanyAbbr;?> would be happy to locate a tenant for your house, duplex or townhome!  Please fill in the information below accurately in order to speed up the process of getting started.  Someone will be calling you within 1 business day of your application.</p>
	<br>
  Your contact info:<br>
  First name: 
  <input name="FirstName" type="text" id="FirstName"> 
  Last name: 
  <input name="LastName" type="text" id="LastName">
  <br>
  Email address: 
  <input name="Email" type="text" id="Email">
  <br>
Cell phone: 
<input name="HomeMobile" type="text" id="HomeMobile">
<br>
Other phone number: 
<input name="Phone" type="text" id="Phone">
<br>
<br>
How did you hear about us?: 
<input name="Referral" type="text" id="Referral">
<br>
<br>
Address of the property you want us to lease:<br>
1204 Marlton St.<br>
San Marcos, TX 78666</p>
<p>This property is a(n): ______</p>
<p>When do you want us to find a lease for this property?: <img align="absbottom" onclick="show_cal(this,this.nextSibling.id);" title="click this to select a date" alt="select date" class="calIcon" src="//<?php echo $GCUserName?>.fantasticshop.com/images/i/calendar1.png"/>
  <input name="DesiredLeaseStart" type="text" id="DesiredLeaseStart" value="today" size="14" />
  <br>
  <br>
  What is the lenth of lease you are looking for?: ____________<br>
  What is your target rent amount (per month)?: 
  <input name="Rent" type="text" id="Rent" size="8">
  <br>
  What deposit do you wish to charge?: 
  <input name="Rent" type="text" id="Rent" size="8">
  <br>
  What utilities are included (paid)?:<br>
  <label>
  <input name="ElectricPaid" type="checkbox" id="ElectricPaid" value="1">
  Electric</label>
  <br>
  <label>
  <input name="WaterPaid" type="checkbox" id="WaterPaid" value="1">
  Water</label>
  <br>
  <label><input name="TrashPaid" type="checkbox" id="TrashPaid" value="1">
  Trash</label><br>
  <label><input name="Gas" type="checkbox" id="Gas" value="1">
  Gas </label>
  <select name="select">
  </select>
  <br>
  <br>
  Give a brief overview/summary of this property:<br>
  <textarea name="Description" cols="45" rows="3" id="Description"></textarea>
  <br>
  <br>
  <input name="sumbit" type="submit" id="sumbit" value="Get Started"> 
  <br>
  <br>
    <br>
</p>
</form>
