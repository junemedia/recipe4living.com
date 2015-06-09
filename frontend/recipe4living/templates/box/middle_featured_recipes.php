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
		<div class="content" style="padding-top: 3px;">
        
        <?php /*
        Let's remove this for now
        <h1 class="main">Featured Video Recipes</h1>
        <div >
           <!--<h2 style="margin-bottom:10px;">Easy, Creamy Recipes From Philadelphia Cream Cheese</h2>-->
           <!--
                <div class="text" style="padding-bottom:10px;">
                    Philadelphia Cream Cheese makes some of the most indulgent recipes on the planet. Take a peek at these 13 easy recipes using the ingredient and make them at home! With everything from dips to cheesecake recipes below, you're set!
                </div>-->
            <div>
                <!--<script type="text/javascript" src="https://spshared.5min.com/Scripts/PlayerSeed.js?sid=1755&amp;width=465&amp;height=300&amp;hasCompanion=true&amp;videoGroupID=149636&amp;sequential=0&shuffle=1"></script>-->
				<!--<script type="text/javascript" src="https://spshared.5min.com/Scripts/PlayerSeed.js?sid=1755&amp;width=465&amp;height=300&amp;hasCompanion=false&amp;videoGroupID=162385&amp;sequential=0&amp;shuffle=1"></script>-->
                <script type="text/javascript" src="http://pshared.5min.com/Scripts/PlayerSeed.js?sid=179&width=571&height=350&videoGroupID=166201"></script>
            </div>
        </div>
        */ ?>        
        
        <div class="clear2"></div>
			<h1 style="margin-top:0px;">Featured Easy Recipes</h1>
			<div class="text">
			<!--At Recipe4Living, we pride ourselves on providing you with thousands of delicious easy recipes. We have everything from rich and creamy casseroles to beautiful cake recipes to keep you and your family well fed. Whether it's weeknight meal ideas or holiday recipes that you're after, we've got it covered! -->
			</div>
			<div class="clear2"></div>
			
			<?php if (empty($items)) { ?>
			No recenty added recipes to display.
			<?php } else { ?>
			<ul class="thumb-list">
				<?php foreach ($items as $item) {?>
				<li>
					<div class="im">
						<a href="<?= SITEURL.$item['link']; ?>" title="<?= $item['title']; ?>"><img alt="<?= isset($item['featuredImage']['filename']) ? $item['featured_alt'] : $item['title']; ?>" src="<?= ASSETURL; ?>/itemimages/75/75/3/<?= isset($item['featuredImage']['filename']) ? $item['featuredImage']['filename'] : $item['image']['filename']; ?>" /></a>
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
        <div class="clear2"></div>
        <div class="screenonly">
        </div>

	</div>
