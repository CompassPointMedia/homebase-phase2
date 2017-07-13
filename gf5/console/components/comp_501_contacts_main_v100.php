<?php
if(!$searchType)$searchType='prospects';
if(!$refreshComponentOnly){
	?>
	<style type="text/css">
	#mainBody{
		width:1000px;
		border:1px solid #ccc;
		padding:10px;
		margin:10px;
		}
	#section1{
		background-color:cornsilk;
		border:1px dotted #333;
		}
	.secHead{
		font-size:100%;
		font-weight:normal;
		text-transform:uppercase;
		margin-left:-20px;
		font-family:Arial, Helvetica, sans-serif;
		}
	.section{
		padding:5px 10px 10px 25px;
		border:1px dotted #666;
		margin-bottom:10px;
		}
	.leftBox{
		width:600px;
		float:left;
		}
	.rightBox{
		width:320px;
		float:right;
		}
	</style>
	<script language="javascript" type="text/javascript">
	function ccInterlock(n){
		for(var i=1; i<=8; i++){
			g('it'+i).disabled=(n=='prospects');
		}
		g('InvoiceTypes').className=(n=='prospects'?'fl gray':'fl');
		g('MoveInDatefrom').disabled=(n=='prospects');
		g('MoveInDateto').disabled=(n=='prospects');
		g('MoveOutDatefrom').disabled=(n=='prospects');
		g('MoveOutDateto').disabled=(n=='prospects');

		g('DesiredMoveInDatefrom').disabled=(n=='customers');
		g('DesiredMoveInDateto').disabled=(n=='customers');
	}
	
	</script><?php
}

?>
<form name="form1" id="form1" action="resources/bais_01_exe.php" target="w2" method="get">
<input name="mode" type="hidden" id="mode" value="customerSearch">
Search for:<br />
<label>
<input name="searchType" type="radio" value="prospects" onchange="ccInterlock('prospects');dChge(this);" <?php echo $searchType=='prospects' || !$searchType?'checked':''?> /> 
Prospects</label>
&nbsp;&nbsp;
<label><input name="searchType" type="radio" value="customers" onchange="ccInterlock('customers');dChge(this);" <?php echo $searchType=='customers'?'checked':''?> />
Customers</label>
&nbsp;&nbsp;
<label><input name="searchType" type="radio" value="all" onchange="ccInterlock('both');dChge(this);" <?php echo $searchType=='all'?'checked':''?> />
Both</label>
<br />
<br />

<div class="fl">
Name, Phone, or Email: 
  <input name="q" type="text" id="q" value="<?php echo h($q);?>" size="35" class="myform" />
<br />
<table>
  <tr>
    <td valign="bottom" style="padding-top:10px;">Desired Move-in Date:</td>
    <td valign="middle">between
      <input name="DesiredMoveInDatefrom" type="text" class="myform" id="DesiredMoveInDatefrom" onchange="dChge(this);" value="<?php echo t($DesiredMoveInDatefrom);?>" size="10" <?php echo $searchType=='customers'?'disabled':'';?> /> 
      and
      <input name="DesiredMoveInDateto" type="text" class="myform" id="DesiredMoveInDateto" onchange="dChge(this);" value="<?php echo t($DesiredMoveInDateto);?>" size="10" <?php echo $searchType=='customers'?'disabled':'';?> /></td>
    </tr>
  <tr>
    <td valign="bottom" style="padding-top:10px;">Move-in Date:</td>
    <td>between
      <input name="MoveInDatefrom" type="text" class="myform" id="MoveInDatefrom" onchange="dChge(this);" value="<?php echo t($MoveInDatefrom);?>" size="10" <?php echo $searchType=='prospects'?'disabled':'';?> /> 
      and 
      <input name="MoveInDateto" type="text" class="myform" id="MoveInDateto" onchange="dChge(this);" value="<?php echo t($MoveInDateto);?>" size="10" <?php echo $searchType=='prospects'?'disabled':'';?> /></td>
    </tr>
  <tr>
    <td valign="baseline" style="padding-top:10px;">Move-out Date:</td>
    <td>between
      <input name="MoveOutDatefrom" type="text" class="myform" id="MoveOutDatefrom" onchange="dChge(this);" value="<?php echo t($MoveOutDatefrom);?>" size="10" <?php echo $searchType=='prospects'?'disabled':'';?> /> 
      and 
      <input name="MoveOutDateto" type="text" class="myform" id="MoveOutDateto" onchange="dChge(this);" value="<?php echo t($MoveOutDateto);?>" size="10" <?php echo $searchType=='prospects'?'disabled':'';?> /></td>
    </tr>
</table>
</div>



<div id="InvoiceTypes" class="fl<?php if($searchType=='prospects' || !$searchType)echo ' gray';?>">
  <p> Invoice type(s):</p>
  <label>
  <input name="InvoiceType[FI]" type="checkbox" id="it1" value="FI" onchange="dChge(this);" <?php echo $InvoiceType['FI']?'checked':''?> <?php echo $searchType=='prospects' || !$searchType?'disabled':'';?> />
    FI (Forecasted Invoice)</label>
  <br />
  <label>
  <input name="InvoiceType[DIS]" type="checkbox" id="it2" value="DIS" onchange="dChge(this);" <?php echo $InvoiceType['DIS']?'checked':''?> <?php echo $searchType=='prospects' || !$searchType?'disabled':'';?> />
    DIS (Discrepancy)</label>
  <br />
  <label>
  <input name="InvoiceType[DUE]" type="checkbox" id="it3" value="DUE" onchange="dChge(this);" <?php echo $InvoiceType['DUE'] || !isset($InvoiceType)?'checked':''?> <?php echo $searchType=='prospects' || !$searchType?'disabled':'';?> />
    DUE (Payment Due)</label>
  <br />
  <label>
  <input name="InvoiceType[PASTD]" type="checkbox" id="it4" value="PASTD" onchange="dChge(this);" <?php echo $InvoiceType['PASTD'] || !isset($InvoiceType)?'checked':''?> <?php echo $searchType=='prospects' || !$searchType?'disabled':'';?> />
    PASTD (Payment is Past Due)</label>
  <br />
  <label>
  <input name="InvoiceType[UNPAID]" type="checkbox" id="it5" value="PAID" onchange="dChge(this);" <?php echo $InvoiceType['UNPAID']?'checked':''?> <?php echo $searchType=='prospects' || !$searchType?'disabled':'';?> />
    UNPAID (Not Paid)</label>
  <br />
  <label>
  <input name="InvoiceType[PP]" type="checkbox" id="it6" value="PP" onchange="dChge(this);" <?php echo $InvoiceType['PP']?'checked':''?> <?php echo $searchType=='prospects' || !$searchType?'disabled':'';?> />
    PP (Partially Paid)</label>
  <br />
  <label>
  <input name="InvoiceType[PAID]" type="checkbox" id="it7" value="PAID" onchange="dChge(this);" <?php echo $InvoiceType['PAID']?'checked':''?> <?php echo $searchType=='prospects' || !$searchType?'disabled':'';?> />
    PAID (Paid in Full)</label>
  <br />
  <label>
  <input name="InvoiceType[VOID]" type="checkbox" id="it8" value="VOID" onchange="dChge(this);" <?php echo $InvoiceType['VOID']?'checked':''?> <?php echo $searchType=='prospects' || !$searchType?'disabled':'';?> />
    VOID (Voided)</label>
  <br />
  <br />
	<em class="gray">(If no options are selected, all types of invoices will be looked for)</em>
</div>
<div class="cb"> </div>
<input type="submit" name="Submit" value="Search" onclick="g('pending').innerHTML=pendingImgHTML;"> <span id="pending"></span>

<div id="customersWrap">
<?php
require('components/comp_502_dataset_contacts_v100.php');

require('components/comp_503_dataset_searches_v100.php');

?>
</div>
<div class="cb"> </div>
</form>
