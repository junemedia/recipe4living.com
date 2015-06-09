/**
 * Fancy Select Library
 *
 * Select drop-down replacement.
 * Based on: elSelect by Sergey Korzhov aka elPas0, www.cult-f.net/2007/12/14/elSelect
 *
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var FancySelect = new Class({

	Implements: [Events, Options],
	
	options: {
		baseClass: 'fancyselect',
		disabledOpacity : 0.2
	},
	
	source: false,
	
	selectBox: false,
	optionsContainer: false,
	
	optionItems: [],
	defaultIndex: null,
	defaultValue: null,
	selectedIndex: null,
	
	selectedOption: false,
	
	dropped: false,

	initialize: function(source, options) {	
		this.setOptions(options);
		this.source = $(source);
		
		if (!this.source) { return; }

		/* Build framework */
		this.buildFrameWork();
		
		/* Add options */
		this.source.getElements('option').each(this.addOption, this);
		
		/* Add events */
		this.selectBox.addEvents({
			'click': this.onDropDown.bindWithEvent(this),
			'outerClick': this.hideDropDown.bind(this)
		});
		
		/* Hide source select and inject replacement */
		this.source.setStyle('display', 'none');
		this.selectBox.inject(this.source, 'after');
	},
	
	buildFrameWork: function() {
	
		var size = this.source.getSize();
	
		this.selectBox = new Element('div', {
			'class': this.options.baseClass,
			'styles': {
				'width': size.x+'px'
			}
		});
		this.selectedOption = new Element('div', {
			'class': 'selected-option'
		});
		this.optionsContainer = new Element('div', {
			'class': 'options-container'
		});
		this.selectBox.adopt(this.selectedOption, this.optionsContainer);
	},

	addOption: function(option, index) {
		
		/* Build option */
    	var o = new Element('div', {
			'class': 'option',
			'value': option.getProperty('value')
		});
		o.addClass(option.getProperty('class'));
		
		/* Add events */
		o.addEvents({
			'click': this.onOptionClick.bindWithEvent(this, [option, o, index]),
			'mouseenter': this.onOptionMouseEnter.bindWithEvent(this, [option, o, index]),
			'mouseleave': this.onOptionMouseLeave.bindWithEvent(this, [option, o, index])
		});
		option.addEvent('updated', this.onOptionUpdated.bindWithEvent(this, [option, o, index]));
		
		/* Set html and update */
		o.set('html', this.getOptionHTML(option));
		option.fireEvent('updated');
		
		/* Inject option and push onto stack */
		o.injectInside(this.optionsContainer);
		this.optionItems.push(option);
	},
	
	getOptionHTML: function(option) {
		var html = option.get('text');
			
		/* Split special options into spans for styling */
		if (option.hasClass('splitoncomma')) {
			html = '<span class="item">'+html.split(', ').join('</span><span class="item">')+'</span>';
			html += '<span class="clear"></span>';
		}
		
		return html;	
	},
	
	onDropDown: function(event) {
		if (this.dropped) {
			this.hideDropDown();
		} else {
			this.showDropDown();
		}
	},
	
	showDropDown: function() {
		/*this.optionsContainer.setStyle('width', this.selectBox.offsetWidth-2);*/
		this.selectBox.setStyle('z-index', 9);
		this.optionsContainer.setStyle('display', 'block');
		if (this.optionsContainer.offsetHeight > 300) {
			this.optionsContainer.setStyle('height', 300);
		}
		this.dropped = true;
	},
	
	hideDropDown: function() {
		this.optionsContainer.setStyle('display', 'none');
		this.selectBox.setStyle('z-index', 0);
		this.optionsContainer.setStyle('height', 'auto');
		this.dropped = false;
	},
	
	onOptionClick: function(event, option, o, index) {
		if (option.getProperty('disabled')) {
			return;
		}
		
		/* Set selected option display text and value */
		this.selectedOption.set('html', '<span class="holder">'+o.get('html')+'</span>');
		this.source.setProperty('value', o.getProperty('value'));
		
		/* Store selected index and fire change event */
		this.selectedIndex = index;
		this.source.fireEvent('change');
	},
	
	onOptionMouseEnter: function(event, option, o, index) {
		o.addClass('over');
	},
	
	onOptionMouseLeave: function(event, option, o, index) {
		o.removeClass('over');
	},
	
	onOptionUpdated: function(event, option, o, index) {
		if (option.getProperty('disabled')) {
			o.addClass('disabled');
			o.setOpacity(this.options.disabledOpacity);
			if (this.source.hasClass('hidedisabled')) {
				o.setStyle('display', 'none');
			}
			
			/* Reset to default if currently selected option gets disabled */
			if (this.selectedIndex == index) {
				var defaultOption = this.optionItems[this.defaultIndex];
				this.selectedOption.set('html', '<span class="holder">'+defaultOption.get('html')+'</span>');
				this.source.setProperty('value', this.defaultValue);
			}
			
		} else {
			o.removeClass('disabled');
			o.setOpacity(1);
			o.setStyle('display', 'block');
			
			/* Update current option if we've just got selected */
			if (option.getProperty('selected') && (this.selectedIndex != index)) {
				this.selectedOption.set('html', '<span class="holder">'+o.get('html')+'</span>');
				this.source.setProperty('value', o.getProperty('value'));
			}
		}
	}
	
});

/**
 * Fancy Form Library
 *
 * Based on: FancyForm 0.91 by Vacuous Virtuoso, lipidity.com
 *
 * Checkbox and radio input replacement script.
 * Toggles defined class when input is selected.
 *
 * @package	 BluCommerce
 * @subpackage  FrontendClientside
 */
var FancyForm = new Class({

	Implements: [Events, Options],

	options: {
		fancySelect: false,
		onclasses: {
			checkbox: 'checked',
			radio: 'selected'
		},
		offclasses: {
			checkbox: 'unchecked',
			radio: 'unselected'
		},
		resetClears: false,
		disabledOpacity : 0.2
	},

	form: null,
	elements: [],
	inputs: [],
	selects: [],

	initialize: function(form, options){
		this.setOptions(options);
		this.form = form;
		
		/* Add inputs */
		this.form.getElements('input[type=checkbox], input[type=radio]').each(this.addInput, this);
		
		/* Fancy select */
		if (this.options.fancySelect) {
			this.form.getElements('select').each(function(select) {
				this.selects.push(new FancySelect(select));		
			}, this);
		}
		
		/* Add reset event */
		this.form.addEvent('reset', this.onReset.bindWithEvent(this));
	},
	
	addInput: function(input){
	
		/* Move input out of view */
		input.setStyle('position', 'absolute');
		input.setStyle('left', '-9999px');
		
		/* Get label and set type and name */
		var el = input.getParent();
		el.type = input.getProperty('type');
		el.name = input.getProperty('name');
		el.value = input.getProperty('value');

		/* Add events */
		el.addEvent('click', this.onElementClick.bindWithEvent(this, [input, el]));
		input.addEvents({
			'click': this.onInputClick.bindWithEvent(this, [input, el]),
			'updated': this.onUpdated.bindWithEvent(this, [input, el])
		});
		if (!Browser.Engine.trident4) {
			input.addEvents({
				'focus': this.onFocus.bindWithEvent(this, [input, el]),
				'blur': this.onBlur.bindWithEvent(this, [input, el])
			});
		}		
		input.fireEvent('updated');

		/* Push onto stack */
		this.elements.push(el);
		this.inputs.push(input);
	},
	
	onElementClick: function(event, input, el) {
		event.stop();
		if (input.getProperty('disabled')) { return; }
		input.setProperty('checked', ((el.type == 'radio') ? true : !input.getProperty('checked')));
		event.target = input;
		input.fireEvent('click', event);
		document.fireEvent('click', event);
	},
	
	onInputClick: function(event, input, el) {
		if ($defined(event)) { event = new Event(event); event.stopPropagation(); }
		if (input.getProperty('disabled')) { return; }
		if (!el.hasClass(this.options.onclasses[el.type])) { this.select(el); }
		else if (el.type != 'radio') { this.deselect(el); }
	},
	
	onFocus: function(event, input, el) {
		el.setStyle('outline', '1px dotted');
	},
	
	onBlur: function(event, input, el) {
		el.setStyle('outline', '0');
	},

	select: function(el) {
		el.removeClass(this.options.offclasses[el.type]);
		el.addClass(this.options.onclasses[el.type]);
		if (el.type == 'radio') {
			this.elements.each(function(other) {
				if (other.name != el.name || other == el) { return; }
				this.deselect(other);
				
				/* Highlight all previous rating stars */
				if (el.hasClass('star')) {
					if (other.get('value') < el.get('value')) {
						other.addClass('on');
					} else {
						other.removeClass('on');
					}
				}
			}, this);
		}
	},

	deselect: function(el) {
		el.removeClass(this.options.onclasses[el.type]);
		el.addClass(this.options.offclasses[el.type]);
	},

	onUpdated: function(event, input, el) {
	
		/* Enabled */
		if (input.getProperty('disabled')) {
			el.addClass('disabled');
			el.setOpacity(this.options.disabledOpacity);
		} else {
			el.removeClass('disabled');
			el.setOpacity(1);
		}
		
		/* Checked? */
		if (input.getProperty('checked')) {
			this.select(el);
		} else {
			this.deselect(el);
		}
	},
	
	onReset: function(event) {
		this.elements.each(function(el, index) {
			var input = this.inputs[index];
			
			/* Always clear? */
			if (this.options.resetClears) {
				input.setProperty('checked', false);
				this.deselect(el);
				
			/* Respect default value */
			} else if (input.defaultChecked) {
				this.select(el);
			} else {
				this.deselect(el);
			}
		}, this);
	}

});

/**
 * Swiff.Uploader - Flash FileReference Control
 *
 * @version		1.1.1
 * @license		MIT License
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @copyright	Authors
 * @package		BluCommerce
 * @subpackage  FrontendClientside
 */
Swiff.Uploader = new Class({

	Extends: Swiff,

	Implements: Events,

	options: {
		path: 'Swiff.Uploader.swf',
		multiple: true,
		queued: true,
		typeFilter: null,
		url: null,
		method: 'post',
		data: null,
		fieldName: 'Filedata',
		callBacks: null
	},

	initialize: function(options){
		if (Browser.Plugins.Flash.version < 9) {
			return false;
		}
		this.setOptions(options);

		var callBacks = this.options.callBacks || this;
		if (callBacks.onLoad) {
			this.addEvent('onLoad', callBacks.onLoad);
		}

		var prepare = {}, self = this;
		['onSelect', 'onAllSelect', 'onCancel', 'onBeforeOpen', 'onOpen', 'onProgress', 'onComplete', 'onError', 'onAllComplete'].each(function(index) {
			var fn = callBacks[index] ? callBacks[index] : $empty;
			prepare[index] = function() {
				self.fireEvent(index, arguments, 10);
				return fn.apply(self, arguments);
			};
		});

		prepare.onLoad = this.load.create({delay: 10, bind: this});
		this.options.callBacks = prepare;

		var path = this.options.path;
		if (!path.contains('?')) {
			path += '?noCache=' + $time(); // quick fix
		}
		delete this.options.params.wMode;
		this.parent(path);

		if (!this.options.container) {
			document.body.appendChild(this.object);
		}
		return this;
	},

	load: function(){
		this.remote('register', this.instance, this.options.multiple, this.options.queued);
		this.fireEvent('onLoad');
	},

	/*
	Method: browse
		Open the file browser.
	*/
	browse: function(typeFilter){
		return this.remote('browse', $pick(typeFilter, this.options.typeFilter));
	},

	/*
	Method: upload
		Starts the upload of all selected files.
	*/
	upload: function(options){
		var current = this.options;
		options = $extend({data: current.data, url: current.url, method: current.method, fieldName: current.fieldName}, options);
		if ($type(options.data) == 'element') {
			options.data = $(options.data).toQueryString();
		}
		return this.remote('upload', options);
	},

	/*
	Method: removeFile
		For multiple uploads cancels and removes the given file from queue.

	Arguments:
		name - (string) Filename
		name - (string) Filesize in byte
	*/
	removeFile: function(file){
		if (file) {
			file = {name: file.name, size: file.size};
		}
		return this.remote('removeFile', file);
	},

	/*
	Method: getFileList
		Returns one Array with with arrays containing name and size of the file.

	Returns:
		(array) An array with files
	*/
	getFileList: function(){
		return this.remote('getFileList');
	}

});

/**
 * Fx.ProgressBar
 *
 * @version		1.0
 * @license		MIT License
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @copyright	Authors
 * @package		BluCommerce
 * @subpackage  FrontendClientside
 */
Fx.ProgressBar = new Class({

	Extends: Fx,

	options: {
		text: null,
		transition: Fx.Transitions.Circ.easeOut,
		link: 'cancel'
	},

	initialize: function(element, options) {
		this.element = $(element);
		this.parent(options);
		this.text = $(this.options.text);
		this.set(0);
	},

	start: function(to, total) {
		return this.parent(this.now, (arguments.length == 1) ? to.limit(0, 100) : to / total * 100);
	},

	set: function(to) {
		this.now = to;
		this.element.setStyle('backgroundPosition', (100 - to) + '% 0px');
		if (this.text) {
			this.text.set('text', Math.round(to) + '%');
		}
		return this;
	}

});

/**
 * FancyUpload - Flash meets Ajax for simply working uploads
 *
 * @version		2.0 beta 4
 * @license		MIT License
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @copyright	Authors
 * @package		BluCommerce
 * @subpackage  FrontendClientside
 */
var FancyUpload2 = new Class({

	Extends: Swiff.Uploader,

	options: {
		limitSize: false,
		limitFiles: 5,
		instantStart: false,
		allowDuplicates: false,
		validateFile: $lambda(true), // provide a function that returns true for valid and false for invalid files.

		fileInvalid: null, // called for invalid files with error stack as 2nd argument
		fileCreate: null, // creates file element after select
		fileUpload: null, // called when file is opened for upload, allows to modify the upload options (2nd argument) for every upload
		fileComplete: null, // updates the file element to completed state and gets the response (2nd argument)
		fileRemove: null // removes the element
		/**
		 * Events:
		 * onSelect, onAllSelect, onCancel, onBeforeOpen, onOpen, onProgress, onComplete, onError, onAllComplete
		 */
	},

	initialize: function(status, list, options) {
		this.status = $(status);
		this.list = $(list);

		this.files = [];

		if (options.callBacks) {
			this.addEvents(options.callBacks);
			options.callBacks = null;
		}
		this.parent(options);
		this.render();
	},

	render: function() {
		this.overallTitle = this.status.getElement('.overall-title');
		this.currentTitle = this.status.getElement('.current-title');
		this.currentText = this.status.getElement('.current-text');

		var progress = this.status.getElement('.overall-progress');
		this.overallProgress = new Fx.ProgressBar(progress, {
			text: new Element('span', {'class': 'progress-text'}).inject(progress, 'after')
		});
		progress = this.status.getElement('.current-progress');
		this.currentProgress = new Fx.ProgressBar(progress, {
			text: new Element('span', {'class': 'progress-text'}).inject(progress, 'after')
		});
	},

	onLoad: function() {
		this.log('Uploader ready!');
	},

	onBeforeOpen: function(file, options) {
		this.log('Initialize upload for "{name}".', file);
		var fn = this.options.fileUpload;
		return (fn) ? fn.call(this, this.getFile(file), options) : null;
	},

	onOpen: function(file, overall) {
		this.log('Starting upload "{name}".', file);
		file = this.getFile(file);
		file.element.addClass('file-uploading');
		this.currentProgress.cancel().set(0);
		this.currentTitle.set('html', 'File Progress "{name}"'.substitute(file) );
	},

	onProgress: function(file, current, overall) {
		this.overallProgress.start(overall.bytesLoaded, overall.bytesTotal);
		this.currentText.set('html', 'Upload with {rate}/s. Time left: ~{timeLeft}'.substitute({
			rate: current.rate ? this.sizeToKB(current.rate) : '- B',
			timeLeft: Date.fancyDuration(current.timeLeft || 0)
		}));
		this.currentProgress.start(current.bytesLoaded, current.bytesTotal);
	},

	onSelect: function(file, index, length) {
		var errors = [];
		if (this.options.limitSize && (file.size > this.options.limitSize)) {
			errors.push('size');
		}
		if (this.options.limitFiles && (this.countFiles() >= this.options.limitFiles)) {
			errors.push('length');
		}
		if (!this.options.allowDuplicates && this.getFile(file)) {
			errors.push('duplicate');
		}
		if (!this.options.validateFile.call(this, file, errors)) {
			errors.push('custom');
		}
		if (errors.length) {
			var fn = this.options.fileInvalid;
			if (fn) {
				fn.call(this, file, errors);
			}
			return false;
		}
		var fileCreateFn = (this.options.fileCreate || this.fileCreate);
		fileCreateFn.call(this, file);
		this.files.push(file);
		return true;
	},

	onAllSelect: function(files, current, overall) {
		this.log('Added ' + files.length + ' files, now we have (' + current.bytesTotal + ' bytes).', arguments);
		this.updateOverall(current.bytesTotal);
		this.status.removeClass('status-browsing');
		if (this.files.length && this.options.instantStart) {
			this.upload.delay(10, this);
		}
	},

	onComplete: function(file, response) {
		this.log('Completed upload "' + file.name + '".', arguments);
		this.currentText.set('html', 'Upload complete!');
		this.currentProgress.start(100);
		var fn = (this.options.fileComplete || this.fileComplete);
		fn.call(this, this.finishFile(file), response);
	},

	onError: function(file, error, info) {
		this.log('Upload "' + file.name + '" failed. "{1}": "{2}".', arguments);
		var fn = (this.options.fileError || this.fileError);
		fn.call(this, this.finishFile(file), error, info);
	},

	onCancel: function() {
		this.log('Filebrowser cancelled.', arguments);
		this.status.removeClass('file-browsing');
	},

	onAllComplete: function(current) {
		this.log('Completed all files, ' + current.bytesTotal + ' bytes.', arguments);
		this.updateOverall(current.bytesTotal);
		this.overallProgress.start(100);
		this.status.removeClass('file-uploading');
	},

	browse: function(fileList) {
		var ret = this.parent(fileList);
		if (ret !== true){
			this.log('Browse in progress.');
			if (ret) {
				alert(ret);
			}
		} else {
			this.log('Browse started.');
			this.status.addClass('file-browsing');
		}
	},

	upload: function(options) {
		var ret = this.parent(options);
		if (ret !== true) {
			this.log('Upload in progress or nothing to upload.');
			if (ret) {
				alert(ret);
			}
		} else {
			this.log('Upload started.');
			this.status.addClass('file-uploading');
			this.overallProgress.set(0);
		}
	},

	removeFile: function(file) {
		var remove = this.options.fileRemove || this.fileRemove;
		if (!file) {
			this.files.each(remove, this);
			this.files.empty();
			this.updateOverall(0);
		} else {
			if (!file.element) {
				file = this.getFile(file);
			}
			this.files.erase(file);
			remove.call(this, file);
			this.updateOverall(this.bytesTotal - file.size);
		}
		this.parent(file);
	},

	getFile: function(file) {
		var ret = null;
		this.files.some(function(value) {
			if ((value.name != file.name) || (value.size != file.size)) {
				return false;
			}
			ret = value;
			return true;
		});
		return ret;
	},

	countFiles: function() {
		var ret = 0;
		for (var i = 0, j = this.files.length; i < j; i++) {
			if (!this.files[i].finished) {
				ret++;
			}
		}
		return ret;
	},

	updateOverall: function(bytesTotal) {
		this.bytesTotal = bytesTotal;
		this.overallTitle.set('html', 'Overall Progress (' + this.sizeToKB(bytesTotal) + ')');
	},

	finishFile: function(file) {
		file = this.getFile(file);
		file.element.removeClass('file-uploading');
		file.finished = true;
		return file;
	},

	fileCreate: function(file) {
		file.info = new Element('span', {'class': 'file-info'});
		file.element = new Element('li', {'class': 'file'}).adopt(
			new Element('span', {'class': 'file-size', 'html': this.sizeToKB(file.size)}),
			new Element('a', {
				'class': 'file-remove',
				'href': '#',
				'html': 'Remove',
				'events': {
					'click': function() {
						this.removeFile(file);
						return false;
					}.bind(this)
				}
			}),
			new Element('span', {'class': 'file-name', 'html': file.name}),
			file.info).inject(this.list);
	},

	fileComplete: function(file, response) {
		var json = $H(JSON.decode(response, true));
		if (json.get('result') == 'success') {
			file.element.addClass('file-success');
			file.info.set('html', json.get('size'));
		} else {
			file.element.addClass('file-failed');
			file.info.set('html', json.get('error') || response);
		}
	},

	fileError: function(file, error, info) {
		file.element.addClass('file-failed');
		file.info.set('html', '<strong>' + error + '</strong><br />' + info);
	},

	fileRemove: function(file) {
		file.element.fade('out').retrieve('tween').chain(Element.destroy.bind(Element, file.element));
	},

	sizeToKB: function(size) {
		var unit = 'B';
		if ((size / 1048576) > 1) {
			unit = 'MB';
			size /= 1048576;
		} else if ((size / 1024) > 1) {
			unit = 'kB';
			size /= 1024;
		}
		return size.round(1) + ' ' + unit;
	},

	log: function(text, args) {
		if (window.console) {
			console.log(text.substitute(args || {}));
		}
	}

});

/**
 * Date extensions
 */
Date.parseDuration = function(sec) {
	var units = {}, conv = Date.durations;
	for (var unit in conv) {
		var value = Math.floor(sec / conv[unit]);
		if (value) {
			units[unit] = value;
			if (!(sec -= value * conv[unit])) {
				break;
			}
		}
	}
	return units;
};

Date.fancyDuration = function(sec) {
	var ret = [], units = Date.parseDuration(sec);
	for (var unit in units) {
		ret.push(units[unit] + Date.durationsAbbr[unit]);
	}
	return ret.join(', ');
};

Date.durations = {years: 31556926, months: 2629743.83, days: 86400, hours: 3600, minutes: 60, seconds: 1, milliseconds: 0.001};
Date.durationsAbbr = {
	years: 'j',
	months: 'm',
	days: 'd',
	hours: 'h',
	minutes: 'min',
	seconds: 'sec',
	milliseconds: 'ms'
};

/** 
 * OverText
 *
 * Shows text over an input that disappears when the user clicks into it.
 * The text remains hidden if the user adds a value.
 *
 * @license http://clientside.cnet.com/wiki/cnet-libraries#license
 * @package		BluCommerce
 * @subpackage  FrontendClientside
 */
var OverText = new Class({
	Implements: [Options, Events],
	options: {
		/*textOverride: null,*/
		/*onTextHide: $empty,
		onTextShow: $empty*/
	},
	overTxtEls: [],
	initialize: function(inputs, options) {
		this.setOptions(options);
		$$(inputs).each(this.addElement, this);
		OverText.instances.push(this);
	},
	addElement: function(el){
		if (this.overTxtEls.contains(el) || el.retrieve('overtext')) {
			return;
		}
		var val = this.options.textOverride || el.get('alt') || el.get('title');
		if (!val) {
			return;
		}
		this.overTxtEls.push(el);
		var txt = new Element('div', {
			'class': 'overtext',
			html: val,
			events: {
				click: this.hideTxt.pass([el, true], this)
			},
			styles: {
				position: 'absolute',
				padding: el.getStyle('padding')
			}
		}).inject(el, 'after');
		el.addEvents({
			focus: this.hideTxt.pass([el, true], this),
			blur: this.testOverTxt.pass(el, this),
			change: this.testOverTxt.pass(el, this)
		}).store('overtext', txt);
		window.addEvent('resize', this.repositionAll.bind(this));
		this.testOverTxt(el);
		this.repositionOverTxt(el);
	},
	hideTxt: function(el, focus){
		var txt = el.retrieve('overtext');
		if (txt && (txt.getStyle('display') == 'block') && !el.get('disabled')) {
			txt.setStyle('display', 'none');
			try {
				if (focus) {
					el.fireEvent('focus').focus();
				}
			} catch(e){} //IE barfs if you call focus on hidden elements
			this.fireEvent('onTextHide', [txt, el]);
		}
		return this;
	},
	showTxt: function(el){
		var txt = el.retrieve('overtext');
		if (txt && !(txt.getStyle('display') == 'block')) {
			txt.setStyle('display', 'block');
			this.fireEvent('onTextShow', [txt, el]);
		}
		return this;
	},
	testOverTxt: function(el){
		if (el.get('value')) {
			this.hideTxt(el);
		} else {
			this.showTxt(el);
		}	
	},
	repositionAll: function(){
		this.overTxtEls.each(this.repositionOverTxt.bind(this));
		return this;
	},
	repositionOverTxt: function (el){
		if (!el) {
			return;
		}
		try {
			var txt = el.retrieve('overtext');
			if (!txt) {
				return;
			}
			this.testOverTxt(el);
			txt.setStyles({
				width: el.offsetWidth - el.getStyle('padding-left').toInt() - el.getStyle('padding-right').toInt(),
				height: el.offsetHeight - el.getStyle('padding-top').toInt() - el.getStyle('padding-bottom').toInt(),
				left: el.offsetLeft + el.getStyle('border-left-width').toInt(),
				top: el.offsetTop + el.getStyle('border-top-width').toInt()
			});
			if (el.offsetHeight) {
				this.testOverTxt(el);
			} else {
				this.hideTxt(el);
			}
		} catch(e){}
		return this;
	}
});
OverText.instances = [];
OverText.update = function(){
	return OverText.instances.map(function(ot){
		return ot.repositionAll();
	});
};

/**
 * FormValidator
 * 
 * A css-class based form validation system.
 *
 * @license http://clientside.cnet.com/wiki/cnet-libraries#license
 * @package		BluCommerce
 * @subpackage  FrontendClientside
 */
var InputValidator = new Class({
	Implements: [Options],
	initialize: function(className, options){
		this.setOptions({
			errorMsg: 'Validation failed.',
			test: function(field){ return true; }
		}, options);
		this.className = className;
	},
	test: function(field){
		if ($(field)) {
			return this.options.test($(field), this.getProps(field));
		} else {
			return false;
		}
	},
	getError: function(field){
		var err = this.options.errorMsg;
		if ($type(err) == "function") {
			err = err($(field), this.getProps(field));
		}
		return err;
	},
	getProps: function(field){
		if ($(field) && $(field).get('validatorProps')){
			try {
				return JSON.decode($(field).get('validatorProps'));
			} catch(e) { return {}; }
		} else {
			return {};
		}
	}
});

var FormValidator = new Class({
	Implements:[Options, Events],
	options: {
		fieldSelectors:"input, select, textarea",
		useTitles:false,
		evaluateOnSubmit:true,
		evaluateFieldsOnBlur: true,
		evaluateFieldsOnChange: true,
		serial: true,
		warningPrefix: "Warning: ",
		errorPrefix: "Error: "
		/*onFormValidate: function(isValid, form){},
		onElementValidate: function(isValid, field){}*/
	},
	initialize: function(form, options){
		this.setOptions(options);
		this.form = $(form);
		this.form.store('validator', this);

		if (this.options.evaluateOnSubmit) {
			this.form.addEvent('submit', this.onSubmit.bind(this));
		}
		if (this.options.evaluateFieldsOnBlur) {
			this.watchFields();
		}
	},
	toElement: function(){
		return this.form;
	},
	getFields: function(){
		return (this.fields = this.form.getElements(this.options.fieldSelectors));
	},
	watchFields: function(){
		this.getFields().each(function(el){
			el.addEvent('blur', this.validateField.pass([el, false], this));
			if (this.options.evaluateFieldsOnChange) {
				el.addEvent('change', this.validateField.pass([el, true], this));
			}
		}, this);
	},
	onSubmit: function(event){
		if (!this.validate(event) && event) {
			event.stop();
		} else {
			this.stop();
			this.reset();
		}
	},
	reset: function() {
		this.getFields().each(this.resetField, this);
		return this;
	}, 
	validate: function(event) {
		var result = this.getFields().map(function(field) { return this.validateField(field, true); }, this);
		result = result.every(function(val){
			return val;
		});
		this.fireEvent('onFormValidate', [result, this.form, event]);
		return result;
	},
	validateField: function(field, force){
		if (this.paused) {
			return true;
		}
		field = $(field);
		var result = field.hasClass('validation-failed');
		var failed, warned;
		if (this.options.serial && !force) {
			failed = this.form.getElement('.validation-failed');
			warned = this.form.getElement('.warning');
		}
		if (field && (!failed || force || field.hasClass('validation-failed') || (failed && !this.options.serial))){
			var validators = field.className.split(" ").some(function(cn){
				return this.getValidator(cn);
			}, this);
			failed = [];
			field.className.split(" ").each(function(className){
				if (!this.test(className,field)) {
					failed.include(className);
				}
			}, this);
			result = (failed.length == 0);
			if (validators && !field.hasClass('warnOnly')){
				if (result) {
					field.addClass('validation-passed').removeClass('validation-failed');
					this.fireEvent('onElementPass', field);
				} else {
					field.addClass('validation-failed').removeClass('validation-passed');
					this.fireEvent('onElementFail', [field, failed]);
				}
			}
			if (!warned) {
				var warnings = field.className.split(" ").some(function(cn){
					if (cn.test('^warn-') || field.hasClass('warnOnly')) {
						return this.getValidator(cn.replace(/^warn-/,""));
					} else {
						return null;
					}
				}, this);
				field.removeClass('warning');
				var warnResult = field.className.split(" ").map(function(cn){
					if (cn.test('^warn-') || field.hasClass('warnOnly')) {
						return this.test(cn.replace(/^warn-/,""), field, true);
					} else {
						return null;
					}
				}, this);
			}
		}
		return result;
	},
	getPropName: function(className){
		return '__advice'+className;
	},
	test: function(className, field, warn){
		field = $(field);
		if (field.hasClass('ignoreValidation')) {
			return true;
		}
		warn = $pick(warn, false);
		if (field.hasClass('warnOnly')) {
			warn = true;
		}
		var isValid = true;
		if (field) {
			var validator = this.getValidator(className);
			if (validator && this.isVisible(field)) {
				isValid = validator.test(field);
				if (!isValid && validator.getError(field)){
					if (warn) {
						field.addClass('warning');
					}
					var advice = this.makeAdvice(className, field, validator.getError(field), warn);
					this.insertAdvice(advice, field);
					this.showAdvice(className, field);
				} else {
					this.hideAdvice(className, field);
				}
				this.fireEvent('onElementValidate', [isValid, field, className]);
			}
		}
		if (warn) {
			return true;
		}
		return isValid;
	},
	showAdvice: function(className, field){
		var advice = this.getAdvice(className, field);
		if (advice && !field[this.getPropName(className)] && 
			(advice.getStyle('display') == "none" || 
			advice.getStyle('visiblity') == "hidden" ||
			advice.getStyle('opacity')==0)) {
			field[this.getPropName(className)] = true;
			advice.setStyle('display','block');
		}
	},
	hideAdvice: function(className, field){
		var advice = this.getAdvice(className, field);
		if (advice && field[this.getPropName(className)]) {
			field[this.getPropName(className)] = false;
			advice.setStyle('display','none');
		}
	},
	isVisible : function(field) {
		while(field != document.body) {
			if ($(field).getStyle('display') == "none") {
				return false;
			}
			field = field.getParent();
		}
		return true;
	},
	getAdvice: function(className, field) {
		return $('advice-' + className + '-' + this.getFieldId(field));
	},
	makeAdvice: function(className, field, error, warn){
		var errorMsg = (warn) ? this.options.warningPrefix : this.options.errorPrefix;
		errorMsg += this.options.useTitles ? field.title || error:error;
		var advice = this.getAdvice(className, field);
		if (!advice){
			var cssClass = (warn)?'warning-advice':'validation-advice';
			advice = new Element('div', {
				text: errorMsg,
				styles: { display: 'none' },
				id: 'advice-'+className+'-'+this.getFieldId(field)
			}).addClass(cssClass);
		} else {
			advice.set('text', errorMsg);
		}
		return advice;
	},
	insertAdvice: function(advice, field){
		switch (field.type.toLowerCase()) {
			case 'radio': case 'checkbox':
				var p = $(field.parentNode.parentNode);
				if (p) {
					p.adopt(advice);
					break;
				}
			default: advice.inject($(field), 'after');
	  }
	},
	getFieldId : function(field) {
		return field.id ? field.id : field.id = "input_"+field.name;
	},
	resetField: function(field) {
		field = $(field);
		if (field) {
			var cn = field.className.split(" ");
			cn.each(function(className) {
				if (className.test('^warn-')) {
					className = className.replace(/^warn-/,"");
				}
				var prop = this.getPropName(className);
				if (field[prop]) {
					this.hideAdvice(className, field);
				}
				field.removeClass('validation-failed');
				field.removeClass('warning');
				field.removeClass('validation-passed');
			}, this);
		}
		return this;
	},
	stop: function(){
		this.paused = true;
		return this;
	},
	start: function(){
		this.paused = false;
		return this;
	},
	ignoreField: function(field, warn){
		field = $(field);
		if (field){
			this.enforceField(field);
			if (warn) {
				field.addClass('warnOnly');
			} else {
				field.addClass('ignoreValidation');
			}
		}
		return this;
	},
	enforceField: function(field){
		field = $(field);
		if (field) {
			field.removeClass('warnOnly').removeClass('ignoreValidation');
		}
		return this;
	}
});
FormValidator.resources = {
	enUS: {
		required:'This field is required.',
		minLength:'Please enter at least {minLength} characters (you entered {length} characters).',
		maxLength:'Please enter no more than {maxLength} characters (you entered {length} characters).',
		integer:'Please enter an integer in this field. Numbers with decimals (e.g. 1.25) are not permitted.',
		numeric:'Please enter only numeric values in this field (i.e. "1" or "1.1" or "-1" or "-1.1").',
		digits:'Please use numbers and punctuation only in this field (for example, a phone number with dashes or dots is permitted).',
		alpha:'Please use letters only (a-z) with in this field. No spaces or other characters are allowed.',
		alphanum:'Please use only letters (a-z) or numbers (0-9) only in this field. No spaces or other characters are allowed.',
		dateSuchAs:'Please enter a valid date such as {date}',
		dateInFormatMDY:'Please enter a valid date such as MM/DD/YYYY (i.e. "12/31/1999")',
		email:'Please enter a valid email address. For example "fred@domain.com".',
		url:'Please enter a valid URL such as http://www.google.com.',
		currencyDollar:'Please enter a valid $ amount. For example $100.00 .',
		oneRequired:'Please enter something for at least one of these inputs.',
		confirmPassword:'Sorry, the passwords do not match. Please try again.',
		termsAndConditions:'You must agree to the terms and conditions.'
	}
};
FormValidator.language = "enUS";
FormValidator.getMsg = function(key, language){
	return FormValidator.resources[language||FormValidator.language][key];
};

FormValidator.adders = {
	validators:{},
	add : function(className, options) {
		this.validators[className] = new InputValidator(className, options);
		//if this is a class
		//extend these validators into it
		if (!this.initialize){
			this.implement({
				validators: this.validators
			});
		}
	},
	addAllThese : function(validators) {
		$A(validators).each(function(validator) {
			this.add(validator[0], validator[1]);
		}, this);
	},
	getValidator: function(className){
		return this.validators[className];
	}
};
$extend(FormValidator, FormValidator.adders);
FormValidator.implement(FormValidator.adders);

FormValidator.add('IsEmpty', {
	errorMsg: false,
	test: function(element) {
		if (element.type == "select-one"||element.type == "select") {
			return !(element.selectedIndex >= 0 && element.options[element.selectedIndex].value != "");
		} else {
			return ((element.get('value') == null) || (element.get('value').length == 0));
		}
	}
});

FormValidator.addAllThese([
	['required', {
		errorMsg: FormValidator.getMsg.pass('required'),
		test: function(element) { 
			return !FormValidator.getValidator('IsEmpty').test(element); 
		}
	}],
	['minLength', {
		errorMsg: function(element, props){
			if ($type(props.minLength)) {
				return FormValidator.getMsg('minLength').substitute({minLength:props.minLength,length:element.get('value').length });
			} else {
				return '';
			}
		}, 
		test: function(element, props) {
			if ($type(props.minLength)) {
				return (element.get('value').length >= $pick(props.minLength, 0));
			} else {
				return true;
			}
		}
	}],
	['maxLength', {
		errorMsg: function(element, props){
			//props is {maxLength:10}
			if ($type(props.maxLength)) {
				return FormValidator.getMsg('maxLength').substitute({maxLength:props.maxLength,length:element.get('value').length });
			} else {
				return '';
			}
		}, 
		test: function(element, props) {
			//if the value is <= than the maxLength value, element passes test
			return (element.get('value').length <= $pick(props.maxLength, 10000));
		}
	}],
	['validate-integer', {
		errorMsg: FormValidator.getMsg.pass('integer'),
		test: function(element) {
			return FormValidator.getValidator('IsEmpty').test(element) || /^-?[0-9]\d*$/.test(element.get('value'));
		}
	}],
	['validate-numeric', {
		errorMsg: FormValidator.getMsg.pass('numeric'), 
		test: function(element) {
			return FormValidator.getValidator('IsEmpty').test(element) || 
				/^-?(?:0$0(?=\d*\.)|[1-9]|0)\d*(\.\d+)?$/.test(element.get('value'));
		}
	}],
	['validate-digits', {
		errorMsg: FormValidator.getMsg.pass('digits'), 
		test: function(element) {
			return FormValidator.getValidator('IsEmpty').test(element) || /^[\d() .:\-\+#]+$/.test(element.get('value'));
		}
	}],
	['validate-alpha', {
		errorMsg: FormValidator.getMsg.pass('alpha'), 
		test: function (element) {
			return FormValidator.getValidator('IsEmpty').test(element) ||  /^[a-zA-Z]+$/.test(element.get('value'));
		}
	}],
	['validate-alphanum', {
		errorMsg: FormValidator.getMsg.pass('alphanum'), 
		test: function(element) {
			return FormValidator.getValidator('IsEmpty').test(element) || !/[^A-Za-z0-9_]/.test(element.get('value'));
		}
	}],
	['validate-date', {
		errorMsg: function(element, props) {
			if (Date.parse) {
				var format = props.dateFormat || "%x";
				return FormValidator.getMsg('dateSuchAs').substitute({date:new Date().format(format)});
			} else {
				return FormValidator.getMsg('dateInFormatMDY');
			}
		},
		test: function(element, props) {
			if (FormValidator.getValidator('IsEmpty').test(element)) {
				return true;
			}
			var d;
			if (Date.parse) {
				var format = props.dateFormat || "%x";
				d = Date.parse(element.get('value'));
				var formatted = d.format(format);
				if (formatted != "invalid date") {
					element.set('value', formatted);
				}
				return !isNaN(d);
			} else {
		    var regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
		    if (!regex.test(element.get('value'))) {
		    	return false;
		    }
		    d = new Date(element.get('value').replace(regex, '$1/$2/$3'));
		    return (parseInt(RegExp.$1, 10) == (1+d.getMonth())) && 
	        (parseInt(RegExp.$2, 10) == d.getDate()) && 
	        (parseInt(RegExp.$3, 10) == d.getFullYear() );
			}
		}
	}],
	['validate-email', {
		errorMsg: FormValidator.getMsg.pass('email'), 
		test: function (element) {
			return FormValidator.getValidator('IsEmpty').test(element) || /\w{1,}[@][\w\-]{1,}([.]([\w\-]{1,})){1,4}$/.test(element.get('value'));
		}
	}],
	['validate-url', {
		errorMsg: FormValidator.getMsg.pass('url'), 
		test: function (element) {
			return FormValidator.getValidator('IsEmpty').test(element) || /^(https?|ftp|rmtp|mms):\/\/(([A-Z0-9][A-Z0-9_\-]*)(\.[A-Z0-9][A-Z0-9_\-]*)+)(:(\d+))?\/?/i.test(element.get('value'));
		}
	}],
	['validate-currency-dollar', {
		errorMsg: FormValidator.getMsg.pass('currencyDollar'), 
		test: function(element) {
			// [$]1[##][,###]+[.##]
			// [$]1###+[.##]
			// [$]0.##
			// [$].##
			return FormValidator.getValidator('IsEmpty').test(element) ||  /^\$?\-?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}\d*(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$/.test(element.get('value'));
		}
	}],
	['validate-one-required', {
		errorMsg: FormValidator.getMsg.pass('oneRequired'), 
		test: function (element) {
			var p = element.parentNode.parentNode;
			return p.getElements('input').some(function(el) {
				if (['checkbox', 'radio'].contains(el.get('type'))) {
					return el.get('checked');
				}
				return el.get('value');
			});
		}
	}],
	['validate-passwordconfirm', {
		errorMsg: FormValidator.getMsg.pass('confirmPassword'),
		test: function(element) {
			return (element.get('value') == $('password').get('value'));
		}
	}],
	['validate-captcha', {
		test: function(element) {
		  if (FormValidator.getValidator('IsEmpty').test(element)) { return false; }

		  /* Send ajax request to captcha controller */
			var testResult = false;
			if (element.get('value').length) {
			  var jsonRequest = new Request.JSON({
					url: SITEURL+'/captcha/check',
					method: 'get',
					format: 'raw',
					async: false,
					onComplete: function(result){
					  testResult = result;
					}
				}).send('captcha='+escape(element.get('value')));
			}
			return testResult;
		}
	}],
	['validate-one-radio-check-required', {
		test: function (element) {
			var p = element.parentNode.parentNode;
			var options = p.getElements('input');
			return $A(options).some(function(el) {
				return el.get('checked');
			});
		}
	}],
	['validate-creditcard', {
		test: function(element){
			var creditCardNumber = new CreditCardNumber(element.get('value'));
			return creditCardNumber.isValid();
		}
	}],
	['validate-switch', {
		test: function(element){
			return (($('startmonth').get('value') && $('startyear').get('value')) || $('issuenum').get('value')); 
		}
	}],
	['validate-custom', {
		errorMsg: function(element, props){
			return props.custom_error.toString();
		},
		test: function(element, props){
			/* Send JSON request, in hope that it will return something vaguely resembling a boolean */
			var testResult = false;
			if (element.get('value').length) {
				var jsonRequest = new Request.JSON({
					url: props.custom_url.toString(),
					format: 'json',
					async: false,
					onSuccess: function(response){
						testResult = Boolean(response);
					}
				}).post({
					'task': 'validate',
					'criteria': element.get('value')
				});
			}
			return testResult;			
		}
	}],
	['validate-terms-required', {
		errorMsg: FormValidator.getMsg.pass('termsAndConditions'), 
		test: function(element){
			if (element.get('type') != 'checkbox'){
				// If not checkbox, pass.
				return true;
			}
			return element.get('checked');
		}
	}]
]);

/**
 * StandardForm
 * 
 * BluCommerce standard forms interface.
 *
 * @package		BluCommerce
 * @subpackage  FrontendClientside
 */
var StandardForm = new Class({

	Implements: Options,
	
	options: {
		stickyWin: {
			fade: true,
			fadeDuration: 300,
			draggable: true,
			width: 390
		},
		scrollContainer: null,
		fancyForm: true
	},
	
	container: null,
	content: null,
	form: null,
	
	vaidationFailed: false,
	
	uploadHolder: null,
	fancyUpload: null,
	filesToUpload: 0,
	fancyUploadErrors: false,
	
	history: null,
	waiter: null,
	scroll: null,
	
	popupWin: null,
	
	refocusId: null,
	
	url: null,

	initialize: function(container, options) {
		this.container = container;
		this.content = this.container.getElement('div.formholder');
		this.setOptions(options);
		
		/* Build waiter */
		this.waiter = new Waiter(this.container);

		/* Add scroller */
		this.scroll = new Fx.Scroll(window, {
			wait: false,
			duration: 200,
			offset: { 'x': 0, 'y': -10}
		});

		/* Add content effect */
		this.content.set('tween', {
			duration: 800,
			fps: 100
		});
		this.content.setStyle('overflow', 'hidden');

		/* Build content */
		this.buildContent();

	},

	buildContent: function() {

		/* Get form */
		this.form = this.content.getElement('form');
		
		/* Get action URL */
		var action = this.form.get('action').split('#', 1);
		this.url = action[0];
		
		/* Update fallback action to accomodate page scrolls on error */
		this.form.set('action', this.form.get('action').replace('#', '#scroll-'));
	
		/* Add form validation handlers */
		var formValidator = new FormValidator(this.form, {
			onFormValidate: this.formSubmitHandler.bindWithEvent(this),
			useTitles: true,
			errorPrefix: '',
			evaluateFieldsOnBlur: false,
			evaluateFieldsOnChange: false
		});
		
		/* Add form reload events */
		this.form.getElements('select.reloads').each(function(select) {
			select.addEvent('change', this.onReloadSelectChange.bindWithEvent(this, select));
		}, this);
		this.form.getElements('button.reloads, input.reloads').each(function(button) {
			button.addEvent('click', this.onReloadButtonClick.bindWithEvent(this, button));
		}, this);

		/* Attach fancy form styling to radio buttons and checkboxes */
		if (this.options.fancyForm && !this.form.hasClass('nofancy')) {
			var fancyForm = new FancyForm(this.form);
		}
		
		/* Add captcha reload */
		var captchaReload = this.form.getElement('div.captcha-reload');
		if (captchaReload) {
			captchaReload.setStyle('display', 'block');
			captchaReload.getElement('a').addEvent('click', this.reloadCaptchaClick.bindWithEvent(this));
		}
		
		/* Popups */
		var infoPopups = new InfoPopups(this.form.getElements('a.info-popup'));
		
		/* Add fancy upload */
		this.uploadHolder = this.form.getElementById('upload-holder');
		if (this.uploadHolder) {
		
			/* Build fancy upload request url */
			var fancyUrl = SITEINSECUREURL + this.url + (this.url.contains('?') ? '&' : '?');
			fancyUrl += 'task='+this.form.getElementById('uploadtask').get('value');
			fancyUrl += '&uploadkey='+this.form.getElementById('uploadkey').get('value');
			fancyUrl += '&queueid='+this.form.getElementById('queueid').get('value');
			fancyUrl += '&format=json';
		
			/* Init fancy upload */
			this.fancyUpload = new FancyUpload2(this.uploadHolder.getElementById('upload-status'), this.uploadHolder.getElementById('upload-list'), {
				'limitSize': 8388608,
				'url': fancyUrl,
				'fieldName': 'fileupload',
				'path': COREASSETURL+'/swf/Swiff.Uploader.swf',
				'onLoad': function() {
					this.uploadHolder.getElementById('upload-actions').setStyle('display', 'block');
					this.uploadHolder.getElementById('upload-fallback').destroy();
				}.bind(this),
				'onAllComplete': function() {
					if (!this.fancyUploadErrors) {
						this.submitForm();
					}
				}.bind(this),
				'onError': this.onUploadError.bind(this),
				'fileInvalid': function(file, errors) {
					if (errors.contains('size')) {
						alert('Sorry, '+file.get('name')+' is larger than the 8MB file size limit');
					}
				},
				'fileCreate': function(file) {
					this.fancyUpload.fileCreate(file);
					this.filesToUpload++;
				}.bind(this),
				'fileRemove': function() {
					this.filesToUpload--;
				}.bind(this)
			});
			
			/* Browse button action */
			this.uploadHolder.getElementById('upload-browse').addEvent('click', function() {
				this.fancyUpload.browse();
				return false;
			}.bind(this));
		}

	},
	
	onReloadSelectChange: function(event, select) {
		this.reloadForm(select.get('id'));
	},
	
	onReloadButtonClick: function(event, button) {
		this.reloadForm(button.get('id'), button.get('name')+'=1');
		event.stop();
	},
	
	reloadCaptchaClick: function(event) {
		this.form.getElement('img.captcha-img').set('src', SITEURL+'/captcha?format=raw&'+$time());
		event.stop();
	},

	formSubmitHandler: function(validationResult, form, event) {

		if (validationResult === true) {
			
			/* AJAX post unless we're doing standard uploads, or other -special- things. */
			var formType = form.get('enctype');
			if (formType != 'multipart/form-data' && !form.hasClass('fullsubmit')) {
			
				/* Handle fancy upload */
				if (this.fancyUpload && (this.filesToUpload > 0)) {
				
					/* Show upload status panel */
					this.uploadHolder.getElementById('upload-status').setStyle('display', 'block');
					
					/* Perform upload */
					this.fancyUploadErrors = false;
					this.fancyUpload.upload();
				
				/* Submit the form */
				} else {
					this.submitForm();			
				}
				
				event.stop();
			}
		
		/* Validation failed - don't submit */
		} else {
			this.vaidationFailed = true;
			event.stop();
		}
		
	},
	
	onUploadError: function(file, error, info) {
		this.fancyUploadErrors = true;
	},
	
	submitForm: function() {
		
		/* Show results in popup? */
		if (this.form.hasClass('popup')) {
			
			/* Build stickyWin */
			if (!this.popupWin) {
				this.popupWin = new StickyWinFx.Ajax(
					$merge(this.options.stickyWin, {
						className: 'stickyWin',
						allowMultipleByClass: true,
						/*relativeTo: this.form,*/
						requestOptions: {
							url: this.url,
							format: 'popup',
							evalScripts: true
						}
					}));
			}
			this.popupWin.Request.post(this.form);
		
		/* Standard submit */
		} else {
		
			/* Start waiter */
			this.waiter.start();
		
			/* Post form */
			var request = new Request.JSON({
				format: 'json',
				url: this.url,
				onComplete: this.onSubmitLoadComplete.bind(this)
			}).post(this.form);
		}
	},
	
	onSubmitLoadComplete: function(response) {
	
		/* Got new form content */
		if (response.form) {
			this.injectContent(response.form);

		} else if (response.messages) {
		  
			/* Get existing messages holder, else create */
			var messages = this.content.retrieve('messages', new Element('div'));

			/* Show messages */
			messages.set('html', response.messages);
			this.waiter.stop();
			if (response.complete) { this.form.destroy(); }
			messages.injectTop(this.content);
			
			/* Scroll container to ensure messages are in view */
			if (this.options.scrollContainer) {
				var scrollFx = new Fx.Scroll(this.options.scrollContainer);
				scrollFx.toElement(messages);
			}
		
		/* Got redirect */
		} else if (response.location) {
			window.location = response.location;
		}
		
	},
	
	reloadForm: function(refocusId, qs) {
		this.waiter.start(this.container);
		
		/* Build query string */
		var requestQs = this.form.toQueryString();
		requestQs += '&task='+this.form.getElementById('reloadtask').get('value');
		if ($defined(qs)) { requestQs += '&'+qs; }
	  
		/* Store ID to refocus */
		this.refocusId = refocusId;
		
		/* Post form */
		var htmlRequest = new Request.HTML({
			format: 'raw',
			url: this.url,
			evalScripts: false,
			onComplete: this.onReLoadComplete.bindWithEvent(this)
		}).send(requestQs);
		
	},

	onReLoadComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {
		this.injectContent(responseHTML, responseJavaScript);
	},
	
	injectContent: function(html, javascript) {
	
		/* Fix content size */
		var size = this.form.getSize();
		this.content.setStyle('height', size.y-1);

		/* Inject content */
		this.content.set('html', html);
		this.buildContent();
		
		/* Hide no script items */
		this.content.getElements('span.noscript').setStyle('display', 'none');
		
		/* Add popups */
		var infoPopups = new InfoPopups(this.content.getElements('a.info-popup'));
		
		/* Execute javascript */
		if ($defined(javascript)) {
			$exec(javascript);
		}

		/* Re-focus correct element */
		if (this.refocusId) {
			var el = this.form.getElementById(this.refocusId);
			if (el) { el.focus(); }
			this.refocusId = null;
		}

		/* Get size of new content */
		size = this.form.getSize();
		size.y--;

		/* Hide waiter and resize container to fit content once the dust has settled */
		var resize = function() {
			this.waiter.stop();
			this.content.get('tween').start('height', size.y).chain(function() {
				this.content.setStyle('height', 'auto');
			}.bind(this));
		}.delay(200, this);
	
	}

});

/**
 * Field length counter
 */
var LengthCounter = new Class({

	Implements: Options,
	
	options: {
		maxLength: false
	},
	
	input: null,
	counter: null,
	
	initialize: function(input, counter, options) {
		this.setOptions(options);
		this.input = $(input);
		this.counter = $(counter);
		
		this.input.addEvent('keyup', this.onUpdate.bindWithEvent(this));
		this.onUpdate();
	},
	
	onUpdate: function(event) {
		var value = this.input.get('value');
	
		/* Update counter */
		if (this.counter) {
			this.counter.set('text', value.length);
		}
		
		/* Enforce max length for textareas */
		if (this.input.get('tag') == 'textarea') {
			var maxLength = this.options.maxLength;
			if (maxLength && (value.length > maxLength)) {
				this.input.set('text', value.substr(0, maxLength));
			}
		}	
	}


}); 