
	<div id="main-content" class="recipes">
		<div id="column-container">
		
			<div id="panel-center" class="column">

				<div class="rounded">
					<?php if($type){?>
						<?php include(BLUPATH_TEMPLATES.'/articles/alphabetical_list_tabs.php') ?>
					<?php }else{?>
						<h2 class="main"><?= Template::get('title'); ?></h2>
					<?php }?>
					<div class="content">
						<div class="text-content">
							<div>
								<?php
									echo '<div class="alphabet">';
									echo '<span class="letter"><a href="'.('?letter=other').'&type='.$type.'">@</a></span> ';
									echo '<span class="letter"><a href="'.('?letter=numeral').'&type='.$type.'">0-9</a></span> ';
									for ($i = 'A'; $i < 'Z'; $i++) {
										echo '<span class="letter"><a href="'.('?letter='.$i).'&type='.$type.'">'.$i.'</a></span> ';
									}
									echo '<span class="letter"><a href="'.('?letter=Z').'&type='.$type.'">Z</a></span>';
									echo '</div>';
								?>
							</div>
						<?php
							$previousFirstLetter = null;
							foreach($items as $item) {
								$firstLetter = strtoupper(substr($item['title'],0,1));
								$aName = 'letter_'.$firstLetter;
								if(!preg_match('/^[A-Z]$/', $firstLetter)) {
									if(preg_match('/^[0-9]$/', $firstLetter)) {
										$firstLetter = '0-9';
										$aName = 'letter_numeral';
									}
									else {
										$firstLetter = '@';
										$aName = 'letter_other';
									}
								}
								if($firstLetter != $previousFirstLetter) {
									echo '<h2 class="letter-heading">';
									echo '<a name="'.$aName.'"></a>';
									echo $firstLetter; 
									echo '</h2>';
								}
								$previousFirstLetter = $firstLetter;
								echo '<a href="'.SITEURL.'/'.$item['type'].'/'.$item['slug'].'.htm">'.$item['title'].'</a><br />';
							}
						?>
						</div>
					</div>
				
				</div>
			
			</div>
			
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
			
			<div id="panel-right" class="column">
				<div class="ad"><?php $this->_advert('openx_300x250atf'); ?></div>

				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
				
				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>
			
			<div class="clear"></div>
		</div>
	</div>
