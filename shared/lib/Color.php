<?php

/**
 * Color Library
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Color
{
	/**
	 * HSV to RGB
	 *
	 * @param array (H, S, V)
	 * @return array (R, G, B)
	 */
	public static function hsvToRgb($hsv)
	{
		$h = $hsv[0];
		$s = $hsv[1];
		$v = $hsv[2];

		if ($s == 0) { // achromatic (grey)
	        return array($v, $v, $v);
	    }

	    if ($h == 360) {
	    	$htemp = 0;
	    } else {
	    	$htemp = $h;
	    }
	    $htemp = $htemp / 60;
	    $i = floor($htemp); // integer <= h
	    $f = $htemp - $i; // fractional part of h
	    $p = $v * (1-$s);
	    $q = $v * (1-($s*$f));
	    $t = $v * (1-($s*(1-$f)));
	    if ($i==0) {$r=$v;$g=$t;$b=$p;}
	    if ($i==1) {$r=$q;$g=$v;$b=$p;}
	    if ($i==2) {$r=$p;$g=$v;$b=$t;}
	    if ($i==3) {$r=$p;$g=$q;$b=$v;}
	    if ($i==4) {$r=$t;$g=$p;$b=$v;}
	    if ($i==5) {$r=$v;$g=$p;$b=$q;}
	    $r = round($r);
	    $g = round($g);
	    $b = round($b);

	    return array($r, $g, $b);
	}

	/**
	 * RGB to HSV
	 *
	 * @param array (R, G, B)
	 * @return array (H, S, V)
	 */
	public static function rgbToHsv($rgb)
	{
		$r = $rgb[0];
		$g = $rgb[1];
		$b = $rgb[2];

	    $v = max(max($r, $g), $b);
	    $min = min(min($r, $g), $b);
	    $delta = $v - $min;
	    if ($v == 0) {
	    	$s = 0;
		} else {
	        $s = $delta / $v;
	    }
	    if ($s == 0) {
	        $h = 0; //achromatic.  no hue
	    } else {
	        if ($r == $v) { // between yellow and magenta [degrees]
	        	$h = 60*($g-$b)/$delta;
	        } elseif ($g == $v) { // between cyan and yellow
	            $h = 120+60*($b-$r)/$delta;
	        } elseif ($b == $v) { // between magenta and cyan
	        	$h = 240+60*($r-$g)/$delta;
	        }
	    }
	    if ($h < 0) {
	        $h+=360;
	    }

		return array($h, $s, $v);
	}

	/**
	 * Convert RGB to hex
	 *
	 * @param array (R, G, B)
	 * @retrun string Hex colour (eg. #FF0000)
	 */
	public static function rgbToHex($rgb)
	{
		return sprintf('#%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
	}

	/**
	 * Convert a hex colour to RGB
	 *
	 * @param string Hex colour (eg. #FF0000)
	 * @retrun array (R, G, B)
	 */
	public static function hexToRgb($hex)
	{
        // Strip off any leading #
        $hex = str_replace('#', '', $hex);

        // Break into hex 3-tuple
        $cutpoint = ceil(strlen($hex) / 2)-1;
        $rgb = explode(':', wordwrap($hex, $cutpoint, ':', $cutpoint), 3);

        // Convert each tuple to decimal
        foreach ($rgb as $k => &$v) {
        	if (strlen($v) < 2) {
        		$v = str_repeat($v, 2);
        	}
        	$v = hexdec($v);
        }

        return $rgb;
    }

}

?>