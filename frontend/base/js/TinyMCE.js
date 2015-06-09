/**
 *	TinyMCE wrapper class.
 */
var TinyMCE = new Class({
	
	/**
	 *	Options.
	 */
	Implements: Options,
	
	/**
	 *	Port TinyMCE functionality.
	 */
	tinyMCE: null,
	
	/**
	 *	Default settings.
	 */
	options: {
		// General options
		mode : "textareas",
		plugins : [
			'safari',
			'pagebreak',
			'style',
			'layer',
			'table',
			'save',
			'advhr',
			'advimage',
			'advlink',
			'emotions',
			'iespell',
			'inlinepopups',
			'insertdatetime',
			'preview',
			'media',
			'searchreplace',
			'print',
			'contextmenu',
			'paste',
			'directionality',
			'fullscreen',
			'noneditable',
			'visualchars',
			'nonbreaking',
			'xhtmlxtras',
			'template',
			'{spellchecker}'
		],
		
		// Example content CSS (should be your site CSS)
		//content_css : "css/content.css",

		// Drop lists for link/image/media/template dialogs
		//template_external_list_url : "lists/template_list.js",
		//external_link_list_url : "lists/link_list.js",
		//external_image_list_url : "lists/image_list.js",
		//media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	},
	
	/**
	 *	Extra available plugins, not enabled by default.
	 */
	availablePlugins: {
		'spellchecker' : false
	},
	
	/**
	 *	"Minimal" theme settings
	 */
	_minimal: {
		theme : "simple"
	},
	
	/**
	 *	"Standard" theme settings.
	 */
	_standard: {
		// Theme options
		theme : "advanced",
		theme_advanced_buttons1 : [
			'bold',
			'italic',
			'underline',
			'separator',
			'strikethrough',
			'justifyleft',
			'justifycenter',
			'justifyright',
			'justifyfull',
			'bullist',
			'numlist',
			'undo',
			'redo',
			'link',
			'unlink',
			'image',
			'{spellchecker}'
		],
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        extended_valid_elements : [
			'a[name|href|target|title|onclick]',
			'img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]',
			'hr[class|width|size|noshade]',
			'font[face|size|color|style]',
			'span[class|align|style]',
			'img[*]'
		],
        theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	},
	
	/**
	 *	"Full" theme settings.
	 */
	_full: {
		// Theme options
		theme : "advanced",
		theme_advanced_buttons1 : [
			'save',
			'newdocument',
			'|',
			'bold',
			'italic',
			'underline',
			'strikethrough',
			'|',
			'justifyleft',
			'justifycenter',
			'justifyright',
			'justifyfull',
			'styleselect',
			'formatselect',
			'fontselect',
			'fontsizeselect'
		],
		theme_advanced_buttons2 : [
			'cut',
			'copy',
			'paste',
			'pastetext',
			'pasteword',
			'|',
			'search',
			'replace',
			'|',
			'{spellchecker}',
			'|',
			'bullist',
			'numlist',
			'|',
			'outdent',
			'indent',
			'blockquote',
			'|',
			'undo',
			'redo',
			'|',
			'link',
			'unlink',
			'anchor',
			'image',
			'cleanup',
			'help',
			'code',
			'|',
			'insertdate',
			'inserttime',
			'preview',
			'|',
			'forecolor',
			'backcolor'
		],
		theme_advanced_buttons3 : [
			'tablecontrols',
			'|',
			'hr',
			'removeformat',
			'visualaid',
			'|',
			'sub',
			'sup',
			'|',
			'charmap',
			'emotions',
			'iespell',
			'media',
			'advhr',
			'|',
			'print',
			'|',
			'ltr',
			'rtl',
			'|',
			'fullscreen'
		],
		theme_advanced_buttons4 : [
			'insertlayer',
			'moveforward',
			'movebackward',
			'absolute',
			'|',
			'styleprops',
			'|',
			'cite',
			'abbr',
			'acronym',
			'del',
			'ins',
			'attribs',
			'|',
			'visualchars',
			'nonbreaking',
			'template',
			'pagebreak'
		],
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	},
	
	/**
	 *	Constructor.
	 */
	initialize: function(form, options){
		this.availablePlugins = $H(this.availablePlugins);
		
		/* Configure plugins */
		if ($H(options).has('plugins')){
			$A(options.plugins).each(function(plugin){
				if (this.availablePlugins.has(plugin)){
					this.availablePlugins.set(plugin, true);
				}
			}, this);
		}
		
		/* Apply theme */
		if (!$H(options).has('theme')){
			options.theme = 'standard';
		}
		switch(options.theme){
			case 'advanced':
			case 'full':
				this.setOptions(this._full);
				break;
				
			case 'simple':
			case 'minimal':
				this.setOptions(this._minimal);
				break;
				
			case 'standard':
			default:
				this.setOptions(this._standard);
				break;
		}
		
		/* Tidy up settings */
		[
			'plugins',
			'theme_advanced_buttons1',
			'theme_advanced_buttons2',
			'theme_advanced_buttons3',
			'theme_advanced_buttons4',
			'extended_valid_elements'
		].each(function(setting){
			if ($H(this.options).has(setting)){
				
				/* Get setting */
				var localSetting = $H(this.options).get(setting);
				
				/* If not array, make into an array */
				if ($type(localSetting) != 'array'){
					localSetting = localSetting.split(', ');
				}
				localSetting = $A(localSetting);	/* Extend native. */
				
				/* Get enabled plugins */
				var enabledPlugins = this.availablePlugins.filter(function(enabled, plugin){
					return enabled;
				}).getKeys();
				
				/* Replace placeholders */
				enabledPlugins.each(function(plugin){
					var placeholder = '{'+plugin+'}';
					if (localSetting.contains(placeholder)){
						var index = localSetting.indexOf(placeholder);
						localSetting[index] = plugin;
					}
				}, localSetting);
			
				/* Clear remaining placeholders, and then replace whole array with its imploded string */
				localSetting = localSetting.filter(function(element){
					return !element.test('^{(.*)}$');
				}).join(', ');
				
				/* Replace option */
				var options = new Hash();
				options.set(setting, localSetting);
				this.setOptions(options.getClean());
				
			}
		}, this);
		
		/* Remove conflict with standardform */
		$(form).addClass('fullsubmit');
		
		/* Initialise TinyMCE instance. */
		this.tinyMCE = window.tinyMCE;
		this.tinyMCE.init(this.options);
		
	},
	
	/**
	 *	Private method, test if using a particular plugin.
	 */
	_usingPlugin: function(plugin){
		
		/* Get list of the plugins in use. */
		var pluginsInUse;
		if ($type(this.options.plugins) == 'array'){
			pluginsInUse = this.options.plugins;
		} else if ($type(this.options.plugins) == 'string'){
			pluginsInUse = this.options.plugins.split(', ');
		} else {
			pluginsInUse = [];
		}
		
		/* Test */
		return $A(pluginsInUse).has(plugin);
		
	}

});

/**
 *	TinyMCE for a single element
 */
var TinyMCEElement = new Class({
	
	Implements: Options,
	
	element: null,
	options: {
		plugins: [],
		layout: 'standard'
	},
	
	initialize: function(element, options) {
		this.element = $(element);
		this.setOptions(options);
		
		// Build config
		var config = {
			mode: 'exact',
			document_base_url: this.options.document_base_url,
			relative_urls : false,
			remove_script_host : false,
			elements: this.element.get('id'),
			plugins: this.options.plugins.join(',')
		};
		
		// Apply layout changes
		switch (this.options.layout) {
			case 'advanced':
			case 'full':
				config = $merge(config, TinyMCELayout.FULL);
				break;
				
			case 'simple':
			case 'minimal':
				config = $merge(config, TinyMCELayout.MINIMAL);
				break;
				
			case 'standard':
			default:
				config = $merge(config, TinyMCELayout.STANDARD);
				break;
		}
		
		// Fix config
		config = this._fixConfig(config);
		
		// Instantiate
		if (tinyMCE) {
			tinyMCE.init(config);
			
		// TinyMCE library not loaded!
		}
	},
	
	/**
	 *	Tidy up config settings
	 */
	_fixConfig: function(config) {
		
		if (config.theme_advanced_buttons1) {
			config.theme_advanced_buttons1 = config.theme_advanced_buttons1.join(',');
		}
		if (config.theme_advanced_buttons2) {
			config.theme_advanced_buttons2 = config.theme_advanced_buttons2.join(',');
		}
		if (config.theme_advanced_buttons3) {
			config.theme_advanced_buttons3 = config.theme_advanced_buttons3.join(',');
		}
		if (config.theme_advanced_buttons4) {
			config.theme_advanced_buttons4 = config.theme_advanced_buttons4.join(',');
		}
		if (config.extended_valid_elements) {
			config.extended_valid_elements = config.extended_valid_elements.join(',');
		}
		
		return config;
	}
});

/**
 *	TinyMCE layout options
 */
var TinyMCELayout = {
	
	/**
	 *	"Minimal" theme settings
	 */
	MINIMAL: {
		theme : "simple"
	},
	
	/**
	 *	"Standard" theme settings.
	 */
	STANDARD: {
		// Theme options
		theme : "advanced",
		theme_advanced_buttons1 : [
			'bold',
			'italic',
			'underline',
			'separator',
			'strikethrough',
			'justifyleft',
			'justifycenter',
			'justifyright',
			'justifyfull',
			'bullist',
			'numlist',
			'undo',
			'redo',
			'link',
			'unlink',
			//'image',
			'code',
			'spellchecker'
		],
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        extended_valid_elements : [
			'a[name|href|target|title|onclick]',
			'img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]',
			'hr[class|width|size|noshade]',
			'font[face|size|color|style]',
			'span[class|align|style]',
			'img[*]'
		],
        theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	},
	
	/**
	 *	"Full" theme settings.
	 */
	FULL: {
		// Theme options
		theme : "advanced",
		theme_advanced_buttons1 : [
			'save',
			'newdocument',
			'|',
			'bold',
			'italic',
			'underline',
			'strikethrough',
			'|',
			'justifyleft',
			'justifycenter',
			'justifyright',
			'justifyfull',
			'styleselect',
			'formatselect',
			'fontselect',
			'fontsizeselect'
		],
		theme_advanced_buttons2 : [
			'cut',
			'copy',
			'paste',
			'pastetext',
			'pasteword',
			'|',
			'search',
			'replace',
			'|',
			'{spellchecker}',
			'|',
			'bullist',
			'numlist',
			'|',
			'outdent',
			'indent',
			'blockquote',
			'|',
			'undo',
			'redo',
			'|',
			'link',
			'unlink',
			'anchor',
			'image',
			'cleanup',
			'help',
			'code',
			'|',
			'insertdate',
			'inserttime',
			'preview',
			'|',
			'forecolor',
			'backcolor'
		],
		theme_advanced_buttons3 : [
			'tablecontrols',
			'|',
			'hr',
			'removeformat',
			'visualaid',
			'|',
			'sub',
			'sup',
			'|',
			'charmap',
			'emotions',
			'iespell',
			'media',
			'advhr',
			'|',
			'print',
			'|',
			'ltr',
			'rtl',
			'|',
			'fullscreen'
		],
		theme_advanced_buttons4 : [
			'insertlayer',
			'moveforward',
			'movebackward',
			'absolute',
			'|',
			'styleprops',
			'|',
			'cite',
			'abbr',
			'acronym',
			'del',
			'ins',
			'attribs',
			'|',
			'visualchars',
			'nonbreaking',
			'template',
			'pagebreak'
		],
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	}
};
