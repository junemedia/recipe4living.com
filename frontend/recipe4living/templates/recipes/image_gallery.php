
	<div id="main-content" class="recipe">

		<div id="column-container">
			
			<div id="panel-center" class="column">

				<div class="search-nav text-content screenonly">
					
					<a class="back fl" href="<?= $item['link']; ?>">Back to recipe</a>
					
					<div class="clear"></div>
				</div>
				
				<?= Messages::getMessages(); ?>
				
				<div id="recipe-title" class="rounded">
					<div class="content">		
						<h2><?= $galleryImages ? 'Image Gallery' : 'Image Upload' ?>: <?= $item['title']; ?></h2>
					</div>
				</div>
	
				<div id="post">
 
					<div class="im fl screenonly">
						<img alt="<?= (!empty($item['default_alt']))?$item['default_alt']:'';?>" src="<?= ASSETURL; ?>/itemimages/200/200/3/<?= $item['image']['filename']; ?>" />
					</div>
					
					<form method="post" enctype="multipart/form-data">
						<div class="fieldwrap image">
							<?php if($item['image']['filename']) { ?>
							<label for="form_imgfile">Submit another picture of this recipe:</label>
							<?php } else { ?>
							<label for="form_imgfile">Submit a picture of this recipe:</label>
							<?php } ?>
							<input type="file" name="gallery_image" id="gallery_image" class="file text-content" size="25" />
							<button class="button-lg" type="submit"><span>Upload</span></button>
							<div class="clear"></div>
						</div>
						<input type="hidden" name="task" value="upload_gallery_image" />
					</form>
					
					<div class="clear"></div>
					
					<?php if($galleryImages) { ?>
					
						<?php if(Template::get('adminPrivileges')) { ?>
						<form method="post">
						<?php } ?>
							
							<ul style="width: 450px; float: left;">
							<?php foreach($galleryImages as $galleryImage) { ?>
								<li style="float: left; margin: 6px;">
									<?php if(Template::get('adminPrivileges')) { ?>
									<label for="i-<?= urlencode($galleryImage['filename']) ?>">
									<?php } ?>
										<img alt="<?= (!empty($item['default_alt']))?$item['default_alt']:'';?>" src="<?= ASSETURL; ?>/itemimages/100/100/3/<?= $galleryImage['filename'] ?>" />
										<?php if(Template::get('adminPrivileges')) { ?>
										<div style="text-align: center;">
											<input id="i-<?= urlencode($galleryImage['filename']) ?>" type="radio" name="default_image" value="<?= $galleryImage['filename'] ?>"<?= $galleryImage['filename']==$item['image']['filename'] ? ' checked="checked"' : '' ?> />
										</div>
										<div style="text-align: center;">
											<a href="<?= $deleteImageLink.'?type=gallery&amp;file='.urlencode($galleryImage['filename']) ?>" onclick="if(!confirm('Are you sure you want to delete this image?')) return false">Delete</a>
										</div>
										<?php } ?>
									<?php if(Template::get('adminPrivileges')) { ?>
									</label>
									<?php } ?>
								</li>
							<?php } ?>
							</ul>
							<div class="clear"></div>
							
							<?= $pagination->get('buttons', array(
								'pre' => '<strong class="fl">Pages: </strong>'
							)); ?>
							
						<?php if(Template::get('adminPrivileges')) { ?>
							<div class="clear"></div>
							<button class="button-lg fr" type="submit"><span>Set as Default</span></button>
							<input type="hidden" name="task" value="set_default_image" />
						</form>
						<?php } ?>
					
					<?php } ?>
					
					<div class="clear"></div>
					
				</div>
				
			</div>

			<div id="panel-left" class="column screenonly">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column screenonly">
				<?php 
					if (DEBUG || STAGING) {
//						$this->nutrition(true); 
					}
				?>
				
				<?php if (DEBUG || STAGING) { ?>
				<div id="calculator" class="standardform rounded">
					<div class="formholder content">
						<?php $this->calculator(); ?>
					</div>
				</div>
				<?php } ?>
				
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
			</div>
			
			<div class="clear"></div>
		</div>

	</div>
