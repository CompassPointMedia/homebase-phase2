<?php
/*
2013-10-11: a step manager for any multi-step process - first used for importing data but can be used for installing a component, etc. etc. 

bugs
--
[18:50:58.054] ReferenceError: jQuery is not defined @ http://hmr.fantasticshop.com/gf5/console/resources/bais_01_exe.php:66

todo
	cache the raw HTML of the form so as to pop it up easily when we navigate back
	with a setting to cache the form or not, and to cache the process or not
	processes need to clean themselves up on completion
*/
$thisfile=end(explode('/',__FILE__));
$processes=array(
	'import'=>array(
		'file'=> 'comp_900_importmanager_v200.php', /* same directory/components */
		'handle'=>'import_long',
		'description'=>'long import with multi-steps, in this case 4',
		'steps'=>array(
			/* keep 1-based for simplicity */
			1=>array(
				'handle'=>'datasource',
				'description'=>'Select data to be imported',
			),
			2=>array(
				'handle'=>'importtarget',
				'description'=>'Select table(s) or location to import data into',
			),
			3=>array(
				'handle'=>'mapdata',
				'description'=>'Map import data to target table(s) or location',
			),
			4=>array(
				'handle'=>'advanced',
				'description'=>'Any advanced processes here',
			),
			5=>array(
				'handle'=>'finished',
				'complete'=>true,
				'description'=>'Data imported successfully',
			),
		),
	),
);
$ignoreFields=array(
	'process','idx','step','mode','submode','dir','suppressprintenv','button','submit','cache',
);

//for future use esp. skipping items
$stepback=-1;
$stepup=1;

if($submode=='submitData'){
	$standardProcessOverride=false;
	#here we call the component by process->idx->step - it will handle error checking and data management, and contextual skips
	require(str_replace($thisfile,$processes[$process]['file'],__FILE__));

	if(!$standardProcessOverride){
		if(!$_SESSION['special']['processes'][$idx]['process']){
			#error_alert('setting process '.$process,1);
			$_SESSION['special']['processes'][$idx]['process']=$process;
			$_SESSION['special']['processes'][$idx]['start']=date('Y-m-d H:i:s');
			$_SESSION['special']['processes'][$idx]['user']=sun();		
		}else if($process!==$_SESSION['special']['processes'][$idx]['process']){
			error_alert('process value mismatch');
		}
		if($dir<0){
			if($n=$_SESSION['special']['processes'][$idx]['steps'][$step+$dir]['cache']){
				//just load the form in stored version
				?><div id="formChangeable"><?php
				echo base64_decode($n);
				?></div>
				<script language="javascript" type="text/javascript">
				window.parent.g('formChangeable').innerHTML=document.getElementById('formChangeable').innerHTML;
				window.parent.g('cache').value='';
				</script><?php
				eOK();
			}else{
				$step+=$dir;
				goto form;
			}
		}else{
			$data=stripslashes_deep($_POST);
			foreach($data as $n=>$v)if(in_array(strtolower($n),$ignoreFields))unset($data[$n]);
			$_SESSION['special']['processes'][$idx]['steps'][$step]['data']=$data;
			$_SESSION['special']['processes'][$idx]['steps'][$step]['cache']=chunk_split(base64_encode(stripslashes($cache)));
			$step+=$dir;
			goto form;
		}
	}
	eOK();
}

//--------------------------- process and location logic --------------------------
foreach($processes as $n=>$v){
	if(!$process)$process=$n;
}
if($idx && $import=$_SESSION['special']['processes'][$idx]){
	$step=count($import['steps'])+1;
	#error_alert("step $step set",1);
//what happens when we refresh the page?
}else if(count($_SESSION['special']['processes'])==1){
	foreach($_SESSION['special']['processes'] as $idx=>$v); //OK
	if($v['completed']){
		//should we destroy that process.. here and for now we leave it alone
		$idx++;
		$step=1;
	}else{
		//we have $idx
		$step=count($_SESSION['special']['processes'][$idx]['steps'])+1;
	}
}else{
	//this actually is not perfect, puts us in the first step of the first process completed or not.. emphasizes that processes need to clean themselves up
	$idx=1;
	$step=1;
}
//------------------------- end process and location logic -------------------------


?>
<?php if(false){ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
</head>

<body>
<?php } ?>

<?php
form:
if(!$refreshComponentOnly){
	?><script language="javascript" type="text/javascript">
	//http://stackoverflow.com/questions/1388893/jquery-html-in-firefox-uses-innerhtml-ignores-dom-changes
	(function($) {
	  var oldHTML = $.fn.html;
	
	  $.fn.formhtml = function() {
		if (arguments.length) return oldHTML.apply(this,arguments);
		$("input,button", this).each(function() {
		  this.setAttribute('value',this.value);
		});
		$("textarea", this).each(function() {
		  // updated - thanks Raja & Dr. Fred!
		  $(this).text(this.value);
		});
		$("input:radio,input:checkbox", this).each(function() {
		  // im not really even sure you need to do this for "checked"
		  // but what the heck, better safe than sorry
		  if (this.checked) this.setAttribute('checked', 'checked');
		  else this.removeAttribute('checked');
		});
		$("option", this).each(function() {
		  // also not sure, but, better safe...
		  if (this.selected) this.setAttribute('selected', 'selected');
		  else this.removeAttribute('selected');
		});
		return oldHTML.apply(this);
	  };
	
	  //optional to override real .html() if you want
	  // $.fn.html = $.fn.formhtml;
	})(jQuery);
	function processMgr(i){
		g('cache').value=$('#formChangeable').formhtml(); //see if it works
		g('dir').value=i;
	}
	</script><?php
}
?>
<form name="form1" id="form1" target="w2" action="/resources/bais_01_exe.php">
<span id="formChangeable">
<span id="formData">
<?php
$display='form';
require(str_replace($thisfile,$processes[$process]['file'],__FILE__));
?>
</span>
<span id="formControls">
	<input type="hidden" name="mode" id="mode" value="importProcess" />
	<input type="hidden" name="submode" id="submode" value="submitData" />
	<input type="hidden" name="process" id="process" value="<?php echo $process;?>" />
	<input type="hidden" name="idx" id="idx" value="<?php echo $idx;?>" />
	<input type="hidden" name="step" id="step" value="<?php echo $step;?>" />
	<input type="hidden" name="dir" id="dir" />
	<input type="hidden" name="suppressPrintEnv" id="suppressPrintEnv" value="<?php echo $suppressPrintEnv;?>" />
	<br />
	<input type="submit" name="Submit" value="&lt; Back" onclick="processMgr(<?php echo $stepback;?>);" <?php if($step==1)echo 'disabled';?> />
	&nbsp;&nbsp;&nbsp;
	<input type="submit" name="Submit" value="Next &gt;" onclick="processMgr(<?php echo $stepup;?>);" <?php if($step==count($processes[$process]['steps']))echo 'disabled';?> />
	&nbsp;&nbsp;&nbsp;
	<input type="button" name="Button" value="Close" onclick="focus_nav_cxl('insert');" />
</span>
</span>
<input type="hidden" name="cache" id="cache" value="" />
</form>

<?php
if($submode=='submitData'){
	?><script language="javascript" type="text/javascript">
	window.parent.g('formChangeable').innerHTML=document.getElementById('formChangeable').innerHTML;
	window.parent.g('cache').value='';
	</script><?php
	eOK();
}
?>

<?php if(false){ ?>
</body>
</html>
<?php } ?>