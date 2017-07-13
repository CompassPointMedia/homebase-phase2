<?php
foreach($GLOBALS as $n=>$v){
	$usedKeys[$n]=1;
}
require('dataset_from_scratch.php');
foreach($GLOBALS as $n=>$v){
	if($usedKeys[$n])continue;
	echo $n . ': ' . $v . '<br />';
}


EXIT;
$b=$GLOBALS;
$c=array_diff($a,$b);
print_r($c);
exit;


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
		'Offices'=>array(
			'header'=>'Office(s)',
			'method'=>'function',
			'fieldExpressionFunction'=>'stlist("Offices")',
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


require($MASTER_COMPONENT_ROOT.'/dataset_generic_precoding_v103.php');

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
require($MASTER_COMPONENT_ROOT.'/dataset_component_v123.php');
?>