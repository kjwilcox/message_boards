<?php

// starts the script timer
$script_timer = array_sum(explode(' ', microtime()));


####################################
### Do not modify above this box ###
####################################

// these are your site settings
$_STATIC = array(
	'site_title'		=>		'Kwilco.net',				// the name of your site, will appear on every page
	'title_in_title'	=>		true,						// whether or not the site's title will appear in the page titles
	'default_style'		=>		'css/board.css',			// the default stylesheet for people who have not specified one. should begin with a slash
	'root_dir'			=>		'http://kwilco.net/board/',	// root directory of the messageboard. *must* include the final slash
	'session_regen'		=>		true,						// whether or not to regenerate the session ID on every page load (prevents session fixation/hi-jacking, slight performance loss/ bandwidth increase) requires PHP 5.1.0 +
	'gzip'				=>		true,						// whether or not to use gzip compression. saves bandwidth, lowers performance, increases required CPU time
	'db_name'			=>		'kwilco_mb',				// database name
	'db_path'			=>		'localhost',				// database path
	'db_user'			=>		'kwilco_peon',				// database username
	'db_pass'			=>		'g8_K1t>eZ+rB',				// database password
	'quickpost'			=>		true,						// whether or not to activate quickpost box
	'debug'				=>		true						// if debug is active, PHP error reporting will be set to output strict errors. DO NOT LEAVE ON.
	);


####################################
### Do not modify below this box ###
####################################


// turns on error reporting
if ($_STATIC['debug']) {
	error_reporting(E_ALL);
}

$abandon_output = false;

function gzip($par1,$par2) {
	global $abandon_output;
	if ($abandon_output) { return ''; }
	return ob_gzhandler($par1,$par2);
}

// starts the output buffer
if (ob_get_level() == 0) {
	if ($_STATIC['gzip']) {
		ob_start('gzip');
	} else {
		ob_start();
	}
}


// strips out the useless carriage returns from POST data
if (!empty($_POST)) {
	foreach($_POST as $key => $val) {
		if (get_magic_quotes_gpc()) {
			$_POST[$key] = stripslashes($val);
		}
		$_POST[$key] = str_replace(array("\r\n", "\r"), "\n", $_POST[$key]);
	}
}


// self explanitory...
$total_mysql_queries = 0;


// a more reliable is_numeric
function is_num($number) {
	 if (preg_match('/^[0-9]+$/', $number)) { return true;} else { return false; }
}


// grabs a GET number, cleans it up  ** DOES NOT NEED TO BE ESCAPED FOR DATABASE USE **
function get_num($variable) {
	if (!isset($_GET[$variable])) { if ($variable == 'page') {return 1;} return 0; }
	if (!is_num($_GET[$variable])) { if ($variable == 'page') {return 1;} return 0; }
	return $_GET[$variable];
}


// prints a pretty error message
function die_($err_text, $err_reason = '') {
global $_STATIC;
global $abandon_output;
$abandon_output = true;
ob_end_clean();
header_();
echo '<div id="fatal-error">
<h2>Error: ',
$err_reason,
'</h2>
<p>',
$err_text,
'</p>
<p><a href="javascript:history.back()">Go back.</a>
<noscript><br><a href="';
if (empty($_SERVER['HTTP_REFERER'])) {
	echo htmlspecialchars($_STATIC['root_dir']);
} else {
	echo htmlspecialchars($_SERVER['HTTP_REFERER']);
}
echo
'">Go back (no javascript).</a></noscript></p>
</div>
</body>
</html>';
die;
}


// gets the login data, returns login state
function login() {
	global $_STATIC;
	session_start();
	if ($_STATIC['session_regen']) { session_regenerate_id(true); }
	if (!isset($_SESSION['logged'])) {
		$_SESSION['logged'] = false;
		$_SESSION['username'] = 'NULL';
		$_SESSION['userlevel'] = 0;
		$_SESSION['style'] = $_STATIC['default_style'];
		$_SESSION['topics'] = 15;
		$_SESSION['messages'] = 10;
		$_SESSION['userid'] = 'NULL';
		$_SESSION['order'] = array(
		'topics_per_page' => 10,
		'topic_sort' => 'DESC',
		'messages_per_page' => 10,
		'message_sort' => 'ASC');
	}

	if ($_SESSION['logged'] === true) {
		return true;
	} else {
		return false;
	}
}


// connects to the database, ends script if connection fails
function db_connect() {
	global $_STATIC;
	$db_resource = mysql_connect($_STATIC['db_path'], $_STATIC['db_user'], $_STATIC['db_pass']);
	if (!$db_resource) { die_('Could not connect to MySQL.'); }
	$select_database = mysql_select_db($_STATIC['db_name']);
	if (!$select_database) { die_('Could not select database:' . $_STATIC['db_name']); }
}


// runs an SQL query
function query($sql) {
	$function_result = mysql_query($sql);
	if (!$function_result) {
		die_('Query failed: ' . $sql, mysql_error());
		return false;
	} else {
		global $total_mysql_queries;
		$total_mysql_queries++;
		return $function_result;
	}
}


// new consolidated queries












// prints the page heading
function header_() {
	global $_STATIC;
	global $logged;

header("Content-Type: text/html; charset= UTF-8");
echo 
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en-US">
<head>
<title>';
//if (($_STATIC['debug']) && ($_STATIC['session_regen'])) { echo session_id(), ' - '; }
if ($_STATIC['title_in_title']) {echo $_STATIC['site_title'], ' - '; }
echo
'Board List</title>
<base href="', $_STATIC['root_dir'], '">
<!--[if lt IE 7]><script src="ie7/ie7-standard-p.js" type="text/javascript"></script><![endif]-->
<link rel="stylesheet" type="text/css" href="', htmlspecialchars($_SESSION['style']), '">
<!-- link rel="shortcut icon" href="favicon.ico" type="image/png" -->
</head>
<body>
<div id="top">
<h1><a href="', $_STATIC['root_dir'], '">', $_STATIC['site_title'], '</a></h1>
<ul id="priNav">
<li><a href="boardlist.php" accesskey="l">Board <em>L</em>ist</a></li>
<li><a href="stats.php" accesskey="s">Site <em>S</em>tatistics</a></li>
<li><a href="help.php" accesskey="h"><em>H</em>elp Files</a></li>
<li><a href="todo.php" accesskey="t"><em>T</em>o Do List</a></li>
';
if (!$logged) {
echo '<li><a href="register.php" accesskey="r"><em>R</em>egister</a></li>', "\n";
}
if ($_SESSION['userlevel'] >= 9) {
echo '<li><a href="queue.php" accesskey="m"><em>M</em>oderation Queue</a></li>', "\n";
}
if ($_SESSION['userlevel'] == 10) {
echo '<li><a href="admin.php" accesskey="a"><em>A</em>dmin Panel</a></li>', "\n";
}
echo
'</ul>
<div id="login">';
if ($logged){
echo
'<p>Logged in as (', $_SESSION['userlevel'], ')<strong>', $_SESSION['username'], '</strong>.</p>
<p><a href="preferences.php">Preferences</a> | <a href="log.php?logout">Logout</a></p>
';
} else {
echo
'<form method="post" action="log.php">
<p><input id="username" type="text" name="username"> <input id="password" type="password" name="password"></p>
<p><input id="remember" name="remember" type="checkbox"><label for="remember"> Remember me</label> <input id="loginsubmit" type="submit" value="Log In"></p>
</form>
';
}
echo
'</div>
</div>
';
}


// prints the page footer
function footer_($flush = true) {
	global $script_timer;
	global $total_mysql_queries;
echo
'<div id="footer">
<p>Powered by PhalanxBB &copy;2006 Kyle Wilcox.</p>
<p>Page generation time: ~', (1000 * round(array_sum(explode(' ', microtime())) - $script_timer, 3)), ' milliseconds. Database queries: ', $total_mysql_queries, '.</p>
</div>
</body>
</html>';

if ($flush) {
ob_end_flush();
die;
}

}





?>