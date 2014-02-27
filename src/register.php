<?php
require('include.php');
$logged = login();
db_connect();

if ($logged) { die_('You are already registered... Remember?'); }

$error = "";
if (!empty($_POST)) { 
	// check if username is okay

	$result = query("SELECT Username FROM userlist WHERE Username = '" . mysql_real_escape_string($_POST['username']) . "' LIMIT 1;");
	if (mysql_num_rows($result)) { $error = $error . "Username is in use. Please select a different one.\n"; }
	
	if (preg_match("/[A-Za-z0-9\.+_-]+(@)[A-Za-z0-9\.-]+(\.)[A-Za-z]+/", $_POST['email']) == 0) { $error = $error . "Your email address is invalid.\n"; }

	if ($_POST['email'] != $_POST['email2']) {  $error = $error . "Email addresses do not match. Check for typing mistakes.\n"; }
	
	if (strlen($_POST['password']) < 6) { $error = $error . "Your password must be at least six characters.\n"; }
	
	if ($_POST['password'] != $_POST['password2'])  {$error = $error . "Passwords do not match. Retype password in both fields.\n"; }


	// registering
	if  ($error == "") {
		$result = query("INSERT INTO userlist VALUES (NULL, '" . mysql_real_escape_string($_POST['username']) . "', 1, '" . mysql_real_escape_string(sha1($_POST['password'])) . "', '" . mysql_real_escape_string($_POST['email']) . "', 'css/board.css', '10,DESC,10,ASC', '', '');");
		header_(); echo '<div id="content">', "\n<p>", 'Signed up successfully! <a href="boardlist.php">Board List</a></p>', "\n</div>\n"; footer_(); die;
	}
}
header_();	 
?>
<div id="content">
<h2>Registration</h2>
<?php if ($error != "") { echo '<p id="error">The following errors occured:<br>', nl2br($error), '</p>', "\n"; } ?>
<form id="registration" method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>">
Username: <input type="text" name="username" value="<?php if (isset($_POST['username'])) { echo $_POST['username']; } ?>">
Password: <input type="password" name="password">
Confirm Password: <input type="password" name="password2">
E-mail: <input type="text" name="email" value="<?php if (isset($_POST['email'])) { echo $_POST['email']; } ?>">
Confirm E-mail:<input type="text" name="email2" value="<?php if (isset($_POST['email2'])) { echo $_POST['email2']; } ?>">
<input type="submit" value="Register">
</form>
</div>
<?php
footer_();
?>