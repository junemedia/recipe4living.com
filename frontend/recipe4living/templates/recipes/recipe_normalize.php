
	<div id="main-content" class="recipe">

		<div id="column-container">
			
			<div id="panel-center" class="column">

				<?php include(BLUPATH_TEMPLATES.'/recipes/details/title.php'); ?>
	
				<div id="post">
					<div class="entry">
					
						<?php include(BLUPATH_TEMPLATES.'/recipes/details/ingredients.php'); ?>
						
					</div>
					
					<form id="form_article_submit" name="form_article_submit" action="#" method="post">
						<input type="hidden" name="task" value="<?= $previewTask; ?>" />
						<div class="fieldwrap">
							<button name="back" class="button-lg fl" type="submit" value="go back" style="margin-right: 10px"><span>Go back</span></button>
						</div>
					</form>
					<form id="form_article_save" name="form_article_save" action="<?//= SITEURL . (isset($submitUrl) ? $submitUrl : '/share') ?>" method="post">
						<input type="hidden" name="task" value="<?= $submitTask; ?>" />
						<div class="fieldwrap">
							<button name="preview" class="button-lg fl" type="submit" value="preview"><span>Continue</span></button>
						</div>
					</form>
					
					<div class="clear"></div>
					
				</div>
			</div>

			<div id="panel-left" class="column screenonly">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column screenonly">
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
			</div>
			
			<div class="clear"></div>
		</div>

	</div>
