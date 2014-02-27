<?php
require('include.php');
$logged = login();
db_connect();


$error = "";
$allowed = false;
$title = "";
$message = "";
$topic = false;
$exists = false;
$posting = false;



if (!isset($_GET['topic'])) { $topic = true; }

if (!$logged) { die_('Posting is restricted to logged in users.'); }

$board_num = get_num('board');
if (!$topic) { $topic_num = get_num('topic'); }


if (!$topic) {

	//check to see if board/topic combo exists
	$result = query("SELECT ID, BoardID, Title, Status FROM topiclist WHERE ID = {$topic_num} AND BoardID = {$board_num} LIMIT 1");
	if (!mysql_num_rows($result)) { 
		$exists = false; 
	} else {
		$exists = true;
		$topic_data = mysql_fetch_assoc($result);
		$topic_name = $topic_data['Title'];
		$topic_status = $topic_data['Status'];
	}
	if (!$exists) { die_('No such topic exists on this board.'); }
}

//check to see if board exists (and get the board name)
$result = query("SELECT ID, Name, PostThreshold FROM boardlist WHERE ID = {$board_num} LIMIT 1");
if (!mysql_num_rows($result)) {
	$exists = false;
} else { 
	$exists = true; 
	$board_data = mysql_fetch_assoc($result);
	$board_name = $board_data['Name'];
	$board_post_threshold = $board_data['PostThreshold'];
}

if ($_SESSION['userlevel'] >= $board_post_threshold) { $allowed = true; }

// now is the time to check for the other allowances
if (!$allowed) { die_('You are not allowed to post here'); }
if (!$exists) { die_('Does not exist.'); }
if (!$topic) { if ($topic_status == 1) { die_('This topic has been closed. Posting is not allowed.'); }}

// if they have reached this point in the script, they are allowed to post.

if (isset($_POST['quickpost'])) { $posting = true; }
if (isset($_POST['posting'])) { $posting = true; } 

// they mean buisiness. we are going to the db

if (isset($_POST['message'])) { $message = $_POST['message']; }
if ($topic) { 
	if (isset($_POST['title'])) { $title = $_POST['title']; }
}


if ($posting) {
	// data validation
	$message = html_entity_decode($message);
	
	if (isset($_POST['quickpost'])) { $message = $_POST['quickpost']; }
	
	if ($topic) {
		$title = html_entity_decode($title);
	}

	if (strlen(trim(strip_tags($message))) < 1) { $error = $error . "Message may not be empty.\n"; }
	if (strlen($message) > 5000) { $error = $error . "Message may not exceed 5000 characters.\n"; }

	if ($topic) {
		if (strlen(trim(strip_tags($title))) < 5) { $error = $error . "Topic title must consist of 5 or more characters.\n"; }
		if (strlen(trim($topic)) > 80) { $error = $error . "Topic title must consist of 80 or less characters.\n"; }
		if (preg_match("/[^A-Za-z0-9 \.\"\$\?!',&]/", "{$title}") == 1) { $error = $error . "Topic title must not contain characters that are not alphanumeric (letters or numbers), or common punctuation.\n"; }
		if (preg_match("/[A-Za-z0-9]/", "{$title}") == 0) { $error = $error . "Topic title must contain at least one alphanumeric (letters or numbers) character.\n"; }
	}

	if ($error == "") { 


function html_prep($input) {
	
	$input = htmlspecialchars($input, ENT_NOQUOTES);
	$input = nl2br($input);
	return $input;
}


		// if they have made it this far, everything checks out, and they are going to get put in the database
		if ($topic) { 
			$board_num = mysql_real_escape_string($board_num);
			$title = mysql_real_escape_string($title);
			$result = query("INSERT INTO topiclist VALUES (NULL, '{$board_num}', '{$title}', 0, 0)");
			$auto_inc = mysql_insert_id();
			$auto_inc = mysql_real_escape_string($auto_inc);
			if (!$result) { die('Failed to create topic.'); }
			$poster_name = mysql_real_escape_string($_SESSION['username']);
			$poster_id = mysql_real_escape_string($_SESSION['userid']);
			$message = mysql_real_escape_string(html_prep($message));
			$result = query("INSERT INTO messagelist VALUES (NULL, '{$auto_inc}', '{$poster_name}', '{$poster_id}', UNIX_TIMESTAMP(NOW()), 0, '{$message}')");
			header_(); echo "<div id=\"content\">\n<h2>Message posted:</h2>\n<ul>\n<li><a href=\"boardlist.php\">Go to board list</a></li>\n<li><a href=\"topiclist.php?board={$board_num}\">Go to board</a></li>\n<li><a href=\"messagelist.php?board={$board_num}&amp;topic={$auto_inc}\">Go to topic</a></li>\n</ul>\n</div>\n"; footer_();
		} else {
			$topic_num = mysql_real_escape_string($topic_num);
			$poster_name = mysql_real_escape_string($_SESSION['username']);
			$poster_id = mysql_real_escape_string($_SESSION['userid']);
			$message = mysql_real_escape_string(html_prep($message));
			$result = query("INSERT INTO messagelist VALUES (NULL, '{$topic_num}', '{$poster_name}', '{$poster_id}', UNIX_TIMESTAMP(NOW()), 0, '{$message}')");
			header_(); echo "<div id=\"content\">\n<h2>Message posted:</h2>\n<ul>\n<li><a href=\"boardlist.php\">Go to board list</a></li>\n<li><a href=\"topiclist.php?board={$board_num}\">Go to board</a></li>\n<li><a href=\"messagelist.php?board={$board_num}&amp;topic={$topic_num}\">Go to topic</a></li>\n</ul>\n</div>\n"; footer_();
		}
	}
}
header_();
?>
<div id="content">
<h2>Posting &gt; <?php echo htmlspecialchars($board_name); if(!$topic) { echo ' &gt; ', htmlspecialchars($topic_name); } ?></h2>
<h3><a href="boardlist.php">Board List</a> | <a href="topiclist.php?board=<?php echo $board_num; ?>">Topic List</a></h3>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>">
<fieldset>
<legend>Message Preview:</legend>
<p>Topic title: <?php if(!$topic) {echo htmlspecialchars($topic_name); } else { echo htmlspecialchars($title); } ?></p>
<div id="messages">
<h3>From: <a href="user.php"><?php echo $_SESSION['username']; ?></a> | Posted: <?php echo date("F j, Y, g:i a", time()); ?> | <a href="#">Message Detail</a></h3>
<p><?php if ($message == "") { echo '&nbsp;'; } else { echo nl2br(htmlspecialchars($message, ENT_QUOTES)); } ?></p>
</div>
<?php if ($topic) { echo '<input name="title" style="display: none;" type="hidden" value="', htmlspecialchars($title, ENT_QUOTES), '" >', "\n"; }
?><input name="message" style="display: none;" type="hidden" value="<?php echo htmlspecialchars($message, ENT_QUOTES); ?>" >
<input name="posting" style="display: none;" type="hidden" value="posting" >
<input type="submit" value="Post"<?php if (empty($message)) {echo ' disabled="disabled"';}?> >
</fieldset>
</form>
<?php if ($error != "") {echo "\n<div id=\"error\"><h3>The follwing errors occured when processing your message.</h3>", nl2br($error), "</div>\n"; } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>">
<fieldset>
<legend>Your Message:</legend>
<?php if($topic) { echo '<label for="title">Topic Title:</label>
<input type="text" size="60" maxlength="80" id="title" name="title" value="', htmlspecialchars($title),'" >', "\n"; }?>
<label for="message">Message text:</label>
<textarea id="message" name="message" cols="60" rows="20"><?php echo htmlspecialchars($message);
if (empty($_POST)) {
	$sig = query("SELECT Signature FROM userlist WHERE Username = '{$_SESSION['username']}' LIMIT 1;");
	if (mysql_num_rows($sig)) { $sig = mysql_fetch_assoc($sig); if (!empty($sig['Signature'])){ echo "\n\n---\n", $sig['Signature']; }} }?></textarea>
<input type="submit" value="Preview" >
</fieldset>
</form>
</div>
<?php
footer_();
?>