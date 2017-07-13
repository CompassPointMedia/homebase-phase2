<?php
/*
2010-11-20: Pulled from list children in G iocosC are

hire date - have -- for 0000-00-00
track and have un_username= be set in place

*/
$dataset='Staff'; 									#more of a concept
$datasetComponent='staffList'; 						#THIS physical component
$datasetGroup=$dataset; 							//as of 2010-04-04, this is not used
if(!$datasetWord)$datasetWord='Staff';
if(!$datasetWordPlural)$datasetWordPlural='Staff';
$datasetFocusPage='staff.php';
$datasetAddObjectJSFunction='ow(this.href,\'l1_staff\',\'800,700\',true);'; //this is because opening an object is not well developed yet
$datasetQueryStringKey='un_username';
$datasetDeleteMode='deleteStaff';
$datasetShowDeletion=true;
$datasetFocusQueryStringKey='un_username';

$datasetQuery=''; 									//this is left blank for list_members; needed because a view didn't contain the same data as a query!
$datasetTable='_v_staff_master_information';		//this can be a single MySQL table or a view
$datasetTableIsView=true;
$datasetArrayType=O_ARRAY_ASSOC;					//added 2010-05-10 - this allows for non-standard left-column-equals-primary-key constructions; default=O_ARRAY_ASSOC
$Dataset_ID='UserName';
$datasetID='UserName';
$datasetFieldList='*';
$modApType='embedded';
$modApHandle='first';
$globalBatchThreshold='10000';

$datasetTheme='';
$footerDisposition='tabularControls'; 				//however, the footer needs to show to nav a large batch like this
$datasetHideFooterAddLink=true;
$hideColumnSelection=false;

//2010-06-03: for gf_children this is not used initially
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

$focusViewDeviceFunction='';							#not used initially
$datasetAdditionalClassFunction='';						#not used initially
function editLink($record){
	//created 2010-05-11
	global $editLink;
	unset($editLink);
	extract($record);
	switch(strtolower($Relationship)){
		case 'foster parent':
			$focusViewURL = 'parents.php?Parents_ID='.$ID;
			$focusViewTitle = 'View this foster parent\'s info';
			$focusViewSelfName = 'l1_parents';
			$focusViewSize = '850,700';
		break;
		case 'staff':
			if(minroles() < ROLE_CLIENT){
				$focusViewURL = 'staff.php?un_username='.$ID;
				$focusViewTitle = 'View this staff member\'s info';
				$focusViewSelfName = 'l1_pds';
				$focusViewSize = '800,700';
			}
		break;
		case 'therapists':
			if(minroles() < ROLE_CLIENT){
				$focusViewURL = 'therapists.php?Therapists_ID='.$ID;
				$focusViewTitle = 'View this therapists\'s info';
				$focusViewSelfName = 'l1_therapists';
				$focusViewSize = '850,700';
			}
		break;
		case 'household member':
			//edit the home they are a part of - no focus
			if($Fosterhomes_ID=q("SELECT a.ID
				FROM gf_fosterhomes a, gf_objects b WHERE
				b.ParentObject='gf_fosterhomes' AND b.Objects_ID=a.ID AND b.ID=$ID",O_VALUE)){
				$focusViewURL = 'homes.php?Fosterhomes_ID='.$Fosterhomes_ID;
				$focusViewTitle = 'View this foster home and household member\'s info';
				$focusViewSelfName = 'l1_homes';
				$focusViewSize = '815,750';
			}
		break;
		case '':
		case 'caregiver':
		case 'non-fostex':
			$focusViewURL = 'subcontractors.php?Subcontractors_ID='.$ID;
			$focusViewTitle = 'View this '.($Relationship=='non-fostex' || !$Relationship ? 'person' : strtolower($Relationship)).'\'s info';
			$focusViewSelfName = 'l1_subcontractors';
			$focusViewSize = '850,700';
		break;
	}
	//globalize component parameters
	$editLink=array(
		'focusViewURL' => $focusViewURL,
		'focusViewTitle' => $focusViewTitle,
		'focusViewSelfName' => $focusViewSelfName,
		'focusViewSize' => $focusViewSize
	);
	if($focusViewURL){
		?><a href="<?php echo $focusViewURL?>" title="<?php echo $focusViewTitle?>" onclick="return ow(this.href,'<?php echo $focusViewSelfName?>','<?php echo $focusViewSize?>');"><img src="/images/i/s/hlw-25x25-9EA9B4/edit-color.png" width="25" height="25" alt="edit" /></a><?php
	}else{
		?><img src="/images/i/spacer.gif" width="25" height="25" alt="  " /><?php
	}
}
function addClass($record){
	//no CBC, due within 60 days, due within 30 days, past due, pending, resolved, failed
	extract($record);
}

$datasetOverrideSort='';								#not used initially

function stlist($param){
	global $record, $submode,$qr;
	$a=$record;
	extract($a);
	ob_start();
	switch($param){
		case 'Name':
			echo $LastName.', '.$FirstName;	
		break;
		case 'Offices':
			if($b=q("SELECT
			oa_org1
			FROM bais_StaffOffices so, bais_orgaliases oa WHERE so.so_stusername='$UserName' AND so.so_unusername=oa.oa_unusername", O_COL)){
				echo implode(', ',$b);	
			}
		break;
		case 'Phones':
			if($Cell)echo $Cell.'(m)<br />';
			if($Phone)echo $Phone .'(p)<br />';
			if($WorkPhone)echo $WorkPhone .'(w)<br />';
		break;
		case 'Email':
			?><a href="mailto:<?php echo $Email?>"><?php echo $Email;?></a><?php
		break;
		case 'ADM':
			echo $ADM ? 'Y' : '&nbsp;';	
		break;
		case 'MGR':
			echo $MGR ? 'Y' : '&nbsp;';	
		break;
		case 'AGT':
			echo $AGT ? 'Y' : '&nbsp;';	
		break;
		case 'AgentReport':
			?><a href="report_agentreport.php?Agents_UserName=<?php echo $UserName?>" title="View invoices and activity for this agent" onclick="return ow(this.href,'l1_agentreport','700,700');">view</a><?php
		break;
		case 'st_hiredate':
			if(substr($st_hiredate,0,10)=='0000-00-00'){
				?>&nbsp;<?php
			}else{
				echo date('n/j/Y',strtotime($st_hiredate));
			}	
		break;
	}
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}

//declare the properties of the dataset->component
$availableCols[$datasetGroup][$modApType][$modApHandle]=array(
	'scheme'=>array(
		'Name'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'stlist("Name")',
			'orderBy'=>'LastName $asc, FirstName $asc'
		),
		'UserName'=>array(
			
		),
		'Phones'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'stlist("Phones")',
			'sortable'=>false,
		),
		'Email'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'stlist("Email")',
			'orderBy'=>'Email $asc'
		),
		'ADMIN'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'stlist("ADM")',
			'orderBy'=>'ADM $asc, LastName $asc, FirstName $asc'
		),
		'MGR'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'stlist("MGR")',
			'orderBy'=>'MGR $asc, LastName $asc, FirstName $asc'
		),
		'AGENT'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'stlist("AGT")',
			'orderBy'=>'AGT $asc, LastName $asc, FirstName $asc'
		),
		'st_hiredate'=>array(
			'header'=>'Hire Date',
			'method'=>'function',
			'fieldExpressionFunction'=>'stlist("st_hiredate")',
		),
	)
);

//2010-06-03 converted active/inactive to discharged/in care
$datasetActiveUsage=true;
$datasetActiveActiveExpression='st_active=1';
$datasetActiveInactiveExpression='st_active=0';
$datasetActiveAllExpression='1';
$datasetActiveField='st_active';
$datasetActiveFieldLabel='active';

//allow this parameter to be passed remotely
if(!isset($hideObjectInactiveControl))$hideObjectInactiveControl=false;
/* 
2011-03-23 - to be deleted soon
$datasetActiveControl='dischargeChild2(".$$Dataset_ID.", ".($$datasetActiveField?1:0).");';
*/
$datasetActiveActivateTitle='Make this staff active';
$datasetActiveInactivateTitle='Make this staff inactive';



require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v104.php');

//not used yet
ob_start();
$datasetPreContent=get_contents();

//html output items
if(!$refreshComponentOnly){
	?><style type="text/css">
	/* -- from filter gadget -- */
	<?php echo $filterGadgetCSS?>
	.frb{
		float:right;
		margin-left:5px;
		}
	.flb{
		float:left;
		margin-right:8px;
		}
	.frb a, .flb a{
		color:#000;
		}
	<?php
	dataset_complexDataCSS(array(
		'datasetColorHeader_'=>'8b4513',
		'datasetColorRowAlt_'=>'f1e8e2',
		'datasetColorSorted_'=>'wheat',
	));
	?>
	</style>
	<script language="javascript" type="text/javascript">
	/* -- from filter gadget -- */
	<?php echo $filterGadgetJS?>
	</script><?php
}
require($MASTER_COMPONENT_ROOT.'/dataset_component_v124.php');
?>