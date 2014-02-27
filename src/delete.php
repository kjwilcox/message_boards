<?php
require('include.php');
$logged = login();
db_connect();

$board = get_num('board');
$topic = get_num('topic');
$message = get_num('message');

// get message data
$result = query("SELECT ID, Visibility, PosterID FROM messagelist WHERE ID = {$message} AND TopicID = {$topic} LIMIT 1");
if (mysql_num_rows($result)) {
	$message_data = mysql_fetch_assoc($result);
	if (($message_data['Visibility'] != 0)) {
		die_('The message cannot be deleted.');
	}
	if ($message_data['PosterID'] != $_SESSION['userid']) { die_('You may only delete your own messages.'); }
} else { die_('Invalid message.'); }

// determine intent ( delete or close)

$result = query("SELECT MIN(ID) FROM messagelist WHERE TopicID = {$topic}");
if (mysql_num_rows($result)) {
	$row = mysql_fetch_assoc($result);
	if ($message == $row['MIN(ID)']) {
		$first_post = true;
	} else {
		$first_post = false;
	}
} else { die_('Invalid message.'); }

if (!$first_post) {
	// delete the message
	query("UPDATE messagelist SET Visibility = 1 WHERE ID = {$message} LIMIT 1");
	
} else {
	// close the topic
	query("UPDATE topiclist SET Status = 1 WHERE ID = {$topic} LIMIT 1");
}



header_();
?>
<div id="content">
<h2>Message Reported</h2>
<p>
<?php
if ($first_post) { echo 'Your topic has been closed'; } else { echo 'Your message has been deleted.'; }
?>
</p>
<ul>
<li><a href="boardlist.php">Go to board list</a></li>
<li><a href="topiclist.php?board=<?php echo $board; ?>">Go to board</a></li>
<li><a href="messagelist.php?board=<?php echo $board, '&amp;topic=', $topic; ?>">Go to topic</a></li>
</ul>
</div>
<?php footer_(); ?>