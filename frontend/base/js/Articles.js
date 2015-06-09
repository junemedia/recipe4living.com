/**
 * ArticleItems
 * 
 * BluApplication article items
 *
 * @package		BluApplication
 * @subpackage  FrontendClientside
 */
var ArticleItems = new Class({

	Implements: [Options, Events],

	options: {
		historyKey: 'items',
		useHistory: true,
		updateTask: 'view_items',
		quickView: {
			use: true,
			fade: true,
			fadeDuration: 300,
			width: 390,
			height: 320,
			draggable: true,
			resizable: true,
			resizeOptions: {
				limit: {
					'x': [ 390, 600 ],
					'y': [ 200, 600 ]
				}
			},
			relative: true,
			modal: false,
			modalOptions: {
			    modalStyle: {
			      'background-color': '#fff',
			      'opacity': 0.6
			    }
			}
		},
		fullDetailsTips: true,
		baseUrl: '',
		filters: {
			container: null,
			useAccordion: true,
			firstOpen: [],
			groupsShown: [],
			maxHeight: 190
		},
		scrollTo: null,
		useFancyForm: false
	},
	
	cart: null,
	
	container: null,
	history: null,
	waiter: null,
	
	filtersContainer: null,
	request: null,
	
	url: null,
	
	initialize: function(container, cart, options) {
		
		// Set options
		this.container = container;
		this.cart = cart;
		this.setOptions(this.siteOptions, options);
		
		// Add history
		if (this.options.useHistory) {
			this.history = HistoryManager.register(
				this.options.historyKey,
				[],
				this.onHistoryMatch.bind(this),
				false,
				this.options.historyKey + '-([^;]*)');
		}
		
		// Add waiter
		this.waiter = new Waiter(this.container);
		
		// Add content effect
		this.container.set('tween', {
			duration: 800,
			fps: 100
		});
		this.container.setStyle('overflow', 'hidden');
		
		// Build update request
		this.request = new Request.JSON({
			method: 'get',
			format: 'json',
			onComplete: this.onLoadComplete.bind(this),
			evalScripts: false,
			link: 'cancel'
		});
		
		// Add events
		this.buildContent();
		this.addUpdateEvents();
		this.addQuickView();
		
		// Add filter events
		if (this.options.filters.container) {
			this.filtersContainer = $(this.options.filters.container);
			
			// Add scroll areas
			var optionGroups = this.filtersContainer.getElements('div.options');
			if (Browser.Engine.trident4) {
				optionGroups.each(function(el) {
					if (el.scrollHeight > (this.options.filters.maxHeight - 1)) {
						el.setStyle('height', this.options.filters.maxHeight);
					}
				}, this);
			}
			this.filtersScrollAreas = new BluScrollAreas(optionGroups);
			optionGroups.each(function(optionGroup) {
				var firstSelectedLink = optionGroup.getElement('a.selected');
				if (firstSelectedLink) {
					new Fx.Scroll(optionGroup).toElement(optionGroup.getElement('a.selected'), {duration:0}).chain(function() {
						optionGroup.fireEvent('scrolled');
					});
				}
			});
			
			// Filtering options accordion
			if (this.options.filters.useAccordion) {
				var filtersSections = this.filtersContainer.getElementById('listing-filters-sections');
				if (filtersSections) {
					this.filtersAccordion = new BluAccordion(filtersSections.getElements('h3'), filtersSections.getElements('div.element'), {
						display: this.options.filters.firstOpen,
						allowMultipleOpen: true,
						onActive: function(toggler, element) { toggler.addClass('open'); },
						onBackground: function(toggler, element) { toggler.removeClass('open'); }
					});
				}
			}
			
			// Update listing on filter item click
			this.filtersContainer.addEvent('click', function(event) {
				var link = $(event.target);
				if (link.get('tag') != 'a') {
					link = link.getParent('a');
				}
				if (link) {
					event.preventDefault();
					var url = link.get('href');
					if (url != '#') {
						this.update(url);
					}
				}
			}.bindWithEvent(this));
		}
	},
	
	onHistoryMatch: function(args) {
		if ($defined(args[0])) {
			this.update(args[0]);
		}
	},
	
	update: function(url) {
		if (url == this.url) {
			return;
		}
		this.waiter.start(this.container);
		this.url = url;
		this.history.setValue(0, this.url);
		this.request.send({url: this.url, data: {task: this.options.updateTask}});
	},
	
	buildContent: function() {
		
		// Typeface
		if ($defined(window._typeface_js)) {
			window._typeface_js.renderDocument(null, this.container);
		}
		
		// Discount tooltips
		var discountTooltips = this.container.getElements('span.abbr');
		discountTooltips.each(function(element){
			var title = element.get('title');
			title.replace('|', '<br />');
			element.set('title', title);
		});
		discountTooltips = new Tips(discountTooltips, {
			offsets: {'x': 0, 'y': 16}
		});
		
		// Expose prod mouse events events
		this.container.getElements('.prod').each(function(prod) {
			prod.addEvents({
				'mouseenter': function() { this.fireEvent('prodOver'); }.bind(this),
				'mouseleave': function() { this.fireEvent('prodOut'); }.bind(this)
			});
		}, this);
		
		// Attach add to cart forms, children AND self
		if (this.cart) {
			var formsToAttach = this.container.getElements('form.itemsaddtocart');
			if ((this.container.get('tag') == 'form') && this.container.hasClass('itemsaddtocart')) {
				formsToAttach.include(this.container);
			}
			this.cart.attachAddForms(formsToAttach);
		}
		
		// Add fanciness
		if (this.options.useFancyForm){
			var formsForFancying = this.container.getElements('form');
			if (this.container.get('tag') == 'form') { formsForFancying.include(this.container); }
			formsForFancying.each(function(form){ new FancyForm(form); });
		}
	
	},

	addUpdateEvents: function() {
		this.container.getElements('form.reloads, select.reloads, a.reloads, div.header a, div.footer a').each(function(el) {
			this.addTrigger(el);
		}, this);
	},
	
	addTrigger: function(element){
		switch(element.get('tag')){
			case 'form':
				element.addEvent('submit', function(event) {
					/*if ($defined(event)) {
						event.stop();
					}*/
					this.update(element.get('action')+'?'+element.toQueryString());
				}.bindWithEvent(this));
				break;
			case 'select':
				element.addEvent('change', function(event) { element.getParent('form').fireEvent('submit'); });
				break;
			case 'a':
				element.addEvent('click', function(event) {
					event.stop();
					this.update(element.get('href'));
				}.bindWithEvent(this));
				break;
			default:
				return false;
		}
		return true;
	},
	
	onLoadComplete: function(response) {
	
		// Update filter availability
		if ($defined(response.metaGroups) && this.options.filters.groupsShown) {
			var metaGroups = $H(response.metaGroups);
			metaGroups.each(function(metaGroup, groupId) {
				if (!this.options.filters.groupsShown.contains(groupId)) {
					return;
				}
				metaValues = $H(metaGroup.values);
				metaValues.each(function(metaValue, valueId) {
					var el = this.filtersContainer.getElementById('filter-'+groupId+'-'+valueId);
					if (el) {
						
						// Enable/disable filter
						if ((metaValue.numProducts > 0) || (metaGroup.neverExclude > 0)) {
							el.removeClass('disabled');
						} else {
							el.addClass('disabled');
						}
						
						// Select/deselect filter
						if (metaValue.selected) {
							el.addClass('selected');
						} else {
							el.removeClass('selected');
						}
						
						// Update number of products
						var numProducts = el.getElement('.numproducts');
						if (numProducts) {
							if (metaValue.numProducts > 0) {
								numProducts.set('html', '('+metaValue.numProducts+')');
							} else {
								numProducts.set('html', '');
							}
						}
						
						// Update link
						el.set('href', metaValue.link);
					}
				}, this);
			}, this);
		}
		
		// Set document title
		if ($defined(response.documentTitle)) {
			document.title = response.documentTitle;
		}
		
		// Parse items text
		var content = {};
		content.html = response.items.stripScripts(function(script){
			content.javascript = script;
		});
	
		// Hide waiter and inject content
		this.waiter.stop();
		this.injectContent(content.html, content.javascript);
	},
	
	injectContent: function(html, javascript) {
		this.container.set('html', html);
		
		// Scroll to an element, (before rebuilding contents, just so users know something happened)
		if (this.options.scrollTo) {
			var scrollTo = this.options.scrollTo === true ? this.container : $(this.options.scrollTo);
			
			new Fx.Scroll(window, {
				duration: 'long', 
				wheelStops: false
			}).toElement(scrollTo);
		}
		
		var inject = function () {
			
			// Execute script 
			$exec(javascript);
			
			// Reload typeface
			if (window._typeface_js) {
				window._typeface_js.renderDocument(this.container);
			}
			
			// Build content/add events
			this.buildContent();
			this.addUpdateEvents();
			this.addQuickView();
		};
		inject.delay(100, this);
	},
	
	addQuickView: function() {
		
		// Add buttons and events
		this.container.getElements('.content .prod').each(function(prod) {
			var link = prod.getElement('div.im a');
			
			// Add full details tooltips
			if (this.options.fullDetailsTips) {
				var tooltip = new Tips(link, {
					offsets: {'x': 4, 'y': 22}
				});
			} else if (link) {
				link.erase('title');
			}
			
			// Build quickview button
			if (this.options.quickView.use) {
				var qvButton = new Element('a', {
					'class': 'quickview-button',
					'href': link.href,
					'events': {
						'click': this.openQuickView.bindWithEvent(this, link)
					},
					'html': '<span></span>'
				}).injectInside(prod.getElement('div.im'));
			}
		
			// Add prod hover events for IE6
			if (Browser.Engine.trident4) {
				prod.addEvents({
					'mouseenter': function() { prod.addClass('hover'); },
					'mouseleave': function() { prod.removeClass('hover'); }
				});
			}
			
		}, this);
		
	},
	
	openQuickView: function(event, link) {
		event.stop();
		
		// Get reference to cart
		var cart = this.cart;
		
		var qvWin = link.retrieve('qvWin');
		if (!qvWin) {
			/* Get sticky win type */
			var StickyWinClass = this.options.quickView.modal ? StickyWinFxModal : StickyWinFx;
			
			// Build url
			var url = link.get('href');
			url += (url.contains('?') ? '&' : '?') + 'task=quick_view';
			
			// Build sticky win
			qvWin = new StickyWinClass.Ajax({
			    className: 'stickyWin quickViewStickyWin'+(this.options.quickView.resizable ? ' resizableStickyWin' : ''),
			    allowMultipleByClass: true,
				fade: this.options.quickView.fade,
				fadeDuration: this.options.quickView.fadeDuration,
				draggable: this.options.quickView.draggable,
				resizable: this.options.quickView.resizable,
				resizeHandleSelector: '.sizeHandle',
				resizeOptions: this.options.quickView.resizeOptions,
				relativeTo: this.options.quickView.relative ? link : null,
				width: this.options.quickView.width,
				height: this.options.quickView.height,
				modalOptions: this.options.quickView.modalOptions,
				useIframeShim: true,
				url: url,
				requestOptions: {
					format: 'popup',
					evalScripts: true
				},
				handleResponse: function(response){				
					var responseScript;
					this.Request.response.text.stripScripts(function(script){ responseScript = script; });
					this.setContent(response);
					
					// Attach add forms and update links
					cart.attachAddForms(this.win.getElements('form.addtocart'), this);
					this.win.getElements('a.update').each(function(link) {
						link.addEvent('click', function(event) {
							qvWin.update(link.get('href'));
							event.stop();
						});
					});
					
					// Attach price handlers
					cart.currency.addPriceInputEvents(this.win.getElements('.blu-price input'));
					
					this.show();
					if (this.evalScripts) { $exec(responseScript); }
				}
			});
			qvWin.update();
			link.store('qvWin', qvWin);
		} else {
			qvWin.show();
		}
		
		event.stop();
	}

});

/**
 * Filters
 * 
 * BluCommerce filters
 *
 * @package		BluCommerce
 * @subpackage  FrontendClientside
 */
var Filters = new Class({
	
	Implements: Options,
	
	options: {
		total: null,
		noneFoundText: 'No products found',
		totalText: '[num] products found'
	},
	
	container: null,
	form: null,
	total: null,
	
	fancyForm: null,
	request: null,
	
	initialize: function(container, options) {
		this.setOptions(options);
		this.container = $(container);
		this.total = $(this.options.total);
		
		/* Build request object */
		this.request = new Request.JSON({
			format: 'json',
			onComplete: this.onLoadComplete.bind(this),
			evalScripts: false,
			link: 'cancel'
		});
		
		/* Build content */
		this.buildContent();
		
		/* Update availability */
		this.updateAvailability();
	},
	
	buildContent: function() {
		this.form = this.container.getElement('form');
		
		/* Fancy form */
		this.fancyForm = new FancyForm(this.form, {
			fancySelect: true
		});
		
		/* Fire form submittal on input change */
		this.form.getElements('input, select').each(function(input) {
			var isSelect = input.match('select');
			
			/* Skip hidden/submit inputs */
			var type = input.get('type');
			if ((!isSelect) && (type != 'checkbox') && (type != 'radio')) {
				return;
			}
			
			/* Add update event */
			var event = (isSelect || (type == 'text')) ? 'change' : 'click';
			input.addEvent(event, this.updateAvailability.bindWithEvent(this));
		}, this);
	},
	
	updateAvailability: function(event) {
	
		/* Build request query string */
		var requestQs = this.form.toQueryString();
		requestQs += '&save_filters=0';
	
		/* Send request */
		this.request.send({
			url: this.form.get('action'),
			data: requestQs
		});
	},
	
	onLoadComplete: function(response) {
	
		/* Update form content */
		if (response.form) {
			response.html = response.form.stripScripts(function(script){
				response.javascript = script;
			});
			this.container.set('html', response.html);
			$exec(response.javascript);
			this.buildContent();
		}
		
		/* Update filter availability */
		if (response.metaGroups) {
	
			var metaGroups = $H(response.metaGroups);
			metaGroups.each(function(metaGroup, groupId) {
				metaValues = $H(metaGroup.values);
				metaValues.each(function(metaValue, valueId) {		
					var el = this.form.getElementById('input-'+groupId+'-'+valueId);
					if (el) {					
						/* Enable/disable filter */
						if ((metaValue.numProducts > 0) || (metaGroup.neverExclude > 0)) {
							el.setProperty('disabled', false);
						} else {
							el.setProperty('disabled', true);
						}
						
						/* Check/uncheck filter */
						var checkProperty = el.match('option') ? 'selected' : 'checked';
						if (metaValue.selected) {
							el.setProperty(checkProperty, true);
						} else if (metaGroup.neverExclude == 0) {
							el.setProperty(checkProperty, false);
						}
						el.fireEvent('updated');
					}
				}, this);
			}, this);
		}
		
		/* Update total */
		if (response.numProducts) {
			this.setTotal(response.numProducts);
		}
	},
	
	setTotal: function(numProducts) {
		if (!this.total) {
			return;
		}
		
		/* Build total text */
		var html;
		if (numProducts) {	
			html = this.options.totalText.replace('[num]', numProducts);
		} else {
			html = this.options.noneFoundText;
		}
		
		/* Update total text */
		this.total.set('html', html);
	}

});

/**
 * ProductOptions
 * 
 * BluCommerce product options interface
 *
 * @package		BluCommerce
 * @subpackage  FrontendClientside
 */
var ProductOptions = new Class({

	Implements: [Options, Events],
	
	options: {
		sku: null,
		stock: [],
		images: false,
		defaultImageModifers: [],
		sym: false,
		backOrder: false,
		taxRate: 1,
		unitPrice: null,
		totalPrice: null,
		headerTotalPrice: null,
		headerTotalListPrice: null
		/*change: $empty,
		updateImages: $empty,
		onPriceRangeChange: $empty*/
	},
	
	container: null,
	form: null,
	addButton: null,
	totalPrice: null,
	headerTotalPrice: null,
	headerTotalListPrice: null,
	quantityInput: null,
	
	/**
	 *	When option changes, updates to latest option's price
	 */
	prevMinPrice: null,
	prevMaxPrice: null,
	prevMinListPrice: null,
	prevMaxListPrice: null,
	
	groups: null,
	selected: [],
	stock: null,
	images: false,
	
	allowSubmit: false,
	
	initialize: function(container, options) {
		this.setOptions(options);
		this.container = container;
		this.form = this.container.getElement('form');
	
		/* Store stock and images hashes */
		this.stock = $H(this.options.stock);
		if (this.options.images) {
			this.images = $H(this.options.images);
		}
		
		/* Get add to cart button */
		this.addButton = this.container.getElement('.addtocart-button');
		
		/* Get group containers */
		this.groups = this.container.getElements('div.product-option');
		this.groups.each(function(group, groupLevel) {
		
			/* Get intial values */
			group.getElements('select, input').each(function(el) {
				var isInput = (el.get('tag') == 'input');
				
				/* Skip non-value hidden inputs */
				if (isInput && (el.get('type') == 'hidden') && !el.hasClass('option_value')) {
					return;
				}
				
				var type = el.get('type');
				var value = el.get('value');
				
				/* Set current selection if checked */
				if ((type == 'radio') || (type == 'checkbox')) {
					if (el.get('checked') == true) {
						this.selected[groupLevel] = value;
					}
				} else {
					this.selected[groupLevel] = ((value == '') ? null : value);
				}
			}, this);
			
			/* Add update events */
			group.getElements('select').addEvent('change', this.onOptionChange.bindWithEvent(this, groupLevel));
			group.getElements('input').each(function(input) {
				var type = input.get('type');
				if (type == 'radio' || type == 'checkbox') {
					input.addEvent('click', this.onOptionChange.bindWithEvent(this, groupLevel));
				} else if (input.hasClass('option_value') || type == 'text') {
					input.addEvent('change', this.onOptionChange.bindWithEvent(this, groupLevel));
				}
			}, this);
			
		}, this);
		
		/* Get selected option headers */
		this.selectedOptionHeaders = this.groups.getElements('.selected-option');
		
		/* Get total price container */
		if (this.options.unitPrice) {
			this.unitPrice = $(this.options.unitPrice);
		} else if (this.options.unitPrice !== false) {
			this.unitPrice = this.container.getElement('.price-unit');
		}
		if (this.options.totalPrice) {
			this.totalPrice = $(this.options.totalPrice);
		} else if (this.options.totalPrice !== false) {
			this.totalPrice = this.container.getElement('.price-total');
		}
		if (this.options.headerTotalPrice) {
			this.headerTotalPrice = $(this.options.headerTotalPrice);
		} else if (this.options.headerTotalPrice !== false) {
			this.headerTotalPrice = document.getElement('.header-price-total');
		}
		if (this.options.headerTotalListPrice) {
			this.headerTotalListPrice = $(this.options.headerTotalListPrice);
		} else if (this.options.headerTotalListPrice !== false) {
			this.headerTotalListPrice = document.getElement('.header-list-price-total');
		}
		
		/* Get quantity selector */
		this.quantityInput = this.container.getElement('input.quantity, select.quantity');
		if (this.quantityInput) {
			this.quantityInput.addEvent('change', this.onQuantityChange.bindWithEvent(this));
		}
		
		/* Update availability and price */
		this.updateAvailability();
		this.updatePrice();
	},
	
	onQuantityChange: function(event) {
		this.updatePrice();
	},
	
	onOptionChange: function(event, level) {	
		var el = $(event.target);
		
		/* Store selected value */
		var value = el.get('value');
		this.selected[level] = ((value == '') ? null : value);
		
		/* Update availability */
		this.updateAvailability(level);
		this.updatePrice();
		
		/* Update selected name */
		this.updateOptionSelectedName(el.get('alt') || el.get('text'), level, event);
		
		/* Update images */
		this.updateImages();
		
		/* Fire generic change event */
		this.fireEvent('change', [el, this.options.sku]);
	},
	
	updatePrice: function() {
		
		/* Get stock level and price for option */
		var stock = this.getStock();

		/* Determine price */
		var minPrice = stock.minPrice;
		var maxPrice = stock.maxPrice;
		var minListPrice = stock.minListPrice;
		var maxListPrice = stock.maxListPrice;
		
		/* Update unit price */
		if (this.unitPrice) {
			this.updatePriceRange(this.unitPrice, minPrice, maxPrice);
		}
		
		/* Get quantity */
		if (this.quantityInput) {
			var quant = this.quantityInput.get('value');
		} else {
			var quant = 1;
		}
		
		/* Get shipping cost */
		var shippingCost = 0;
		if (this.options.includeShipping) {
			shippingCost = this.getShippingCost();
		}
		
		/* Update total price */
		if (this.totalPrice) {
			this.updatePriceRange(this.totalPrice, (minPrice * quant) + shippingCost, (maxPrice * quant) + shippingCost);
		}
		
		/* Update header total price */
		if (this.headerTotalPrice) {
			this.updatePriceRange(this.headerTotalPrice, (minPrice * quant) + shippingCost, (maxPrice * quant) + shippingCost);
			if (this.prevMinPrice != minPrice || this.prevMaxPrice != maxPrice) {
				this.fireEvent('priceRangeChange');
			}
		}
		
		/* Update header total list price */
		if (this.headerTotalListPrice) {
			this.updatePriceRange(this.headerTotalListPrice, (minListPrice * quant) + shippingCost, (maxListPrice * quant) + shippingCost);
			if (this.prevMinListPrice != minListPrice || this.prevMaxListPrice != maxListPrice) {
				this.fireEvent('priceRangeChange');
			}
		}
		
		/* Update internal price state */
		this.prevMinPrice = minPrice;
		this.prevMaxPrice = maxPrice;
		this.prevMinListPrice = minListPrice;
		this.prevMaxListPrice = maxListPrice;
	},
	
	updatePriceRange: function(holder, minPrice, maxPrice) {
		var maxPriceHolder = holder.getElement('span.max-price');
		if (minPrice == maxPrice || !maxPriceHolder) {
			var input = holder.getElement('input');
			input.set('value', minPrice);
			input.fireEvent('change');
			
			if (maxPriceHolder) {
				maxPriceHolder.setStyle('display', 'none');
			}
		} else {
			/* Update min price */
			var input = holder.getElement('span.min-price input');
			input.set('value', minPrice);
			input.fireEvent('change');
			
			/* Update max price */
			var input = holder.getElement('span.max-price input');
			input.set('value', maxPrice);
			input.fireEvent('change');
			
			/* Show max price holder */
			if (maxPriceHolder) {
				maxPriceHolder.setStyle('display', 'inline');
			}
		}
	},
	
	updateOptionSelectedName: function(name, level, event) {
	
		/* Check option headers exist */
		if (!this.selectedOptionHeaders) {
			return;
		}
		
		/* Update element text */
		var el = this.selectedOptionHeaders[level];
		if (el.get('tag') == 'input') {
			el.set('value', name);
		} else {
			el.set('text', name);
		}
		el.fireEvent('change', [event]);
	},
	
	updateAvailability: function(skipLevel) {
	
		this.groups.each(function(group, groupLevel) {
			
			/* Don't need to update the changed level */
			if (groupLevel == skipLevel) { return; }
			
			/* Enable/disable options according to stock level */
			group.getElements('option, input').each(function(el) {
				var isInput = (el.get('tag') == 'input');
			
				/* Skip non-value hidden inputs */
				if (isInput && (el.get('type') == 'hidden') && !el.hasClass('option_value')) {
					return;
				}
				
				/* Skip text option inputs */
				if (isInput && (el.get('type') == 'text') && el.hasClass('textOption')) {
					return;
				}

				/* Get value */
				var value = el.get('value');

				/* Update availability */
				if (this.isAvailable(groupLevel, value)) {
					el.setProperty('disabled', false);
					if (isInput) {
						el.getParent().removeClass('disabled');
						el.getParent().set('opacity', 1);
					}
				} else {
					el.setProperty('disabled', true);
					if (isInput) {
						el.getParent().addClass('disabled');
						el.getParent().set('opacity', 0.2);
					}
				}
			}, this);
		}, this);
		
		/* Enable/disable add button */
		if (this.allOptionsSelected()) {
			this.allowSubmit = true;
			if (this.addButton) {
				this.addButton.set('opacity', 1);
			}
		} else {
			this.allowSubmit = false;
			if (this.addButton) {
				this.addButton.set('opacity', 0.15);
			}
		}
	},
	
	updateImages: function() {
		if (!this.images) {
			return;
		}
	
		/* Get relevant images */
		var matchStr = this.getMatchStr(null, null, true);
		var images = this.images.filter(function(imageData, sku) {
			return sku.match(matchStr);
		}).getValues();
	
		/* If we've found images, fire the change event */
		if (images.length) {
			this.fireEvent('updateImages', [images[0], matchStr]);
		}
	},
	
	getMatchStr: function(checkLevel, checkValue, useImageDefault) {
	
		/* Build stock SKU match regexp */
		var matchStr = this.options.sku;
		this.groups.each(function(group, i) {
		
			/* Determine appropriate match fragment */
			if (i == checkLevel) {
				fragment = checkValue;
			} else if (this.selected[i] != null) {
				fragment = this.selected[i];
			} else if (useImageDefault && this.options.defaultImageModifers[i]) {
				fragment = this.options.defaultImageModifers[i];
			} else {
				fragment = '[0-9]+';
			}
		
			matchStr += '_'+fragment;
		}, this);
		
		return matchStr;
	}, 
	
	getStock: function() {
		
		/* Get available stock */
		var matchStr = this.getMatchStr();
		var availableStock = this.stock.filter(function(stockData, sku) {
			return sku.match(matchStr) && (stockData.stock > 0);
		});
		
		/* Get stock totals */
		var ret = {
			stock: 0,
			minPrice: 0,
			maxPrice: 0,
			minListPrice: 0,
			maxListPrice: 0
		};
		var price, listPrice, stock;

		if (availableStock.getLength() == 0) {
			this.stock.each(function(stockData, sku) { 
				price = stockData.priceGross.toFloat();
				listPrice = stockData.listPriceGross.toFloat();
				ret.minPrice = ((ret.minPrice > 0) ? Math.min(price, ret.minPrice) : price);
				ret.maxPrice = Math.max(price, ret.maxPrice);
				ret.minListPrice = ((ret.minListPrice > 0) ? Math.min(listPrice, ret.minListPrice) : listPrice);
				ret.maxListPrice = Math.max(listPrice, ret.maxListPrice);
			});
		} else {
			availableStock.each(function(stockData, sku) {
				stock = stockData.stock.toInt();
				price = stockData.priceGross.toFloat();
				listPrice = stockData.listPriceGross.toFloat();
				
				/* Add to totals */
				ret.stock += stock;
				ret.minPrice = ((ret.minPrice > 0) ? Math.min(price, ret.minPrice) : price);
				ret.maxPrice = Math.max(price, ret.maxPrice);
				ret.minListPrice = ((ret.minListPrice > 0) ? Math.min(listPrice, ret.minListPrice) : listPrice);
				ret.maxListPrice = Math.max(listPrice, ret.maxListPrice);
			});
		}
		return ret;
	},
	
	isAvailable: function(checkLevel, checkValue) {
		
		/* Always available if on backorder */
		if (this.options.backOrder) { return true; }
		
		/* Check for at least some stock */
		var matchStr = this.getMatchStr(checkLevel, checkValue);
		return this.stock.some(function(stockData, sku) {
			return sku.match(matchStr) && (stockData.stock > 0);
		});
	},
	
	allOptionsSelected: function() {
	
		/* Check all options have a selected value */
		return this.selected.every(function(value) {
			return value != null;
		});
	},
	
	getShippingCost: function() {
		
		var shippingCost;
		var request = new Request.JSON({
			url: SITEURL+'/shipping_cost',
			async: false,
			onComplete: function(cost) {
				shippingCost = cost;
			},
			format: 'json'
		}).get(SITEURL+'/products?'+this.form.toQueryString()+'&task=shipping_cost');
		
		return shippingCost;
	}

});
