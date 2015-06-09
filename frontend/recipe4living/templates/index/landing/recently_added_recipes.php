<style>
.toprecipes{
width: 490px !important;
margin-bottom:10px;
}
.toprecipes div.desc{
width : 130px !important;
}
.toprecipes ul.thumb-list li{
float:left;
margin-right:13px;
}
.toprecipes ul.thumb-list{
/*float: left;
padding-bottom: 15px !important;*/
}
</style>
	<div id="top_recipes" class="toprecipes rounded half fr">
	
		<div class="content">
			
			<h1 style="margin-bottom:0px;">Featured Easy Recipes</h1>
			<div class="text">
			Static text here. Static text here.Static text here.Static text here.Static text here.Static text here.Static text here.Static text here.Static text here.Static text here.Static text here.Static text here.Static text here.Static text here.	
			</div>
			<div class="clear2"></div>
			
			<?php if (empty($recentlyAddedRecipes)) { ?>
			No recenty added recipes to display.
			<?php } else { ?>
			<ul class="thumb-list">
				<?php foreach ($recentlyAddedRecipes as $item) { ?>
				<li>
				
					<div class="im">
						<a href="<?= SITEURL.$item['link']; ?>" title="<?= $item['title']; ?>"><img alt="<?= isset($item['featuredImage']['filename']) ? $item['featured_alt'] : $item['default_alt']; ?>" src="<?= ASSETURL; ?>/itemimages/75/75/3/<?= isset($item['featuredImage']['filename']) ? $item['featuredImage']['filename'] : $item['image']['filename']; ?>" /></a>
					</div>
					
					<div class="desc">
						<h5><a href="<?= SITEURL.$item['link']; ?>" title="<?= $item['title']; ?>"><?= Text::trim($item['title'], 28); ?></a></h5><?php if(strlen($item['title'])<=20) echo "<br>";?>
						<?php /*if ($item['author']) { ?>
						<div class="text-content fl">
							Shared by <a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>"><?= $item['author']['username']; ?></a>
						</div>
						<?php }*/ ?>
						<div class="rating text-content fl">
							<?php include(BLUPATH_TEMPLATES.'/articles/items/rating.php'); ?>
						</div>
					</div>
					
					<div class="clear"></div>
					
				</li>
				<?php } ?>
			</ul>
<a style="float:right;margin-top:5px;margin-bottom:10px;font-size:12px;"  href="<?= SITEURL; ?>/recipes/?sort=date_desc&searchterm_extra=&page=1">Click for more recipes!</a>
			<?php } ?>
		</div>

	</div>
