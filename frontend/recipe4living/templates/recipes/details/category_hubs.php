<div class="category_hubs screenonly">
    <h3 style="float:left"><a href="/">Home</a> <?php if(isset($categories[0]["link"])){?>&gt;
        <a href="/recipes/<?php echo $categories[0]["link"]?>">
            <?php echo $categories[0]["name"]?>
        </a> <?php }?>&gt;
		<a href="<?php echo SITEURL.$item['link'];?>"><?php echo $item['title']?></a>		
	</h3>
	<!--<div id="accesibility" style="float:right;">
		<a class="font-decrease" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-decrease.png" /></a>
		<img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size.png" />
		<a class="font-increase" href="#"><img src="<?= SITEASSETURL; ?>/images/recipes/detail/font-size-increase.png" /></a>
	</div>-->
</div>      
