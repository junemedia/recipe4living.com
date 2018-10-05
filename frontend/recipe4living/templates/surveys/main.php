
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
            
       
     
            <div style="overflow: hidden;">
                  <!-- Left column -->
                  <div style=" width: 300px; float: left;">                
                        <!-- Recipe Image -->
                        <img style="margin: 5px;" src="<?= ASSETURL; ?>/itemimages/300/300/3/<?= $item['image']['filename']; ?>" alt="" />
                  </div>
                
                  <!-- Right Column-->
                  <div style="width: 320px; float: right; margin-top: 10px;">       
                            <!-- The recipe card content -->
                            <div id="slidePageRecipeDesc" style="font-size: 0.75em; line-height: 1.5em;" ><?php echo $item['body']?></div>
                   </div>
            </div>
            <div style="float: right;">      
                <a href="<?php echo '/surveys/details/'. $slug . '/2'; ?>" style="font-size: 12px;">
                    Check out the first recipe here
                    <img src="/frontend/recipe4living/images/site/slideshows_forward.png" alt="" style="vertical-align: middle;">
                </a>
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
     
     
     
     
          
           
        
          
          
        <div class="clear"></div>
    </div>
</div>


<div id="panel-right" class="column screenonly"> 
    <div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
</div> 
<div class="clear"></div>
