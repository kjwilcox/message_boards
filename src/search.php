<?php
require('include.php');
$logged = login();
db_connect();

$searching = !empty($_POST);

$board = get_num('board');
$topic = get_num('topic');
$message = get_num('message');

header_();
?>
<div id="content">
<h2>Search</h2>
<form method="post" action="search.php" id="search">
<fieldset>
<legend>Search <?php echo $_STATIC['site_title']; ?></legend>
<label for="searchterm" title="must be at least 3 characters">Search for:</label>
<input id="searchterm" type="text" name="term" value="<?php if (isset($_POST['term'])) { echo $_POST['term']; } ?>">
<label for="searchin">in:</label>
<select id="searchin" name="in">
<option<?php if ($_POST['in'] == 'Topic titles') { echo ' selected="selected"'; } ?>>Topic titles</option>
<option<?php if ($_POST['in'] == 'Messages') { echo ' selected="selected"'; } ?>>Messages</option>
<option<?php if ($_POST['in'] == 'Messages & topic titles') { echo ' selected="selected"'; } ?>>Messages &amp; topic titles</option>
</select>
<label for="searchfrom">from:</label>
<select id="searchfrom" name="from">
<option<?php if ($_POST['from'] == 'All boards') { echo ' selected="selected"'; } ?>>All boards</option>
<option<?php if ($_POST['from'] == 'This board') { echo ' selected="selected"'; } ?>>This board</option>
<option<?php if ($_POST['from'] == 'This topic') { echo ' selected="selected"'; } ?>>This topic</option>
</select>
<input type="submit" value="Search">
</fieldset>
</form>
<?php
if ($searching) {
	
	// determines the appropriate query
	$query = array(); 	
	$term = mysql_real_escape_string($_POST['term']);
	switch($_POST['in']) {
		case 'Topic titles': 
			switch($_POST['from']) {
				case 'All boards': 
				$query['t'] = "SELECT ID, Title FROM topiclist WHERE Title LIKE '{$term}'";
				break;
				case 'This board':
				$query['t'] = "SELECT ID, Title FROM topiclist WHERE BoardID = {$board} AND Title LIKE '{$term}'";
				break;
				default:
				break;
			}
		break;
		case 'Messages':
			switch($_POST['from']) {
				case 'All boards': 
				$query['m'] = "SELECT ID, PosterName, MessageText FROM messagelist WHERE MessageText LIKE '{$term}'";
				break;
				case 'This board':
				$query['m'] = "SELECT m.ID, m.PosterName, m.MessageText FROM messagelist m, topiclist t WHERE t.BoardID = {$board} AND t.ID = m.TopicID AND m.MessageText LIKE '{$term}'";
				break;
				case 'This topic':
				$query['m'] = "SELECT ID, PosterName, MessageText FROM messagelist WHERE TopicID = {$topic} AND MessageText LIKE '{$term}'";
				break;
			}
		break;
		case 'Messages & topic titles':
			case 'Topic titles': 
			switch($_POST['from']) {
				case 'All boards': 
				$query['t'] = "SELECT ID, Title FROM topiclist WHERE Title LIKE '{$term}'";
				$query['m'] = "SELECT ID, PosterName, MessageText FROM messagelist WHERE MessageText LIKE '{$term}'";
				break;
				case 'This board':
				$query['t'] = "SELECT ID, Title FROM topiclist WHERE BoardID = {$board} AND Title LIKE '{$term}'";
				$query['m'] = "SELECT m.ID, m.PosterName, m.MessageText FROM messagelist m, topiclist t WHERE t.BoardID = {$board} AND t.ID = m.TopicID AND m.MessageText LIKE '{$term}'";
				break;
				case 'This topic':
				$query['m'] = "SELECT ID, PosterName, MessageText FROM messagelist WHERE TopicID = {$topic} AND MessageText LIKE '{$term}'";
				break;
			}
		break;
				
	}
}

$query_results = array();

// makes the queries
foreach ($query as $key => $val) {
	$query_results[$key] = query($val);
}

$query_num_results = 0;

// determines the number of total queries
foreach ($query_results as $key => $val) {
	$query_num_results = $query_num_results + mysql_num_rows($val);
}

// echo the number of queries
echo "\n<h3>", $query_num_results, " results.</h3>\n";

foreach ($query_results as $key => $val) {
	echo "\n<table summary=", '"query results"' ,">";
	while($row = mysql_fetch_assoc($val)) {
		echo "\n<tr>";
		foreach ($row as $col => $data) {
			echo '<td>', $data, '</td>';
		}
		echo "</tr>";
	}
	echo "\n</table>";
}

?>
</div>
<?php footer_(); ?>