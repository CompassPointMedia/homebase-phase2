<?php
$functionVersions['CSS_parser']='1.0';
function CSS_parser($str){
	/*
	2011-08-16 - first used on cpm024 Juliet to parse css decs and separate out.
	does not handle @media=screen{ .. } overwrap and would fail in this..
	*/
	global $CSS_parser;
	if(!$CSS_parser['retain_global'])$CSS_parser=array();
	for($i=0; $i<strlen($str); $i++){
		$cp=$str{$i-1};
		$c=$str{$i};
		$cn=$str{$i+1};
		if($escLevel==2){
			if($cp=='*' && $c=='/'){
				//buffer
				$buffer=preg_replace('/\*$/','',$buffer);
				$rand=str_repeat( 
					md5(rand(1,1000000)), 
					ceil(strlen($buffer)/32)
				);
				$rand=substr($rand,0,strlen($buffer));
				$escapes[]=array($start,$i,$buffer,$rand);
				//substr_replace($str,....);
				$buffer='';
				$escLevel='';
			}else{
				$buffer.=$c;
			}
		}else if($escLevel==1){
			if($c==$char){
				$rand=str_repeat( 
					md5(rand(1,1000000)), 
					ceil(strlen($buffer)/32)
				);
				$rand=substr($rand,0,strlen($buffer));
				$escapes[]=array($start,$i,$buffer,$rand);
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
			}else if($c=='\'' || $c=='"'){
				$escLevel=1;
				$char=$c;
				$start=$i;
			}
		}
	}
	$clean=$str;
	if($escapes) foreach($escapes as $v) $clean=str_replace($v[2],$v[3],$clean);

	//now I've enumerated comments and strings; replace for later surgery
	//print_r($str);

	//cubic but necessary
	if(preg_match_all('/([^\r\n{]+?)\{([^}]*?)\}/',$clean,$declarations)){
		if($escapes){
			foreach($declarations[0] as $n=>$v){
				foreach($escapes as $o=>$w){
					if(strstr($v,$w[3])){
						$declarations[0][$n]=str_replace($w[3],$w[2],$v);
					}
				}
			}
//-------------- Luke's addition to remove comments from $declarations ----------------------
			foreach($declarations[2] as $n=>$v){
				foreach($escapes as $o=>$w){
					$replace_nothing = "/*".$w[3]."*/";
					$replace_dquote = "\"".$w[3]."\"";
					$replace_squote = "'".$w[3]."'";
					if(strstr($v,$replace_nothing)){
						$declarations[2][$n] = str_replace($replace_nothing,"",$v);
					}
					if(strstr($v, $replace_dquote)){
						$declarations[2][$n] = str_replace($replace_dquote, "\"".$w[2]."\"",$v);
					}
					if(strstr($v, $replace_squote)){
						$declarations[2][$n] = str_replace($replace_squote, "'".$w[2]."'",$v);
					}
				}
			}
//-------------------------------------------------------------------------------------------
		}
//-------------------------------------More additions----------------------------------------
		foreach($declarations[2] as $n=>$v){
			if(preg_match_all('/[^;]+;/',$v, $sub_declarations)){
				$declarations[3][$n] = $sub_declarations[0];
			}
			foreach($declarations[3][$n] as $o=>$w){
				if(preg_match_all('/[^:]+:|[^;]+;/',$w, $sub2_declarations)){
					$declarations[4][$n][$o] = $sub2_declarations[0];
				}
			}
		}
		$declarations['rules'] = $declarations[0];
		$declarations['selectors'] = $declarations[1];
		$declarations['declaration_groups'] = $declarations[2];
		$declarations['declarations'] = $declarations[3];
		$declarations['property:value'] = $declarations[4];
		
		unset($declarations[0]);
		unset($declarations[1]);
		unset($declarations[2]);
		unset($declarations[3]);
		unset($declarations[4]);
//-------------------------------------------------------------------------------------------
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