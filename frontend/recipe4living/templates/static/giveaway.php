<?php require_once(dirname(__FILE__) . "/giveaway_offer.php");?>   
<?php

if (date('m') == 03 || date('m') == 04) {
	$giveaway_title = "Win A Pressure Oven From Wolfgang Puck!";
	$giveaway_text = "At Recipe4Living, we provide you with thousands of the best easy recipes on the web. Plus, we give away some of the best kitchen gadgets out there, like this Wolfgang Puck pressure oven! Just sign up for our recipe newsletters and you'll also be entered to win! Entries will be accepted until April 30, 2014 at 11:59 PM CST. Good luck!";
	$giveaway_right_img = "http://pics.recipe4living.com/giveaway/pressureoven2_small_img_logo.png";
	$giveaway_src = "WolfgangPuck";
}

if (date('m') == 05 || date('m') == 06) {
	$giveaway_title = "Win A Crock-Pot Casserole Crock Slow Cooker!";
	$giveaway_text = "At Recipe4Living, we provide you with thousands of the best easy recipes on the web. Plus, we give away some of the best kitchen gadgets out there, like this Crock-Pot Casserole Crock Slow Cooker! Just sign up for our recipe newsletters and you'll also be entered to win! Entries will be accepted until June 30, 2014 at 11:59 PM CDT. Good luck!";
	$giveaway_right_img = "http://pics.recipe4living.com/giveaway/CrockPot_product.jpg";
	$giveaway_src = "CrockPotSlowCooker";
}

if (date('m') == 07) {
	$giveaway_title = "Win A Nescafe Dolce Gusto Genio!";
	$giveaway_text = "This Nescafe Dolce Gusto Genio is this month's Recipe4Living giveaway! Want to make the coffee of your dreams? Just sign up for our recipe newsletters and you'll also be entered to win! Entries will be accepted until July 31, 2014 at 11:59 PM Central. Good luck!";
	$giveaway_right_img = "/giveaway2/images/Nescafe-product.png";
	$giveaway_extra_right_img = '<br><br><img src="/giveaway2/images/Nescafe-logo.png" width="55%">';
	$giveaway_src = "NescafeDolceGustoGenio";
}

if (date('m') == 8) {
	$giveaway_title = "Win 5-piece Anolon Baking Set!";
	$giveaway_text = "This month's giveaway from Recipe4Living is a 5-piece Anolon Baking set! This one's for all the bakers out there. Just sign up for our recipe newsletters and you'll also be entered to win! Entries will be accepted until August 30, 2014 at 11:59 PM Central. Good luck!";
	$giveaway_right_img = "http://pics.recipe4living.com/giveaway/Anolon_Product.png";
	$giveaway_extra_right_img = '<br><br><img src="http://pics.recipe4living.com/giveaway/Anolon_Logo.png" style="max-width:345px;">';
	$giveaway_src = "AnolonBakingSet";
}

if (date('m') == 9 || date('m') == 10) {
	$giveaway_title = "Win A Dyson DC59 Animal Vacuum!";
	$giveaway_text = "This month's giveaway from Recipe4Living is a Dyson DC59 Cordless Vacuum! This one's for all the messy cooks out there. Just sign up for our recipe newsletters and you'll also be entered to win! Entries will be accepted until October 31, 2014 at 11:59 PM Central. Good luck!";
	$giveaway_right_img = "http://pics.recipe4living.com/giveaway/Dyson_Product.png";
	$giveaway_extra_right_img = '<br><br><img src="http://pics.recipe4living.com/giveaway/Dyson_Logo.png" style="max-width:345px;">';
	$giveaway_src = "DysonDC59CordlessVacuum";
}

if (date('m') == 11|| date('m') == 12) {
	$giveaway_title = "Win A Yuletide Treasures Pack From Omaha Steaks!";
	$giveaway_text = "Celebrate the holidays with this delicious and beautiful gift pack from the folks at Omaha steaks. This pack is stuffed to the brim with all their bestsellers. Guess what? All you have to do to enter is sign up for our newsletter below to qualify to win! Entries will be accepted until December 31st 2014 at 11:59 PM Central. Good luck!";
	$giveaway_right_img = "http://pics.recipe4living.com/giveaway/Omaha_Steaks.jpg";
	$giveaway_extra_right_img = '';
	$giveaway_src = "YuletideTreasuresPackSteaks";
}

if (date('m') == "03"|| date('m') == "04") {
    $giveaway_title = "R4L Giveaways – COMING SOON!";
    $giveaway_text = "Looking for the Recipe4Living food and kitchen appliance giveaway? Well, we're currently working on our next great freebie that only requires you to enter your email address! In the meantime, feel free to fill out our Contact Form at the bottom of the site and let us know what you'd like us to give away! A KitchenAid stand mixer? A Le Creuset French oven? Some more Omaha Steaks? Some other fancy kitchen gadget or new gourmet food you'd like to try? We'd love to hear it! Keep checking the Recipe4Living giveaway page, we'll have another high-quality giveaway for all you food lovers soon. In the meantime, sign up for the Recipe4Living newsletters and keep up to date with all the fantastic quick and easy recipes we're cooking up!";
    $giveaway_right_img = "";
    $giveaway_extra_right_img = '';
    $giveaway_src = "YuletideTreasuresPackSteaks";
}

?>
	<div id="main-content" class="static">
		<div id="column-container">
			<div id="panel-center" class="column" >
				<div id="contact" class="rounded" style="width:762px;font: Arial, Helvetica, sans-serif;">
					<h2 style="font-size:15px;background-color:#ED1D24;color:white;height:27px;text-aling:left;padding-left:10px;padding-top:5px;"><?php //echo $giveaway_title; ?></h2>
					<div>
						<table border="0">
							<tr>
								<td width="412px">
                                    <div style=" overflow: hidden;"></div>
									<h1 style="color: #333;padding-left:10px;max-width:480px;"><?php echo $giveaway_title; ?></h1>
									<p style="font: 12px Arial, Helvetica, sans-serif;padding-left:10px;">
										<?php echo $giveaway_text; ?>
									</p>
                                    
                                    <div style="width:400px; height: 730px;"></div>
                                    
									<!--<p>
									<iframe src="http://r4l.popularliving.com/subctr/forms/r4l_giveaway.php?src=<?php //echo $giveaway_src; ?>&guid=<?php //if (isset($_REQUEST['guid'])) { echo trim($_REQUEST['guid']); } ?>&offid=<?php //if (isset($_REQUEST['offid'])) { echo abs(trim($_REQUEST['offid'])); } ?>" width="400px" height="730px" frameborder="0" scrolling="No"></iframe>
									</p>-->
								</td>
								<?php if(date('m') == 11|| date('m') == 12){?>
								<td  valign="top" style="text-align:center;"><img src="<?php echo $giveaway_right_img; ?>" style="max-width:245px;"></img><?php echo $giveaway_extra_right_img;?></td>
								<?php }else{?>
								<td  valign="top" style="text-align:center;"><img src="<?php echo $giveaway_right_img; ?>" style="max-width:345px;"></img><?php echo $giveaway_extra_right_img;?></td>
								<?php }?>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
			<div class="clear"></div>
		</div>
	</div>
