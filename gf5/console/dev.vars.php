<?php
PRINT_R( $_REQUEST);
$a=$_REQUEST['var:var2'];
echo urlencode($a);
for($I=0;$I<=255;$I++){
	$a= CHR($I);
	$b=urlencode(chr($I));
	if($a==$b)echo chr($I).'<br />';
	}
?>