/**
 * Wizard
 * 
 * Wizard stages inline submission and loading.
 *
 * @package		BluCommerce
 * @subpackage  FrontendClientside
 */
var Wizard = new Class({

	Implements: Options,

	container: null,
	form: null,
	history: null,
	spinner: null,
	
	stage: null,
	content: null,
	
	stageDetails: null,
	currentStageNum: null,
	
	options: {
		historyKey: null,
		useHistory: true,
		stageDetails: null,
		currentStageNum: 1,
		baseUrl: null,
		stageViewTask: 'view_stage',
		sidebarTask: 'view_sidebar',
		useFancyForm: true
	},
	
	initialize: function(container, sidebar, options) {
		this.setOptions(options);
		this.container = container;
		this.sidebar = sidebar;
		
		/* Add history */
		this.historyKey = $pick(this.options.historyKey, 'stage');
		if (this.options.useHistory) {
			this.history = HistoryManager.register(
				this.historyKey,
				[],
				function(args) {
					if (args[0] && args[0] != this.options.currentStageNum) { 
						this.gotoStage(this.options.baseUrl+'/'+args[0]);
					}
				}.bind(this),
				false,
				false);
		}
		
		/* Build waiter */
		this.waiter = new Waiter(this.container);
		
		/* Add scroller */
		this.scroll = new Fx.Scroll(window, {
			wait: false,
			duration: 200,
			offset: { 'x': 0, 'y': -80}
		});
		
		/* Get stages details */
		this.stageDetails = this.options.stageDetails;
		this.currentStageNum = this.options.currentStageNum;
		if (this.history) { this.history.setValue.delay(100, this, [0, this.currentStageNum]); }
		
		/* Get current stage elements */
		this.stage = this.container.getElementById(this.stageDetails[this.currentStageNum-1].id);
		this.content = this.stage.getElement('div.stagecontent');
		
		/* Add edit link click handlers */
		this.container.getElements('a.stage-edit').each(function(link) {
			link.addEvent('click', this.editClickHandler.bindWithEvent(this, link));
		}, this);
		
		/* Build stage content */
		this.buildContent();
		if (this.sidebar) {
			this.buildSidebar();
		}

	},
	
	buildContent: function() {
	
		/* Add content effect */
		this.content.set('tween', {
			duration: 800,
			fps: 100
		});
		this.content.setStyle('overflow', 'hidden');
		
		this.content.getElements('form').each(function(form) {
		
			/* Add form validation handlers */
			if (!form.hasClass('no-submit-handling')) {
				var formValidator = new FormValidator(form, {
					onFormValidate: this.formSubmitHandler.bindWithEvent(this),
					useTitles: true,
					errorPrefix: '',
					evaluateFieldsOnBlur: false,
					evaluateFieldsOnChange: false
				});
			}
			
			if (form.hasClass('disable-on-submit')) {
				form.addEvent('submit', function(){
					submitButton = form.getElement('input[name=submit]');
					if (!submitButton) {
						submitButton = form.getElement('button[name=submit]')
					}
					submitButton.setProperty('disabled','disabled');
				});
			}
			
			/* Attach fancy form styling to radio buttons and checkboxes */
			if (this.options.useFancyForm) {
				var fancyForm = new FancyForm(form);
			}
			
			/* Add captcha reload */
			var captchaReload = form.getElement('div.captcha-reload');
			if (captchaReload) {
				captchaReload.setStyle('display', 'block');
				captchaReload.getElement('a').addEvent('click', this.reloadCaptchaClick.bindWithEvent(this, form));
			}
			
		}, this);
		
		/* Popups */
		var infoPopups = new InfoPopups(this.content.getElements('a.info-popup'));	
	},
	
	editClickHandler: function(event, link) {
		this.gotoStage(link.get('href'));
		event.stop();
	},
	
	reloadCaptchaClick: function(event, form) {
		form.getElement('img.captcha-img').set('src', SITEURL+'/captcha?format=raw&'+$time());
		event.stop();
	},
	
	gotoStage: function(url) {
	
		this.waiter.start(this.container);
		
		/* Send request */
		var jsonRequest = new Request.JSON({
			format: 'json',
			url: url,
			method: 'get',
			onComplete: this.onStageLoadComplete.bindWithEvent(this, true)
		}).send('task='+this.options.stageViewTask);
	
	},
	
	formSubmitHandler: function(validationResult, form, event) {
	
		if (validationResult === true) {
			this.waiter.start(this.container);
			
			/* AJAX post unless we're doing standard uploads */
			var formType = form.get('enctype');
			if (formType != 'multipart/form-data') {
				var jsonRequest = new Request.JSON({
					format: 'json',
					url: form.get('action'),
					onComplete: this.onStageLoadComplete.bindWithEvent(this)
				}).post(form);
				
				event.stop();
			}
		
		/* Validation failed - don't submit */
		} else {
			event.stop();
		}
	},
	
	reloadSidebar: function() {
		
		/* Send request */
		var request = new Request.HTML({
			format: 'raw',
			url: this.options.baseUrl,
			method: 'get',
			update: this.sidebar,
			onComplete: this.buildSidebar.bindWithEvent(this)
		}).send('task='+this.options.sidebarTask);
	
	},
	
	buildSidebar: function() {
	
		/* Add stage edit click events */
		this.sidebar.getElements('a.stage-edit').each(function(link) {
			link.addEvent('click', this.editClickHandler.bindWithEvent(this, link));
		}, this);
		
	},
	
	close: function(stagecontent) {
		var size = stagecontent.getElement('div.wrapper').getSize();
		stagecontent.get('tween').start('height', size.y, 0).chain(function() {
			var hide = function() {
				stagecontent.setStyle('display', 'none');
				stagecontent.empty();
			}.delay(200);
		});
	},
	
	open: function(stagecontent) {
		var size = stagecontent.getElement('div.wrapper').getSize();
		stagecontent.get('tween').start('height', size.y).chain(function() {
			stagecontent.setStyle('height', 'auto');
			
			/* re-apply hasLayout for IE6 */
			if (Browser.Engine.trident4) {
				stagecontent.setStyle('zoom', '1');
			}
			
			/* scroll into view */
			this.scroll.toElement(stagecontent);
		}.bind(this));
	},

	onStageLoadComplete: function(response) {
		
		try {
			/* Got a new stage */
			if (response.stageNum) {
				
				/* Store reference to old content */
				var oldContent = this.content;
				var isNewStage = (response.stageNum != this.currentStageNum);
				
				/* Get current stage elements */
				this.stage = this.container.getElementById(this.stageDetails[response.stageNum-1].id);
				this.content = this.stage.getElement('div.stagecontent');
				
				/* Get initial height */
				var height = 0;
				var size;
				if (!isNewStage) {
					size = this.content.getSize();
					height = size.y;
				}
				
				/* Set stage content */
				response.html = response.content.stripScripts(function(script){
					response.javascript = script;
				});
				this.content.setStyles({
					'display': 'block',
					'height': height
				});
				var buildContent = function() {
					this.content.set('html', response.html); 
					$exec(response.javascript);
					this.buildContent();
				}.delay(50, this);
				
				/* Update stage statuses */
				this.stage.set('class', 'current');
				this.stage.getAllNext().set('class', 'stage incomplete');
				this.stage.getAllPrevious().set('class', 'stage complete');
				
				/* Hide/show edit links */
				response.stageDetails.each(function(stage) {
					var editLink = this.container.getElementById(stage.id).getElement('a.stage-edit');
					editLink.setStyle('display', (stage.edit ? 'block' : 'none'));
				}, this);
				
				/* Hide waiter and slide sections once the dust has settled */
				var slide = function(isNewStage, oldContent, newContent) {
					this.waiter.stop();
					if (isNewStage) { this.close(oldContent); }
					this.open(newContent);
				}.delay(200, this, [isNewStage, oldContent, this.content]);
				
				/* Re-load sidebar */
				if (this.sidebar) {
					this.reloadSidebar();
				}
				
				/* Store current stages details */
				this.stageDetails = response.stageDetails;
				this.currentStageNum = response.stageNum;
				if (this.history) { this.history.setValue(0, this.currentStageNum); }
	
			} 
			
			/* Got messages */
			if(response.messages) {
				
				/* Get existing messages holder, else create */
				var messages = this.stage.retrieve('messages', new Element('div'));
				
				/* Show messages */
				messages.set('html', response.messages);
				this.waiter.stop();
				messages.injectTop(this.content);
				this.scroll.toElement(messages);
				
			}
		} catch(err) {
			
			/* Fall back to reloading checkout on error */
			if (DEBUG) {
				console.log(err);
			} else {
				window.location = this.options.baseUrl;
			}
			
		}
	}

});