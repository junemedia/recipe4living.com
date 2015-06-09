/**
 *	TinyMCE wrapper class.
 */
var TinyMCE = new Class({
	
	/**
	 *	Options.
	 */
	Implements: Options,
	
	/**
	 *	Options.
	 */
	options: {},
	
	/**
	 *	Port TinyMCE functionality.
	 */
	instance: null,
	
	/**
	 *	Default settings.
	 */
	_base: {
		// General options
		mode : "textareas",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

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
		theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,image",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],img[*]",
        theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	},
	
	/**
	 *	"Full" theme settings.
	 */
	_full: {
		// Theme options
		theme : "advanced",
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	},
	
	/**
	 *	TinyMCE wrapper constructor.
	 */
	initialize: function(container, options){
		
		/* Configure settings. */
		var _options = $merge(this._base, options);
		if ($H(_options).has('theme')){
			switch(_options.theme){
				case 'advanced':
				case 'full':
					$extend(_options, this._full);
					break;
					
				case 'simple':
				case 'minimal':
					$extend(_options, this._minimal);
					break;
					
				case 'standard':
				default:
					$extend(_options, this._standard);
					break;
			}
		} else {
			$extend(_options, this._standard);
		}
		this.setOptions(_options);
		
		/* Remove conflict with standardform */
		$(container).addClass('fullsubmit');
		
		/* Initialise TinyMCE instance. */
		this.instance = tinyMCE;
		this.instance.init(this.options);
		
	}

});
