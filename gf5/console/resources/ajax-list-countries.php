<?php

$appEnv = getenv('AppEnv');

if(!function_exists('config_get')){
    /**
     * config_get: return defined variables for multiple config(.php) files in order called.  File paths must be readable as-is.
     *
     * @created = 2017-07-13
     * @author = Sam Fullman <sam-git@compasspointmedia.com>
     * @param $__files
     * @param array $__config (Note: this position is reserved if needed)
     * @param array $__args
     * @return array
     */
    function config_get($__files, $__config = [], $__args = []){
        /*
         * Example of use:
         * ---------------
         * $files = ['../private/config.php', '../private/qa/config.php'];
         * print_r(config_get($files, [], ['foo'=>'bar']));
         */

        // File input list must be valid
        if(empty($__files) || !is_array($__files)) return $__args;

        // Accept only valid readable files
        foreach($__files as $__n=>$__v){
            unset($__files[$__n]);
            if(!is_readable($__v) || !is_file($__v)) continue;

            // Read the file
            require($__v);
            break;
        }

        // Collect defined vars in config file, or array if none present
        $__working = get_defined_vars();
        foreach(['__files', '__config', '__args', '__n', '__v'] as $__unset) unset($__working[$__unset]);
        $__args = array_merge($__args, $__working);

        return config_get($__files, $__config, $__args);

    }
}

// Get config files by precedence
$config = [ $_SERVER['DOCUMENT_ROOT'] . '/../private/config.php' ];
if($appEnv){
    $config[] = str_replace('/private/config.php', '/private/'.$appEnv.'/config.php', $config[0]);
}
$config = config_get($config);
extract($config);

$conn = mysqli_connect($MASTER_HOSTNAME, $MASTER_USERNAME, $MASTER_PASSWORD) or die(mysqli_error($conn));
mysqli_select_db($conn, $MASTER_DATABASE);

if(true || (isset($_GET['getCountriesByLetters']) && isset($_GET['letters']))){
	$letters = $_GET['letters'];
	$letters = preg_replace("/[^a-z0-9 ]/si","",$letters);
	$res = mysql_query("select ID,countryName from ajax_countries where countryName like '".$letters."%'") or die(mysqli_error());
	#echo "1###select ID,countryName from ajax_countries where countryName like '".$letters."%'|";
	while($inf = mysqli_fetch_array($res)){
		echo $inf["ID"]."###".$inf["countryName"]."|";
	}	
}

