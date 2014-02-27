<?php
require('include.php');
$logged = login();
db_connect();

$category = get_num('cat');

header_();
?>
<div id="content">
<h2>Board List &gt; <?php

$result = query("SELECT Name FROM categories WHERE ID = $category");
if (mysql_num_rows($result)) {
	$cat_info = mysql_fetch_assoc($result);
	echo $cat_info['Name'];
} else {
	echo 'Unknown';
}

?></h2>
<table summary="This is the list of boards.">
<colgroup><col id="col-1"><col id="col-2"><col id="col-3"><col id="col-4"></colgroup>
<thead><tr><th>Board Name</th><th>Topics</th><th>Messages</th><th>Last Post</th></tr></thead>
<tbody>
<?php

$result = query("SELECT ID, Name, Description, SplitType FROM boardlist WHERE Category = $category");
if (mysql_num_rows($result)) { 
	while ($row = mysql_fetch_assoc($result)) {
   
		$topic_find_num_posts = query("SELECT m.PostTime FROM topiclist t, messagelist m WHERE t.BoardID = {$row['ID']} AND m.TopicID = t.ID ORDER BY m.PostTime DESC");
		$row_posts = mysql_num_rows($topic_find_num_posts);
		$row_latest_post_array = mysql_fetch_assoc($topic_find_num_posts);

		$topic_find_num_topics = query("SELECT ID FROM topiclist WHERE BoardID = {$row['ID']}");
		$row_topics = mysql_num_rows($topic_find_num_topics);
		
		
		if ($row['SplitType'] == 1) {
			 echo 
			 '<tr><td><a href="topiclist.php?board=',
			 $row['ID'],
			 '">',
			 htmlspecialchars($row['Name']),
			 '</a><dfn>',
			 htmlspecialchars($row['Description']),
			 '</dfn></td><td colspan="3">Split Listing</td></tr>',
			 "\n";
		} else {
			echo
			'<tr><td><a href="topiclist.php?board=',
			$row['ID'],
			'">',
			htmlspecialchars($row['Name']),
			'</a><dfn>',
			htmlspecialchars($row['Description']),
			'</dfn></td><td>',
			$row_topics,
			'</td><td>',
			$row_posts,
			'</td><td>';
			if (!empty($row_latest_post_array['PostTime'])) {echo date("F j, Y, g:i a", $row_latest_post_array['PostTime']); } else {echo 'n/a'; }
			echo
			'</td></tr>',
			"\n";
		}
	}
} else { echo '<tr><td colspan="4">No boards in specified category.</td></tr>'; }

?>
</tbody>
</table>
</div>
<?php footer_(); ?>