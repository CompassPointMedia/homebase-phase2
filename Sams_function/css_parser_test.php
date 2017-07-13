<!DOCTYPE html>
<html>
<head>
	<?php
		require("function_CSS_parser_v200.php");
	?>
</head>
<body>
	<?php
		$f = file("css_code_to_parse.css");
		$fstr = implode("", $f);
		CSS_parser($fstr);
		echo "<br/><br/><br/>";
		echo "<pre>";
		print_r($CSS_parser);
		echo "<br/><br/>----------------------------------------------------------------------<br/><br/>";
		print_r($fstr);
?>
</body>
</html>