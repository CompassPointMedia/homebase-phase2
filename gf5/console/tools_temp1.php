<?php
//identify this script/GUI
$localSys['scriptID']='gen_access1';
$localSys['scriptVersion']='4.0';
$localSys['componentID']='focusview';
$localSys['pageType']='Properties Window';
$localSys['pageLevel']=0;

require('systeam/php/config.php');
require('resources/bais_00_includes.php');


($a=q("SHOW TABLES", O_ARRAY));
$start=1;
if(!$end)$end=10;
$i=0;
foreach($a as $n=>$v){
	$i++;
	$table=$v['Tables_in_cpm180_hmr'];
	if(!preg_match('/^_v_/',$table))continue;
	prn(q("SHOW CREATE VIEW `$table`",O_ROW));
	continue;
	prn("optimizing $table");
	q('optimize table `'.$table.'`', ERR_ECHO);
	prn($qr);	
}
//prn($a);
exit(here);


$a=q("SELECT ID, SKU, Grouping FROM finan_items WHERE resourcetype is not null order by id", O_ARRAY);
?><table><tr><td>ID</td><td>SKU</td><td>Grouping</td></tr><?php
foreach($a as $v){
	?><tr>
	<td><?php echo $v['ID'];?></td>
	<td><?php echo $v['SKU'];?></td>
	<td><?php echo $v['Grouping'];?></td>
	</tr><?php
}

?></table><?php
exit;

q("update finan_items set grouping=16 where grouping=17 order by id limit 1");
q("update finan_items set grouping=17 where grouping=18 order by id limit 1");
q("update finan_items set grouping=18 where grouping=19 order by id limit 1");



if(false)q("update finan_items set grouping=19 where grouping=18");
if(false)q("update finan_items set grouping=18 where grouping=17");
if(false)q("update finan_items set grouping=17 where grouping=16");
if(false)q("update finan_items set grouping=16 where grouping=15 order by id desc limit 199");

prn(q("SELECT grouping, count(*) FROM finan_items where resourcetype is not null group by grouping order by grouping", O_COL_ASSOC));

exit;
//4981
$a=q("SELECT ID, Grouping FROM finan_items where id>=4982 AND resourcetype is not null ORDER BY ID", O_COL_ASSOC);
$i=0;
foreach($a as $n=>$v){
	if($v==15)continue;
	$i++;
	if($i<=25){
		q("UPdate finan_items set grouping=15 where id=$n");
	}else{
		$j++;
		prn("changing $n from $v to ".(floor($j/200)+15));
		q("UPDATE finan_items set grouping=". (floor($j/200)+15)." where id=$n");
	}
}
prn(q("SELECT grouping, count(*) FROM finan_items where resourcetype is not null group by grouping order by grouping", O_COL_ASSOC));

exit;
prn($a);


exit;
($a=q("SHOW TABLES", O_ARRAY));
$start=1;
if(!$end)$end=10;
$i=0;
foreach($a as $n=>$v){
	$i++;
	$a[$v['Tables_in_cpm180_hmr']]=$v['Tables_in_cpm180_hmr'];
	unset($a[$n]);
	
	if($i<$start)continue;
	if(in_array($i,explode(',',$skip))){
		prn('--- skipping '.$v['Tables_in_cpm180_hmr']);
		continue;
	}
	if($i>$end)break;
	
	prn($v['Tables_in_cpm180_hmr'].': '.q("SELECT COUNT(*) FROM `".$v['Tables_in_cpm180_hmr']."`", O_VALUE));
}
//prn($a);
exit(here);
prn(q('select count(*) from gf_poststorage'));
exit;

q('delete from gf_poststorage where date(editdate)>="2013-09-01"');
prn($qr);
exit;
$a=q("truncate table gf_poststorage", O_ARRAY);
prn($a);


exit;
/*
q("ALTER TABLE aux_ebaystorenumbers CHANGE Category Category CHAR(45) NOT NULL DEFAULT ''");
q("UPDATE aux_ebaystorenumbers SET Category='Historic City Maps', StartLetter='A', EndLetter='I' WHERE ID=7");
q("UPDATE aux_ebaystorenumbers SET Category='Historic City Maps', StartLetter='J', EndLetter='R' WHERE ID=11");
q("UPDATE aux_ebaystorenumbers SET Category='Historic City Maps', StartLetter='S', EndLetter='Z' WHERE ID=8");
q("UPDATE aux_ebaystorenumbers SET Category='Old County Maps', StartLetter='A', EndLetter='D' WHERE ID=3");
q("UPDATE aux_ebaystorenumbers SET Category='Old County Maps', StartLetter='E', EndLetter='I' WHERE ID=12");
q("UPDATE aux_ebaystorenumbers SET Category='Old County Maps', StartLetter='J', EndLetter='N' WHERE ID=9");
q("UPDATE aux_ebaystorenumbers SET Category='Old County Maps', StartLetter='O', EndLetter='S' WHERE ID=5");
q("UPDATE aux_ebaystorenumbers SET Category='Old County Maps', StartLetter='T', EndLetter='Z' WHERE ID=6");
q("UPDATE aux_ebaystorenumbers SET Category='Old State Maps', StartLetter='A', EndLetter='I' WHERE ID=10");
q("UPDATE aux_ebaystorenumbers SET Category='Old State Maps', StartLetter='J', EndLetter='R' WHERE ID=2");
q("UPDATE aux_ebaystorenumbers SET Category='Old State Maps', StartLetter='S', EndLetter='Z' WHERE ID=4");
q("UPDATE aux_ebaystorenumbers SET Category='Historical Topographic Maps', StartLetter='A', EndLetter='M' WHERE ID=19");
q("UPDATE aux_ebaystorenumbers SET Category='Historical Topographic Maps', StartLetter='N', EndLetter='Z' WHERE ID=20");
q("UPDATE aux_ebaystorenumbers SET Category='Historical Topographic Maps', StartLetter='California', EndLetter='' WHERE ID=18");
q("UPDATE aux_ebaystorenumbers SET Category='Civil War Maps', StartLetter='A', EndLetter='Z' WHERE ID=13");
q("UPDATE aux_ebaystorenumbers SET Category='Old International Maps', StartLetter='A', EndLetter='Z' WHERE ID=17");
q("UPDATE aux_ebaystorenumbers SET Category='Old Mine Maps', StartLetter='A', EndLetter='Z' WHERE ID=16");
q("UPDATE aux_ebaystorenumbers SET Category='Old Panoramic Maps', StartLetter='A', EndLetter='Z' WHERE ID=14");
q("UPDATE aux_ebaystorenumbers SET Category='Old Railroad Maps', StartLetter='A', EndLetter='Z' WHERE ID=15");
q("UPDATE aux_ebaystorenumbers SET Category='Other Items', StartLetter='A', EndLetter='Z' WHERE ID=1");
q("UPDATE aux_ebaystorenumbers SET Category='Revolutionary War Maps', StartLetter='A', EndLetter='Z' WHERE ID=155067016");
q("INSERT INTO aux_ebaystorenumbers SET Category='Other Wars', ID=4548876016, StartLetter='A', EndLetter='Z'");
*/
q("ALTER TABLE aux_ebaystorenumbers DROP PRIMARY KEY");
q("INSERT INTO aux_ebaystorenumbers SET Category='Old Transportation Maps', ID=15, StartLetter='A', EndLetter='Z'");

exit;

$a=(q("SELECT * FROM aux_ebaystorenumbers", O_ARRAY));

?><table>
<?php
foreach($a as $n=>$v){
	if(!$done){
		$done=true;
		?><thead><tr><?php
		foreach($v as $o=>$w){
			?><td><?php echo $o;?></td><?php
		}
		?></tr></thead><?php 
	}
	?><tr><?php
	foreach ($v as $o=>$w){
		?><td><?php echo $w;?></td><?php
	}
	?></tr><?php
}
?>
</table>