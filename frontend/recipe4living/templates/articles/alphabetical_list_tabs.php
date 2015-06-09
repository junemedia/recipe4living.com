<div class="article-tabs"> 

    <ul>
        <li<? if (isset($type) && $type=='recipe'){?> class="on"<? }?>><a href="<?= SITEURL ?>/alphabeticallist?type=recipe">All Recipes</a></li>
        <li<? if (isset($type) && $type=='article'){?> class="on"<? }?>><a href="<?= SITEURL ?>/alphabeticallist?type=article">All Articles</a></li>
        <li<? if (isset($tabs) && $tabs==1){?> class="on"<? }?>><a href="<?= SITEURL ?>/articles/encyclopedia_of_tips?tabs=1">Tips</a></li>
    </ul>
    
    <div class="clear"></div>

</div>