    <div id="main-content" class="recipe">

        <div id="column-container" style="padding-left:0px;width:645px;">
            <!-- start of class hrecipe -->
            <div itemscope itemtype="http://schema.org/Recipe">
            <div id="panel-center" class="column">
        
        
            <div style="overflow: hidden;">

            <!-- Slide Title First-->
            <div id="recipe-title" class="rounded">
            <div class="content">
            <span class="item"><h1 style="font-size: 1.25em; line-height: 1.25em; color:#ffffff; margin-bottom: 0;" class="fn"><?php echo $item['title'];?> </h1></span>
            </div>
            </div>
            
              <!-- Left column -->
              <div style=" width: 300px; float: left;">                
                    <!-- Recipe Image -->
                    <img style="margin: 5px;" src="<?= ASSETURL; ?>/itemimages/300/300/3/<?= $pages['image']['filename']; ?>" alt="">
              </div>
            
              <!-- Right Column-->
              <div style="width: 320px; float: right;">
                                
                             <!-- Pagination-->   
                            <div id="slidePagination" style="float: right; overflow: hidden; width: 100%; margin: 5px;"> 
                                <a href="<?php echo $pageLinkPre;?>"><img src="/frontend/recipe4living/images/site/slideshows_back.png" alt="" style="float: left;"></a>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <a href="<?php echo $pageLinkNext;?>"><img src="/frontend/recipe4living/images/site/slideshows_forward.png" alt="" style="float: right;"></a>
                            </div>
                            
                       
                             <!-- Preview gallery-->
                             
                             <div>
                                <?php
                                        foreach($slideAll as $key=> $value){
                                        $order = $itemsModel->getSlideOrderBySlidePageArticleId($item['id'], $value['id']);    
                                ?>
                                    <a href="<?php echo $pageLinkPre = '/slidearticles/details/'. $arg[0] . '/' . $order;?>">
                                        <img style="<?php  if($value['id'] == $pages['id']){?> border: red 5px solid;<?php }else { ?> border: white 5px solid; <?php } ?>" src="<?= ASSETURL; ?>/itemimages/50/50/3/<?= $value['image']['filename']; ?>" alt="">
                                    </a>
                                <?php } ?>                                                                                                                                                                         
                             </div>
                           
                           <!-- Main Content-->
                           <div id="slidePageTitle" style="font-size: 16px; margin: 5px; font-family: Arial, Helvetica, sans-serif;"><a href="<?php echo $pages['link'];?>" target="_blank"><?php echo $pages['title']?></a></div>
                           <div id="slidePageContent" style="margin: 5px; font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><?php if(trim($pages['slide_desc']) == "") {echo $pages['description'];}else{echo $pages['slide_desc'];}?></div> 
                             
                        
                            <!-- Jump the Recipe page-->
                            <a href="<?php echo $pages['link'];?>" target="_blank">Get the recipe here!</a>
                            
                              <!-- Social Sharing Buttons -->
                              <div id="shareButton">
                                    <div class="nav-icon-item" style="margin-top: 10px;">
                                        <span>Share:</span>
                                        <a href="http://goo.gl/8XKWk" target="_blank"><img width="30" height="30" src="<?= SITEASSETURL; ?>/images/site/R4l-facebook-box.png"/></a>
                                        <a href="http://goo.gl/QWWuy" target="_blank"><img width="30" height="30"  src="<?= SITEASSETURL; ?>/images/site/R4l-twitter-box.png"/></a>
                                        <a href="http://goo.gl/LBcYo" target="_blank"><img  width="30" height="30" src="<?= SITEASSETURL; ?>/images/site/R4l-pinterest-box.png"/></a>
                                        <a href="http://bit.ly/ZxqJ3w" target="_blank"><img width="30" height="30"  src="<?= SITEASSETURL; ?>/images/site/R4l-googleplus-box.png"/></a>
                                    </div>
                              </div> 
                      </div>              
               </div>         
     
     
                                   <?php
                                   /**
                                   * About the recipe information display
                                   * Removed again since we do not want the user to see this. We want them to nav to the recipe details page 
                                   */
                                // Begin of the recipe card
                                
                                /*
                                if($pages["type"] == "recipe"){
                              ?>
                              <div id="recipeCardContent"> 
                                        <!-- The recipe card content -->
                                        <h4>Description</h4>
                                        <div id="slidePageRecipeDesc" style="font-size: 12px; margin: 5px; font-family: Arial, Helvetica, sans-serif;"><?php echo $pages['description']?></div>
                                        <h4>Ingredients</h4>
                                        <div id="slidePageIngredient" style="margin: 5px; font-family: Arial, Helvetica, sans-serif; font-size: 10px;"><ol><?php foreach($pages['ingredients'] as $ingredient){ echo "<li>" . $ingredient . "</li>";}?></ol></div>
                                        <h4>Directions</h4>
                                        <div id="slidePageDirection" style="margin: 5px; font-family: Arial, Helvetica, sans-serif; font-size: 10px;"><?php echo $pages['body']; ?></div>
                                </div>
                            <?php } // End of the recipe card ?>
                            
                            <?php */?>
     
     
     
     
          
    <?php
    /*
    <div class="entry_right" style="overflow: hidden;">
        <div class="rounded" id="recipe-title">
            <div class="content">
                <span class="item" style="text-align: center;">
                    <h2 style="line-height: 1.25em; color: rgb(255, 255, 255); margin-bottom: 0px;" itemprop="name" class="fn">Our Readers Also Loved</h2>
                </span>
            </div>        
        </div>        
        <div class="right_content">        
            <?php if(!empty($readerloved)){?>
            <ul class="thumb-list">
                <?php foreach ($readerloved as $key=>$ritem) { ?>
                <li  style="width: 300px; float: left;">
                
                    <div class="im">
                        <a href="<?= SITEURL.$ritem['link']; ?>">
                        <img width="75" height="75" alt="<?= isset($ritem['featuredImage']['filename']) ? $ritem['featured_alt'] : $ritem['default_alt'];?>" src="<?= ASSETURL; ?>/itemimages/75/75/3/<?= isset($ritem['featuredImage']['filename']) ? $ritem['featuredImage']['filename'] : $ritem['image']['filename']; ?>" />
                        </a>
                    </div>
                    
                    <div class="desc">
                        <h5><a href="<?= SITEURL.$ritem['link']; ?>"><?= $ritem['title']; ?></a></h5>
                        <div class="rating text-content fl">
                            <?php include(BLUPATH_TEMPLATES.'/articles/items/reader_loved_rating.php'); ?>
                        </div>
                    </div>
                    
                    <div class="clear"></div>
                    
                </li>
                <?php } ?>
            </ul>    
            <?php } else{?>
                No matched recipes.
            <?php }?>            
        </div>
    </div>
    <!--End entry right-->
     */         
    ?>
    <div class="clear2"></div>
    <div class="entry_right" style="overflow: hidden;">
        <div id="pubexchange_below_content"></div>
    </div>
          
        <div class="clear"></div>
    </div>
</div>


<div id="panel-right" class="column screenonly"> 
    <?php include(BLUPATH_TEMPLATES.'/site/newsletter.php') ?>  				
    <div class="ad"><?php $this->_advert('AD_RIGHT1'); ?></div>
	<?php $this->_box('right_column_featured_recipes', array('limit' => 1,'boxid'=>34)); ?>
    <div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
</div> 
<div class="clear"></div>
</div>
</div>
