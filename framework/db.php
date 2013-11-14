<?php
	$db_host = "localhost";

	$db_name = "jdinov_cyspell";

	$db_username = "jdinov_cyspell";

	$db_pass = "dontforget";
$connection = mysql_connect($db_host, $db_username, $db_pass) or die(mysql_error());
$database = mysql_select_db($db_name) or die(mysql_error());
mysql_query ("set character_set_results='utf8'"); 

?>