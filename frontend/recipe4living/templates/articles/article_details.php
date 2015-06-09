
	<div id="main-content" class="recipe">

		<div id="column-container" style="padding-left:0px;width:645px;">

			<div id="panel-center" class="column">
				<?= Messages::getMessages(); ?>

				<?php if ($referer&&0) { ?>
				<div class="search-nav text-content screenonly">

					<a class="back fl" href="<?= SITEURL.$referer; ?>">Back to previous search results</a>

					<? /* Could put previous/next links here?
					<span class="fr">
						<a class="screenonly" href="#">Previous</a>
						&nbsp;|&nbsp;
						<a class="screenonly" href="#">Next</a>
					</span>
					*/ ?>

					<div class="clear"></div>
				</div>
				<?php } ?>
<?php  if ($this->_doc->getFormat() != 'print') { $this->category_hubs(); }?>
				<?php include(BLUPATH_TEMPLATES.'/articles/details/title.php'); ?>
				
				<?php 

				$currUrl = urlencode('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
				$currImage = urlencode('http://'.$_SERVER['SERVER_NAME'].ASSETURL.'/itemimages/200/200/3/' .$item['image']['filename']);
				?>

				<div id="post">

					<!--<div id="post-details">
						<p class="text-content fl screenonly">-->
							<!--Added: <?php Template::date($item['date']); ?>-->
					<!--	</p>
						<div class="user-info ">	
							<a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>" style="float:left;margin-right:10px;clear:none;width:65px;height:65px;">								
								<img width="65" height="65" alt="" src="<?= ASSETURL; ?>/userimages/65/65/1/<?= $item['author']['image'];?>">								
							</a>

							<div style="float:left;margin-top:8px;">								
								<a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>" style="font-size:16px;font-weight:bold;color:#0F52B0;">
									<?php $displayname = $item['author']['firstname'].' '.$item['author']['lastname'];
										  if($displayname != " " && $displayname != "" && !is_null($displayname))
											echo $displayname;
										  else
										  {
											$displayname = "Recipe4Living";
											echo "Recipe4Living";
										  }
									?>
								</a>
								<span style="display:block;font-size:12px;color:#4E4E4E;">
									<?php 
									$CommunityContributor = array(
                                            'vanessalantigua',
                                            'jennkendall',
                                            'kianishimine',
                                            'meaghansloand',
                                            'ashleighevans',
                                            'daniellegalian',
                                            'dawnharris',
                                            'samanthas',
                                            'katherineb',
                                            'morganq',
                                            'shalaynep',
                                            'stephaniek',
                                            'haleysoehn',
                                            'kaitlynconn',
                                            'kelleygrant',
                                            'erincullen',
                                            'laurabolbat',
                                            'tamarlange',
                                            'rachelfarnsworth'
                                            );
									if(trim($displayname) == "Recipe4Living") {
										echo '';
									} else if($item['author']['type'] == 'admin') {
										if (in_array(strtolower($item['author']['username']),$CommunityContributor)) { echo "Community Contributor"; } else { echo "Editor"; }
									} else {
										//if (in_array(strtolower($item['author']['username']),$CommunityContributor)) {
                                         echo "Community Contributor"; 
                                        //} else { 
                                        //    echo "Former Editor"; 
                                        //}
									} ?>
								</span>
							</div>
						</div>
						<ul id="cs-links" class="text-content fr screenonly" style="width:246px;margin-left: -30px;">
							<li class="first" style="margin-bottom:10px;">
							<span style="padding-right:5px"><a href="http://pinterest.com/pin/create/button/?url=<?=$currUrl;?>&media=<?=$currImage;?>" class="pin-it-button" count-layout="none"><img border="0" src="http://assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></span>
							<span  class='st_facebook_button' displayText='Facebook'></span>
							<span  class='st_twitter_button' displayText='Tweet'></span>
							<span  class='st_plusone_button' ></span>
							</li>
							
							<script type="text/javascript">var switchTo5x=false;</script>
							<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
							<script type="text/javascript">stLight.options({publisher:'0541fe9f-2a3f-4c01-ac74-8f02c84e7fde'});</script>
							<li style="border-left:none"><span  class='st_email_button' displayText='Email'></span></li>
							<li><a href="?format=print" class="print-popup">Print</a></li>
							<li id="accesibility">
								<a class="font-decrease" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-decrease.png" /></a>
								<img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size.png" />
								<a class="font-increase" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-increase.png" /></a>
							</li>
						</ul>

						<div class="clear"></div>
					</div>
				<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
				-->
					<?php $temp_img = $item['image']['filename']; if (strstr($item['image']['filename'],'avatar')) { $item['image']['filename']='';} ?>
					<?php if ($item['image']['filename'] !='') { ?>
					<?php if ($this->_doc->getFormat() != 'print') {?>
					<div class="im screenonly">
						<img width="350" width="350" src="<?= ASSETURL; ?>/itemimages/400/400/3/<?= $item['image']['filename']; ?>" class="photo" alt="<?= (!empty($item['default_alt']))?$item['default_alt']:$item['title'];?>" style="float:left;margin:0 20px 20px 0;"/>
						<div class="gallery-link">
							<?php /*if($item['image']['filename']) { ?>
								<a href="<?= SITEURL . $imageGalleryLink ?>">Submit more pictures of this article</a>
							<?php } else { ?>
								<a href="<?= SITEURL . $imageGalleryLink ?>">Be the first to submit a picture<br />of this article</a>
							<?php } */?>
						</div>
					</div>
					<?php }
					} else { ?>
					<meta itemprop="image" content="<?= ASSETURL; ?>/itemimages/200/200/3/<?php echo $temp_img; ?>">
					<?php } ?>
				

					<div class="teaser fl">
						<div class="user-info ">	
							<a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>" style="float:left;margin-right:10px;clear:none;width:65px;height:65px;">								
								<img width="65" height="65" alt="" src="<?= ASSETURL; ?>/userimages/65/65/1/<?= $item['author']['image'];?>">								
							</a>

							<div style="float:left;margin-top:8px;">								
								<a href="<?= SITEURL; ?>/profile/<?= $item['author']['username']; ?>" style="font-size:16px;font-weight:bold;color:#0F52B0;">
									<?php $displayname = $item['author']['firstname'].' '.$item['author']['lastname'];
										  if($displayname != " " && $displayname != "" && !is_null($displayname))
											echo $displayname;
										  else
										  {
											$displayname = "Recipe4Living";
											echo "Recipe4Living";
										  }
									?>
								</a>
								<span style="display:block;font-size:12px;color:#4E4E4E;">
									<?php 
									$CommunityContributor = array('vanessalantigua','jennkendall','kianishimine','meaghansloand','ashleighevans','daniellegalian','dawnharris','samanthas','katherineb','morganq','shalaynep','stephaniek','haleysoehn','kaitlynconn','kelleygrant','erincullen', 'laurabolbat', 'tamarlange', 'rachelfarnsworth');
									if(trim($displayname) == "Recipe4Living") {
										echo '';
									} else if($item['author']['type'] == 'admin') {
										if (in_array(strtolower($item['author']['username']),$CommunityContributor)) { echo "Community Contributor"; } else { echo "Editor"; }
									} else {
                                        //if (in_array(strtolower($item['author']['username']),$CommunityContributor)) {
                                         echo "Community Contributor"; 
                                        //} else { 
                                        //    echo "Former Editor"; 
                                        //}
									} ?>
								</span>
							</div>
						</div>
						<div class="clear" style="margin:25px 0;"></div>
						<?php //$this->_addRating(); ?>
						<?php if ($item['image']['filename'] !='') { ?>
							<p class="snippet">
						<?php } else { ?>
							<p class="snippet" style="width:460px;">
						<?php } ?>
						<?= $item['teaser']; ?></p>
						<?php
							$format = $this->_doc->getFormat();
							
							/*if ($format != 'print') {
								if ($item['id'] == '48479') {
									echo "&nbsp;<script type='text/javascript' src='http://thirdparty.fmpub.net/placement/456005?fleur_de_sel=[timestamp]'></script>&nbsp;";
								}
								if ($item['id'] == '48489') {
									echo "&nbsp;<script type='text/javascript' src='http://thirdparty.fmpub.net/placement/456006?fleur_de_sel=[timestamp]'></script>&nbsp;";
								}
								if ($item['id'] == '48491') {
									echo "&nbsp;<script type='text/javascript' src='http://thirdparty.fmpub.net/placement/456004?fleur_de_sel=[timestamp]'></script>&nbsp;";
								}
								if ($item['id'] == '48795') {
									echo "&nbsp;<script type='text/javascript' src='http://thirdparty.fmpub.net/placement/477963?fleur_de_sel=[timestamp]'></script>&nbsp;
									<img src='http://r1.fmpub.net/?r=https%3A%2F%2Fwin.ge.com%2F&k4=3173&k5={banner_id}' width='0' height='0' />";
								}
							}
							
							if (in_array($item['id'],array('38128','38129','38131','42925','48373','48375','48376','48378','48380','48382','48384','48385','48387','48388','48389','48391','48392','48395','48397','48398','48400','48402','48404','48406','48407','48409','48410','48412','48413','48414','48415','48416','48417','48418','48419','48420','48422','48423','48425','48426','48429','48431','48432','48434','48436','48439','48394','38180','48451','48452','48446','48444','48445','48441'))) {
								echo "<p><script type='text/javascript' src='http://thirdparty.fmpub.net/placement/448322?fleur_de_sel=[timestamp]'></script></p>";
								if ($format == 'print') {
									echo "<img src='http://bs.serving-sys.com/BurstingPipe/adServer.bs?cn=tf&c=19&mc=imp&pli=3197103&PluID=0&ord=[timestamp]&rtu=-1' width='0' height='0' border='0' />";
								}
							}*/
						?>

					</div>
<?php if ($this->_doc->getFormat() != 'print') {?>
					<div class="clear"></div>
					<div class="share_recipe screenonly">
						<div class="share_title" style="margin-bottom: 15px;"><h2>Share Article</h2></div>
						<div class="share_icon">
							<ul>
								<li><a href="http://www.facebook.com/sharer.php?u=<?php echo $currUrl;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-facebook-box.png"/></a></li>
								<li><a href="https://twitter.com/share?original_referer=<?php echo $currUrl;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-twitter-box.png"/></a></li>
								<li><a href="http://pinterest.com/pin/create/button/?url=<?php echo $currUrl;?>&media=<?=$currImage;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-pinterest-box.png"/></a></li>
								<li><a href="https://plus.google.com/share?url=<?php echo $currUrl;?>" target="_blank"><img src="<?= SITEASSETURL; ?>/images/site/R4l-googleplus-box.png"/></a></li>
								<li class="share_print"><a href="?format=print" class="print-popup"><img src="<?= SITEASSETURL; ?>/images/site/R4l-print-box.png"/></a></li>
								<li class="first"><span  class='st_email_button' displayText='Email'></span></li>
							</ul>
							<script type="text/javascript">var switchTo5x=false;</script>
							<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
							<script type="text/javascript">stLight.options({publisher:'0541fe9f-2a3f-4c01-ac74-8f02c84e7fde'});</script>
						</div>
					</div>
<?php }?>
					<div class="clear"></div>

					<div class="entry">
						<?php include_once(BLUPATH_TEMPLATES.'/site/ads/AOL_VIDEOS.php'); ?>
						
						<?php $this->view_page(); ?>
						
						<?php //$this->_advert('WEBSITE_INLINE_1'); ?>
						<div class="clear"></div>
					</div>

					<?php include(BLUPATH_TEMPLATES.'/articles/details/share.php'); ?>

					<?php $this->_addReview(); ?>

					<?php $this->reviews(); ?>

					<div class="clear"></div>
				</div>

				<?php //if (STAGING) { ?>
				<div class="conversationalist ad">
					<!-- FM Test STAMP ("Home") 500x250 Zone -->
					<script type='text/javascript' src='http://static.fmpub.net/zone/2560'></script>
					<!-- FM Test STAMP ("Home") 500x250 Zone -->
				</div>
				<?php //} ?>
			</div>

			<!--<div id="panel-left" class="column screenonly">
				<?php $this->leftnav(); ?>
			</div>-->

			<div id="panel-right" class="column screenonly">
				<?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>

				<?php //if (STAGING) { ?>
				<div class="ad">
				</div>
				<?php //} ?>

				<div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
				<?php $this->_box('right_column_featured_recipes', array('limit' => 1,'boxid'=>34)); ?>
<?php $this->_box('right_column_feature_collection', array('limit' => 3,'boxid'=>35)); ?>
				<?php // $this->landing_featured_question(); ?>
<div class="clear"></div>
				<?php //$this->_box('reference_guides', array('limit' => 10)); ?>
				<div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
			</div>

			<div class="clear"></div>
		</div>

	</div>
