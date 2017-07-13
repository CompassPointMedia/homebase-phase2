<?php
if(!function_exists('q'))
require($FUNCTION_ROOT.'/function_q_v130.php');
if(!function_exists('prn'))
require($FUNCTION_ROOT.'/function_prn.php');
if(!function_exists('subkey_sort'))
require($FUNCTION_ROOT.'/function_array_subkey_sort_v300.php');
if(!function_exists('prn'))
require($FUNCTION_ROOT.'/function_xml_read_tags_v134.php');
if(!function_exists('sql_insert_update_generic'))
require($FUNCTION_ROOT.'/function_sql_insert_update_generic_v111.php');
if(!function_exists('sql_autoinc_text'))
require($FUNCTION_ROOT.'/function_sql_autoinc_text_v232.php');
if(!function_exists('array_transpose'))
require($FUNCTION_ROOT.'/function_array_transpose.php');
if(!function_exists('quasi_resource_generic'))
require($FUNCTION_ROOT.'/function_quasi_resource_generic_v201.php');
require_once($FUNCTION_ROOT.'/function_relatebase_dataobjects_settings_v100.php');
if(!function_exists('replace_form_elements'))
require($FUNCTION_ROOT.'/function_replace_form_elements_v100.php');
if(!function_exists('enhanced_mail'))
require($FUNCTION_ROOT.'/function_enhanced_mail_v211.php');
if(!function_exists('navigate'))
require($FUNCTION_ROOT.'/function_navigate_v141a.php');
if(!function_exists('callback'))
require($FUNCTION_ROOT.'/function_callback_v101.php');
if(!function_exists('t'))
require($FUNCTION_ROOT.'/function_t_v112.php');
if(!function_exists('human_height'))
require($FUNCTION_ROOT.'/function_human_height_v100.php');
if(!function_exists('parse_query'))
require($FUNCTION_ROOT.'/function_parse_query_v200.php');
if(!function_exists('get_navstats'))
require($FUNCTION_ROOT.'/function_get_navstats_v110.php');
if(!function_exists('get_contents'))
require($FUNCTION_ROOT.'/function_get_contents_v100.php');
if(!function_exists('get_file_assets'))
require($FUNCTION_ROOT.'/function_get_file_assets_v100.php');
if(!function_exists('parse_name'))
require($FUNCTION_ROOT.'/function_parse_name_v101.php');
if(!function_exists('read_logical'))
require($FUNCTION_ROOT.'/function_is_logical_v100.php');
if(!function_exists('CMSB'))
require_once($FUNCTION_ROOT.'/function_CMSB_v311.php');
require_once($FUNCTION_ROOT.'/function_subsearch_v100.php');
require_once($FUNCTION_ROOT.'/function_tabs_enhanced_v300.php');
require_once($FUNCTION_ROOT.'/function_sql_query_parser_v100.php');
require_once($FUNCTION_ROOT.'/group_tree_functions_v100.php');
require_once($FUNCTION_ROOT.'/group_sE_v100.php');
require_once($FUNCTION_ROOT.'/function_array_to_csv_v200.php');
require_once($FUNCTION_ROOT.'/function_attach_download_v100.php');


function year_trans($x, $thresh=35, $oneMil=true){
	/* year must be between [one Millennium] and 3000 if 4 digits */
	if(preg_match('/^[0-9]{2}$/',$x)) return ($x>=$thresh ? '19' : '20').$x;
	if(preg_match('/^[0-9]{3}$/',$x)){
		if($oneMil) return '';
		return $x;
	}
	if(preg_match('/^[0-9]{4}$/',$x)) return $x;
	//no criteria matches
	return '';
}
function month_trans($x, $mode='int'){
	$months[1]=array('jan','january','01',1);
	$months[2]=array('feb','febuary','02',2,'febr');
	$months[3]=array('mar','march','03',3);
	$months[4]=array('apr','april','04',4);
	$months[5]=array('may','may','05',5);
	$months[6]=array('jun','june','06',6);
	$months[7]=array('jul','july','07',7);
	$months[8]=array('aug','august','08',8);
	$months[9]=array('sep','september','09',9,'sept');
	$months[10]=array('oct','october','10',10);
	$months[11]=array('nov','november','11',11,'novem');
	$months[12]=array('dec','december','12',12,'decem');
	if(!$x)return 0;
	$x=trim(strtolower(str_replace('.','',$x)));
	foreach($months as $key=>$month){
		if(in_array($x,$month)){
			switch(true){
				case $mode=='int': return $key;
				case $mode=='Short': return strtoupper(substr($month[0],0,1)).substr($month[0],-2);
				case $mode=='SHORT': return strtoupper($month[0]);
				case $mode=='short': return $month[0];
				case $mode=='Long': return strtoupper(substr($month[1],0,1)).substr($month[1],1-strlen($month[1]));
				case $mode=='LONG': return strtoupper($month[1]);
				case $mode=='long': return $month[1];
				case $mode=='zerofill': return $month[2];
				default: echo '<strong>unrecognized month translation mode: int, Short, short, SHORT, Long, LONG, long, zerofill</strong>';
			}
		}
	}
}
?>