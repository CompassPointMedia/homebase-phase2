<?php if(false){ ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><?php } ?>
<?php
/*created 2012-08-10
per Ric G's suggestions and some ideas I have..

FOR GOOGLE PRODUCTS:
DESCRIPTION SIMILAR TO AMAZON OR EBAY
LINK TO ACCOUNT PAGE
LINK TO IMAGE 1 AND 2: http://www.historicmapsrestored.com/mm5/images/WIAP0001a.jpg

todo
----
DONE be able to change the fields value - it is not sticking right now

implement delimiter - with custom combo
escaping method - address
implement a jQuery listener in basic parameters - who am I what am I listening for and when do I need to listen for it and what do I do when I hear it, what departments do I change
ondelete profile "this profile has a history (has been used). you are not an administrator and cannot delete a profile which has a history"


2012-09-07
	when transitioning from add profile to a saved profile, several things need to "appear"; grayed-out options need to be enabled, and history table needs to show
2012-09-06
	DONE	show/hide unselected rows
	DONE	the available profiles dropdown - needs to respect detectChange
	DONE	time to store the settings so that I could in theory do a re-export.  Store a total snapshot of setting AT THE TIME OF EXPORT
	also need a button to close that does the same
	drag-drop of field rows
	auto-rewriting criteria clause
	show the other aggregated tables on the view up where it says primary table
	onchange = title = '*'+title, plus color change
	add expressions on the bottom and allow them to be either hbstringvalue OR mysqlexpression
	add colors and iconography to this - multi-gradients for key items + images on the tabs and change states
	
	DATA TRANSLATION IS THE KEY
	
	dynamic worksheet with "primers"
	
	develop ignorePreviouslyExported
	[translate data..]
	cross product or pseudo- on for example a comma-separated field value like 
	ability to 4-fold a table by an unqualified join "all rows uniformly by a table"
	

2012-09-05
field labels are all string.  Currently no variable support is provided for labels.  
For field values, there are two types of formats; 
	1. Home Base string/variable format, which is a will replace %firstname% with that field value on that row, and 
	2. (mysql) expression format, which will be taken literally.
	
Additionally, any table can have registered expressions.  An example would be fullname = CONCAT(FirstName,' ',LastName).  In a profile this can be expressed in string/variable format as %fullname%; these are case insensitive.
Next, it is possible to aggregate tables to the primary table.  The system handles all aliases automatically.  to aggregate a table you need 
	1. an alias for the table which must be unique and contain only letters and numbers[1]
	2. a join type.  Options are OUTER JOIN, or "join regardless", and INNER JOIN, or "join only if".  An outer join is also the same as a LEFT JOIN
	3. a join criterion or criteria.  These are normal MySQL expressions and must use table aliases used to that point.



[1] Note: the root or primary table will always have an alias of a


*/

$profileVersion='1.0';

$dTA[1]='tinyint';
$dTA[2]='smallint';
$dTA[3]='mediumint';
$dTA[4]='int';
$dTA[5]='bigint';
//decimal fields
$dTA[6]='float';
$dTA[7]='double';
$dTA[8]='decimal';
//time fields
/* ----------- old vs. new; dTA=dbTypeArray -------- */
$newThresholds=true;
$dTA[9]='date';			if($newThresholds)$dTA[9]='date';
$dTA[10]='datetime';	if($newThresholds)$dTA[10]='year';
$dTA[11]='timestamp';	if($newThresholds)$dTA[11]='datetime';
$dTA[12]='time';		if($newThresholds)$dTA[12]='timestamp';
$dTA[13]='year';		if($newThresholds)$dTA[13]='time';
//text fields
$dTA[14]='char';
$dTA[15]='varchar';
$dTA[16]='tinyblob';
$dTA[17]='tinytext';
$dTA[18]='text';
//long text fields
$dTA[19]='blob';			if($newThresholds)$dTA[19]='enum';
$dTA[20]='mediumblob';		if($newThresholds)$dTA[20]='set';
$dTA[21]='mediumtext';		if($newThresholds)$dTA[21]='blob';
$dTA[22]='longblob';		if($newThresholds)$dTA[22]='mediumblob';
$dTA[23]='longtext';		if($newThresholds)$dTA[23]='mediumtext';
$dTA[24]='enum';			if($newThresholds)$dTA[24]='longblob';
$dTA[25]='set';				if($newThresholds)$dTA[25]='longtext';
$dataThresholds=array(
	0=>'Logical',
	1=>'Integer',
	6=>'Decimal',
	9=>'Date',
	11=>'Date/time',
	13=>'Time',
	14=>'Text',
	21=>'Long text',
	100=>'Unknown',
);

if(!function_exists('array_to_csv'))require($FUNCTION_ROOT.'/function_array_to_csv_v200.php');
if(!function_exists('attach_download'))require($FUNCTION_ROOT.'/function_attach_download_v100.php');
function sql_get_fields($table,$options=array()){
	extract($options);
	global $sql_get_fields,$dataThresholds,$dTA;
	ob_start();
	$a=q("EXPLAIN $table", O_ARRAY, ERR_ECHO);
	$err=ob_get_contents();
	ob_end_clean();
	if($err)return;
	
	foreach($a as $v){
		$key=strtolower($v['Field']);
		$fields[$key]=$v;
		if($v['Type']=='timestamp')$sql_get_fields['hasTimestamp']=true;
		if($key=='resourcetoken')$sql_get_fields['hasResourceToken']=true;
		preg_match('/^([a-z]+)(\([0-9 ,]+\))*/i',$v['Type'],$attrib);
		$fields[$key]['DTYPE']=$attrib[1];
		$fields[$key]['DATTRIB']=preg_replace('/\(|\)/','',$attrib[2]);
		$k=array_search($fields[$key]['DTYPE'],$dTA);
		foreach($dataThresholds as $o=>$w){
			if($k>=$o){
				$fields[$key]['type']=$w;
			}else{
				break;
			}
		}
		//to get other goodies look in function mysql_declare_field_attributes_rtcs
	}
	return $fields;
}
function export_parse($n,$mode,$options=array()){
	extract($options);
	global $export_parse,$sql_get_fields,$Tables_ID,$tables;
	if(!isset($export_parse['expressions'])){
		$a=unserialize(base64_decode(q("SELECT Settings FROM system_tables WHERE ID=$Tables_ID", O_VALUE)));
		$export_parse['expressions']=$a['expressions'];
	}
	if(count($a['expressions']))$expressionsRegex='[a:]*('.implode('|',array_keys($a['expressions'])).')';
	
	/*
	NOTE!!! As of 2012-09-06 we are still not exporting
	legal hbstringvariable:
	%firstname% 				(if not specified, a: will be added if tables present)
	%a:firstname%				(better notation)
	%a:firstname%, %b:title%	
	%a:firstname%, %b:title		(use of another table alias)
	%a:firstname%, %catsubcat%	(%catsubcat% will be replaced by the original expression which will in turn be replaced by a. aliasing)
	
	
	*/
	if($mode=='hbstringvariable'){
		//escape the % sign
		$rand=md5(rand(1,1000));
		$n=str_replace('\%',$rand,trim($n));
		$nopct=str_replace('%','',$n);
		//return any non-variable strings
		if(strlen($n)-strlen($nopct)==0) return '\''.str_replace('\'','\\\'',str_replace($rand,'%',$n)).'\'';
		if(fmod(strlen($n)-strlen($nopct),2))error_alert('error in expression: uneven number of % characters');

		//replace first % with $rand2.%, second with %.$rand2
		$rand2=md5(rand(1001,2000));
		$i=0;
		$p=-1;
		$_n_=$n;
		while(true){
			$i++;
			$p=strpos($n,'%',$p+1);
			$_n_=substr_replace($_n_, fmod($i,2) ? $rand2.'%' : '%'.$rand2, $p+(strlen($rand2) * ($i-1)), 1);
			if(!strlen(substr($n,$p+1,10000)))break;
		}
		//create an array containing %fields% - note that %field1%%field2% will not explode with a space in between
		$n=explode($rand2,str_replace($rand2.$rand2,$rand2,$_n_));
		if(!strlen($n[count($n)-1]))unset($n[count($n)-1]);
		if(!strlen($n[0]))unset($n[0]);
		foreach($n as $o=>$w){
			//convert to a string or a field, OR an expression
			if(strstr($w,'%')){
				$w=str_replace('%','',$w);
				/* 2012-09-06
				this is a fairly complex process; so far I am not checking for the presence of the declared field unless !alias and multiple tables are present (which is a pretty sound starting approach)
				
				*/
				$w=explode(':',$w);
				if(count($w)==1){
					$alias='';
					$field=$w[0];
				}else{
					$alias=$w[0];
					$field=$w[1];
				}
				$multi=(count($tables)>1);
				if($alias){
					$n[$o]=$alias.'.';
					$g=$tables[strtolower($alias)][strtolower($field)];
					$n[$o]=($g['expression'] ? $g['expression'] : $g['Field']);
				}else{
					if(!$multi){
						$n[$o]=$field;
					}else{
						//this could be wrong but we have a good shot at it with unique fields a user might intend
						foreach($tables as $p=>$x){
							if($y=$x[strtolower($field)]){
								$n[$o]=($y['expression'] ? '' : $p.'.').($y['expression']?$y['expression']:$y['Field']);
								break;
							}
						}
					}
				}
			}else{
				$n[$o]='\''.str_replace('\'','\\\'',str_replace($rand,'%',$w)).'\'';
			}
		}
		return (count($n)==1 ? implode('',$n) : 'CONCAT('.implode(', ',$n).')');
	}else if('mysqlexpression'){
		//we still need to prepend aliases to fields if they are not there
	}
}
function write_menu($options=array()){
	extract($options);
	if(!$type)$type='toddler';
	if(!$menuID)$menuID='definitionTools';

	if($type=='toddler'){
		if(!$alignment)$alignment='mouse,20,-40';
		if(!$inner)$inner=ob_get_contents();
		ob_end_clean();
		?><style type="text/css">
		.balloonWrap{
			width:50px;
			position:absolute;
			visibility:hidden;
			left:50px;
			top:50px;
			}
		.dropshadow{
			left:10px; /* offset to left */
			top:24px; /* height of tick mark - 1 to cover border + 10 for offset */
			width:250px;
			height:145px;
			background-color:#333333;
			opacity:.45;
			filter:alpha(opacity=45);
			position:absolute;
			z-index:499;
			}
		.indices{
			position:absolute;
			z-index:501;
			background-image:url('/images/i/arrows/indices-style01-up.png');
			background-position:top left;
			background-repeat:no-repeat;
			top:0px;
			right:0px;
			width:17px; /* actual size of the tick mark */
			height:15px;
			}
		.balloonContent{
			background-color:white;
			border:1px solid sienna;
			width:250px;
			height:145px;
			position:absolute;
			z-index:500;
			top:14px; /* height of tick mark minus 1 */
			}
		.balloonContent .spd{
			padding:5px 10px;
			}
		#balloonKill{
			float:right;
			text-align:center;
			width:15px;
			height:15px;
			color:white;
			background-color:darkred;
			font-size:10px;
			margin:1px;
			cursor:pointer;
			}
		.wordDef{
			cursor:pointer;
			}
		.reexport{
			color:#ccc;
			}
		.italic{
			font-style:italic;
			}
		</style>
		<script language="javascript" type="text/javascript">
		AssignMenu('<?php echo $objectRegex?$objectRegex:'none'?>','<?php echo $menuID?>', '<?php echo $alignment?>');
		</script>
		<div id="<?php echo $menuID;?>" class="balloonWrap" precalculated="<?php echo $precalculated?>">
			<div class="dropshadow">
			</div>
			<div class="balloonContent">
				<div id="balloonKill" onclick="hidemenuie5(event)">X</div>
				<div id="<?php echo $menuID?>_content" class="spd">
					<?php echo $inner;?>
				</div>
			</div>
			<div class="indices">
			</div>
		</div><?php
	}
}
function exportmanager_history($ID, $options=array()){
	/*
		<a href="exportmanager.php?subview=history&display=table&ID=<?php echo $ID;?>" target="w2">reload history table</a>
	*/
	extract($options);
	if(!$display)$display='table'; // or export (individual page for an export)
	if($display=='table'){
		?><div id="history">
		<table id="historyTable" class="yat">
		<thead>
		<tr>
			<th>Ref#</th>
			<th>Date</th>
			<th>Time</th>
			<th>by..</th>
			<th>Feed(s) Used</th>
			<th>Records</th>
			<th>Format/Notes</th>
			<th>Actions</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if($a=q("SELECT * FROM gen_batches WHERE Profiles_ID='$ID' ORDER BY CreateDate", O_ARRAY)){
			$i=0;
			foreach($a as $n=>$v){
				$i++;
				$e=unserialize(base64_decode($v['Settings']));
				$s=$e['_SESSION'];
				$p=$e['_POST'];
				?><tr id="r_<?php echo $v['ID'];?>" class="<?php echo $v['Batches_ID']?'reexport':''?>">
					<td class="tac"><?php echo $v['ID'];?></td>
					<td><?php echo date('n/j/Y',strtotime($v['CreateDate']));?></td>
					<td><?php echo date('g:iA',strtotime($v['CreateDate']));?></td>
					<td><?php echo $s['admin']['firstName'];?></td>
					<td>
					<?php
					$b=explode(',',$v['SubType']);
					if(in_array('ExportToEmail',$b)){
						?>&nbsp;<a class="wordDef" id="r<?php echo $v['ID']?>_ExportToEmail" onclick="something=this;hidemenuie5(event,1);showmenuie5(event,1);return false;" oncontextmenu="return false;" href="<?php echo $p['data']['ExportToEmailAddress'];?>#"><img src="/images/i-local/sm-email.png" height="14" title="email attachment" /></a><?php
					}
					if(in_array('ExportAsAttachment',$b)){
						?>&nbsp;<a class="wordDef" id="r<?php echo $v['ID']?>_ExportAsAttachment" onclick="something=this;hidemenuie5(event,1);showmenuie5(event,1);return false;" oncontextmenu="return false;" href="<?php echo $p['data']['ExportFileName'];?>#"><img src="/images/i-local/sm-download.png" height="14" title="download" /></a><?php
					}
					if(in_array('ExportToAPI',$b)){
						?>&nbsp;<a class="wordDef" id="r<?php echo $v['ID']?>_ExportToAPI" onclick="something=this;hidemenuie5(event,1);showmenuie5(event,1);return false;" oncontextmenu="return false;" href="<?php echo $p['data']['APIURL'];?>#"><img src="/images/i-local/sm-settings.png" height="14" title="push to API" /></a><?php
					}
					?></td>
					<td><?php echo $v['Quantity'];?></td>
					<td title="<?php echo h($v['Notes']);?>"><?php
					echo substr($v['Notes'],0,35).(strlen($v['Notes'])>35?'...':'');
					?></td>
					<td>
					[<a href="exportmanager.php?subview=history&display=instance&ID=<?php echo $ID;?>&Batches_ID=<?php echo $v['ID'];?>" onclick="return ow(this.href,'l1_instances','450,450');">view..</a>]&nbsp;[<a href="exportmanager.php?subview=history&display=table&action=delete&ID=<?php echo $ID;?>&Batches_ID=<?php echo $v['ID'];?>" onclick="if(!confirm('Are you sure you want to delete this feed batch from the history?  This cannot be undone'))return false;" title="delete this batch from this profile's history" target="w2">delete..</a>]<?php
					if(!$v['Batches_ID']){
						?>&nbsp;[<a href="exportmanager.php?suppressPrintEnv=1&mode=updateProfile&submode=exportprofile&reexport=1&ID=<?php echo $ID?>&Batches_ID=<?php echo $v['ID'];?>" target="w2" onclick="if(!confirm('This will re-export this feed with the same records as before (unless they have been deleted). Continue?'))return false;" title="re-export this data feed and all components">re-export..</a>]<?php
					}
					?>
					</td>
				</tr><?php
			}
		}else{
			?><tr>
			<td colspan="100%"><em class="gray">No history avaialable for this profile</em></td>
			</tr><?php
		}
		?>
		</tbody>
		</table>
		</div><?php
	}else{
		//single export display - need Batches_ID and SubType
		$a=q("SELECT * FROM gen_batches WHERE ID=$Batches_ID", O_ROW);
		$Settings=unserialize(base64_decode($a['Settings']));
		$data=$Settings['_POST']['data'];
		//prn($data);
		//don't need these for now
		unset($data['label'],$data['value'],$data['use']);
		$b=q("SELECT * FROM system_profiles WHERE ID=$ID", O_ROW);
		//prn($b);
		$c=q("SELECT * FROM system_tables WHERE ID='".$b['Tables_ID']."'", O_ROW);
		$table=$c['SystemName'];
		?>
		<h2>Feed History - <?php echo $b['Name'];?></h2>
		<p>
		Exported: <strong><?php echo date('n/j/Y \a\t g:iA',strtotime($a['CreateDate']));?></strong><br />
		By: <?php echo $Settings['_SESSION']['admin']['firstName'].' '.$Settings['_SESSION']['admin']['lastName'];?><br />
		<?php if(false){ ?>
		<div class="special"> this was a auto-scheduled export </div>
		<?php } ?>
		<?php if($a['Batches_ID']){ ?>
		<div class="balloon1">NOTE: this is a re-export from batch reference #<?php echo $a['Batches_ID']?></div>
		<?php } ?>
		<h3>Export method(s):</h3>
		<?php 
		if($data['ExportToEmail'])$m['Email']=$data['ExportToEmailAddress'];
		if($data['ExportAsAttachment']){
			if($data['exportTo']=='stdout'){
				$m['Immediate']=true;
			}
			if($data['exportTo']=='file'){
				$fileName=preg_replace('/%c%/i',$a['Quantity'],$data['ExportFileName']);
				$fileName=preg_replace('/%u%/i',$Settings['_SESSION']['admin']['userName'],$fileName);
				if(preg_match('/%d:([^%]+)%/i',$fileName,$matches)){
					$fileName=str_replace($matches[1],date($matches[1],strtotime($b['CreateDate'])),$fileName);
				}
				if($data['ExportFileFolder']){
					$folderName=preg_replace('/%c%/i',$a['Quantity'],$data['ExportFileFolder']);
					$folderName=preg_replace('/%u%/i',$Settings['_SESSION']['admin']['userName'],$folderName);
					if(preg_match('/%d:([^%]+)%/i',$folderName,$matches)){
						$folderName=str_replace($matches[1],date($matches[1],strtotime($b['CreateDate'])),$folderName);
					}
				}
				$m['SavedFile']=$folderName.($folderName?'/':'').$fileName;
			}
		}
		if($data['ExportToAPI']){
			$m['API']=$data['APIURL'];
		}		
		?>
		<div class="bq">
		<?php
		if($n=$m['Email']){
			?>
			To email: <strong><?php echo $n;?></strong><br />
			<?php
		}
		if($m['Immediate']){
			?>A<?php echo count($m)>1?'lso a':''?>s <u>immediate download</u><br /><?php
		}
		if($n=$m['SavedFile']){
			?>
			As file saved to: <strong><?php echo $n;?></strong><br />
			<?php
		}
		if($m['Email'] || $m['Immediate'] || $m['SavedFile']){
			?>
			Format: Delimiter=<?php echo $data['delimiter']==','?'Comma':($data['delimiter']=="\t"?'Tab':$data['delimiter']);?><br />
			<?php
		}
		if($n=$m['API']){
			?>
			To API: <strong><?php echo $n;?></strong><br />
			<?php
		}
		?>
		</div>
		Records at time of export: <?php echo $a['Quantity'];?><br />
		<?php if($n=$data['ExportComments']){ ?>
		Comments: <span class="inlineComment"><?php echo $n;?></span><br />
		<?php } ?>
		<br />
		<br />
		Primary table: <strong><?php echo $table;?></strong><br />
		Secondary table(s): <strong><?php
		if($t=$data['table'])
		foreach($t as $n=>$v)if(!trim($v))unset($t[$n]);
		if(empty($t)){
			echo '<span class="gray">(None)</span>';
		}else{
			echo implode(', ',$t);
		}
		?></strong><br />
		Criteria: <span class="SQL"><?php echo $data['filter'];?></span><br />
		Excluding records previously exported: <strong><?php echo $data['ignorePreviouslyExported']?'Yes':'No';?><br />
		<br />
		</strong><br /> <!-- N/A? -->
		
		<input type="button" name="Button" value="Close" onclick="window.close();" />
		</p>
		<?php
	}
	//prn($p);
}




for($__i__=1; $__i__<=1; $__i__++){ //------------------------------ break loop -----------------------------

##############------------------- SECTION 1: MODES AND SUBMODES ----------------------###############
//process any update requests

//shutdown coding
if($mode && !$shutdownRegistered){
	$shutdownRegistered=true;
	$assumeErrorState=true;
	register_shutdown_function('iframe_shutdown');
	ob_start('store_html_output');
}
if($mode && !$suppressPrintEnv){
	if(!empty($_POST))prn($_POST);
	if(!empty($_GET))prn($_GET);
}
if($mode=='insertTable' || $mode=='updateTable' || $mode=='deleteTable'){
	if($mode=='deleteTable'){
		if($c=q("SELECT COUNT(*) FROM system_profiles WHERE Tables_ID='$Tables_ID'", O_VALUE))error_alert('This table cannot be removed; it has profiles associated with it.  You must first delete the profiles before removing this table from the export manager');
		q("DELETE FROM system_tables WHERE ID=$Tables_ID");
		?><script language="javascript" type="text/javascript">
		window.parent.g('r_<?php echo $Tables_ID;?>').style.display='none';
		</script><?php
		eOK();
	}

	//type
	ob_start();
	q("SHOW CREATE VIEW $table", ERR_ECHO);
	$err=ob_get_contents();
	ob_end_clean();
	$Type=($err?'table':'view');

	//level
	if(!$Level)$Level=1;
	
	//settings
	$Settings=($mode==$insertMode?array():unserialize(base64_decode(q("SELECT Settings FROM system_tables WHERE SystemName='$table'", O_VALUE))));

	if($newName || $newExpression){
		if($newName xor $newExpression)error_alert('you must choose a name (token) AND an expression to create a new field expression');
		ob_start();
		$a=q("SELECT ".stripslashes($newExpression)." FROM $table LIMIT 1", O_VALUE, ERR_ECHO);
		$err=ob_get_contents();
		ob_end_clean();
		if($err)error_alert('Expression is not valid');			
		if($Settings['expressions']){
			foreach($Settings['expressions'] as $n=>$v){
				if($n==strtolower($newName))error_alert('You have a duplicate expression name');
			}
		}
		$Settings['expressions'][strtolower($newName)]=stripslashes($newExpression);
	}

	if($ID=q("SELECT ID FROM system_tables WHERE SystemName='$table'", O_VALUE) /* $mode==$insertMode */){
		q("UPDATE system_tables SET
		Name='$Name',
		SystemName='$table',
		KeyField='$KeyField',
		Description='$Description',
		Type='$Type',
		Level='$Level',
		Settings='".base64_encode(serialize($Settings))."'
		WHERE ID=$ID");
	}else{
		$ID=q("INSERT INTO system_tables SET
		Name='$Name',
		SystemName='$table',
		KeyField='$KeyField',
		Description='$Description',
		Type='$Type',
		Level='$Level',
		Settings='".base64_encode(serialize($Settings))."'
		", O_INSERTID);
	}
	prn($qr);
	//refresh always for now
	?><script language="javascript" type="text/javascript">
	window.parent.location='exportmanager.php?view=<?php echo $view;?>&subview=<?php echo $subview?>&table=<?php echo $table?>';
	</script><?php
	eOK(__LINE__);
}
if($mode=='insertProfile' || $mode=='updateProfile' || $mode=='deleteProfile'){
	if($submode=='exportprofile'){
		if($reexport){
			$a=q("SELECT Settings, Type, SubType, Status, Quantity, Errors FROM gen_batches WHERE ID=$Batches_ID", O_ROW);
			$Settings=unserialize(base64_decode($a['Settings']));
			$bufferSession=$_SESSION;
#			$_SESSION=$Settings['_SESSION'];
			//there is not post to buffer; this would be called by query string
			$_POST=$Settings['_POST'];
			extract($_POST);
			$Profiles_ID=$ID;
		}
		//error checking
		if(!$data['ExportToEmail'] && !$data['ExportAsAttachment'] && !$data['ExportToAPI'])error_alert('Select at least one type of export for this feed on the Export tab (by email, download, or API push)');

		//get data
		$record=q("SELECT * FROM system_tables WHERE ID=$Tables_ID",O_ROW);

		if($data['ExportToEmail']){
			$data['ExportToEmailAddress']=str_replace('(email addresses, separate multiple by commas)','', $data['ExportToEmailAddress']);
			if(!$data['ExportToEmailAddress'])error_alert('select an email(s) to send the export to, or uncheck "Export to email"');
			$emails=explode(',',str_replace(' ','',$data['ExportToEmailAddress']));
			foreach($emails as $v)if(!valid_email($v))error_alert(count($emails)==1?'Your email address is not valid':'At least one email address is not valid');
		}
		//now we take into account tables to be aggregated
		$data['tables']=array_transpose(array(
			'active'=>$data['active_hidden'],
			'table'=>$data['table'],
			'alias'=>$data['alias'],
			'join'=>$data['join'],
			'criteria'=>$data['criteria'],
			'notes'=>$data['notes'],
		));
		unset(
			$data['active'],
			$data['active_hidden'],
			$data['table'],
			$data['alias'],
			$data['join'],
			$data['criteria'],
			$data['notes']
		);
		$data['tables']=stripslashes_deep($data['tables']);
		foreach($data['tables'] as $n=>$v)if(!trim($v['table']) || !$v['active'])unset($data['tables'][$n]);
		if(count($data['tables'])){
			foreach($data['tables'] as $n=>$v){
				$joinClause.=(trim($v['notes']) ? "\n". '/*'.str_replace('/*','',str_replace('*/','',$v['notes'])).'*/': '')."\n".$v['join']. ' '.$v['table']. ' `'.$v['alias'].'` ON '.$v['criteria'];
			}
		}

		$table=$record['SystemName'];
		$Settings=(strlen($record['Settings'])>9 ? unserialize(base64_decode($record['Settings'])) : array());
		$tables['a']=sql_get_fields($table);
		if($a=$Settings['expressions']){
			foreach($a as $n=>$v){
				$tables['a'][strtolower($n)]=array(
					'Field'=>$n,
					'expression'=>preg_replace('/\b('.implode('|',array_keys($tables['a'])).')\b/i','a.$1',$v),
				);
			}
		}
		$aliases['a']=$table;
		foreach($data['tables'] as $n=>$v){
			if($v['table']!=$table){
				$aliases[strtolower($v['alias'])]=$v['table'];
				$tables[strtolower($v['alias'])]=sql_get_fields($v['table']);
				if(($Settings=q("SELECT Settings FROM system_tables WHERE SystemName='".$v['table']."'", O_VALUE)) && strlen($Settings)>9){
					$Settings=unserialize(base64_decode($Settings));
					if($a=$Settings['expressions']){
						foreach($a as $o=>$w){
							$tables[strtolower($v['alias'])][strtolower($o)]=array(
								'Field'=>$o,
								'expression'=>preg_replace('/\b('.implode('|',array_keys($tables[strtolower($v['alias'])])).')\b/i',$v['alias'].'.$1',$w),
							);
						}
					}
				}
			}
		}
		//build the query
		$sql='SELECT '."\n";
		$selectClause='';
		if(count($data['use']))
		foreach($data['use'] as $n=>$v){
			$selectClause.=export_parse($data['value'][$n],'hbstringvariable');
			$selectClause.=' AS \''.$data['label'][$n].'\','."\n";
		}
		if(count($data['useexpr']))
		foreach($data['useexpr'] as $n=>$v){
			$selectClause.=export_parse($data['exprval'][$n],'hbstringvariable');
			$selectClause.=' AS \''.$data['exprcol'][$n].'\','."\n";
		}
		foreach($data['additionalexpr'] as $n=>$v){
			if(!strlen(trim($v)) || !strlen(trim($data['additionalexprval'][$n])))continue;
			if($data['additionalexprdisp'][$n]=='mysqlExpression'){
				$selectClause.=stripslashes($data['additionalexprval'][$n]);
			}else{
				$selectClause.=stripslashes(export_parse($data['additionalexprval'][$n],'hbstringvariable'));
			}
			$selectClause.=' AS \''.$data['additionalexpr'][$n].'\','."\n";
		}
		$selectClause=rtrim($selectClause,",\n");
		//notice changes that are maded by reexport=1; from clause in parenthesized and then we join ge_batches_entries
		$sql.=$selectClause."\nFROM ".($reexport?'(':'').$table .' `a`';
		$sql.=$joinClause;
		if($reexport)$sql.=")\n";
		if($reexport)$sql.=" JOIN gen_batches_entries _b_ ON _b_.Batches_ID=$Batches_ID AND a.ID=_b_.Objects_ID\n";
		//here with the reexport, we lose the where clause since rows are implicit by join to gen_batches_entries
		if(!$reexport && ($data['filter'] || $sql_get_fields['hasResourceToken'])){
			$sql.="\nWHERE ";
			$whereClause=array();
			if($sql_get_fields['hasResourceToken'])$whereClause[]='(a.ResourceType IS NOT NULL)';
			if($data['filter'])$whereClause[]='('.stripslashes($data['filter']).')';
			$whereClause=implode(' AND ',$whereClause);
			$sql.=$whereClause;
		}
		if($data['order'])$sql.="\nORDER BY ".$data['order'];
		ob_start();
		$result=q($sql, O_ARRAY, ERR_ECHO);
		$err=ob_get_contents();
		ob_end_clean();
		if($err){
			$err=end(explode('Query failed with the following:',$qr['err']));
			$err=current(explode('<br />',$err));
			error_alert(addslashes(str_replace("\r",'',str_replace("\n",'',trim($err)))));
		}
		$str=array_to_csv(
			$result,
			true,
			array(
				'delimiter'=>$data['delimiter'],
				'qt'=>$data['wrapper'],
				'escQt'=>($data['delimiter']=="\t"?'"':'""'),
				'suppressQuotes'=>($data['wrapMethod']==0?true:false),
			)
		);

		if(!$qr['count'])error_alert('No records in this table!');
		//export file name if needed
		if($name=$data['ExportFileName']){
			$name=preg_replace('/%c%/i',$qr['count'],$name);
			$name=preg_replace('/%t%/i',$table,$name);
			$name=str_replace('/%u%/i',sun(),$name);
			if(preg_match('/%d[:]([^%]+)%/i',$name,$m))$name=str_replace($m[0],date($m[1]),$name);
		}else{
			$name=$table.'_'.date('Ymd_His').'_['.$qr['count'].'].csv';
		}
		
		if($data['ExportToEmail']){
			$path=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/tmp/'.$name;
			$fp=fopen($path,'w');
			fwrite($fp,$str,strlen($str));
			fclose($fp);
			$fileArray=$path;
			$emailTo=implode(',',$emails);
			
			$emailTo=$developerEmail;
			
			$emailSource=$_SERVER['DOCUMENT_ROOT'].'/gf5/console/emails/email_9000_export_attach.php';
			require($_SERVER['DOCUMENT_ROOT'] . '/components/emailsender_03.php');
			if(!$data['ExportAsAttachment'])error_alert('Your data has been successfully emailed'.(count($emails)>1?' to '.count($emails).' email addresses':''),1);
		}
		//download the file
		if($data['ExportAsAttachment'])attach_download($file='', $str, $name, '', array('suppressExit'=>true, 'headerSet'=>false));
		$suppressNormalIframeShutdownJS=true;
		if($RecordBatch){
			//we will store the export in gen_batches
			if($data['ExportToEmail'])$subtypes[]='ExportToEmail';
			if($data['ExportAsAttachment'])$subtypes[]='ExportAsAttachment';
			if($data['ExportToAPI'])$subtypes[]='ExportToAPI';
			$Batches_ID=q("INSERT INTO gen_batches SET
			".($reexport ? "Batches_ID=$Batches_ID,":'')."
			Profiles_ID=$Profiles_ID,
			Description='Data Export/feed from Export Manager',
			StartTime=NOW(),
			StopTime=NOW(),
			Type='Export',
			SubType='".implode(',',$subtypes)."',
			Status='Complete',
			Quantity='".count($result)."',
			Source='$MASTER_HOSTNAME',
			Process='".end(explode('/',__FILE__))."',
			Settings='".base64_encode(serialize(array('_SESSION'=>$_SESSION, '_POST'=>stripslashes_deep($_POST))))."',
			Notes='".str_replace('Enter export comments here..','',$data['ExportComments'])."',
			CreateDate=NOW(),
			Creator='".sun()."'", O_INSERTID);
			if(!$reexport && $a=q(str_replace($selectClause,'a.ID',$sql), O_COL)){
				foreach($a as $v)q("INSERT INTO gen_batches_entries SET Batches_ID=$Batches_ID, ObjectName='$table', Objects_ID=$v");
			}
		}
		//revert to normal session vars
		if($reexport)$_SESSION=$bufferSession;

		eOK();
	}
	if($mode=='deleteProfile'){
		//need to check if it has been used - if so delete history
		q("DELETE FROM system_profiles WHERE ID=$Profiles_ID");
		?><script language="javascript" type="text/javascript">
		window.parent.g('r_<?php echo $Profiles_ID?>').style.display='none';
		</script><?php
		eOK(__LINE__);
	}
	foreach($data['additionalexpr'] as $n=>$v){
		if(!strlen(trim($v)) || !strlen(trim($data['additionalexprval'][$n])))
		unset($data['additionalexpr'][$n], $data['additionalexprval'][$n], $data['additionalexprdisp'][$n]);
	}
	$data['tables']=array_transpose(array(
		'active'=>$data['active_hidden'],
		'table'=>$data['table'],
		'alias'=>$data['alias'],
		'join'=>$data['join'],
		'criteria'=>$data['criteria'],
		'notes'=>$data['notes'],
	));
	unset(
		$data['active'],
		$data['active_hidden'],
		$data['table'],
		$data['alias'],
		$data['join'],
		$data['criteria'],
		$data['notes']
	);	
	$Settings=($mode==$updateMode && ($n=q("SELECT Settings FROM system_profiles WHERE ID=$ID", O_VALUE)) ? unserialize(base64_decode($n)) : array());
	$Settings['data']=stripslashes_deep($data);
	$Settings=base64_encode(serialize($Settings));
	if($mode==$insertMode)unset($ID);
	$sql=sql_insert_update_generic($MASTER_DATABASE,'system_profiles',$mode);
	$x=q($sql, O_INSERTID);
	if($mode==$insertMode)$ID=$x;
	?><script language="javascript" type="text/javascript">
	if(<?php echo $mode==$insertMode?'true':'false';?>)window.parent.g('mode').value='<?php echo $updateMode;?>';
	if(<?php echo $mode==$insertMode?'true':'false';?>)window.parent.g('ID').value='<?php echo $ID;?>';
	window.parent.detectChange=0;
	</script><?php
	if($mode=='insertProfile'){
		?><script language="javascript" type="text/javascript">
		window.parent.g('mode').value='updateProfile';
		window.parent.g('ID').value='<?php echo $ID;?>';
		window.parent.g('RecordBatch').disabled=false;
		window.parent.g('RecordBatchWrap').onclick='';
		//add to the dropdownlist
		window.parent.g('_profiles').options[window.parent.g('_profiles').length]= new Option('<?php echo $Name;?>',<?php echo $ID?>);		
		window.parent.g('_profiles').selectedIndex=window.parent.g('_profiles').length-1;
		window.parent.g('data[ignorePreviouslyExported]').disabled=false;
		</script><?php
	}
	error_alert($mode==$insertMode?'New profile has been created':'Profile updated OK');
	eOK(__LINE__);
}

if(!$view)$view='tables';
if(!$refreshComponentOnly && !$suppressPrintEnv){
	?><style type="text/css">
	.lateNight{
		border-collapse:collapse;
		}
	.lateNight td{
		border:1px solid #666;
		padding:2px 10px;
		}	
	.scroll{
		border:2px solid #99BD0C;
		overflow:scroll;
		width:80%;
		height:300px;
		}
	.scroll:focus{
		border:2px solid burlywood;
		}
	.unused{
		background-color:#eee;
		color:#888;
		}
	.unused input{
		color:#666;
		}
	td.leftNav{
		padding:0px;
		width:140px;
		}
	.leftNav ul{
		list-style:none;
		padding:0px;
		margin:0px;
		width:100%;
		}
	.leftNav li{
		border-bottom:1px dotted #666;
		padding:2px 7px;
		}
	#fieldGrid thead tr{
		border:1px solid Gold;
		border-bottom:none;
		}
	#fieldGrid th{
		background-color:LightSteelBlue;
		vertical-align:bottom;
		padding:2px 10px;
		color:white;
		}
	#RecordBatchWrap{
		float:right;
		margin-top:7px;
		}
	td.nobo{
		border:none;
		}
	.codeScroll{
		width:600px;
		height:500px;
		overflow:scroll;
		border:1px solid darkred;
		padding:5px;
		margin:10px 0px;
		background-color:#fef6ff;
		}
	</style>
	<script language="javascript" type="text/javascript">
	function toggleNewProfile(o){
		window.location='exportmanager.php?view=profiles&Tables_ID='+o.value;
	}
	function toggleProfiles(n,t){
		if(detectChange && !confirm('You have made changes to this view.  Are you sure you want to leave?')){
			g('_profiles').value=g('_profiles').getAttribute('initialvalue');
			return false;
		}
		window.location='exportmanager.php?view=profiles&Profiles_ID='+n+'&Tables_ID='+t;
		return true;
	}
	function toggleRow(o){
		g('r_'+o.id.replace(/[^0-9]+/g,'')).className=(o.checked?'':'unused');
		if(!o.checked && !g('showUnselectedRows').checked)g('r_'+o.id.replace(/[^0-9]+/g,'')).style.display='none';
	}
	function regTable(o){
		if(!o.value)return
		return ow('exportmanager.php?view=tables&subview=managetable&table='+o.value,'l2_regtable','600,700');
	}
	function getWordDef(){
		var n=something.id.split('_');
		var o=something.href.split('#');
		var str='';
		alert(n[1]);
		switch(n[1]){
			case 'ExportToEmail':
				str='<strong>Emailed to:</strong> '+o[0];
			break;
			case 'ExportAsAttachment':
				str='<strong>File name:</strong> '+o[1];
			break;
			case 'ExportToAPI':
				str='<strong>API URL:</strong> '+o[1];
			break;						
		}
		g('definitionTools_content').innerHTML=str;
	}
	function toggleUnselectedRows(o){
		$('.unused').css('display',(o.checked?'table-row':'none'));
	}
	var toggle=true;
	function toggleAll(o){
		alert('not developed');
		return false;
		toggle=!toggle;
		$('#fieldGrid input[type="checkbox"]').each();
	}
	</script><?php
}

##############---------------------- SECTION 2: SUBVIEWS ---------------------#############
if($subview=='quickexport'){
	$fields=sql_get_fields($table);
	$data=array_to_csv(q("SELECT * FROM $table ". ($sql_get_fields['hasResourceToken']?'WHERE ResourceToken IS NOT NULL':''), O_ARRAY));
	if(!$qr['count'])error_alert('No records in this table!');
	attach_download($file='', $data, $nameAs=$table.'_'.date('Ymd_His').'_['.$qr['count'].'].csv');
	//so easy..
}else if($subview=='managetable'){
	//sql table data
	$fields=sql_get_fields($table);
	
	//bread crumbs
	?><h2><?php echo $view . ' &raquo; '. $subview;?></h2><?php
	
	//this to get table record data
	$insertMode='insertTable';
	$updateMode='updateTable';
	$deleteMode='deleteTable';
	if($record=q("SELECT * FROM system_tables WHERE SystemName='$table'", O_ROW)){
		$mode=$updateMode;
		extract($record);
		if(strlen($Settings))$Settings=unserialize(base64_decode($Settings));
	}else{
		$mode=$insertMode;
	}

	if($submode=='testExpression'){
		?><h1>Expression Tester</h1>
		Table: <?php echo $table;?><br />
		<pre><?php echo stripslashes($expression);?></pre><?php
		ob_start();
		$a=q("SELECT ".($KeyField ? $KeyField.',':'').stripslashes($expression)." FROM $table ".($KeyField?"ORDER BY $KeyField":''), O_COL_ASSOC, ERR_ECHO);
		$err=ob_get_contents();
		ob_end_clean();
		if($err){
			prn($err,1);
		}else{
			?><table>
			<?php
			if($a){
				?><thead>
				<tr><th>Key Field (<?php echo $KeyField;?>)</th>
				<th>Result</th>
				</tr></thead><?php
				foreach($a as $n=>$v){
					?><tr><td><?php echo $n?></td>
					<td><?php echo $v?></td>
					</tr><?php
				}
			}else{
				?><tr><td colspan="100%">No records available, but expression is valid!</td></tr><?php
			}
			?></table><?php
		}
		?><input type="button" name="Button" value="Close" onclick="window.close();" /><?php
		break; //end break loop
	}


	get_contents_enhanced('start');
	?><table><?php
	foreach($fields as $n=>$v){
		?><tr>
		<td><?php echo $v['Field'];?></td>
		<?php 
		if($loadValues){
			//show dropdown lists of data
		}
		?>
		</tr><?php
	}
	?></table><?php
	$fieldString=get_contents_enhanced('noecho,cxlnextbuffer');

	?><h2>Table: <?php echo $table;?></h2>
	<?php echo '<form id="form1" name="form1" method="post" action="" target="w2">';?>
	  Table name: 
	  <input name="Name" type="text" id="Name" value="<?php echo h($Name?$Name:$table);?>" />
	  <span class="gray">(used in exports; use letters, numbers and underscores)</span><br />
	  System name: <span class="gray"><?php echo $table;?></span>
	  <br />
	  Key field: 
	  <select name="KeyField" id="KeyField">
	  <option value="">&lt;Select..&gt;</option>
	  <?php
	  foreach($fields as $v){
	  	?><option value="<?php echo $v['Field'];?>" <?php echo $KeyField==$v['Field']?'selected':''?>><?php echo $v['Field'];?></option><?php
	  }
	  ?>
      </select>
	   <span class="gray">(used in creating reports and testing)</span><br />
	  Description and export info:<br />
	  <textarea name="Description" cols="40" rows="4" id="Description"><?php echo h($Description);?></textarea>
<br />


	  <h3>Fields</h3>
	  <div class="scroll">
	  <?php 
	  echo $fieldString;
	  ?>
	  </div>
	  <h3>Expressions</h3>
	  <?php
	  if($Settings['expressions']){
	  	$i=0;
	  	?><table><?php
		  foreach($Settings['expressions'] as $n=>$v){
		  	$i++;
			?><tr>
			<td><input type="text" name="_Settings[expressions][name][]" value="<?php echo $n;?>" onchange="dChge(this);" id="name<?php echo $i?>" /></td>
			<td><textarea name="_Settings[expressions][expression][]" rows="2" cols="30" onchange="dChge(this);" id="expression<?php echo $i;?>"><?php echo h($v);?></textarea></td>
			<td>[<a href="exportmanager.php?subview=managetable&submode=testExpression&table=<?php echo $table;?>&expression=" onclick="return ow(this.href+escape(g('expression<?php echo $i;?>').value),'l3_test','400,450');">test..</a>]</td>
			</tr><?php
		  }
		?></table><?php
	  }else{
	  
	  }
	  ?>
	  Add new expression:<br />
		Name: <input name="newName" type="text" id="newName" size="20" /> 
		<em class="gray">letters, numbers and underscore only</em><br />
		Value: <textarea name="newExpression" rows="2" cols="30" onchange="dChge(this);" id="newExpression"></textarea><br />
		[<a href="exportmanager.php?subview=managetable&submode=testExpression&table=<?php echo $table;?>&expression=" onclick="return ow(this.href+escape(g('newExpression').value),'l3_test','400,450');">test..</a>]
	  <br />
	  <input type="submit" name="Submit" value="OK" />
	  <input name="mode" type="hidden" id="mode" value="<?php echo $mode;?>" />
	  <input name="insertMode" type="hidden" value="<?php echo $insertMode;?>" />
	  <input name="updateMode" type="hidden" value="<?php echo $updateMode;?>" />
	  <input name="deleteMode" type="hidden" value="<?php echo $deleteMode;?>" />
	  <input name="submode" type="hidden" id="submode" value="" />
	  <input name="table" type="hidden" value="<?php echo $table;?>" />
	  <input name="ID" type="hidden" id="ID" value="<?php echo $ID;?>" />
    <?php echo '</form>';?>
	
	<?php
	break; //end break loop
}else if($subview=='history'){
	if($action=='delete'){
		error_alert('here2');
		q("DELETE FROM gen_batches WHERE ID=$Batches_ID");
		q("DELETE FROM gen_batches_entries WHERE Batches_ID=$Batches_ID");
		?><script language="javascript" type="text/javascript">
		window.parent.g('r_<?php echo $Batches_ID?>').style.display='none';
		</script><?php
		eOK();
	}
	$options=array(
		'display'=>$display,
		'Batches_ID'=>$Batches_ID,
	);
	exportmanager_history($ID,$options);
	if($display=='table'){
		?><script language="javascript" type="text/javascript">
		window.parent.g('history').innerHTML=document.getElementById('history').innerHTML;
		</script><?php
	}
	eOK();
}

#############---------------------- SECTION 3: VIEWS ----------------------##############
ob_start();
switch(true){
	case $view=='tables':
		?>
		<h2>Tables</h2>
		<table id="thisTable" class="myTable">
		<thead>
		<tr>
			<th>&nbsp;</th>
			<th>Name</th>
			<th>Fields</th>
			<th>Description</th>
			<th>Size</th>
			<th>Last Activity</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if($systemTables=q("SELECT SystemName, s.* FROM system_tables s WHERE 1 ORDER BY Name", O_ARRAY_ASSOC)){
			$i=0;
			foreach($systemTables as $v){
				extract($v);
				unset($sql_get_fields);
				$fields=sql_get_fields($SystemName);
	
				ob_start();
				$records=q("SELECT COUNT(*) FROM $SystemName WHERE ".($sql_get_fields['hasResourceToken'] ? 'ResourceType IS NOT NULL':1), O_VALUE, ERR_ECHO);
				$err=ob_get_contents();
				ob_end_clean();
				if($err){
					mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals('error querying table'),$fromHdrBugs);
					continue;
				}else{
				
				}
				if($sql_get_fields['hasTimestamp'] && $records)$latestActivity=q("SELECT MAX(".$sql_get_fields['hasTimestamp'].") FROM $SystemName WHERE ".($sql_get_fields['hasResourceToken'] ? 'ResourceType IS NOT NULL':1), O_VALUE);

				$data=q("SHOW TABLE STATUS LIKE '$SystemName'", O_ROW);
				unset($data['Name']);
				extract($data);
				
				$i++;
				$j++;
				?><tr id="r_<?php echo $v['ID'];?>" class="<?php echo !fmod($j,2)?'alt':''?>">
					<td>[<a href="exportmanager.php?mode=deleteTable&Tables_ID=<?php echo $v['ID'];?>" onclick="if(!confirm('Are you sure you want to remove this table from the export manager? (This will not delete the actual table itself)'))return false;" target="w2">delete</a>]</td>
					<td><a href="exportmanager.php?view=tables&subview=managetable&table=<?php echo $SystemName;?>" title="click to edit this table's settings" onclick="return ow(this.href,'l2_regtable','600,700');"><?php echo $Name;?></a><br />
					<span class="gray"><?php echo $SystemName;?></span>				</td>
					<td><?php echo count($fields);?></td>
					<td><?php echo $Description;?></td>
					<td><?php echo $Data_length >= 1024*1024 ? round($Data_length/1024/1024,2).'MB' : round($Data_length/1024,2).'kb';?></td>
					<td><?php echo $latestActivity ? date('n/j/Y \a\t g:iA',strtotime($latestActivity)) : '<span class="gray" title="information not available">N/A</span>';?></td>
				<td><a title="Quick Download" href="exportmanager.php?view=tables&subview=quickexport&table=<?php echo $SystemName?>&suppressPrintEnv=1" target="w2"><img src="/images/i-local/flash-download.png" alt="img" width="25" height="28" /></a></td>
				<td><a title="Download/Profile" href="exportmanager.php?view=profiles&Tables_ID=<?php echo $ID;?>"><img src="/images/i-local/user_male_olive_blue_black.png" alt="img" width="32" height="28" /></a></td>
				</tr><?php
			}
		}else{
			?><tr>
			<td colspan="100%"><em class="gray">No records found for that criteria</em></td>
			</tr><?php
		}
		if(minroles()<=ROLE_DBADMIN){
			?><tr>
			<td colspan="100%">
			Set up another table:
			<select name="table" id="table" onchange="return regTable(this);">
			<option value="">&lt;Select..&gt;</option>
			<?php
			$a=q("SHOW TABLES IN $MASTER_DATABASE", O_ARRAY);
			foreach($a as $v){
				$table=$v['Tables_in_'.$MASTER_DATABASE];
				if($systemTables[$table])continue;
				?><option value="<?php echo $table;?>"><?php echo $table;?></option><?php
			}
			?>
			</select>		</td>
			</tr><?php
		}
		?>
		</tbody>
		</table>
		<?php
	break;
	case $view=='profiles':
	if(!$Profiles_ID && !$Tables_ID){
		?>
		<h2>Profiles</h2>
		<table id="profiles" class="yat">
		<thead>
			<tr>
			<th>&nbsp;</th>
			<th>Profile Name</th>
			<th>Primary Table</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if($a=q("SELECT p.*, t.SystemName, t.Name AS TableName FROM system_profiles p LEFT JOIN system_tables t ON p.Tables_ID=t.ID WHERE 1 ORDER BY p.Name", O_ARRAY)){
			$i=0;
			foreach($a as $v){
				$i++;
				extract($v);
				?><tr id="r_<?php echo $ID;?>">
				<td>[<a onclick="if(!confirm('Are you sure you want to permanently delete this profile?'))return false;" href="exportmanager.php?mode=deleteProfile&Profiles_ID=<?php echo $ID;?>" target="w2" title="delete this profile">delete</a>]</td>
				<td><a title="Open this profile" href="exportmanager.php?view=profiles&Profiles_ID=<?php echo $ID;?>"><h3 class="nullTop"><?php echo $Name;?></h3></a></td>
				<td><?php echo $TableName?> <span class="gray">(<?php echo $SystemName;?>)</span></td>
				</tr><?php
			}
		}
		?>
		<tr>
		<td colspan="100%">
		<?php
		if($tables=q("SELECT ID, SystemName, Name FROM system_tables ORDER BY Name", O_ARRAY)){
			?>
			Create a new profile with this table: 
			<select name="_table_" id="_table_" onchange="toggleProfiles('',this.value);">
			<option value="">&lt;Select..&gt;</option>
			<?php
			foreach($tables as $v){
				?><option value="<?php echo $v['ID']?>"><?php echo $v['Name'] . ($v['Name']!==$v['SystemName'] ? ' ('.$v['SystemName'].')':'');?></option><?php
			}
			?></select><?php
		}else{
			?>To create a profile, you must first set up one or more tables.  <a href="exportmanager.php?view=tables">Click here to begin</a><?php
		}
		?>
		</td>
		</tr>
		</tbody>
		</table><?php
		break;
	}
	/* 2012-08-15 @11:45AM - this can be presented with a profile id or simply a tables_id - NOT a table name
	move this code up as far as necessary
	
	*/
	$insertMode='insertProfile';
	$updateMode='updateProfile';
	$deleteMode='deleteProfile';
	
	if($Profiles_ID){
		$mode=$updateMode;
		if(!($record=q("SELECT * FROM system_profiles WHERE ID=$Profiles_ID", O_ROW)))exit('Error, <a href="exportmanager.php">click here to redirect</a>');
		if($record['Version']==1.0){
			extract($record);
			strlen($Settings)>7? $pSettings=unserialize(base64_decode($Settings)) : $pSettings=array();
			if(!($t=q("SELECT * FROM system_tables WHERE ID=$Tables_ID", O_ROW)))exit('Error, <a href="exportmanager.php">click here to redirect</a>');
			$Tables_ID=$t['ID'];
			$table=$t['SystemName'];
			$tableLabel=$t['Name'];
			strlen($t['Settings'])>7?$tSettings=unserialize(base64_decode($t['Settings'])) : $Settings=array();
		}else{
			exit('unrecognized version');
		}
		$ID=$Profiles_ID;
	}else if($Tables_ID){
		$mode=$insertMode;
		unset($ID);
		if(!($t=q("SELECT * FROM system_tables WHERE ID='$Tables_ID'", O_ROW)))exit('Error, <a href="exportmanager.php">click here to redirect</a>');
		unset($table['ID']);
		$table=$t['SystemName'];
		$tableLabel=$t['Name'];
		strlen($t['Settings'])>7?$tSettings=unserialize(base64_decode($t['Settings'])) : $tSettings=array();
	}
	$data=$pSettings['data'];
	?>
	<div class="fr">
	<input type="submit" name="Submit" value="Save Profile Changes" onclick="g('submode').value='';g('suppressPrintEnv').value=0;" />&nbsp;
    <input type="submit" name="Submit" value="Export Now" onclick="g('submode').value='exportprofile';g('suppressPrintEnv').value=1;" /> 
	<br />
	<label id="RecordBatchWrap" onclick="<?php if($mode==$insertMode)echo 'alert(\'You can only do this after you have saved this profile\');';?>"><input name="RecordBatch" type="checkbox" id="RecordBatch" value="1" checked="checked" onchange="dChge(this);" <?php echo $mode==$insertMode?'disabled':''?> /> Log this export in History</label><br />
	<textarea name="data[ExportComments]" cols="30" rows="2" id="data[ExportComments]" onchange="dChge(this);" onfocus="if(this.value=='Enter export comments here..'){this.className='';this.value='';}" onblur="if(this.value==''){this.value='Enter export comments here..';this.className='gray italic';}" class="gray italic">Enter export comments here..</textarea>
	</div>
	<h2><a href="exportmanager.php?view=profiles" title="Main profile list">Profiles</a> : Edit and Export</h2>
	<h3><?php echo $secondaryTables ? 'Tables':'Primary table'?>: <strong><?php echo $tableLabel;?></strong> <span class="gray">(<?php echo $table;?>)</span></h3>
	Available profiles: 
	<select name="_profiles_" id="_profiles" initialvalue="<?php echo $Profiles_ID;?>" onchange="return toggleProfiles(this.value,<?php echo $Tables_ID;?>);">
	<?php
	$profiles=q("SELECT ID, Name FROM system_profiles WHERE Tables_ID=$Tables_ID ORDER BY Name", O_COL_ASSOC);
	?>
	<option value=""><?php echo $profiles?'&lt;Create a new profile..&gt;':'&lt;none, creating new profile now&gt;';?></option>
	<?php
	foreach($profiles as $n=>$v){
		?><option value="<?php echo $n;?>" <?php echo $Profiles_ID==$n?'selected':'';?>><?php echo h($v);?></option><?php
	}
	?>
	</select>
	<br />
	<br />
	<p>Profile name: 
	  <input name="Name" type="text" id="Name" onchange="dChge(this);" value="<?php echo h($Name);?>" size="60" />
	  <br />
	Description:<br />
	<textarea name="Description" id="Description" rows="2" cols="65" onchange="dChge(this);"><?php echo h($Description);;?></textarea>
	<br />


	<?php ob_start(); //begin tabs ?>
	<label><input type="checkbox" value="1" name="nofield" onclick="toggleAll(this)" onchange="dChge(this);" /> Select/unselect all fields</label>
	<br />
	<br />

	<input type="hidden" value="0" name="data[showUnselectedRows]" />
	<label><input type="checkbox" value="1" name="data[showUnselectedRows]" id="showUnselectedRows" <?php echo $data['showUnselectedRows']?'checked':'';?> onclick="toggleUnselectedRows(this);" /> Show unselected rows/fields</label>
	
	<table id="fieldGrid" class="yat">
	<thead>
      <tr>
        <th><h2 class="nullBottom">Fields</h2></th>
        <th valign="bottom">type</th>
        <th valign="bottom" class="tac">u</th>
        <th valign="bottom">label</th>
        <th valign="bottom">value</th>
        <th valign="bottom">format</th>
      </tr>
	</thead>
	<?php
	$fields=sql_get_fields($table);
	$i=0;
	foreach($fields as $n=>$v){
		$i++;
		?><tr id="r_<?php echo $i;?>" class="<?php echo !isset($data['use']) || $data['use'][$n]?'':'unused';?>" <?php if(!$data['showUnselectedRows'] && !(!isset($data['use']) || $data['use'][$n]))echo 'style="display:none;"';?>>
	  <td><label title="<?php echo $v['Field'];?>">
		<input type="checkbox" name="data[use][<?php echo $n;?>]" id="use<?php echo $i;?>" value="1" <?php echo !isset($data['use']) || $data['use'][$n]?'checked':''?> onchange="dChge(this);toggleRow(this)" />          
		<?php echo substr($v['Field'],0,32).(strlen($v['Field'])>32?'..':'');?>
		</label>
	  </td>
		<td><?php echo $v['type'];?></td>
		<td><div align="center"><?php
		$a=q("SELECT COUNT(DISTINCT ".$v['Field'].") AS a, COUNT(*) AS b FROM $table WHERE ".$v['Field']." IS NOT NULL", O_ROW);
		echo $a['a']==$a['b']?'y':'&nbsp;';
		?></div></td>
		<td><input name="data[label][<?php echo $n;?>]" id="label<?php echo $i;?>" type="text" value="<?php echo $data['label'][$n]?h($data['label'][$n]):$v['Field'];?>" onchange="dChge(this);" /></td>
		<td><input name="data[value][<?php echo $n;?>]" id="value<?php echo $i;?>" type="text" value="<?php echo isset($data['value'][$n])?h($data['value'][$n]):'%'.$v['Field'].'%';?>" onchange="dChge(this);" /></td>
		<td class="tc">string</td>
	</tr><?php
	}
	if($a=$tSettings['expressions']){
		?><tr>
		<td colspan="100%"><h2>Expressions</h2></td>
		</tr><?php
		foreach($a as $n=>$v){
			$i++;
			?><tr id="r_<?php echo $i;?>" class="<?php echo $data['useexpr'][$n]?'':'unused';?>">
				<td><label><input type="checkbox" name="data[useexpr][<?php echo $n;?>]" value="1" <?php echo $data['useexpr'][$n]?'checked':'';?> onchange="dChge(this);toggleRow(this);" id="use<?php echo $i?>" />
				  <?php echo $n;?></label></td>
				<td>expression <a title="click to see expression" href="#" onclick="alert('<?php echo str_replace("'","\'",h($v));?>');return false;">[i]</a> </td>
				<td class="na">&nbsp;</td>
				<td><input name="data[exprcol][<?php echo $n;?>]" type="text" value="<?php echo $data['exprcol'][$n]?h($data['exprcol'][$n]):$n;?>" onchange="dChge(this);" /></td>
				<td><input name="data[exprval][<?php echo $n;?>]" type="text" value="<?php echo $data['exprval'][$n]?h($data['exprval'][$n]):'%'.$n.'%';?>" onchange="dChge(this);" /></td>
				<td class="tc">string</td>
		</tr><?php
		}
	}
	?>
	<tr>
	<td colspan="1">&nbsp;</td>
	<td colspan="5"><h2>Additional export columns:</h2></td>
	</tr>
	<?php
	for($j=1; $j<=count($data['additionalexpr'])+5; $j++){
		?><tr>
			<td colspan="1">&nbsp;</td>
			<td colspan="2"><input type="text" name="data[additionalexpr][<?php echo $j;?>]" value="<?php echo h($data['additionalexpr'][$j]);?>" onchange="dChge(this);" /></td>
			<td colspan="2">
			<textarea name="data[additionalexprval][<?php echo $j;?>]" rows="2" cols="50" onchange="dChge(this);"><?php echo h($data['additionalexprval'][$j]);?></textarea></td>
			<td><select name="data[additionalexprdisp][<?php echo $j;?>]" onchange="dChge(this);">
			<option value="hbstringvariable" <?php echo $data['additionalexprdisp'][$j]=='hbstringvariable'?'selected':''?>>string</option>
			<option value="mysqlExpression" <?php echo $data['additionalexprdisp'][$j]=='mysqlExpression'?'selected':''?>>mySQL expr.</option>
			</select>
			</td>
		</tr>
		<?php
	}
	?>
    </table>
	<?php
	//------------------------- store fields ------------------------------
	get_contents_tabsection('section_fields');
	$data['tables'][]=
	array(
		'type'=> 'aggregate', /* aggregate=default, also can be values like '(' and '((' and '))' */
		'active'=>true, /* active = default, or unset also=true */
		'alias'=>'',
		'join'=>'OUTER JOIN',
		'table'=>'',
		'criteria'=>'',
		'notes'=>'',
	);
	?>
	<table id="jointables" class="yat">
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>Table</th>
		<th>Alias</th>
		<th>Join Type</th>
		<th>Criteria</th>
		<th>Notes</th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($a=$data['tables']){
		$i=0;
		foreach($a as $n=>$v){
			$i++;
			?><tr>
				<td>ctrl</td>
				<td><input name="data[active][]" type="checkbox" value="1" <?php echo !isset($v['active']) || $v['active']?'checked':''?> onchange="dChge(this);" onclick="this.nextSibling.value=(this.checked?1:0);" /><input type="hidden" name="data[active_hidden][]" value="<?php echo !isset($v['active']) || $v['active']?'1':''?>" /></td>
				<td><input name="data[table][]" type="text" value="<?php echo $v['table'];?>" size="12" /></td>
				<td><input name="data[alias][]" type="text" value="<?php echo $v['alias'];?>" size="3" /></td>
				<td><select name="data[join][]">
				<option value="LEFT JOIN" <?php if($v['join']=='LEFT JOIN')echo 'selected';?>>Join regardless (OUTER JOIN)</option>
				<option value="JOIN" <?php if($v['join']=='JOIN')echo 'selected';?>>Join only if (INNER JOIN)</option>
				</select></td>
				<td><input type="text" name="data[criteria][]" value="<?php echo $v['criteria'];?>" /></td>
				<td><input type="text" name="data[notes][]" value="<?php echo $v['notes'];?>" /></td>
			</tr><?php
		}
		?><tr>
		<td class="addRow">+
		</td>
		<td colspan="6" class="nobo">&nbsp;</td>
		</tr><?php
	}else{
		?><tr>
		<td colspan="100%"><em class="gray">Add a table join</em></td>
		</tr><?php
	}
	?>
	</tbody>
	</table>
	<br />
	<br />
	GROUP RECORDS BY: 
	<input name="data[groupBy]" type="text" id="data[groupBy]" onchange="dChge(this);" value="<?php echo h($data['groupBy']);?>" size="60" /> 
	<span class="gray">(optional)</span>
	<br />
	<br />
	<br />
	Sort records by:
	<input name="data[orderBy]" type="text" id="data[orderBy]" onchange="dChge(this);" value="<?php echo h($data['orderBy']);?>" size="60" /> 
	<span class="gray">(optional)</span>
	<br />
	<?php
	//------------------------- store fields ------------------------------
	get_contents_tabsection('section_tables');
	?>
	
	<label><input name="data[ignorePreviouslyExported]" type="checkbox" id="data[ignorePreviouslyExported]" <?php echo $mode==$insertMode?'disabled':''?> <?php echo $data['ignorePreviouslyExported']? 'checked':''?> value="1" onchange="dChge(this);" />
	Exclude records that have been previously exported by this profile</label>
	<br />
	<br />

	<strong>Filter</strong> (&quot;where&quot; clause of mySQL query) :<br />
	<textarea name="data[filter]" cols="45" rows="2" id="filter" onchange="dChge(this);"><?php echo h($data['filter']);?></textarea>
	<br />
	<span class="gray">NOTE: if you are aggregating other tables for this feed, you should use the table alias next to any field that might be ambiguous.  So for example, instead of &quot;ID BETWEEN 387 AND 496&quot;, enter &quot;a.ID BETWEEN 387 AND 496&quot;.  The prefix a is always the `alias` for the primary table.</span>

	<?php
	//------------------------- store fields ------------------------------
	get_contents_tabsection('section_criteria');
	?>
	<div>
	Separate fields with a 
	<select name="data[delimiter]" id="data[delimiter]" onchange="dChge(this);">
	<option value="," <?php echo $data['delimiter']==','?'selected':''?>>Comma</option>
	<option value="	" <?php echo $data['delimiter']=="\t"?'selected':''?>>Tab</option>
	<option value="|" <?php echo $data['delimiter']=='|'?'selected':''?>>Pipe (|)</option>
	<option value="^" <?php echo $data['delimiter']=='^'?'selected':''?>>Caret (^)</option>
	</select>
	<br />
	Enclose (quote) fields with a 
    <select name="data[wrapper]" id="data[wrapper]" onchange="dChge(this);">
      <option value="" <?php echo $data['wrapper']==''?'selected':''?>>&lt;nothing&gt;</option>
      <option <?php echo $data['wrapper']=='"' || !isset($data['wrapper'])?'selected':''?> value="&quot;">Double Quote (&quot;)</option>
    </select>
    <br />
    <select name="data[wrapMethod]" id="data[wrapMethod]" onchange="dChge(this);">
	<option value="2" <?php echo $data['wrapMethod']==2?'selected':''?>>Wrap all fields in quotes</option>
	<option value="1" <?php echo $data['wrapMethod']==1?'selected':''?>>Wrap only non-numeric fields in quotes</option>
	<option value="0" <?php echo $data['wrapMethod']==='0'?'selected':''?>>&lt;Do not wrap fields in quotes&gt;</option>
    </select>	
    <br />
    <br />
	<h2>XML Information</h2>
	Row-level format:<br />
    <textarea name="data[rowLevelFormat]" cols="45" rows="2" id="filter" onchange="dChge(this);"><?php echo h($data['rowLevelFormat']);?></textarea>
    <br />
	Field-level format:<br />
	<textarea name="data[fieldLevelFormat]" cols="45" rows="2" id="filter" onchange="dChge(this);"><?php echo h($data['fieldLevelFormat']);?></textarea>
	</div>
	<?php
	//------------------------- store format ------------------------------
	get_contents_tabsection('section_format');
	?>
	<p class="gray">You can select multiple export methods for this export/feed.  At least one method must be selected</p>
	<label>
	<input name="data[ExportToEmail]" type="hidden" value="0" />
	<input name="data[ExportToEmail]" type="checkbox" id="data[ExportToEmail]" value="1" <?php echo !isset($data['ExportToEmail']) || $data['ExportToEmail'] ? 'checked':'';?> onchange="dChge(this);" /> 
	Export to email:</label>
	<input name="data[ExportToEmailAddress]" type="text" id="data[ExportToEmailAddress]" value="<?php echo h($data['ExportToEmailAddress']?$data['ExportToEmailAddress']:'(email addresses, separate multiple by commas)');?>" size="50" class="<?php echo !$data['ExportToEmailAddress']?'gray':''?>" onfocus="if(this.value=='(email addresses, separate multiple by commas)'){this.value='';this.className='';}" onblur="if(this.value==''){this.value='(email addresses, separate multiple by commas)';this.className='gray';}" onchange="dChge(this);" />
	<em class="gray">(separate multiple by commas)</em>
	<br />
	<br />
	<br />
	<label><input name="data[ExportAsAttachment]" type="checkbox" id="data[ExportAsAttachment]" value="1" onchange="dChge(this);" <?php echo $data['ExportAsAttachment']?'checked':''?> /> Export as attachment</label>
	<br />
	File name for export: 
	<input name="data[ExportFileName]" type="text" id="data[ExportFileName]" onchange="dChge(this);" value="<?php echo h($data['ExportFileName'] ? $data['ExportFileName'] : '');?>" size="40" />
	[<a href="javascript:alert('%c% = record count\n%t% = primary table\n%d:Ymd_His% = PHP date variables\n%u% = your user name\n\nFor a list of PHP date variables go to http://php.net/date');">click to see filename wildcard values</a>]<br />
	<label><input id="exportToStdout" name="data[exportTo]" type="radio" value="stdout" onchange="dChge(this);" <?php echo !$data['exportTo'] || $data['exportTo']=='stdout'?'checked':''?> /> 
	Export <u>right now</u></label><br />
	<label><input id="exportToFile" name="data[exportTo]" type="radio" value="file" <?php echo $data['exportTo']=='file'?'checked':''?> /> 
	Save in this secure folder:</label>
	<span class="green">documents/</span><input name="data[ExportFileFolder]" type="text" id="data[ExportFileFolder]" onchange="dChge(this);" value="<?php echo h($data['ExportFileName'] ? $data['ExportFileName'] : '');?>" size="40" /> <span class="gray">(wildcard variables allowed here also)</span>
	<br />
	<br />
	<br />
	<label><input type="checkbox" name="data[ExportToAPI]" id="data[ExportToAPI]" value="1" onchange="dChge(this);" <?php echo $data['ExportToAPI']?'checked':''?> /> Push to an API</label><br />
	URL:
	<input name="data[APIURL]" type="text" id="data[APIURL]" onchange="dChge(this);" value="<?php echo h($data['APIURL']);?>" size="40" />
	<br />
	<br />
	</p>
	<?php
	//------------------------- store export ------------------------------
	get_contents_tabsection('section_export');
	?>
	<p class="gray">Scheduling has not been developed yet.  This will allow you to set up a schedule to run this Feed Profile, for example each day at 2AM, etc.</p>	
	<?php
	//------------------------- store schedule ------------------------------
	get_contents_tabsection('section_schedule');
	//-------------------- menu creator ----------------------
	ob_start();
	?><!-- initial menu will be filled in here --><?php
	$options=array(
		'type'=>'toddler',
		'objectRegex'=>'^r[0-9]+_E',
		'menuID'=>'definitionTools',
		'precalculated'=>'getWordDef()'
	);
	write_menu($options);
	//------------------ end menu creator ---------------------
	exportmanager_history($ID);
	
	
	//------------------------- store history ------------------------------
	get_contents_tabsection('section_history');
	//help section
	if(false){ ?>
	<div style="display:none;"><?php }
	?>
	<h3>What is a Feed?</h3>
	<p>Basically, a feed is a way to get data from one location to another. The &quot;from&quot; in this case is your Home Base database, which consists of multiple tables of data that are often joined together in various ways to create invoices, orders, customer lists, customer history and more. The &quot;to&quot; represents a file download, an email sent out with the file attached, or a &quot;push&quot; to a remote source such as an API. Feeds can be in different formats, but the most common is a comma-separated value, or CSV format. This may also be referred to sometimes as a &quot;flat file&quot; format. Other formats that feeds can be in include XML, SQL query commands, or even zip files containing images or documents. </p>
	<h3>Feed History Storage </h3>
	<p>
	Note that feed history storage can only be done for profiles that have first been saved. Below the Export Now button is a checkbox option to "Log this export in history". If this is checked, then whatever type of feed(s) you select will be listed on the History tab. NOTE that the profile does not store any actual files that you exported, only the profile settings at the time of export or download. This means your feed may not be identical to the last time if:</p>
	<ul>
	  <li>You have changed the records that were exported</li>
	  <li>You have deleted or added records (depending on your filter criteria)</li>
	  <li>You have deleted or added columns to tables used by the profile </li>
	  </ul>
	<p>However, the system will attempt to pull the same exact records that were pulled in the previous feed unless you request otherwise. So if you are exporting all products in the category &quot;Tableware&quot;, then normally a re-export of the data will export those exact items, UNLESS you select the option to &quot;forget specific records&quot;. In this case, your export will contain all Tableware products exported previously, plus any new products that have since been added. NOTE: even if you do not check &quot;forget specific records&quot;, your re-export cannot contain products that have since been deleted. This underscores the importance of backing up data.</p>
	<p>&nbsp; </p>
	<div class="codeScroll">
	<?php
	prn('tSettings');
	prn($tSettings);
	prn('pSettings');
	prn($pSettings);
	?>
	</div>
	
	<?php
	if(false){ ?></div><?php }
	//------------------------- store help ------------------------------
	get_contents_tabsection('section_help');
	tabs_enhanced(array(
		'section_fields'=>array(
			'label'=>'Fields'
		),
		'section_tables'=>array(
			'label'=>'Tables'
		),
		'section_criteria'=>array(
			'label'=>'Criteria'
		),
		'section_export'=>array(
			'label'=>'Export'
		),
		'section_format'=>array(
			'label'=>'Format'
		),
		'section_schedule'=>array(
			'label'=>'Schedule'
		),
		'section_history'=>array(
			'label'=>'History'
		),
		'section_help'=>array(
			'label'=>'Help'
		),
	));
	?>
	<br />
	<input name="mode" type="hidden" id="mode" value="<?php echo $mode;?>" />
	<input name="insertMode" type="hidden" value="<?php echo $insertMode;?>" />
	<input name="updateMode" type="hidden" value="<?php echo $updateMode;?>" />
	<input name="deleteMode" type="hidden" value="<?php echo $deleteMode;?>" />
	<input name="submode" type="hidden" id="submode" value="" />
	<input name="version" type="hidden" id="version" value="<?php echo $Version?$Version:$profileVersion;?>" />
	<input name="Type" type="hidden" id="Type" value="Export" />
	<input name="Tables_ID" type="hidden" id="Tables_ID" value="<?php echo $Tables_ID;?>" />
	<input name="ID" type="hidden" id="ID" value="<?php echo $ID;?>" />
	<input name="suppressPrintEnv" type="hidden" id="suppressPrintEnv" value="" />
	<?php
	break;
}


$out=ob_get_contents();
ob_end_clean();

?>
<table width="100%" cellpadding="0" class="lateNight">
  <tr>
    <td colspan="100%" class="tar"><strong>Welcome <?php echo sun('fnln');?>; you are a <?php echo $userType[minroles()];?></strong></td>
  </tr>
  <tr>
    <td class="leftNav">
	<ul>
		<li><a href="<?php echo $_SERVER['PHP_SELF'].'?view=tables';?>">Tables</a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF'].'?view=profiles';?>">Profiles</a></li>
		<li>--</li>
		<li><a href="http://en.wikipedia.org/wiki/Web_Colors#X11_color_names" title="View web colors" onclick="return ow(this.href,'l1_colors','750,750');">Colors</a></li>
	</ul>
	</td>
    <td>
	<form name="form1" id="form1" target="w2" method="post"><?php echo $out;?></form>
	</td>
  </tr>
</table>
<?php
} //------------------------------ end break loop -----------------------------
$assumeErrorState=false;
$suppressIframeNormalShutdownJS=true;
?>