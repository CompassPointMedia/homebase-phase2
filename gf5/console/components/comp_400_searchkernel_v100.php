<?php
/*
2012-06-16:


*/
if(!function_exists('search_precedence'))require($FUNCTION_ROOT.'/function_search_suite_v100.php');
if(!function_exists('text_functions'))require($FUNCTION_ROOT.'/group_text_functions_v100.php');
if(!function_exists('gl_link_manager'))require($FUNCTION_ROOT.'/function_gl_link_manager_v101.php');



if($mode=='search'){
	$key=substr(md5(time().rand(1,100000)),0,16);

	if(!function_exists('filter_inverse')){
		function filter_inverse(){
			$a=func_get_args();
			$filter=$a[0];
			if(!$filter)return false;
			unset($a[0]);
			return (in_array($filter,$a) ? false : true);
		}
	}
	
	if(preg_match('/^[A-Z]{4}[0-9]{4}$/i',$q)){
		if($ID=q("SELECT ID FROM finan_items WHERE SKU='$q'", O_VALUE)){
			?><script language="javascript" type="text/javascript">
			window.parent.ow('/gf5/console/products.php?Items_ID=<?php echo $ID?>','l1_items','750,700');
			</script><?php
			eOK(__LINE__);
		}
	}
	$searchFields['finan_items']=array(
		'SKU'=>array('posn'=>'start'),
		'Name'=>array(),
		'Category'=>array(),
		'SubCategory'=>array(),
		'Featured'=>array(),
		'Description'=>array(),
		'LongDescription'=>array(),
		'MetaTitle'=>array(),
		'MetaDescription'=>array(),
		'MetaKeywords'=>array(),
		'FileSize'=>array('posn'=>'exact'),
		'Theme'=>array(),
		'SubTheme'=>array(),
		'AMAZON_ListingID'=>array(),
		'AMAZON_ASIN1'=>array(),
		
	);
	unset($fields);
	foreach($searchFields['finan_items'] as $n=>$v){
		$str=$n;
		$str.=($v['type']=='exact'?'=':' LIKE ');
		$str.='\'';
		if($v['posn']!=='start' && $v['posn']!=='exact')$str.='%';
		$str.=$q;
		if($v['posn']!=='exact')$str.='%';
		$str.='\'';
		$fields[]=$str;
	}
	$fields='('.implode(' OR ',$fields).')';
	if($a=q("SELECT ID, SKU, Name, Description, LongDescription, Featured FROM finan_items WHERE ResourceType IS NOT NULL AND $fields ORDER BY SKU", O_ARRAY)){
		$results['Products']=$a;
		//OK
	}	
	if('old search stuff for use' && false){
		//handle special instructions on q:
		if(preg_match('/^(invoice|payment|check|deposit|client|customer|staff):/i',$q,$m)){
			$a=explode(':',$q);
			$filter=strtolower($m[1]);
			unset($a[0]);
			$q=trim(implode(':',$a));
		}
		if(!filter_inverse($filter, 'invoice','payment','check','deposit') && preg_match('/^([-a-z]{0,3})*[-0-9]+([-a-z]{0,3})*$/i',stripslashes($q)) && $a=q("SELECT a.ID, a.HeaderType, a.HeaderDate, a.HeaderNumber FROM finan_headers a WHERE '$q' =a.HeaderNumber", O_ARRAY)){
			if(count($a)==1 && !$suppressQuickResult){
				extract($a[1]);
				if(strtolower($HeaderType)=='invoice'){
					$Leases_ID=q("SELECT
					lt.Leases_ID
					FROM
					finan_headers h, finan_transactions t, gl_LeasesTransactions lt
					WHERE h.ID=t.Headers_ID AND h.Accounts_ID!=t.Accounts_ID AND t.ID=lt.Transactions_ID AND h.ID=$ID", O_VALUE);
					?><script language="javascript" type="text/javascript">
					window.parent.ow('/gf5/console/leases.php?Leases_ID=<?php echo $Leases_ID?>','l1_leases','700,700');
					</script><?php
				}else if(strtolower($HeaderType)=='payment'){
					?><script language="javascript" type="text/javascript">
					window.parent.ow('/gf5/console/payments.php?Payments_ID=<?php echo $ID; ?>','l1_payments','850,700');
					</script><?php
				}else if(strtolower($HeaderType)=='check'){
					?><script language="javascript" type="text/javascript">
					window.parent.ow('/gf5/console/checks.php?Checks_ID=<?php echo $ID; ?>','l1_checks','700,700');
					</script><?php
				}else if(strtolower($HeaderType)=='deposit'){
					?><script language="javascript" type="text/javascript">
					window.parent.ow('/gf5/console/deposits.php?Deposits_ID=<?php echo $ID; ?>','l1_deposits','700,700');
					</script><?php
				}
				eOK(__LINE__);
			}else{
				foreach($a as $v){
					if(filter_inverse($filter,$v['HeaderType']))continue;
					$results[strtolower($v['HeaderType'])][]=$v;
				}
			}
		}
		if(!filter_inverse($filter, 'address') &&
			(preg_match('/^[0-9]+([-a-z0-9 \/]{0,4})*\s+[-a-z0-9.]+/i',stripslashes($q)) || $filter=='address')){ //an address like 1204 Marlton St
			if($a=q("SELECT u.ID, p.Type FROM gl_properties p, gl_properties_units u WHERE p.ID=u.Properties_ID AND REPLACE(p.PropertyAddress,'.','') LIKE '".str_replace('.','',$q)." %' GROUP BY p.ID", O_COL_ASSOC)){
				if(count($a)==1 && !$suppressQuickResult){
					foreach($a as $ID=>$Type);
					?><script language="javascript" type="text/javascript">
					window.parent.ow('/gf5/console/properties<?php echo strtolower($Type)=='apt'?'3':'2'?>.php?Units_ID=<?php echo $ID?>','l1_properties','700,700');
					</script><?php
					eOK(__LINE__);
				}else{
					foreach($a as $v)$results['address'][]=$v;
				}
			}
		}
		unset($m);
		if(!filter_inverse($filter,'invoice','property','client','staff') && $r=parse_name(stripslashes($q)) or preg_match('/^[-a-z\']+$/i',stripslashes(trim($q)),$m)){
			#error_alert(__LINE__,1);
			//search for tenants (customers)
			//search for properties
			//search for staff
			prn($r);
			if($m){
				unset($r);
				$r['LastName']=stripslashes(trim($q));
			}
			if($a=q("/* invoice (customer) */
				SELECT
				PropertyName, FirstName, LastName, 'invoice' AS ObjectName, ID AS Objects_ID, '' AS Qual1
				FROM _v_leases_master WHERE 
				(".($m ? '' : "FirstName='".addslashes($r['FirstName'])."' AND")." LastName='".addslashes($r['LastName'])."')
				
				UNION
				
				/* property */
				SELECT
				PropertyName, '' AS FirstName, '' AS LastName, 
				IF((p.PropertyName LIKE '$q%' ".($m ? '' : " OR p.PropertyName LIKE '".addslashes($r['LastName']).', '.addslashes($r['FirstName'])."%'")."), 'property', 'client') AS ObjectName, 
				IF((p.PropertyName LIKE '$q%' ".($m ? '' : " OR p.PropertyName LIKE '".addslashes($r['LastName']).', '.addslashes($r['FirstName'])."%'")."), p.ID, c.ID) AS Objects_ID, 
				p.Type AS Qual1
				FROM finan_clients c, gl_properties p WHERE
				c.ID=p.Clients_ID AND
				((p.PropertyName LIKE '$q%' ".($m ? '' : " OR p.PropertyName LIKE '".addslashes($r['LastName']).', '.addslashes($r['FirstName'])."%'").")
				OR
				(c.ClientName LIKE '$q%' ".($m ? '' : " OR c.ClientName LIKE '".addslashes($r['LastName']).', '.addslashes($r['FirstName'])."%'")."))
				
				UNION
				
				/* staff */
				SELECT
				'' AS PropertyName, un_firstname FirstName, un_lastname LastName, 'staff' AS ObjectName, un_username AS Objects_ID, st_active AS Qual1
				FROM bais_universal u, bais_staff s WHERE u.un_username=s.st_unusername AND
				(".($m ? '' : "u.un_firstname='".addslashes($r['FirstName'])."' AND")." u.un_lastname='".addslashes($r['LastName'])."')", O_ARRAY)){
				if(count($a)==1 && !$suppressQuickResult){
					$a=$a[1];
					if($a['ObjectName']=='staff'){
						$url='staff.php?un_username='.trim($a['Objects_ID']);
						$win='l1_staff';
						$dims='700,700';
					}else if($a['ObjectName']=='property'){
						$url='properties'.(strtolower($a['Type'])=='apt'?3:2).'.php?Properties_ID='.trim($a['Objects_ID']);
						$win='l1_properties';
						$dims='700,700';
					}else if($a['ObjectName']=='invoice'){
						$url='leases.php?Leases_ID='.trim($a['Objects_ID']);
						$win='l1_properties';
						$dims='700,700';
					}else if($a['ObjectName']=='client'){
						$url='clients.php?Clients_ID='.trim($a['Objects_ID']);
						$win='l1_clients';
						$dims='700,700';
					}
					?><script language="javascript" type="text/javascript">
					window.parent.ow('/gf5/console/<?php echo $url?>','<?php echo $win?>','<?php echo $dims;?>');
					</script><?php
					eOK(__LINE__);
				}else{
					foreach($a as $v){
						if(filter_inverse($filter,$v['ObjectName']))continue;
						$results[$v['ObjectName']][]=$v;
					}
				}
				prn($qr);
			}
		}
		if(false){
			//most likely a single string??
		}
	}
	
	if(count($results)){
		prn($results);
		#error_alert('tst');
		$_SESSION['special']['search'][$key]=$_GET;
		$_SESSION['special']['search'][$key]['results']=$results;
		$_SESSION['special']['search'][$key]['query']=$qr;
		?><script language="javascript" type="text/javascript">
		window.parent.location='/gf5/console/root_globalsearch.php?key=<?php echo $key;?>';
		</script><?php
	}else{
		error_alert('No results found for that search');
	}
	eOK(__LINE__);
}else{
	ob_start();

	if(!($a=$_SESSION['special']['search'][$key]))exit('Unable to find stored search in session');
	$keyThisPage=$a['thispage'];
	$keyThisFolder=$a['thisfolder'];
	unset($a['thispage'],$a['thisfolder']);
	extract($a);
	?><h1>Search Results for "<?php echo stripslashes($q);?>"</h1>
	<p>The following results were found for this search.  Click any item/person to go directly to it</p>
	<?php
	foreach($results as $type=>$v){
		?><h2><?php echo $type;?> (<?php echo count($v);?>)</h2>
		<div class="resultGroup"><?php
		foreach($v as $o=>$w){
			extract($w);
			
			?><a href="products.php?Items_ID=<?php echo $w['ID'];?>" onclick="return ow(this.href,'l1_items','900,700');" title="view this item"><?php echo $w['SKU'] . ' - '. $w['Name'];?></a><br /><?php
			
			continue;
			if($HeaderType=='invoice' || $ObjectName=='invoice'){
				if($ObjectName=='invoice')extract(q("SELECT ID, HeaderNumber, HeaderDate FROM _v_leases_master WHERE ID=$Objects_ID", O_ROW));				
				?><a href="/gf5/console/leases.php?Leases_ID=<?php echo $ID;?>" title="click to open this invoice" onclick="return ow(this.href,'l1_leases','700,800');"><?php echo 'Invoice #'.$HeaderNumber;?> - <?php echo date('n/j/Y',strtotime($HeaderDate));?></a><br><?php
			}else if(strtolower($HeaderType)=='payment'){
				?><a href="/gf5/console/payments.php?Payments_ID=<?php echo $ID;?>" title="click to open this payment" onclick="return ow(this.href,'l1_payments','850,500');"><?php echo 'Payment #'.$HeaderNumber;?> - <?php echo date('n/j/Y',strtotime($HeaderDate));?></a><br><?php
			}else if($ObjectName=='staff' && (minroles()<ROLE_AGENT || $Objects_ID==sun())){
				?><a href="/gf5/console/staff.php?un_username=<?php echo trim($Objects_ID);?>" title="click to open this record" onclick="return ow(this.href,'l1_staff','700,600');"><?php echo $LastName.', '.$FirstName;?></a><br><?php
			}else if($ObjectName=='client'){
				prn('client');
				prn($w);
			}else if($ObjectName=='property'){
				?><a href="properties<?php echo $w['Qual1']=='SFR'?'2':'3';?>.php?Properties_ID=<?php echo trim($w['Objects_ID']);?>" title="View/edit this property" onclick="return ow(this.href,'l1_properties','750,700');"><?php echo $w['PropertyName'] . (!$w['PropertyName'] ? $w['LastName'].', '.$w['FirstName']:'');?></a><br />
				<?php
			}else{
				if(!$alerted)$alerted=mail($developerEmail, 'Error in '.$MASTER_USERNAME.':'.end(explode('/',__FILE__)).', line '.__LINE__,get_globals($err='undeveloped node on search results for multiple'),$fromHdrBugs);
			}
		}
		?></div><?php
	}

	$searchOut=ob_get_contents();
	ob_end_clean();	
}

?>