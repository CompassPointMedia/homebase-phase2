<?php 
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='generic';
$localSys['pageLevel']=0;

require_once('systeam/php/config.php');

require_once('resources/bais_00_includes.php');

require_once('systeam/php/auth_i2_v100.php');


if( $mode=='deleteRelativeFile' ||
	$mode=='uploadRelativeFile' ||
	$mode=='createRelativeFolder'){
	$name=glossary($mode=='deleteRelativeFile'?$Files_ID:$Tree_ID, $glossary);
	if(minroles()>ROLE_MANAGER && $name!=='{system:library:personal:'.sun().'}')error_alert('You do not have permission to create folders, delete or upload files to the Company Document Library.  Click on the Personal tab to upload your own files');
}
if($mode=='downloadRelativeFile'){
	$name=glossary($Files_ID,$glossary);
	if(minroles()>ROLE_MANAGER && !($name=='{system:library:personal:'.sun().'}' || $name=='{system:library:company}'))error_alert('You do not have permission to download this file');
}

if($mode=='deleteRelativeFile'){ //Files_ID
	if($Files_ID){
		$fileName=q("SELECT Name FROM relatebase_tree WHERE ID='$Files_ID'",O_VALUE);
		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/general/'.$fileName)){
			if(unlink($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/general/'.$fileName)) error_alert('Successfully Deleted',1);
			q("DELETE FROM relatebase_tree WHERE ID='$Files_ID'");
		} else {
			error_alert('Was not able to delete file, file does not exist on the server');
		}
	} else if ($Folders_ID){
		tree_delete_children($Folders_ID,$options=array('customFileRoot'=>$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/general/'));
		/*
			$files=q("SELECT * FROM relatebase_tree WHERE Tree_ID='$Folders_ID' AND Type='file'",O_ARRAY);
			q("DELETE FROM relatebase_tree WHERE Tree_ID='$Folders_ID' OR ID='$Folders_ID'");
			if(is_array($files)){
				foreach($files as $n=>$v){
					if(file_exists('/images/documentation/'.$GCUserName.'/general/documents/'.$v['Name'])){
						unlink('/images/documentation/'.$GCUserName.'/general/documents/'.$v['Name']);		
					}	
				}
			}
		*/
	}
	?><script language="javascript" type="text/javascript">window.parent.location=window.parent.location+'';</script><?php
	$assumeErrorState=false;
	exit;
}else if($mode=='uploadRelativeFile'){ //Tree_ID
	$handle=substr(md5(time().rand(100,10000)),0,5).'_';
	$ext=strtolower(end(explode('.',$_FILES['localFile']['name'])));
	if(!preg_match('/^(gif|jpg|png|xls|xlsx|doc|docx|pdf|txt|html|htm|tif|tiff|xif)$/',$ext))error_alert($ext.' is not an allowed extension! The following are allowed: gif,jpg,png,xls,xlsx,doc,docx,pdf,txt,html,htm,tif,tiff,xif');
	if(!is_uploaded_file($_FILES['localFile']['tmp_name']))error_alert('Abnormal error, unable to upload file');

	if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/general')){
		if(!mkdir($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/general')){
			mail($developerEmail, 'Error file '.__FILE__.', line '.__LINE__,get_globals($err='Abnormal error creating the general file folder; developer has been notified'),$fromHdrBugs);
			error_alert($err);
		}
	}
	$fileFullPath=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/general/'.$handle.stripslashes($_FILES['localFile']['name']);
	if(!move_uploaded_file($_FILES['localFile']['tmp_name'],$fileFullPath)){
		mail($developerEmail,'error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
		error_alert('Unable to move file to the general folder');
	}
	q("INSERT INTO relatebase_tree SET Tree_ID='$Tree_ID', Name='".$handle.$_FILES['localFile']['name']."', Type='file'");
	?><script language="javascript" type="text/javascript">
		window.parent.location=window.parent.location+'';
	</script><?php
	$assumeErrorState=false;
	exit;
}else if($mode=='createRelativeFolder'){ //Tree_ID
	if($Tree_ID){	
		q("INSERT INTO relatebase_tree SET Tree_ID='$Tree_ID', Name='$folderName', Type='Folder'");
		?><script language="javascript" type="text/javascript">window.parent.location=window.parent.location+'';</script><?php
	}
	exit;
}else if($mode=='downloadRelativeFile'){
	$file=q("SELECT * FROM relatebase_tree WHERE ID='$Files_ID'",O_ROW);
	if(!file_exists($path=$_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/general/'.$file['Name'])){
		error_alert('Unable to locate this file; it may have been deleted '.$path);
	}
	$nameAs=explode('_',$file['Name']);
	header ("Accept-Ranges: bytes");  
	header ("Connection: close");  
	header ("Content-type: ".mime_content_type($path)."");  
	header ("Content-Length: ". filesize($path));   
	header ("Content-Disposition: attachment; filename=\"".$nameAs[1]."\"");
	ob_clean();
	readfile($path);
	$suppressNormalIframeShutdownJS=true;
	$assumeErrorState=false;
	exit;
}

$hideCtrlSection=false;
$suppressForm=true;

if(minroles() > ROLE_AGENT){
	exit('You do not have access to this page; only agents and above have access');
}
if($glossary=='personal'){

}else if($glossary='company'){
	//OK
} 

/*
$place='/images/documentation/lutheran/general/documents/';
$zip = zip_open($_SERVER['DOCUMENT_ROOT'].'/admin-local/All Forms.zip');

if (is_resource($zip)) {
	while ($zip_entry = zip_read($zip)) {
		preg_match('/^(.*)\//',zip_entry_name($zip_entry),$folder);
		preg_match('/\/(.*)$/',zip_entry_name($zip_entry),$files);
		$fileExtention=zip_entry_name($zip_entry);
		$fileExtention=explode('.',$fileExtention);
		echo strlen(max($fileExtention));
		if(strlen(max($fileExtention))==3){
			$options=array('defaultTable'=>'relatebase_tree', 'lastNodeType'=>'file');
		}else{
			$options=array('defaultTable'=>'relatebase_tree');
		}
		$ID=tree_build_path(zip_entry_name($zip_entry),$options);
		$fold=tree_path_to_id($folder[0]);
		$file=tree_path_to_id(zip_entry_name($zip_entry));
		$rand=substr(md5(rand(100000,99999999)),0,5);
		echo $rand.'_'.str_replace(' ','',$files[1]).'=';
		q("UPDATE relatebase_tree SET Tree_ID='$GlossaryTree_ID', Name='".addslashes($rand.'_'.str_replace(' ','',$files[1]))."' WHERE ID='$ID'");
		$dir=opendir($_SERVER['DOCUMENT_ROOT'].'/images/documentation/lutheran/general/documents');
		while ($file=readdir($dir)) {
			$realfile=substr($file,5);
			if($realfile==str_replace(' ','',$files[1])) $skip=1;
		}
		if(!$skip && zip_entry_open($zip,$zip_entry)){
			echo str_replace(' ','',$_SERVER['DOCUMENT_ROOT'].$place.$rand.'_'.$files[1]).'<br />';
			$newfile=fopen(str_replace(' ','',$_SERVER['DOCUMENT_ROOT'].$place.$rand.'_'.$files[1]),'w');
			$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
			fwrite($newfile,$buf);
			zip_entry_close($zip_entry);
			fclose($newfile);
		}
		unset($skip);
		?>
		<?php
	}
	zip_close($zip);
} 
*/
function tree_id_to_path2($n, $options=array()){
	global $glossary;
	extract($options);
	global $tree_id_to_path;
	$row=q("SELECT Tree_ID, Name, Type FROM relatebase_tree WHERE ID='$n'", O_ROW);
	if($row['Type']=='file')$tree_id_to_path=$row;
	return ($row ? tree_id_to_path2($row['Tree_ID'],$options).'/'.($link?'<a href="'.$link.'?glossary='.$glossary.'&Folders_ID='.$n.'">':'').$row['Name'].($link?'</a>':'') : '');
}
function glossary($Tree_ID, $glossary){
	$name='{system:library:'.$glossary.($glossary=='personal' ? ':'.sun() : '').'}';
	while(true){
		$a=q("SELECT Tree_ID, Name FROM relatebase_tree WHERE ID=$Tree_ID", O_ROW);
		if(!$a)return false;
		extract($a);
		if($name==$Name /*parent is the object*/){
			return $name;
		}else if(!$Tree_ID /*could not be*/){
			return false;
		}
	}
}
//recursively build array
function build_filenames($Tree_ID, $q){
	global $build_filenames;
	if($a=q("SELECT ID, Tree_ID, Name, Title, Description, Type, IF(CreateDate, CreateDate, EditDate) AS CreateDate FROM relatebase_tree WHERE Tree_ID=$Tree_ID", O_ARRAY_ASSOC)){
		foreach($a as $n=>$v){
			if($v['Type']=='file'){
				if(
					preg_match('/'.$q.'/i',$v['Name']) ||
					preg_match('/'.$q.'/i',$v['Description']) ||
					preg_match('/'.$q.'/i',$v['Title'])){
					$build_filenames[$n]=array(
						'Name'=>$v['Name'],
						'Description'=>$v['Description'], 
						'Title'=>$v['Title'], 
						'Tree_ID'=>$v['Tree_ID'],
						'CreateDate'=>$v['CreateDate']
					);
				}
			}else{
				//folder
				build_filenames($n,$q);
			}
		}
	}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $PageTitle='Document Library - '.$AcctCompanyName?></title>



<link id="cssUndoHTML" rel="stylesheet" href="/Library/css/cssreset01.css" type="text/css" />
<link id="cssSimple" rel="stylesheet" href="/site-local/gf5_simple.css" type="text/css" />
<link id="cssData" rel="stylesheet" href="/Library/css/DHTML/data_04_i1.css" type="text/css" />
<link id="cssDHTML" rel="stylesheet" href="/Library/css/DHTML/dynamic_04_i1.css" type="text/css" />
<style type="text/css">
a{
	color:#003300;
	}
.hover td{
	background-color:cornsilk;
	}
.normal td{
	background-color:none;
	}
body{
	margin:10px 20px;
	}
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/loader_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/dataobjects_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="../../site-local/local.js"></script>
<script language="JavaScript" type="text/javascript">
/* periwinkle coding */
var thispage='<?php echo $thispage?>';
var thisfolder='<?php echo $thisfolder?>';
var browser='<?php echo $browser?>';
var ctime='<?php echo $ctime?>';
var PHPSESSID='<?php echo $PHPSESSID?>';
//for nav feature
var count='<?php echo $nullCount?>';
var ab='<?php echo $nullAbs?>';
</script>


</head>

<body id="report">
<?php if(!$suppressForm){ ?>
<form action="resources/bais_01_exe.php" method="post" enctype="multipart/form-data" name="form1" target="w2" id="form1" onSubmit="return beginSubmit();">
<?php }?>
<div id="header">


<style type="text/css">
#tabWrap{
	margin-top:33px;
	position:relative;
	}
#tabWrap a:hover{
	text-decoration:none;
	}
.tabon, .taboff{
	float:left;
	margin-right:5px;
	background-color:#fff;
	border-left:1px solid #444;
	border-right:1px solid #444;
	border-top:1px solid #444;
	-moz-border-radius: 4px 4px 0px 0px;
	border-radius: 4px 4px 0px 0px;
	cursor:pointer;
	}
.tabon{
	padding:3px 5px 8px 5px;
	margin-top:5px;
	border-bottom:1px solid white;
	}
.taboff{
	padding:3px 5px;
	margin-top:10px;
	}
#lowerline{
	border-top:1px solid #444;
	clear:both;
	margin-top:-1px;
	background-color:#99CCFF;
	}
#tabRaise{
	position:absolute;
	top:-33px;
	left:15px;
	}
</style>
<div class="fr">
	<?php echo '<form id="search" method="get">'?>
		<input type="hidden" name="glossary" id="glossary" value="<?php echo $glossary;?>" />
		<input type="text" name="q" id="q" value="" />
		<input type="submit" value="Search" size="7" />
	<?php echo '</form>'?>
	<input type="button" onClick="javascript:window.close();" value="Close" />
</div>
<br />
<br />

<div id="tabWrap">
	<div id="lowerline"> </div>
	<div id="tabRaise">
		<div id="tab_company" class="<?php echo $glossary=='company'?'tabon':'taboff'?>"><a href="intranet.php?glossary=company" title="view company documents">Company Documents</a></div>
		<div id="tab_personal" class="<?php echo $glossary=='personal'?'tabon':'taboff'?>"><a href="intranet.php?glossary=personal" title="view personal documents">Personal Documents</a></div>
	</div>
</div>


<p>&nbsp;</p>

</div>
<div id="mainBody">

<?php


if(!($GlossaryTree_ID=q("SELECT ID FROM relatebase_tree WHERE Tree_ID IS NULL AND Type='folder' AND Name='{system:library:".$glossary.($glossary=='personal'?':'.sun():'')."}'", O_VALUE))){
	$GlossaryTree_ID=q("INSERT INTO relatebase_tree SET
	Type='folder',
	Name='{system:library:".$glossary.($glossary=='personal'?':'.sun():'')."}',
	Title='Intranet Glossary - ".strtoupper($glossary)."', 
	CreateDate=NOW(),
	Creator='".sun()."'", O_INSERTID);
}

$dir=opendir($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/general');
?>
<?php
if($q){
	?><h2>Search Results: <?php echo stripslashes($q);?></h2><?php
	$q=stripslashes($q);
	build_filenames($GlossaryTree_ID,$q);
	if(count($build_filenames)){
		?><table width="100%"><?php 
		foreach($build_filenames as $n=>$v){
			$extension=end(explode('.',$v['Name']));
			?><tr id="<?php echo 'link'.$n;?>" class="normal" onMouseOver="this.className='hover';" onMouseOut="this.className='normal';"><?php
			$name=explode('_',$v['Name']);
			?>
			<td>
			<img src="/images/i/_<?php echo $extension?>.gif" align="absbottom" />&nbsp;
			<a href="/gf5/console/resources/bais_01_exe.php?=<?php echo $n;?>&mode=downloadRelativeFile&suppressPrintEnv=1" target="w2"><?php echo preg_replace('/('.$q.')/i','<strong style="font-size:larger;">$1</strong>',$name[1]?$name[1]:$name[0]);?></a>
			</td>
			<td align="right">
			<a href="/gf5/console/resources/bais_01_exe.php?=<?php echo $n;?>&mode=downloadRelativeFile&suppressPrintEnv=1" target="w2">Download</a>
			</td>
			</tr>
			<tr>
			<td colspan="100%" style="padding-bottom:12px;padding-left:25px;">
			<div class="fl gray" style="width:150px;">
			Added: <?php echo date('n/j/Y @g:iA',strtotime($v['CreateDate']));?>
			</div>
			<img src="/images/i/1148-folder1.gif" width="16" height="16" /> Location: <strong><a href="intranet.php?glossary=<?php echo $glossary;?>&Folders_ID=<?php echo $v['Tree_ID'];?>" title="Go to this folder directly"><?php
			echo tree_id_to_path($v['Tree_ID']);
			?></a></strong>
			</td>
			</tr><?php
		}
		?></table><?php
	}
	?><p><?php echo !count($build_filenames) ? 'No matches found..':''?> Search again:
	<?php echo '<form id="search" method="get">';?>
		<input type="hidden" name="glossary" id="glossary" value="<?php echo $glossary;?>" />
		<input type="text" name="q" id="q" value="" />
		<input type="submit" value="Search" />
	<?php echo '</form>'?>
	[<a href="intranet.php">Return to main folder</a>]
	</p><?php
}else{
	if(!$Folders_ID){
		$Folders_ID=$GlossaryTree_ID;
	}else{
		if(!glossary($Folders_ID,$glossary)){
			mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='just look'),$fromHdrBugs);
			?>
			Error: improper call of page<br />
			<a href="intranet.php">Go to main document library</a>
			<?php
			exit;
		}
	}
	$files=q("SELECT * FROM relatebase_tree WHERE Tree_ID='$Folders_ID' ORDER BY Type DESC",O_ARRAY);
	//bread crumbs
	?>
	<h3><img src="/images/i/1148-folder1.gif" width="16" height="16" /> <?php echo tree_id_to_path2($Folders_ID,$options=array('link'=>'intranet.php'));?>/</h3>
	<?php
	if(is_array($files)){
		?><table width="100%"><?php
		foreach($files as $n=>$v){
			if(strtolower($v['Type'])=='file'){
				$trid='link'.$n;
			}else{
				$i++;
				$trid='folder'.$i;
			}

			?><tr id="<?php echo $trid?>" class="normal" onMouseOver="this.className='hover';" onMouseOut="this.className='normal';"><?php
			$name=explode('_',$v['Name']);
			if(strtolower($v['Type'])=='file'){
				$extension=end(explode('.',$v['Name']));
				?><td>
				<img src="/images/i/_<?php echo $extension?>.gif" />
				<?php echo preg_replace('/^[0-9a-f]+_/i','',$v['Name']);?>
				<?php
				if(preg_match('/(pdf)$/',$v['Name'])){
					?>
					<a href="<?php echo '/images/documentation/'.$GCUserName.'/general/'.$v['Name']?>" target="_blank">View In Browser</a>
					<?php
				} else {
				}
				?>
				</td>
				<td align="right">
				<a href="/gf5/console/resources/bais_01_exe.php?glossary=<?php echo $glossary;?>&Files_ID=<?php echo $v['ID'];?>&mode=downloadRelativeFile&suppressPrintEnv=1" target="w2">Download</a>
				&nbsp;
				<?php if(minroles()<ROLE_AGENT || glossary($Folders_ID,$glossary)=='{system:library:personal:'.sun().'}'){ ?>
				<a href="/gf5/console/resources/bais_01_exe.php?glossary=<?php echo $glossary;?>&Files_ID=<?php echo $v['ID']?>&mode=deleteRelativeFile" onClick="if(!confirm('Are you sure you want to delete this file?'))return false;" target="w2">Delete</a> 
				<?php } ?>
				</td><?php
			}else{
				?><td>
				<img src="/images/i/1148-folder1.gif" width="16" height="16" />
				<a href="intranet.php?glossary=<?php echo $glossary;?>&Folders_ID=<?php echo $v['ID']?>"><?php echo $v['Name'];?></a> 
				</td>
				<td align="right">
				<?php if($glossary=='company' && minroles()>ROLE_MANAGER){ ?>
				<!-- hide delete -->
				<?php }else{ ?>
				<a href="/gf5/console/resources/bais_01_exe.php?mode=deleteRelativeFile&Folders_ID=<?php echo $v['ID'];?>" onClick="if(!confirm('Are you sure you want to delete this folder? This will delete every folder and file inside of it.'))return false;" target="w2">Delete</a>
				<?php } ?>
				</td><?php
			}
			?></tr><?php
		}
		?></table><?php
	}else{
		?><span class="gray">(This Folder Is Currently Empty)</span>
		<br />
		<?php
	}
	?>
	<br />
	<br />
	<br />
	<br />
	<?php if(minroles()<ROLE_AGENT || glossary($Folders_ID,$glossary)=='{system:library:personal:'.sun().'}'){ ?>
	<?php echo '<form id="form1" action="/gf5/console/resources/bais_01_exe.php" method="post" target="w2">'?>
		<input type="text" name="folderName" value="(Enter Folder Name)" class="gray" onFocus="if(this.value=='(Enter Folder Name)'){this.value=''; this.className='';}" />
		<input type="hidden" name="mode" value="createRelativeFolder" />
		<input type="hidden" name="glossary" value="<?php echo $glossary;?>" />
		<input type="submit" value="Create a New Folder" />
		<input type="hidden" name="Tree_ID" value="<?php echo $Folders_ID?>" /> 
	<?php echo '</form>'?>
	<br />
	Add a file:<br />
	<?php echo '<form name="form2" id="form2" action="/gf5/console/resources/bais_01_exe.php" method="post" enctype="multipart/form-data" target="w2">'?>
		<input type="file" name="localFile"/>
		<input type="hidden" name="mode" value="uploadRelativeFile" />
		<input type="hidden" name="glossary" value="<?php echo $glossary;?>" />
		<input type="hidden" name="Tree_ID" value="<?php echo $Folders_ID?>" />
		<input type="submit" name="button" value="Upload" />
	<?php echo '</form>'?>
	<?php } ?>
	<?php
}
?>

</div>
<div id="footer">
&nbsp;&nbsp;
</div>
<?php if(!$suppressForm){ ?>
</form>
<?php }?>
<?php if(!$hideCtrlSection){ ?>
<div id="showTester" title="Javascript Tester" onClick="g('tester').style.display='block';">&nbsp;</div>
<div id="tester" >
	<a href="#" onClick="g('ctrlSection').style.display='block';return false;">Show Control Section</a><br />
	<textarea name="test" cols="65" rows="4" id="test">clear_form();</textarea><br />
	<input type="button" name="button" value="Test" onClick="jsEval(g('test').value);"><br />
	<textarea id="result" name="result" cols="65" rows="3" ></textarea>
</div>
<div id="ctrlSection" style="display:<?php echo $testModeC ? 'block':'none'?>">
	<iframe name="w1" src="/Library/js/blank.htm"></iframe>
	<iframe name="w2" src="/Library/js/blank.htm"></iframe>
	<iframe name="w3" src="/Library/js/blank.htm"></iframe>
	<iframe name="w4" src="/Library/js/blank.htm"></iframe>
</div>
<?php } ?>
</body>
</html><?php page_end();?>