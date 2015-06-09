/**
 * TopNav
 * 
 * BluCommerce top navigation.
 *
 * @package		BluCommerce
 * @subpackage  FrontendClientside
 */
var TopNav = new Class({
	
	menu: null,
	
	initialize: function(menu) {
		this.menu = menu;
		
		/* Add hover events for IE6 */
		if (Browser.Engine.trident4) {
			this.menu.getElements('li.drops').each(function(el) {
				el.addEvent('mouseenter', this.ie6over.bindWithEvent(this, el));
				el.addEvent('mouseleave', this.ie6out.bindWithEvent(this, el));
			}, this);
		}
		
		var topNavAccount = this.menu.getElementById('nav-top-account');
		if (topNavAccount) {
		
			/* Add events to hold account popup open when in use */
			topNavAccount.getElements('input').each(function(input) {
				input.addEvent('focus', this.inputFocus.bindWithEvent(this, topNavAccount));
				input.addEvent('blur', this.inputBlur.bindWithEvent(this, topNavAccount));
			}, this);
			
			/* Add forgot pass opener */
			if ($('nav-top-forgotpass')) { var forgotpassPanel = new PanelSlider($('nav-top-forgotpass-link'), $('nav-top-forgotpass')); }
			
			/* Add form validators */
			topNavAccount.getElements('form').each(function(form) {
				var topnavFormValidator = new FormValidator(form, {
				useTitles: true,
				errorPrefix: '',
				evaluateFieldsOnBlur: false,
				   evaluateFieldsOnChange: false
				});
			}, this);
			
		}
		
	},
	
	inputFocus: function(event, el) {
		el.addClass('open');
		el.getElement('div.nav-item a').addClass('on');
	},
	
	inputBlur: function(event, el) {
		el.removeClass('open');
		el.getElement('div.nav-item a').removeClass('on');
	},
	
	ie6over: function(event, el) {
		event.stop();
		
		el.getElement('div.nav-item a').addClass('hover');
		
		/* Show popup */
		popup = el.getElement('div.nav-popup');
		popup.setStyle('display', 'block');
		
		/* Create and show iframe shim */
		var shim = el.retrieve('shim');
		if (!shim) {
			shim = new IframeShim(popup);
			el.store('shim', shim);
		}
		shim.show();
	},
	
	ie6out: function(event, el) {
		event.stop();
		
		el.getElement('div.nav-item a').removeClass('hover');
		
		/* Hide popup and shim */
		el.getElement('div.nav-popup').setStyle('display', 'none');
		var shim = el.retrieve('shim');
		if (shim) {
			shim.hide();
		}
	}
	
});

/**
 * QuickSearch
 * 
 * BluCommerce quick search.
 *
 * @package		BluCommerce
 * @subpackage  FrontendClientside
 */
var QuickSearch = new Class ({

	searchBox: null,
	searchTarget: null,
	request: null,

	initialize: function(searchBox, searchTarget) {
		this.searchBox = searchBox;
		this.searchTarget = searchTarget;
		
		/* Build request object */
		this.request = new Request.HTML({
			format: 'raw',
			url: SITEURL+'/quicksearch',
			update: this.searchTarget,
			onComplete: this.showResults.bind(this),
			link: 'cancel'
		});
		
		/* Add events */
		this.searchBox.addEvent('keyup', this.doQuickSearch.bindWithEvent(this));
		this.searchTarget.addEvent('outerClick', this.onOuterClick.bindWithEvent(this));
	},

	doQuickSearch: function(event) {
		event.stop();
		this.request.send('searchterm='+this.searchBox.value);
	},
	
	onOuterClick: function(event) {
		if (event.target != this.searchBox) {
			this.hideResults();
		}
	},
	
	showResults: function() {
		this.searchTarget.setStyle('display', 'block');
		if (Browser.Engine.trident4) {
			var shim = this.searchTarget.getParent().retrieve('shim');
			if (!shim) {
				shim = new IframeShim(this.searchTarget);
				this.searchTarget.getParent().store('shim', shim);
			}
			shim.show();
		}
	},
	
	hideResults: function() {
		this.searchTarget.setStyle('display', 'none');
		if (Browser.Engine.trident4) {
			var shim = this.searchTarget.getParent().retrieve('shim');
			if (shim) {
				shim.hide();
			}
		}  
	}
	
});
