<?php
require('include.php');
$logged = login();
db_connect();

if ($_SESSION['userlevel'] < 9) { die_('You must be a moderator or administrator to access this page.'); }


$page = get_num('page');
$page_start = ($_SESSION['order']['messages_per_page'] * ($page - 1));


header_();
?>
<div id="content">
<h2>Moderation Queue</h2>
<p>Page:<?php

//check total pages worth of 
$result = query("SELECT ID FROM reports WHERE 1");
$pages_num = mysql_num_rows($result); 
$pages_num = ceil($pages_num / $_SESSION['order']['messages_per_page']);

for ($p = 1; $p <= $pages_num; $p++) {
	if ($p == $page) { echo "\n", $p; } else {
		echo "\n<a href=\"messagelist.php?board={$board}&amp;topic={$topic}&amp;page={$p}\">{$p}</a>";
	}
}
echo "\n",'</p>',"\n";
?>
<table summary="This is the list of boards using this board as a parent." id="queue">
<colgroup><col id="col-1"><col id="col-2"><col id="col-3"></colgroup>
<thead><tr><th>Message ID</th><th>Reason</th><th>Reports</th></tr></thead>
<tbody>
<?php

$result = query("
SELECT MessageID, Reason, ReasonText, COUNT(MessageID) AS NumReports
FROM reports
WHERE Used = 0
GROUP BY MessageID
ORDER BY NumReports DESC
LIMIT {$page_start}, {$_SESSION['order']['messages_per_page']}
");

if (mysql_num_rows($result)) {
	while($row = mysql_fetch_assoc($result)) {
		echo
		'<tr><td><a href="mod.php?message=',
		$row['MessageID'],
		'">',
		$row['MessageID'],
		'</a></td><td>';
		
		switch($row['Reason']) {
			case 0: echo 'Offensive'; break;
			case 1:	echo 'Censor Bypass'; break;
			case 2: echo 'Trolling'; break;
			case 3: echo 'Flaming'; break;
			case 4: echo 'Advertising'; break;
			case 5: echo 'Illegal Activities'; break;
			case 6: echo 'Spoilers'; break;
			case 7: echo 'Disruptive'; break;
			case 8: echo 'Off-topic'; break;
			case 9: echo 'Other: ', htmlspecialchars($row['ReasonText']); break;
		}
		echo
		'</td><td>',
		$row['NumReports'],
		'</td></tr>',
		"\n";
	}
} else {
	echo '<tr><td colspan="3">Moderation queue is empty!</td></tr>';
}

?>
</tbody>
</table>
</div>
<?php footer_(); ?>