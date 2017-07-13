<?php
if(!function_exists('str_replace_once')){
function str_replace_once($str_pattern, $str_replacement, $string){ 
	if (@strpos($string, $str_pattern) !== false){ 
		$occurrence = strpos($string, $str_pattern); 
		return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern)); 
	} 
	return $string; 
}}
$functionVersions['CSS_parser']='1.1';
function CSS_parser($str){
	/*
	2011-08-16 - first used on cpm024 Juliet to parse css decs and separate out.
	does not handle @media=screen{ .. } overwrap and would fail in this..
	*/
	global $CSS_parser;
	if(!$CSS_parser['retain_global'])$CSS_parser=array();
	$escLevel='';
	$cp='';
	$len=strlen($str);
	for($i=0; $i<$len; $i++){
		if($i)$cp=$str{$i-1};
		$c=$str{$i};
		if($i-2<$len){
			@$cn=$str{$i+1};
		}else{
			$cn='';
		}
		if($escLevel==2){
			if($cp=='*' && $c=='/'){
				//buffer
				/*
				//2014-04-17 SF I don't care if the overall string expands or contracts, no operation to now depends on the interrelation of $clean and coding after
				$buffer=preg_replace('/\*$/','',$buffer);
				$rand=str_repeat( 
					md5(rand(1,1000000)), 
					ceil(strlen($buffer)/32)
				);
				$rand=substr($rand,0,strlen($buffer));
				*/
				$rand=md5(rand(1,1000000).'-'.rand(1,1000000));
				$escapes[]=array($start-($char=='/**/'?1:0),$i,$buffer,$rand,'/**/');
				//substr_replace($str,....);
				$buffer='';
				$escLevel='';
			}else{
				if(!($c=='*' && $cn=='/'))$buffer.=$c;
			}
		}else if($escLevel==1){
			if($c==$char){
				/*
				$rand=str_repeat( 
					md5(rand(1,1000000)), 
					ceil(strlen($buffer)/32)
				);
					$rand=substr($rand,0,strlen($buffer));
				*/
				if(strstr($buffer,'{') || strstr($buffer,'}')){
					$rand=md5(rand(1,1000000).'-'.rand(1,1000000));
					$escapes[]=array($start-($char=='/**/'?1:0),$i,$buffer,$rand,$char);
				}
				$buffer='';
				$escLevel='';
			}else{
				$buffer.=$c;
			}
		}else{
			$buffer='';
			if($cp=='/' && $c=='*'){
				$escLevel=2;
				$start=$i;
				$char='/**/';
			}else if($c=='\'' || $c=='"'){
				$escLevel=1;
				$char=$c;
				$start=$i;
			}
		}
	}
	$clean=$str;
	if($escapes) foreach($escapes as $v) $clean=str_replace_once(
		/*($v[4]=='/** /'?'/*':$v[4]).*/$v[2]/*.($v[4]=='/** /'?'* /':$v[4])*/,
		$v[3],
		$clean
	);
	//now we are going to find media type boundaries
	if(preg_match_all('/((@media\s*[^{]+)\{)|(\}[^{]*\})/i',$clean,$a)){
		if(count($a[0])/2 != floor(count($a[0])/2)){
			$CSS_parser['error']='Unbalanced braces for media type ranges';
			return false;
		}
		foreach($a[0] as $n=>$v){
			if(fmod($n,2))continue;
			$CSS_parser['mediaTypes'][]=array(
				'type'=>$a[2][$n],
				'starting'=>$v, /* raw */
				'ending'=>$a[0][$n+1],
				'start_range'=>'', /* will be something like 735:772 for range of @media .. { */
				'end_range'=>'', /* same for closing } brackets */
				'hash'=>md5(rand(1,1000000).'-'.rand(1,1000000)),
			);
		}
		foreach($CSS_parser['mediaTypes'] as $n=>$v){
			$clean=str_replace($v['starting'],'/*--begin_media_type_range--'.$v['hash'].'--*/',$clean);
			$clean=str_replace_once(
				$v['ending'],
				'}/*--end_media_type_range--'.$v['hash'].'--*/',
				$clean
			);
		}
	}else{

	}

	//now I've enumerated comments and strings; replace for later surgery
	//print_r($str);

	//cubic but necessary
	#previous: /([^\r\n{]+?)\{[^}]*?\}/
	if(preg_match_all('/([^*\/;}]+)\s*\{([^}]*)\}/',$clean,$declarations)){
		#unset($declarations[2],$declarations[3]);
		if($escapes)
		foreach($declarations[0] as $n=>$v){
			$declarations[0][$n]=trim($v);
			$declarations[1][$n]=trim(preg_replace('/[\r\n\t ]+/',' ',$declarations[1][$n]));
			$w=trim(preg_replace('/\/\*(.|\s)*?\*\//','',$declarations[2][$n]));
			if(strlen($w)){
				if(preg_match_all('/([^:]*)\s*:\s*([^;]*)\s*;/',$w,$b)){
					unset($b[0]);
					foreach($b[1] as $o=>$w){
						$b[1][$o]=trim($w);
						$b[2][$o]=trim($b[2][$o]);
					}
					$declarations[2][$n]=$b;					
				}
			}else{
				unset($declarations[2][$n]);
			}
			/*
			foreach($escapes as $o=>$w){
				if(strstr($v,$w[3])){
					$declarations[0][$n]=str_replace($w[3],$w[2],$v);
				}
			}
			*/
		}
	}
	
	$CSS_parser['clean']=$clean;
	$CSS_parser['escapes']=$escapes;
	$CSS_parser['declarations']=$declarations;
}
/*
CSS_parser($str);

exit;
*/

?>