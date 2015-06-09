<?php

/**
 * Object base class
 *
 * @package BluApplication
 */
abstract class BluObject extends BluModel
{
	/**
	 * Reference to what cache object ID should be. Ensures caching on objects.
	 *
	 * @var String
	 */
	protected $_cacheObjectID;

	/**
	 *	This holds all data taken directly from the database (in a stdClass object)
	 */
	protected $_data;

	/**
	 *	Common functionality for BluObject constructors.
	 *
	 *	Handles initial cache/database retrieval, and ultimately defines the scope of all object variables.
	 */
	protected function _buildObject($query){

		/* Fetch data, (plus throw any exceptions) */
		$record = $this->_fetch($query, $this->_cacheObjectID, 0, 1, false);
		if (!Utility::is_loopable($record)){ throw new NoDataException($this); }

		/* Data goes directly into $this->_data. Remember that $this->_data has protected scope... */
		$this->_data = Utility::toObject($record);

		/* ...that's why other object variables go directly into $this, and are made publicly available. */
		$this->_setVariables();

	}

	/**
	 *	Defaults to objectifying parent method.
	 *
	 *	Overrides BluModel.
	 *
	 *	@return (mixed) stdClass object, or an array.
	 */
	protected function _fetch($query, $cacheKey = null, $offset = null, $limit = null, $objectify = true){

		/* Parent method */
		$data = parent::_fetch($query, $cacheKey, $offset, $limit);

		/* Objectify the *first* row of results? */
		return $objectify ? Utility::toObject($limit == 1 ? $data : array_shift($data)) : $data;

	}

	/**
	 *	Set publicly available data.
	 *	Try to set only those which are frequently used. Others should be obtained by standard accessor methods.
	 */
	protected function _setVariables(){}
	
	/**
	 *	Flush the cached version of this object.
	 */
	public function flushCached(){
		$flushed = $this->_cache->delete($this->_cacheObjectID);
		return $flushed;
	}

}
?>
