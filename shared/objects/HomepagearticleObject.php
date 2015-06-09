<?

/**
 *	Articles of type articleType = 'article'
 */
class HomepagearticleObject extends BluObject{

	/**
	 *	Initialise.
	 */
	public function __construct($id){
	
		parent::__construct();
		$this->id = (int)$id;
		$this->_cacheObjectID = 'homepagearticle_'.$this->id;
		
		/* Build object */
		$query = "SELECT * 
			FROM `homepageswitcher` 
			WHERE `hpid` = ".$this->id;
		$this->_buildObject($query);
		
	}
	
	/**
	 *	Go wild.
	 */
	protected function _setVariables(){
		$this->id = $this->_data->hpid;
		$this->title = $this->_data->hpTitle1;
		$this->body = $this->_data->hpContent;
		$this->image = $this->_data->hpImage;
		$this->order = $this->_data->hpOrder;
		$this->url = $this->_data->hpLink;
		$this->live = $this->_data->hpLive;
		return $this;
	}
	
}

?>