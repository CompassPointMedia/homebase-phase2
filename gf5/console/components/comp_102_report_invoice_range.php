<?php
$dataset='invoices';
$datasetGroup=$dataset;					//so far (2011-04-02) always same as dataset
$datasetComponent='invoiceList';		//used in HTML div id for component wrap (used for refresh and etc.)
if(!$datasetWord)$datasetWord='Invoice';
if(!$datasetWordPlural)$datasetWordPlural='Invoices';

if($test==17)$datasetDebug['query']=md5($MASTER_PASSWORD);
$datasetReferenceFile=end(explode('/',__FILE__));
$datasetReferenceFileKey=md5('salt:hydroponic'.$MASTER_PASSWORD);
$datasetTable='';
$datasetID='Leases_ID';
$datasetArrayType=O_ARRAY_ASSOC;
$datasetQueryValidation=md5($MASTER_PASSWORD);

if(isset($changeParams['IncludeVoids'])){
	$IncludeVoids=($changeParams['IncludeVoids'] ? '' : 'i.HeaderStatus!=\'Void\' AND ');
}else{
	$IncludeVoids=($_SESSION['userSettings']['invoicesIncludeVoids'] ? '' : 'i.HeaderStatus!=\'Void\' AND ');
}

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
l.Units_ID,
pu.Properties_ID,
p.PropertyName,
Verification_ID,
VerificationDetails,
Escort,
u.un_firstname, 
un_lastname,
c.ID AS Contacts_ID,
c.FirstName,
c.LastName,
GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) AS DateDueFrom
FROM 
_v_x_finan_headers_master i, 
gl_LeasesTransactions lt, 
gl_leases l,
gl_properties_units pu,
gl_properties p,
bais_universal u,
gl_LeasesContacts lc,
addr_contacts c
WHERE
$IncludeVoids
".(minroles()>=ROLE_AGENT ? "(l.Agents_username='".sun()."' OR l.SubAgents_username='".sun()."') AND ":'')."
l.Agents_username=u.un_username AND
l.ID=lc.Leases_ID AND
l.Units_ID=pu.ID AND
pu.Properties_ID=p.ID AND
lc.Contacts_ID=c.ID AND lc.Type='Primary' AND
(i.AmountApplied + i.OriginalTotal < 0  OR /* listed_invoices*/ 0) AND 
i.HeaderType='Invoice' AND
i.Transactions_ID=lt.Transactions_ID AND 
lt.Leases_ID=l.ID AND ".
($targetDate=='LeaseEndDate' ? "DATE( IF(l.LeaseTerminationDate, l.LeaseTerminationDate, l.LeaseEndDate) )" : "DATE(l.$targetDate)").
" BETWEEN '$ReportDateFrom' AND '$ReportDateTo' AND ".
($targetDate=='LeaseEndDate' ? "(l.LeaseEndDate>0 OR l.LeaseTerminationDate>0)" : "l.$targetDate>0").
" ORDER BY un_lastname, un_firstname, l.CreateDate";

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
$datasetShowDeletion=false;



//focus control
$datasetFocusPage='leases.php';
$datasetFocusAddObjectJSFunction='ow(this.href,\'l1_leases\',\'800,700\',true);'; //this is because opening an object is not well developed yet
$datasetFocusQueryStringKey='Leases_ID';

$datasetFocusViewDeviceFunction='';		//not used initially

//batching
$datasetDefaultBatch=1000;				//will be 50 if not declared, or if globalBatchThreshold not declared

//sorting
### see the variables datasetReferenceFile[Key] above


//breaks and grouping
$datasetShowBreaks=true;
$datasetBreakFields=array(
	1=>array(
		'column'=>'un_lastname',
		'blank'=>'not specified'
	),
);
$datasetCalcFields=array(
	array(
		'name'=>'OriginalTotal',
		'calc'=>'sum',
		'fieldExpressionFunction'=>'number_format(abs($n),2)',
	),
);
/*


*/
$modApType='embedded';
$modApHandle='first';
$availableCols[$datasetGroup][$modApType][$modApHandle]=array(
	'scheme'=>array(
		'HeaderNumber'=>array(
			'header'=>'Inv #',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Number")',
			'orderBy'=>'CAST(HeaderNumber AS UNSIGNED)',
		),
		'Status'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Status")',
			'orderBy'=>'HeaderStatus $asc',
		),
		'DateDueFrom'=>array(
			'header'=>'Due',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Gen:Date","DateDueFrom")',
		),
		'PropertyName'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Property:Name")',
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
			'orderBy'=>'Agents_username $asc',
			'visibility'=> COL_HIDDEN,
		),
		'UnitAddress'=>array(
			'header'=>'Unit/Addr',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:UnitAddress")',
		),
		'LeaseStartDate'=>array(
			'header'=>'Move-in',
			'datatype'=>'date',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("field:LeaseStartDate")',
		),
		'Rent'=>array(
			'header'=>'Rent Amt',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("field:Rent")',
		),
		'Total'=>array(
			'header'=>'Invoice Amt',
			'method'=>'function',
			/*
			'fieldExpressionFunction'=>'colConfig("Gen:Dollar", "OriginalTotal", 
			array(
				"negative"=>1, 
			))',
			*/
			'fieldExpressionFunction'=>'colConfig("field:OriginalTotal")',
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
		/*
		'Void'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("Inv:Void")',
			'visibility'=>COL_HIDDEN,
		),
		*/
		'Flag'=>array(
			'header'=>'New Discrepancy',
		),
		'Verification_ID'=>array(
			'header'=>'Verify',
		),
	),
);

//appearance
$datasetTheme='report';
$datasetFooterDisposition='tabularControls'; 	//however, the footer needs to show to nav a large batch like this
$datasetHideFooterAddLink=true;
$datasetHideColumnSelection=false;

function colConfig($param,$field='',$options=array()){
	global $record, $submode,$qr, $developerEmail, $fromHdrBugs, $modApType, $modApHandle;
	global $allInvoices, $thisLateFee, $applicationLateFee;
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
			//this WAS in the late fee column but we need this for allInvoices
			if(
				/* force */
				$GLF_LateStatus==1 || 
				/* neutral and balance, but late */
				($GLF_LateStatus==0 && abs($OriginalTotal + $AmountApplied)>0 && $DateDueFrom<date('Y-m-d', strtotime('-30 days')))){

				$thisLateFee=($GLF_LateCharge>0 ? $GLF_LateCharge : $applicationLateFee);
				$allInvoices['LATEFEES'][$Leases_ID]=$thisLateFee;
			}else{
				$thisLateFee=0;
			}


			if(strtolower($HeaderStatus)=='void'){
				echo 'VOID';
			}else if($OriginalTotal + $AmountApplied + $thisLateFee >= 0 /* and we have to consider the late fee here */){
				echo 'PAID';
			}else{
				if($AmountApplied > 0){
					echo 'PP/';
					$allInvoices['PP'][$Leases_ID]=$AmountApplied;
				}
				if($LeaseStartDate > date('Y-m-d')){
					echo $k='FI';
				}else if($DateDueFrom <= date('Y-m-d',strtotime('-30 days'))){
					echo $k='PASTD';
				}else{
					echo $k='DUE';
				}
				$allInvoices[$k][$Leases_ID]=abs($OriginalTotal + $AmountApplied) + $thisLateFee;
			}
			if(($GLF_DiscrepancyDate!='0000-00-00' && $GLF_DiscrepancyDate!='') || $GLF_DiscrepancyReason){
				$allInvoices['DIS'][$Leases_ID]=abs($OriginalTotal + $AmountApplied) + $thisLateFee;
			}
		break;
		case 'inv:tenant':
			?><a href="contacts.php?Contacts_ID=<?php echo $Contacts_ID;?>" title="View this contact" onclick="return ow(this.href,'l1_contacts','650,750');"><?php echo $FirstName.' '.$LastName;?></a><?php
		break;
		case 'property:name':
			?><a href="properties3.php?Properties_ID=<?php echo $Properties_ID;?>" onclick="return ow(this.href,'l1_properties','750,700');" title="View this property"><?php echo $PropertyName;?></a><?php
		break;
		case 'inv:agent':
			echo $un_firstname ? $un_firstname.' '.$un_lastname : $Agents_username;
		break;
		case 'inv:unitaddress':
			echo $UnitNumber;
		break;
		case 'field:leasestartdate':
			echo t($LeaseStartDate, f_qbks);
		break;
		case 'field:rent':
			echo number_format($Rent,2);
		break;
		case 'field:originaltotal':
			echo number_format(-$OriginalTotal,2);
		break;
		case 'inv:balance':
			$bal=abs($OriginalTotal + $AmountApplied)+$thisLateFee;
			if(($GLF_DiscrepancyDate!='0000-00-00' && $GLF_DiscrepancyDate!='') || $GLF_DiscrepancyReason){
				?><img src="/images/i/findicons.com-flag_red.png" width="16" height="16" alt="DIS" title="Discrepancy <?php echo date('n/j/Y',strtotime($GLF_DiscrepancyDate))?>: <?php echo h($GLF_DiscrepancyReason);?>" style="padding-right:4px;" /><?php
			}
			if($bal==0){
				?><span id="balance_<?php echo $Leases_ID;?>" class="gray">pd.</span><?php
			}else{
				$allInvoices['ACTIVE'][$Leases_ID]=$bal;
				?><span id="balance_<?php echo $Leases_ID;?>"><?php echo number_format($bal,2);?></span><?php
			}
		break;
		case 'inv:void':
			?><select name="Status[<?php echo $ID?>]" id="void<?php echo $ID?>" onchange="dChge(this);setVoid(<?php echo $ID?>,this.value,g('IncludeVoids').checked)" class="smallSelect">
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
				?><a id="pymt_<?php echo $Leases_ID?>" href="history.php?Headers_ID=<?php echo $ID?>" onclick="return ow(this.href,'l1_pymthistorybubble','500,230')" title="This invoice has multiple payments; click to view list"><?php echo number_format($AmountApplied,2);?></a><?php
			}else if($Distributions==1){
				?><a id="pymt_<?php echo $Leases_ID?>" href="payments.php?Payments_ID=<?php echo q("SELECT Headers_ID FROM finan_transactions WHERE ID='$ParentTransactions_ID'", O_VALUE);?>&FocusInvoices_ID=<?php echo $Leases_ID?>" onclick="return ow(this.href,'l1_payments','900,600');" title="Click to view this payment"><?php echo number_format($AmountApplied,2);?></a><?php
			}else{
				?><a id="pymt_<?php echo $Leases_ID?>" href="payments.php?Clients_ID=<?php echo $Clients_ID;?>&FocusInvoices_ID=<?php echo $Leases_ID?>" onclick="return ow(this.href,'l1_payments','900,600');" title="Click to view this payment" style="color:#aaa;">0.00</a><?php
			}
		break;
		case 'inv:latefee':				
			if($thisLateFee){
				//calculated under the status column
				?><span class="red"><?php echo number_format($thisLateFee,2);?></span><?php
			}else if($GLF_LateStatus==-1){
				//override
				?><span id="latefee_<?php echo $Leases_ID?>" class="<?php if($DateDueFrom  < date('Y-m-d', strtotime('-30 days'))/*it is past due*/)echo 'red';?>">0.00</span><?php
			}else{
				?><span class="red" id="latefee_<?php echo $Leases_ID?>" >&nbsp;&nbsp;&nbsp;&nbsp;</span><?php
			}
		break;
		default:
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}

require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v104.php');

//html output items prior to dataset
ob_start();
?>
<p class="gray">This lists ALL invoices created within a time period whether paid or not</p>
<?php
$datasetPreContent=get_contents();
if(!$refreshComponentOnly){
	?><style type="text/css">
	/* -- from filter gadget -- */
	<?php echo $filterGadgetCSS?>
	<?php
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'556b2f',
		'datasetColorRowAlt_'=>'99a682',
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
	.smallSelect{
		width:109px;
		font-size:smaller;
		}
	.smallSelect option{
		font-size:13px;
		}
	.large{
		font-size:215%;
		font-weight:900;
		}
	.void td{
		text-decoration:line-through;
		/* background-color:mistyrose; */
		color:#555;
		}

	/* -- headers for report format; first attempt at this -- */
	.standardReport th{
		border:1px solid #300;
		background-color:papayawhip;
		}
	.standardReport td{
		border-bottom:1px dotted #ccc;
		}
	.standardReport td.dataobjectHeading{
		border-bottom:none;
		}
	.level1 h1{
		font-size:129%;
		font-family:"Times New Roman", Times, serif;
		border-bottom:1px solid #777;
		margin:20px 0px 0px -25px;
		padding:4px 10px;
		}
	.level2 h2{
		font-size:119%;
		font-family:"Times New Roman", Times, serif;
		border-bottom:1px solid #999;
		margin:10px 0px 0px -12px;
		padding:4px 10px;
		}
	</style>
	<script language="javascript" type="text/javascript">
	/* -- from filter gadget -- */
	<?php echo $filterGadgetJS?>
	</script>
<?php
}
require($MASTER_COMPONENT_ROOT.'/dataset_component_v124.php');
?>