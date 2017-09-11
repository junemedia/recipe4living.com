	
	<div id="main-content">
		<div id="column-container">

			<div id="panel-center" class="column">
				
				<?= Messages::getMessages(); ?>

				<div class="rounded" id="account-details">
					<div class="top"></div>
					<div class="content">
						<div id="account_icon" class="icon fl"></div>
						<h1><?= Template::get('pageTitle'); ?></h1>
						
						<form action="<?= SITEURL.$taskUrl; ?>" method="post" enctype="multipart/form-data"><div>
							
							<p class="text-content"><?= Template::get('pageDescription'); ?></p>
							<dl>
								<dt><label for="title">Title <span class="red-ast">*</span></label></dt>
								<dd><input type="text" class="textinput required" name="title" value="<?= $title; ?>" /></dd>
								
								<dt><label for="description">Description</label></dt>
								<dd><textarea name="description" rows="5" cols="30"><?= $description; ?></textarea></dd>

								<dt><label for="private">Make this cookbook private</label></dt>
                                                                <dd><input type="checkbox" name="private" value="1"<?= $private ? ' checked="checked"' : ''?> /></dd>						
							</dl>
							
							<div class="divider"></div>

							<h3>Upload a Photo</h3>
							<div class="fieldwrap photo">
								<img src="<?= ASSETURL ?>/itemimages/150/150/1/<?= $image; ?>" class="fl" style="margin-right: 20px;" />
								<label for="image">Save with a photo from your computer...</label>
								<input type="file" name="image" id="image" class="file text-content" size="30" />
								<div class="clear"></div>
							</div>

							<div class="divider"></div>
							
							<button name="submit" value="submit" type="submit" class="button-md fl"><span><?= Template::get('pageButtonText'); ?></span></button>
							<input type="hidden" id="queueid" name="queueid" value="<?= $queueId ?>" />

							<div class="clear"></div>
							
						</div></form>
					</div>
					<div class="bot"></div>
				</div>
			</div>

			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column">
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

				<?php $this->_box('reference_guides', array('limit' => 10)); ?>

				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		
		</div>

	</div>
