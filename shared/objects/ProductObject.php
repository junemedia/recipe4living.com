<?

class ProductObject extends BluObject {

	/**
	 *	Build the object.
	 */
	public function __construct($id){

		/* Database and cache. */
		parent::__construct();

		/* Definitions. */
		$this->id = (int) $id;
		$this->_cacheObjectID = 'product_' . $this->id;

		/* Build. */
		$query = "select l.*,mpi.*,mpc.*,m.* from marketplace m
			left join marketplaceImages mpi on mpi.marketID = m.marketID  and mpi.mpiOrder=1
			left join marketplaceCategories mpc on mpc.mpcID = m.mCategory
			left join location l on m.mContactLocation = l.locationID
			where m.marketID= " . $this->id;
		$this->_buildObject($query);

	}

	/**
	 *	Images functionality.
	 */
	function addImage($ord,$img)
	{
		mysql_query('insert into marketplaceImages (`marketID`,`mpiOrder`,`mpiFile`) values ("'.$this->id.'","'.(int)$ord.'","'.Database::escape($img).'")');
	}
	function rmImage($ord)
	{
		mysql_query('delete from marketplaceImages where marketID="'.$this->id.'" and mpiOrder = "'.(int)$ord.'"');
	}
	function totalImages()
	{
		$sql = mysql_query('select count(*) from marketplaceImages where marketID="'.$this->id.'"');
		return mysql_result($sql,0);
	}

	/**
	 *	Increase the number of views.
	 */
	function increaseViews() {
		mysql_query('update marketplace set mViewed = mViewed + 1 where marketID = "'.$this->id.'"');
	}


	###							PRIVATE/PROTECTED CONVENIENCE FUNCTIONS							###

	/**
	 *	Required by BluObject.
	 */
	protected function _setVariables()
	{
		$this->title = $this->_getTitle();
		$this->shortTitle = $this->_getShortTitle();
		$this->description = $this->_getDescription();
		$this->links = $this->_getLinks();
		$this->discount = $this->_getDiscount();
		$this->contact = $this->_getContact();
		$this->author = $this->_getAuthor();
		$this->live = $this->_getLive();
		$this->paid = $this->_getPaid();
		$this->views = $this->_getViews();
		$this->website = $this->_getWebsite();
		$this->linkProfile = $this->_getLinkProfile();

		$this->images = $this->_getImages();
		$this->image = $this->images[0]->image;
		$this->category = $this->_getCategory();
		return $this;
	}

	/**
	 *	Data
	 */
	private function _getTitle()
	{
		return isset($this->_data->mTitle) && $this->_data->mTitle ? $this->_data->mTitle : null;
	}

	private function _getShortTitle()
	{
		return isset($this->_data->mShortTitle) && $this->_data->mShortTitle ? $this->_data->mShortTitle : Text::trim($this->_getTitle(), 100);
	}

	private function _getDescription()
	{
		return isset($this->_data->mDescription) && $this->_data->mDescription ? $this->_data->mDescription : null;
	}

	private function _getLinks($num = null)
	{
		$links = array();
		$max = $num ? min((int) $num, 5) : 5;
		for($i = 1; $i < $max + 1; $i++){
			$fieldName = 'mLink' . $i;
			if (isset($this->_data->$fieldName) && $this->_data->$fieldName){
				$rawlink = $this->_data->$fieldName;
				$links[] = (!strpos($rawlink, '://') ? 'http://' : '') . htmlentities($rawlink, ENT_QUOTES);
			}
		}
		return $links;
	}

	private function _getDiscount()
	{
		return isset($this->_data->mDiscounts) && $this->_data->mDiscounts ? $this->_data->mDiscounts : null;
	}

	private function _getContact()
	{
		$contact = new stdClass();

		if (isset($this->_data->mContactName) && $this->_data->mContactName) {
			$contact->name = $this->_data->mContactName;
		}
		if (isset($this->_data->mContactEmail) && $this->_data->mContactEmail) {
			$contact->email = $this->_data->mContactEmail;
		}
		if (isset($this->_data->mContactPhone) && $this->_data->mContactPhone) {
			$contact->phone = $this->_data->mContactPhone;
		}
		if (isset($this->_data->mLocationLongName) && $this->_data->mLocationLongName) {
			$contact->location = $this->_data->mLocationLongName;
		}

		$contact->email_show = isset($this->_data->mContactShowEmail) && isset($contact->email) ? (bool) $this->_data->mContactShowEmail : false;
		$contact->phone_show = isset($this->_data->mContactShowPhone) && isset($contact->phone) ? (bool) $this->_data->mContactShowPhone : false;
		$contact->location_show = isset($this->_data->mContactShowLocation) && isset($contact->location) ? (bool) $this->_data->mContactShowLocation : false;

		return $contact;
	}

	private function _getAuthor(){
		return isset($this->_data->mUserID) ? BluApplication::getModel('person')->getPerson(array('member' => $this->_data->mUserID)) : null;
	}
	private function _getLive(){
		return isset($this->_data->mLive) ? (bool) $this->_data->mLive : null;
	}
	private function _getPaid(){
		$payment = mysql_query('select * from marketplacePayments where marketID = "'.$this->id.'" and paid=1 and startDate <= NOW() and endDate >= NOW() limit 1');
		return (bool) mysql_num_rows($payment);
	}
	private function _getViews(){
		return isset($this->_data->mViewed) ? (int) $this->_data->mViewed :  null;
	}
	private function _getWebsite(){
		$link = $this->_getLinks(1);
		return Utility::is_loopable($link) ? array_shift($link) : null;
	}
	private function _getLinkProfile(){
		return isset($this->_data->mShowOnProfile) ? (bool) $this->_data->mShowOnProfile : false;
	}

	/**
	 *	Get ALL the images.
	 */
	private function _getImages(){

		/* Get*/
		$query = "SELECT mi.mpiID AS `id`
			FROM `marketplaceImages` AS `mi`
			WHERE mi.marketID = " . $this->id . "
			ORDER BY mi.mpiOrder ASC";
		$records = $this->_fetch($query, $this->_cacheObjectID . '_images', null, null, false);

		/* Format */
		$images = array();

		if (DEBUG){
			// Replicate a MarketphotoObject object.
			$image = new stdClass();
			$image->image = 'dummy-market.jpg';
			$images[] = $image;
		} else {
			// Default market image gets added to the array here.
		}

		foreach((array)$records as $record){
			try {
				$images[] = BluApplication::getObject('marketphoto', (int) $record['id']);
			} catch (NoDataException $exception) {}
		}

		if (count($images) > 1){ array_shift($images); }

		/* Return */
		return $images;

	}

	/**
	 *	Get the category.
	 */
	private function _getCategory()
	{
		/* Triviality */
		if (!isset($this->_data->mCategory) || !$this->_data->mCategory){ return null; }

		/* Get */
		$query = "SELECT *
			FROM `marketplaceCategories` AS `mc`
			WHERE mc.mpcID = " . (int) $this->_data->mCategory;
		$obj = $this->_fetch($query, $this->_cacheObjectID . '_category');

		/* Format */
		if (!isset($obj->categoryName)) { return null; }
		$category = new stdClass();
		$category->name = $obj->categoryName;
		$category->shortName = isset($obj->categoryShortName) ? $obj->categoryShortName : Text::trim($category->name, 100);

		/* Return */
		return $category;
	}

}

?>