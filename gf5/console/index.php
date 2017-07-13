<?php
header('Location: home.php'.($_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:''));
exit;
?>