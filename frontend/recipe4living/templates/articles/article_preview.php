
	<div id="main-content" class="recipe">

		<div id="column-container">
			
			<div id="panel-center" class="column">

				<?php include(BLUPATH_TEMPLATES.'/articles/details/title.php'); ?>
	
				<div id="post">
 
					<div id="post-details">
						<p class="text-content fl screenonly">
							Date: <?php Template::date(date('Y-m-d')); ?>
						</p>
						
						<div class="clear"></div>
					</div>
			
					<div class="im fl screenonly fieldwrap image" style="width: 200px; height: 200px;">
						<?php if(!empty($image)) { ?>
						<img src="<?= ASSETURL . '/tempimages/200/200/3/' . $image ?>" alt="<?= (!empty($default_alt))?$default_alt:'';?>" />
						<?php } else { ?>
						<img src="<?= ASSETURL; ?>/itemimages/200/200/3/<?= isset($item['image']) ? $item['image']['filename'] : ''; ?>" alt="<?= (!empty($default_alt))?$default_alt:'';?>" />
						<?php } ?>
					</div>
		
					<div class="teaser fl">

						<p class="snippet"><?= $item['teaser']; ?></p>

						<?php if ($item['author']) { ?>
						<p class="text-content">
							Shared by <a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>"><?= $item['author']['username']; ?></a><?php if ($item['author']['location']) { ?>, <br /><?= $item['author']['location']; ?><?php } ?>
						</p>
						<?php } ?>

					</div>

					<div class="clear"></div>

					<div class="entry">
					
						<?php include(BLUPATH_TEMPLATES.'/articles/details/body.php'); ?>
						
					</div>
					
					<form id="form_article_submit" name="form_article_submit" action="#" method="post">
						<input type="hidden" name="task" value="<?= $previewTask; ?>" />
						<div class="fieldwrap">
							<button name="back" class="button-lg fl" type="submit" value="go back" style="margin-right: 10px"><span>Go back</span></button>
						</div>
					</form>
					<form id="form_article_save" name="form_article_save" action="<?= SITEURL . (isset($submitUrl) ? $submitUrl : '/articles/share') ?>" method="post">
						<input type="hidden" name="task" value="<?= $submitTask; ?>" />
						<div class="fieldwrap">
							<button name="save" class="button-lg fl" type="submit" value="save"><span>Save your article</span></button>
						</div>
					</form>
					
					<div class="clear"></div>
					
				</div>
			</div>

			<div id="panel-left" class="column screenonly">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column screenonly">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

				<?php // $this->landing_featured_question(); ?>
				
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
			</div>
			
			<div class="clear"></div>
		</div>

	</div>
