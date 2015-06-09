
	<div id="main-content" class="static">

		<div id="column-container">
			<div id="panel-center" class="column">
		
				<div id="press" class="rounded"><div class="content">

					<h1>Page not Found</h1>
		
					<div class="text-content">
					
						<p>We're sorry, but the recipe or article that you requested is no longer available.
						We will take you to our search feature in a few seconds or you can click <a href="<?php echo SITEURL.'/sitemap'?>">here</a>.</p>
					</div>
				</div>
				<div>
				<script language="javascript">
				/*var i = 5;
				window.onload=redirect;
				function redirect()
				{
				    var time = document.getElementById('time');
				    i--;
				    setTimeout("redirect()",1100);
					if(i<0)
					{
						i = 5;
					}
				    if(i==0)
				    {
						i = 5;
				        location.replace("<?php echo SITEURL.'/sitemap'?>");
				    }
				}*/
				</script>
				<span id="time">
				</span>
				</div>
				</div>
				<div class="clear"></div>
				<div class="formholder" style="margin-top:10px;margin-left:10px;">
				<form id="nav-top-form-search" action="<?= SITEURL; ?>/search" method="get" class="search fullsubmit">
				<div>
					<div class="categories">
						<label class="radio"><input class="controllerradio" name="controller" type="radio" value="recipes"<?= Template::get('searchType', 'recipes') == 'recipes' ? ' checked="checked"' : ''; ?> /> Recipes</label>
						<label class="radio"><input class="controllerradio" name="controller" type="radio" value="articles"<?= Template::get('searchType', 'recipes') == 'articles' ? ' checked="checked"' : ''; ?> /> Articles</label>
						<!--<label class="radio"><input class="controllerradio" name="controller" type="radio" value="profile"<?= Template::get('searchType', 'recipes') == 'profile' ? ' checked="checked"' : ''; ?> /> UserName</label>-->
						<div class="clear"></div>
					</div>
					<input class="textinput simpletext fl" type="text" title="Enter search keywords..." autocomplete="off" name="searchterm" value="<?= Template::get('searchTerm'); ?>" />
					<?php $recipelinks = Template::get('recipelinks'); ?>			
					<button class="button-lg fl" type="submit" title="Find"><span>Search</span></button>
				</div></form>
			</div>
				
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
