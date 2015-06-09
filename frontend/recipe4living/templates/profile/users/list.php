	<div id="recipes_listing">
	
		<?= Messages::getMessages(); ?>
		
		<?php if (!empty($users)) { ?>
		<div class="thumb-list">
			<ul>
			<?php
				foreach ($users as $user) {
			?>			
 					<li class="list">
						<div class="im fl">
							<a href="<?= SITEURL; ?>/profile/<?= $user['username']; ?>" rel="milkbox:userimage" title="<?= $user['fullname']; ?>">
								<img src="<?= ASSETURL; ?>/userimages/100/100/1/<?= $user['image']; ?>" />
							</a>
						</div>
						
						<div class="desc" style="width:365px;">
							<h5 class="fl"><a href="<?= SITEURL; ?>/profile/<?= $user['username']; ?>">
							<?= $user['fullname']; ?>
							</a></h5>
							<div class="clear"></div>
							
							<div class="text-content">
								<p>
									<?php if(empty($user['about'])) { ?>
									<em><?= Template::get('self') ? 'You haven\'t' : 'This user hasn\'t'; ?> entered any information yet!</em>
									<?php } else { ?>
									<?= htmlspecialchars($user['about']); ?>
									<?php } ?>
								</p>
							</div>
							<div class="clear"></div>
						</div>
						
						<div class="clear"></div>
					</li> 
			<?php 
				}
			?>
			</ul>
			<div class="clear"></div>
		</div>
		<?php
			if ($pagination) {
				echo $pagination->get('buttons', array(
					'pre' => '<strong class="fl">Pages: </strong>'
				));
			}
		 ?>
		<?php } ?>
		
	</div>
