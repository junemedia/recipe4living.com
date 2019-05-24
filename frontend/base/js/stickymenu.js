jQuery(document).ready(function () {
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
    if (nav_top < window_top) {
      scroll_nav_apply();
    } else {
      scroll_nav_revert();
    }
  }

  function scroll_nav_apply() {
    nav.addClass('sticky');
    nav.before(filler);
  }

  function scroll_nav_revert() {
    nav.removeClass('sticky');
    nav.prev('.scroll-filler').remove();
  }
});
