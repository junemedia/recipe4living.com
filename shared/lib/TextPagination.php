<?php

/**
 *	Paginate a piece of text
 */
class TextPagination {

	/**
	 *	Contains the full text.
	 */
	protected $_content;
	
	/**
	 *	The current page.
	 */
	protected $_currentpage;
	
	/**
	 *	The wanted number of words to display.
	 */
	protected $_limit;
	
	/**
	 *	Total number of pages.
	 */
	protected $_total;
	
	/**
	 *	Location hash.
	 */
	protected $_locationHash;
	
	/**
	 *	Pagination base Url.
	 */
	protected $_url;
	
	/**
	 *	Whether to render a single button if there is only one page.
	 */
	protected $_showSingleButton = false;
	
	/**
	 *	Accessor method.
	 *
	 *	@access public
	 *	@param string Key
	 *	@param array Arguments
	 *	@return mixed
	 */
	public function get($key, array $args = array())
	{
		switch($key){
			case 'limit':
				/* Number of words per page */
				return $this->_limit;
				break;
				
			case 'current':
				/* Current page */
				return $this->_currentpage;
				break;
				
			case 'pages':
				/* Number of pages */
				$this->_parseContent();
				return $this->_total;
				break;
				
			case 'content':
				/* Content to display for that page. */
				return $this->_parseContent();
				break;
				
			case 'buttons':
				/* Generate buttons */
				$pages = $this->get('pages');
				$current = $this->get('current');
				$url = $this->get('url');
				$locationHash = $this->get('locationHash');
				$showSingleButton = $this->get('showSingleButton');
				return $pages ? Pagination::buttons($current, $pages, $url, $locationHash, $showSingleButton, $args) : '';
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
			case 'content':
				/* Full content */
				$this->_content = $value;
				break;
				
			case 'current':
				/* Current page number */
				$this->_currentpage = (int) $value;
				break;
				
			case 'limit':
				/* Words per page. */
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
	 *	Generates paginated (tag-stripped) text from full text and offset / limit.
	 *	Rounds to the nearest paragraph.
	 *
	 *	@return (string) the text from that page.
	 */
	protected function _parseContent(){
		
		/* Input data */
		$offset = $this->_currentpage - 1;
		$text = $this->_content;
		$numwords = $this->_limit;
		
		/* Tidy it up */
		$outstr = '';
		$words = 0;
		
		/* Logic from old WIM - cheers Duncan! */
		$match = Array("/(<br>|<br( [^>]*)?\>)/i","/(<p( [^>]*)?\>(.*?)<\/p>)/i","/([\r\n][\t ]*){2,}/","/\n(<\/.*?\>)\n/","/\n(<[^>]*?\>)\n/");
		$replace = Array("\n","\n<div \\2>\\3</div>","\n","\\1\n","\n\\1");
		$text = preg_replace($match,$replace,$text);
		
		
		$x = explode("\n",$text);
		
		$n = count($x);
		$i = 0;
		$a = 0;
		
		$total = $n;
		
		
		
		while ($i < $n) {
			if ($offset == $a) {
				$outstr .= $x[$i];
			}
			$words += substr_count($x[$i],' ');
			
			if ($words > $numwords) {
				$words = 0;
				if (($i + 1) != $n)
					$a++;
			}
			++$i;
			
		}
		
		/* Output */
		// Store total number of pages.
		$this->_total = $a + 1;
		
		// This is the content for this page:-
		return $outstr;
		
	}

}

?>