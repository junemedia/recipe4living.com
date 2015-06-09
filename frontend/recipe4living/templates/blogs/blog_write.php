
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="share-recipe" class="rounded" style="margin-bottom:10px;"><div class="content">

					<h1><?= $pageHeading; ?></h1>
			
					<?= Messages::getMessages(); ?>

					<div id="form-share-recipe" class="standardform"><div class="formholder">
						<form id="form_recipe_submit" name="form_article_submit" action="#" method="post" enctype="multipart/form-data">
						
						<div class="fieldwrap title">
							<label for="title">Title <span class="red-ast">*</span></label>
							<input type="text" class="textinput required" maxlength="255" name="title" id="form_title" value="<?= $title; ?>" />							
						</div>

						<div class="fieldwrap directions">
							<label for="body">Your Blog Post <span class="red-ast">*</span></label>
							<textarea id="form_directions" class="textinput required" name="body" cols="10" rows="20"><?= $body; ?></textarea>
						</div>

						<?php Template::startScript('load'); ?>
							new TinyMCEElement('form_directions', {plugins: ['spellchecker'], document_base_url: '<?= 'http://'.$_SERVER['SERVER_NAME'].FRONTENDSITEURL ?>'});
						<?php Template::endScript(); ?>

						<div class="fieldwrap">
							<button name="submit" class="button-lg fl" type="submit" value="submit" style="margin-left: 10px"><span>Submit your blog post</span></button>
						</div>
						
						<div class="clear"></div>
						
						<input type="hidden" name="task" value="<?= $submitTask; ?>" />
						</form>
						
					</div></div>

				</div></div>

			</div>
	
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
	
				<?php // $this->landing_featured_question(); ?>
				
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>
	
	</div>
