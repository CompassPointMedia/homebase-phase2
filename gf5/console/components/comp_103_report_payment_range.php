<?php
$dataset='payments';
$datasetGroup=$dataset.'_report';
$datasetComponent='paymentsList';		//used in HTML div id for component wrap (used for refresh and etc.)
if(!$datasetWord)$datasetWord='Payment';
if(!$datasetWordPlural)$datasetWordPlural='Payments';

if($test==17)$datasetDebug['query']=md5($MASTER_PASSWORD);
$datasetReferenceFile=end(explode('/',__FILE__));
$datasetReferenceFileKey=md5('salt:hydroponic'.$MASTER_PASSWORD);
$datasetTable='';
$datasetID='ID';
$datasetArrayType=O_ARRAY_ASSOC;
$datasetQueryValidation=md5($MASTER_PASSWORD);

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
i.$targetDate BETWEEN '$ReportDateFrom' AND '$ReportDateTo'
ORDER BY i.ID DESC
";

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
$datasetFocusPage='payments.php';
$datasetFocusAddObjectJSFunction='ow(this.href,\'l1_payments\',\'850,500\',true);'; //this is because opening an object is not well developed yet
$datasetFocusQueryStringKey='Payments_ID';

$datasetFocusViewDeviceFunction='';		//not used initially

//batching
$datasetDefaultBatch=1000;				//will be 50 if not declared, or if globalBatchThreshold not declared

//sorting
### see the variables datasetReferenceFile[Key] above


//breaks and grouping
$datasetShowBreaks=false;
$datasetBreakFields=array(
	1=>array(
		'column'=>'un_lastname',
		'blank'=>'not specified'
	),
);
$modApType='embedded';
$modApHandle='first';
$availableCols[$datasetGroup][$modApType][$modApHandle]=array(
	'scheme'=>array(
		'HeaderDate'=>array(
			'header'=>'Date',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("HeaderDate",array("format"=>"date"))',
		),
		'CompanyName'=>array(
			'header'=>'Client',
			'orderBy'=>'CompanyName $asc',
		),
		'HeaderNumber'=>array(
			'header'=>'Payment #',
			'orderBy'=>'CAST(HeaderNumber AS UNSIGNED) $asc',
		),
		'PaymentType'=>array(
			'header'=>'Type',
			'sortable'=>false,
		),
		'LineItemCount'=>array(
			'header'=>'#Inv. Applied',
			'sortable'=>false,
			'visibility'=>COL_VISIBLE,
		),
		'OriginalTotal'=>array(
			'header'=>'Amount',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("OriginalTotal",array("format"=>"currency", "absolute"=>true))',
			'sortable'=>false,
		),
		'RB'=>array(
			'header'=>'Running&nbsp;Bal.',
			'method'=>'function',
			'fieldExpressionFunction'=>'colConfig("RB",array("format"=>"currency", "absolute"=>true))',
			'sortable'=>false,
		),
	),
);

//appearance
$datasetTheme='report';
$datasetFooterDisposition='tabularControls'; 	//however, the footer needs to show to nav a large batch like this
$datasetHideFooterAddLink=true;
$datasetHideColumnSelection=true;

function colConfig($field,$options=array()){
	/* 2012-01-22: this is updated and the first-of-kind from other colConfig functions, aim to make it better */
	global $record, $submode,$qr, $developerEmail, $fromHdrBugs, $modApType, $modApHandle;
	global $allInvoices, $thisLateFee, $applicationLateFee;
	$param=strtolower($param);
	$a=$record;
	extract($a);
	extract($options);
	if($invert)$$field *= -1;
	if($absolute)$$field=abs($$field);

	ob_start();
	switch(true){
		case $field=='RB':
			global $runningBalance, $paymentCount;
			$paymentCount++;
			$runningBalance+= -$OriginalTotal;
			echo number_format($runningBalance,2);
		break;
		case $format=='currency':
			if($$field==0 && $nozero){
				if(is_string($nozero))echo $nozero;
				break;
			}
			echo (isset($currency) ? (is_string($currency) ? $currency : '$') : '').number_format($$field, (strlen($decimals)?$decimals : 2));
		break;
		case $format=='date':
			echo t($$field, f_qbks);
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