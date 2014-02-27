<?php
require('include.php');
$logged = login();
db_connect();

$board = get_num('board');
$topic = get_num('topic');
$message = get_num('message');

if (!$logged) { die_('You must be logged in to report messages.'); }

// get message data
$result = query("SELECT ID, Visibility, PosterID FROM messagelist WHERE ID = {$message} AND TopicID = {$topic} LIMIT 1");
if (mysql_num_rows($result)) {
	$message_data = mysql_fetch_assoc($result);
	if (($message_data['Visibility'] != 0) && ($message_data['Visibility'] != 1)) {
		die_('The message has already been deleted.');
	}
	if ($message_data['PosterID'] == $_SESSION['userid']) { die_('...Are you seriously trying to report your own message?'); }
} else { die_('Invalid message.'); }

// check to see if they have already reported
$result = query("SELECT MessageID, Reporter FROM reports WHERE MessageID = {$message} AND Reporter = {$_SESSION['userid']} LIMIT 1");
if (mysql_num_rows($result)) { die_('You have already reported this message. Attempting to mark messages multiple times does not expedite its deletion.', 'Message already reported.'); }

// validate POST data
switch ($_POST['reason']) {
	case 0: $reason = 0; $reason_text = NULL; break;
	case 1: $reason = 1; $reason_text = NULL; break;
	case 2: $reason = 2; $reason_text = NULL; break;
	case 3: $reason = 3; $reason_text = NULL; break;
	case 4: $reason = 4; $reason_text = NULL; break;
	case 5: $reason = 5; $reason_text = NULL; break;
	case 6: $reason = 6; $reason_text = NULL; break;
	case 7: $reason = 7; $reason_text = NULL; break;
	case 8: $reason = 8; $reason_text = NULL; break;
	case 9: $reason = 9; $reason_text = mysql_real_escape_string($_POST['reasontext']); break;
	default: $reason = 9; $reason_text = mysql_real_escape_string('Unknown reason.'); break;
}

// report the message
query("INSERT INTO reports VALUES(NULL,{$message},{$reason},'{$reason_text}',{$_SESSION['userid']},0)");

header_();
?>
<div id="content">
<h2>Message Reported</h2>
<p>
The message has been reported. Thank you for taking the time to make <?php echo $_STATIC['site_title']; ?> a better place.
</p>
<ul>
<li><a href="boardlist.php">Go to board list</a></li>
<li><a href="topiclist.php?board=<?php echo $board; ?>">Go to board</a></li>
<li><a href="messagelist.php?board=<?php echo $board, '&amp;topic=', $topic; ?>">Go to topic</a></li>
</ul>
</div>
<?php footer_(); ?>