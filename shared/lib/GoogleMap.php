<?php

/**
 * Google map helper object
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class GoogleMap
{
	/**
	 * Map GEO points
	 * 
	 * @var array
	 */	 	 	 	
    private $_points = array();

	/**
	 * Google API key
	 * 
	 * @var string
	 */	 	 	 	
    private $_apiKey = null;
    
	/**
	 * Whether to show map type controls 
	 * 
	 * @var bool
	 */	 	 	 	
    private $_showTypeControls = false;
    
	/**
	 * Map control type (small / large / null)
	 * 
	 * @var string
	 */	  	 	
    private $_controlType = 'large';
    
    /**
     * Map latitude
     * 
     * @var float
     */
	private $_lat = 53.15;
	
	/**
     * Map longitude
     * 
     * @var float
     */
	private $_lng = -5;
	
	/**
	 * Map width
	 * 
	 * @var int
	 */	 	 	 	
    private $_width = 640;
	
	/**
	 * Map height
	 * 
	 * @var int
	 */	 	 	 	
    private $_height = 480;
		 	 	     
	/**
	 * Map zoom level
	 * 
	 * @var int
	 */	 	 	 	
    private $_zoom = 11;

	/**
	 * Constructor
	 */	 	
	public function __construct()
	{
		$this->_apiKey = BluApplication::getSetting('GMapsAPIKey');
	}

	/**
	 * Add a GEO point to the map
	 * 
	 * @param float Latitude
	 * @param float Longitude
	 * @param int Point id
	 */	 	 	 	 	 	
	public function addGeoPoint($lat, $lng, $id)
	{
		$this->_points[] = array(
			'lat' => $lat,
			'lng' => $lng,
			'id' => $id);
	}
    
	/**
	 * Set map width
	 * 
	 * @param int Map width
	 */	 	 	 	
	public function setWidth($width)
	{
		$this->_width = $width;
	}

	/**
	 * Set map height
	 * 
	 * @param int Map width
	 */	 	 
	public function setHeight($height)
	{
		$this->_height = $height;
	}
	
	/**
	 * Set map zoom level
	 * 
	 * @param int Zoom level
	 */
	public function setZoom($zoom)
	{
		$this->_zoom = $zoom;
	}
	
	/**
	 * Set start latitude and longitude co-ordinates
	 * 
	 * @param float Latitude	 	 	
	 * @param float Longitude
	 */	  	 
	public function setCoords($lat, $lng)
	{
		$this->_lat = $lat;
		$this->_lng = $lng;
	}
	
	/**
	 * Show map type control?
	 * 
	 * @param bool Whether to show map type control
	 */
	public function setTypeControlsVisibility($visibility)
	{
		$this->_showTypeControls = $visibility;
	}
	
	/**
	 * Set map control type (large / small / null)
	 * 
	 * @param string Map control type
	 */
	public function setControlType($type)
	{
		$this->_controlType = $type;
	}
	
	/**
	 * Get google maps JS URL
	 * 
	 * @param string Google JS script source URL	 	 	 
	 */
	public function getGoogleJS()
	{
		return 'http://maps.google.com/maps?file=api&v=2.x&key='.$this->_apiKey;
	}
	
	/**
	 * Get map options for interactive javascript
	 * 
	 * @param float Latitude to intially center map about
	 * @param float Longitude to intially center map about	 
	 * @return array Array of map options
	 */
	public function getMapOptions()
	{	
		$mapOptions = array();
		$mapOptions['lat'] = $this->_lat;
		$mapOptions['lng'] = $this->_lng;
		$mapOptions['zoom'] = $this->_zoom;
		$mapOptions['controlType'] = $this->_controlType;
		$mapOptions['showTypeControls'] = $this->_showTypeControls;
		$mapOptions['points'] = $this->_points;
		
		return $mapOptions;
	}

	/**
	 * Get fallback static map image url
	 * 
	 * @param float Latitude to center map about
	 * @param float Longitude to center map about
	 * @return string Map image url
	 */
	public function getMapImage()
	{
		// Colour selection for markers 
		$colors = array('black', 'brown', 'green', 'purple', 'yellow', 'blue', 'gray', 'orange', 'red', 'white');
		
		// Set map vars
		$mapVars['center'] = $this->_lat.','.$this->_lng;
		$mapVars['zoom'] = $this->_zoom;
		$mapVars['size'] = min(640, $this->_width).'x'.min(640, $this->_height);
		$mapVars['key'] = $this->_apiKey;
		$mapVars['maptype'] = 'mobile';
		
		// Add marker points
		foreach ($this->_points as $g => $point) {
			$markers[] = $point['lat'].','.$point['lng'].',small'.$colors[array_rand($colors)];   
		}
		$mapVars['markers'] = implode ('|', $markers);
		
		// Build map image url
		$imageUrl = 'http://maps.google.com/staticmap?';
		foreach ($mapVars as $key => $mapVar) {
			$imageUrl .= $key.'='.$mapVar.'&';	
		}
		
		return $imageUrl;
	}
}

?>
