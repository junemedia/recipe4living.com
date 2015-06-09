jQuery(document).ready(function(){
		/*Navigation*/
		var nav = jQuery('#nav-top');
		var nav_top = nav.offset().top;
		var filler = jQuery('<div class="scroll-filler"></div>').height(nav.height()).css({ marginBottom: '5px', padding: '4px 0' });
		scroll_nav_check();
		jQuery(window).scroll(scroll_nav_check);
		jQuery(window).resize(scroll_nav_check);
		function scroll_nav_check(){
			if(jQuery('html').hasClass('mobile-view') || jQuery('html').hasClass('touch-large-view')){
			scroll_nav_revert();
			return;
		}
		var window_top = jQuery(window).scrollTop();
		if(nav_top < window_top){
			scroll_nav_apply();
		}else{
			scroll_nav_revert();
		}
		}
		function scroll_nav_apply(){
			nav.addClass('sticky');
			nav.before(filler);
		}
		function scroll_nav_revert(){
			nav.removeClass('sticky');
			nav.prev('.scroll-filler').remove();
		}

		/*Sidebar*/
		/*var sidebar = jQuery('#panel-left');
		var scroll_top = sidebar.offset().top;
		var aside_width = sidebar.width();
		scroll_sidebar_check();
		jQuery(window).scroll(scroll_sidebar_check);
		jQuery(window).resize(scroll_sidebar_check);
		function scroll_sidebar_check(){
			if(jQuery('html').hasClass('mobile-view') || jQuery('html').hasClass('touch-large-view')){
				scroll_revert();
				return;
			}
			var aside_height = sidebar.height();
			var window_top = jQuery(window).scrollTop();
			var article_post = jQuery('#panel-center'); 
			var article_post_height = jQuery('#column-container').outerHeight(true) - aside_height + jQuery('#column-container').offset().top;
			
			// accomodate the sticky nav
			window_top += 45;
			if(window_top < article_post.offset().top){
				scroll_revert();
			}else if(window_top > article_post_height){
				scroll_bottom_apply();
			}else if(window_top > article_post.offset().top){
				scroll_top_apply();
			}
		}
		function scroll_top_apply(){
			var left = jQuery('#main-content').offset().left;//$('article.post').offset().left;
			sidebar.css({ position: 'fixed',margin: 0, top: 45, bottom: '', left: left }); 
		}
		function scroll_bottom_apply(){
			var top = jQuery('#column-container').outerHeight(true) - sidebar.height() + jQuery('#column-container').offset().top;
			var left = jQuery('#main-content').offset().left;
			sidebar.css({ position: 'absolute', margin: 0, top: top, left:left });
		}
		function scroll_revert(){
			sidebar.removeAttr('style');
			sidebar.removeClass('sticky_sidebar');
		} */		
	});		