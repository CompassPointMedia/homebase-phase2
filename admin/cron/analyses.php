<?php
/*
to be done on analyses
----------------------
synch system_analyses tables on all dbs
remove 	if($MASTER_DATABASE!=='cpm18001')continue;
finish the queres for (Children_ID:foundationdirector) etc. and make useful
remove the TRUNCATE queries
remove false's that are stopping up the system entries
remove the prn's
add else-> error checking


*/
if(false){ //----------------  not ready yet ----------------------
	$localSys['scriptID']='generic';
	$localSys['scriptVersion']='0.1';
	require($_SERVER['DOCUMENT_ROOT'].'/gf5/console/systeam/php/config.php');
	require($_SERVER['DOCUMENT_ROOT'].'/admin/config.admin.php');
	require($_SERVER['DOCUMENT_ROOT'].'/gf5/console/resources/bais_00_includes.php');
	
	$nameRecordAs='child';
	
	$fl=__FILE__;
	$rightnow=date('H:i:s');
	$accounts=q("SELECT * FROM gf_account", O_ARRAY);
	foreach($accounts as $account){
		extract($account);
		//for now just run this system in GL Franchise
		if($MASTER_DATABASE!=='cpm180_hmr')continue;
		prn($MASTER_DATABASE);
		q("TRUNCATE TABLE system_analyses_runs", $MASTER_DATABASE);
		q("TRUNCATE TABLE system_analyses_matches", $MASTER_DATABASE);
		$ln=__LINE__+1;
		if($analyses=q("SELECT a.* FROM system_analyses a 
			LEFT JOIN system_analyses_runs b ON	
			a.ID=b.Analyses_ID AND 
			SUBSTRING(DATE_ADD(NOW(), INTERVAL -(a.Recurrency) DAY),1,10) <= SUBSTRING(b.StartTime,1,10)
			WHERE 
			a.Active=1 AND 
			a.RunTime<'$rightnow' AND
			b.ID IS NULL", O_ARRAY_ASSOC, $MASTER_DATABASE)){
			foreach($analyses as $Analyses_ID=>$analysis){
				//only run version 1.0 with this system
				if($analysis['Version']!=='1.0')continue;
				
				//convert the query - it may contain PHP but must go "out" of string mode with quotes and then go back
				eval('$sql="'.$analysis['Query'].'";');
				
				//enter the run
				$ln=__LINE__+1;
				$Runs_ID=q("INSERT INTO system_analyses_runs SET
				Analyses_ID=$Analyses_ID,
				Settings='".base64_encode(serialize(stripslashes_deep($analysis)))."',
				StartTime=NOW(),
				FinishTime=NOW(),
				CreateDate=NOW(),
				Creator='cron'", O_INSERTID, $MASTER_DATABASE);
	
				$ln=__LINE__+1;
				if($records=q($sql, O_ARRAY_ASSOC)){
					$i=0;
					foreach($records as $key=>$$nameRecordAs){
						prn($$nameRecordAs);
						extract($$nameRecordAs);
						$i++;
						/*
						there is some question about the storage method and what is stored.  This is so far predicated on the concept that an analyses only wants to catch an event once, not more
						*/
						$ln=__LINE__+1;
						if($match=q("SELECT * FROM system_analyses a, system_analyses_matches b WHERE a.ID=b.Analyses_ID AND a.ID=$Analyses_ID AND b.ObjectKey=$key", O_ROW, $MASTER_DATABASE)){
							//do we want to redo the event?
							echo 'already matched<br />';
							continue;
						}
						//mark the event in matches
						if(false){
						$ln=__LINE__+1;
						$Matches_ID=q("INSERT INTO system_analyses_matches SET
						Analyses_ID='$Analyses_ID',
						Runs_ID=$Runs_ID,
						Record='".base64_encode(serialize(stripslashes_deep($record)))."',
						ObjectKey='$key',
						CreateDate=NOW(),
						Creator='cron'",O_INSERTID, $MASTER_DATABASE);
						}
						
						//perform the action
						if(strtolower($analysis['Action'])=='send email'){
							if(file_exists($emailSource=$_SERVER['DOCUMENT_ROOT'].'/gf5/console/emails/'.$analysis['FileName'])){
								//get recips
								$recipients=explode(',',$analysis['Output']);
								foreach($recipients as $recipient){
									$a=explode(':',$recipient);
									$position=$a[1];
									switch($a[1]){
										case 'parent':	
											$ln=__LINE__+1;
											$person=q("SELECT p.ID,p.FirstName,p.LastName,p.Email FROM gf_parents p, gf_ChildrenFosterhomes cf, gf_FosterhomesParents fp 
											WHERE 
											cf.Fosterhomes_ID=fp.Fosterhomes_ID AND 
											cf.Children_ID=$Children_ID AND 
											cf.DateAssigned<=NOW() AND 
											(cf.DateReleased>=NOW() OR !cf.DateReleased) AND 
											fp.DateAssigned<=NOW() AND
											(fp.DateReleased>=NOW() OR !fp.DateReleased) AND 
											fp.Position='Primary' AND
											fp.Parents_ID=p.ID", O_ROW, $MASTER_DATABASE);
										break;
										case 'casemanager':
											$ln=__LINE__+1;
											if(false){
											$b=q("SELECT
													  
											FROM
											gf_ChildrenFosterhomes cf, gf_fosterhomes f, bais_universal u
											WHERE
											cf.Children_ID=$Children_ID AND
											cf.Fosterhomes_ID=f.ID AND
											f.fh_stusername=u.un_username AND 
											un_username=gf_OfficeStaff", $MASTER_DATABASE);
											}
										break;
										case 'foundationdirector':
											$person=array(
												'FirstName'=>'Samuel',
												'LastName'=>'Fullman',
												'Email'=>'samuelf@compasspoint-sw.com',
											);
									}
									$emailTo='samuelf@compasspoint-sw.com';
									require($MASTER_COMPONENT_ROOT.'/emailsender_02.php');
								}
							}else{
								mail($developerEmail,'error file '.__FILE__.', line '.__LINE__,get_globals(),$fromHdrBugs);
								break;
							}
							
						}
					}
				}
				$ln=__LINE__+1;
				q("UPDATE system_analyses_runs SET FinishTime=NOW() WHERE ID=$Runs_ID");
			}	
		}
		prn($qr);
	}
	
	
	
	/* - this was an earlyier version and can be deleted probably -- */
	exit;
	if(false){
		$daysout=45;
		$pastdaysout=45;
		if($currentModerateLOC=q("SELECT 
			b.ID AS Childrenlocs_ID, 
			a.ID AS Children_ID, 
			a.FirstName, 
			a.LastName, 
			b.Locs_ID, 
			b.StartDate, 
			IF( b.ExpirationDate, b.ExpirationDate, '(none)' ) AS ExpirationDate,
			f.Address, f.City, f.State, f.Zip,
			u.un_username, un_firstname, un_lastname, un_email
			FROM gf_children a
			LEFT JOIN gf_ChildrenLocs b ON a.ID = b.Children_ID
			LEFT JOIN gf_ChildrenLocs c ON c.Children_ID = a.ID
			AND DATE_ADD( b.ExpirationDate, INTERVAL 1 DAY ) = c.StartDate,
			gf_ChildrenFosterhomes cf, gf_fosterhomes f 
			LEFT JOIN bais_universal u ON f.fh_stusername=u.un_username
			WHERE 
			a.Active=1 AND
			
			b.Locs_ID >1 AND b.ExpirationDate	BETWEEN NOW() AND DATE_ADD( NOW() , INTERVAL 75	DAY )
			/* lapse in LOC */
			AND c.ID IS NULL
			/* only children assigned to a foster home */
			AND a.ID=cf.Children_ID AND cf.DateAssigned<=NOW() AND (!cf.DateReleased OR cf.DateReleased>=NOW())
			AND cf.Fosterhomes_ID=f.ID
			/* and this has not already been noted by this analysis */
			ORDER BY b.ExpirationDate", O_ARRAY)){
			?><table class="temp"><?php
			foreach($currentModerateLOC as $v){
				$i++;
				foreach($v as $o=>$w){
					if(preg_match('/date/',$n)){
						$v[$o]=($w=='0000-00-00' ? '(none)' : date('m/d/Y',strtotime($w)));
					}	
				}
				if($i==1){
					?><tr><td><?php echo implode('</td><td>',array_keys($v));?></td></tr><?php	
				}
				?><tr><td>
				<?php echo implode('</td><td>',$v);?>
				</td>
				</tr><?php
			}
			?></table><?php
		}
	}
} ///---------------------- end false shield ----------------------
?>