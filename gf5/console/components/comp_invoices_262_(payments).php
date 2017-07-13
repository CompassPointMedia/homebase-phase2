<?php
/*
*/
$dataset='payments';
$datasetGroup='payments';
$datasetComponent='paymentsList';
$datasetQueryValidation=md5($MASTER_PASSWORD);
$datasetDebug['query']= ($testQuery==17 ? md5($MASTER_PASSWORD) : '');
$datasetDebug['time']= ($testTime==17 ? md5($MASTER_PASSWORD) : '');
$datasetQuery="SELECT
i.*,
c.CompanyName,
t.Name AS PaymentType
FROM
_v_x_finan_headers_master i, 
finan_clients c,
finan_payments p,
finan_payments_types t
WHERE
i.HeaderType='Payment' AND
i.Clients_ID=c.ID AND
i.ID=p.Headers_ID AND
p.Types_ID=t.ID AND
Clients_ID IN(".implode(',',list_clients()).") ORDER BY i.ID DESC";
$datasetFunction='parse';
#-- 3 above: --
$datasetActiveHideControl=true;
$datasetShowDeletion=false;
$datasetFocusViewDeviceFunction='focus_payment';
$datasetHideFooterAddLink=true;
$datasetHideEditControls=true;
#-- 4 above: --
$datasetBreaks=true;//($section=='OnlinePayment'?false:true);
$datasetBreakFields=array(
	1=>array(
		'column'=>'Properties_ID',
	),
);
$datasetOptionsTop=array('section'=>'top', 'disposition'=>'header_payments');
$datasetOptionsMid=array('section'=>'mid', 'disposition'=>'header_payments');
$datasetOptionsBottom=array('section'=>'bottom', 'disposition'=>'footer_payments');
$datasetCalcFields=array(
	array(
		'name'=>'OriginalTotal',
		'calc'=>'sum',
		'function'=>'number_format(abs($n),2)',
	),
);
#-- 5 --
$datasetNoRecordsRow='invoices_norecords()';
function invoices_norecords(){
	global $invoices_norecords;
	$invoices_norecords=true;
	?><h2>No Payments Found</h2><?php
}
function footer_payments(){
	global $availableCols,$dataset,$modApType,$modApHandle;
	global $col_payments_surcharge;
	?><tr><?php 
	$a=$availableCols[$dataset][$modApType][$modApHandle]['scheme'];
	foreach($a as $handle=>$scheme){
		if(!$scheme['outputted'])continue;
		?><td class="tar"><?php
		if($handle=='AmountPaid'){
			echo '$<span id="Total">'.number_format(@array_sum($_SESSION['special']['payment']),2).'</span>';
		}else{
			?>&nbsp;<?php
		}
		?></td><?php
	}
	?></tr><?php
	if($col_payments_surcharge){
		?><tr><td colspan="100%"><span class="asterisk">*</span> Invoices older than 7 days are charged a 1.5% processing surcharge.  We appreciate your prompt payment!</td></tr><?php
	}
}
function footer_idler(){
	//nothing
}

function focus_invoice($r){
	/* 2012-04-20, what we have here is a basic opening device
	not sure use of the global function->var feature, and it certainly has never been used consistently or with any protocol
	*/
	global $focus_invoice;
	extract($r);
	?><a href="leases.php?Leases_ID=<?php echo $Leases_ID;?>" title="View details and print" onclick="return ow(this.href,'l1_invoices','750,700');"><img src="/images/i/edit2.gif" alt="edit" /></a><?php
}
function col($options=array()){
	/* 2012-04-19: hopefully last version of this function :) 
	one goal = to call an external function AMAP and to keep this generic
	generally, we want to do the following:
		convert a value (such as foreign key to string)
		store an array also so we don't run the same operation
		calculate running balances
		format a value
	the general order of these operations is
		convert
		store
		calculate running balances or data
		format for output
	*/
	global $record,$mode,$submode,$dataset;
	global $qr,$fl,$ln,$developerEmail,$fromHdrBugs, $modApType,$modApHandle;
	if(is_array($options)){
		extract($options);	//should contain $field
		unset($options['field']);
	}else{
		$field=$options;
	}
	//additional globals, for example allInvoices, thisLateFee, applicationLateFee
	if($global)eval('global $'.preg_replace('/,\s*/',', $',$global).';');
	$r=$record;

	//typical conversion calls
	if($invert)$r[$field] *= -1;
	if($absolute)$r[$field]=abs($r[$field]);

	//store fetches
	
	//process
	ob_start();
	if($function=='default')$function='col_'.$dataset;
	$out=($function ? $function($field,$options) : $r[$field]);
	$err=ob_get_contents();
	ob_end_clean();
	if($err){
		//notify
	}
	
	if($format){
		//format as a date, etc. etc. or a link or email link or a picture sized as needed - for example I only need Tree_ID=571 & disposition=350x,2
		switch(true){
			case substr($format,0,5)=='date:':
				$out=date(end(explode(':',$format)),strtotime($out));
			break;
			case substr($format,0,13)=='number_format':
				$a=explode(':',$format);
				$a=explode(',',$a[1]);
				$out=($a[1]?$a[1]:'').number_format($out,($a[0]?$a[0]:2));
			break;
		}
	}
	return $out;
}
function col_payments($field,$options=array()){
	/* 
	2012-04-20: this is the native function for this dataset
	NOTE only the coding between the lines needs to be updated
	
	*/
	global $record,$submode,$MASTER_USERNAME,	$qr,$fl,$ln,$developerEmail,$fromHdrBugs, $modApType,$modApHandle;
	extract($options);
	//additional globals, for example allInvoices, thisLateFee, applicationLateFee
	if($global)eval('global $'.preg_replace('/,\s*/',', $',$global).';');

	$a=$record;
	extract($a);
	ob_start();
	switch(true){
		//------------------------------ custom coding ------------------------------


		//------------------------------ end custom coding ------------------------------
		default:
			//this means that field or columns' action was not specified
			echo $field;
			global $col_notified;
			if(!$col_notified[$field]){
				$col_notified[$field]=true;
				mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='Field '.$field.' not set up'),$fromHdrBugs);
			}
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}
function header_payments($options=array()){
	/* 2012-04-21: first use of a function to output a complete group break header; must include the <tr><td> tags as well */
	extract($options);
	echo '<tr class="dataobjectHeading"><td colspan="100%">'."\n";	
	?>here we are<?php
	echo '</td></tr>'."\n";
	global $datasetPlaceholder;
	echo $datasetPlaceholder='<!-- dataset placeholder here -->';
	echo "\n";
}
$a=array(
	'PropertyName'=>array(
	),
	'HeaderNumber'=>array(
		'header'=>'Inv#',
		'headerAlignment'=>'center',
		'function'=>'col(field=HeaderNumber&function=default)',
		'colattribs'=>array(
			'class'=>'tac',
		),
	),
	'HeaderDate'=>array(
		'header'=>'Date',
		'function'=>'col(field=HeaderDate&format=date:n/j/Y)',
	),
	'Unit'=>array(
		'header'=>'Addr./Unit',
	),
	'Tenant'=>array(
		'function'=>'col(field=Contacts_ID&function=default)',
	),
	'Rent'=>array(
		'function'=>'col(Rent)',
		'headerAlignment'=>'right',
	),
	'Amount'=>array(
		'header'=>'Amount',
		'function'=>'col(field=OriginalTotal&absolute=true&format=number_format:2,$)',
		'headerAlignment'=>'right',
	),
	'LateFee'=>array(
		'function'=>'col(field=LateFee&function=default&global=allInvoices,thisStatus)',
		'headerAlignment'=>'right',
	),
	'AmountApplied'=>array(
		'header'=>'Payments',
		'headerAlignment'=>'right',
		'function'=>'col(field=AmountApplied&format=number_format)',
	),
	'Balance'=>array(
		'headerAlignment'=>'right',
		'function'=>'col(field=Balance&function=default&global=thisLateFee,allInvoices,bal)',
	),
	'Status'=>array(
		'function'=>'col(field=Status&function=default&global=thisStatus,usedProperties)',
		'sortable'=>false,
	),
	'Agent'=>array(
		'function'=>'col(field=Agent&function=default)',
	),
	'PayOnline'=>array(
		'function'=>'col(field=PayOnline&function=default&global=thisStatus)',
		'sortable'=>false,
		'visibility'=>($section=='OnlinePayment'?COL_HIDDEN:COL_VISIBLE),
	),
	'AmountPaid'=>array(
		'function'=>'col(field=AmountPaid&function=default&global=thisStatus,bal)',
		'sortable'=>false,
		'visibility'=>($section!='OnlinePayment'?COL_HIDDEN:COL_VISIBLE),
	),
);
$a=array(
	'Date'=>array(
	),
	'Client'=>array(
	),
	'PropertyName'=>array(
	),
	'UnitNumber'=>array(
	),
	'Type'=>array(
	),
	'HeaderNumber'=>array(
	),
	'OriginalTotal'=>array(
	),
);
$availableCols[$datasetGroup][$modApType][$modApHandle]['scheme']=$a;

require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v104.php');

if(!$refreshComponentOnly){
	?><style type="text/css"><?php
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'556b2f',
		'datasetColorRowAlt_'=>'99a682',
		'datasetColorSorted_'=>'wheat',
	));
	?>
	.asterisk{
		font-family:Georgia, "Times New Roman", Times, serif;
		font-size:109%;
		color:darkred;
		}
	</style>
	<script language="javascript" type="text/javascript">
	</script>
	<?php
}
require($MASTER_COMPONENT_ROOT.'/dataset_component_v130.php');

?>