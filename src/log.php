<?php
require('include.php');

//if they are trying to log, check user|pass combo
if ((isset($_POST['username'])) && (isset($_POST['password']))) {

	db_connect();

	//check to see if credentials are right

	$username = mysql_real_escape_string($_POST['username']);
	$password = mysql_real_escape_string(sha1($_POST['password']));

	$result = query("SELECT ID, Username, UserLevel, Style, DisplayOrder FROM userlist WHERE Username = '{$username}' AND Password = '{$password}' LIMIT 1");
	if (mysql_num_rows($result)) {
		$account_data = mysql_fetch_assoc($result);
		session_start();
		$_SESSION['logged'] = true;
		$_SESSION['username'] = $account_data['Username'];
		$_SESSION['userlevel'] = $account_data['UserLevel'];
		$_SESSION['style'] = $account_data['Style'];
		$_temp_order_data = explode(',', $account_data['DisplayOrder']);
		$_SESSION['order'] = array(
			'topics_per_page' => $_temp_order_data[0],
			'topic_sort' => $_temp_order_data[1],	
			'messages_per_page' => $_temp_order_data[2],
			'message_sort' => $_temp_order_data[3]);	
		$_SESSION['topics'] = $account_data['TopicsPerPage'];
		$_SESSION['messages'] = $account_data['MessagesPerPage'];
		$_SESSION['userid'] = $account_data['ID'];
	} else {
		die('login failed');// they failed, redirect them somewhere helpful
	}

}
//logging out
if (isset($_GET['logout'])) {

	//kill global data
	$_SESSION = array();

	//kill cookie
	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time()-42000, '/');
	}

	//baleet session
	session_destroy();
}

//get last page address (or server home if there was no last address
if (isset($_SERVER['HTTP_REFERER'])) {
	$old = $_SERVER['HTTP_REFERER'];
} else {
	$old = "http://{$_SERVER['HTTP_HOST']}";
}

//refer them back to last page
header("Location: $old");
header("Expires: Mon, 10 Jul 2000 05:00:00 GMT");

?>