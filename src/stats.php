<?php
require('include.php');
$logged = login();
db_connect();

header_();
?>
<div id="content">
<h2>Site Statistics</h2>
<h3>Totals:</h3>
<dl>
<dt>Boards:</dt><dd><?php echo mysql_num_rows(query("SELECT ID FROM boardlist WHERE 1")); ?></dd>
<dt>Topics:</dt><dd><?php echo mysql_num_rows(query("SELECT ID FROM topiclist WHERE 1")); ?></dd>
<dt>Messages:</dt><dd><?php echo mysql_num_rows(query("SELECT ID FROM messagelist WHERE 1")); ?></dd>
<dt>Users:</dt><dd><?php echo mysql_num_rows(query("SELECT ID FROM userlist WHERE 1")); ?></dd>
</dl>
<p>What kind of statistics would you find interesting? <a href="mailto:&#107;&#46;&#106;&#46;&#119;&#105;&#108;&#99;&#111;&#120;&#64;&#103;&#109;&#97;&#105;l&#46;c&#111;&#109;">Send me an e-mail</a> with suggestions.</p>
</div>
<?php footer_(); ?>