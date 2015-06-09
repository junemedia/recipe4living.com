<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?= $storename ?> Admin</title>
	<meta name="Description" content="<?= $description ?>" />
	<meta name="Keywords" content="<?= $keywords ?>" />
	<meta name="author" content="blubolt Design, www.blubolt.com" />	
	
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
	<?php //if (Template::get('tinyMce')) { ?>
	<!--<script type="text/javascript" src="<?= '/frontend/base' // COREASSETURL ?>/plugins/tiny_mce/tiny_mce.js"></script>-->
	<script type="text/javascript" src="<?= '/backend/base' // COREASSETURL ?>/plugins/jscripts/tiny_mce/tiny_mce.js"></script>

	<script type="text/javascript">
	
		tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,|,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,link,unlink",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
	
	</script>
	<?php //} ?>
<? /*
	<script type="text/javascript" src="<?= COREASSETURL ?>/plugins/tiny_mce/tiny_mce.js?v=3"></script>
	<script type="text/javascript" src="<?= COREASSETURL ?>/js/TinyMCE.js"></script>
*/ ?>
	<script type="text/javascript" src="<?= COREASSETURL; ?>/js/mootoolsCore.js,mootoolsMore.js,Interface.js,HistoryManager.js,StickyWin.js,Forms.js,BrowseArea.js,Table.js,pop2.js,Articles.js"></script>
	
	<link rel="stylesheet" href="<?= COREASSETURL; ?>/css/autocompleter.css,adminstyles.css,site.css,Table.css,stickywin.css,styles.css,stylesie.css" />
	<style type="text/css">
		table tr:hover {
			background-color: #FFFFCC;
		}
	</style>

	<?php
	/* Page-specific script includes */
	echo $includeScript;
	?>
	
	<script type="text/javascript">
	window.addEvent('domready', function(){
		
		/*
		AJAXIMAGE = new Asset.image('<?=COREASSETURL;?>/images/site/ajax.gif', {id: 'Ajax Loading image', title: 'Loading...'}).setStyles({
			'position': 'absolute',
			'top': '50%',					// Centre the height
			'left': '50%',					// Centre the width
			'margin-top': '-33px',				// Half the height of the image
			'margin-left': '-33px'				// Half the width of the image
		});
		*/

		/* Standard forms */
		$(document.body).getElements('div.standardform, fieldset.standardform').each(function(formcontainer) {
			var standardForm = new StandardForm(formcontainer);
		});
		
		<?= $domreadyScript; ?>
		
		/* Popups */
		var infoPopups = new InfoPopups($(document.body).getElements('a.info-popup'));
		
	});
		
	</script>

</head>
<body>

	<?php if ($breadcrumbs) { ?>
	<div class="breadcrumbs">
		<?= $breadcrumbs; ?>
	</div>
	<?php } ?>

	<?php
		if (Template::get('showTopNav', true)) { 
			echo $topNav;
		}
	?>
	
	<?= Messages::getMessages(); ?>

	<div class="outer">
		<div id="main">
		<div style="float:right;">
			<a href="javascript:history.back()">Back</a>
		</div>
