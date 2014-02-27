<?php
require('include.php');
$logged = login();
db_connect();

$user = get_num('user');
$board = get_num('board');
$topic = get_num('topic');
$page = get_num('page');


//check to see if board/topic combo exists
$result = query("SELECT ID, BoardID, Title FROM topiclist WHERE ID = $topic AND BoardID = $board LIMIT 1;");
if (!mysql_num_rows($result)) 
{ $valid = false; } else { $valid = true; }

if ($valid) {
	$result = query("SELECT ID, Username, UserLevel, Signature, Profile FROM userlist WHERE ID = $user LIMIT 1;");
	if (mysql_num_rows($result)) { $exists = true; $user_data = mysql_fetch_assoc($result); } else { $exists = false; }
}


header_();
?>
<div id="content">
<h2>User Profile &gt; <?php echo htmlspecialchars($user_data['Username']); ?></h2>
<h3><a href="boardlist.php">Board List</a> | <a href="topiclist.php?board=<?php echo $board; ?>">Topic List</a> | <a href="messagelist.php?board=<?php echo $board, '&amp;topic=', $topic; ?>">Message List</a></h3>
<?php
if ($valid) {
	echo '<dl id="profile">';
	foreach ($user_data as $key => $value) {
		if ($value == '') { $value = '&nbsp;'; }
		echo "\n<dt>", $key, ":</dt>\n<dd>", $value, "</dd>";
	}
	echo "\n</dl>\n";
} else { echo '<p id="profile">User Profile must be linked from an existing topic.</p>'; } 
?>
</div>
<?php footer_(); ?>