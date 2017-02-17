

	<div id="main-content" class="recipe">

		<div id="column-container">
			
			<div id="panel-center" class="column">
				
				<div id="recipe-title" class="rounded">
					
						<?php if($tabs == 1){?>		
							<?php include(BLUPATH_TEMPLATES.'/articles/alphabetical_list_tabs.php') ?>
						<?php }else{?>
                        <div class="content">
							<h2>Encyclopedia of Tips</h2>	
                        </div>					
						<?php }?>
					
				</div>
	
				<div id="post">
				
					<div id="post-details">
						
						<ul id="cs-links" class="text-content fr screenonly">
							<li class="first"><script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=d78b8763-d922-459b-871d-1177ab5d4c23&amp;type=website&amp;buttonText=Email%20or%20Share%20This&amp;style=rotate"></script></li>
							<li><a href="?format=print" class="print-popup">Print</a></li>
							<li id="accesibility">
								<a class="font-decrease" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-decrease.png" /></a>
								<img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size.png" />
								<a class="font-increase" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-increase.png" /></a>
							</li>
						</ul>
						
						<div class="clear"></div>
					</div>
					
					<div class="teaser fl">
						<?php $this->_addRating(); ?>
					</div>
					
					<div class="clear"></div>
					
					<div class="entry">

					<a name="top"></a>

					<p style="text-align: center">
					<?php
						/*for($i='A';$i<'Z';$i++) {
							if(isset($quicktipsByFirstLetters[$i])) {
								echo '<a href="#'.$i.'" style="font-size: 12px; font-weight: bold; margin: 0px 3px;">';
							}
							else {
								echo '<span style="font-size: 12px; font-weight: bold; color: #CCCCCC; margin: 0px 3px;">';
							}
							echo $i;
							if(isset($quicktipsByFirstLetters[$i])) {
								echo '</a>';
							}
							else {
								echo '</span>';
							}
						}
						$i = 'Z';
						if(isset($quicktipsByFirstLetters[$i])) {
							echo '<a href="#'.$i.'" style="font-size: 12px; font-weight: bold; margin: 0px 3px;">';
						}
						else {
							echo '<span style="font-size: 12px; font-weight: bold; color: #CCCCCC; margin: 0px 3px;">';
						}
						echo $i;
						if(isset($quicktipsByFirstLetters[$i])) {
							echo '</a>';
						}
						else {
							echo '</span>';
						}*/
					?>
					</p>

					<?php
						//foreach($groupedQuicktips as $firstLetter=>$letterQuicktips) {
					?>
							<!--<hr style="border: 1px solid #CCCCCC; margin-bottom: 10px;" />
							<p style="text-align: center;"><a name="<?= $firstLetter ?>" style="font-weight: bold; font-size: 25px;"><?= $firstLetter ?></a></p>
							<hr style="border: 1px solid #CCCCCC; margin-bottom: 10px;" />-->
							<?php
								/*foreach($letterQuicktips as $quicktipIdOrSection=>$data) {
									if(isset($data['id'])) {
										$quicktip = $data;
							?>
									<a name="<?= $quicktip['title'] ?>"></a><a name="<?= $quicktip['title'].';scroll-'.$quicktip['title'] ?>"></a>
									<h3 style="font-size: 18px; margin-bottom: 10px;"><?= $quicktip['title'] ?></h3>
									<?= $quicktip['body'] ?>
							<?php
									}
									else {
										$section = $quicktipIdOrSection;
							?>
											<a name="<?= $section ?>"></a><a name="<?= $section.';scroll-'.$section ?>"></a>
											<h3 style="font-size: 18px; margin-bottom: 10px;"><?= $section ?></h3>
							<?php
										$i = 0;
										foreach($data as $quicktipId=>$quicktip) {
											if($section!=$quicktip['title']) {
							?>
											<a name="<?= $quicktip['title'] ?>"></a><a name="<?= $quicktip['title'].';scroll-'.$quicktip['title'] ?>"></a>
											<h4 style="font-size: 15px; margin-bottom: 10px; font-style: italic; font-weight: normal; color: #4d4949;"><?= $quicktip['title'] ?></h4>
							<?php
										}
							?>
											<?= $quicktip['body'] ?>
							<?php
											if(++$i<count($data)) {
							?>
											<hr style="border: 1px solid #EEEEEE; margin-bottom: 10px;" />
							<?php
											}
										}
									}
							?>
									<hr style="border: 1px solid #CCCCCC; margin-bottom: 10px;" />
							<?php
								}*/
								echo $item["body"];
							?>
							<p style="text-align: right;"><a href="#top">Back to Top</a></p>
					<?php
						//}
					?>

						<div class="clear"></div>
					</div>
					
					<?php include(BLUPATH_TEMPLATES.'/articles/details/share.php'); ?>
					
					<?php //$this->_addReview(); ?>
					
					<?php //$this->reviews(); ?>
					
					<div class="clear"></div>
				</div>
			</div>

			<div id="panel-left" class="column screenonly">
				<?php $this->leftnav(); ?>
			</div>
	
			<div id="panel-right" class="column screenonly">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>
				
				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>

				<?php // $this->landing_featured_question(); ?>
				
				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>

	</div>

