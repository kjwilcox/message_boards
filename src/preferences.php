<?php
require('include.php');
$logged = login();
db_connect();


if (!$logged) {	die_('Must be logged in to edit settings.'); }

if (!empty($_POST)) { $posting = true; } else { $posting = false; }

if ($posting) {
$input = array();
$input['email'] = mysql_real_escape_string($_POST['email']);
switch($_POST['topics_per_page']) {
	case 10: $input['topics_per_page'] = 10; break;
	case 15: $input['topics_per_page'] = 15; break;
	case 20: $input['topics_per_page'] = 20; break;
	case 30: $input['topics_per_page'] = 30; break;
	case 50: $input['topics_per_page'] = 50; break;
	default: $input['topics_per_page'] = 10;
}

switch($_POST['messages_per_page']) {
	case 10: $input['messages_per_page'] = 10; break;
	case 15: $input['messages_per_page'] = 15; break;
	case 20: $input['messages_per_page'] = 20; break;
	case 30: $input['messages_per_page'] = 30; break;
	case 50: $input['messages_per_page'] = 50; break;
	default: $input['messages_per_page'] = 10;
}

switch($_POST['topic_sort']) {
	case 'Newest First': $input['topic_sort'] = 'DESC'; break;
	case 'Oldest First': $input['topic_sort'] = 'ASC'; break;
	default: $input['topics_per_page'] = 'DESC';
}

switch($_POST['message_sort']) {
	case 'Newest First': $input['message_sort'] = 'DESC'; break;
	case 'Oldest First': $input['message_sort'] = 'ASC'; break;
	default: $input['topics_per_page'] = 'ASC';
}

$input['signature'] = mysql_real_escape_string(substr($_POST['signature'], 0, 255));
$input['profile'] = mysql_real_escape_string(substr($_POST['profile'], 0, 255));

$result = query("UPDATE userlist SET Email = '{$input['email']}', DisplayOrder = '{$input['topics_per_page']},{$input['topic_sort']},{$input['messages_per_page']},{$input['message_sort']}', Signature  = '{$input['signature']}', Profile = '{$input['profile']}' WHERE Username = '{$_SESSION['username']}' LIMIT 1");

	
}

$result = query("SELECT Email, Style, DisplayOrder, Signature, Profile FROM userlist WHERE Username = '{$_SESSION['username']}' LIMIT 1");
if (mysql_num_rows($result)) {
	$account_data = mysql_fetch_assoc($result);
	$_temp_order_data = explode(',', $account_data['DisplayOrder']);
	$account_data['order'] = array(
		'topics_per_page' => $_temp_order_data[0],
		'topic_sort' => $_temp_order_data[1],	
		'messages_per_page' => $_temp_order_data[2],
		'message_sort' => $_temp_order_data[3]);	
}


header_();
?>
<div id="content">
<h2>User Profile Management &gt; <?php echo $_SESSION['username']; ?></h2>
<div id="profile">
<p>You must log out before the requested changes take effect.</p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>">
<!--Style:<select name="style">
<?php


/*
<option<?php if ($account_data['order']['topics_per_page'] == 10) { echo ' selected="selected"'; } ?>>10</option>
<option<?php if ($account_data['order']['topics_per_page'] == 15) { echo ' selected="selected"'; } ?>>15</option>
<option<?php if ($account_data['order']['topics_per_page'] == 20) { echo ' selected="selected"'; } ?>>20</option>
<option<?php if ($account_data['order']['topics_per_page'] == 30) { echo ' selected="selected"'; } ?>>30</option>
<option<?php if ($account_data['order']['topics_per_page'] == 50) { echo ' selected="selected"'; } ?>>50</option>
*/


?>
</select-->
Email:<input type="text" name="email" value="<?php echo $account_data['Email']; ?>"/>
Topics per page:<select name="topics_per_page">
<option<?php if ($account_data['order']['topics_per_page'] == 10) { echo ' selected="selected"'; } ?>>10</option>
<option<?php if ($account_data['order']['topics_per_page'] == 15) { echo ' selected="selected"'; } ?>>15</option>
<option<?php if ($account_data['order']['topics_per_page'] == 20) { echo ' selected="selected"'; } ?>>20</option>
<option<?php if ($account_data['order']['topics_per_page'] == 30) { echo ' selected="selected"'; } ?>>30</option>
<option<?php if ($account_data['order']['topics_per_page'] == 50) { echo ' selected="selected"'; } ?>>50</option>
</select>
Topic order:<select name="topic_sort">
<option<?php if ($account_data['order']['topic_sort'] == 'DESC') { echo ' selected="selected"'; } ?>>Newest First</option>
<option<?php if ($account_data['order']['topic_sort'] == 'ASC') { echo ' selected="selected"'; } ?>>Oldest First</option>
</select>
Messages per page:<select name="messages_per_page">
<option<?php if ($account_data['order']['messages_per_page'] == 10) { echo ' selected="selected"'; } ?>>10</option>
<option<?php if ($account_data['order']['messages_per_page'] == 15) { echo ' selected="selected"'; } ?>>15</option>
<option<?php if ($account_data['order']['messages_per_page'] == 20) { echo ' selected="selected"'; } ?>>20</option>
<option<?php if ($account_data['order']['messages_per_page'] == 30) { echo ' selected="selected"'; } ?>>30</option>
<option<?php if ($account_data['order']['messages_per_page'] == 50) { echo ' selected="selected"'; } ?>>50</option>
</select>
Topic order:<select name="message_sort">
<option<?php if ($account_data['order']['message_sort'] == 'DESC') { echo ' selected="selected"'; } ?>>Newest First</option>
<option<?php if ($account_data['order']['message_sort'] == 'ASC') { echo ' selected="selected"'; } ?>>Oldest First</option>
</select>
Signature:<textarea name="signature" rows="3" cols="60"><?php echo htmlspecialchars($account_data['Signature']); ?></textarea>
Profile:<textarea name="profile" rows="5" cols="60"><?php echo htmlspecialchars($account_data['Profile']); ?></textarea>
<input type="submit" value="Submit" />
</form>
</div>
</div>
<?php footer_(); ?>