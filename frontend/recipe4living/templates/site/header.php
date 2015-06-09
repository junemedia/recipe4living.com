<!--INFOLINKS_OFF-->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge" />
	<script type="text/javascript">
	var mobile_domain ="m.recipe4living.com";
	// Set to false to not redirect on iPad.
	var ipad = false;
	// Set to false to not redirect on other tablets (Android , BlackBerry, WebOS tablets).
	var other_tablets = false;
	document.write(unescape("%3Cscript src='"+location.protocol+"//s3.amazonaws.com/me.static/js/me.redirect.min.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php
if (strstr($_SERVER['REQUEST_URI'], '/links') ) { ?>
<title>Advertise With Us</title>

<?php } else { ?> 
<title><?= $title; ?></title>
<?php } ?>
	<?php if (strstr($_SERVER['REQUEST_URI'],'subctr')) { ?>
<meta name="keywords" content="recipe newsletters, free recipe newsletter, recipe newsletter, daily recipe newsletter, crockpot recipe newsletter, slow cooker recipe newsletter, free slow cooker recipes, free crockpot recipes, free crockpot recipe newsletter budget cooking recipes, budget cooking newsletter,party recipes and tips, party recipe newsletter, quick and easy recipe newsletter, quick and easy recipes" />
<?php } else { ?>
<meta name="keywords" content="<?= $keywords ? htmlentities($keywords, ENT_QUOTES, 'utf-8') : 'Recipe, Recipes, Easy Recipes, Food Ideas, Quick Recipes, Healthy Recipes, Cooking Tips, Cooking Ideas, Food Tips,'; ?>" />
<?php } ?>
	<meta name="description" content="<?= $description ? htmlentities($description, ENT_QUOTES, 'utf-8') : 'Looking for recipes and cooking tips? Recipe4Living has a recipe for every occasion. Whether you are looking for a quick and easy recipe, healthy recipes, or food ideas & tips for a special occasion, we have you covered.' ?>" />
	<meta name="author" content="Recipe4Living, Recipe4Living.com" />
	<meta name="verify-v1" content="MdhXUubKMGRn6vL5WSVMEXeKt6D4mMrULy9MG+6+Zf8=" />
<?php if ($format !='print') {?>	<link rel="stylesheet" href="<?= SITEASSETURL; ?>/css/site.css,nav.css,index.css,articles.css,landing.css,recipes.css,account.css,static.css,stickywin.css?v=a80cb" type="text/css" media="screen" /> <? } ?>
	<?php if ($format == 'print') {?><link rel="stylesheet" href="<?= SITEASSETURL; ?>/css/print.css?v=4" type="text/css"<?php if ($format != 'print') { ?> media="print"<?php } ?> /><?php }?>
	<link type="text/css" href="/frontend/recipe4living/css/print.css?v=4" media="print" rel="stylesheet">
	<!--[if IE 6]><link href="<?= SITEASSETURL; ?>/css/ie6.css?v=4" rel="stylesheet" type="text/css" /><![endif]-->
	<!--[if IE 7]><link href="<?= SITEASSETURL; ?>/css/ie7.css?v=4" rel="stylesheet" type="text/css" /><![endif]-->
	<link rel="shortcut icon" href="<?= SITEASSETURL; ?>/images/favicon.ico" type="image/vnd.microsoft.icon" />
	<link rel="icon" href="<?= SITEASSETURL; ?>/images/favicon.ico" type="image/vnd.microsoft.icon" />
	<?php if (Template::get('rssUrl')) { ?>
	<link href="<?= SITEINSECUREURL.Template::get('rssUrl'); ?>" rel="alternate" type="application/rss+xml" title="<?= BluApplication::getSetting('storeName'); ?> | <?= Template::get('rssTitle', 'Latest Recipes'); ?>" />
	<?php } ?>

	<?php if (($format != 'print') && (strpos($_SERVER['HTTP_USER_AGENT'], 'CrazyEgg Robot') !== 0)) { ?>
	<script type="text/javascript">
		/* Define global static variables. */
		DEBUG = <?= DEBUG ? 'true' : 'false' ?>;
		SITEURL = '<?= SITEURL ?>';
		SITESECUREURL = '<?= SITESECUREURL ?>';
		SITEINSECUREURL = '<?= SITEINSECUREURL ?>';
		ASSETURL = '<?= ASSETURL ?>';
		COREASSETURL = '<?= COREASSETURL ?>';
		SITEASSETURL = '<?= SITEASSETURL ?>';
	</script>

<?php if ((substr($_SERVER['REQUEST_URI'],0,9) == '/recipes/' && strstr($_SERVER['REQUEST_URI'],'.htm'))) { ?>
	<script type="text/javascript" src="<?= COREASSETURL ?>/js/StickyWin.js,Interface.js,Nav.js,HistoryManager.js,Forms.js,BrowseArea.js,Autocompleter.js,Milkbox.js,Wizard.js,sifr.js,Slideshow.js,Articles.js?ver=6wnp"></script>
<?php } else { ?>
	<script type="text/javascript" src="<?= COREASSETURL ?>/js/mootoolsCore.js,mootoolsMore.js,StickyWin.js,Interface.js,Nav.js,HistoryManager.js,Forms.js,BrowseArea.js,Autocompleter.js,Milkbox.js,Wizard.js,sifr.js,Slideshow.js,Articles.js?ver=6wnp"></script>
<?php } ?>
	
	<script type="text/javascript" src="<?= COREASSETURL ?>/js/jquery.min.js,jquery.fancybox-1.3.4.pack.js,jquery.cookie.js"></script>

	<!--<script type="text/javascript" src="http://www.jmtkg.com/js/tracker.js"></script>-->
    <script type="text/javascript" src=""></script>
	<script type="text/javascript">
		var R4LSignUpDhtml = jQuery.noConflict();
		var R4LDhtml = jQuery.noConflict();
	</script>
	
    <?php if (Template::get('tinyMce')) { ?>
	<script type="text/javascript" src="<?= COREASSETURL ?>/plugins/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript" src="<?= COREASSETURL; ?>/js/TinyMCE.js"></script>
	<?php } ?>

	<?php
		// Page-specific script includes
		echo $includeScript;

		// Generic bits of header
		echo $genericHeader;
	?>

	<script type="text/javascript">
	window.addEvent('domready', function(){

		/* Init history manager */
		HistoryManager.initialize();

		/* Get reference to body content */
		var bodyContent = $(document.body);

		/* Top nav */
		var topNav = new TopNav($('nav-top'));

		/* Standard forms */
		bodyContent.getElements('div.standardform, fieldset.standardform').each(function(formcontainer) {
			var standardForm = new StandardForm(formcontainer);
		});

		<?= $domreadyScript; ?>

		/* Popups */
		var infoPopups = new InfoPopups(bodyContent.getElements('a.info-popup'));
		var printPopups = new AssetPopups(bodyContent.getElements('a.print-popup'), {
			windowKey: 'recipe4living_print_popup'
		});

		/* Page scroll */
		var pageScroll = new PageScroll(bodyContent.getElements('a.scroll'), {
			wheelStops: false
		});

		/* Start history manager */
		HistoryManager.start();

		/* Input over text */
		$$('input.simpletext, textarea.simpletext').each(function(input) {
			new InputText(input);
		});

	});

	window.addEvent('load', function() {

		<?= $loadScript; ?>

	});
	</script>
        
        <!-- Yieldbot.com Intent Tag LOADING -->
        <script type="text/javascript" src="https://cdn.yldbt.com/js/yieldbot.intent.js"></script>
        <!-- Yieldbot.com Intent Tag ACTIVATION -->
        <script type="text/javascript">
            yieldbot.pub('6e09');
            yieldbot.defineSlot('LB_ATF');
            yieldbot.defineSlot('MR_ATF');
            yieldbot.defineSlot('MR_Mid');
            yieldbot.defineSlot('LB_BTF');
            yieldbot.go();
        </script>
        <!-- END Yieldbot.com Intent Tag -->      
	<?php } ?>
<meta name="msvalidate.01" content="E03168D9BB4076DC3C37E21B03C7EE91" />

<!--<script type="text/javascript" src="http://m.recipe4living.com/mobify/redirect.js"></script>
<script type="text/javascript">try{_mobify("http://m.recipe4living.com/");} catch(err) {};</script>-->


<?php
/*
// Section Target Tag (this allows us to roadblock the recipes with RW Cooking ads only)
$array_section_target_tag = array('/recipes/mind_blowing_green_bean_casserole_recipe.htm/',
'/recipes/broccoli_cheese_casserole_2.htm/',
'/recipes/turkey_broccoli_alfredo.htm/',
'/recipes/swiss_vegetable_casserole.htm/',
'/recipes/oven_roasted_root_vegetables.htm/',
'/recipes/french_onion_turkey_casserole.htm/',
'/recipes/jollof_chicken_rice.htm/',
'/recipes/sensational_turkey_noodle_soup.htm/',
'/recipes/secret_ingredient_sweet_potato_pie_2.htm/',
'/recipes/moist_savory_stuffing.htm/',
'/recipes/homemade_cranberry_pecan_stuffing_recipe.htm/',
'/recipes/roasted_orange_cranberry_sauce.htm/',
'/recipes/holiday_brie_en_croute.htm/',
'/recipes/pear_cranberry_strudel_with_caramel_sauce.htm/',
'/recipes/out_of_this_world_broccoli_casserole_recipe.htm/',
'/recipes/heavenly_sweet_potatoes.htm/',
'/recipes/latkes_with_sour_cream_mushroom_sauce.htm/',
'/recipes/super_duper_beef_wellington_recipe.htm/',
'/recipes/ginger_spice_cheesecake.htm/',
'/recipes/white_chocolate_silk_pie.htm/',
'/recipes/lemon_basil_hummus_dip.htm/',
'/recipes/amazing_warm_spinach_dip_recipe.htm/',
'/recipes/brilliant_baked_corn_casserole_recipe.htm/',
'/recipes/turkey_cornbread_stuffing_with_sun_dried_tomatoes.htm/',
'/recipes/first_rate_praline_pumpkin_mousse_cornucopias_recipe.htm/',
'/recipes/sweet_potato_dip.htm/',
'/recipes/fine_shrimp_dip_recipe.htm/',
'/recipes/cranberry_ginger_eggnog_torte.htm/',
'/recipes/roast_turkey_with_mushroom_stuffing.htm/',
'/recipes/classic_roasted_turkey_with_pan_gravy.htm/',
'/recipes/cream_of_pumpkin_soup_with_cranberry_drizzle.htm/',
'/recipes/slow_cooker_gingerbread_with_dried_cherries.htm/',
'/recipes/yummy_apple_strudel_recipe.htm/',
'/recipes/chicken_breast_with_ham_provolone_rosemary_and_wolfgang_puck_s_creamy_vodka_sauce.htm/',
'/recipes/caramelized_onion_with_pancetta_rosemary_stuffing.htm/',
'/recipes/marvelous_festive_cranberry_stuffing_recipe.htm/',
'/recipes/stunning_orange_cranberry_sauce_recipe.htm/',
'/recipes/braised_brisket_with_garlic.htm/',
'/recipes/_1_buffalo_chicken_dip_recipe.htm/',
'/recipes/hearty_turkey_tortilla_soup.htm/',
'/recipes/holiday_turkey_with_cranberry_pecan_stuffing.htm/',
'/recipes/herb_seasoned_sausage_sweet_onion_and_celery_stuffing.htm/',
'/recipes/roasted_turkey_breast_with_herbed_au_jus.htm/',
'/recipes/sweet_potato_pecan_soup.htm/',
'/recipes/hot_crab_salsa_dip.htm/',
'/recipes/holiday_banana_bread_pudding.htm/',
'/recipes/ultra_creamy_mashed_potatoes.htm/',
'/recipes/cornbread_stuffing_with_chestnuts_bacon.htm/',
'/recipes/cherry_port_glazed_ham.htm/',
'/recipes/spinach_cheese_swirls.htm/',
'/recipes/chocolate_pirouette_crusted_cake.htm/',
'/recipes/cranberry_apple_bread_pudding.htm/',
'/recipes/chicken_crunch_2.htm/',
'/recipes/quick_easy_chicken_broccoli_brown_rice_2.htm/',
'/recipes/one_dish_chicken_rice_bake_2.htm/',
'/recipes/easy_chicken_cheese_enchiladas_2.htm/',
'/recipes/easy_chicken_pot_pie.htm/',
'/recipes/creamy_pesto_chicken_bow_ties_2.htm/',
'/recipes/chicken_broccoli_alfredo_2.htm/',
'/recipes/slow_cooker_savory_pot_roast_3.htm/',
'/recipes/mushroom_smothered_beef_burgers.htm/',
'/recipes/3_cheese_pasta_bake_2.htm/',
'/recipes/chicken_broccoli_divan.htm/',
'/recipes/slow_cooker_chicken_tortilla_soup.htm/',
'/recipes/easy_chicken_stroganoff.htm/',
'/recipes/quick_chicken_parmesan.htm/',
'/recipes/broccoli_cheese_stuffed_shells.htm/',
'/recipes/pork_mozzarella.htm/',
'/recipes/slow_cooker_spaghetti_bolognese.htm/',
'/recipes/slow_cooked_orange_chicken.htm/',
'/recipes/the_heartiest_beef_stew.htm/',
'/recipes/chicken_bean_chili.htm/',
'/recipes/steak_with_bell_peppers.htm/',
'/recipes/easy_taco_tamale_pie.htm/',
'/recipes/skillet_pork_chops_beans.htm/',
'/recipes/one_pot_chicken_chili_rice.htm/',
'/recipes/curried_chicken_chowder.htm/');

	if (in_array($_SERVER['REQUEST_URI'],$array_section_target_tag) || strstr($_SERVER['REQUEST_URI'], 'campbell_s_recipes')) { ?>
		<script type="text/javascript">federated_media_section="Campbells_RW";</script>
<?php } */?>



<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>


<?php
//$temptemp = Template::get('curr_itemid');
$curr_title = Template::get('curr_title');
//var_dump($curr_title);
$default_img = Template::get('default_img');
//var_dump($default_img);
$img_url = '';
if ($default_img !='') {
	$img_url = 'http://'.$_SERVER['SERVER_NAME'].ASSETURL.'/itemimages/200/200/3/'.$default_img;
}
if ($curr_title !='' && $img_url !='') { ?>
<meta property="og:title" content="<?php echo $curr_title; ?>" />
<meta property="og:type" content="article" />
<meta property="og:url" content="http://<?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" />
<meta property="og:image" content="<?php echo $img_url; ?>" />
<?php } ?>



</head>
<body<?= Template::get('paula_deen') ? ' id="paula_deen"' : ''; ?>>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-PPMDBL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PPMDBL');</script>
<!-- End Google Tag Manager -->


<!-- JMTKG.com tracker.js disabled. so let's removed it here as well -->
<!--
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" ></script>
<script type="text/javascript">
var site_name = "recipe4living";
var site_path = window.location.href;
var site_domain = ".recipe4living.com;path=/";  
jm_checkCookie(site_name, site_domain);

var site_guest_id = jm_getCookie('SITE_GUEST_ID');
var email = jm_getCookie('EMAIL_ID') ? jm_getCookie('EMAIL_ID'): "NO_EMAIL" ;


jm_push(site_name, site_path, site_guest_id, email);
jQuery.noConflict(); 
</script>
-->
<!--Sticky Menu-->
<script type="text/javascript" src="<?= COREASSETURL; ?>/js/stickymenu.js"></script>

<?php /*if (!isset($_GET['gclid'])) { ?>
<SCRIPT LANGUAGE='JavaScript'>
<!--
function GetCookie(name) {
	var arg=name+'=';
	var alen=arg.length;
	var clen=document.cookie.length;
	var i=0;
	while (i<clen) {
		var j=i+alen;
		if (document.cookie.substring(i,j)==arg)
		return 'here';
		i=document.cookie.indexOf(" ",i)+1;
		if (i==0) break;
	}
	return null;
}
var visit=GetCookie("ChefPopup");
var visit2=GetCookie("ChefPopup2");
if (visit == null && visit2 == null) {
	var expire=new Date();
	window.name = 'chef';
	var url = 'http://www.focusforfree.com/dispatch2.asp?home=1451-28667E-L1';
	newwin=open(url, 'dispwin', 'width=850,height=650,scrollbars=yes,menubar=no');
	expire=new Date(expire.getTime()+7776000000);
	document.cookie='ChefPopup=here;expires='+expire+';path=/';
}
// -->
</SCRIPT>
<?php }*/ ?>


<?php
include_once("dhtml.php");
?>


<!--<div id="promotion">
<ul>
	<li><a href="http://www.workitmom.com"><img src="<?= SITEASSETURL; ?>/images/site/wim.png"/></a></li>
	<li><a href="http://www.savvyfork.com"><img src="<?= SITEASSETURL; ?>/images/site/sf.png"/></a></li>
	<li><a href="http://www.fitandfabliving.com"><img src="<?= SITEASSETURL; ?>/images/site/ff.png"/></a></li>
	<li style="border-right:none;"><a href="http://www.recipe4living.com"><img src="<?= SITEASSETURL; ?>/images/site/r4l.png"/></a></li>
</ul>
</div>-->
<div id="nav-header">
<?php //if ($_SERVER['REQUEST_URI'] !='' &&$_SERVER['REQUEST_URI'] !='/index.php/') { // Such a bloody bodge ?>
	<div class="site-wrapper" id="top-ad"><?= Template::get('headerAdvert'); ?></div>
	<?php //} ?>
	<div class="site-wrapper">
		<div id="logo">
			<a href="<?= SITEURL; ?>/"><img style="width:387px;margin-left: -5px;" class="screenonly" alt="<?= BluApplication::getSetting('storeName'); ?>" src="<?= SITEASSETURL; ?>/images/site/R4L-Homepage-Logo.png" /></a>
			<a href="<?= SITEURL; ?>/"><img class="printonly" alt="<?= BluApplication::getSetting('storeName'); ?>" src="<?= SITEASSETURL; ?>/images/site/R4L-Homepage-Logo.png" /></a>
		</div>
		<?php $controls = (isset($_GET['controls']))?$_GET['controls']:1;?>
		<?php if ($format == 'print' && $controls == 1) { ?>
		<div class="print-popup-controls">
			<a href="#" onclick="window.print();" class="print">Print this page</a>
			<a href="#" onclick="window.close();" class="close">Close this window</a>
		</div>
		<?php } ?>
		<div class="tagline"><h4>Easy recipes and a helping of fun from home cooks like you.</h4></div>
		<div class="standardform screenonly">
			<div id="nav-links" class="screenonly text-content fr">
				<ul>
					
					<li>
						<?php
							$currentUser = Template::get('currentUser');
							if ($currentUser) {
						?>
							<a href="<?= SITEURL; ?>/account/">My Account</a> / <a href="<?= SITEURL ?>/facebook/logout/">Log out</a>
						<?php
							} else {
						?>
							<a href="<?= SITEURL ?>/facebook/login/" id="sign_in"><span>Login</span></a> /
							<a href="<?= SITEURL ?>/register/" id="join_now"><span>Register</span></a>
						<?php
							}
						?>
					</li>
					<!--<li><a href="<?= SITEURL; ?>/index/subctr">Manage My Newsletters</a></li>-->
				</ul>
			</div>
			<div class="formholder">
				<form id="nav-top-form-search" action="<?= SITEURL; ?>/search" method="get" class="search fullsubmit">
				<div>
					<div class="categories">
						<label class="radio"><input class="controllerradio" name="controller" type="radio" value="recipes"<?= Template::get('searchType', 'recipes') == 'recipes' ? ' checked="checked"' : ''; ?> /> Recipes</label>
						<label class="radio"><input class="controllerradio" name="controller" type="radio" value="articles"<?= Template::get('searchType', 'recipes') == 'articles' ? ' checked="checked"' : ''; ?> /> Articles</label>
						<!--<label class="radio"><input class="controllerradio" name="controller" type="radio" value="profile"<?= Template::get('searchType', 'recipes') == 'profile' ? ' checked="checked"' : ''; ?> /> UserName</label>-->
						<div class="clear"></div>
					</div>
					<input class="textinput simpletext fl" type="text" title="Enter search keywords..." autocomplete="off" name="searchterm" value="<?= Template::get('searchTerm'); ?>" />
					<?php $recipelinks = Template::get('recipelinks'); ?>			
					<button class="button-lg fl" type="submit" title="Find"><span>Search</span></button>
				</div></form>
			</div>
		</div>
		
	
		

		<!--<div id="nav-call-to-action" style="width:245px;" class="screenonly">
			
			<img src="<?= SITEURL; ?>/frontend/recipe4living/images/site/addarecipe.gif" style="float: left;"><a href="<?= SITEURL; ?>/share" id="share-recipe-link" class="parent"  style="float: left; padding-top: 5px;">Add My Recipe</a>
			
</div>-->

		<div class="clear"></div>
	</div>
</div>

<?= $topNav; ?>


<div id="content-wrapper">
	<div class="site-wrapper">
	<!--INFOLINKS_ON-->
<?php if($format == 'print'){?>
<?php echo "<!--INFOLINKS_OFF-->";?>
<?php }?>
