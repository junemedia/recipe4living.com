<?php Messages::getMessages(); ?>

<div id="main-content" class="index">
	<div id="column-container">

		<div id="lc" style="overflow: hidden; width: 650px; margin-left: -160px; float: left;">

			<!-- SlideShow -->
			<div>
				<?php $this->_box('slideshow', array('limit' => 7)); ?>
				<div class="homepage conversationalist ad">
					<!-- FM Test STAMP ("Home") 500x250 Zone -->
					<script type='text/javascript' src='http://static.fmpub.net/zone/2560'></script>
				</div>
			</div>

			<!-- Center -->
			<div id="panel-center" class="column">

				<?php $this->_box('middle_featured_recipes', array('limit' => 4,'boxid'=>33)); ?>
				<div class="clear"></div>

				<div id="top_recipes" class="toprecipes rounded half fr">
					<div id="pubexchange_below_content___narrow"></div>
				</div>

			</div>

			<!-- Left -->
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
		</div>

		<!-- Right -->
		<div id="panel-right" class="column">

			<div class="ad">
				<?php $this->_advert('AD_RIGHT1', 'index'); ?>
			</div>

			<?php $this->_box('right_column_featured_recipes', array('limit' => 1,'boxid'=>34)); ?>
			<?php $this->_box('right_column_feature_collection', array('limit' => 3,'boxid'=>35)); ?>
			<?php include(BLUPATH_TEMPLATES.'/polls/polls.php') ?>
			<div class="clear"></div>

			<div class="ad">
				<?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?>
			</div>
		</div>

	</div>
	<div class="clear"></div>
</div>

<?php
	if (Template::get('showPopup')) {

		// I feel so dirty doing this. Eugh.
		Template::startScript();
?>

	/*window.open('http://www.adviceismeaningless.com/dispatch2.asp?home=70-26175X-L1', '_blank', 'width=790,height=480');*/

<?php
		Template::endScript();
	}
?>
