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
                        <img style="margin: 5px;" src="<?= ASSETURL; ?>/itemimages/300/300/3/<?= $recipe['image']['filename']; ?>" alt="">
                  </div>
                
                  <!-- Right Column-->
                  <div style="width: 320px; float: right;">       
                            <!-- The recipe card content -->
                            <div id="slidePageRecipeDesc" style="margin-top:10px; margin-bottom: 10px;"  ><a href="<?php echo $recipe['link'];?>"><?php echo $recipe['title']?></a></div>
                            <h2>Description</h2>
                            <div id="slidePageRecipeDesc" style="font-size: 0.75em; line-height: 1.5em;" ><?php echo $recipe['description']?></div>
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
                                */
                              ?>
     <div id="post">
               <div class="entry">               
                              <div id="recipeCardContent">
                                    <div id="recipe-ingredients" class="block"> 
                                        <!-- The recipe card content -->
                                        <h2>Ingredients</h2>
                                        <div id="slidePageIngredient"  ><ul><?php foreach($recipe['ingredients'] as $ingredient){ echo "<li class=\"ingredient\">" . $ingredient . "</li>";}?></ul></div>
                                        <h2>Directions</h2>
                                        <div id="slidePageDirection" ><?php echo $recipe['body']; ?></div>
                                    </div>
                                </div>
                            <?php /*} // End of the recipe card ?>
                            
                            <?php */?>
                            
<?php
    // We have to set it be 3 recipes only upon the request
    
    $preMessages = array(
                                    "",
                                    "Return to the Intro",
                                    "Return to the first recipe",
                                    "Return to the second recipe"
                            );
    $nextMessages = array(
                                    "Check out the first recipe here",
                                    "Check out the second recipe here",
                                    "Check out the third recipe here",
                                    "Vote for your favorite recipe here"
                            );

?>                            
                     <div>
                        <a href="<?php echo '/surveys/details/'. $slug . '/' . ($surveyPage - 1); ?>" style="float: left;">
                            <img src="/frontend/recipe4living/images/site/slideshows_back.png" alt="" style="vertical-align: middle;" >
                            <?php echo $preMessages[$surveyPage - 1];?>
                        </a>
                        <a href="<?php echo '/surveys/details/'. $slug . '/' . ($surveyPage + 1); ?>" style="float: right;">
                            <?php echo $nextMessages[$surveyPage - 1];?>
                            <img src="/frontend/recipe4living/images/site/slideshows_forward.png" alt="" style="vertical-align: middle;" >
                        </a>
                    </div>
               </div>
     </div>
     
     
     
     
          
           
        
          
          
        <div class="clear"></div>
    </div>
</div>


<div id="panel-right" class="column screenonly"> 
    <div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
</div> 
<div class="clear"></div>
