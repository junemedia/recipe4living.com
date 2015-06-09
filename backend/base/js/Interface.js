/**
 * IframeShim
 * 
 * Defines IframeShim, a class for obscuring select lists and flash objects in IE.
 * 
 * @license http://clientside.cnet.com/wiki/cnet-libraries#license
 */	
var IframeShim = new Class({
	Implements: [Options, Events],
	options: {
		name: '',
		className: 'iframeShim',
		display: false,
		browsers: (Browser.Engine.trident4 || (Browser.Engine.gecko && !Browser.Engine.gecko19 && Browser.Platform.mac))
	},
	initialize: function (element, options){
		this.setOptions(options);
		this.element = $(element);
		this.makeShim();
		return;
	},
	makeShim: function(){
		if (!this.options.browsers) {
			return this;
		}
		
		/* Determine id */		
		this.id = this.options.name || new $time()+'_shim';
		
		/* Determine z-index */
		if (this.element.getStyle('z-index').toInt()<1 || isNaN(this.element.getStyle('z-index').toInt())) {
			this.element.setStyle('z-index', 5);
		}
		var z = this.element.getStyle('z-index')-1;
		
		/* Create shim */
		this.shim = new Element('iframe', {
			'src': "javascript:'';",
			'frameborder': '0',
			'scrolling': 'no',
			'id': this.id,
			'class': this.options.className,
			'styles': {
				'width': 0,
				'height': 0,
				'left': 0,
				'top': 0,
				'position': 'absolute',
				'z-index': z,
				'border': 'none',
				'filter': 'progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)'
			}
		});
		this.element.store('shim', this);

		/* Inject shim */
		var inject = function(){
			this.shim.inject(this.element, 'after');
			if (this.options.display) {
				this.show();
			} else {
				this.hide();
			}
		};
		if (Browser.Engine.trident && !IframeShim.ready) {
			window.addEvent('load', inject.bind(this));
		} else {
			inject.run(null, this);
		}
	},
	position: function(shim){
		if (!this.options.browsers || !IframeShim.ready) {
			return this;
		}
		
		/* Size shim */
		var size = this.element.getSize();
 		this.shim.setStyles({
			'width': size.x,
			'height': size.y,
			'left': this.element.offsetLeft,
			'top': this.element.offsetTop
		});
		return this;
	},
	hide: function(){
		if (this.options.browsers) {
			this.shim.setStyle('display', 'none');
		}
		return this;
	},
	show: function(){
		if (this.options.browsers) {
			this.shim.setStyle('display', 'block');
		}
		return this.position();
	},
	dispose: function(){
		if (this.options.browsers) {
			this.shim.dispose();
		}
		return this;
	}
});
window.addEvent('load', function(){
	IframeShim.ready = true;
});

/**
 * Waiter
 *
 * Adds a semi-transparent overlay over a dom element with a spinnin ajax icon.
 * 
 * @license http://clientside.cnet.com/wiki/cnet-libraries#license
 * @package BluCommerce
 * @subpackage FrontendClientside
 */
var Waiter = new Class({

	Implements: Options,
	
	options: {
		opacity: 0.8,
		className: 'waiter',
		fade: false,
		fxOptions: {
			duration: 100
		},
		iframeShimOptions: {}
	},
	
	waiter: null,
	waiterFx: null,
	shim: null,
	fx: null,
	
	initialize: function(target, options){
		this.target = $(target) || $(document.body);
		this.setOptions($merge(this.siteOptions, options));
		
		/* Build waiter */
		this.waiter = new Element('div', {
			'styles': {
				position: 'absolute',
				display: 'none'
			},
			'class': this.options.className
		}).inject(document.body);
		
		/* Set fx */
		if (this.fade) {
			this.waiter.set('tween', this.options.fxOptions);
		} 
		
		/* Make iframe shim */
		this.shim = new IframeShim(this.waiter, this.options.iframeShimOptions);		
	},
	
	start: function(element){
		this.stop();
		element = $(element) || $(this.target);
		
		/* Inject and size waiter */
		var size = element.getSize();
		var offsets = element.getOffsets();
		this.active = element;
		this.waiter.setStyles({
			width: size.x,
			height: size.y,
			left: offsets.x,
			top: offsets.y,
			display: 'block',
			opacity: this.options.fade ? 0 : this.options.opacity
		});
		if (this.options.fade) {
			this.waiter.fade(this.options.opacity);
		}
		
		/* Show shim */
		this.shim.show();
		
		return this;
	},
	
	stop: function() {
		if (!this.active) {
			return;
		}
		
		/* Hide waiter */
		this.active = null;
		if (this.options.fade) {
			this.waiter.fade('out');
		} else {
			this.waiter.setStyle('display', 'none');
		}
		this.shim.hide();
		
		return this;
	}
});

Request.HTML = new Class({
	Extends: Request.HTML,
	options: {
		useWaiter: false,
		waiterOptions: {},
		waiterTarget: false
	},
	initialize: function(options){
		this._send = this.send;
		this.send = function(options){
			if(this.waiter) {
				this.waiter.start();
				this._send(options);
			} else {
				this._send(options);
			}
			return this;
		};
		this.parent(options);
		if (this.options.useWaiter && ($(this.options.update) || $(this.options.waiterTarget))) {
			this.waiter = new Waiter(this.options.waiterTarget || this.options.update, this.options.waiterOptions);
			['onComplete', 'onException', 'onCancel'].each(function(event){
				this.addEvent(event, this.waiter.stop.bind(this.waiter));
			}, this);
		}
	}
});

/**
 * BluAccordion
 *
 * Extends Accordion to allow multiple open sections and history.
 * 
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var BluAccordion = new Class({

	Extends: Accordion,
	 
	history: null,
	togglerIds: [],
	 
	options: {
		historyKey: 'accordion',
		useHistory: false,
		historyIds: false,
		show: false,
		opacity: false,
		duration: 500,
		transition: Fx.Transitions.Cubic.easeOut,
		fps: 100,
		allowMultipleOpen: false,
		onActive: function(toggler, element) { toggler.removeClass('closed').addClass('open'); },
		onBackground: function(toggler, element) { toggler.removeClass('open').addClass('closed'); }
	},
 
	initialize: function(togglers, elements, options) {
		this.setOptions(options);
		
		/* Add history */
		if (this.options.useHistory) {
			this.history = HistoryManager.register(
				this.options.historyKey,
				[this.options.display],
				this.onHistoryMatch.bind(this),
				false,
				false);
		}
		
		return this.parent(togglers, elements, this.options);
	},
	
	onHistoryMatch: function(args) {	
		if (!args[0]) { return; }		
		var index = this.options.historyIds ? this.togglerIds.indexOf(args[0]) : args[0];
		this.display(index, true);
	},
	
	addSection: function(toggler, element, pos) {
		
		/* Add to ID mapping */
		if (this.options.historyIds) {
			var id = toggler.get('id').replace(this.options.historyKey+'-', '');
			toggler.erase('id');
			this.togglerIds.push(id);
		}
	
		/* Add hover events for IE6 */
		if (Browser.Engine.trident4) {
			this.togglers.each(function(el) {
				el.addEvent('mouseover', function() { this.addClass('hover'); });
				el.addEvent('mouseout', function() { this.removeClass('hover'); });
			}, this);
		}
		
		return this.parent(toggler, element, pos);
	},
	
	display: function(index) {
		index = ($type(index) == 'element') ? this.elements.indexOf(index) : index;
		if ((this.timer && this.options.wait) || (index === this.previous && !this.options.alwaysHide && !this.options.allowMultipleOpen)) {
			return this;
		}
		
		var obj = {};
		if (this.options.allowMultipleOpen) {
			if ($type(index) != 'array') { index = [index]; }
			index.each(function(i){
				var el = this.elements[i];
				obj[i] = {};
				var hide = (el.offsetHeight > 0);
				this.fireEvent(hide ? 'onBackground' : 'onActive', [this.togglers[i], el]);
				for (var fx in this.effects) {
					obj[i][fx] = hide ? 0 : el[this.effects[fx]];
				}
			}, this);
		} else {
		
			/* Update history value */
			if (this.history) {
				var val = this.options.historyIds ? this.togglerIds[index] : index;
				this.history.setValue(0, val);
			}
		
			this.previous = index;
			this.elements.each(function(el, i){
				obj[i] = {};
				var hide = (i != index) || (this.options.alwaysHide && (el.offsetHeight > 0));
				this.fireEvent(hide ? 'onBackground' : 'onActive', [this.togglers[i], el]);
				for (var fx in this.effects) {
					obj[i][fx] = hide ? 0 : el[this.effects[fx]];
				}
			}, this);
		}		
		return this.start(obj);
	}
});

/**
 * SimpleTabs
 *
 * Unobtrusive Tabs with Ajax
 *
 * @version 1.0
 * @license MIT License
 * @author Harald Kirschner <mail [at] digitarald.de>
 * @copyrigh 2007 Author
 */
var SimpleTabs = new Class({

	Implements: [Events, Options],
	
	options: {
		show: 0,
		selector: '.tab-tab',
		classWrapper: 'tab-wrapper',
		classMenu: 'tab-menu',
		classContainer: 'tab-container',
		onSelect: function(toggle, container, index) {
			toggle.addClass('tab-selected');
			container.setStyle('display', '');
		},
		onDeselect: function(toggle, container, index) {
			toggle.removeClass('tab-selected');
			container.setStyle('display', 'none');
		},
		onRequest: function(toggle, container, index) {
			container.addClass('tab-ajax-loading');
		},
		onComplete: function(toggle, container, index) {
			container.removeClass('tab-ajax-loading');
		},
		onFailure: function(toggle, container, index) {
			container.removeClass('tab-ajax-loading');
		},
		onAdded: Class.empty,
		getContent: null,
		ajaxOptions: {},
		cache: true
	},

	/**
	 * Constructor
	 *
	 * @param {Element} The parent Element that holds the tab elements
	 * @param {Object} Options
	 */
	initialize: function(element, options) {
		this.element = $(element);
		this.setOptions(options);
		this.selected = null;
		this.build();
	},

	build: function() {
		this.tabs = [];
		this.menu = new Element('ul', {'class': this.options.classMenu});
		this.wrapper = new Element('div', {'class': this.options.classWrapper});

		this.element.getElements(this.options.selector).each(function(el) {
			var content = el.get('href') || (this.options.getContent ? this.options.getContent.call(this, el) : el.getNext());
			this.addTab(el.innerHTML, el.title || el.innerHTML, content);
		}, this);
		this.element.empty().adopt(this.menu, this.wrapper);

		if (this.tabs.length) {
			this.select(this.options.show);
		}
	},

	/**
	 * Add a new tab at the end of the tab menu
	 *
	 * @param {String} inner Text
	 * @param {String} Title
	 * @param {Element|String} Content Element or URL for Ajax
	 */
	addTab: function(text, title, content) {
		var grab = $(content);
		var container = (grab || new Element('div'))
			.setStyle('display', 'none')
			.addClass(this.options.classContainer)
			.inject(this.wrapper);
		var pos = this.tabs.length;
		var evt = this.options.hover ? 'mouseenter' : 'click';
		var tab = {
			container: container,
			toggle: new Element('li').grab(new Element('a', {
				href: '#',
				title: title
			}).grab(
				new Element('span', {html: text})
			)).addEvent(evt, this.onClick.bindWithEvent(this, [pos])).inject(this.menu)
		};
		if (!grab && $type(content) == 'string') {
			tab.url = content;
		}
		this.tabs.push(tab);
		return this.fireEvent('onAdded', [tab.toggle, tab.container, pos]);
	},

	onClick: function(event, index) {
		this.select(index);
		event.stop();
	},

	/**
	 * Select the tab via tab-index
	 *
	 * @param {Number} Tab-index
	 */
	select: function(index) {
		if (this.selected === index || !this.tabs[index]) {
			return this;
		}
		if (this.ajax) {
			this.ajax.cancel().removeEvents();
		}
		var tab = this.tabs[index];
		var params = [tab.toggle, tab.container, index];
		if (this.selected !== null) {
			var current = this.tabs[this.selected];
			if (this.ajax && this.ajax.running) {
				this.ajax.cancel();
			}
			params.extend([current.toggle, current.container, this.selected]);
			this.fireEvent('onDeselect', [current.toggle, current.container, this.selected]);
		}
		this.fireEvent('onSelect', params);
		if (tab.url && (!tab.loaded || !this.options.cache)) {
			this.ajax = this.ajax || new Request.HTML();
			this.ajax.setOptions({
				url: tab.url,
				method: 'get',
				update: tab.container,
				onFailure: this.fireEvent.pass(['onFailure', params], this),
				onComplete: function(resp) {
					tab.loaded = true;
					this.fireEvent('onComplete', params);
				}.bind(this)
			}).setOptions(this.options.ajaxOptions);
			this.ajax.send();
			this.fireEvent('onRequest', params);
		}
		this.selected = index;
		return this;
	}

});

/**
 * BluTabs
 *
 * Extends SimpleTabs to allow history.
 * 
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var BluTabs = new Class({

	Extends: SimpleTabs,
	
	history: null,
	tabIds: [],
	
	options: {
		historyKey: 'tab',
		useHistory: false,
		historyIds: false
	},
 
	initialize: function(element, options) {
		this.setOptions(options);
		
		/* Add history */
		if (this.options.useHistory) {
			this.history = HistoryManager.register(
				this.options.historyKey,
				[this.options.show],
				this.onHistoryMatch.bind(this),
				false,
				false);
		}

		return this.parent(element, this.options);
	},
	
	onHistoryMatch: function(args) {
		if (args[0] === undefined) { return; }
		var index = this.options.historyIds ? this.tabIds.indexOf(args[0]) : args[0];
		this.select(index, true);
	},
	
	addTab: function(text, title, content) {
		
		/* Add to ID mapping */
		if (this.options.historyIds) {
			var id = null;
			if (content) {
				id = content.get('id').replace(this.options.historyKey+'-', '');
				content.erase('id');
			}
			this.tabIds.push(id);
		}
		
		return this.parent(text, title, content);
	},
 
	select: function(index, scroll) {
		if (this.selected === index || !this.tabs[index]) { return this; }

		/* Update history value */
		if (this.history) {
			var val = this.options.historyIds ? this.tabIds[index] : index;
			this.history.setValue(0, val);
		}
		
		/* Select tab */
		return this.parent(index);
	}
 
}); 

/**
 * InfoPopups
 *
 * Standard information popups, with content loaded via AJAX.
 * 
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var InfoPopups = new Class({

	Implements: Options,

	links: null,
	
	options: {
		fade: true,
		fadeDuration: 300,
		draggable: true,
		width: 390
	},

	initialize: function(links, options) {
		this.setOptions(options);
		this.links = $$(links);
		
		this.links.each(function(link) {
			var options = link.get('rel');
			if (options) {
				options = JSON.decode(options);
			}
			link.addEvent('click', this.showPopup.bindWithEvent(this, [link, options]));
			link.addEvent('hrefChanged', this.updatePopup.bindWithEvent(this, [link, options]));
		}, this);
	},
	
	showPopup: function(event, link, options) {
	
		var infoWin = link.retrieve('infoWin');
		if (!infoWin) {
			/* Build sticky win */
			infoWin = new StickyWinFx.Ajax(
				$merge(this.options, options, {
					className: 'stickyWin',
					allowMultipleByClass: true,
					relativeTo: link,
					url: link.get('href'),
					handleResponse: function(response){
						var responseScript = "";
						this.Request.response.text.stripScripts(function(script){ responseScript += script; });
						this.setContent(response);
						if (!link.retrieve('shown')) {
							this.show();
							link.store('shown', true);
						}
						if (this.evalScripts) {
							$exec(responseScript);
						}
					},
					requestOptions: {
						format: 'popup',
						evalScripts: true
					}
				}));
			infoWin.update();
			link.store('infoWin', infoWin);
		} else {
			infoWin.show();
		}
		
		event.stop();
	},
	
	updatePopup: function(event, link, options) {
		var infoWin = link.retrieve('infoWin');
		if (infoWin) {
			infoWin.update(link.get('href'));
		}
	}

});

/**
 * AssetPopups
 *
 * Asset/Print page popups.
 * 
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var AssetPopups = new Class({

	Implements: Options,

	links: null,
	
	options: {
		windowKey: null
	},

	initialize: function(links, options){
		this.setOptions(options);
		this.links = links;
		
		this.links.each(function(link){
			link.addEvent('click', this.showPopup.bindWithEvent(this, link));
		}, this);
	},
	
	showPopup: function(event, link) {

		var width = window.getWidth()*0.9;
		if (width > 1024) { width = 1024; }
		var height = window.getHeight()*0.9;
		var left = (window.getWidth() - width) / 2 + window.screenX;
		var top = (window.getHeight() - height) / 2 + window.screenY;
		window.open(link.get('href'), this.options.windowKey, 
			'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=1,resizable=1,'+
			'width='+width+',height='+height+',left='+left+',top='+top).focus();
		
		event.stop();
	}

}); 

/**
 * PageScroll
 *
 * In-situ and cross-request page scrolling.
 * 
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var PageScroll = new Class({

	Extends: Fx.Scroll,
	
	initialize: function(links, options) {
		this.parent(window, options);
		
		links.each(function(link){
		  var anchor = link.href.substr(link.href.indexOf('#')+1);
			link.href = link.href.replace('#', '#scroll-');
			
			var location = window.location.href.match(/^[^#]*/)[0] + '#';
			if (link.href.indexOf(location) === 0) {
				link.addEvent('click', function() {
				  this.toElement(anchor);
				 }.bind(this));
			}
		}, this);

		/* Add smooth scroll by way of history manager */
		this.history = HistoryManager.register(
			'scroll',
			[],
			function(args) {
				if (args[0]) { this.toElement(args[0]); }
			}.bind(this),
			false,
			false);
	}

});

/**
 * ImageSwitcher
 *
 * Multiple image switcher from thumbnail menu.
 * 
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var ImageSwitcher = new Class({

	Implements: [Options, Events],
	
	options: {
		baseUrl: ASSETURL+'/product'
		/*onChange: $empty*/
	},
	
	image: null,
	menu: null,
	links: null,
	
	initialize: function(image, menu, options) {
		this.setOptions(options);
		this.image = image;
		this.menu = menu;
	
		/* Add image switch events */
		this.buildMenu();
	},
	
	buildMenu: function() {
		if (!this.menu) {
			return;
		}
		this.links = this.menu.getElements('a');
		
		this.links.each(function(link) {
			link.addEvent('click', this.onLinkClick.bindWithEvent(this, link));
		}, this);
	},
	
	onLinkClick: function(event, link) {
		link.blur();
		
		/* Get image source */
		var src = link.get('rel');
		
		/* Switch image */
		this.image.set('src', src);
		this.fireEvent('change', src);
		
		/* Highlight current image link */
		this.menu.getElements('li').removeClass('on');
		link.getParent('li').addClass('on');	
			
		event.stop();
	},
		
	updateImages: function(images, thumbX, thumbY, thumbZc, largeX, largeY, largeZc) {
		
		/* Add new items to menu */
		if (this.menu) {
			this.menu.empty();
		}
		images.each(function(image, index) {
		
			/* Determine URLs */
			var largeUrl = this.options.baseUrl+'/'+largeX+'/'+largeY+'/'+largeZc+'/'+image.fileName;
			var thumbUrl = this.options.baseUrl+'/'+thumbX+'/'+thumbY+'/'+thumbZc+'/'+image.fileName;
		
			/* Switch large image */
			if (index == 0) {
				this.image.set('src', largeUrl);
				this.fireEvent('change', largeUrl);
			}
			
			/* Add li to menu */
			if (this.menu) {
				var el = new Element('li', {
					'html': '<a rel="'+largeUrl+'" href="'+this.location+'?image='+image.fileName+'"><img src="'+thumbUrl+'" alt="'+image.description+'" /></a>'
				}).inject(this.menu);
			}				
		}, this);
		
		/* Add switch events */
		if (this.menu) {
			this.buildMenu();
		}
	}
	
}); 

/**
 * RangeSlider
 * 
 * Class for creating horizontal and vertical range slider controls.
 *
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var RangeSlider = new Class({

	Implements: [Events, Options],

	options: {/*
		onChange: $empty,
		onComplete: $empty,*/
		snap: true,
		offset: 0,
		range: [],
		steps: 100,
		mode: 'horizontal',
		values: []
	},

	initialize: function(track, knobs, options) {
		this.setOptions(options);
		
		this.previousChange = $A(this.options.values);
		this.previousEnd = $A(this.options.values);
		this.step = $A(this.options.values);
		
		/* Store reference to track and knobs */
		this.track = track;
		this.knobs = knobs;
		
		/* Add mouse events to track */
		this.track.addEvent('mousedown', this.clickedTrack.bindWithEvent(this));
		
		/* Get positioning details */
		var offset, size, limit = {}, modifiers = {'x': false, 'y': false};
		switch (this.options.mode){
			case 'vertical':
				this.axis = 'y';
				this.property = 'top';
				offset = 'offsetHeight';
				size = 'height';
				break;
			case 'horizontal':
				this.axis = 'x';
				this.property = 'left';
				offset = 'offsetWidth';
				size = 'width';
		}
		
		/* Calculate half knob size, and required space at end of track */
		this.half = this.knobs[0][offset] / 2;
		var trackEnd = this.knobs[0][offset] + (this.options.offset * 2);
		
		/* Calculate ranges */
		this.min = $chk(this.options.range[0]) ? this.options.range[0] : 0;
		this.max = $chk(this.options.range[1]) ? this.options.range[1] : this.options.steps;
		this.range = this.max - this.min;
		this.full = this.track[offset] - trackEnd;
		this.steps = this.options.steps || this.full;
		
		/* Shrink track to fit number of steps exactly in snap mode */
		if (this.options.snap) {
			this.full = this.steps * Math.floor((this.track[offset] - trackEnd) / this.options.steps);
			this.track.setStyle(size, this.full + trackEnd);
  		}
		
		/* Caulculate step sizes */
		this.stepSize = Math.abs(this.range) / this.steps;
		this.stepWidth = this.stepSize * this.full / Math.abs(this.range);
		
		/* Set initial knob positions */
		this.step.each(function(step, idx) {
		    this.set(idx, step);
		}, this);
		
		/* Add knob draggers */
		this.draggers = [];
		modifiers[this.axis] = this.property;
		this.knobs.each(function(knob, idx) {
			var drag = new Drag(knob, {
				snap: 0,
				modifiers: modifiers,
				limit: limit,
				onDrag: this.draggedKnob.bindWithEvent(this, idx),
				onStart: this.draggedKnob.bindWithEvent(this, idx),
				onComplete: function(drag) {
					this.draggedKnob(drag, idx);
					this.end(idx);
				}.bindWithEvent(this, idx)
			});
			if (this.options.snap) {
				drag.options.grid = Math.ceil(this.stepWidth);
   			}
			this.draggers.push(drag);
		}, this);
		this.updateLimits();
	},
	
	set: function(idx, step) {
	    this.step[idx] = step;
		this.knobs[idx].setStyle(this.property, this.toPosition(this.step[idx]));
		this.checkStep(idx);
		this.end(idx);
		return this;
	},

	clickedTrack: function(event) {
		var position = event.page[this.axis] - this.track.getPosition()[this.axis] - this.half;
		var step = this.toStep(position);
		
		/* Find closest knob */
		var diff = this.max, idx = 0;
		this.step.each(function(v, i) {
		    var abs = Math.abs(step - v);
			if (abs < diff) { diff = abs; idx = i; }
		});
		
		/* And move it */
		this.step[idx] = step;
		if (this.options.snap) {
		    position = this.toPosition(step);
		}
		this.knobs[idx].setStyle(this.property, position);
		this.checkStep(idx);
		this.end(idx);
	},
	
	draggedKnob: function(knob, idx) {
		var position = this.draggers[idx].value.now[this.axis];
		this.step[idx] = this.toStep(position);
		this.checkStep(idx);
	},

	checkStep: function(idx) {
		if (this.previousChange[idx] != this.step[idx]){
			this.previousChange[idx] = this.step[idx];
			this.fireEvent('change', [idx, this.step[idx]]);
		}
	},

	end: function(idx) {	
		if (this.previousEnd[idx] !== this.step[idx]){
			this.previousEnd[idx] = this.step[idx];
			this.updateLimits();
			this.fireEvent('complete', [idx, this.step[idx]]);
		}
	},
	
	updateLimits: function() {
	    this.draggers.each(function(drag, idx) {
			var min = $defined(this.step[idx-1]) ? this.toPosition(this.step[idx-1]+this.stepSize) : - this.options.offset;
			var max = $defined(this.step[idx+1]) ? this.toPosition(this.step[idx+1]-this.stepSize) : this.full;
			this.draggers[idx].options.limit[this.axis] = [min, max];
		}, this);
	},

	toStep: function(position) {
		var dir = this.range < 0 ? -1 : 1;
		var step = (position + this.options.offset) * this.stepSize / this.full * this.steps;
		step = this.options.steps ? Math.round(step -= step % this.stepSize) : step;
		return Math.round(this.min + dir * step);
	},

	toPosition: function(step) {
		return (this.full * Math.abs(this.min - step)) / (this.steps * this.stepSize) - this.options.offset;
	}

});

/**
 * MinMaxSlider
 * 
 * Extends range slider for min max only sliders.
 *
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var MinMaxSlider = new Class({

	Extends: RangeSlider,
	
	options: {
		range: [],
		steps: 100,
		sliderClass: 'slider',
		knobClass: 'knob',
		valueTextPrefix: '',
		valueDecimals: 0
	},
	
	inputs: [],
	knobs: [],
	valueText: [],
									
	initialize: function(container, minInput, maxInput, options) {
		this.setOptions(options);
		this.container = $(container);
		
		/* For each input, extract the current value and create a replacement hidden input */ 
		var values = [];
		[$(minInput), $(maxInput)].each(function(el) {
		
			/* Get value from input/select */
			var value;
			if (el.get('tag') == 'select') {
		    	value = el.options[el.selectedIndex].get('value');
		    } else {
		    	value = el.get('value');
		    }
		    value = value.toFloat();
		    
		    /* Push value onto stack and create replacement input */
		    values.push(value);
		    this.inputs.push(new Element('input', {
				type: 'hidden',
				name: el.get('name'),
				value: value
			}));
		}, this);
	
		/* Add track */
		this.track = new Element('div', {
			'class': this.options.sliderClass
		});
		
		/* Add knobs and value text*/
		values.each(function(value, idx) {
			this.knobs.push(new Element('div', {
				'class': this.options.knobClass
			}));
			
			this.valueText.push(new Element('div', {
				'class': (idx === 0) ? 'min' : 'max',
				'html': '<span>' + this.options.valueTextPrefix + values[idx].format(this.options.valueDecimals)+'</span>'
			}));
			
		}, this);
		this.track.adopt(this.valueText, this.knobs);
		
		/* Inject slider and inputs into container */
		this.container.empty().adopt(this.inputs, this.track);
		
		/* Add completion and change events */
		this.addEvents({
			'onComplete': function(idx, step) {
					this.inputs[idx].set('value', step);
				}.bind(this),
			'onChange': function(idx, step) {
					this.valueText[idx].getElement('span').set('html', this.options.valueTextPrefix + step.format(this.options.valueDecimals));
				}.bind(this)
		});
		
		/* Create range slider */
		this.options.values = values;
		return this.parent(this.track, this.knobs, this.options);
	}

});

/**
 * ShoeSize
 * 
 * Shoe size switchers.
 *
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var ShoeSize = new Class({
	
	Implements: Options,
	
	options: {
		type: null,
		mapping: null	
	},
	
	containers: null,
	type: null,
	mapping: null,
	
	fallbackUrl: null,
	
	initialize: function(containers, options) {
		this.containers = containers;
		this.setOptions(options);
		this.type = this.options.type;
		this.mapping = $H(this.options.mapping);
		this.mapping.each(function(value1, key1, hash1) {
			valhash1 = $H(value1);
			valhash1.each(function(value2, key2, hash2) {
				valhash2 = $H(value2);
				valhash2.each(function(value3, key3, hash3) {
					valhash3 = $H(value3);
					hash3.set(key3, valhash3);
				});
				hash2.set(key2, valhash2);
			}); 
			hash1.set(key1, valhash1);
		});
		
		/* Add click events for selectors */
		this.containers.each(function(container) {
			container.getElements('li').each(function(el) {
				var type = el.get('class');
				el.getElement('a').addEvent('click', this.changeShoeSize.bindWithEvent(this, type));
			}, this);
		}, this);
		
		/* Add size alteration events */
		$$('span.blu-shoesize').each(function(el) {
			el.getElement('input.size-value').addEvent('change', this.updateDisplay.bind(this, el));
		}, this);
	},

	changeShoeSize: function(event, type) {
		this.type = type;
				
		$$('span.blu-shoesize').each(function(el) {
			this.updateDisplay(el);
		}, this);
		
		/* Update current indicator on selectors */
		this.containers.each(function(container) {
			container.getElements('li a').removeClass('current');
			container.getElement('li.'+type.toUpperCase()+' a').addClass('current');
		}, this);
		
		/* Save current type in session */
		var jsonRequest = new Request.JSON({
			format: 'json',
			url: SITEURL+'/',
			method: 'post'
		}).send('type='+type+'&task=shoesizetype_save');
		
		event.stop();
	},
	
	updateDisplay: function(el) {
	
		var sizeInput = el.getElement('input.size-value');
		var genderInput = el.getElement('input.size-gender');
		var brandInput = el.getElement('input.size-brand');
		var span = el.getElement('span');
		
		/* Get values */
		var size = sizeInput.get('value');
		var gender = genderInput.get('value');
		var brandId = brandInput.get('value');
		
		if (!size) {
			return;
		}
		
		/* Determine size string */
		if (this.mapping.has('b'+brandId)) {
			brand = ('b'+brandId);
		} else {
			brand = ('b0');
		}
		
		if (!this.mapping.get(brand).has(gender)) {
			brand = 'b0';
		}
		
		var shoeSize = this.mapping.get(brand).get(gender).get(this.type).get(size);
		if (shoeSize == null) {
			var shoeSize = this.mapping.get('b0').get(gender).get(this.type).get(size);
		}
		shoeSize = String(shoeSize).replace(/\.5/, '&frac12;');
		
		/* Update contents */
		span.set('html', shoeSize);
	}
	
});

/**
 * MooScroller
 * 
 * Recreates the standard scrollbar behavior for elements with overflow but using 
 * DOM elements so that the scroll bar elements are completely styleable by css.
 *
 * @license http://clientside.cnet.com/wiki/cnet-libraries#license
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var MooScroller = new Class({
	Implements: [Options, Events],
	options: {
		maxThumbSize: 10,
		mode: 'vertical',
		width: 0, //required only for mode: horizontal
		scrollSteps: 10,
		wheel: true,
		scrollLinks: {
			forward: 'scrollForward',
			back: 'scrollBack'
		},
		hideWhenNoOverflow: true
		/*onScroll: $empty,
		onPage: $empty*/
	},

	initialize: function(content, knob, options){
		this.setOptions(options);
		this.horz = (this.options.mode == "horizontal");

		this.content = $(content).setStyle('overflow', 'hidden');
		this.knob = $(knob);
		this.track = this.knob.getParent();
		this.setPositions();
		
		if (this.horz && this.options.width) {
			this.wrapper = new Element('div');
			this.content.getChildren().each(function(child){
				this.wrapper.adopt(child);
			}, this);
			this.wrapper.inject(this.content).setStyle('width', this.options.width);
		}

		this.bound = {
			'start': this.start.bind(this),
			'end': this.end.bind(this),
			'drag': this.drag.bind(this),
			'wheel': this.wheel.bind(this),
			'page': this.page.bind(this)
		};

		this.position = {};
		this.mouse = {};
		this.update();
		this.attach();
		
		var clearScroll = function (){
			$clear(this.scrolling);
		}.bind(this);
		['forward','back'].each(function(direction) {
			var lnk = $(this.options.scrollLinks[direction]);
			if (lnk) {
				lnk.addEvents({
					mousedown: function() {
						this.scrolling = this[direction].periodical(50, this);
					}.bind(this),
					mouseup: clearScroll.bind(this),
					click: clearScroll.bind(this)
				});
			}
		}, this);
		this.knob.addEvent('click', clearScroll.bind(this));
		window.addEvent('domready', function(){
			try {
				$(document.body).addEvent('mouseup', clearScroll.bind(this));
			}catch(e){}
		}.bind(this));
	},
	setPositions: function(){
		[this.track, this.knob].each(function(el){
			if (el.getStyle('position') == 'static') {
				el.setStyle('position', 'relative');
			}
		});

	},
	toElement: function(){
		return this.content;
	},
	update: function(){
		var plain = this.horz?'Width':'Height';
		this.contentSize = this.content['offset'+plain];
		this.contentScrollSize = this.content['scroll'+plain];
		this.trackSize = this.track['offset'+plain];

		this.contentRatio = this.contentSize / this.contentScrollSize;

		this.knobSize = (this.trackSize * this.contentRatio).limit(this.options.maxThumbSize, this.trackSize);

		if (this.options.hideWhenNoOverflow) {
			this.hidden = this.knobSize == this.trackSize;
			this.track.getParent().setStyle('display', this.hidden?'none':'block');
		}
		
		this.scrollRatio = this.contentScrollSize / this.trackSize;
		this.knob.setStyle(plain.toLowerCase(), this.knobSize);

		this.updateThumbFromContentScroll();
		this.updateContentFromThumbPosition();
	},

	updateContentFromThumbPosition: function(){
		this.content[this.horz?'scrollLeft':'scrollTop'] = this.position.now * this.scrollRatio;
	},

	updateThumbFromContentScroll: function(){
		this.position.now = (this.content[this.horz?'scrollLeft':'scrollTop'] / this.scrollRatio).limit(0, (this.trackSize - this.knobSize));
		this.knob.setStyle(this.horz?'left':'top', this.position.now);
	},

	attach: function(){
		this.knob.addEvent('mousedown', this.bound.start);
		if (this.options.scrollSteps) {
			this.content.addEvent('mousewheel', this.bound.wheel);
		}
		this.track.addEvent('mouseup', this.bound.page);
	},

	wheel: function(event){
		if (this.hidden) {
			return;
		}
		this.scroll(-(event.wheel * this.options.scrollSteps));
		this.updateThumbFromContentScroll();
		event.stop();
	},

	scroll: function(steps){
		steps = steps||this.options.scrollSteps;
		this.content[this.horz?'scrollLeft':'scrollTop'] += steps;
		this.updateThumbFromContentScroll();
		this.fireEvent('onScroll', steps);
	},
	forward: function(steps){
		this.scroll(steps);
	},
	back: function(steps){
		steps = steps||this.options.scrollSteps;
		this.scroll(-steps);
	},

	page: function(event){
		var axis = this.horz?'x':'y';
		var forward = (event.page[axis] > this.knob.getPosition()[axis]);
		this.scroll((forward?1:-1)*this.content['offset'+(this.horz?'Width':'Height')]);
		this.updateThumbFromContentScroll();
		this.fireEvent('onPage', forward);
		event.stop();
	},

	
	start: function(event){
		var axis = this.horz?'x':'y';
		this.mouse.start = event.page[axis];
		this.position.start = this.knob.getStyle(this.horz?'left':'top').toInt();
		document.addEvent('mousemove', this.bound.drag);
		document.addEvent('mouseup', this.bound.end);
		this.knob.addEvent('mouseup', this.bound.end);
		event.stop();
	},

	end: function(event){
		document.removeEvent('mousemove', this.bound.drag);
		document.removeEvent('mouseup', this.bound.end);
		this.knob.removeEvent('mouseup', this.bound.end);
		event.stop();
	},

	drag: function(event){
		var axis = this.horz?'x':'y';
		this.mouse.now = event.page[axis];
		this.position.now = (this.position.start + (this.mouse.now - this.mouse.start)).limit(0, (this.trackSize - this.knobSize));
		this.updateContentFromThumbPosition();
		this.updateThumbFromContentScroll();
		event.stop();
	}
});

/**
 * BluScrollAreas
 * 
 * Creates scrollable areas with MooScroller scroll bars
 *
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var BluScrollAreas = new Class({

	Implements: Options,
	
	options: {},
	
	areas: null,
	
	initialize: function(areas, options) {
		this.areas = $$(areas);
		this.setOptions(options);
		
		this.areas.each(function(content, index) {
			var sizes = this.getSizes(content);
			
			content.zsizex = sizes.size.x;
			content.zsizey = sizes.size.y;
			content.zscrollSizex = sizes.scrollSize.x;
			content.zscrollSizey = sizes.scrollSize.y;
			content.zoffsetWidth = content.offsetWidth;
			
			/* Add scrollbars */			
			if (sizes.require.x || sizes.require.y) {
				/* Wrap content in scroll area */
				container = new Element('div', {
					'class': 'scrollarea',
					'styles': {
						'width': content.offsetWidth,
						'height': content.offsetHeight
					}
				}).wraps(content, 'top');
				
				/* Build scrollbars */
				if (sizes.require.x) {
					this.addScrollbar(container, content, 'horizontal');
				}
				if (sizes.require.y) {
					this.addScrollbar(container, content, 'vertical');
				}
			}
			
		}, this);
	},
	
	addScrollbar: function(container, content, mode) {
		var scrollbar = new Element('div', { 'class': 'scrollbar-'+mode });
		if (Browser.Engine.trident4) {
			if (mode == 'vertical') {
				scrollbar.setStyle('height', content.offsetHeight);
			} else {
				scrollbar.setStyle('width', content.offsetWidth);
			}
		}
		var scrollbarTrack = new Element('div', { 'class': 'scrollbar-track' });
		var scrollbarKnob = new Element('div', { 'class': 'scrollbar-knob' });
		container.adopt(scrollbar.adopt(scrollbarTrack.adopt(scrollbarKnob)));
		var scroller = new MooScroller(content, scrollbarKnob, {
			mode: mode
		});
	},
	
	getSizes: function(content) {
		
		/* Get sizes */
		var size = content.getSize();
		var scrollSize = content.getScrollSize();
		
		/* Return sizes */
		return {
			size: size,
			scrollSize: scrollSize,
			require: {
				x: scrollSize.x > size.x,
				y: scrollSize.y > size.y
			}
		};
	}
});

/**
 * Toggle between two panels by clicking on two corresponding links
 */
var PanelToggler = new Class({

	Implements: Options,

	container: null,
	
	panel1: null,
	panel1link: null,
	
	panel2: null,
	panel2link: null,
	
	options: {
		fxOptions: {
			duration: 800,
			fps: 100
		},
		focus: true
	},
	
	initialize: function(container, panel1, panel1link, panel2, panel2link, options) {
		this.container = container;
		this.panel1 = panel1;
		this.panel1link = panel1link;
		this.panel2 = panel2;
		this.panel2link = panel2link;
		
		this.setOptions(options);
		
		/* Hide panel 2 */
		panel2.setStyle('display', 'none');
		
		/* Set up container tween */
		this.container.set({
			'tween': this.options.fxOptions,
			'styles': {
				'overflow': 'hidden'
			}
		});
		
		/* Add link click events */
		this.panel1link.addEvent('click', this.togglePanels.bindWithEvent(this, [panel1, panel2]));
		this.panel2link.addEvent('click', this.togglePanels.bindWithEvent(this, [panel2, panel1]));
			
	},
	
	togglePanels: function(event, newPanel, oldPanel) {
		
		/* Fix container at current size */
		var currentSize = this.container.getSize();
		this.container.setStyle('height', currentSize.y);
		
		/* Toggle display of panels */
		oldPanel.setStyle('display', 'none');
		newPanel.setStyle('display', 'block');
		
		/* Resize container to fit new panel */
		var newSize = newPanel.getSize();
		this.container.get('tween').start('height', newSize.y).chain(function() {
			this.container.setStyle('height', 'auto');
			
			/* re-apply hasLayout for IE6 */
			if (Browser.Engine.trident4) {
				this.container.setStyle('zoom', '1');
			}
			
			/* Focus first input element */
			if (this.options.focus) {
				var input = newPanel.getElement('input');
				if (input) { input.focus(); }
			}
		}.bind(this));
				
		event.stop();
	}

});

/**
 * Show and hide a panel depending on a check box state
 */
var CheckPanel = new Class({

	Implements: Options,
	
	panel: null,
	check: null,
	
	size: null,
	
	options: {
		fxOptions: {
			duration: 800,
			fps: 100
		},
		focus: true
	},
	
	initialize: function(panel, check, options) {
		this.setOptions(options);
		this.panel = panel;
		this.check = check;
		
		/* Get initial panel size */
		this.size = this.panel.getSize();
		
		/* Set up panel tween */
		this.panel.set({
			'tween': this.options.fxOptions,
			'styles': {
				'overflow': 'hidden'
			}
		});
		
		/* Hide panel if checkbox not ticked */
		if (!check.get('checked')) {
			this.panel.setStyle('display', 'none');
		}
		
		/* Add check click events */
		this.check.addEvent('click', this.onCheckClick.bindWithEvent(this));
		
		/* Attach cancel link events */
		this.panel.getElements('a.cancel').each(function(link) {
			link.setStyle('display', 'block');
			link.addEvent('click', this.onCancelClick.bindWithEvent(this));
		}, this);
	},
	
	onCheckClick: function(event) {
		if (this.check.getProperty('checked')) {
			this.showPanel();
		} else {
			this.hidePanel();
		}	
	},
	
	onCancelClick: function(event) {
		this.check.setProperty('checked', false);
		this.check.fireEvent('updated');
		this.hidePanel();
		event.stop();
	},
	
	showPanel: function() {
		this.panel.setStyle('height', '0');
		this.panel.setStyle('display', 'block');
		this.panel.get('tween').start('height', this.size.y).chain(function() {
			this.panel.setStyle('height', 'auto');
			
			/* re-apply hasLayout for IE6 */
			if (Browser.Engine.trident4) {
				this.panel.setStyle('zoom', '1');
			}
			
			/* Focus first input element */
			if (this.options.focus) {
				var input = this.panel.getElement('input');
				if (input) { input.focus(); }
			}
		}.bind(this));
	},
	
	hidePanel: function() {
		this.size = this.panel.getSize();
		this.panel.setStyle('height', this.size.y);
		this.panel.get('tween').start('height', 0).chain(function() {
			this.panel.setStyle('display', 'none');
		}.bind(this));
	}
	
});

/**
 * Slide in panel when a link is clicked (and hide the link)
 */ 
var PanelSlider = new Class({
	
	Implements: Options,
	
	options: {
		fxOptions: {
			duration: 800,
			fps: 100
		},
		focus: true,
		hidePanel: true,
		hideLink: true,
		linkDisplay: 'block'
	},
	
	link: null,
	panel: null,
	
	shown: false,
	
	initialize: function(link, panel, options) {
		this.link = $(link);
		this.panel = $(panel);
		
		this.setOptions(options);
		
		/* Show link */
		this.link.setStyle('display', this.options.linkDisplay);
		
		/* Set up tween and hide overflow */
		this.panel.set({
			'tween': this.options.fxOptions,
			'styles': {
				'overflow': 'hidden'
			}
		});
		
		/* Hide panel */
		if (this.options.hidePanel) {
			this.panel.setStyles({
				'height': '0',
				'visibility': 'hidden'
			});
			
			/* Disable inputs */
			this.panel.getElements('input, select').set('disabled', true).addClass('ignoreValidation');
		}
		
		/* Add link click events */
		this.link.addEvent('click', function(event) {
			this.togglePanel();
			event.stop();
		}.bindWithEvent(this));

		/* Show close links and attach event */		
		this.panel.getElements('a.close').set({
			'events': {
				'click': function(event) {
					this.hidePanel();
					event.stop();
				}.bindWithEvent(this)
			},
			'styles': {
				'display': 'block'
			}
		});		
	},
	
	togglePanel: function(event) {
		if (!this.shown) {
			this.showPanel();
		} else {
			this.hidePanel();
		}
	},
	
	showPanel: function() {
	
		/* Hide link */
		if (this.options.hideLink) {
			this.link.setStyle('display', 'none');
		}
		
		/* Get panel size */
		var size = this.panel.getScrollSize();
		
		/* Enable inputs */
		this.panel.getElements('input, select').set('disabled', false).removeClass('ignoreValidation');
		
		/* Show panel and slide in */
		this.panel.setStyles({
			'visibility': 'visible'
		});			
		this.panel.get('tween').start('height', size.y).chain(function() {
			this.panel.setStyles({
				'height': 'auto',
				'overflow': 'visible'
			});
			
			/* re-apply hasLayout for IE6 */
			if (Browser.Engine.trident4) {
				this.panel.setStyle('zoom', '1');
			}
			
			/* Focus first input element */
			if (this.options.focus) {
				var input = this.panel.getElement('input');
				if (input) { input.focus(); }
			}
		}.bind(this));
		this.shown = true;
	},
	
	hidePanel: function() {
	
		/* Re-show link */
		this.link.setStyle('display', this.options.linkDisplay);
		
		/* Slide panel out and hide */
		this.panel.setStyle('overflow', 'hidden');
		this.panel.get('tween').start('height', 0).chain(function() {
			this.panel.setStyles({
				'visibility': 'hidden'
			});
			
			/* Disable inputs */
			this.panel.getElements('input, select').set('disabled', true).addClass('ignoreValidation');
			
		}.bind(this));
		
		this.shown = false;
	}
	
});

/**
 *	Rating.
 */
var Ratings = new Class({
	
	/**
	 *	Implements.
	 */
	Implements: [Options, Chain],
	
	/**
	 *	The star elements.
	 */
	stars: [],
	
	/**
	 *	Initial status.
	 */
	options: {
		current: 3,
		hovered: false,
		disabled: false
	},
	
	/**
	 *	Constructor.
	 *
	 *	@args (Element) element: the container to search for stars within.
	 *	@args (JSON encoded string) initStatus: the status of the stars to show initially.
	 */
	initialize: function(element, options){
		
		/* Set up */
		this.setOptions(options);
		this.stars = $(element).getElements('a.star');
		
		/* Add event listeners */
		this.stars.each(function(star){
			star.addEvents({
				mouseenter: this.onMouseEnter.bindWithEvent(this, star),
				mouseleave: this.onMouseLeave.bindWithEvent(this, star),
				click: this.onMouseClick.bindWithEvent(this, star)
			});
		}, this);
		
		/* Initial setup */
		this.setUnhovered();
		
	},
	
	/**
	 *	What happens when a star is hovered over.
	 */
	onMouseEnter: function(event, star){
		event.stop();
		this.options.hovered = true;
		this.setHovered(star);
	},
	
	/**
	 *	What happens when a star is hovered out.
	 */
	onMouseLeave: function(event, star){
		event.stop();
		this.options.hovered = false;
		this.setUnhovered.create({
			delay: 1000, 
			bind: this
		}).attempt();
	},
	
	/**
	 *	What happens when a star is clicked upon.
	 */
	onMouseClick: function(event, star){
		this.options.disabled = true;
		this.submitRating(star);
	},
	
	/**
	 *	Set stars when one is hovered.
	 */
	setHovered: function(star){
		
		// Check if disabled
		if (this.options.disabled){
			return false;
		}
	
		// Get index of hovered star.
		var hoveredIndex = this.stars.indexOf(star);
		
		// Set stars
		this.stars.each(function(star, index){
			if (index < hoveredIndex){
				this._setFilled(index);
			} else if (index == hoveredIndex) {
				this._setHovered(index);
			} else {
				this._setUnfilled(index);
			}
		}, this);
		
	},
	
	/**
	 *	Set stars when unhovered.
	 */
	setUnhovered: function(){
		
		// Check if disabled/hovered/clicked.
		if (this.options.disabled || this.options.hovered || this.options.clicked){
			return false;
		}
		
		// Fill in default stars
		this.stars.each(function(star, index){
			if (index <= this.options.current - 1){
				this._setFilled(index);
			} else {
				this._setNormal(index);
			}
		}, this);
		
	},
	
	/**
	 *	Submit a rating.
	 */
	submitRating: function(star){
		
		// Check if disabled
		if (this.options.disabled){
			return false;
		}
		
		// Get index of clicked star.
		var index = this.stars.indexOf(star);
		var value = index + 1;
		
		// Do some AJAX here.
		window.location = '?task=vote&rating='+value;
		
	},
	
	/**
	 *	Set a star as 'normal'.
	 */
	_setNormal: function(index){
		if (this.stars.length < index){ return false; }
		var star = this.stars[index].getElement('img');
		star.set('src', SITEASSETURL+'/images/site/icon-star_off.png');
		star.get('tween', {duration: 2500}).start('opacity', 1);
		return true;
	},
	
	/**
	 *	Set a star as 'unfilled' - not the same as 'normal'.
	 */
	_setUnfilled: function(index){
		if (this.stars.length < index){ return false; }
		var star = this.stars[index].getElement('img');
		star.set('src', SITEASSETURL+'/images/site/icon-star_off.png');
		star.get('tween', {duration: 200, link: 'cancel'}).set('opacity', 0.5);
		return true;
	},
	
	/**
	 *	Set a star as 'filled'.
	 */
	_setFilled: function(index){
		if (this.stars.length < index){ return false; }
		var star = this.stars[index].getElement('img');
		star.set('src', SITEASSETURL+'/images/site/icon-star.png');
		star.get('tween', {duration: 500, link: 'cancel'}).start('opacity', 1);
		return true;
	},
	
	/**
	 *	Set a star as 'hovered'.
	 */
	_setHovered: function(index){
		if (this.stars.length < index){ return false; }
		var star = this.stars[index].getElement('img');
		star.set('src', SITEASSETURL+'/images/site/icon-star.png');
		star.get('tween', {duration: 200, link: 'cancel'}).start('opacity', 1);
		return true;
	}
	
});

/**
 *	Autocompleter
 *
 *	@package BluApplication
 *	@subpackage BackendJS
 */
var Autocompleter = new Class({
	
	/**
	 *	Options
	 */
	Implements: Options,
	
	/**
	 *	Options
	 */
	options: {
		emptyText: 'No results found',
		menuClass: 'autocompleter-menu',
		itemClass: 'autocompleter-item',
		request: {
			url: SITEURL,
			link: 'cancel',
			data: {
				format: 'json'
			}
		},
		typeDelay: 300,	// Milliseconds to wait for the user to finish typing
		hideDelay: 0,	// Milliseconds to wait before hiding the menu again
		queryKey: 'value',
		loadingOpacity: 0.2,	// Opacity to set when "loading" results
		callback: $empty	// Callback function for JSON success
	},
	
	/**
	 *	Input box
	 */
	input: null,
	
	/**
	 *	Dropdown
	 */
	dropdown: null,
	
	/**
	 *	Request
	 */
	request: null,
	
	/**
	 *	Timer for the user typing
	 */
	typeTimer: null,
	
	/**
	 *	Timer for the menu display
	 */
	displayTimer: null,
	
	/**
	 *	Constructor
	 *
	 *	@param Element Input box
	 *	@param object Options
	 */
	initialize: function(input, options) {
		this.input = $(input);
		this.setOptions(options);
		
		this.build();
		
		this.options.request.onSuccess = this.onSuccess.bind(this);
		this.request = new Request.JSON(this.options.request);
	},
	
	/**
	 *	Build the dropdown menu and other bits and bobs
	 */
	build: function() {
		this.input.set('autocomplete', 'off');	// Turn off native autocompleter.
		
		// Build elements
		this.dropdown = new Element('ul', {'class': this.options.menuClass});
		this.hide();
		this.dropdown.inject(this.input, 'after');
		
		// Add event listeners
		this.input.addEvents({
			'keyup': this.onChange.bindWithEvent(this)
		});
	},
	
	/**
	 *	Event handler for value changes
	 *
	 *	@param Event
	 */
	onChange: function(event) {
		event.stop();
		
		this.stop();
		
		// Keys to ignore
		if (['tab', 'esc', 'home', 'end', 'left', 'right'].contains(event.key) || event.shift || event.control || event.alt) {
			this.hide();
			return;
		}
		
		this.typeTimer = this.query.delay(this.options.typeDelay, this);
	},
	
	/**
	 *	Display menu
	 */
	show: function() {
		this.dropdown.fade('in');
		
		$clear(this.displayTimer);
		this.displayTimer = this.hide.delay(this.options.hideDelay, this);
	},
	
	/**
	 *	Hide menu
	 */
	hide: function() {
		this.dropdown.fade('hide');
	},
	
	/**
	 *	Show "loading" on menu
	 */
	loading: function() {
		this.dropdown.fade(this.options.loadingOpacity);
	},
	
	/**
	 *	Update menu with items
	 *
	 *	@param array Items
	 */
	update: function(items) {
		this.dropdown.empty();
		
		// Append items
		if (items.length) {
			items.each(function (item) {
				this.dropdown.adopt(this._create(item.value, item.html || item.value));
			}, this);
			
		// Append empty item
		} else {
			this.dropdown.adopt(this._create('', this.options.emptyText));
		}
	},
	
	/**
	 *	Create a display item from data
	 *
	 *	@param string Input value
	 *	@param string Display HTML
	 *	@return Element 
	 */
	_create: function(value, html) {
		
		// Create element
		var item = new Element('li', {
			'class': this.options.itemClass,
			'title': value,
			'html': html
		});
		item.store('value', value);
		
		// Add event listeners (but not for the Empty item)
		if (value) {
			item.addEvents({
				'click': this.onSelect.bindWithEvent(this, item),
				'keyup': function(event, item) {
					if (event.key == 'enter') {
						this.onSelect.bind(this, [event, item]);
					}
				}.bindWithEvent(this, item)
			});
			
		// Empty item just closes when clicked
		} else {
			item.addEvent('click', this.hide.bind(this));
		}
		
		// Return
		return item;
	},
	
	/**
	 *	Event handler for when an item is selected
	 *
	 *	@param Event
	 *	@param Element Item selected
	 */
	onSelect: function(event, item) {
		if (event) {
			event.stop();
		}
		
		// Update the input box value
		var value = item.retrieve('value');
		this.input.set('value', value);
		
		// We're done with the autocompleter.
		this.hide();
	},
	
	/**
	 *	Make a JSON request
	 */
	query: function() {
		var value = this.input.get('value');
		
		// Nothing 'ado
		if (!value) {
			this.hide();
			return true;
		}
		
		// Set waiter potentially?
		this.loading();
		
		// Build query
		var query = new Hash(this.options.request.data);
		query.set(this.options.queryKey, value);
		
		// Get data
		this.request.post(query);
	},
	
	/**
	 *	Stop a JSON request
	 */
	stop: function() {
		$clear(this.typeTimer);
		this.request.cancel();
	},
	
	/**
	 *	Cancel any pending queries
	 */
	cancel: function() {
		this.stop();
		this.hide();
	},
	
	/**
	 *	JSON callback
	 *
	 *	@param object JSON object
	 */
	onSuccess: function(items) {
		this.update(items);
		this.show();
		if (this.options.callback) {
			this.options.callback.run();
		}
	}
});

/**
 *	Panel toggler.
 */
var Panels = new Class({

	Implements: Options,

	options: {
		mode: 'horizontal',
		transition: 'back:out',
		link: 'cancel',
		duration: 900,
		defaultPanel: 'intro',
		indexElement: null,
		rotate: 0,	// Rotate speed (ms)
		updateHash: true,
		panels: 'div.panel',
		resetHeight: true,
		removeId: true
	},
	
	holder: null,
	panels: null,
	keys: null,
	fx: null,
	nav: null,
	indexElement: null,
	links: null,
	
	/**
	 *	Currently visible panel's ID
	 */
	current: null,
	
	/**
	 *	Timer ID, (if rotating)
	 */
	timer: null,
	
	/**
	 *	Constructor
	 */
	initialize: function(holder, nav, options) {
		this.setOptions(options);
		this.holder = $(holder);
		this.nav = nav ? $(nav) : false;
		this.indexElement = this.options.indexElement ? $(this.options.indexElement) : false;
	
		/* Hide overflow and create fx */
		this.holder.setStyle('overflow', 'hidden');
		this.fx = new Fx.Scroll(this.holder, {
			transition: this.options.transition,
			duration: this.options.duration,
			link: this.options.link,
			wheelStops: false
		});
		this.holder.set('tween', {
			duration: this.options.duration - 100,
			link: this.options.link
		});
		
		/* Add panels to hash and erase id to prevent nav on location hash update */
		var panels = this.holder.getElements(this.options.panels);
		this.panels = $H();
		panels.each(function(panel, index) {
			if (this.options.mode == 'vertical') {
				panel.setStyle('float', 'none');
			}
			this.panels.set(panel.get('id'), panel);
			if (this.options.removeId) {
				panel.erase('id');
			}
		}, this);
		this.keys = this.panels.getKeys();
		if (this.options.mode == 'vertical') {
			this.holder.getElementById('panels-inner').setStyles({
				'width': 'auto',
				'padding-bottom': 1000
			});
		}
		
		/* Add tab events */
		this.links = new Hash();
		var links = this.nav ? this.nav.getElements('a') : [];
		links.each(function(link) {
			
			/* Get target id */
			var href = link.get('href');
			var id = href.substr(href.indexOf('#') + 1);
			
			/* Add event */
			link.addEvent('click', this.onTabClick.bindWithEvent(this, id));
			
			// Store reference
			this.links.set(id, link);
			
		}, this);
		
		/* Mark current section active */
		var current = this.panels.get(location.hash.substr(1) || this.options.defaultPanel);
		if (current) {
			this._scrollTo(location.hash.substr(1) || this.options.defaultPanel);
		}
		
		// Start rotating
		if (this.options.rotate) {
			this.start();
		}
	},
	
	/**
	 *	Click handler
	 */
	onTabClick: function(event, id) {
		var link = this.links.get(id);
		link.blur();
		
		this._scrollTo(id);
		
		// Stop events
		this.pause();
		if ($defined(event)) {
			event.stop();
		}
	},
	
	/**
	 *	Scroll to a panel, setting all the display stuff too, i.e. CSS and location hash
	 */
	_scrollTo: function(id) {
		
		/* Update object */
		this.current = id;
		
		/* Smooth scroll to new panel */
		var panel = this.panels.get(this.current);
		if (this.options.resetHeight) {
			this.holder.tween('height', panel.getHeight());
		}
		
		//this.fx.toElement(panel);
		// getPosition is ballsed in the current version of moo, so we'll have this stupidity for now
		this.fx.start(panel.offsetLeft, panel.offsetTop);
		
		/* Update index display */
		if (this.indexElement) {
			this.indexElement.set('html', this.keys.indexOf(this.current) + 1);
		}
		
		/* Mark tab active */
		this.links.each(function(link) { link.removeClass('current'); });
		var currentLink = this.links.get(this.current);
		if (currentLink) {
			currentLink.addClass('current');
		}
		
		/* Update location hash */
		if (this.options.updateHash) {
			location.hash = this.current;
		}
	},
	
	/** 
	 *	Display next in line
	 */
	gotoNext: function() {
		var index = this.keys.indexOf(this.current) + 1;
		return this._scrollTo(this.keys[index % this.keys.length]);
	},
	
	/**
	 *	Display previous in line.
	 */
	gotoPrevious: function() {
		var index = this.keys.indexOf(this.current) + this.keys.length - 1;	// Lol, JS
		return this._scrollTo(this.keys[index % this.keys.length]);
	},
	
	/**
	 *	Start rotating through the panels
	 */
	start: function(speed) {
		this.timer = this.gotoNext.periodical(speed || this.options.rotate, this);
	},
	
	/**
	 *	Pause any rotation
	 */
	pause: function() {
		$clear(this.timer);
	}
});

/**
 *	Expandable
 */
var Expandable = new Class({
	
	Implements: [Options],
	
	container: null,
	elements: null,
	timers: {},
	
	/**
	 *	Options
	 */
	options: {
		delay: 800	// Mouseover toggle delay
	},
	
	/**
	 *	Constructor
	 */
	initialize: function(container, options) {
		this.container = $(container);
		this.setOptions(options);
		this.timers = new Hash();
		
		this.elements = this.container.getElements('li');
		this.elements.removeClass('expandable');
		this.elements.each(function(li) {
			var children = li.getElement('ul');
			if (children) {
				children.slide('hide');
				
				var link = li.getElement('a');
				if (link) {
					link.addEvents({
						'mouseenter': this.onMouseenter.bindWithEvent(this, li),
						'mouseleave': this.onMouseleave.bindWithEvent(this, li)
					});
				}
				
				li.addClass('expandable');
			}
		}, this);
	},
	
	/**
	 *	Mouseover an expandable branch
	 */
	onMouseenter: function(event, branch) {
		event.stop();
		
		var id = branch.get('id');
		
		var timer = this.toggle.delay(this.options.delay, this, branch);
		this.timers.set(id, timer);
	},
	
	/**
	 *	Mouseout from an expandable branch
	 */
	onMouseleave: function(event, branch) {
		event.stop();
		
		var id = branch.get('id');
		
		$clear(this.timers.get(id));
		this.timers.erase(id);
	},
	
	/**
	 *	Toggle a branch
	 */
	toggle: function(branch) {
		var children = branch.getElement('ul');
		children.slide('toggle');
	},
	
	/**
	 *	Expand an element and all its parents
	 */
	expand: function(branch) {
		branch = $(branch);
		while (this.container.hasChild(branch)) {
			var children = branch.getElement('ul');
			if (children) {
				children.slide('in');
			}
			
			// Go up a level
			branch = branch.getParent('li');
		}
	}
	
});

/**
 * Outer click event
 */
(function(){
	var events;
	var check = function(e) {
		var target = $(e.target);
		var parents = target.getParents();
		events.each(function(item) {
			var element = item.element;
			if (element != target && !parents.contains(element)) {
				item.fn.call(element, e);
			}
		});
	};
	Element.Events.outerClick = {
		onAdd: function(fn) {
			if (!events) {
				document.addEvent('click', check);
				events = [];
			}
			events.push({element: this, fn: fn});
		},
		onRemove: function(fn){
			events = events.filter(function(item) {
				return item.element != this || item.fn != fn;
			}, this);
			if (!events.length) {
				document.removeEvent('click', check);
				events = null;
			}
		}
	};
})();
