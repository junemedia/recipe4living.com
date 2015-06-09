<?php

/**
 *	Future replacement for current Pagination class.
 */
class SimplePagination {
	
	/**
	 *	Whether to render a single button if there is only one page.
	 */
	protected $_showSingleButton = false;
	
	/**
	 *	Accessor method
	 */
	public function get($key, array $args = array())
	{
		switch($key){
			case 'start':
				/* Showing [x] - y of z. */
				return $this->get('total') ? $this->get('offset') + 1 : 0;
				break;
				
			case 'end':
				/* Showing x - [y] of z. */
				return min($this->get('start') + $this->get('limit') - 1, $this->get('total'));
				break;
				
			case 'limit':
				return isset($this->_limit)?$this->_limit:0;
				break;
				
			case 'total':
				/* Showing x - y of [z]. */
				return $this->_total;
				break;
				
			case 'current':
				/* Current page */
				return $this->_current;
				break;
				
			case 'pages':
				/* Total number of pages */
				return ($limit = $this->get('limit')) ? ceil($this->get('total') / $limit) : 1;
				break;
				
			case 'offset':
				/* Technical: offset */
				return ($this->get('current') - 1) * $this->get('limit');
				break;
				
			case 'buttons':
				/* HTML for the buttons */
				return $this->get('pages') ? Pagination::buttons($this->get('current'), $this->get('pages'), $this->get('url'), $this->get('locationHash'), $this->_showSingleButton, $args) : '';
				break;
				
			case 'hasPrevious':
				/* Convenience */
				return $this->get('current') > 1;
				break;
				
			case 'hasNext':
				/* Convenience */
				return $this->get('total') && $this->get('current') < $this->get('total');
				break;
				
			case 'url':
				/* Base url */
				return isset($this->_url) ? $this->_url : null;
				break;
				
			case 'locationHash':
				/* Location hash */
				return isset($this->_locationHash) ? $this->_locationHash : null;
				break;
			
		}
		
	}
	
	/**
	 *	Mutator method.
	 */
	public function set($key, $value = null){
		
		if (Utility::iterable($key)){
			foreach($key as $k => $v){
				$this->set($k, $v);
			}
			return $this;
		} else if (is_null($value)){
			return false;
		}
		
		switch($key){
			case 'total':
				/* Thing count */
				$this->_total = (int) $value;
				break;
				
			case 'current':
				/* Current page */
				if (isset($this->_total)){
					/* Error checking - defaults to page 1. */
					if ($value < 1){
						$value = 1;							// Non-positive page number
					} else if ($this->get('pages') < 1) {
						$value = 0;							// No results
					} else if($value > $this->get('pages')) {
						$value = $this->get('pages');		// Not that many pages.
					}
				}
				$this->_current = (int) $value;
				break;
				
			case 'limit':
				/* Things per page */
				$this->_limit = (int) $value;
				break;
				
			case 'url':
				/* Buttons: base URL */
				$this->_url = $value;
				break;
				
			case 'locationHash':
				/* For use with non-JS users' pagination buttons */
				$this->_locationHash = $value;
				break;
			
			case 'showSingleButton':
				$this->_showSingleButton = $value;
				break;
		}
		
		return $this;
	}
	
	/**
	 *	Chopper method.
	 */
	public function chop(&$thing){
		$thing = array_slice($thing, $this->get('offset'), $this->get('limit'), true);
		return $thing;
	}

}
?>
