<?php
$localSys['scriptID']='cron';
$localSys['scriptVersion']='4.0';

require($_SERVER['DOCUMENT_ROOT'].'/gf5/console/systeam/php/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/gf5/console/resources/bais_00_includes.php');

define('SEQ_ERROR',1); //error - overlapping LOCs
define('SEQ_NORECORD',2); //no level of care, assignment etc. present (no record) for this look-forward period
define('SEQ_EXPIRING',3); //expiring
define('SEQ_EXPIRING_NOTED',4); //expiring but previously noted by this Analysis
define('SEQ_CURRENT',5); //current


if($analyses=q("SELECT a.*, UNIX_TIMESTAMP(MAX(a.CreateDate)) AS AnalysisStart, c.Name AS EventName, c.Type AS EventType, c.Query, c.Method, UNIX_TIMESTAMP(MAX(b.CreateDate)) AS LastAnalysisRun, UNIX_TIMESTAMP(MIN(b.CreateDate)) AS FirstAnalysisRun, COUNT(DISTINCT b.ID) AS AnalysesRun FROM system_analyses a LEFT JOIN system_analyses b ON a.ID=b.Analyses_ID, system_analyses_events c WHERE a.Events_ID=c.ID AND a.Analyses_ID IS NULL AND a.Active=1 GROUP BY a.ID ORDER BY a.Priority DESC", O_ARRAY_ASSOC)){
	$i=0;
	foreach($analyses as $Analysis_ID=>$analysis){
		$i++;
		extract($analysis);
		//copy the record over
		$thisAnalysis_ID=q("INSERT INTO system_analyses SET
		Version = '".addslashes($analysis['Version'])."',
		Analyses_ID = '$Analysis_ID',
		Active = '1',
		Name = '".addslashes($analysis['Name'])."',
		Priority = '".addslashes($analysis['Priority'])."',
		Description = '".addslashes($analysis['Description'])."',
		Recurrency = '".addslashes($analysis['Recurrency'])."',
		Events_ID = '".addslashes($analysis['Events_ID'])."',
		Proximity = '".addslashes($analysis['Proximity'])."',
		Action = '".addslashes($analysis['Action'])."',
		Resources_ID = '".addslashes($analysis['Resources_ID'])."',
		Output = '".addslashes($analysis['Output'])."',
		CreateDate = NOW(),
		Creator = 'cron'", O_INSERTID);
		echo 'creating new analysis from parent, with id '.$thisAnalysis_ID.'<br />';
		
		//get candidate objects
		$candidateObject='children';
		if(!$$candidateObject){
			//in this case..
			$$candidateObject=list_children('keys');
		}

		if($EventType=='expiring'){

			//get object group - and we have to iterate individually for now [for all? probably not but certainly here]
			$candidateObject='children'; //this is the "for" set or CONFORMING CANDIDATES
			$candidateKey='Children_ID';

			//these would come from some type of dataset - first two required for a CSDR table
			$StartDateField='StartDate'; 
			$EndDateField='ExpirationDate';

			$rootTable='gf_children';
			$rootTablePrimaryKey='ID';
			$firstSecondaryTable='gf_ChildrenLocs';
			$firstSecondaryTableForeignKey='Children_ID';

			$objectName='childrenLOCs';
			$objectKeyField='ID';
			$objectKeyTableAlias='secondary1';

			$proximity=$analysis['Proximity'];
			
			foreach($$candidateObject as $n=>$$candidateKey){
				echo q("select concat(firstname, ' ',lastname) from gf_children WHERE id=".$$candidateKey, O_VALUE).'<br />';
				if($analysis['Version']=='1.0'){
					//-------------- expiring "kernel" logic 1.0 - 2008-12-14 ----------------------
					//initial state
					$seqStatus=SEQ_NORECORD;
					unset($lastRecord);
	
					$criteria="secondary1.$StartDateField <= CURDATE() AND (secondary1.$EndDateField >=CURDATE() OR !secondary1.$EndDateField)";
					//this is the query for a contiguous-sequential-daterange (CSDR) table
					while($record=q("SELECT
					
						/* ---------- example (a bit inaccurate) -------------
						$firstSecondaryTable.$firstSecondaryTablePrimaryKey AS ID,
						UNIX_TIMESTAMP(StartDate) AS StartDate, UNIX_TIMESTAMP(ExpirationDate) AS ExpirationDate, sysj.Parentanalyses_ID AS Flagged
						FROM gf_children root, gf_ChildrenLocs secondary
						LEFT JOIN system_analyses_joins sysj on sysj.Parentanalyses_ID=$Analysis_ID AND Object='childrenLOCs' /* can be broader than a table name *-/ AND ObjectKey=secondary.ID
						WHERE 1 AND root.ID=secondary.Children_ID AND
						
						EITHER
						secondary.StartDate <= CURDATE() AND (secondary.ExpirationDate >=NOW OR !secondary.ExpirationDate)
						OR
						secondary.StartDate = 'specific date' AND (secondary.ExpirationDate >=NOW OR !secondary.ExpirationDate)
						   --------------------------------- */
						
						$objectKeyTableAlias.$objectKeyField AS ID, 
						UNIX_TIMESTAMP($StartDateField) AS StartDate, 
						UNIX_TIMESTAMP($EndDateField) AS ExpirationDate, 
						$StartDateField AS StartDateEN,
						$EndDateField AS EndDateEN,
						DATEDIFF($EndDateField, NOW()) AS DateDiff, sysj.Parentanalyses_ID AS Flagged
						FROM $rootTable root, $firstSecondaryTable secondary1
						LEFT JOIN system_analyses_joins sysj ON sysj.Parentanalyses_ID=$Analysis_ID AND Object='$objectName' /* can be broader than a table name */ AND ObjectKey=$objectKeyTableAlias.$objectKeyField
						WHERE 1 AND root.$rootTablePrimaryKey=secondary1.$firstSecondaryTableForeignKey AND root.ID='".$$candidateKey."' AND
						/*
						EITHER
						secondary1.$StartDateField <= NOW() AND (secondary1.$EndDateField >=NOW() OR !secondary1.$EndDateField)
						OR
						secondary1.$StartDateField = 'tomorrow' AND 
						(secondary1.$EndDateField >='tomorrow' OR !secondary1.$EndDateField)
						*/
						$criteria", O_ARRAY)){
						if(count($record)>1){
							$seqStatus=SEQ_ERROR;
							unset($lastRecord);
							mail($developerEmail,'error file ' . __FILE__.' line '.__LINE__,get_globals(), $fromHdrBugs);
							break;
						}
						$record=$record[1];
						if($record['DateDiff'] > $proximity || !$record['ExpirationDate']){
							//expiration date falls outside range of proximity, we are done
							unset($lastRecord);
							$seqStatus=SEQ_CURRENT;
							break;
						}else{
							//query for a record EXACTLY ONE DAY AFTER this record
							$lastRecord=$record;
							$tomorrow=date('m/d/Y',$record['ExpirationDate']).' +1 day';
							$tomorrow=date('Y-m-d',strtotime($tomorrow));
							prn($record);
							echo 'querying again, next day is '.$tomorrow . '<br />';
							$criteria="secondary1.$StartDateField = '$tomorrow' AND (secondary1.$EndDateField >='$tomorrow' OR !secondary1.$EndDateField)";
							continue;
						}
					}
					if($lastRecord['Flagged']){
						echo 'record already flagged<br />';
						//last contiguous record was already flagged by the cron
						$seqStatus=SEQ_EXPIRING_NOTED;
					}else if($lastRecord){
						echo 'flagging record<br />';
						//flag record
						$seqStatus=SEQ_EXPIRING;
						$joinID=q("INSERT INTO system_analyses_joins SET
						Analyses_ID='$thisAnalysis_ID',
						Parentanalyses_ID='$Analysis_ID',
						Object='$objectName',
						ObjectKey='".$lastRecord['ID']."'", O_INSERTID);
						$joinOutputKeys[$lastRecord['ID']]=$joinID;
					}
					//------------------- end expiring kernel logic --------------------------
				}
			}
		}
	}
}



?>