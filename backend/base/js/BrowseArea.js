var BrowseArea = new Class({
	
	Implements: Options,
	
	container: null,
	history: null,
	request: null,
	
	qs: null,
	
	options: {
		historyKey: 'items',
		useHistory: true,
		updateTask: 'listing',
		scrollTo: null,
		evalScripts: false
	},
	
	initialize: function(container, options){
		this.container = $(container);
		this.setOptions($merge({
			/* Set the container to scroll to, on completion */
			scrollTo: this.container
		}, options));
		
		/* Add history */
		if (this.options.useHistory) {
			this.history = HistoryManager.register(
				this.options.historyKey,
				[],
				this.onHistoryMatch.bind(this),
				false,
				this.options.historyKey + '-([^;]*)');
		}
		
		/* Build listing update request */
		this.request = new Request.HTML({
			format: 'raw',
			method: 'get',
			onComplete: this.onLoadComplete.bind(this),
			evalScripts: this.options.evalScripts,
			link: 'cancel',
			update: this.container,
			useWaiter: true
		});
		
		/* Add events */
		this.addUpdateEvents();
		
		return this;
	},
	
	addUpdateEvents: function() {
	
		/* Add form events */
		var reloadForms = this.container.getElements('form.reloads');
		if (reloadForms) {
			reloadForms.each(function(form) {
			
				/* Add form submit events */
				form.addEvent('submit', this.onFormSubmit.bindWithEvent(this, form));
			
				/* Add form reload events */
				form.getElements('select.reloads').each(function(select) {
					select.addEvent('change', this.onOptionChange.bindWithEvent(this, form));
				}, this);
			}, this);
		}
		
		/* Add link click event handlers */
		this.container.getElements('div#browse_bar a, div.pagination a').each(function(link) {
			link.addEvent('click', this.onLinkClick.bindWithEvent(this, link));
		}, this);
	},
	
	onHistoryMatch: function(args) {
		var qs = $pick(args[0], false);
		
		/* Update listing? */
		if (qs && (qs != this.qs)) {
			this.qs = qs;
			this.request.send((qs ? qs + '&' : '')+'task='+this.options.updateTask);
		}
	},
	
	onLoadComplete: function() {
		
		/* Add update events */
		this.addUpdateEvents();
		
		/* Scroll to top of listin */
		new Fx.Scroll(window, {
			duration: 'long'
		}).toElement($(this.options.scrollTo));
	
	},
	
	onLinkClick: function(event, link) {	
		var href = link.get('href');
		var qs = href.slice(href.indexOf('?') + 1);
		
		this.updateListing(qs);
		event.stop();
	},
	
	onOptionChange: function(event, form) {
		this.updateListing(form.toQueryString());
	},
	
	onFormSubmit: function(event, form) {
		this.updateListing(form.toQueryString());
		event.stop();
	},
	
	updateListing: function(qs) {
		
		/* Update history and store current query string */
		this.qs = qs;
		if (this.history) {
			this.history.setValue(0, qs);
		}
		
		/* Send request */
		this.request.send((qs ? qs + '&' : '')+'task='+this.options.updateTask);
	}

});