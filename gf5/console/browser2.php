<?php
//$_d='-d "email=sam-git@samuelfullman.com&password=secret"';
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='generic';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');
require_once($FUNCTION_ROOT.'/function_enhanced_parse_url_v101.php');

function s($n){return stripslashes($n);}

if(!$agent)$agent='User-Agent: Mozilla/5.0 (Windows NT 6.0; rv:15.0) Gecko/20100101 Firefox/15.0.1';

if(strlen($url) && !preg_match('/^([a-z]+\.)*pinterest\.com/i',$url))exit('thank you for helping me out from PHP builder.  ONLY queries to pinterest are allowed for this browser emulator.  If you would like the source code for this page, let me know and I\'d be happy to share it with you!<br><br>click back in your browser to return.');

?>
<!-- minimal code/style intrusion -->
<style type="text/css">
.gray{
	color:#aaa;
	}
.fr{
	float:right;
	margin:0px 0px 5px 15px;
	padding:0px 0px 5px 5px;
	}
</style>
<script language="JavaScript" type="text/javascript" src="/Library/js/global_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/common_04_i1.js"></script>
<script language="JavaScript" type="text/javascript" src="/Library/js/forms_04_i1.js"></script>

<h2>Browser Emulator</h2>
<?php
if($showpost){
	?><h3>Data String</h3><?php
	foreach($_POST as $n=>$v)echo $n . '='.s($v).'<br />';
	?><h3>Raw Array</h3><?php
	prn($_POST,1);
}
?>
<form method="get">
URL: 
  <select name="protocol" id="protocol">
    <option value="http://" <?php echo s($protocol)=='http://'?'selected':''?>>http://</option>
    <option value="https://" <?php echo s($protocol)=='https://'?'selected':''?>>https://</option>
    <option value="ftp://" <?php echo s($protocol)=='ftp://'?'selected':''?>>ftp://</option>
  </select>
  <input name="url" type="text" id="url" value="<?php echo h(s($url));?>" size="100">
  <br>
  <span class="gray">(include query string OK)</span>  <br>
  <br>
  <div class="fr">
  On output replace string:<br>
  <input tabindex="-1" name="replacefrom" type="text" id="replacefrom" value="<?php echo h(s($replacefrom));?>" />
  <br>
	With string:
	<br>
	<input tabindex="-1" name="replaceto" type="text" id="replaceto" value="<?php echo h(s($replaceto));?>" />
	<br>
	<label><input tabindex="-1" name="useregex" type="checkbox" id="useregex" value="1" <?php echo $useregex?'checked':''?> />
	Use regex</label>
    <br>
<br>
  </div>
data: <span class="gray">(one data pair per line)</span> <br>
<textarea name="data" cols="50" rows="5" id="data"><?php echo h(s($data));?></textarea>
<br>
<br>

<input type="submit" name="submit" value="Submit" />
<input type="button" name="button" value="Clear" onClick="window.location='browser.php';" />
<input type="hidden" name="post" value="1" />
</form>
<?php
if($post){
	?><h3>Results</h3><?php
	//url
	$url=$protocol.$url;
	enhanced_parse_url(s($url));
	$u=($enhanced_parse_url);
	//data
	if($data=trim($data)){
		$data=preg_split('/[\r\n]+/',$data);
		$str='';
		foreach($data as $v){
			$v=explode('=',$v);
			$str.=$v[0].'='.s($v[1]).'&';
		}
		$str=rtrim($str,'&');
		$data='-d "'.$str.'"';
	}
	//token storage - here we may want to use it, or not send but receive, or (maybe) not receive but send..
	$tokens='cookies/c_'.str_replace('www.','',strtolower($u['domain'])).'.txt';
	if(!file_exists($tokens))fclose($fp=fopen($tokens,'w'));
	if($agent)$agent='-A "'.$agent.'"';
	$out=`curl $agent -i -b $tokens -c $tokens $data $url`;
	?><h3>Query to site</h3><?php
	prn("curl -i -b $tokens -c $tokens $data $url");
	echo '<br>';
	$out=explode("\n",trim($out));
	foreach($out as $n=>$v){
		if($n==0){
			$status=trim($v);
			unset($out[0]);
			continue;
		}
		if(!trim($v)){
			$out=trim(implode("\n",$out));
			break;
		}
		$line=explode(':',$v);
		$key[]=$line[0];
		unset($line[0]);
		$value[]=implode(':',$line);
		unset($out[$n]);
	}
	echo '<h3>Status: '.$status.'</h3><br>';
	if($key)foreach($key as $n=>$v){
		if($n==0)echo '<h3>Headers</h3><br>';
		echo $v . ': '.$value[$n];
		if(strtolower($v)=='location'){
			preg_match('/^([a-z]+:\/\/)/i',trim($value[$n]),$m);
			$passProtocol=$m[1];
			$passURL=str_replace($m[1],'',trim($value[$n]));
			?>[<a href="browser.php?<?php
			$i=0;
			foreach($_REQUEST as $o=>$w){
				if(!strlen($w) || $o=='submit')continue;
				$i++;
				if($i>1)echo '&';
				echo $o.'='.urlencode($o=='url' ? $passURL : ($o=='protocol' ? $passProtocol : $w));
			}
			?>" title="go to this redirect location">Go here</a>]<?php
		}
		echo '<br>';
	}
	if(file_exists($tokens)){
		$a=file($tokens);
		foreach($a as $n=>$v){
			if(substr($v,0,1)=='#' || !trim($v))unset($a[$n]);
		}
		if(!empty($a)){
			?><h3>Cookies</h3><?php
			foreach($a as $v){
				$v=explode("\t",$v);
				echo '<span title="'.implode("\t",$v).'">'.$v[count($v)-2].': <strong>'.$v[count($v)-1].'</strong></span><br>';
			}
		}
	}	
	ob_start(); //--- begin tabs ---
	if(trim($replacefrom) && trim($replaceto)){
		$replacefrom=s($replacefrom);
		$replaceto=s($replaceto);
		$fn=($useregex?'preg_replace':'str_replace');
		if($useregex)$replacefrom='/'.$replacefrom.'/i';
		$out=$fn($replacefrom,$replaceto,$out);
	}
	echo $out;
	get_contents_tabsection('HTML');

	?><textarea cols="200" rows="75"><?php echo h($out);?></textarea><?php

	get_contents_tabsection('Code');
	tabs_enhanced(array(
		'HTML'=>array(
			'label'=>'HTML'
		),
		'Code'=>array(
			'label'=>'Code'
		),
	));
	
}
?>
