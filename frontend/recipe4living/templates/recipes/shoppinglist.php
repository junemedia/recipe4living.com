
	<div id="main-content" class="recipes">
		<div id="column-container">
		
			<div id="panel-center" class="column">

	<div id="list-heading" class="rounded"> 
		<div class="content"> 
			<h2>Your Shopping List</h2> 
			<div class="clear"></div> 
		</div> 
	</div> 
<?= Messages::getMessages(); ?>

		<div class="standardform">
		<div class="formholder">
		
<?=$this->shopping_list_items(); ?>
</div>
</div>
</div>

<?php if ($this->_doc->getFormat() != 'print') { ?>
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
			
			<div id="panel-right" class="column">
				<div class="ad"><?php $this->_advert('openx_300x250atf'); ?></div>

				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
				
				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
<?php } ?>
		</div>
	</div>
