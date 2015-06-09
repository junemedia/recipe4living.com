<?php

/**
 *	Array helpers.
 */
class Arrays
{
	/**
	 *	Checks whether input is an array and is not empty.
	 *
	 *	@param mixed Input
	 *	@return bool.
	 */
	public static function iterable($var) {
		return is_array($var) && !empty($var);
	}
	
	/**
	 *	Fetch an element from an (multi-dimensional) array, if it exists, or return a default.
	 *
	 *	Usage: multi_array_get($anArray, 'alice', 'bob', 'carlos', $dave) will basically return:
	 *		isset($anArray['alice']['bob']['carlos']) ? $anArray['alice']['bob']['carlos'] : $dave;
	 *
	 *	@param array Array to search through.
	 *	@param string Key to search for.
	 *	@param string ...optionally more keys (for multidimensional arrays)...
	 *	@param mixed Default value.
	 *	@return mixed Element of array, or default value.
	 */
	public static function get() {
		
		/* This method's arguments. */
		$args = func_get_args();
		
		/* Get default value: last argument */
		$default = self::iterable($args) ? array_pop($args) : null;
		
		/* Get array: first argument */
		if (!self::iterable($args)){
			return $default;
		}
		$array = array_shift($args);
		
		/* Next key to check */
		if (!self::iterable($args)){
			
			// We are done.
			return $array;
			
		}
		
		/* Grab (and remove) key from list of arguments */
		$nextKey = array_shift($args);
		
		/* Key exists? */
		if (self::iterable($array) && array_key_exists($nextKey, $array)){
			
			// Go one level deeper
			$array = $array[$nextKey];
			
			// Rebuild arguments
			array_unshift($args, $array);
			array_push($args, $default);
			
			// Recurse
			return call_user_func_array(array('self', __FUNCTION__), $args);
			
		}
		
		/* Fail */
		return $default;
		
	}

	/**
	 *	Filter a 2D array by a column.
	 *
	 *	@static
	 *	@param array To filter (2D).
	 *	@param string Array key to filter by
	 *	@return array Filtered array (1D).
	 */
	public static function column(array $array, $columnIndex)
	{
		// Initial check
		if (empty($array)) {
			return $array;
		}
		
		// Pull out representative
		foreach ($array as $key => &$value) {
			if (isset($value[$columnIndex])) {
				$value = $value[$columnIndex];
				
			// Fail
			} else {
				return false;
			}
		}
		unset($value);
		
		// Return
		return $array;
	}
	
	/**
	 *	Get the last element of an array
	 *
	 *	@param array Array.
	 *	@param mixed Fallback.
	 *	@return mixed Element.
	 */
	public static function last($array, $default = null){
		return self::iterable($array) ? array_pop($array) : $default;
	}
	
	/**
	 *	Reassign the keys of a 2-or-more dimensional array using one of the keys of the element.
	 *
	 *	N.B. don't select a column that has duplicate values, obviously.
	 *
	 *	@param array Array.
	 *	@param string Key.
	 *	@return array Array with keys reassigned.
	 */
	public static function reassign_keys($array, $column)
	{
		// Sanity
		if (empty($array) || !is_array($array)) {
			return false;
		}
		
		// Get list of new keys from elements
		$newKeys = self::column($array, $column);
		if (!is_array($newKeys)) {
			return false;
		}
		
		// Reorder
		return array_combine($newKeys, $array);
	}
	
	/**
	 *	Group a 2D array by one of its element's elements.
	 *
	 *	@static
	 *	@access public
	 *	@param array Array to group.
	 *	@param string Key to group by.
	 *	@param string Key to key off subgroups (should be unique)
	 *	@return array Grouped array.
	 */
	public static function group($array, $column, $subKey = null)
	{
		// Sanity
		if (!self::iterable($array)) {
			return false;
		}
		
		// Group column
		$keys = self::column($array, $column);
		if (!self::iterable($keys)) {
			return false;
		}
		
		// Prepare structure
		$output = array();
		foreach ($keys as $key) {
			$output[$key] = array();
		}
		
		// Append 1D arrays
		foreach ($array as $row) {
			if ($subKey) {
				$output[$row[$column]][$row[$subKey]] = $row;
			} else {
				$output[$row[$column]][] = $row;
			}
		}
		
		// Return
		return $output;
	}
	
	/**
	 *	Get all combinations of a 2d array.
	 *
	 *	@param array 2D array.
	 *	@param array Values to prepended to each combination, usually an empty array.
	 *	@return array List of combinations.
	 */
	public static function combinations(array $masterArray, array $prepend = array(), array &$dataStore = array()){
		
		/* End of recursion */
		if (empty($masterArray)){
			$dataStore[] = $prepend;
			return $dataStore;
		}
		
		/* Shift off first row */
		$row = array_shift($masterArray);
		foreach($row as $element){
			$newCombination = array_merge($prepend, array($element));
			self::combinations($masterArray, $newCombination, $dataStore);	// Add to datastore.
		}
		
		/* Return */
		return $dataStore;
		
	}

	/**
	 *	Pop an element from an array.
	 *
	 *	@param array Array to remove element from.
	 *	@param string Key of element to remove.
	 *	@return mixed Popped element
	 */
	public static function pop(array &$array, $key, $default = null)
	{
		if (array_key_exists($key, $array)){
			$element = $array[$key];
			unset($array[$key]);
			return $element;
		}
		return $default;
	}
	
	/**
	 *	Rename the keys for an array.
	 *
	 *	N.B. Overwrites elements with the same key.
	 *
	 *	@param array Array to rename keys for.
	 *	@param array List of keys to rename from/to.
	 *	@param bool Include unrenamed keys in output.
	 *	@return array Renamed array.
	 */
	public static function rename(array $original, array $mapping, $includeUnrenamed = true){
		
		/* Output */
		$output = array();
		
		/* Shift data about */
		if (!empty($mapping)){
			foreach($mapping as $from => $to){
				if (array_key_exists($from, $original)){
					$output[$to] = self::pop($original, $from);
				}
			}
		}
		
		/* Merge with unrenamed elements? */
		if ($includeUnrenamed){
			$output = array_merge($original, $output);
		}
		
		/* Return */
		return $output;
		
	}
	
	/**
	 *	Checks if an array is empty bar a specific key.
	 *
	 *	@param array Haystack
	 *	@param string/array Needle(s)
	 *	@return bool Success.
	 */
	public static function contains_not_only_keys($array, $keys) {
		
		// Array
		if (!self::iterable($array)) {
			return false;
		}
		
		// Criteria
		$keys = (array) $keys;
		if (empty($keys)) {
			return true;
		}
		
		// Remove unwanted
		foreach ($keys as $key) {
			unset($array[$key]);
		}
		
		// Test
		return !empty($array);
		
	}
	
	/**
	 * Convert an array to xml, using keys and values for element names and content
	 *
	 * @param array Array to convert
	 * @param int Level of nesting
	 * @return string XML
	 */	
	public static function toXML($array, $nestingLevel=0)
	{
		$text = '';
		foreach($array as $key => $value){
			if(!is_array($value)){
				$text .= str_repeat("\t", $nestingLevel);
				$text .= "<$key>".Utility::htmlNumericEntities($value)."</$key>\n";
			} else {
				if (!is_int($key)) {
					$text .= str_repeat("\t", $nestingLevel);
					$text .= "<$key>\n";
				}
				$text .= self::toXML($value, $nestingLevel+1);
				if (!is_int($key)) {
					$text .= str_repeat("\t",$nestingLevel);
					$text .= "</$key>";
				}
				$text.= "\n";
			}
		}
		return $text;
	}

	/**
	 *	Flatten an array (multi-dimensional into a 1D).
	 *
	 *	Only works for arrays with numeric keys.
	 *	(String keys overwrite each other when using array_merge).
	 *
	 *	@param array Multidimensional array
	 *	@return array Single-dimensional array.
	 */
	public static function flatten(array $array)
	{
		$master = array();
		while (list($key, $element) = each($array)) {
			switch (gettype($element)) {
				case 'object':
					$element = (array) $element;
					// Continue to array clause
					
				case 'array':
					$master = array_merge($master, self::flatten($element));
					break;
					
				default:
					$master[$key] = $element;
					break;
			}
		}
		return $master;
	}
	
	/**
	 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
	 * keys to arrays rather than overwriting the value in the first array with the duplicate
	 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
	 * this happens (documented behavior)
	 *
	 *	@see Yoinked from BluCommerce.
	*/
	public static function merge_recursive_distinct(array $array1, $array2 = null)
	{
		$merged = $array1;

		if (is_array($array2))
		foreach ($array2 as $key => $val){
			if (is_array($array2[$key])){
				$merged[$key] = (!empty($merged[$key]) && is_array($merged[$key])) ? self::merge_recursive_distinct($merged[$key], $array2[$key]) : $array2[$key];
			} else {
				$merged[$key] = $val;
			}
		}
		return $merged;
	}

	/**
	 *	Filter a 2D array by a flag
	 *
	 *	@static
	 *	@access public
	 *	@param array 2D array
	 *	@param string Flag name
	 *	@return array Filtered array
	 */
	public static function filterByFlag($array, $flag)
	{
		// Pass-through
		if (empty($array)) {
			return $array;
		}

		// Filter filter filter
		foreach ($array as $key => $row) {
			if (empty($row[$flag])) {
				unset($array[$key]);
			}
		}
		
		// Return
		return $array;
	}
}

?>
