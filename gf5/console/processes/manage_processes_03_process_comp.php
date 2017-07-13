<?php
/******** --------------------------------------------------------
2004-06-15 by Sam
This is the first page to deal with hideHeaders systematically.
If a page is standalone we'll need to connect and show headers, however if the page is text in another page, we do not show <html>, <head>, etc. nor include javascript or css because it would (should) be found in the parent.
The system below is pretty hacker-proof because trying to call page.php?hideHeaders=1 would shut down the file, and calling page.php?hideHeaders=0 would require authorization and a connection (which is done in the parent if it's an include).

-------------------------------------------------------- *******/
if($hideHeaders){
	substr(__FILE__,-(strlen($_SERVER['PHP_SELF'])))==$_SERVER['PHP_SELF']? exit('-include page locked'):'';
}
if(!$hideHeaders){
	//begin session and identify script, include main configs
	//note this is a weakness because this script will work a process.  It has its own id, the parent script does too -- only one can be present at a time. 2004-06-16, I made the name the same for now, but note the componentID
	session_start();
	$localSys['scriptID']='staff';
	$localSys['scriptVersion']='4.0';
$localSys['componentID']='01';
	
	require("../systeam/php/config.php");

	//include authorization here
	require("../../../consolde/systeam/php/auth_i2_v100.php");

	//include needed files here

	if(!$roleAccessPresent){
		?><script>alert('You do not have permission to do this task');</script><?php
		exit('This page allows access through a specific role only.  Please see your administrator');
	}
	
	//connect -- e.g. no data pulled if we try to hack the page
	$db_cnx=mysqli_connect($MASTER_HOSTNAME,$MASTER_USERNAME,$MASTER_PASSWORD);
	mysqli_select_db($db_cnx,$MASTER_DATABASE);
	
}

if(!$hideHeaders){
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Staff List</title>

<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/site-local/local.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/contextmenus_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle .02 coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
var ResourceToken='<?php echo $ResourceToken;?>';
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';

//alert('The context menus are not installed in this page');
var ownPage=1;
//------------- begin menu javascript -----------------------------
var menuLvl=new Array();
//type of menu matching. regexp uses rMenuMap, normal uses menuMap (see below)
var menuType='regexp'; //must be 'normal', or 'regexp'

//this menu map is the normal way, one-to-one correspondence between object and menu
var menuMap=new Array();

//this menu map uses regular expressions so that loc1,loc2,loc. all map =>amenu1
//if you use this make sure you don't get menus showing in unexpected locations!
var rMenuMap=new Array();
rMenuMap['^ro_[0-9]+_pr_[0-9]+$']='roleMenu';
rMenuMap['^pr_[0-9]$']='processMenu';

//this is the id of the div containing the menu, set initially to blank
var menuIDName='';

//Under Version 1.0 -- hidemenu-cancel (hmcxl) this field is used to prevent hidemenu from being called twice when it would cause problems
var hm_cxl=0;
var hm_cxlseq=0;
var option_hm_cxl=0;
//this determines the alignment from the source element.  Must correspond to either menuMap or rMenuMap.  Options under development are 'mouse','topleftalign','bottomleftalign', 'rightalign', and there will be more -- these are not all developed yet.
//NOTE: default is 'mouse'
var menuAlign= new Array();
menuAlign['^ro_[0-9]+_pr_[0-9]+$']='mouse';
menuAlign['^ro_[0-9]+$']='mouse';

//holds the status message during mouseovers, initially set to blank
var statusBuffer='';
var ownPage=1;
//------------- end menu javascript -----------------------------
</script>
</head>
<body><?php }else{
	?><script>var ownPage=0;</script><?php }

/****
Table layout library item 03
This is a list of processes and the objective is to mirror a the inbox window of Microsoft Outlook or Groupwise, with the buttons at the top eventually becoming sortable (but better than MS :-), clicking a row highlights and selects the object, and double clicking selects the object and completes a asecondary process in this case.
****/

//

//NOTE that in some cases the sorting would need to be handled by other than SQL and ^> we should work with arrays in the future


/* ------------------------------ SORTABLE DATA GENERATOR 1.0 ----------------------
2004-06-22: Added column control standard layout with highlight_select() functionality as well

Todo list:
1. Handle icon links, events, and alt tags
2. Allow colgroups to control layout and also javascript events -- can I call a colgroup and also have an onclick event
3. The function only handles single primary keys right now
4. standardize a way of declaring in javscript what the sort index is, adn what each column represents


2004-06-28: limitations at this point are that both main and sub table must have a single primary key
------------------------------------------------------------------------------------- */

//none of these arrays may have duplicate values
$colLabel = array('Category','Process','Description','Window Type');
$colHandle = array('Category','Process','Description','WindowType');
$colFieldset = array(
	'pr_category',
	'pr_name',
	'pr_description',
	'pr_windowtype'
);
$colSortableValue = array('pr_category', 'pr_name','','');
//this represents column 1 (0 for the arrays), 0 and 1 are possible values; case sensitive!!
/**
$colIconMap[0]['0']='';
$colIconMap[0]['1']='/DynamicForms/images/person.gif';
$colDisplay[0]='special';
**/

//need to develop the display of an image or icon based on category
$colSortable = array(1,1,1,1);
$colHideDisplay = array(0,0,0,0);
//this is applied to the <tbody> tag
$colRegionName='processList';
$col[highlightSelect]=1; //whether onClick will highlight the row
$col[highlightGroup]=''; //by default this is blank, needs a value for multiple highlightable regions
$col[tableClass]='data1';
$col[showEmptyResultSet]=true;
//handles the naming of the table rows
$col[primaryKey]='pr_id';
$col[primaryKeyLayout]='pr_{primaryKey}';
//SQL expression which defines the label for each record
$col[rowLabel]='CONCAT(pr_name," - ",pr_description)';

//convention for sort order
#not developed
$fromClause="FROM bais_processes";
$whereClause='WHERE 1';
//this needs to become changeable
$orderClause='ORDER BY pr_name, pr_version';
//no limit clause for now -- not developed

$colSubRow=1;


//visual layout settings
$colCellVerticalAlign='top';

//temporary: column b
$colB[primaryKey]='co_id';
$colB[foreignKey]='co_prid';
$colB[table]="bais_components";
$colB[showHeader]=false;
$colCellVerticalAlignB='';
$colIconMapB[0]['0']='';
$colIconMapB[0]['2']='/DynamicForms/images/object01.gif';
$colFieldsetB=array(
	2, co_handle, co_name, co_description, co_category
);
$colHandleB=array(
	'fld1','Handle', 'Name','Description','Category'
);
$colHideDisplayB=array(0,0,0,0,0);
$colNowrapB=array(0,1,1,0,0);
$colCellVerticalAlignB=array(1,1,1,1,1);
//$colTableClassB='data1';
//$colTableStyleB='border:none';
$colB[highlightSelect]=1; //whether onClick will highlight the row
$colB[highlightGroup]=2; //by default this is blank, needs a value for multiple highlightable regions
$colB[primaryKey]='co_id';
$colB[primaryKeyLayout]='co_{primaryKey}';

//------------------ END SETTINGS ---------------------------

ob_start();
//step 1: create the query
unset($a);
foreach($colFieldset as $n=>$v){
	$a[]=$v.(trim($colHandle[$n])?' AS '.$colHandle[$n]:'');
}
$selectClause='SELECT '.implode(', ',$a);
$rand=rand(1000,10000);
if(is_array($colSortableValue)){
	foreach($colSortableValue as $n=>$v){
		if(trim($v)){
			$i++;
			$colSortableValueMap[$n]="f$i$rand";
			$selectClause.=', '.$v.' AS '."f$i$rand";
		}
	}
}
//handle primary key field
if($x=$col[primaryKey]){
	$selectClause.=', '.$x.' AS primaryKeyField';
}
//handle requested label for each row
if($col[rowLabel]){
	if(in_array($col[rowLabel],$colFieldset)){
		$col[rowLabelMap]=$colHandle[array_search($col[rowLabel],$colFieldset)];
	}else{
		$col[rowLabelMap]='rowLabelMap';
		$selectClause.=', '.$col[rowLabel].' AS rowLabelMap';
	}
}
$sql="$selectClause \n $fromClause \n $whereClause \n $orderClause";
$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
$x=ob_get_contents();
ob_end_clean();
if(strlen($x)){
	//handle errors and warnings called
	#not developed
	echo $x;
}

//--------------------------------------------------
$result=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
$i=0;
while(($r=mysqli_fetch_array($result)) || $col[showEmptyResultSet]){
	$i++;
	$col[showEmptyResultSet]=false;
	if($i==1){
		//declare table and first row
		//by default the first row is thead / th combo
		if(!$colRegionName){$colRegionName='i'.rand(1000,100000);}
		?>
		<div id="<?php echo $colRegionName?>"><table width="100%" class="<?php echo $col[tableClass]?>" cellpadding="0" cellspacing="0"><thead>
		<?php
		for($j=1;$j<=count($colFieldset);$j++){
			$idx=$j-1;
			?><th><div nowrap class="colCtrl1" onMouseDown="this.className='colCtrl2'" onMouseUp="this.className='colCtrl1'"><?php
			//----------- declare header control contents ------------------
			echo $colLabel[$idx]?$colLabel[$idx]:'&nbsp;';
			?></div></th>
			<?php
		}
		?></thead><?php
	}
	if(!($r)){
		$i=0;
		echo '</table></div>';
		break;
	}
	if($i==1){
		?><tbody><?php
	}
	//---------- now declare the rows ---------------
	if($col[primaryKey] && $col[primaryKeyLayout]){
		$pk=str_replace('{primaryKey}',$r[primaryKeyField],$col[primaryKeyLayout]);
	}else{
		$pk='';
	}
	if($col[highlightSelect]){
		$hs=' onclick="highlight_select(this'.($col[highlightGroup]?','.$col[highlightGroup]:'').')"';	
	}else{
		$hs='';
	}
	if($col[rowLabelMap]){
		$label=' label="'.htmlentities($r[$col[rowLabelMap]]).'"';
	}else{
		$label='';
	}
	?><tr id="<?php echo $pk?>"<?php echo $hs.$label?>><?php
	//for now, add leading col and other features
	$colSpan=count($colFieldset);
	for($j=1;$j<=$colSpan;$j++){
		$idx=$j-1;
		?><td<?php echo $colCellVerticalAlign?' valign="'.$colCellVerticalAlign.'"':''?>><?php
		//------------ declare cell values --------------
		if(isset($colIconMap[$idx])){
			if($x=$colIconMap[$idx][$r[$colHandle[$idx]]]){
				echo "<img src='$x' border='0'/>";
			}else{
				//display nothing
			}
		}else if(!$colHideDisplay[$idx]){
			echo $r[$colHandle[$idx]];
		}
		?></td><?php
	}
	?></tr><?php
	//handle sub rows here
	if($colSubRow==1){
		//this row is the container for a sub-table
		#1. handle the none/block display issue either in bulk or to retain state
		#2. table inside of this
		
		unset($b);
		foreach($colFieldsetB as $n=>$v){
			$b[]=$v.(trim($colHandleB[$n])?' AS '.$colHandleB[$n]:'');
		}
		$selectClauseB='SELECT '.implode(', ',$b);
		$rand=rand(1000,10000);
		if(is_array($colSortableValueB)){
			foreach($colSortableValueB as $n=>$v){
				if(trim($v)){
					$i++;
					$colSortableValueMapB[$n]="f$i$rand";
					$selectClauseB.=', '.$v.' AS '."f$i$rand";
				}
			}
		}
		//handle primary key field
		if($x=$colB[primaryKey]){
			$selectClauseB.=', '.$x.' AS primaryKeyField';
		}
		//handle requested label for each row
		if($colB[rowLabel]){
			if(in_array($colB[rowLabel],$colFieldsetB)){
				$colB[rowLabelMap]=$colHandleB[array_search($colB[rowLabel],$colFieldsetB)];
			}else{
				$colB[rowLabelMap]='rowLabelMap';
				$selectClauseB.=', '.$col[rowLabel].' AS rowLabelMap';
			}
		}
		
		$sql="$selectClauseB FROM ".$colB[table]." WHERE 1 AND ".$colB[foreignKey]." = '".addslashes($r[primaryKeyField])."'"; 
		$resLevel2=mysqli_query($db_cnx,$sql) or die(mysqli_error($db_cnx));
		if(mysqli_num_rows($resLevel2)){

			if($colB[highlightSelect]){
				$hsB=' onclick="highlight_select(this'.($colB[highlightGroup]?','.$colB[highlightGroup]:'').')"';	
			}else{
				$hsB='';
			}
			?><tr id="__<?php echo $pk?>" style="display:block"><td<?php echo $colSpan>1?' colspan="'.$colSpan.'"':''?> style="padding:0;"><?php 
			//generate the nested table		
			?><table class="<?php echo $colTableClassB?>" style="<?php echo $colTableStyleB?>" cellpadding="0" cellspacing="0"><?php
			while($r2=mysqli_fetch_array($resLevel2,$db_cnx)){
				$j++;
				if($j==1 && $colB[showHeader]){
					//handle header here in <thead> tag
				}
				
				if($colB[primaryKey] && $colB[primaryKeyLayout]){
					$pkB=str_replace('{primaryKey}',$r2[primaryKeyField],$colB[primaryKeyLayout]);
				}else{
					$pkB='';
				}
				?><tr id="<?php echo $pkB?>"<?php echo $hsB.$labelB?>><?php
				//data cells for sub region
				//-------------------------------------------------------
				for($k=1;$k<=count($colFieldsetB);$k++){
					$idx=$k-1;
					?><td<?php echo $colCellVerticalAlignB[$idx]?' valign="top"':''?><?php echo $colNowrapB[$idx]?' nowrap':''?>><?php
					if(isset($colIconMapB[$idx])){
						if($x=$colIconMapB[$idx][$r2[$colHandleB[$idx]]]){
							echo "<img src='$x' border='0'/>";
						}else{
							//display nothing
						}
					}else if(!$colHideDisplayB[$idx]){
						echo $r2[$colHandleB[$idx]];
					}
					?></td><?php
				}
				//-------------------------------------------------------
				?></tr><?php
			}
			?></table><?php
			//this is the closing table row for the wrapper

			?></td></tr><?php
		
		
		}else{
			//no sub-rows for this
			?><tr id="__<?php echo $pk?>" style="display:none"><td<?php echo $colSpan>1?' colspan="'.$colSpan.'"':''?>></td></tr><?php
		}
	}
}
if($i>0){
	?></tbody></table></div><?php
}
//--------------------------------------------------
if($srcregion && $tgtregion){
	?><script>window.parent.<?php echo $tgtregion?>.innerHTML=<?php echo $srcregion?>.innerHTML;</script><?php
}
if(!$hideHeaders){?></body>
</html><?php }?>