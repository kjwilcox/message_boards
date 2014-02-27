<?php
require('include.php');
$logged = login();
db_connect();

$board = get_num('board');
$topic = get_num('topic');
$page = get_num('page');

$page_start = ($_SESSION['order']['messages_per_page'] * ($page - 1));


//check to see if board/topic combo exists
$result = query("SELECT ID, BoardID, Title, Status FROM topiclist WHERE ID = $topic AND BoardID = $board LIMIT 1");
if (!mysql_num_rows($result)) 
{ $exists = false; } else { 
$exists = true; 
$topic_data = mysql_fetch_assoc($result);
$topic_title = $topic_data['Title'];
$topic_status = $topic_data['Status'];
}

// get board name
$result = query("SELECT Name, ViewThreshold, PostThreshold FROM boardlist WHERE ID = {$board} LIMIT 1");
if (mysql_num_rows($result)) {
$board_data = mysql_fetch_assoc($result);
$board_name = $board_data['Name'];
$board_view_threshold = $board_data['ViewThreshold'];
$board_post_threshold = $board_data['PostThreshold'];
} else { $board_name = "";}



if ($board_view_threshold <= $_SESSION['userlevel']) { $allowed = true;} else { $allowed = false; }
if (!$allowed) {die_('You must be level ' . $board_view_threshold . ' to view messages on this board.', 'Access denied.'); }


header_();
?>
<div id="content">
<h2>Message list &gt; <?php echo htmlspecialchars($board_name), ' &gt; ', htmlspecialchars($topic_title); ?></h2>
<h3><a href="boardlist.php">Board List</a> | <a href="topiclist.php?board=<?php echo $board; ?>">Topic List</a><?php if (($exists) && ($board_post_threshold <= $_SESSION['userlevel']) && ($topic_status != 1)) { echo ' | <a href="post.php?board=', $board, '&amp;topic=', $topic, '">Post Message</a>'; } ?></h3>
<p>Page:<?php

//check total pages worth of 
$result = query("SELECT ID FROM messagelist WHERE TopicID = $topic");
$pages_num = mysql_num_rows($result); 
$pages_num = ceil($pages_num / $_SESSION['order']['messages_per_page']);

for ($p = 1; $p <= $pages_num; $p++) {
	if ($p == $page) { echo "\n", $p; } else {
		echo "\n<a href=\"messagelist.php?board={$board}&amp;topic={$topic}&amp;page={$p}\">{$p}</a>";
	}
}

?>

</p>
<div id="messages">
<?php 

$message_num = $page_start + 1;

//pull out the appropriate messages to post

if ($exists) {
	$result = query("SELECT ID, PosterName, PosterID, PostTime, MessageText, Visibility FROM messagelist WHERE TopicID = {$topic} ORDER BY ID {$_SESSION['order']['message_sort']} LIMIT {$page_start}, {$_SESSION['order']['messages_per_page']}");
	if (mysql_num_rows($result)) { 
		while ($row = mysql_fetch_assoc($result)) {
			echo
			'<h3 id="m',
			$message_num,
			"\">From: <a href=\"user.php?board={$board}&amp;topic={$topic}&amp;user={$row['PosterID']}\">",
			$row['PosterName'],
			"</a> | Posted: ",
			date("F j, Y, g:i a", $row['PostTime']),
			" | <a href=\"detail.php?board={$board}&amp;topic={$topic}&amp;message={$row['ID']}\">",
			'Message Detail</a> | #',
			$message_num,
			"</h3>\n<p>\n ";
			switch($row['Visibility']) {
				case 0:	echo $row['MessageText']; break;
				case 1:	echo '[This message has been deleted by the original poster.]'; break;
				case 2:	echo '[This message has been deleted by a moderator.]'; break;
				case 3:	echo '[This message has been deleted by an administrator.]'; break;
				default: echo $row['MessageText']; break;
			}
			echo
			"\n</p>\n";
			$message_num++;
		}
	}
} else { echo 'Invalid Topic/Board Combination'; }


?>
</div>
<?php
if (($logged) && ($_STATIC['quickpost'])) {
?>
<form id="quickpost" method="post" action="post.php?board=<?php echo $board; ?>&amp;topic=<?php echo $topic; ?>">
<fieldset>
<legend>Quick Post</legend>
<textarea name="quickpost"></textarea>
<input type="submit" value="Quick Post" name="submit">
</fieldset>
</form>
<?php
}
?>
</div>
<?php footer_(); ?>