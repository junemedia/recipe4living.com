
	<?php if (!empty($reviews)) { ?>
	<div id="comments" class="item_list screenonly">
		<h4><?= $reviewsTitle ?> (<?= $reviewsCount; ?>)</h4>
		<ul>
			<?php $alt = false; foreach ($reviews as $review) { $alt = !$alt; ?>
			<li<?= $alt ? ' class="odd"' : ''; ?>>
				<a href="<?= SITEURL; ?>/profile/<?= $review['author']['username']; ?>" class="img">
					<img src="<?= ASSETURL; ?>/userimages/50/50/1/<?= $review['author']['image']; ?>" width="50" height="50" />
				</a>
				<div class="body text-content">
					<div class="comment"><?= nl2br(Text::enableLinks($review['body'])); ?></div>
					<p class="sub">
						<span class="fr">
							<?php if ($review['reports']['active']) { ?>
							<span class="flag">Reported</span>
							<?php } else { ?>
							<a href="<?= SITEURL.$reportLink; ?>?review=<?= $review['id']; ?>" class="flag">Flag as inappropriate</a>
							<?php } ?>
							
							<?php if (false && $review['canDelete']) { // Not implemented ?>
							&nbsp;&nbsp;<a href="?task=delete_comment&comment=<?= $review['id']; ?>" title="Delete" class="delete">Delete</a>
							<?php } ?>
						</span>
						<span class="comment-by">
							<a href="<?= SITEURL; ?>/profile/<?= $review['author']['username']; ?>"><?= $review['author']['username']; ?></a> &nbsp;|&nbsp; <?php Template::date($review['date']); ?>
						</span>
					</p>
				</div>
				<div class="clear"></div>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>
