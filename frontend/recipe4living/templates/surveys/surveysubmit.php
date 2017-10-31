
<!-- Alright, we have to use jquery to switch the display -->
<STYLE type="text/css">
#recipes ul li{overflow: hidden;}
#recipes {margin-top: 10px; margin-bottom: 10px;}
.item{border-bottom: 1px;}
.mouseOver{background-color: #99ccff; cursor: pointer;}
.selectedRecipe{background-color: #99ccff; border: #3333ff 2px solid;}
</STYLE>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript">
    jQuery.noConflict();
    jQuery( document ).ready(function() {
        toggleLi();
    });
    
    function toggleLi(){
        jQuery('.item').mouseover(function() {jQuery(this).addClass('mouseOver');});
        jQuery('.item').mouseout(function() {jQuery(this).removeClass('mouseOver');});
        jQuery('.item').click(function () {

                jQuery('.item').removeClass('selectedRecipe');
                jQuery('#recipes').find(':radio').removeAttr('checked');
                jQuery("#surveyForm")[0].reset(); 
                jQuery(this).addClass('selectedRecipe');
                jQuery(this).find(':radio').attr("checked", "checked");
                //$('#recipes').find(':radio').removeAttr('checked');
                //$(this).find(':radio').attr("checked", "checked");

        });
//        jQuery('.item').click()(function() {jQuery(this).children('input').attr("checked", "checked");}
    }


</script>
<!-- This is the end -->


    <div id="main-content" class="recipe">

        <div id="column-container" style="padding-left:0px;width:645px;">
            <!-- start of class hrecipe -->
            <div itemscope itemtype="http://schema.org/Recipe">
            <div id="panel-center" class="column">
        
        
            <div style="overflow: hidden;">

            <!-- Slide Title First-->
            <div id="recipe-title" class="rounded">
            <div class="content">
            <span class="item"><h1 style="font-size: 1.25em; line-height: 1.25em; color:#ffffff; margin-bottom: 0;" class="fn">Please Choose Your Favorite </h1></span>
            </div>
            </div>
            
            <div>
                <form method="post" name="survey" id="surveyForm" action="<?php echo '/surveys/details/'. $slug . '/' . ($surveyPage + 1); ?>" enctype="multipart/form-data">



            <div id="recipes">    
                <ul>
                    <?php foreach($recipes as $recipe){?>
                        <li class="item">
                            <img style="margin: 5px; vertical-align: middle;" src="<?= ASSETURL; ?>/itemimages/100/100/3/<?= $recipe['image']['filename']; ?>" alt=""><?php echo $recipe['title']; ?><input type="radio" name="recipeSurvey" value="<?php echo $recipe['id']?>">
                        </li>
                    <?php }?>
                </ul>
            </div>



            <div style="margin-top: 5px;">
                <a href="<?php echo '/surveys/details/'. $slug . '/' . ($surveyPage - 1); ?>" style="float: left;">
                    <img src="/frontend/recipe4living/images/site/slideshows_back.png" alt="" style="vertical-align: middle;" >
                    Return to third recipe
                </a>
                <a href="<?php echo '/surveys/details/'. $slug . '/' . ($surveyPage + 1); ?>" style="float: right;">
                    Submit your vote to see the result
                    <button class="button-lg" value="Submit My choice" type="submit" name="survey_commit" style="color: white; padding-right: 5px; vertical-align: middle;">Submit My choice </button>
                </a>             
            </div>                
                
                
                </form>
                
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
    <div class="ad"><?php $this->_advert('openx_300x250atf'); ?></div>
    <div class="ad"><?php $this->_advert('WEBSITE_RIGHT_BANNER_1'); ?></div>
</div> 
<div class="clear"></div>
