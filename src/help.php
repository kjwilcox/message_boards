<?php
require('include.php');
$logged = login();
header_();
?>
<div id="content">
<h2>Help Files</h2>
<ol id="helpfiles">
	<li>Registration
		<ol>
			<li>How do I register?</li>
			<li>Why do I have to register to post?</li>
			<li>Do I have to pay to register?</li>
		</ol>
	</li>
</ol>
<dl id="helplist">
<dt>How do I register?</dt>
<dd>Click 'Register' on the navigation bar, and fill in the required fields. At this time, a valid email address isn't really nessecary, but you should provide one all the same.</dd>
<dt>Why do I have to register to post?</dt>
<dd>You have to register, so I can keep spammers and trolls away from the boards.</dd>
<dt>Do I have to pay to register?</dt>
<dd>No way.</dd>
</dl>
</div>
<?php footer_(); ?>