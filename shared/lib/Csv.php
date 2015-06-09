<?php

/**
 *	CSV library.
 *
 *	@package BluApplication
 *	@subpackage SharedLib
 */
class Csv
{
	/**
	 *	Headings (from document).
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_headings = array();
	
	/**
	 *	Document data.
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_data = array();
	
	/**
	 *	Global display options.
	 *
	 *	@static
	 *	@access public
	 *	@var array
	 */
	public static $options = array(
		'outputHeadings' => true,
		'autoPad' => '',
		'encoding' => 'UTF-8',
		'delimiter' => ',',
		'enclosure' => '"',
		'escape' => '\'',
		'linebreak' => "\r\n"
	);
	
	/**
	 *	Current file's display options.
	 *
	 *	@access protected
	 *	@var array
	 */
	protected $_options = array();
	
	/**
	 *	Create new instance.
	 *
	 *	@access public
	 *	@param array Headings
	 *	@param array Options
	 */
	public function __construct(array $headings, array $options = array())
	{
		// Prepare
		if (empty($headings)) {
			return false;
		}
		$this->_headings = self::escape($headings);
		
		// Set options
		$this->_options = array_merge(self::$options, $options);
	}
	
	/**
	 *	Get the raw data.
	 *
	 *	@access public
	 *	@return array
	 */
	public function get()
	{
		return $this->_data;
	}
	
	/**
	 *	Add a row to the data.
	 *
	 *	@access public
	 *	@param array Row to add
	 *	@return this
	 */
	public function appendRow($row)
	{
		// Sanity
		if (empty($row)) {
			return false;
		}
		
		// Check if empty row
		$notEmpty = false;
		foreach ($row as $datum) {
			$datum = (string) $datum;
			if (strlen($datum)) {
				$notEmpty = true;
				break;
			}
		}
		if (!$notEmpty) {
			return false;
		}
		
		// Check row length matches headers length
		$desiredLength = count($this->_headings);
		if (count($row) != $desiredLength) {
			
			// Use auto-padding
			if ($this->_options['autoPad'] === false) {
				return false;
			}
			
			// Chop
			$row = array_pad($row, $desiredLength, $this->_options['autoPad']);		// Too short.
			$row = array_slice($row, 0, $desiredLength, true);						// Too long.
			
		}
		
		// Convert encoding
		$row = Utility::convert_encoding($row, $this->_options['encoding'], array(
			'fix_excel' => true
		));
		
		// Append
		$this->_data[] = $row;
		
		// Return
		return $this;
	}
	
	/**
	 *	Shortcut.
	 *
	 *	@access public
	 *	@param array Rows to add.
	 *	@return this.
	 */
	public function appendRows($rows)
	{
		// Sanity
		if (empty($rows)) {
			return false;
		}
		
		// Loop
		foreach ($rows as $row) {
			$this->appendRow($row);
		}
		
		// Return
		return $this;
	}	
	
	/**
	 *	Output raw data as a string.
	 *
	 *	@access public
	 *	@param &var Return buffer.
	 *	@return this
	 */
	public function output(&$buffer = null)
	{
		// Buffer?
		if (!is_null($buffer)) {
			ob_start();
		}
		
		// Output headings?
		if ($this->_options['outputHeadings']) {
			$headings = self::escape($this->_headings);	// Clean.
			$this->_outputRow($this->_headings);
		}
		
		// Output data
		if (!empty($this->_data)) {
			$data = self::escape($this->_data);	// Clean.
			foreach ($data as $row) {
				echo $this->_options['linebreak'];
				$this->_outputRow($row);
			}
		}
		
		// End buffer?
		if (!is_null($buffer)) {
			$buffer = ob_get_clean();
		}
		
		// Return
		return $this;
	}
	
	/**
	 *	Output a row of data as a string.
	 *
	 *	@access protected
	 *	@param array Row of data.
	 *	@return this
	 */
	protected function _outputRow($row)
	{
		// Sanity
		if (empty($row)) {
			return false;
		}
		
		// Output
		echo implode($this->_options['delimiter'], $row);
		
		// Return
		return $this;
	}
	
	/**
	 *	Escape content.
	 *
	 *	@static
	 *	@access public
	 *	@param mixed String or array of strings.
	 *	@return mixed Same as above, but escaped.
	 */
	public static function escape($input)
	{
		// Recurse
		if (Arrays::iterable($input)) {
			$output = array();
			foreach ($input as $key => $value) {
				$output[$key] = self::escape($value);
			}
			
		// Escape
		} else if (is_string($input)) {
			$output = '"'.str_replace('"', '""', $input).'"';
			
		// Skip
		} else {
			$output = $input;
		}
		
		// Return
		return $output;
	}
	
	/**
	 *	Read in data from a CSV file.
	 *
	 *	@static
	 *	@access public
	 *	@param string Source filepath
	 *	@param array CSV parse options
	 *	@return Csv.
	 */
	public static function read($path, array $options = array())
	{
		// Open file handler
		if (!$fileHandle = fopen($path, 'rb')) {
			return false;
		}
		
		// Get delimiters etc.
		$options = array_merge(self::$options, $options);
		extract($options);
		
		// Read in first row of file as headings
		$headings = fgetcsv($fileHandle, 0, $delimiter, $enclosure);
		if (empty($headings)) {
			fclose($fileHandle);
			return false;
		}
		
		// Instantiate object
		$csv = new Csv($headings, $options);
		
		// Read in rest of data
		while ($row = fgetcsv($fileHandle, 0, $delimiter, $enclosure)) {
			$csv->appendRow($row);
		}
		
		// Clean up
		fclose($fileHandle);
		
		// Return object
		return $csv;
	}
	
	/**
	 *	String (HTML) representation.
	 *
	 *	@access public
	 *	@return string.
	 */
	public function __toString()
	{
		// Start buffer
		ob_start();
		
		// Display HTML table
		?>
		
		<table>
			<tr>
		<?php foreach ($this->_headings as $heading) { // Headings ?>
		
				<th><?= $heading; ?></th>
		<?php } ?>
		
			</tr>
		<?php foreach ($this->_data as $row) { // Data row ?>
		
			<tr>
		<?php foreach ($row as $entry) { // Data cell ?>
		
				<td><?= $entry; ?></td>
		<?php } ?>
		
			</tr>
		<?php } ?>
		
		</table>
		<?php
		
		// Return
		return ob_get_clean();
	}
	
	/**
	 *	Check whether a CSV entry is "empty".
	 *
	 *	@static
	 *	@access public
	 *	@param string Content
	 *	@return bool
	 */
	public static function isEmpty($entry)
	{
		return empty($entry) || (is_string($entry) && in_array(strtolower(trim($entry)), array('null', 'n/a')));
	}
}

?>
