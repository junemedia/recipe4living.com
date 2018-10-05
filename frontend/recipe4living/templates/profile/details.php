
	<div id="main-content" class="profile">

		<div id="column-container">
			<div id="panel-center" class="column">
	
				<div id="profile" class="rounded"><div class="content">

					<?= Messages::getMessages(); ?>
	
					<div id="user-im">
						<a href="<?= ASSETURL; ?>/userimages/600/500/1/<?= $user['image']; ?>" rel="milkbox:userimage" title="<?= $user['fullname']; ?>">
							<img src="<?= ASSETURL; ?>/userimages/100/100/1/<?= $user['image']; ?>" />
						</a>
					</div>

					<div class="header">
						<h1><?= $user['fullname']; ?></h1>
						<div class="text-content">
							<p><strong>Joined:</strong> <?php Template::date($user['joined']); ?></p>
							<p class="user-rank rank<?= $user['ranking']['level']?>"><strong>User ranking:</strong> <?= htmlspecialchars($user['ranking']['name']); ?></p>
						</div>
					</div>

					<div class="clear"></div>
					
					<div class="body">
						
						<div class="block">
							<h2>About <?= $user['fullname']; ?></h2>
							<div class="text-content">
								<?php if(empty($user['about'])) { ?>
								<p><em><?= Template::get('self') ? 'You haven\'t' : 'This user hasn\'t'; ?> entered any information yet!</em></p>
								<?php } else { ?>
								<p><?= htmlspecialchars($user['about']); ?></p>
								<?php } ?>
							</div>
						</div>
						
						<?php if(!empty($user['favouriteFoods'])) { ?>
						<div class="block">
							<h2><?= $user['fullname']; ?>'s Favorite Foods</h2>
							<div class="text-content">
								<p><?= htmlspecialchars($user['favouriteFoods']); ?></p>
							</div>
						</div>
						<?php } ?>

						<div class="block">
							<h2><?= $user['fullname']; ?>'s Recipes</h2>
							<?php if(!empty($articles)) { ?>
							
							<?= $pagination->get('buttons', array(
								'pre' => '<strong class="fl">Pages: </strong>'
							)); ?>
							<div class="clear"></div>
							
							<ul class="thumb-list">
							<?php foreach($articles as $article) { ?>
							
								<li>
									<div class="im">
										<a href="#">
											<img src="<?= ASSETURL; ?>/itemimages/75/75/1/<?= $article['image']['filename']; ?>" alt="<?php if(isset($article['default_alt'])) echo $article['default_alt']; ?>"/>
										</a>
									</div>
									<div class="desc">
										<h5><a href="<?= SITEURL.$article['link']; ?>"><?= $article['title']; ?></a></h5>
										<div class="text-content">
											<p><?= Text::trim($article['teaser'], 200); ?></p>
											<div class="fr">
												<div class="views fl"><?= $article['views']; ?> views</div>
												<div class="chef-hats fl"><?= round($article['ratings']['average'], 2); ?> chef hats</div>
											</div>
										</div>
										
									</div>
									<div class="clear"></div>
								</li>
								<?php } ?>
							</ul>
							<?php } else { ?>
							<p><em><?= Template::get('self') ? 'You haven\'t' : 'This user hasn\'t'; ?> submitted any recipes</em></p>
							<?php } ?>
						</div>
						
						<div class="block">
							<h2>RSS feeds</h2>
							<div class="text-content">
								<ul>
									<li><a href="<?= SITEURL.'/profile/rss/'.urlencode($user['username']).'?channel=recipes' ?>">Recipes</a></li>
									<li><a href="<?= SITEURL.'/profile/rss/'.urlencode($user['username']).'?channel=reviews' ?>">Reviews</a></li>
									<li><a href="<?= SITEURL.'/profile/rss/'.urlencode($user['username']).'?channel=images' ?>">Images</a></li>
									<li><a href="<?= SITEURL.'/profile/rss/'.urlencode($user['username']).'?channel=blogposts' ?>">Blog Posts</a></li>
								</ul>
							</div>
						</div>
						
						<div class="block">
							<h2>Contact</h2>
							<div class="text-content">
								<a class="button-lg fl" href="<?= SITEURL ?>/account/write_message/<?= $user['username'] ?>"><span>Send Message</span></a>
							</div>
						</div>
						
					</div>
				
					<div class="clear"></div>
					
				</div><div class="bot"></div>
				</div>

			</div>
	
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
		
			<div id="panel-right" class="column">
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
	
			<div class="clear"></div>
		</div>
		
	</div>
