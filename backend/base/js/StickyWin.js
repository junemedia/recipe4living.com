/**
 * Modalizer
 * 
 * Defines Modalizer: functionality to overlay the window contents with a 
 * semi-transparent layer that prevents interaction with page content until it is removed.
 * 
 * @license http://clientside.cnet.com/wiki/cnet-libraries#license
 */
var Modalizer = new Class({
	defaultModalStyle: {
		display: 'block',
		position: 'fixed',
		top: 0,
		left: 0,	
		'z-index': 5000,
		'background-color': '#333',
		opacity: 0.8
	},
	setModalOptions: function(options){
		this.modalOptions = $merge({
			width: window.getScrollSize().x,
			height: window.getScrollSize().y,
			elementsToHide: 'select',
			hideOnClick: true,
			modalStyle: {},
			updateOnResize: true,
			layerId: 'modalOverlay',
			onModalHide: $empty,
			onModalShow: $empty
		}, this.modalOptions, options);
		return this;
	},
	toElement: function(){
		if (!this.modalOptions.layerId) {
			this.setModalOptions();
		}
		return $(this.modalOptions.layerId) || new Element('div', {id: this.modalOptions.layerId}).inject(document.body);
	},
	resize: function(){
		if ($(this)) {
			$(this).setStyles({
				width: window.getScrollSize().x,
				height: window.getScrollSize().y
			});
		}
	},
	setModalStyle: function (styleObject){
		this.modalOptions.modalStyle = styleObject;
		this.modalStyle = $merge(this.defaultModalStyle, {
			width:this.modalOptions.width,
			height:this.modalOptions.height
		}, styleObject);
		if ($(this)) {
			$(this).setStyles(this.modalStyle);
		}
		return this.modalStyle;
	},
	modalShow: function(options){
		this.setModalOptions(options);
		$(this).setStyles(this.setModalStyle(this.modalOptions.modalStyle));
		if (Browser.Engine.trident4) {
			$(this).setStyle('position','absolute');
		}
		$(this).removeEvents('click').addEvent('click', function(){
			this.modalHide(this.modalOptions.hideOnClick);
		}.bind(this));
		this.bound = this.bound||{};
		if (!this.bound.resize && this.modalOptions.updateOnResize) {
			this.bound.resize = this.resize.bind(this);
			window.addEvent('resize', this.bound.resize);
		}
		if ($type(this.modalOptions.onModalShow)  == "function") {
			this.modalOptions.onModalShow();
		}
		this.togglePopThroughElements(0);
		$(this).setStyle('display','block');
		return this;
	},
	modalHide: function(override){
		if (override === false) {
			return false; //this is internal, you don't need to pass in an argument
		}
		this.togglePopThroughElements(1);
		if ($type(this.modalOptions.onModalHide) == "function") {
			this.modalOptions.onModalHide();
		}
		$(this).setStyle('display','none');
		if (this.modalOptions.updateOnResize) {
			this.bound = this.bound||{};
			if (!this.bound.resize) {
				this.bound.resize = this.resize.bind(this);
			}
			window.removeEvent('resize', this.bound.resize);
		}
		return this;
	},
	togglePopThroughElements: function(opacity){
		if (Browser.Engine.trident4 || (Browser.Engine.gecko && Browser.Platform.mac)) {
			$$(this.modalOptions.elementsToHide).each(function(sel){
				sel.setStyle('opacity', opacity);
			});
		}
	}
});

/**
 * StickyWin
 * 
 * Creates a div within the page with the specified contents at the location relative
 * to the element you specify; basically an in-page popup maker.
 *
 * @license http://clientside.cnet.com/wiki/cnet-libraries#license
 */
var StickyWin = new Class({
	Implements: [Options, Events],
	options: {
		/*onDisplay: $empty,
		onClose: $empty,*/
		closeClassName: 'closeSticky',
		pinClassName: 'pinSticky',
		content: '',
		zIndex: 10000,
		className: '',
		relativeTo: null, // Sometimes we do need to set this (IE6 doesn't like doc.body if it ain't loaded)
		width: false,
		height: false,
		timeout: -1,
		allowMultipleByClass: false,
		allowMultiple: true,
		showNow: true,
		useIframeShim: true,
		iframeShimSelector: ''
	},
	initialize: function(options){
		this.options.inject = {
			target: document.body,
			where: 'bottom' 
		};
		this.setOptions(options);
		
		this.id = this.options.id || 'StickyWin_'+new Date().getTime();
		this.makeWindow();
		if (this.options.content) {
			this.setContent(this.options.content);
		}
		if (this.options.timeout > 0) {
			this.addEvent('onDisplay', function(){
				this.hide.delay(this.options.timeout, this);
			}.bind(this));
		}
		if (this.options.showNow) {
			this.show();
		}
	},
	toElement: function() {
		return this.win;
	},
	makeWindow: function(){
		this.destroyOthers();
		if (!$(this.id)) {
			this.win = new Element('div', {
				id: this.id
			}).addClass(this.options.className).addClass('StickyWinInstance').addClass('SWclearfix').setStyles({
			 	display: 'none',
				position: 'absolute',
				top: 0,
				left: 0,
				zIndex: this.options.zIndex
			}).inject(this.options.inject.target, this.options.inject.where).store('StickyWin', this);			
		} else {
			this.win = $(this.id);
		}
		if (this.options.width && $type(this.options.width.toInt())=="number") {
			this.win.setStyle('width', this.options.width.toInt());
		}
		if (this.options.height && $type(this.options.height.toInt())=="number") {
			this.win.setStyle('height', this.options.height.toInt());
		}
		return this;
	},
	show: function(){
		this.fireEvent('onDisplay');
		this.showWin();
		if (this.options.useIframeShim) {
			this.showIframeShim();
		}
		this.visible = true;
		return this;
	},
	showWin: function(){
		this.win.setStyle('display','block');
		this.position.delay(5, this);
	},
	hide: function(suppressEvent){
		if (!suppressEvent) {
			this.fireEvent('onClose');
		}
		this.hideWin();
		if (this.options.useIframeShim) {
			this.hideIframeShim();
		}
		this.visible = false;
		return this;
	},
	hideWin: function(){
		this.win.setStyle('display','none');
	},
	destroyOthers: function() {
		if (!this.options.allowMultipleByClass || !this.options.allowMultiple) {
			$$('div.StickyWinInstance').each(function(sw) {
				if (!this.options.allowMultiple || (!this.options.allowMultipleByClass && sw.hasClass(this.options.className))) {
					sw.dispose();
				}
			}, this);
		}
	},
	setContent: function(html) {
		if (this.win.getChildren().length > 0) {
			this.win.empty();
		}
		if ($type(html) == "string") {
			this.win.set('html', html);
		} else if ($(html)) {
			this.win.adopt(html);
		}
		this.win.getElements('.'+this.options.closeClassName).each(function(el){
			el.addEvent('click', this.hide.bind(this));
		}, this);
		this.win.getElements('.'+this.options.pinClassName).each(function(el){
			el.addEvent('click', this.togglepin.bind(this));
		}, this);
		return this;
	},	
	position: function(){
	
		/* Position at center of element (or body) */
		var relativeTo = this.options.relativeTo || document.body;
		var pos = relativeTo.getPosition();
		var scrollPos = document.body.getScroll();
		var relativeSize = relativeTo.getSize();
		var winSize = this.win.getSize();
		for (var z in pos) {
			pos[z] += relativeSize[z] / 2;
			pos[z] -= winSize[z] / 2;
			if (!this.options.relativeTo) {
				pos[z] += scrollPos[z];
			}
		}
		this.win.position(pos);
		
		/* Position shim */
		if (this.shim) {
			this.shim.position();
		}
		
		this.positioned = true;
		return this;
	},
	pin: function(pin) {
		if (!this.win.pin) {
			return this;
		}
		this.pinned = $pick(pin, true);
		this.win.pin(pin);
		return this;
	},
	unpin: function(){
		return this.pin(false);
	},
	togglepin: function(){
		return this.pin(!this.pinned);
	},
	makeIframeShim: function(){
		if (!this.shim){
			var el = this.options.iframeShimSelector ? this.win.getElement(this.options.iframeShimSelector) : this.win;
			this.shim = new IframeShim(el, {
				display: false,
				name: 'StickyWinShim'
			});
		}
	},
	showIframeShim: function(){
		if (this.options.useIframeShim) {
			this.makeIframeShim();
			this.shim.show();
		}
	},
	hideIframeShim: function(){
		if (this.options.useIframeShim) {
			this.shim.hide();
		}
	},
	destroy: function(){
		if (this.win) {
			this.win.dispose();
		}
		if (this.options.useIframeShim) {
			this.shim.dispose();
		}
		if ($('modalOverlay')) {
			$('modalOverlay').dispose();
		}
	}
});

/**
 * StickyWinFx
 *
 * Extends StickyWin to create popups that fade in and out and can be dragged
 * and resized (requires StickyWinFx.Drag.js).
 * 
 * @license http://clientside.cnet.com/wiki/cnet-libraries#license
 */
var StickyWinFx = new Class({
	Extends: StickyWin,
	options: {
		fade: true,
		fadeDuration: 150,
		/*fadeTransition: 'sine:in:out',*/
		draggable: false,
		dragOptions: {},
		dragHandleSelector: '.dragHandle',
		resizable: false,
		resizeOptions: {},
		resizeHandleSelector: ''
	},
	setContent: function(html){
		this.parent(html);
		if (this.options.draggable) {
			this.makeDraggable();
		}
		if (this.options.resizable) {
			this.makeResizable();
		}
		return this;
	},	
	hideWin: function(){
		if (this.options.fade) {
			this.fade(0);
		} else {
			this.parent();
		}
	},
	showWin: function(){
		if (this.options.fade) {
			this.fade(1);
		} else {
			this.parent();
		}
	},
	fade: function(to){
		if (!this.fadeFx) {
			this.win.setStyles({
				opacity: 0,
				display: 'block'
			});
			var opts = {
				property: 'opacity',
				duration: this.options.fadeDuration
			};
			if (this.options.fadeTransition) {
				opts.transition = this.options.fadeTransition;
			}
			this.fadeFx = new Fx.Tween(this.win, opts);
		}
		if (to > 0) {
			this.win.setStyle('display','block');
			this.position.delay(5, this);
		}
		this.fadeFx.clearChain();
		this.fadeFx.start(to).chain(function (){
			if (to == 0) {
				this.win.setStyle('display', 'none');
			}
		}.bind(this));
		return this;
	}
});

/**
 * StickyWinFx.Drag
 * 
 * Implements drag and resize functionaity into StickyWinFx. See StickyWinFx for the options.
 * 
 * @license http://clientside.cnet.com/wiki/cnet-libraries#license
 */
StickyWinFx.implement({
	makeDraggable: function(){
		var toggled = this.toggleVisible(true);
		if (this.options.useIframeShim) {
			this.makeIframeShim();
			var onComplete = (this.options.dragOptions.onComplete || $empty);
			this.options.dragOptions.onComplete = function(){
				onComplete();
				this.shim.position();
			}.bind(this);
		}
		if (this.options.dragHandleSelector) {
			var handle = this.win.getElement(this.options.dragHandleSelector);
			if (handle) {
				handle.setStyle('cursor','move');
				this.options.dragOptions.handle = handle;
			}
		}
		this.win.makeDraggable(this.options.dragOptions);
		if (toggled) {
			this.toggleVisible(false);
		}
	}, 
	makeResizable: function(){
		var toggled = this.toggleVisible(true);
		if (this.options.useIframeShim) {
			this.makeIframeShim();
			var onComplete = (this.options.resizeOptions.onComplete || $empty);
			this.options.resizeOptions.onComplete = function(){
				onComplete();
				this.shim.position();
			}.bind(this);
		}
		if (this.options.resizeHandleSelector) {
			var handle = this.win.getElement(this.options.resizeHandleSelector);
			if (handle) {
				this.options.resizeOptions.handle = this.win.getElement(this.options.resizeHandleSelector);
			}
		}
		this.win.makeResizable(this.options.resizeOptions);
		if (toggled) {
			this.toggleVisible(false);
		}
	},
	toggleVisible: function(show){
		if (!this.visible && Browser.Engine.webkit && $pick(show, true)) {
			this.win.setStyles({
				display: 'block',
				opacity: 0
			});
			return true;
		} else if (!$pick(show, false)){
			this.win.setStyles({
				display: 'none',
				opacity: 1
			});
			return false;
		}
		return false;
	}
});

/**
 * StickyWin.Modal
 * 
 * This script extends StickyWin and StickyWinFx classes to add Modalizer functionality.
 * 
 * @license http://clientside.cnet.com/wiki/cnet-libraries#license
 */
var StickyWinModal, StickyWinFxModal;
(function(){
var modalWinBase = function(extend){
	return {
		Extends: extend,
		initialize: function(options){
			options = options||{};
			this.setModalOptions($merge(options.modalOptions||{}, {
				onModalHide: function(){
						this.hide(false);
					}.bind(this)
				}));
			this.parent(options);
		},
		show: function(showModal){
			if ($pick(showModal, true)) {
				this.modalShow();
				this.win.getElements(this.modalOptions.elementsToHide).setStyle('opacity', 1);
			}
			this.parent();
		},
		hide: function(hideModal){
			if ($pick(hideModal, true)) {
				this.modalHide();
			}
			this.parent($pick(hideModal, true));
		}
	};
};
StickyWinModal = new Class(modalWinBase(StickyWin));
StickyWinModal.implement(new Modalizer());
StickyWinFxModal = new Class(modalWinBase(StickyWinFx));
StickyWinFxModal.implement(new Modalizer());
})();

/**
 * StickyWin.Ajax
 *
 * Adds ajax functionality to all the StickyWin classes.
 * 
 * @license http://clientside.cnet.com/wiki/cnet-libraries#license
 */
(function(){
	var SWA = function(extend){
		return {
			Extends: extend,
			options: {
				url: '',
				showNow: false,
				requestOptions: {
					method: 'get'
				},
				caption: '',
				uiOptions:{},
				handleResponse: function(response){
					var responseScript = "";
					this.Request.response.text.stripScripts(function(script){ responseScript += script; });
					this.setContent(response);
					this.show();
					if (this.evalScripts) {
						$exec(responseScript);
					}
				}
			},
			initialize: function(options){
				this.parent(options);
				this.evalScripts = this.options.requestOptions.evalScripts;
				this.options.requestOptions.evalScripts = false;
				this.createRequest();
			},
			createRequest: function(){
				this.Request = new Request(this.options.requestOptions).addEvent('onSuccess',
					this.options.handleResponse.bind(this));
			},
			update: function(url, options){
				this.Request.setOptions(options).send({url: url||this.options.url});
				return this;
			}
		};
	};
	StickyWin.Ajax = new Class(SWA(StickyWin));
	StickyWinFx.Ajax = new Class(SWA(StickyWinFx));
	StickyWinModal.Ajax = new Class(SWA(StickyWinModal));
	StickyWinFxModal.Ajax = new Class(SWA(StickyWinFxModal));
})();