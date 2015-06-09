   <div id="contentbody">
   <?php if(substr(trim($item['body']),0,5)!="<pre>")
   {
		echo "<pre style='font-family: Arial,​Helvetica,​sans-serif;white-space: pre-wrap;'>".$item['body']."</pre>";
   }else
   {
		echo $item['body'];
   }?>
	<?php //echo $item['title']; ?>
	</div>
	<?php include(BLUPATH_TEMPLATES.'/box/addalt.php'); ?>
	<?php if (!empty($item['related'])) { ?>
	<div class="block screenonly" id="similar-dishes">
		<h2>Similar Articles</h2>
		<!--<div class="content">
			<ul class="thumb-list">
				<?php foreach ($item['related'] as $relatedItem) { ?>
				<li>
					<div class="im">
						<a href="<?= SITEURL.$relatedItem['link']; ?>">
							<img alt="<?= (!empty($relatedItem['default_alt']))?$relatedItem['default_alt']:'';?>" " src="<?= ASSETURL; ?>/itemimages/35/35/3/<?= $relatedItem['image']['filename']; ?>" />
						</a>
					</div>
					<div class="desc">
						<h5 style="width: 400px"><a href="<?= SITEURL.$relatedItem['link']; ?>"><?= $relatedItem['title']; ?></a></h5>
						<div class="rating text-content fl">
							<?php include(BLUPATH_TEMPLATES.'/articles/items/similar_rating.php'); ?>
						</div>
					</div>
					<div class="clear"></div>
				</li>
				<?php } ?>
			</ul>
		</div>-->
		<div class="similar_dishes">
			<ul class="thumb-list" style="margin-bottom:0px;">
				<?php foreach ($item['related'] as $relatedItem) { ?>
				<li>
					<?php //if (strstr($relatedItem['image']['filename'],'avatar')) { $relatedItem['image']['filename']='';} if ($relatedItem['image']['filename'] !='') { ?>
					<div class="im">
						<a href="<?= SITEURL.$relatedItem['link']; ?>">
							<img width="100" height="100" alt="<?= (!empty($relatedItem['default_alt']))?$relatedItem['default_alt']:'';?>" src="<?= ASSETURL; ?>/itemimages/120/120/3/<?= isset($relatedItem['featuredImage']['filename']) ? $relatedItem['featuredImage']['filename'] : $relatedItem['image']['filename']; ?>" />
						</a>
					</div>
					<?php //} ?>
					<div class="desc">
						<h5 style="font-size:12px"><a href="<?= SITEURL.$relatedItem['link']; ?>"><?= Text::trim($relatedItem['title'], 25); ?></a></h5><?php if(strlen($relatedItem['title'])<=15)echo "<br>"?>
						<div class="rating text-content fl">
							<?php include(BLUPATH_TEMPLATES.'/articles/items/similar_rating.php'); ?>
						</div>
					</div>
					<div class="clear"></div>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<?php } ?>
 <?php if (!empty($item['links'])) { ?>
        <div class="block screenonly" id="similar-dishes">
                <h2>Related Links</h2>
                <div class="content">
                        <ul class="thumb-list">
                                <?php foreach ($item['links'] as $item) { ?>
                                <li>
                                        <div class="desc">
                                                <h5><a href="<?= SITEURL.$item['href']; ?>"><?= $item['title']; ?></a></h5>
                                                <?php if($item['description']) { ?>
                                                <div class="rating text-content fl">
                                                        <?= $item['description']; ?>
                                                </div>
                                                <?php } ?>
                                        </div>
                                        <div class="clear"></div>
                                </li>
                                <?php } ?>
                        </ul>
                </div>
        </div>
        <?php } ?>
