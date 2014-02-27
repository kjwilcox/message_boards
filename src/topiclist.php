<?php
require('include.php');
$logged = login();
db_connect();



$board = get_num('board');
$page = get_num('page');
$page_start = ($_SESSION['order']['topics_per_page'] * ($page - 1));

//check to see if board exists
$result = query("SELECT ID, Name, SplitType, ViewThreshold, PostThreshold FROM boardlist WHERE ID = $board LIMIT 1");
if (!mysql_num_rows($result)) { 
	$exists = false;
	$board_name = 'Unknown';
	$board_type = 0;
	$board_view_threshold = 0;
	$board_post_threshold = 0;
} else { 
	$exists = true; 
	$board_data = mysql_fetch_assoc($result);
	$board_name = $board_data['Name'];
	$board_type = $board_data['SplitType'];
	$board_view_threshold = $board_data['ViewThreshold'];
	$board_post_threshold = $board_data['PostThreshold'];
}

if ($board_view_threshold <= $_SESSION['userlevel']) { $allowed = true;} else { $allowed = false; }
if (!$allowed) {die_('You must be level ' . $board_view_threshold . ' to view topics on this board.', 'Access denied.'); }

header_();
?>
<div id="content">
<h2>Topic List &gt; <?php echo htmlspecialchars($board_name); ?></h2>
<h3><a href="boardlist.php">Board List</a><?php if (($exists) && ($board_type != 1) && ($board_post_threshold <= $_SESSION['userlevel'])) { echo ' | <a href="post.php?board=', $board, '">Create Topic</a>'; } ?></h3>
<?php



// subforums display code

if ($board_type != 0) { 
	if ($board_type == 1) { echo '<h2>Split List</h2>'; }
	if ($board_type == 2) { echo '<h2>Subforums</h2>'; }
	echo "\n";
	$result = query("SELECT ID, Name, Description, SplitType FROM boardlist WHERE Parent = $board");
	echo '<table summary="This is the list of boards using this board as a parent.">
	<colgroup><col id="col-1"><col id="col-2"><col id="col-3"><col id="col-4"></colgroup>
	<thead><tr><th>Topic</th><th>Author</th><th>Messages</th><th>Last Post</th></tr></thead>
	<tbody>';

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
	} echo "\n</tbody>\n</table>";
}


if ($board_type == 2) { echo "\n", '<h2>Topics</h2>', "\n"; }



// pagination code

if ($board_type != 1) { 
	echo '<p>Page:';
	// check total pages worth on board 
	$result = query("SELECT ID FROM topiclist WHERE BoardID = {$board}");
	$pages_num = mysql_num_rows($result);
	if ($pages_num == 0) { $pages_num = 1; }

	$pages_num = ceil($pages_num / $_SESSION['order']['topics_per_page']);
	for ($p = 1; $p <= $pages_num; $p++) {
		if ($p == $page) { echo "\n", $p; } else {
			echo "\n<a href=\"topiclist.php?board={$board}&amp;page={$p}\">{$p}</a>";
		}
	}
	echo "\n", '</p>', "\n";
}



// topic display code

if ($board_type != 1) { 
	echo '<table summary="This is the list of topics on the board.">', "\n", '<colgroup><col id="col-1"><col id="col-2"><col id="col-3"><col id="col-4"></colgroup>', "\n", '<thead><tr><th>Topic</th><th>Author</th><th>Messages</th><th>Last Post</th></tr></thead>';
	echo "\n", '<tbody>';

	if ($exists) {
		$even = false;
		if ($page == 1) {
			//sticky topics
			$result = query("SELECT t.ID, t.Title, MAX(m.PostTime) AS last_post, t.Status FROM topiclist AS t LEFT JOIN messagelist AS m ON m.TopicID = t.ID WHERE t.BoardID = {$board} AND t.Sticky = 1 GROUP BY m.TopicID ORDER BY t.ID DESC");
			if (mysql_num_rows($result)) {
				while ($row = mysql_fetch_assoc($result)) {
					$even = (!$even);

					$topic_find_info = query("SELECT PosterName, PostTime FROM messagelist WHERE TopicID = {$row['ID']} ORDER BY PostTime");
					$row_creator_array = mysql_fetch_assoc($topic_find_info);
					$row_posts = mysql_num_rows($topic_find_info);

					echo
					"\n<tr class=\"sticky\"",
					"><td><a href=\"messagelist.php?board={$board}&amp;topic=",
					$row['ID'],
					'"><img src="images/sticky.gif" alt="(sticky topic)"> ',
					htmlspecialchars($row['Title']);
					if ($row['Status'] == 1) { echo ' <img src="images/closed.gif" alt="(closed topic)">'; }
					echo
					'</a></td><td>',
					htmlspecialchars($row_creator_array['PosterName']),
					'</td><td>',
					$row_posts,
					'</td><td>',
					date("F j, Y, g:i a", $row['last_post']),
					'</td></tr>';
				}
			}
		}
		//normal topics
		$result = query("SELECT t.ID, t.Title, MAX(m.PostTime) AS last_post, t.Status FROM topiclist AS t LEFT JOIN messagelist AS m ON m.TopicID = t.ID WHERE t.BoardID = {$board} AND t.Sticky = 0 AND t.Status != 2 GROUP BY m.TopicID ORDER BY last_post {$_SESSION['order']['topic_sort']} LIMIT {$page_start}, {$_SESSION['order']['topics_per_page']}");
		if (mysql_num_rows($result)) {
			while ($row = mysql_fetch_assoc($result)) {
				$even = (!$even);

				$topic_find_info = query("SELECT PosterName, PostTime FROM messagelist WHERE TopicID = {$row['ID']} ORDER BY PostTime");
				$row_creator_array = mysql_fetch_assoc($topic_find_info);
				$row_posts = mysql_num_rows($topic_find_info);

				echo
				"\n<tr";
				if (!$even) { echo ' class="odd"'; }
				echo
				"><td><a href=\"messagelist.php?board={$board}&amp;topic=",
				$row['ID'],
				'">',
				htmlspecialchars($row['Title']);
				if ($row['Status'] == 1) { echo ' <img src="images/closed.gif" alt="(closed topic)">'; }
				
				echo
				'</a>',
				'</td><td>',
				htmlspecialchars($row_creator_array['PosterName']),
				'</td><td>',
				$row_posts,
				'</td><td>',
				date("F j, Y, g:i a", $row['last_post']),
				'</td></tr>';
			}
		}
	
	} else { echo '<tr><td colspan="4">Invalid Board ID</td></tr>'; }
	echo "\n", '</tbody>',"\n", '</table>', "\n";
}

echo '</div>', "\n";

footer_();
?>