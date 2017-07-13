<?php

/*

[all children implied] {childlevelofcare:expiring}

maybe xml? : and ; are not really enough - need a better structure definitely
*/

$event='{childlevelofcare:expiring}';

$eventParameters=explode(':',$event);
$dataset=q("SELECT * FROM system_analyses_datasets WHERE Name='".$eventParameters[0]."'", O_ROW);

foreach($children as $n=>$v){
		/*
		this is where Active is going to have to come in, this would get ALL children even those without a current foster home or current foster home for this period
		current foster home or foster home for this period would be
		select
		from gf_children, gf_ChildrenFosterhomes WHERE DateAssigned >= end of period AND (DateReleased <= start of period OR !DateReleased)		
		
		*/
	if($eventParameters[1]=='expiring'){
		if($a=q("SELECT
			$StartField AS StartField, $EndField AS EndField
			
			/* ============ root query block ============== */
			FROM 
			gf_children c, gf_ChildrenLocs cl
			
			#---------- insert here --------------
			#note we can do this on the first iterative query and that should be sufficient
			LEFT JOIN system_objects so ON cl.ID=so.Key1 AND so.object='gf_ChildrenLocs' AND so.ParentAnalyses_ID='$thisAnalysis'
			#--------- end insert here -----------
			
			WHERE 1 AND c.ID=cl.Children_ID
			/* ============ end root query block ============== */
			AND 
			/* -- hasn't been flagged already - very important -- */
			so.ID IS NULL
			..
			/* -- from the parameter {expiring} -- but we substitute $dataset[$StartField], $dataset[$EndField] - and note that CURDATE() could be two actual periods to represent a range -- */
			AND
			StartDate <=CURDATE() AND ExpirationDate >CURDATE() AND DATE_ADD(CURDATE(), INTERVAL $proximity)>ExpirationDate
			
			AND a.ID=$thisChildID", O_VALUE|O_ROW)){
			extract($a);
			//find the next CONTIGUOUS record
			if($null=q("SELECT
				
				/* ============ root query block ============== */
				FROM 
				gf_children c, gf_ChildrenLocs cl
				
				WHERE 1 AND c.ID=cl.Children_ID
				/* ============ end root query block ============== */
				
				AND
				StartDate IS ONE DAY GREATER THAN $EndField [AND THAT expirationdate is not also too close]
				
				", O_ROW)){
				if("THAT expiration date is too close"){
				
				}	
			}
			
			
			
		}else{
			//NO current level of care
		}
		
		
		}
	}
	

}


?>