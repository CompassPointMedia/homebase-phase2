<?php
/*
00. todo
--------
	DONE	remove bluescreen error
	DONE	wrap in form
	DONE	invoices() showing - -- fix this
	DONE	test the sorting out
	DONE	remove active/inactive
	NODE	** remove edit
	DONE	implement late charge
	add print tool
	have voiding working
	TIME TO INTRODUCE CTRL-D AND CTRL-H
	implement discrepancy
	
	contextualize the deletion feature

	
	lean out the layout some, more twd report  - like SFC activity reports
	hightlight color - retain even though a report - maybe ctrl-clicking

*/



/*
0. first require the two amigos at the bottom in latest version (for this it was precoding_v104, and component_v124)

DOCUMENTATION
-------------
This code block is a clean-up from other datasets I have created, grouped by how they affect the dataset, and in order of criticality.  Actually, a dataset can function off of a view or table with as little as the variables 1) $dataset and 2) $datasetTable.  Everything else configures the features and layout of that data.
*/


/*
1. first we declare the dataset name - must be unique throughout our system
Dataset Group is a concept that groups several datasets of the same basic information.  Dataset COMPONENT is the HTML identity of the object and is referenced in refreshing the component.  Currently (2011-04-02) datasetGroup is not used or significant other than it is the node-key of availableCols.
*/
$dataset='invoices';
$datasetGroup=$dataset;					//so far (2011-04-02) always same as dataset
$datasetComponent='invoiceList';		//used in HTML div id for component wrap (used for refresh and etc.)
if(!$datasetWord)$datasetWord='Invoice';
if(!$datasetWordPlural)$datasetWordPlural='Invoices';

if(false)$datasetDebug['query']=md5($MASTER_PASSWORD);
$datasetReferenceFile=end(explode('/',__FILE__));
$datasetReferenceFileKey=md5('salt:hydroponic'.$MASTER_PASSWORD);

/*
1b. NOW that we have dataset handle declared, we can add other pre-coding type features.  These features should be geared toward keying ot the dataset and datasetComponent
*/







/* --------------------------------- selection widget -------------------------------- */
if(!function_exists('widget_selection'))require($FUNCTION_ROOT.'/function_widget_selection_v100.php');
$paidStatusOutput=widget_selection(array(
	'filterNode' => 'invoiceStatuses',
	'optionLabel' => 'invoice status',
	'varGroup' => 'invoices',
	'anchorLabel' => 'Pay status',
	'anchorImgPath' => '/images/assets/i-payment.jpg',
	'selOptions' => array(
		1=>'Unpaid',
		2=>'Partially Paid',
		3=>'Paid',
	)
));
//woo hoooooooo!!!!
$dueStatusOutput=widget_selection(array(
	'filterNode' => 'invoiceDueStatuses',
	'optionLabel' => 'Due status',
	'varGroup' => 'invoices',
	'anchorLabel' => 'Due status',
	'anchorImgPath' => '/images/assets/i-due.jpg',
	'selOptions' => array(
		1=>'Forecasted Invoice (FI)',
		2=>'Due Invoice (DUE)',
		3=>'Past Due Invoice (PASTD)',
	)
));







/* -------------- now use the selection widget statusSet to customize the invoice query ------------- */
$statuses=array();
for($i=1;$i<=3;$i++){
	if(in_array($i,$widget_selection['invoiceStatuses']['statusSet']))$statuses[]=$i;
}
$statuses=implode(',',$statuses);
/*
balance = amountapplied + originaltotal + latecharge

				---------------------------------- first -----------------	 --------- latecharge -------
latecharge = if(status=1 OR (status=0 AND balance AND duedate over 30 days), if(latecharge,latecharge,10), 0)
latecharge = IF(i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND duedate over 30 days), IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 0.00)

//final result
balance = amountapplied + originaltotal + 
IF(
	i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
	IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 
	0.00
)

1 - unpaid
2 - partially paid
3 - paid

1 = NO PAYMENTS APPLIED
1,2 = NOT PAID IN FULL (balance > 0)
1,3 = !(amount applied and balance > 0)
2, = balance > 0 and amount applied
2,3 = amount applied
3 = balance = 0
*/
switch($statuses){
	case '1,2,3':
		$statusQuery='';
	break;
	case '1':
		$statusQuery='AND i.AmountApplied=0';
	break;
	case '1,2':
		$statusQuery='AND
		IF(
			i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
			IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 
			0.00
		) > 0';
	break;
	case '1,3':
		$statusQuery='AND !(i.AmountApplied > 0 AND 
		IF(
			i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
			IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 
			0.00
		) > 0)';
	break;
	case '2':
		$statusQuery='AND (i.AmountApplied > 0 AND 
		IF(
			i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
			IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 
			0.00
		) > 0)';
	break;
	case '2,3':
		$statusQuery='AND i.AmountApplied > 0';
	break;
	case '3':
		$statusQuery='AND 
		IF(
			i.GLF_LateStatus=1 OR (i.GLF_LateStatus=0 AND (i.AmountApplied + i.OriginalTotal<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
			IF(i.GLF_LateCharge,i.GLF_LateCharge,10.00), 
			0.00
		)
		=0';
	break;
}











































/*
2. next we declare the datasetTable; if datasetFieldList=* then no initial sort for user will be declared
	- datasetFieldList optional, default value will be *; better to use a comma separated list
	- datasetTable (table or view)
	- datasetQuery (alternate method, leave blank if not used)
	- datasetID default=ID - primary key of the dataset
	- datasetArrayType default=O_ARRAY_ASSOC
*/
$datasetTable='';
$datasetID='Leases_ID';
$datasetArrayType=O_ARRAY_ASSOC;
$datasetQueryValidation=md5($MASTER_PASSWORD);
$datasetQuery="SELECT
i.*, 
l.Rent,
l.ID AS Leases_ID, 
l.Agents_username, 
l.LeaseSignDate,
l.LeaseStartDate,
l.LeaseEndDate,
l.LeaseTerminationDate,
l.UnitNumber,
Verification_ID,
VerificationDetails,
Escort,
u.un_firstname, 
un_lastname,
c.FirstName,
c.LastName,
GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) AS DateDueFrom
FROM 
_v_x_finan_headers_master i, 
gl_LeasesTransactions lt, 
gl_leases l LEFT JOIN bais_universal u ON l.Agents_username=u.un_username,
gl_LeasesContacts lc,
addr_contacts c
WHERE
l.ID=lc.Leases_ID AND
lc.Contacts_ID=c.ID AND lc.Type='Primary' AND
/* ---- (i.AmountApplied + i.OriginalTotal < 0  OR listed_invoices) AND ---- */
i.HeaderType='Invoice' AND
i.Transactions_ID=lt.Transactions_ID AND 
lt.Leases_ID=l.ID $statusQuery";


/*
***** AT THIS POINT THE DATASET WILL BEGIN TO SHOW THE "NATURAL" COLUMNS *****
	- active/inactive column
	- delete rows
	- focus control
	- default colors and alt highlighting
	- hightlight select

*/
//active/inactive toggle
$datasetActiveHideControl=true;
$datasetActiveUsage=true;
$datasetActiveActiveExpression='Active=1';
$datasetActiveInactiveExpression='Active=0';
$datasetActiveAllExpression='1';
$datasetActiveField='Active';
$datasetActiveActivateTitle='Make this item active';
$datasetActiveInactivateTitle='Make this item inactive';
$datasetActiveControl='someFunction(".$$datasetID.", ".($$datasetActiveField?1:0).");';
//allow this parameter to be passed remotely
if(!isset($datasetActiveHideControl))$datasetActiveHideControl=false;

//deletion of rows
$datasetDeleteMode='deleteInvoice';
$datasetShowDeletion=true;

//focus control
$datasetFocusPage='leases.php';
$datasetFocusAddObjectJSFunction='ow(this.href,\'l1_leases\',\'800,700\',true);'; //this is because opening an object is not well developed yet
$datasetFocusQueryStringKey='Leases_ID';

$datasetFocusViewDeviceFunction='';		//not used initially
$datasetAdditionalClassFunction='';		//not used initially


//batching
$datasetDefaultBatch=1000;				//will be 50 if not declared, or if globalBatchThreshold not declared

//sorting
### see the variables datasetReferenceFile[Key] above


//breaks and grouping
$datasetShowBreaks=false;
$datasetBreakFields=array(
	1=>array(
		'column'=>'Office',
		'blank'=>'not specified'
	),
	2=>array(
		'column'=>'HomeName',
		'blank'=>'not specified'
	)
);


/*
4. now we declare availableCols.  Note that datasetGroup must be declared as it is a node in the array
	- this will override the "natural" columns in the datasetTable
	- this brings up the point that the protocol on availableCols is unmanageably difficult, and I need a GUI on this	
*/
$modApType='embedded';
$modApHandle='first';
ob_start();
?>
Print<br />
<span class="large">
<a href="#" onclick="return checkAll(true);">+</a> <a href="#" onclick="return checkAll(false);">-</a>
</span>
<?php
$printWithAllNone=ob_get_contents();
ob_end_clean();

$availableCols[$datasetGroup][$modApType][$modApHandle]=array(
	'scheme'=>array(
		'Print'=>array(
			/*tool*/
			'header'=>$printWithAllNone,
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Print")',
		),
		'HeaderNumber'=>array(
			'header'=>'Inv #',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Number")',
			'orderBy'=>'CAST(HeaderNumber AS UNSIGNED)',
		),
		'Status'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Status")',
		),
		'DateDueFrom'=>array(
			'header'=>'Due',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Gen:Date","DateDueFrom")',
		),
		'PrimaryTenant'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Tenant")',
		),
		'Agent'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Agent")',
			'nowrap'=>true,
			'colAttrib'=>array(
				'nowrap'=>'nowrap',
			),
		),
		'UnitAddress'=>array(
			'header'=>'Unit/Addr',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:UnitAddress")',
		),
		'LeaseStartDate'=>array(
			'header'=>'Move-in',
			'datatype'=>'date',
		),
		'Rent'=>array(
			'header'=>'Rent Amt',
			
		),
		'Total'=>array(
			'header'=>'Invoice Amt',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Gen:Dollar", "OriginalTotal", 
			array(
				"negative"=>1, 
			))',
			'colattribs'=>array(
				'id'=>'total_',
			),
		),
		'LateFee'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:LateFee")',
		),
		'AmountApplied'=>array(
			'header'=>'Prior Pymt',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Applied")',
		),
		'Balance'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Balance")',
		),
		'Void'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Void")',
		),
		'Flag'=>array(
			'header'=>'New Discrepancy',
		),
		'Verification_ID'=>array(
			'header'=>'Verify',
		),
	),
);

//appearance
$datasetTheme='';
$datasetFooterDisposition='tabularControls'; 	//however, the footer needs to show to nav a large batch like this
$datasetHideFooterAddLink=true;
$datasetHideColumnSelection=false;

ob_start();
?>
<div>
<h3 class="nullBottom">Invoice <span id="latefeeInvoiceNbr"> </span></h3>
<br />
<label><input name="GLF_LateStatus" type="radio" value="-1" id="GLF_LateStatus-1" onchange="g('lateChargeUpdate').disabled=false;" /> 
Override</label><br />
<label><input name="GLF_LateStatus" type="radio" value="0" checked="checked" id="GLF_LateStatus0" onchange="g('lateChargeUpdate').disabled=false;" /> 
Use due date at 30 days</label><br />
<label><input name="GLF_LateStatus" type="radio" value="1" id="GLF_LateStatus1" onchange="g('lateChargeUpdate').disabled=false;" />
Force late fee</label><br />
<br />
Charge of: 
<input name="GLF_LateCharge" type="text" id="GLF_LateCharge" value="<?php echo number_format($applicationLateFee,2);?>" size="7" onchange="g('lateChargeUpdate').disabled=false;" />
&nbsp;
<input type="button" name="Button" id="lateChargeUpdate" value="Update" disabled="disabled" onclick="lateCharge(this);" /> <span id="lateChargePending" style="display:none;"><img src="/images/i/ani/ani-gif-bars-ltgreen.gif" width="43" height="11" alt="processing.." /></span>
</div>
<?php
$inner=ob_get_contents();
ob_end_clean();

//--------------------------- functions only from here ------------------------------
if(!function_exists('write_menu'))require($FUNCTION_ROOT.'/function_write_menu_v100.php');

function colConfig($param,$field='',$options=array()){
	global $record, $datasetID, $submode,$qr, $developerEmail, $fromHdrBugs, $modApType, $modApHandle;
	$param=strtolower($param);
	$a=$record;
	extract($a);
	extract($options);
	ob_start();
	switch($param){
		case 'gen:dollar':
			if($absolute)$$field=abs($$field);
			if($negative)$$field *= -1;
			if($$field==0 && $nozero){
				if(is_string($nozero))echo $nozero;
				break;
			}
			echo (isset($currency) ? (is_string($currency) ? $currency : '$') : '').number_format($$field, (strlen($decimals)?$decimals : 2));
		break;
		case 'gen:date':
			echo t($$field, f_qbks);
		break;
		case 'inv:status':
			if($OriginalTotal + $AmountApplied >= 0 /* and we have to consider the late fee here */){
				echo 'PAID';
			}else{
				if($AmountApplied > 0){
					echo 'PP/';
				}
				if($LeaseStartDate > date('Y-m-d')){
					echo 'FI';
				}else if($DateDueFrom <= date('Y-m-d',strtotime('-30 days'))){
					echo 'PASTD';
				}else{
					echo 'DUE';
				}
			}
		break;
		case 'inv:tenant':
			echo $FirstName.' '.$LastName;
		break;
		case 'inv:agent':
			echo $un_firstname ? $un_firstname.' '.$un_lastname : $Agents_username;
		break;
		case 'inv:unitaddress':
			echo $UnitNumber;
		break;
		case 'inv:balance':
			global $thisLateFee;
			$bal=abs($OriginalTotal + $AmountApplied)+$thisLateFee;
			if($bal==0){
				?><span id="balance_<?php echo $$datasetID;?>" class="gray">pd.</span><?php
			}else{
				?><span id="balance_<?php echo $$datasetID;?>"><?php echo number_format($bal,2);?></span><?php
			}
		break;
		case 'inv:void':
			?><select name="Status[<?php echo $ID?>]" id="void<?php echo $ID?>" onchange="dChge(this);" class="smallSelect">
			<option value="">Select Reason</option>
			<?php
			global $voidreasons;
			if(!$voidreasons)$voidreasons=q("SELECT ID, Name FROM gl_voidreasons", O_COL_ASSOC);
			foreach($voidreasons as $n=>$v){
				?><option value="<?php echo $n?>" <?php echo $GLF_VoidReasons_ID==$n?'selected':''?>><?php echo h($v);?></option><?php
			}
			?>
			</select><?php
		break;
		case 'inv:number':
			?><a href="leases.php?Leases_ID=<?php echo $Leases_ID?>" onclick="return ow(this.href,'l1_leases','700,800');" title="click to view and edit this lease"><?php echo $HeaderNumber?></a><?php
		break;
		case 'inv:applied':
			if($Distributions>1){
				?><a id="pymt_<?php echo $$datasetID?>" href="history.php?Headers_ID=<?php echo $ID?>" onclick="return ow(this.href,'l1_pymthistorybubble','500,230')" title="This invoice has multiple payments; click to view list"><?php echo number_format($AmountApplied,2);?></a><?php
			}else if($Distributions==1){
				?><a id="pymt_<?php echo $$datasetID?>" href="payments.php?Payments_ID=<?php echo q("SELECT Headers_ID FROM finan_transactions WHERE ID='$ParentTransactions_ID'", O_VALUE);?>" onclick="return ow(this.href,'l1_payments','900,600');" title="Click to view this payment"><?php echo number_format($AmountApplied,2);?></a><?php
			}else{
				?><span id="pymt_<?php echo $$datasetID?>" class="ghost">0.00</span><?php
			}
		break;
		case 'inv:latefee':
			global $applicationLateFee, $thisLateFee;
			$thisLateFee=0;
			if(
				/* force */
				$GLF_LateStatus==1 || 
				/* neutral and balance, but late */
				($GLF_LateStatus==0 && abs($OriginalTotal + $AmountApplied)>0 && $DateDueFrom<date('Y-m-d', strtotime('-30 days')))){

				$thisLateFee=($GLF_LateCharge>0 ? $GLF_LateCharge : $applicationLateFee);

				?><span id="latefee_<?php echo $$datasetID?>" class="red" oncontextmenu="return false;" onclick="hidemenuie5(event,1); showmenuie5(event,1);"><?php echo number_format($thisLateFee,2);?></span><?php
			}else if($GLF_LateStatus==-1){
				//override
				?><span id="latefee_<?php echo $$datasetID?>" class="<?php if($DateDueFrom  < date('Y-m-d', strtotime('-30 days'))/*it is past due*/)echo 'red';?>" oncontextmenu="return false;" onclick="hidemenuie5(event,1); showmenuie5(event,1);">0.00</span><?php
			}else{
				echo '&nbsp;';
			}
			?><input type="hidden" id="GLF_LateStatusCharge<?php echo $Leases_ID?>" value="<?php echo $GLF_LateStatus.':'.$GLF_LateCharge.':'.$HeaderNumber;?>" /><?php
		break;
		case 'inv:print':
			?>
			<input type="checkbox" name="print[<?php echo $Leases_ID?>]" id="print_<?php echo $Leases_ID?>" value="1" />
<?php
		break;
		default:
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}

require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v104.php');


write_menu($options=array(
	'type'=>'toddler',
	'objectRegex'=>'^latefee_',
	'menuID'=>'definitionTools',
	'precalculated'=>'lateChargeInfo(event)',
	'inner'=>$inner,
));

ob_start();
//content including controls between titlage and dataset table
?>
<form name="form1" id="form1" action="resources/bais_01_exe.php" method="post">
<span class="gray">instructions here and overview of statuses to show also</span><br />


<?php
$datasetPreContent=get_contents();
?>
<div class="fr">
	
	<?php
	echo $paidStatusOutput;
	?>
	<?php 
	echo $dueStatusOutput;
	?>
	<div class="frb">
	<label>
	<input type="checkbox" name="IncludeVoids" value="1" /> 
	Include Voids 
	</label>
	</div>
	<div class="frb">
	[<a href="javascript:alert('to be developed soon');">print selected</a> | <a href="javascript:alert('this will send selected invoices to PM companies by email');">send selected</a>]
	</div>
	<input name="mode" type="hidden" id="mode" value="refreshComponent" />
	<input name="submode" type="hidden" id="submode" />
	<input name="component" type="hidden" id="component" value="invoices" />  
</div>
<?php
$datasetPreSubContent=get_contents();

//html output items prior to dataset
if(!$refreshComponentOnly){
	?><style type="text/css">
	/* -- from filter gadget -- */
	<?php echo $filterGadgetCSS?>
	<?php
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'8b4513',
		'datasetColorRowAlt_'=>'f1e8e2',
		'datasetColorSorted_'=>'wheat',
	));
	?>
	.frb{
		float:right;
		margin-left:5px;
		}
	.flb{
		float:left;
		margin-right:8px;
		}
	.red{
		color:darkred;
		}
	.smallSelect{
		width:100px;
		}
	.smallSelect option{
		font-size:smaller;
		}
	.large{
		font-size:215%;
		font-weight:900;
		}
	</style>
	<script language="javascript" type="text/javascript">
	/* -- from filter gadget -- */
	<?php echo $filterGadgetJS?>
	function lateChargeInfo(e){
		if(!e) var e = window.event;
		e=GetSourceElement(e);
		SelectedLeases_ID=getParentMatching(e,/^r_/).id.replace('r_','');
		var a=g('GLF_LateStatusCharge'+SelectedLeases_ID).value.split(':');
		g('GLF_LateStatus'+(a[0]?a[0]:'0')).checked=true;
		g('GLF_LateCharge').value=(a[1]!='0.00' ? a[1] : '<?php echo number_format($applicationLateFee,2);?>');
		g('latefeeInvoiceNbr').innerHTML=a[2];
		g('lateChargePending').style.display='none';
	}
	function lateCharge(){
		g('lateChargePending').style.display='inherit';
		g('lateChargeUpdate').disabled=true;
		//get vars
		if(g('GLF_LateStatus-1').checked){
			GLF_LateStatus=-1;
		}else if(g('GLF_LateStatus0').checked){
			GLF_LateStatus=0;
		}else if(g('GLF_LateStatus1').checked){
			GLF_LateStatus=1;
		}
		window.open('/gf5/console/resources/bais_01_exe.php?mode=updateLateStatus&Leases_ID='+SelectedLeases_ID+'&GLF_LateStatus='+GLF_LateStatus+'&GLF_LateCharge='+escape(g('GLF_LateCharge').value), 'w2');
	}
	function checkAll(n){
		var a=g('form1').getElementsByTagName('input');
		for(var i in a){
			try{
			if(!a[i].id.match(/print_/))continue;
			a[i].checked=n;
			}catch(e){ }
		}
		return false;
	}
	</script>
<?php
}


require($MASTER_COMPONENT_ROOT.'/dataset_component_v124.php');

?></form>