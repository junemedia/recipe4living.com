<?php

header("Location:http://r4l.popularliving.com/subctr/unsub/unsub_r4l.php");
exit;

?>
	<div class="popup-content">
		<h2><?= Template::text('title_unsubscribe'); ?></h2>
		<p>We'll be sad to see you go, but if you would like to stop receiving correspondence from us, please enter the email address you signed up with in the field below:</p>

		<?php $this->_unsubscribe(); ?>
	</div>