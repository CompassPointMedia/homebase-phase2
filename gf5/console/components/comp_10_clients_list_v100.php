<?php
/*
2010-11-20: Pulled from list children in G iocosC are

hire date - have -- for 0000-00-00
track and have un_username= be set in place

*/
$dataset='Clients'; 									#more of a concept
$datasetComponent='clientList'; 						#THIS physical component
$datasetGroup=$dataset; 							//as of 2010-04-04, this is not used
if(!$datasetWord)$datasetWord='Client';
if(!$datasetWordPlural)$datasetWordPlural='Clients';
$datasetFocusPage='clients.php';
$datasetAddObjectJSFunction='ow(this.href,\'l1_clients\',\'800,700\',true);'; //this is because opening an object is not well developed yet
$datasetQueryStringKey='Clients_ID';
$datasetDeleteMode='deleteClient';

$datasetQuery=''; 									//this is left blank for list_members; needed because a view didn't contain the same data as a query!
$datasetTable='_v_clients_master_list';		//this can be a single MySQL table or a view
$datasetTableIsView=true;
$datasetArrayType=O_ARRAY_ASSOC;					//added 2010-05-10 - this allows for non-standard left-column-equals-primary-key constructions; default=O_ARRAY_ASSOC
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

function cllist($param){
	global $record, $submode,$qr;
	$a=$record;
	extract($a);
	ob_start();
	switch($param){
		case 'Name':
			echo $ClientName;	
		break;
		case 'Contact':
			if($LastName xor $FirstName){
				echo $LastName.' '.$FirstName;
			}else{
				echo $LastName.', '.$FirstName . ($MiddleName ? strtoupper(substr($MiddleName,0,1)).'.':'');
			}
			if($Email){
				?><br /><a href="mailto:<?php echo $Email?>" title="Email this person"><?php echo $Email?></a><br /><?php
			}
			if($HomeMobile){
				?><br />c: <?php echo $HomeMobile?><br /><?php
			}
		break;
		case 'Address':
			echo $Address1.'<br />';
			echo $City.', '.$State.'  '.$Zip.'<br />';
		break;
		case 'Phones':
			if($Phone)$p[]= 'p: '.$Phone;
			if($Fax)$p[]= 'f: '.$Fax;
			echo implode('<br />',$p);
		break;
		case 'Property':
			//logic here
			if(!$PropertyCount){
				//should not happen
				?>&nbsp;<?php
			}else if($PropertyCount==1){
				//list it and icon for type
				?><a href="properties3.php?Properties_ID=<?php echo $Properties_ID?>" onclick="return ow(this.href,'l1_properties','700,700');" title="Open and view/edit this property"><?php echo h($PropertyName)?></a><?php
			}else{
				//get properties
				if($a=q("SELECT
				p.*
				FROM gl_properties p
				WHERE Clients_ID=$ID ORDER BY PropertyName", O_ARRAY)){
					foreach($a as $v){
						?><a href="properties3.php?Properties_ID=<?php echo $v['ID']?>" onclick="return ow(this.href,'l1_properties','700,700');" title="Open and view/edit this property"><?php echo $v['PropertyName']?></a><br /><?php
					}
				}
			}
		break;
		case 'ClientSince':
			echo date('n/j/Y',strtotime($ClientCreateDate));	
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
			'fieldExpressionFunction'=>'clList("Name")',
			'orderBy'=>'ClientName $asc'
		),
		'Contact'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'clList("Contact")',
			'sortable'=>false,
		),
		'Address'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'clList("Address")',
			'width'=>300,
		),
		'Phones'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'clList("Phones")',
			'sortable'=>false,
			'visibility'=>COL_AVAILABLE,
		),
		'Property'=>array(
			'header'=>'Property(ies)',
			'method'=>'function',
			'fieldExpressionFunction'=>'clList("Property")',
		),
		'ClientSince'=>array(
			'method'=>'function',
			'fieldExpressionFunction'=>'clList("ClientSince")',
		),
	)
);

//2010-06-03 converted active/inactive to discharged/in care
$datasetActiveUsage=false;
$datasetActiveActiveExpression='st_active=1';
$datasetActiveInactiveExpression='st_active=0';
$datasetActiveAllExpression='1';
$datasetActiveField='st_active';
//allow this parameter to be passed remotely
if(!isset($hideObjectInactiveControl))$hideObjectInactiveControl=true;
$datasetActiveControl='';
$datasetActiveActivateTitle='';
$datasetActiveInactivateTitle='';
$datasetShowDeletion=false;

if(!$datasetFetchSettings)require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v103.php');

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
		'datasetColorHighlight_'=>'b0c4c5',
	));
	?>
	</style>
	<script language="javascript" type="text/javascript">
	/* -- from filter gadget -- */
	<?php echo $filterGadgetJS?>
	</script><?php
}
if(!$datasetFetchSettings)require($MASTER_COMPONENT_ROOT.'/dataset_component_v123.php');
?>