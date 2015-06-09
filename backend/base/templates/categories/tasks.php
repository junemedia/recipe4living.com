
	<h2>
		<?php 
			if (!empty($ancestry)) { 
				foreach ($ancestry as $ancestor) {
		?>
		<a href="<?= SITEURL; ?>/categories/<?= $ancestor['slug']; ?>"><?= $ancestor['name']; ?></a> / 
		<?php
				}
			}
			echo $name;
		?>
	</h2>
<style>
.livebtn { display: block; background: #c12c05;  text-decoration: none; color: white; 
text-align: center; padding: 7px; width: 150px; margin: 15px;
}
</style>
	<a href="/oversight/categories/pushchangeslive" class="livebtn" onclick="if(!confirm('Do you really want to push changes live? This can take a very long time, so only do it when you\'re done making changes.')){return false;}">Push changes live</a>
	
	<div id="tasks">
		<h3>Tasks</h3>
		<ul>
			<li class="mkdir">
				<a id="mkdir_link" href="<?= SITEURL; ?>/categories/add_category/<?= $slug; ?>">Add subcategory</a>
				<div id="mkdir_panel">
					<?php $this->add_category(); ?>
				</div>
				
				<?php Template::startScript(); ?>
					
					new PanelSlider('mkdir_link', 'mkdir_panel', {
						hideLink: false
					});
					
				<?php Template::endScript(); ?>
			</li>
			<li class="edit">
				<a id="edit_link" href="<?= SITEURL; ?>/categories/edit_category/<?= $slug; ?>">Edit this category</a>
				<div id="edit_panel">
					<?php $this->edit_category(); ?>
				</div>
				
				<?php Template::startScript(); ?>
					
					new PanelSlider('edit_link', 'edit_panel', {
						hideLink: false
					});
					
				<?php Template::endScript(); ?>
			</li>
			<li class="rmdir">
				<a id="rmdir_link" href="<?= SITEURL; ?>/categories/remove_category/<?= $slug; ?>">Remove this category</a>
				<div id="rmdir_panel">
					<?php $this->remove_category(); ?>
				</div>
				
				<?php Template::startScript(); ?>
					
					new PanelSlider('rmdir_link', 'rmdir_panel', {
						hideLink: false
					});
					
				<?php Template::endScript(); ?>
			</li>
			<?php if(!$category['display']){?>
			<li class="create">
				<a id="pb_link" href="<?= SITEURL; ?>/categories/publish_category/<?= $slug; ?>">Publish this category and its sub categories</a>
				<div id="pb_panel">
					<?php $this->publish_category(); ?>
				</div>
				
				<?php Template::startScript(); ?>
					
					new PanelSlider('pb_link', 'pb_panel', {
						hideLink: false
					});
					
				<?php Template::endScript(); ?>
			</li>
			<?php }else{?>
			<li class="rmdir">
				<a id="unpdir_link" href="<?= SITEURL; ?>/categories/unpublish_category/<?= $slug; ?>">Unpublish this category  and its sub categories</a>
				<div id="unpdir_panel">
					<?php $this->unpublish_category(); ?>
				</div>
				
				<?php Template::startScript(); ?>
					
					new PanelSlider('unpdir_link', 'unpdir_panel', {
						hideLink: false
					});
					
				<?php Template::endScript(); ?>
			</li>
			<?php }?>
			<li class="create">
				<a id="create_link" href="<?= SITEURL; ?>/categories/add_item/<?= $slug; ?>">Add recipe/article</a>
				<div id="create_panel">
					<?php $this->add_item(); ?>
				</div>
				
				<?php Template::startScript(); ?>
					
					new PanelSlider('create_link', 'create_panel', {
						hideLink: false
					});
					
					new ArticleItems($('create_panel'), null, {
						updateTask: 'add_item'
					});
					
				<?php Template::endScript(); ?>
			</li>
			<li class="ls">
				<a href="<?= FRONTENDSITEURL.$link; ?>">View listings on frontend</a>
			</li>
			<li class="flush">
				<a href="?refresh=1">Refresh listings</a>
			</li>
		</ul>
	</div>
	
	<div id="items">
		<?php $this->items(); ?>
	</div>
