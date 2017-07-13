<?php
/* take-from
datasetWord MUST be var-like because hl_group uses it for the node as <?php $datasetWord;?>opt - we need another value for this, develop following:
	$datasetTitle
	$datasetDescription
	$datasetInstructions
	(and implement legacy logic accordingly)

*/

/*
dataset
datasetGroup
datasetQuery
required 2 files (ready to go)
availableCols (here is where we do some work)
SPECIAL: status (unpaid, pp, and paid) and dueStatus (fi, due, pd) required work and the interruption of normal flow in the build; we needed to put variables in datasetQuery, and analyze those variables beforehand (and they were buggers too)


SPECIAL: status (unpaid, pp, and paid) and dueStatus (fi, due, pd) required work and the interruption of normal flow in the build; we needed to put variables in datasetQuery, and analyze those variables beforehand (and they were buggers too)
OOPS! We cannot do this as a query, need a view because HAVING is dicey
we do not use active/inactive:
	datasetActiveHideControl=true;
now the focus device
	datasetFocusPage=leases.php
	but this gave this url: leases.php?_ID=13618
	so we add this: datasetFocusQueryStringKey=Leases_ID
	and this is available: datasetFocusAddObjectJSFunction='ow(this.href,\'l1_leases\',\'800,700\',true);'; //because opening an object is not well developed yet

*/
$dataset='contactsInvoices';
$datasetGroup='contacts';
$datasetComponent='contactsInvoicesList';
$datasetWord='Contacts';
$datasetWordPlural='Prospects and Customers';
$datasetTable='_v_contacts_leases_master';
$datasetTableIsView=true;
$tbodyScrollingThreshold=100000;
$datasetActiveHideControl=true;
$datasetFocusPage='leases.php';
$datasetFocusPageDims='850,600';
$datasetFocusQueryStringKey='Leases_ID';

/* -------------- requested statuses based on form submit ------------- */
$statuses=array();
/*
if($InvoiceType['UNPAID'])$statuses[]=1;
if($InvoiceType['PP'])$statuses[]=2;
if($InvoiceType['PAID'])$statuses[]=3;
*/
switch(implode(',',$statuses)){
	case '1,2,3':
		$statusQuery='';
	break;
	case '1':
		$statusQuery='AND IF(Headers_ID IS NOT NULL, AmountApplied=0, 1)';
	break;
	case '1,2':
		//no invoices that are not paid in full
		$statusQuery='AND IF(Headers_ID IS NOT NULL, !(
		AmountApplied >= (Extension * -1)
		), 1)';
	break;
	case '1,3':
		$statusQuery='AND IF(Headers_ID IS NOT NULL, !(AmountApplied > 0 AND 
		IF(
			GLF_LateStatus=1 OR (GLF_LateStatus=0 AND (AmountApplied + Extension<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
			IF(GLF_LateCharge,GLF_LateCharge,10.00), 
			0.00
		) > 0), 1)';
	break;
	case '2':
		$statusQuery='AND IF(Headers_ID IS NOT NULL, (AmountApplied > 0 AND 
		IF(
			GLF_LateStatus=1 OR (GLF_LateStatus=0 AND (AmountApplied + Extension<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
			IF(GLF_LateCharge,GLF_LateCharge,10.00), 
			0.00
		) > 0), 1)';
	break;
	case '2,3':
		$statusQuery='AND IF(Headers_ID IS NOT NULL, AmountApplied > 0, 1)';
	break;
	case '3':
		$statusQuery='AND IF(Headers_ID IS NOT NULL, 
		IF(
			GLF_LateStatus=1 OR (GLF_LateStatus=0 AND (AmountApplied + Extension<0) AND (GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)<DATE_SUB(CURDATE(),INTERVAL 30 DAY))), 
			IF(GLF_LateCharge,GLF_LateCharge,10.00), 
			0.00
		)
		=0, 1)';
	break;
}

$statuses=array();
/*
if($InvoiceType['FI'])$statuses[]=1;
if($InvoiceType['DUE'])$statuses[]=2;
if($InvoiceType['PASTD'])$statuses[]=3;
*/
switch(implode(',',$statuses)){
	case '1,2,3':
		$dueStatusQuery='';
	break;
	case '1':
		//due date > now
		$dueStatusQuery='AND IF(Headers_ID IS NOT NULL, GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)>CURDATE(), 1)';
	break;
	case '1,2':
		//"not past due" - due date > now - 30
		$dueStatusQuery='AND IF(Headers_ID IS NOT NULL, GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate)>DATE_SUB(CURDATE(), INTERVAL 30 DAY), 1)';
	break;
	case '1,3':
		//!(current invoices) - come back on this
		mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
		$dueStatusQuery='';
	break;
	case '2':
		//now between due and due + 30
		$dueStatusQuery='AND IF(Headers_ID IS NOT NULL, CURDATE() BETWEEN GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) AND DATE_ADD(GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate), INTERVAL 30 DAY), 1)';
	break;
	case '2,3':
		//not forecasted - due <= now
		$dueStatusQuery='AND IF(Headers_ID IS NOT NULL, GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) <= CURDATE(), 1)';
	break;
	case '3':
		//past due
		$dueStatusQuery='AND IF(Headers_ID IS NOT NULL, GREATEST(l.CreateDate, l.LeaseStartDate, l.LeaseSignDate) < DATE_SUB(CURDATE(), INTERVAL 30 DAY), 1)';
	break;
}


$datasetFocusViewDeviceFunction='recordFocus';
function recordFocus($a){
	?><a href="#" onclick="return recordFocus(contact,id);">edit</a><?php
}
function colConfig($param,$field='',$options=array()){
	global $record, $submode,$qr, $developerEmail, $fromHdrBugs, $modApType, $modApHandle;
	global $allInvoices, $thisLateFee, $applicationLateFee;
	$param=strtolower($param);
	$a=$record;
	extract($a);
	extract($options);
	ob_start();
	switch($param){
		case 'type':
			?><img src="/images/i-local/i-<?php echo $Headers_ID?'customer':'prospect';?>.png" alt="<?php echo $Headers_ID?'customer':'prospect';?>" align="absbottom" /><?php
		break;
	
		case 'prospectname':
			echo $LastName.', '.$FirstName;
		break;
		case 'emailcheckbox':
			if(true){
				?><input type="checkbox" name="" value="" onclick="selectFor(this);" /><?php
			}
		break;
		case 'leaseinfo':
			if(true){
				?><input type="checkbox" name="" value="" onclick="selectFor(this);" /><?php
			}
		break;
		case 'phone':
			echo $HomeMobile ? $HomeMobile : $HomePhone;
		break;
		case 'status':
		
		break;
		
	
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
			echo $FirstName.' '.$LastName;
		break;
		case 'inv:agent':
			echo $un_firstname ? $un_firstname.' '.$un_lastname : $Agents_username;
		break;
		case 'inv:unitaddress':
			dChgeField($Leases_ID);
			?><input type="text" name="formData[<?php echo $Leases_ID?>][UnitNumber]" value="<?php echo h($UnitNumber);?>" size="5" onchange="dChge(this,oro,<?php echo $Leases_ID;?>);" /><?php
		break;
		case 'field:leasestartdate':
			dChgeField($Leases_ID);
			?><input type="text" name="formData[<?php echo $Leases_ID?>][LeaseStartDate]" value="<?php echo t($LeaseStartDate, f_qbks);?>" size="11" onchange="dChge(this,oro,<?php echo $Leases_ID;?>);" /><?php
		break;
		case 'field:rent':
			dChgeField($Leases_ID);
			?><input type="text" name="formData[<?php echo $Leases_ID?>][Rent]" value="<?php echo number_format($Rent,2);?>" size="11" onchange="dChge(this,oro,<?php echo $Leases_ID;?>);" /><?php
		break;
		case 'field:originaltotal':
			dChgeField($Leases_ID);
			?><input type="text" name="formData[<?php echo $Leases_ID?>][OriginalTotal]" value="<?php echo number_format(-$OriginalTotal,2);?>" size="7" onchange="dChge(this,oro,<?php echo $Leases_ID;?>);" /><?php
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
		case 'inv:discrepancy':
			$str= ($GLF_DiscrepancyDate!=='0000-00-00' ? date('n/j/Y',strtotime($GLF_DiscrepancyDate)). ': ':'').$GLF_DiscrepancyReason;
			echo strlen($str)?$str:'&nbsp;';
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
				?><a id="pymt_<?php echo $Leases_ID?>" href="payments.php?Clients_ID=<?php echo $Clients_ID;?>&FocusInvoices_ID=<?php echo $Leases_ID?>" onclick="return ow(this.href,'l1_payments','900,600');" title="Click to view this payment" style="color:#888;">0.00</a><?php
			}
		break;
		case 'inv:latefee':				
			if($thisLateFee){
				//calculated under the status column
				?><span id="latefee_<?php echo $Leases_ID?>" class="red" oncontextmenu="return false;" onclick="hidemenuie5(event,1); showmenuie5(event,1);"><?php echo number_format($thisLateFee,2);?></span><?php
			}else if($GLF_LateStatus==-1){
				//override
				?><span id="latefee_<?php echo $Leases_ID?>" class="<?php if($DateDueFrom  < date('Y-m-d', strtotime('-30 days'))/*it is past due*/)echo 'red';?>" oncontextmenu="return false;" onclick="hidemenuie5(event,1); showmenuie5(event,1);">0.00</span><?php
			}else{
				?><span class="red" id="latefee_<?php echo $Leases_ID?>" oncontextmenu="return false;" onclick="hidemenuie5(event,1); showmenuie5(event,1);">&nbsp;&nbsp;&nbsp;&nbsp;</span><?php
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
$a=array(
	'Type'=>array(
		'method'=>'function',
		'fieldExpressionFunction'=>'colConfig("Type")',
		'orderBy'=>'IF(Headers_ID IS NOT NULL,1,2) $asc, LastName $asc, FirstName $asc',
	),
	'LeaseInfo'=>array(
		'header'=>'Lease Info',
		'method'=>'function',
		'fieldExpressionFunction'=>'colConfig("LeaseInfo")',
		'sortable'=>false,
	),
	'EmailCheckbox'=>array(
		'method'=>'function',
		'fieldExpressionFunction'=>'colConfig("EmailCheckbox")',
		'sortable'=>false,
		'header'=>'Email',
	),
	'ProspectName'=>array(
		'method'=>'function',
		'fieldExpressionFunction'=>'colConfig("ProspectName")',
		'orderBy'=>'LastName $asc, FirstName $asc',
	),
	'Phone'=>array(
		'method'=>'function',
		'fieldExpressionFunction'=>'colConfig("Phone")',
		'sortable'=>false,
	),
	'Email'=>array(
		
	),
	'TargetMID'=>array(
		'method'=>'field',
		'fieldExpressionFunction'=>'GLF_MoveInDate',	
	),
	'LeaseStartDate'=>array(
		'method'=>'field',
		'fieldExpressionFunction'=>'LeaseStartDate',
		'header'=>'Actual<br />MID',
		'datatype'=>'date',
		'orderBy'=>'LeaseStartDate $asc',
	),
	'LeaseExpirationDate'=>array(
		'header'=>'Lease Exp.',
		'method'=>'field',
		'fieldExpressionFunction'=>'LeaseEndDate',
		'datatype'=>'date',
	),
	'HeaderNumber'=>array(
		'header'=>'Inv#',
	),
	'Property'=>array(
		'method'=>'field',
		'fieldExpressionFunction'=>'PropertyName',
	),
	'Status'=>array(
		'method'=>'function',
		'fieldExpressionFunction'=>'colConfig("Status")',
		'sortable'=>false,
	),
	'Verify'=>array(
	
	),
	'Agent'=>array(
		'method'=>'field',
		'fieldExpressionFunction'=>'Agents_username',
	),
	'AddtlAgent'=>array(
		'header'=>'Addt\'l<br />Agent',
	),
	'LastCorresp'=>array(
		'header'=>'Last<br />Corresp.',
	
	),



	/* --
	'Status'=>array(
		'method'=>'function',
		'fieldExpressionFunction'=>'colConfig("Inv:Status")',
	),
	-- */
);
$availableCols[$datasetGroup][$modApType][$modApHandle]['scheme']=$a;


$datasetFile=end(explode('/',__FILE__));

/*
2012-02-09: OK the remaining coding is additional to relatebase console/comp_30_list_events_v110.php as specified on that date

*/
$datasetInternalFilter=(minroles()>=ROLE_AGENT ? "Creator='".sun()."'":'');
if($statusQuery){
	$datasetInternalFilter.=($datasetInternalFilter ? $statusQuery : preg_replace('/^\s*AND\s*/i','',$statusQuery));
}
if($dueStatusQuery){
	$datasetInternalFilter.=($datasetInternalFilter ? $dueStatusQuery : preg_replace('/^\s*AND\s*/i','',$dueStatusQuery));
}
if($searchType){
	$typeQuery=($searchType=='prospects' ? "AND Headers_ID IS NULL" : ($searchType=='customers' ? "AND Headers_ID IS NOT NULL" : ''));
	$datasetInternalFilter.=($datasetInternalFilter ? $typeQuery : preg_replace('/^\s*AND\s*/i','',$typeQuery));
}
$datasetInternalFilter.=($datasetInternalFilter ? " AND ":'').($InvoiceType['VOID'] ? '1' : "IF(Headers_ID IS NOT NULL, HeaderStatus!='Void', 1)");
if($mode=='customerSearch'){
	$str='';
	$dates=array(
	'DesiredMoveInDatefrom',
	'DesiredMoveInDateto',
	'MoveInDatefrom',
	'MoveInDateto',
	'MoveOutDatefrom',
	'MoveOutDateto',
	);
	foreach($dates as $v){
		if(!t($$v, dironal))error_alert('Your date "'.$v.'" is not valid.  Try again or leave it blank');
		if(preg_match('/to$/',$v) && ($$v xor $GLOBALS[preg_replace('/to$/','from',$v)]))error_alert('Move-in/Move-out dates must come in pairs');
	}
	if(trim($q)){
		if(strstr($q,'@')){
			$str.=" AND Email LIKE '%".$q."%'";
		}else if(preg_match('/[0-9]{4,}/',preg_replace('/[^0-9]/','',$q))){
			$str.=" AND 
			REPLACE(REPLACE(REPLACE(REPLACE(CONCAT(HomeMobile,'*',HomePhone)
			,'-','')
			,' ','')
			,'(','')
			,')','') LIKE '%".$q."%'";
		}else if($a=parse_name(stripslashes($q))){
			if(count($a)==1){
				foreach($a as $v)$val=$v;
				$str.=" AND (FirstName='".addslashes($q)."' OR LastName='".addslashes($q)."')";
			}else{
				foreach($a as $n=>$v){
					$str.=" AND $n='".addslashes($v)."'";
				}
			}
		}else error_alert('Name does not appear to be valid, matching other criteria',1);
		
	}
	if($searchType!=='prospects'){
		if($MoveInDatefrom && $MoveInDateto)$str.=" AND LeaseStartDate BETWEEN '$MoveInDatefrom' AND '$MoveInDateto'";
		if($MoveOutDatefrom && $MoveOutDateto)$str.=" AND LeaseEndDate BETWEEN '$MoveOutDatefrom' AND '$MoveOutDateto'";
	}
	if($searchType!=='customers'){
		if($DesiredMoveInDatefrom && $DesiredMoveInDateto)$str.=" AND GLF_MoveInDate BETWEEN '$DesiredMoveInDatefrom' AND '$DesiredMoveInDateto'";
	}
	if($str)$datasetInternalFilter.=($datasetInternalFilter ? $str : preg_replace('/^\s*AND\s*/i','',$str));
}




if($test==17)$datasetDebug['query']=md5($MASTER_PASSWORD);

require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v104.php');
if(!$refreshComponentOnly){
	?><style type="text/css">
	<?php
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'a37029',
		'datasetColorRowAlt_'=>'f1e8e2',
		'datasetColorSorted_'=>'wheat',
		'datasetColorHighlight_'=>'d3b8a5',
	));
	?>
	</style>
	<script language="javascript" type="text/javascript">
	/* 
	"HW" Coding
	2012-02-08: added very simple functions specific to the event 
	2012-02-09: first effort at making this generic
	*/
	<?php if(!$datasetJSDeclared){ ?>
	<?php $datasetJSDeclared=true; ?>
	var dataset={};
	function addObject(o){
		ow(dataset[o][datasetFocusPage], 'l1_'+dataset[o][datasetWord], (typeof dataset[o][datasetFocusPageDims]!=='undefined' ? dataset[o][datasetFocusPageDims] : '750,700'),true);
		return false;
	}
	function openObject(o){
		for(var i in hl_grp[dataset[o][datasetWord]+'opt']){
			ow(dataset[o][datasetFocusPage]+'?'+dataset[o][datasetFocusQueryStringKey]+'='+i.replace('r_',''),'l1_'+dataset[o][datasetWord], (typeof dataset[o][datasetFocusPageDims]!=='undefined' ? dataset[o][datasetFocusPageDims] : '750,700'));
			return false;
		}
	}
	<?php }else{ ?>
	
	<?php }?>
	obj='<?php echo $datasetComponent;?>';
	dataset[obj]={
	<?php if($n=$datasetFocusPageDims){ ?>'datasetFocusPageDims':'<?php echo $n;?>',<?php } ?>
		'datasetGroup':'<?php echo $datasetGroup;?>',
		'dataset':'<?php echo $dataset;?>',
		'datasetFocusPage':'<?php echo $datasetFocusPage;?>',
		'datasetFocusQueryStringKey':'<?php echo $datasetFocusQueryStringKey;?>',
		'datasetWord':'<?php echo strtolower($datasetWord);?>'
	}
	</script><?php
}
require($MASTER_COMPONENT_ROOT.'/dataset_component_v124.php');
?>