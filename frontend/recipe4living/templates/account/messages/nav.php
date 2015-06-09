<?php if($folder != 'inbox') { ?><a href="<?= SITEURL ?>/account/messages?folder=inbox"><?php } 
	else { ?><strong><?php } ?>
	Inbox
	<?php if($folder != 'inbox') { ?></a><?php } 
	else { ?></strong><?php } ?> 
	| 
	<?php if($folder != 'sent') { ?><a href="<?= SITEURL ?>/account/messages?folder=sent"><?php } 
	else { ?><strong><?php } ?>Sent Messages<?php 
	if($folder != 'sent') { ?></a><?php } 
	else { ?></strong><?php } ?>
	
	| 
	<?php if($folder != 'compose') { ?><a href="<?= SITEURL ?>/account/write_message"><?php } 
	else { ?><strong><?php } ?>Compose Message<?php 
	if($folder != 'compose') { ?></a><?php } 
	else { ?></strong><?php } ?>
<div class="clear" style="margin-top: 10px;"></div>
