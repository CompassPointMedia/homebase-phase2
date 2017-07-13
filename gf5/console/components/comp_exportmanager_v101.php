<?php
if(!function_exists('get_image')) require($_SERVER['DOCUMENT_ROOT'] . '/functions/function_get_image_v220.php');
$bImages=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName, array('positiveFilters'=>'\.(jpg|gif|png|svg)$',));
$cImages=get_file_assets($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/master', array('positiveFilters'=>'\.(jpg|gif|png|svg)$',));

$_fl=__FILE__;
$_fl = explode('/',$_fl);
$_fl = end($_fl);

$restrictedFields=preg_split('/[ ,\t\n\r]+/',strtolower(trim(trim(q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='admin' AND varnode='' AND varkey='restrictedFields'", O_VALUE)),',')));

function process_views($present,$Merchant){
	/* 2013-11-29 */
	global $exportTypes, $Merchant, $tables, $list, $listFrom, $listRaw, $hasField, $hasFieldName;
	$t=q("SHOW TABLES", O_ARRAY);
	$tables=array();
	foreach($t as $n=>$v){
		foreach($v as $w)break;
		$tables[$w]=$w;
	}
	if($present && !is_array($present)){
		$present=explode(',',$present);
	}
	
	if(!is_array($Merchant))$Merchant=preg_split('/,\s+/',$Merchant);
	$exports=array();
	foreach($Merchant as $m){
		$add=$exportTypes[strtolower($m)];
		foreach($add as $n=>$v){
			if(isset($present) && !in_array($add[$n]['view'],$present)){
				unset($add[$n]);
			}else{
				$add[$n]['merchant']=$m;
			}
		}
		if(count($add))$exports=array_merge($exports,$add);
	}
	//break apart by unions
	$ct=count($exports);
	foreach($exports as $n=>$v){
		if($n==$ct)break; //prevent new items added on
		$create=q("SHOW CREATE VIEW ".$v['view'], O_ROW);
		$create=$create['Create View'];
		$exports[$n]['create']=$create;
		
		if(stristr($create,'union select')){
			$hasUnion=true;
			$a=preg_split('/(\s+UNION SELECT\s+)|(`'.$v['view'].'` AS select\s+)/i',$create);
			unset($a[0]);
			foreach($a as $w){
				$w_=$w;
				$w=preg_split('/\s+FROM\s+(`|\()/i',$w);
				$w[1]=(preg_match('/\s+FROM\s+`/i',$w_)?'`':'(').$w[1];

				preg_match_all('/ AS `([^`]+)`,*/',$w[0],$m);
				unset($list);
				foreach($m[0] as $x){
					$key=end(explode('`',rtrim($x,',`')));
					$str=substr($w[0],0,strpos($w[0],$x));
					//make case insensitive
					$hasFieldName[strtolower($key)]=$key;
					$hasField[strtolower($key)][$w[1]]=str_replace('_utf8','',$str);
					$list[strtolower($key)]=str_replace('_utf8','',$str);
					$w[0]=substr($w[0],strpos($w[0],$x)+strlen($x),100000);
				}
				$exports[$n]['list'][]=$list;
				$exports[$n]['list_from'][]=$w[1];
				
			}
		}else{
			$w=preg_split('/`'.$v['view'].'` AS\s+/i',$create);
			$w_=$w[1];
			$w=preg_split('/\s+FROM\s+(`|\()/i',$w[1]);
			$listRaw=preg_replace('/^\s*SELECT\s+/i','',$w[0]).'`';
			$listFrom=(preg_match('/\s+FROM\s+`/i',$w_)?'`':'(').$w[1];
			
			preg_match_all('/ AS `([^`]+)`,*/',$listRaw,$m);
			unset($list);
			foreach($m[0] as $w){
				$key=end(explode('`',rtrim($w,',`')));
				$str=substr($listRaw,0,strpos($listRaw,$w));
				$hasFieldName[strtolower($key)]=$key;
				$hasField[strtolower($key)][$v['view']]=$str;
				$list[strtolower($key)]=str_replace('_utf8','',$str);
				$listRaw=substr($listRaw,strpos($listRaw,$w)+strlen($w),100000);
			}
			$exports[$n]['list'][0]=$list;
			$exports[$n]['list_from'][0]=$listFrom;
			$exports[$n]['create_view']=$create;
		}
	}
	return $exports;
}
function update_view($view,$collection,$options=array()){
	global $NewField, $qr,$fl,$ln, $developerEmail, $MASTER_USERNAME;
	extract($options);
	if(!$view)error_alert('Abnormal error, no view selected to update');
	ob_start();
	$a=q("SHOW CREATE VIEW $view", O_ROW, ERR_ECHO);
	$err=ob_get_contents();
	ob_end_clean();
	if($err)error_alert('Abnormal error in reading view; cannot update');
	$str=$a['Create View'];
	if(preg_match('/\s+UNION\s+/i',$str))error_alert('View with the "UNION" statement cannot be updated this way');
	$a=preg_split('/\sFROM\s+\(/i',$str);
	if(count($a)<2)error_alert('Unable to locate FROM statement');
	$from='('.$a[count($a)-1];
	$sql='CREATE OR REPLACE VIEW '.$view.' AS SELECT ';
	unset($fields);
	foreach($collection as $n=>$v){
		$fields[strtolower($n)]=$n;
	}
	foreach($collection as $n=>$v){
		if(!trim($v))continue;
		if($n=='_new_field_'){
			if(!strlen($NewField))error_alert('Enter a name for the new column');
			$NewField=str_replace('`','',stripslashes($NewField));
			if($fields[strtolower($NewField)])error_alert('The new column you want to add is already present, either for this view or others in this comparison; look at the list above');
			$sql.="\n".stripslashes($v).' AS `'.$NewField.'`,';
		}else{
			$sql.="\n".stripslashes($v).' AS `'.$n.'`,';
		}
	}
	$sql=rtrim($sql,',');
	$sql.="\nFROM ".$from;
	prn($sql);
	ob_start();
	q($sql, ERR_ECHO);
	$err=ob_get_contents();
	ob_end_clean();
	if($err){
		prn($err);
		error_alert('Error updating view, take a look in the ctrlSection for details');
	}
	//mail me the view
	mail($developerEmail, 'backup view build from '.$MASTER_USERNAME.':'.$GLOBALS['_fl'].', line '.__LINE__,get_globals($notice='structure of view '.$view." just prior to update:\n\n".$str."\n\n"),'From: backups@'.$_SERVER['HTTP_HOST']);
	if(!$suppress_alerts)error_alert('View '.$view.' successfully updated',1);
}

/*
routine to trim fields
if(sun()=='sam'){
	unset($records,$fields);
	$a=q("SELECT ID, Name, LongDescription, MetaTitle, Featured, Caption, Description, ManufacturerSKU FROM finan_items", O_ARRAY_ASSOC);
	foreach($a as $ID=>$v){
		$sql='update finan_items SET EditDate=EditDate, ';
		foreach($v as $o=>$w){
			if(strlen(trim($w))!==strlen($w)){
				$w=preg_replace('/\n +/',"\n",$w);
				$fields[$o]=$o;
				$records[$ID]=$ID;
				$sql.=$o.'=\''.addslashes(trim($w)).'\', ';
			}
		}
		if(!$records[$ID]){
			unset($sql);
			continue;
		}
		$sql=rtrim($sql,', ')." WHERE ID=$ID";
		prn("\n----\n");
		prn($sql);
		q($sql);
	}
	
  
	prn($fields);
	exit;
}
*/
//get split size
if($zipSplitSize=q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='items' AND varnode='settings' AND varkey='zipSplitSize'", O_VALUE)){
	//OK
}else{
	$zipSplitSize=30;
}

function showBatches($batches,$options=array()){
	/* created 2012-07-06
	disposition= 1:pre; 2:post (link to export)
	*/
	global $exportTypes,$Merchant,$qr,$developerEmail,$fromHdrBugs,$MASTER_USERNAME,$GCUserName,$bImages,$zipSplitSize;
	
	extract($options);
	if(!$show)$show='partial';
	$rc=0;
	if(count($batches))
	foreach($batches as $Batches_ID){
		$rc++;
		//we always do this to handle deletes
		q("DELETE e.* FROM gen_batches_entries e LEFT JOIN finan_items i ON e.Objects_ID=i.ID WHERE e.Batches_ID=$Batches_ID AND i.ID IS NULL");
		if($qr['affected_rows']){
			mail($developerEmail, 'Notice in '.$MASTER_USERNAME.':'.$GLOBALS['_fl'].', line '.__LINE__,get_globals($err='batches_entries pared down'),$fromHdrBugs);
		}
		
		if(!($data=q("SELECT SubType AS Merchant, Notes, StartTime, StopTime, COUNT(*) AS Records, Creator FROM gen_batches b, gen_batches_entries e WHERE b.ID=$Batches_ID AND b.ID=e.Batches_ID GROUP BY b.ID", O_ROW)))continue;
		extract($data);
		$exports=$exportTypes[strtolower($Merchant)];
		?>
		<div id="b_<?php echo $Batches_ID;?>" class="batch">
		<h3><?php echo $Merchant;?></h3>
		<?php
		if($disposition=='allExports'){
			?><p class="gray"><?php echo $Records;?> records, exported on <?php echo date('n/j/Y \a\t g:iA',strtotime($StartTime));?> - by <?php echo $Creator?></p><?php
		}
		if(trim(preg_replace('/^Added by exe page line [0-9]+$/','',$Notes))){
			echo $Notes;
		}
		?><span id="batch_<?php echo $Batches_ID;?>"><?php
		if($disposition=='allExports'){
			?>
            <div class="fl">
                [<a href="/gf5/console/resources/bais_01_exe.php?mode=exportManager&submode=deleteBatch&Batches_ID=<?php echo $Batches_ID?>" target="w2" onclick="if(!confirm('This will delete this batch, however it will NOT re-flag the records for export.  If you want to re-export the records you would need to do it manually. Continue?'))return false;">Delete&nbsp;batch..</a>]<br />
                [<a href="/gf5/console/resources/bais_01_exe.php?mode=exportManager&submode=deleteBatch&Batches_ID=<?php echo $Batches_ID?>&reflag=1" target="w2" onclick="if(!confirm('This will delete this batch and re-flag the records as needing to be exported to this merchant.  Continue?'))return false;">Delete and Re-flag for Export..</a>]<br />
                [<a href="report_generic.php?report=itemsquery&searchtype=batch&q=<?php echo $Batches_ID;?>" onclick="return ow(this.href,'l1_reportexport','825,700');" title="view records contained in this batch">View record list..</a>]<br />
                [<a href="products.php?searchtype=batch&q=<?php echo $Batches_ID;?>" title="view and edit all records as a search group" onclick="return ow(this.href,'l1_items','815,700');">Edit records..</a>]<br />
                [<a href="report_outletstatus.php?searchType=batch&Batches_ID=<?php echo $Batches_ID;?>" title="Scan this batch at Historic Maps Restored site" onclick="if(!confirm('This will scan your primary sales site for the presence of these records.  If you have not imported this batch yet, nothing will show.  Continue?'))return false; return ow(this.href,'l1_outlets','800,700');">Scan this batch</a>]

            </div>
            <?php
		}
		?>
		<div class="fl">
		<ul>
		<?php
		$comparison='';
		foreach($exports as $n=>$v){
			$exportFileName=date('Ymd_His',strtotime($StartTime));
			$exportFileName='_'.$exportFileName.'_['.$Records.']';
			$exportFileName=$Merchant.'_'.str_replace('.',$exportFileName.'.',$v['filename']);
			
			$comparison.=(strlen($comparison)?',':'').$v['view'];
			?><li><?php
			if($disposition!='preExport'){
				?><a href="/gf5/console/resources/bais_01_exe.php?mode=exportManager&submode=download&suppressPrintEnv=1&Batches_ID=<?php echo $Batches_ID?>&fileName=<?php echo $v['filename'];?>&exportFileName=<?php echo urlencode($exportFileName);?>" title="Download this file"><?php
			}
			echo $disposition=='preExport'?$exportFileName:$v['filename'];
			if($disposition!='preExport'){
				?></a><?php
			}
			?></li><?php
		}
		if($disposition!='preExport'){
			if($records=q("SELECT i.ID, i.SKU FROM finan_items i, gen_batches_entries e WHERE i.ID=e.Objects_ID AND e.ObjectName='finan_items' AND e.Batches_ID=$Batches_ID", O_ARRAY)){
				foreach($records as $n=>$v){
					#2013-01-24: not working, taking too long and freezing the system
					#if(!($g=get_image($SKU,$bImages)))unset($records[$n]);
				}
			}
			if($records){
				?><li style="margin-top:20px;"><?php
				if(count($records)<=$zipSplitSize){
					//Image Zip File
					?><a href="/gf5/console/resources/bais_01_exe.php?mode=exportManager&submode=downloadImages&suppressPrintEnv=1&Batches_ID=<?php echo $Batches_ID?>&fileName=<?php echo $v['filename'];?>&exportFileName=<?php echo urlencode($exportFileName);?>" title="Download images associated with this batch">Image Zip File (<?php echo count($records);?>)..</a><?php
				}else{
					?>Multiple zip files..<br /><?php
					$i=0;
					$fs=0;
					while(true){
						$fs++;
						$i+=$zipSplitSize;
						//[1-15]
						$min=$i-$zipSplitSize+1;
						$max=min($i,count($records));
						?><a href="/gf5/console/resources/bais_01_exe.php?mode=exportManager&submode=downloadImages&suppressPrintEnv=1&Batches_ID=<?php echo $Batches_ID?>&fileName=<?php echo $v['filename'];?>&exportFileName=<?php echo urlencode($exportFileName);?>&range=<?php echo $min.','.$max;?>" title="Download images associated with this batch">[<?php echo $min;?>-<?php echo $max;?>]</a><br /><?php
						if($i>count($records))break;
						if($fs>5)break;
					}
				}
				?></li><?php
			}else{
				?><li class="gray" style="margin-top:20px;"><em>(no images available to zip)</em></li><?php
			}
		}		
		?>
		</ul>
		</div>
		<div class="fl">
		<a href="export_manager.php?submode=compareViews&Merchant=<?php echo $Merchant;?>&present=<?php echo $comparison;?>&Batches_ID=<?php echo $Batches_ID?>" title="compare export views for this merchant" onclick="return ow(this.href,'l1_compare','700,700');">compare..</a><br />
	
		</div>
		<div class="cb"> </div>
		</span>
		</div><?php
		if($show=='partial' && $rc>50){
			?><div class="showMore">
			[<a href="export_manager.php?disposition=allExports&show=all">see older batches..</a>]
			</div><?php
			break;
		}
	}
}
function enhanced_nl2br($n){
	$n=trim($n);
	$init=strlen($n);
	$n=str_replace("\t",' ',$n);
	$rand=md5(rand(1,1000000).time());
	$n=str_replace("\r\n",$rand,$n);
	$n=str_replace("\n\r",$rand,$n);
	$n=str_replace($rand,"\n",$n);
	$n=str_replace("\n",'<br />',$n);
	$end=strlen($n);
	return $n;
}
$merchants=array(
	
);
for($__i__=1; $__i__<=1; $__i__++){ //------------ break loop ----------------
if($submode=='batchPrepare'){
	if(!count($export))error_alert('Select at least one merchant to export to');
	foreach($export as $merchant=>$null){
		if($a=q("SELECT ID FROM finan_items WHERE ".$merchant."_ToBeExported >0 ".($_SESSION['special']['currentExport'][$merchant]?' AND ID IN('.implode(',',$_SESSION['special']['currentExport'][$merchant]).')':'')." AND ResourceType IS NOT NULL", O_COL)){
			$haveExport=true;
			//we get them all with no selection ability
			q("UPDATE finan_items SET ".$merchant."_ToBeExported=0 WHERE ".$merchant."_ToBeExported>0 ".($_SESSION['special']['currentExport'][$merchant]?' AND ID IN('.implode(',',$_SESSION['special']['currentExport'][$merchant]).')':''));
			$i=0;
			foreach($a as $ID){
				$i++;
				if($i==1){
					$Batches_ID=q("INSERT INTO gen_batches SET
					Description='Batch export to $merchant',
					StartTime=NOW(),
					Type='Export',
					SubType='$merchant',
					Status='Pending',
					Quantity='".count($a)."',
					Notes='Added by exe page line ".__LINE__."',
					CreateDate=NOW(),
					Creator='".sun()."'", O_INSERTID);
				}
				q("INSERT INTO gen_batches_entries SET Batches_ID=$Batches_ID, Objects_ID=$ID");
			}
			q("UPDATE gen_batches SET StopTime=NOW() WHERE ID=$Batches_ID");
			$batches[]=$Batches_ID;
		}
	}
	if(!$haveExport){
		mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.$GLOBALS['_fl'].', line '.__LINE__,get_globals($err='No records were found to export; either a batch has just been created for these records or there was a system error; an administrator has been notified'),$fromHdrBugs);
		error_alert($err);
	}
	?><script language="javascript" type="text/javascript">
	window.parent.ow('/gf5/console/export_manager.php?disposition=preExport&batches=<?php echo implode(',',$batches);?>','l1_export','950,600');
	var l=window.parent.location;
	window.parent.location=l+'';
	</script><?php
	break;
}else if($submode=='cancelBatch'){
	//reverse each batch
	$batches =explode(',',$batches);
	foreach($batches as $Batches_ID){
		$merchant=q("SELECT SubType FROM gen_batches WHERE ID=$Batches_ID",O_VALUE);
		q("DELETE FROM gen_batches WHERE ID=$Batches_ID");
		q("UPDATE finan_items i, gen_batches_entries e SET ".$merchant."_ToBeExported=1 WHERE i.ID=e.Objects_ID AND e.Batches_ID=$Batches_ID");
		q("DELETE FROM gen_batches_entries WHERE Batches_ID=$Batches_ID");
	}
	?><script language="javascript" type="text/javascript">
	window.parent.close();
	</script><?php
	break;
}else if($submode=='commitBatch'){
	//easy step
	q("UPDATE gen_batches SET Status='Committed' WHERE ID IN($batches)");
	?><script language="javascript" type="text/javascript">
	window.parent.location='/gf5/console/export_manager.php?disposition=readyExport&batches=<?php echo $batches;?>';
	</script><?php
	//we DO NOT BREAK here because we have an HTML output to do
}else if($submode=='downloadImages'){
	//functions first
	if(!function_exists('get_image'))require($FUNCTION_ROOT.'/function_get_image_v220.php');
	if(!function_exists('create_thumbnail'))require($FUNCTION_ROOT.'/function_create_thumbnail_v201.php');
	if(!class_exists('zipFile'))require($FUNCTION_ROOT.'/class_zipfile_v100.php');
	function memory_usage($msg){
		global $z,$z2,$memThreshold,$notified2,$MASTER_USERNAME,$developerEmail,$memThreshold,$iniMem,$fromHdrBugs,$added,$memmax,$debug;
		$z2=memory_get_usage();
		if($z2>$memThreshold && !$notified2){
			$notified2=true;
			mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.$GLOBALS['_fl'].', line '.__LINE__,get_globals($warn='The zipfile system has approached '.($memThreshold*100).'% of maximum usable memory ('.$iniMem.').  If the zipfile is not successfully created, please contact an administrator.'),$fromHdrBugs);
		}
		$added=$z2-$z; $memmax=max($memmax,$z2);
		$z=$z2;
		gmicrotime($y=(round($z/1024/1024,4).'M - '.$msg.' with '.round($added/1024/1024,4).'M'));
		if($debug==17)prn($y);
	}
	set_time_limit(60*15);

	if($maxFileSizeToZip=q("SELECT varvalue FROM bais_settings WHERE username='system' AND vargroup='items' AND varnode='settings' AND varkey='maxFileSizeToZip'", O_VALUE)){
		//ok;
	}else{
		$maxFileSizeToZip=5; //in megabytes
	}
	$maxFileSizeToZip*=(1024*1024);
	$memmax=0;
	$filelengthmax=0;
	$iniMem=ini_get('memory_limit');
	$memmaxSystem=preg_replace('/[^0-9]*/','',$iniMem)*1024*1024;
	$memThreshold=.5 * $memmaxSystem;

	$get_imageReturnMethod='array';
	$str='';

	memory_usage('before zip');
	$zipfile= new zipfile();
	memory_usage('after zip');
	
	if($range)$range=explode(',',$range);
	
	if($a=q("SELECT i.SKU, i.ID, i.ThumbData FROM finan_items i, gen_batches_entries b WHERE i.ID=b.Objects_ID AND b.Batches_ID=$Batches_ID", O_ARRAY_ASSOC)){
        /**
         * NOTE: 2016-12-21 SF - Ric wanted the images to be 2000x2000 with no "a" image.  That code was pretty cool and is below here.
         */
        $t=1;
        foreach($a as $n=>$v)$a[$n]['idx']=($t++);
        $t=0;
        memory_usage('after query');
        foreach($a as $SKU=>$v){
            $t++;

            if(isset($range)){
                if($t<$range[0] || $t>$range[1])continue;
            }

            //main image, get _a first, then just .jpg
            if(($g=$bImages[strtolower($SKU).'_a.jpg']) || ($g=$bImages[strtolower($SKU).'.jpg'])){
                $unreadable=false;
                $oversize=false;
                if(!$g['width'] || !$g['height']){
                    $str.='WARNING! On preprocessing, file '.$g['name'].' appears to be unreadable'."\n";
                    $unreadable=true;
                }
                if(!$a_folder && !$unreadable){
                    $a_folder=true;
                    $zipfile->add_dir('a/');
                    memory_usage('after `a` directory ('.$t.')');
                }
                if(!$unreadable){
                    ob_start();
                    @readfile($_SERVER['DOCUMENT_ROOT'].'/images/documentation/'.$GCUserName.'/'.$g['name']);
                    $filedata=ob_get_contents();
                    $filelength=strlen($filedata);
                    ob_end_clean();


                    if($filelength> $maxFileSizeToZip){
                        $oversize=true;
                        $oversizeFile[]=$SKU.' (file size: '.round($filelength/1024/1024,3).'Mb)';
                        if(!$notified){
                            //$notified=true;
                            mail(sun('e').','.$developerEmail,'oversize file omitted from zip','The following file was over the size limit to put in a zip file which is '.round($maxFileSizeToZip/1024/1024,2).'MB.  You can increase this limit by going to Home > Preferences and updating the value for Max file size to add to zip.'."\n\n".'NOTE: there may be other oversized files besides this one'."\n\n".$oversizeFile[count($oversizeFile)-1]."\n".$g['name'],$fromHdrBugs);
                        }
                    }else{
                        $filelengthmax=max($filelengthmax,round($filelength,0));
                        $avg++;
                        $avgx=( ($avgx*($avg-1) + $filelength)/$avg );
                        $zipfile->add_file($filedata,'a/'.$SKU.'_a.jpg');
                        memory_usage('after `a` image ('.$t.')');
                        $mainadded.="\n".$SKU.' - '.$g['name'].', '.number_format($filelength,0).'b';
                        $a_added++;
                    }
                }
            }else{
                //not sure what to do with this
                $mainmissing.="\n".$SKU;
            }
        }


        /** old code.. */
        if(false) {
            $t = 1;
            foreach ($a as $n => $v) $a[$n]['idx'] = ($t++);
            $t = 0;
            memory_usage('after query');
            foreach ($a as $SKU => $v) {
                $t++;

                if (isset($range)) {
                    if ($t < $range[0] || $t > $range[1]) continue;
                }

                //main image, get _a first, then just .jpg
                if (($g = $bImages[strtolower($SKU) . '_a.jpg']) || ($g = $bImages[strtolower($SKU) . '.jpg'])) {
                    $unreadable = false;
                    $oversize = false;
                    if (!$g['width'] || !$g['height']) {
                        $str .= 'WARNING! On preprocessing, file ' . $g['name'] . ' appears to be unreadable' . "\n";
                        $unreadable = true;
                    }
                    if (!$a_folder && !$unreadable) {
                        $a_folder = true;
                        $zipfile->add_dir('a/');
                        memory_usage('after `a` directory (' . $t . ')');
                    }
                    if (!$unreadable) {
                        ob_start();
                        @readfile($_SERVER['DOCUMENT_ROOT'] . '/images/documentation/' . $GCUserName . '/' . $g['name']);
                        $filedata = ob_get_contents();
                        $filelength = strlen($filedata);
                        ob_end_clean();


                        if ($filelength > $maxFileSizeToZip) {
                            $oversize = true;
                            $oversizeFile[] = $SKU . ' (file size: ' . round($filelength / 1024 / 1024, 3) . 'Mb)';
                            if (!$notified) {
                                //$notified=true;
                                mail(sun('e') . ',' . $developerEmail, 'oversize file omitted from zip', 'The following file was over the size limit to put in a zip file which is ' . round($maxFileSizeToZip / 1024 / 1024, 2) . 'MB.  You can increase this limit by going to Home > Preferences and updating the value for Max file size to add to zip.' . "\n\n" . 'NOTE: there may be other oversized files besides this one' . "\n\n" . $oversizeFile[count($oversizeFile) - 1] . "\n" . $g['name'], $fromHdrBugs);
                            }
                        } else {
                            $filelengthmax = max($filelengthmax, round($filelength, 0));
                            $avg++;
                            $avgx = (($avgx * ($avg - 1) + $filelength) / $avg);
                            $zipfile->add_file($filedata, 'a/' . $SKU . '_a.jpg');
                            memory_usage('after `a` image (' . $t . ')');
                            $mainadded .= "\n" . $SKU . ' - ' . $g['name'] . ', ' . number_format($filelength, 0) . 'b';
                            $a_added++;
                        }
                    }
                } else {
                    //not sure what to do with this
                    $mainmissing .= "\n" . $SKU;
                }
                if (false && $g = $bImages[strtolower($SKU) . '_b.jpg']) {
                    if (!$b_folder) {
                        $b_folder = true;
                        $zipfile->add_dir('b/');
                    }
                    ob_start();
                    @readfile($_SERVER['DOCUMENT_ROOT'] . '/images/documentation/' . $GCUserName . '/' . $g['name']);
                    $filedata = ob_get_contents();
                    $filelength = strlen($filedata);
                    ob_end_clean();
                    $zipfile->add_file($filedata, 'b/' . $SKU . '_b.jpg');
                    $badded .= "\n" . $SKU . ' - ' . $g['name'] . ', ' . number_format($filelength, 0) . 'b';
                } else {
                    //from "a" image above
                    if ($oversize) continue;
                    unset($ThumbData, $imgMag, $imgLeft, $imgTop, $thumbLeft, $thumbTop, $thumbWidth, $thumbHeight, $path);
                    if (strlen($v['ThumbData']) > 7) {
                        $ThumbData = unserialize(base64_decode($v['ThumbData']));
                        @extract($ThumbData);
                    }
                    if (!$imgMag) $imgMag = 1.00;
                    if (!$imgLeft) $imgLeft = 0;
                    if (!$imgTop) $imgTop = 0;
                    if (!$thumbLeft) $thumbLeft = 0;
                    if (!$thumbTop) $thumbTop = 0;
                    if (false && $g = get_image($SKU, $cImages)) {
                        if (!($g_sub = get_image($SKU, $bImages))) {
                            mail($developerEmail, 'Error in ' . $MASTER_USERNAME . ':' . end(explode('/', __FILE__)) . ', line ' . __LINE__, get_globals($notic = 'there is a SKU image in the master but not in the root of ' . $GCUserName . '. We need to create it on the fly but it should be there'), $fromHdrBugs);
                            continue;
                        }
                        $path = '/images/documentation/' . $GCUserName . '/master/' . $g['name'];
                        //blow things up
                        $imgMag *= ($g_sub['width'] / $g['width']);
                        $imgLeft = round($imgLeft * $g_sub['width'] / $g['width'], 0);
                        $imgTop = round($imgTop * $g_sub['width'] / $g['width'], 0);
                        if (!$thumbWidth) $thumbWidth = round($g['width'] * $imgMag, 0) - $thumbLeft;
                        if (!$thumbHeight) $thumbHeight = round($g['height'] * $imgMag, 0) - $thumbTop;
                    } else if ($g = get_image($SKU, $bImages)) {
                        $g = current($g);
                        if (!$g['width'] || !$g['height']) {
                            $str .= 'WARNING! On preprocessing, file ' . $g['name'] . ' appears to be unreadable; "b" image not created' . "\n";
                            continue;
                        }
                        $path = '/images/documentation/' . $GCUserName . '/' . $g['name'];
                        if (!$thumbWidth) $thumbWidth = round($g['width'] * $imgMag, 0) - $thumbLeft;
                        if (!$thumbHeight) $thumbHeight = round($g['height'] * $imgMag, 0) - $thumbTop;
                    } else {
                        continue;
                    }

                    $thumbStats .= "$SKU:\nmag=$imgMag\nimgLeft=$imgLeft\nimgTop=$imgTop\nthumbLeft=$thumbLeft\nthumbLeft=$thumbLeft\nthumbTop=$thumbTop\nthumbWidth=$thumbWidth\nthumbHeight=$thumbHeight\n\n";
                    $path2 = '/images/documentation/' . $GCUserName . '/tmp/' . $SKU . '_b.jpg';
                    $resource = create_thumbnail($_SERVER['DOCUMENT_ROOT'] . $path, $shrink = $imgMag, $crop = '', $location = 'returnresource', $options = array());
                    memory_usage('after `b` first pass (' . $t . ')');
                    $left = $thumbLeft - $imgLeft;
                    $top = $thumbTop - $imgTop;
                    create_thumbnail($resource, '', array($left, $top, $left + $thumbWidth, $top + $thumbHeight), $_SERVER['DOCUMENT_ROOT'] . $path2);
                    memory_usage('after `b` second pass (' . $t . ')');
                    if (!$b_folder) {
                        $b_folder = true;
                        $zipfile->add_dir('b/');
                        memory_usage('after adding b directory (' . $t . ')');
                    }
                    ob_start();
                    @readfile($_SERVER['DOCUMENT_ROOT'] . $path2);
                    $filedata = ob_get_contents();
                    $filelength = strlen($filedata);
                    ob_end_clean();
                    $zipfile->add_file($filedata, 'b/' . $SKU . '_b.jpg');
                    memory_usage('after b file ' . $SKU . '_b.jpg added (' . $t . ')');
                    $badded .= "\n" . $SKU . ' - ' . $g['name'] . ' (thumbnail), ' . number_format($filelength, 0) . 'b';
                    $b_added++;
                }
            }
        } /** end if false block/old code */
	}
	$str.='Following files added to this zip:'."\n";
	if($mainadded){
		$str.="\n".'Main or "a" file(s) added:'."\n";
		$str.='-------------------------';
		$str.=$mainadded;
		$str.="\n";
	}
	if($mainmissing){
		$str.="\n".'Main or "a" file(s) missing:'."\n";
		$str.='---------------------------';
		$str.=$mainmissing;
		$str.="\n";
	}
	if($badded){
		$str.="\n".'Thumbnail or "b" file(s) added:'."\n";
		$str.='------------------------------';
		$str.=$badded;
		$str.="\n";
	}
	if($oversizeFile){
		$str.="\n".'Oversized files not added:'."\n";
		$str.='--------------------------'."\n";
		$str.=implode("\n",$oversizeFile)."\n";
	}
	$str.="\n".'Maximum memory used: '.round($memmax/1024/1024,3).'Mb'."\n";
	$str.="\n".'Maximum file size: '.round($filelengthmax/1024/1024,3).'Mb'."\n";
	$str.="\n".'Average file size: '.round($avgx/1024/1024,3).'Mb'."\n";
	$str.="\n".'B Image Generation Values:'."\n".'--------------------------'."\n".$thumbStats;
	memory_usage('before add summary.txt');
	$str.="\n".'Memory and process history:'."\n".'---------------------------'."\n".implode("\n",array_keys($gmicrotime))."\n";
	$zipfile->add_file($str, 'summary.txt');

	header("Content-type: application/octet-stream");
	header("Content-disposition: attachment; filename=ZipFile_".date('Ymd_His').'_batch_'.$Batches_ID.'_count='.$a_added.'-'.$b_added.".zip");
	echo $zipfile->file();
	memory_usage('after zip output');
	eOK(__LINE__);		
}else if($submode=='download'){
	$msg=', hit the back button or refresh the page';
	set_time_limit(15*60);
	if(!($a=q("SELECT * FROM gen_batches WHERE ID=$Batches_ID", O_ROW)))error_alert('Unable to locate specified batch'.$msg);
	$merchant=strtolower($a['SubType']);
	$haveFormat=false;
	unset($qt,$lastCol);
	if(!($b=$exportTypes[$merchant]))error_alert('Merchant '. strtoupper($merchant).' is not defined in export types'.$msg);
	foreach($b as $format){
		if($format['filename']==$fileName){
			$haveFormat=true;
			break;
		}
	}
	if(!$haveFormat)error_alert('this format is not specified'.$msg);
	extract($format);
	if(!$delimiter)error_alert('Delimiter not specified'.$msg);
	if(!($data=q("SELECT v.*, $Batches_ID AS BATCH_ID FROM $view v, gen_batches_entries e WHERE v.ID=e.Objects_ID AND e.Batches_ID=$Batches_ID".($orderBy?' ORDER BY '.$orderBy:''), O_ARRAY)))error_alert('no records found in this batch'.$msg);
	if(!function_exists('array_to_csv'))require($FUNCTION_ROOT.'/function_array_to_csv_v200.php');
	if(!function_exists('attach_download'))require($FUNCTION_ROOT.'/function_attach_download_v100.php');
	//2013-02-14: sort in the view was slowing it down way too much
	if($format['sort'])$data=subkey_sort($data,$format['sort']);
	if($templateFile=$format['templateFile']){
		$str='';
		//------- now map out the columns ---------
		$templateFile=preg_split('/[\n\r]+/',trim($templateFile));
		if($ColumnsToUse==$format['ColumnsToUse']){
			$ColumnsToUse=explode(',',trim($format['ColumnsToUse']));
		}else{
			for($i=0;$i<count($templateFile);$i++)$ColumnsToUse[]=$i+1;
		}		
		foreach($templateFile as $n=>$v){
			if(in_array($n+1,$ColumnsToUse))$str.=preg_replace('/[\n\r]+$/','',$v)."\t".($n==2 || $n==3?'ID':'')."\t".($n==2 || $n==3?'RlxID':'')."\t".($n==2 || $n==3?'BATCH_ID':'')."\n";
			$templateFile[$n]=explode("\t",$v);
			$widths[count($v)]=count($v);
			$rows[]=count($v);
		}
		if(false && count($templateFile)!==3){
			error_alert('WARNING! This template is not valid - it does not have 3 rows');
		}else if(count($widths)>1){
			foreach($rows as $n=>$v)$stat[]='row '.($n+1).'='.$v;
			error_alert('WARNING! This template is not valid - each row '.
			'does not have the same columns ('.implode('; ',$stat).')');
		}		
		$map=array();
		foreach($templateFile[1] as $n=>$v){
			//we assume each column is unique; we will key off of both fields
			$map[strtolower($v)]=$n;
			$map[strtolower($templateFile[2][$n])]=$n;
		}
		foreach($data as $r){
			if(!$map2){
				$i=-1;
				foreach($r as $n=>$v){
					$i++;
					$map2[strtolower($n)]=$n;
				}
			}
			//now begin pulling data - this is the "smart part"
			foreach($templateFile[1] as $idx=>$field){
				$key=(isset($map2[strtolower($templateFile[2][$idx])]) ? $map2[strtolower($templateFile[2][$idx])] : $map2[strtolower($field)]);
				$str.=($key ? $r[$key] : '')."\t";
			}
			$str.=$r['ID']."\t".$r['RlxID']."\t".$Batches_ID."\n";
		}
		//-----------------------------------------
	}else{
		$str='';
		if($additionalHeader)$str.=$additionalHeader."\n";
		$str.=array_to_csv(
			$data,
			$showHeaders || !isset($showHeaders) ? true : false,
			$options=array(
				'delimiter'=>$delimiter,
				'qt'=>(isset($qt)?$qt:NULL),
				'function'=>$function,
				'lastCol'=>(isset($lastCol)?$lastCol:NULL),
			)
		);
	}
	attach_download('',$str,$exportFileName);
}else if($submode=='deleteColumn'){
	if(!$column)error_alert('field value not passed');
	prn($restrictedFields);
	if(in_array(strtolower($column),$restrictedFields))error_alert('The field '.$column.' cannot be deleted; it is a restricted field.  If you are an administrator go to Home > Preferences and change this under Data Management');
	$exports=process_views($present,$Merchant);
	foreach($exports as $n=>$v){
		$view=$v['view'];
		$list=$v['list'][0];
		$__POST=array();
		foreach($list as $lcaseField=>$str){
			if(strtolower($column)==strtolower($lcaseField)){
				$removed++;
				continue; //exclude
			}
			$__POST[$view][$hasFieldName[$lcaseField]]=$str;
		}
		update_view($view,$__POST[$view],array(
			'suppress_alerts'=>true,
		));
	}
	?><script language="javascript" type="text/javascript">
	window.parent.$('#r_<?php echo preg_replace('/[^-_a-z0-9]/i','',$column);?>').remove();
	</script><?php
	error_alert('Field '.$column.' removed from '.$removed.' view'.($removed>1?'s':''));
}else if($submode=='deleteBatch'){
	//reverse this batch
	$merchant=q("SELECT SubType FROM gen_batches WHERE ID=$Batches_ID",O_VALUE);
	q("DELETE FROM gen_batches WHERE ID=$Batches_ID");
	ob_start();
	if($reflag)q("UPDATE finan_items i, gen_batches_entries e SET ".$merchant."_ToBeExported=1 WHERE i.ID=e.Objects_ID AND e.Batches_ID=$Batches_ID", ERR_ECHO);
	$err=ob_get_contents();
	ob_end_clean();
	if($err){
		mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.$GLOBALS['_fl'].', line '.__LINE__,get_globals($err='error on batch subtype'),$fromHdrBugs);
	}
	q("DELETE FROM gen_batches_entries WHERE Batches_ID=$Batches_ID");

	?><script language="javascript" type="text/javascript">
	alert('Batch entry deleted');
	window.parent.g('batch_<?php echo $Batches_ID;?>').style.display='none';
	</script><?php
	eOK();
}else if($submode=='compareViews'){
	$exports=process_views($present,$Merchant);
	?><style>
	.yat td, .yat th{
		border:1px solid #ccc;
		}
	.there{
		background-color:beige;
		}
	.padright{
		padding-right:18px;
		}
	.same{
		background-image:url("/images/i/check-darkgreen.png");
		background-position:center right;
		background-repeat:no-repeat;
		}
	.editable{
		cursor:pointer;
		}
	.editable textarea{
		display:none;
		size:inherit;
		font-family:inherit;
		background-color:aliceblue;
		border:none;
		margin:0px;
		}
	.editable p{
		display:inline;
		padding:0px;
		margin:0px;
		}
	.nopad{
		padding:0px;
		}
	</style>
	<script language="javascript" type="text/javascript">
	$(document).ready(function(){
		$('.editable').click(function(){
			$(this).addClass('nopad').find('span').hide();
			$(this).find('textarea').show().css('width','100%','height','100%').focus();
		});
		$('.editable textarea').blur(function(){
			$(this).parent().removeClass('nopad');
			$(this).hide();
			$(this).prev().show().text($(this).val());
		});
	});
	</script>
	<form name="form2" id="form2" action="export_manager.php" target="w2" method="post">
	<h1>Merchant<?php echo count($Merchant)>1?'s':'';?>: <?php echo implode(', ',$Merchant);?></h1>
	<p class="gray">this does a side-by-side field comparision of all export views for the named merchant(s). <br />
	This can also identify unneeded fields or the same field value by different names. </p>
	<a href="#" id="toggle1"><?php echo ($toggle1=='hide'?'Show':'Hide').' field values';?></a>
	<br />
	<table class="yat">
	<thead>
	<tr>
	<th>&nbsp;</th>
	<th class="tac">File Info </th>
	<?php
	//views
	foreach($exports as $v){
		foreach($v['list_from'] as $w){
			?><th class="tac" title="<?php echo h($w);?>" style="cursor:pointer;">
			<?php
			prn($v['filename']);
			?></th><?php
		}
	}
	?>
	</tr>
	<?php
	//merchants if plural
	if(count($Merchant)>1){
		foreach($exports as $v){
			foreach($v['list_from'] as $w){
				$cols[$v['merchant']]++;
			}
		}
		?><tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<?php
		prn($cols);
		foreach($cols as $n=>$v){
			?><th colspan="<?php echo $v;?>"><?php echo $n;?></th><?php
		}
		?></tr><?php
	}
	//overlist
	if($hasUnion){
		?><tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<?php
		foreach($exports as $v){
			?><th class="tac" colspan="<?php echo count($v['list']);?>"><?php echo $v['view'];?></th><?php
		}
		?></tr><?php
	}
	?><tr>
	<th>&nbsp;</th>
	<th class="tac">Field Info </th>
	<?php
	//views
	foreach($exports as $v){
		foreach($v['list_from'] as $w){
			?><th class="tac" title="<?php echo h($w);?>" style="cursor:pointer;">
			
			<h3 class="red"><?php echo $v['view'];?></h3>
			<span style="font-weight:normal;">with: </span><?php
			if(preg_match('/^[`_a-zA-Z0-9 ]+$/',$w)){
				echo str_replace('`','',$w);
			}else{
				unset($tlist);
				foreach($tables as $t)if(preg_match('/\b'.$t.'\b/',$w)){
					$tlist[]=$t;
				}
				echo implode(', ',$tlist);
			}
			?></th><?php
		}
	}
	?>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td>&nbsp;</td>
	<td>View Info:</td>
	<?php
	//views
	foreach($exports as $v){
		?><th class="tac">
		Template: <a href="/gf5/console/systementry.php?_Profiles_ID_=13&hbs_exporttypes_ID=<?php echo $v['ID'];?>" title="Edit this view's properties" onclick="alert('This is NOT a user-friendly form; if you have any questions about using this form please contact a DB administrator first!'); return ow(this.href,'l1_se','700,800');"><strong><?php echo trim($v['templateFile'])?'Yes':'No';?></strong></a>&nbsp;&nbsp;
		<a href="export_manager.php?submode=viewTemplate&Profiles_ID=<?php echo $v['ID'];?>" onclick="return ow(this.href,'l1_template','900,500');"><img src="/images/i-local/binocs.png" width="26" height="22" alt="view" /></a>
		</th><?php
	}
	?>
	</tr>
	<?php
	//field rows
	foreach($hasField as $n=>$v){
		$same=array();
		$total=0;
		foreach($exports as $w){
			foreach($w['list_from'] as $p=>$x){
				$val=trim(strtolower($w['list'][$p][$n]));
				if(!strlen($val))continue;
				$total++;
				$same[$val]=true;
			}
		}
		?>
		<tr id="r_<?php echo preg_replace('/[^-_a-z0-9]/i','',$hasFieldName[strtolower($n)]);?>">
		<td style="opacity:.75;"><a href="/gf5/console/export_manager.php?submode=deleteColumn&column=<?php echo $hasFieldName[strtolower($n)];?>&Merchant=<?php echo implode(',',is_array($Merchant)?$Merchant:array($Merchant));?>&present=<?php echo is_array($present)?implode(',',$present):$present;?><?php if($Batches_ID)echo '&Batches_ID='.$Batches_ID;?>" target="w2" title="delete this column for all views" onclick="return confirm('Are you sure you want to delete this column? (A backup will be made of the view prior to deleting)');"><img src="/images/i/del2.gif" width="16" height="18" alt="del" /></a></td>
		<td class="padright<?php echo count($same)==1 && $total>1?' same':''?>"><input name="hasField[<?php echo h($n);?>]" type="hidden" id="hasField[<?php echo h($n);?>]" value="0" />
		<label title="show or hide this field">
			<input name="hasFieldName[<?php echo h($n);?>]" type="checkbox" id="hasFieldName[<?php echo h($n);?>]" value="1" <?php echo $_SESSION['special']['fieldComp']['hide'][$n]?'':'checked';?> />
		<?php echo $hasFieldName[strtolower($n)];?></label></td>
		<?php
		foreach($exports as $w){
			foreach($w['list_from'] as $p=>$x){

				$there=isset($w['list'][$p][$n]);

				$val=$w['list'][$p][$n];
				$val=preg_replace('/\b(_utf8|_latin1)\b/','',$val);
				$e='if|concat|round|least|greatest|trim|lpad|substr|convert';
				foreach(explode('|',$e) as $er)$val=preg_replace('/\b'.$er.'\(/',strtoupper($er.'('),$val);
				if(preg_match('/^[`_a-zA-Z0-9]+$/',$x)){
					$val=str_replace($x.'.','',$val);
				}else{
					foreach($tables as $t)if(preg_match('/\b'.$t.'\b/',$x)){
						$val=str_replace('`'.$t.'`.','',$val);
					}
				}

				$len=strlen($val);
				?><td class="editable <?php echo $there?'there':'';?>" title="<?php echo h($val);?>"><span><?php
				echo $there?($toggle1=='hide'?'Y':$val):'&nbsp;';
				?></span><textarea name="<?php echo $w['view'] . '['.$hasFieldName[strtolower($n)].']';?>"><?php echo h($val);?></textarea></td><?php
			}
		}
		?></tr><?php
	}
	?>
	<tr>
	<td>&nbsp;</td>
	<td>New field: <br />
	<input name="NewField" type="text" id="NewField" /></td>
	<?php
	foreach($exports as $w){
		foreach($w['list_from'] as $p=>$x){
			?><td class="editable"><span><p class="gray">{new field}</p></span><textarea name="<?php echo $w['view'] . '[_new_field_]';?>"></textarea></td><?php
		}
	}
	?>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td><input name="submode" type="hidden" id="submode" value="updateView" />
	  <input name="view" type="hidden" id="view" /></td>
	<?php
	foreach($exports as $w){
		?><td>
		<input type="submit" name="Submit" value="Update this Template" onclick="g('view').value='<?php echo $w['view'];?>';" />
		</td><?php
	}
	?>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>Code:</td>
		<?php
		foreach($exports as $w){
			foreach($w['list_from'] as $p=>$x){
				?><td><textarea style="width:100%;min-width:250px;" rows="15"><?php 
				echo h(
				str_replace('ALGORITHM=UNDEFINED DEFINER=`cpm180`@`%` SQL SECURITY DEFINER','OR REPLACE', 
				preg_replace('/(\s+AS\s+`[^`]+`,)/i','$1'."\n",
				preg_replace('/\b(_utf8|_latin1)\b/','',$w['create'])
				))
				);
				echo ";\n";
				echo 'SELECT * FROM '.$w['view'].';';
				?></textarea></td><?php
			}
		}
		?>
	</tr>
	</tbody>
	</table>
	</form>
	<?php
	
	//send me this data
	/* gnarly cool but no longer needed
	$data=chunk_split(base64_encode(serialize(array(
		'data'=>'View comparison for listed merchants',
		'Merchant'=>$Merchant,
		'exports'=>$exports,
		'hasField'=>$hasField,
		'hasFieldName'=>$hasFieldName,
	))));
	$hash=md5($data);
	mail($developerEmail, 'Notice for merchant(s) '.implode(', ',$Merchant).', hash='.$hash,'Base64 encoded snapshot of data views for merchant(s) '.implode(', ',$Merchant).', hash='.$hash."\n\n".$data,$fromHdrBugs);
	*/
	goto bypass;
}else if($submode=='updateView'){
	update_view($view, $_POST[$view]);
	goto bypass;
}else if($submode=='viewTemplate'){
	$a=q("SELECT * FROM hbs_exporttypes WHERE ID=$Profiles_ID", O_ROW);
	extract($a);
	$templateFile=preg_split('/[\n\r]+/',trim($templateFile));
	foreach($templateFile as $n=>$v){
		$templateFile[$n]=explode("\t",$v);
		$widths[count($v)]=count($v);
		$rows[]=count($v);
	}
	?>
	<style type="text/css">
	.template{
		border-collapse:collapse;
		}
	.template td{
		border:1px solid #ccc;
		}
	.template td,.template th{
		padding:3px 4px 1px 7px;
		}
	</style>
	<h1>Template Specification</h1>
	<p>Fields on the top row are not shown here but will also export with the data</p>
	<?php
	if(count($templateFile)!==3){
		?><p class="red">WARNING! This template is not valid - it does not have 3 rows</p><?php
	}
	if(count($widths)>1){
		?><p class="red">WARNING! This template is not valid - each row does not have the same columns</p>
		<?php
		foreach($rows as $n=>$v){
			echo 'Row '.($n+1).': '.$v.' columns<br />';
		}
		?>
		<?php
	}
	
	?>
	<table class="template">
	<?php if(false){ ?>
	<thead><tr>
	<?php
	ob_start();
	$i=0;
	foreach($templateFile[0] as $n=>$v){
		if(!strlen($v)){
			$cols++;
		}else{
			$i++;
			if($rand){
				$colspans[$rand]=$cols;
			}
			$rand=md5(rand(1,1000000));
			if($n>0)echo '</td>';
			?><td xcolspan="<?php echo $rand;?>"><?php echo $v;?><?php
		}
	}
	?></td><?php
	$out=ob_get_contents();
	ob_end_clean();
	foreach($colspans as $rand=>$cols){
		$out=str_replace('xcolspan="'.$rand.'"', 'colspan="'.$cols.'"',$out);
	}
	echo $out;
	?></tr>
	</thead>
	<?php
	}
	?><tbody>
	<?php
	for($i=1;$i<=2;$i++){
		?><tr>
		<?php
		$j=0;
		foreach($templateFile[$i] as $n=>$v){
			?><td><?php 
			if($i==1){
				$j++;
				$mod=fmod($j,26);
				if($mod==0)$mod=26;
				$second=floor(($j-1)/26);
				$map=($second?chr(64+$second):'').chr(64+$mod);
				prn($map);
			}
			echo $v;?></td><?php
		}
		?>
		</tr><?php
	}
	?>
	</tbody>
	</table>
	<?php
	goto bypass;
}
//------ presentation output -------
if(!$refreshComponentOnly){
	?><style type="text/css">
	#exportManager ul{
		margin-top:0px;
		}
	</style>
	<script language="javascript" type="text/javascript">		
	</script><?php
}
?>
<form id="formExportManager" name="formExportManager" action="/gf5/console/resources/bais_01_exe.php" method="post" target="w2"><?php
if($disposition=='preExport' || $disposition=='readyExport'){
	/*
	2012-07-04
		* this is when the batch has been created as Pending and they need to commit to it
	*/
	ob_start();
	?>
	  <input type="button" name="Submit" value="See Full Batch History" onclick="window.location='export_manager.php?disposition=allExports';" />
	<?php
	$buttonsAfter=ob_get_contents();
	ob_end_clean();
	?>
	<h2><?php echo $disposition=='preExport'?'Batch Export Confirmation':'Batch Export Files';?></h2>
	<p><?php
	if($disposition=='preExport'){
		?>The following export files will be created<?php
	}else{
		?>Click on each file name to download.  When you have downloaded all files, click Close<?php
	}
	?></p>
	<?php
	$batches=explode(',',$batches);
	showBatches($batches,array('disposition'=>$disposition,'show'=>'all'));
	?>
	<p><?php
	if($disposition=='preExport'){
		?><?php echo $Records==1?'This':'These'?> <?php echo $Records;?> record<?php echo $Records==1?'':'s'?> will be marked for export and given a batch number.  Click "commit" to continue or "Cancel" to undo this action.<?php
	}else{
		//nothing fo rnow
	}
	?></p>
	<div class="fr">
	<?php if($disposition=='preExport')$suppressCloseButton=true;?>
	<?php if($disposition=='preExport'){ ?>
	<input type="hidden" name="disposition" id="disposition" value="readyExport" />
	<input type="hidden" name="batches" id="batches" value="<?php echo is_array($batches)?implode(',',$batches):$batches;?>" />
	<input type="submit" name="Submit" value="Commit" onclick="g('submode').value='commitBatch';" />
	<input type="submit" name="Submit" value="Cancel" onclick="if(!confirm('Are you sure you want to cancel exporting this batch?')){return false;} g('submode').value='cancelBatch';" />
	<?php } ?>
	</div>
	<!--
	<p>Note you are in a precarious position right now in between [explain options]</p>
	-->
	<?php
}else if($disposition=='allExports'){
	?><div id="exportManager">
	<h1>Export Batch History</h1>
	<p>
	The following is a list of export batches from newest to oldest, grouped by merchant type.  To re-export simply click on any of the file formats.  To review products that are contained in a batch, click the "view records" link to the left of any batch
	</p>
	<?php
    $sql = "SELECT ID FROM gen_batches WHERE Type='Export' AND SubType IN('".implode("','",array_keys($exportTypes))."') ORDER BY StartTime Desc";
	$batches=q($sql, O_COL);
	showBatches($batches,array('disposition'=>$disposition,'show'=>($show?$show:'partial')));
	?>
	</div><?php
}else{
	$submode='batchPrepare';
	?>
	<style>
	#exportManager{
		border:1px solid #665;
		border-radius:7px;
		padding:10px;
		background-color:#fffcf4;
		float:left;
		width:50%;
		}
	#exportManager td{
		padding:3px 5px 1px 7px;
		}
	</style><div id="exportManager">
	<h2>Export Manager</h2>
	<p class="gray">If indicated, the following records need to be exported to merchants</p>
	<table>
	<?php
	if($a=q("SELECT MIVA_ToBeExported AS MIVA, AMAZON_ToBeExported AS AMAZON, EBAY_ToBeExported AS EBAY FROM finan_items WHERE ResourceType IS NOT NULL AND (MIVA_ToBeExported>0 OR AMAZON_ToBeExported>0 OR EBAY_ToBeExported>0)", O_ARRAY)){
		foreach($a as $v){
			$MIVA+=$v['MIVA'];
			$AMAZON+=$v['AMAZON'];
			$EBAY+=$v['EBAY'];
		}
		foreach(array('MIVA','AMAZON','EBAY') as $v){
			?><tr>
			<!-- <td><input name="export[<?php echo $v;?>]" type="checkbox" id="export<?php echo $v;?>" value="1" onchange="dChge(this);" <?php echo $$v?'':'disabled';?> /></td> -->
			<td>
			<!-- <label for="export<?php echo $v?>"> -->
			<?php echo $v?>
			<!-- </label> -->
			</td>
			<td>(<?php echo $$v ? $$v.' record'.($$v>1?'s':'') : '<em>none</em>';?>)</td>
			</tr><?php
		}
	}
	?>
	</table>
	<div class="fr">
	<input type="button" name="button" value="Select Records for Batch.." onclick="return ow('export_select.php','l1_export','850,700');" />
	<br />
	<input type="button" onclick="return ow('export_manager.php?disposition=allExports','l1_export','950,600');" name="Submit" value="View Export Batch History.." />
	<br />
	<br />
	<input name="button" type="button" value="Vendor Export Status" onclick="return ow('export_summary.php','l1_expsummary','850,600');" />
	
	<?php
	if(!$exportDisableCancel){
		?><input type="button" name="button" value="Cancel" /><?php
	}
	?>
	</div>
	</div>
	<?php
}
?>
<input name="mode" type="hidden" id="mode" value="exportManager" />
<input name="submode" type="hidden" id="submode" value="<?php echo $submode;?>" />
</form><?php
} //------------ end break loop ----------------
bypass:
?>