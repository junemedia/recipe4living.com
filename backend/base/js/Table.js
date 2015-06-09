/**
 *	Enable control-clicking.
 */
Element.Events.ctrlclick = {
	base: 'click',
	condition: function(event){
		return (event.control == true);
	}
};

/**
 *	Table data 
 */
var Table = new Class({

	Implements: Options,
	
	/**
	 *	Container table.
	 */
	table: null,
	
	/**
	 *	Rows in the table.
	 */
	rows: null,
	
	/**
	 *	Element that serves as toggler for checking all boxes.
	 */
	masterCheck: null,

	/**
	 *	Enable table functionality.
	 */
	initialize: function(selector, options){
		
		/* Set options */
		this.setOptions(options);
		
		/* Store reference to the table */
		this.table = $(selector);
		this.rows = this.table.getElements('tr').filter(this._filter);		
		
		/* Enable row hovering */
		this.rows.each(function(tr){
			tr.addEvents({
				'mouseenter': function(event, tr){
					event.stop();
					tr.addClass('hovered');
				}.bindWithEvent(this, tr),
				'mouseleave': function(event, tr){
					event.stop();
					tr.removeClass('hovered');
				}.bindWithEvent(this, tr),
				'click': this._check.bindWithEvent(this, tr)
			});
		}, this);
		
		/* Make the first 'checkall'-classed element do just that */
		this.masterCheck = this.table.getElement('input.checkall[type=checkbox]');
		if (this.masterCheck){
			this.masterCheck.addEvent('click', this._checkAll.bindWithEvent(this));
		}
		
	},
	
	/**
	 *	Filter <tr> tags by data rows.
	 */
	_filter: function(tr){
		return !tr.hasClass('metadata');
	},
	
	/**
	 *	Selects one row.
	 */
	_check: function(event, tr){
	
		// Clicked on anchor or input itself?
		if (event.target.get('tag') != 'a'){
			
			// Get checkbox for the row
			var input = tr.getElement('input[type=checkbox]');
			if (!input){
				return false;
			}
			
			// Prevent checking twice
			if (event.target != input){	
				input.set('checked', !input.get('checked'));
			}
			
			// Unset master checkbox?
			if (this.masterCheck && !this.rows.every(this._checked)){
				this.masterCheck.set('checked', false);
			}
			
		}
		
	},
	
	/**
	 *	Selects all rows.
	 */
	_checkAll: function(event){
		
		// Swap to the same as the master checkbox value.
		this.rows.each(function(tr){
			var input = tr.getElement('input[type=checkbox]');
			return input ? input.set('checked', this.masterCheck.get('checked')) : false;
		}, this);
		
	},
	
	/**
	 *	Check if a row is checked.
	 */
	_checked: function(tr){
		var input = tr.getElement('input[type=checkbox]');
		return input ? input.get('checked') == true : false;
	}

});