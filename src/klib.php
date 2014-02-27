<?php


function dbSafe($input) {
	if (get_magic_quotes_gpc()) {
		$input = stripslashes($input);
	}

	return mysql_real_escape_string($input);
}


function htmlSafe($input) {
	return htmlentities($input, ENT_NOQUOTES);
}


function dbConnect($database='kwilco_mb') {
	mysql_connect('localhost', 'username', 'password')
		or die(mysql_error());
	mysql_select_db($database);
}


function query($query) {
	$result = mysql_query($query);
	if (!$result) { die(mysql_error()); }
	return $result;
}

?>