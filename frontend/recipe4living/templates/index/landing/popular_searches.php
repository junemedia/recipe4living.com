
	<div id="popular_searches" class="rounded half fl">
		<div class="content">
			<h2>Popular Searches</h2>
			<div class="text-content">
				
				<?php if (!empty($popularSearchTerms)) { $popularSearchTermCount = count($popularSearchTerms); ?>
					<?php for ($i = 0, $j = 1; $i < $popularSearchTermCount; $i++, $j++) { ?>
						<?php if($j == 1) { ?>
							<ul style="margin: 3px 0px 0px 0px">
						<?php } ?>
						<?php
							$term = strtolower($popularSearchTerms[$i]['term']);
							switch($popularSearchTerms[$i]['type']) {
								case 'recipe': $searchUrl = 'search?controller=recipes&amp;searchterm='.urlencode($term); break;
								case 'article': $searchUrl = 'search?controller=articles&amp;searchterm='.urlencode($term); break;
							}
						?>
							<li><a href="<?= SITEURL.$searchUrl; ?>"><?= $term ?></a></li>
						<?php if($j == 12 || $i == $popularSearchTermCount - 1) { $j = 0; ?>
							</ul>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			
				<div class="clear"></div>
			</div>
		</div>
	</div>