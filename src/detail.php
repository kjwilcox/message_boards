<?php

include('include.php');
$logged = login();
db_connect();

$board = get_num('board');
$topic = get_num('topic');
$message = get_num('message');

$my_message = false;
$first_post = false;

//check to see if board/topic combo exists
$result = query("SELECT ID, BoardID, Title FROM topiclist WHERE ID = $topic AND BoardID = $board LIMIT 1");
if (!mysql_num_rows($result)) 
{ $exists = false; } else { 
$exists = true; 
$topic_data = mysql_fetch_assoc($result);
$topic_title = $topic_data['Title'];
}

// get board name
$result = query("SELECT Name, ViewThreshold FROM boardlist WHERE ID = {$board} LIMIT 1");
if (mysql_num_rows($result)) {
$board_data = mysql_fetch_assoc($result);
$board_name = $board_data['Name'];
$board_view_threshold = $board_data['ViewThreshold'];
} else { $board_name = "";}

if ($board_view_threshold <= $_SESSION['userlevel']) { $allowed = true;} else { $allowed = false; }
if (!$allowed) {die_('You must be level ' . $board_view_threshold . ' to view messages on this board.', 'Access denied.'); }


header_();
?>
<div id="content">
<h2>Message detail &gt; <?php echo htmlspecialchars($board_name), ' &gt; ', htmlspecialchars($topic_title); ?></h2>
<h3><a href="boardlist.php">Board List</a> | <a href="topiclist.php?board=<?php echo $board; ?>">Topic List</a><?php if ($logged) { echo '| <a href="post.php?board=', $board, '&amp;topic=', $topic, '">Post Message</a>'; } ?></h3>
<div id="messages">
<?php 


//pull out the appropriate message

if ($exists) {
	$result = query("SELECT ID, PosterName, PosterID, PostTime, MessageText, Visibility FROM messagelist WHERE ID = {$message} LIMIT 1");
	if (mysql_num_rows($result)) { 
		$row = mysql_fetch_assoc($result);
			echo
			'<h3>',
			"From: <a href=\"user.php?board={$board}&amp;topic={$topic}&amp;user={$row['PosterID']}\">",
			$row['PosterName'],
			"</a> | Posted: ",
			date("F j, Y, g:i a", $row['PostTime']),
			" | <a href=\"detail.php?board={$board}&amp;topic={$topic}&amp;message={$row['ID']}\">",
			'Message Detail</a>',
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
		
		if ($row['PosterID'] == $_SESSION['userid']) { $my_message = true; }
		
	} else { echo 'Message does not exist.'; }
} else { echo 'Message does not exist.'; }


?>

</div>
<?php 

if ($logged) {
	if (($row['Visibility'] == 0) || ($row['Visibility'] == 1)) {
		if (!$my_message) {
		// not your message, able to report it

?>
<form id="moderation" method="post" action="report.php?board=<?php echo $board; ?>&amp;topic=<?php echo $topic; ?>&amp;message=<?php echo $message; ?>">
<fieldset>
<legend>Report this message:</legend>
<ul>
<li><input type="radio" name="reason" value="0" id="r0"><label for="r0"> Offensive:</label> Sexually explicit/racist/hate speech and threats.</li>
<li><input type="radio" name="reason" value="1" id="r1"><label for="r1"> Censor Bypassing:</label> Not completely obscuring offensive/banned words.</li>
<li><input type="radio" name="reason" value="2" id="r2"><label for="r2"> Trolling:</label> Intending solely to annoy and/or offend other posters.</li>
<li><input type="radio" name="reason" value="3" id="r3"><label for="r3"> Flaming:</label> Clear insults of other board users.</li>
<li><input type="radio" name="reason" value="4" id="r4"><label for="r4"> Advertising:</label> Advertising Web Sites, Other Boards, For Sale/Rent/Trade.</li>
<li><input type="radio" name="reason" value="5" id="r5"><label for="r5"> Illegal Activities:</label> ROM/Warez/MP3/Cracks Begging and/or Providing.</li>
<li><input type="radio" name="reason" value="6" id="r6"><label for="r6"> Spoiler with no Warning:</label> Revealing critical plot details with no warning.</li>
<li><input type="radio" name="reason" value="7" id="r7"><label for="r7"> Disruptive Posting:</label> ALL CAPS, large blank posts, multiple hard-to-read posts, mass bumping, etc.</li>
<li><input type="radio" name="reason" value="8" id="r8"><label for="r8"> Off-Topic Posting:</label> Topics outside of the board description.</li>
<li><input type="radio" name="reason" value="9" id="r9"><label for="r9"> Other:</label> Fill in reason here: <input type="text" size="30" maxlength="100" name="reasontext"></li>
</ul>
<input type="submit" name="submit" value="Report Message">
</fieldset>
</form>
<?php

		} else {
		// is your message,  can delete or close it
			$result = query("SELECT MIN(ID) FROM messagelist WHERE TopicID = {$topic}");
			if (mysql_num_rows($result)) {
				$row = mysql_fetch_assoc($result);
				if ($message == $row['MIN(ID)']) {
					$first_post = true;
				} else {
					$first_post = false;
				}
			}


			if ($first_post) {
?>
<form id="deletion" method="post" action="delete.php?board=<?php echo $board; ?>&amp;topic=<?php echo $topic; ?>&amp;message=<?php echo $message; ?>">
<fieldset>
<legend>Close your topic:</legend>
<p>You may close your topic if no one has posted in it for ten minutes.</p>
<input type="hidden" name="messageid" value="<?php echo $message; ?>">
<input type="submit" name="submit" value="Close Topic">
</fieldset>
</form>
<?php
			} else {
?>
<form id="deletion" method="post" action="delete.php?board=<?php echo $board; ?>&amp;topic=<?php echo $topic; ?>&amp;message=<?php echo $message; ?>">
<fieldset>
<legend>Delete your message:</legend>
<p>You may delete your message from this topic.</p>
<input type="hidden" name="messageid" value="<?php echo $message; ?>">
<input type="submit" name="submit" value="Delete Message">
</fieldset>
</form>
<?php
			}
		}
	}
}

?>
</div>
<?php
footer_(); 
?>