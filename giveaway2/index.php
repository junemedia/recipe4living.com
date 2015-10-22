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
	$giveaway_right_img = "http://pics.recipe4living.com/giveaway/Yuletide-steak.jpg";
	$giveaway_extra_right_img = '';
	$giveaway_src = "YuletideTreasuresPackSteaks";
}

if (date('m') == 01 || (date('m') == 02 && date('d')<=15)) {
	$giveaway_title = "Win a gift pack of delicious baked goods!";
	$giveaway_text = "We're so excited to share this new giveaway with you!  We love Dancing Deer Baking Company because they have been recognized as a sustainable business leader, and place a large emphasis on giving back to the community. Their Sweet Home Project for funding low-income single parents who want to further their education is great, and their sweets are just as good. If you want to enter to win a gift pack of delicious baked goods from the Dancing Deer Baking Company, just fill out the form below! Giveaways will be accepted until February 15th, 2015 at 11:59 PM EST. Good luck!";
	$giveaway_right_img = "http://pics.recipe4living.com/giveaway/DancingDeerBaking.png";
	$giveaway_extra_right_img = '';
	$giveaway_src = "DancingDeerBaking";
}

if (date('m') == "03"|| date('m') == "04") {
    $giveaway_title = "R4L Giveaways – COMING SOON!";
    $giveaway_text = "Looking for the Recipe4Living food and kitchen appliance giveaway? Well, we're currently working on our next great freebie that only requires you to enter your email address! In the meantime, feel free to fill out our Contact Form at the bottom of the site and let us know what you'd like us to give away! A KitchenAid stand mixer? A Le Creuset French oven? Some more Omaha Steaks? Some other fancy kitchen gadget or new gourmet food you'd like to try? We'd love to hear it! Keep checking the Recipe4Living giveaway page, we'll have another high-quality giveaway for all you food lovers soon. In the meantime, sign up for the Recipe4Living newsletters and keep up to date with all the fantastic quick and easy recipes we're cooking up!";
    $giveaway_right_img = "";
    $giveaway_extra_right_img = '';
    $giveaway_src = "YuletideTreasuresPackSteaks";
?>
<title><?php echo $giveaway_title; ?></title>
<link rel="stylesheet" href="/frontend/recipe4living/css/site.css,nav.css,index.css,articles.css,landing.css,recipes.css,account.css,static.css,stickywin.css?v=a80cb" type="text/css" media="screen" />
<center>
<div id="contact" class="rounded" style="width:762px;font: Arial, Helvetica, sans-serif;">
	<h2 style="font-size:15px;background-color:#ED1D24;color:white;height:27px;text-aling:left;padding-left:10px;padding-top:5px;"><?php echo $giveaway_title; ?></h2>
	<div>
		<table border="0" style="font: Arial, Helvetica, sans-serif;">
			<tr>
				<td width="412px">
					<h1 style="font: Arial, Helvetica, sans-serif;color: #333;padding-left:10px;max-width:380px;"><?php echo $giveaway_title; ?></h1>
					<p style="font: 12px Arial, Helvetica, sans-serif;padding-left:10px;max-width:360px;">
						<?php echo $giveaway_text; ?>
					</p>
					<p>
						<iframe src="http://r4l.popularliving.com/subctr/forms/r4l_giveaway2.php?src=<?php echo $giveaway_src; ?>&guid=<?php if (isset($_REQUEST['guid'])) { echo trim($_REQUEST['guid']); } ?>" width="400px" height="730px" frameborder="0" scrolling="No"></iframe>
					</p>
				</td>
				<?php if(date('m') == 11|| date('m') == 12){?>
				<td width="350px" valign="top" style="text-align:center;"><img src="<?php echo $giveaway_right_img; ?>" style="max-width:245px;"></img><?php echo $giveaway_extra_right_img;?></td>
				<?php }else{?>
				<td width="350px" valign="top" style="text-align:center;"><img src="<?php echo $giveaway_right_img; ?>" style="max-width:345px;"></img><?php echo $giveaway_extra_right_img;?></td>
				<?php }?>
			</tr>
		</table>
	</div>
</div>
</center>
<script type="text/javascript">
/*
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-1200417-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
*/
</script>
<script>
/*
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-43033437-1', 'recipe4living.com');
  ga('send', 'pageview');
*/
</script>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-PPMDBL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PPMDBL');</script>
<!-- End Google Tag Manager -->
