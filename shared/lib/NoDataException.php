<?php

/**
 *	Exception for when we can't find any data from the database.
 */
class NoDataException extends Exception{

	/**
	 *	The thing that supposedly threw the exception.
	 */
	private $_thing;
	
	/**
	 *	Set the exception message.
	 */
	public function __construct($thing){
		
		/* Save the thing */
		$this->_thing = $thing;
		
		/* Set the exception message */
		$message = 'no data for ' . get_class($this->_thing);
		
		/* Throw the exception */
		parent::__construct($message);
		
	}
	
}

?>