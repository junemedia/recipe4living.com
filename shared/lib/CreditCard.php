<?php

/**
 * Credit Card Library
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class CreditCard
{
	/**
	 * Check a credit card number for validity
	 * 
	 * @param int Credit card number (may opionally contain asterisks)
	 * @param bool Whether to allow asterisks	 
	 * @return bool True if valid, false otherwise	 	 
	 */
	public static function isValid($cardNumber, $allowAsterisks = true)
	{		
		return ((strlen($cardNumber) <= 19) && 
			(($allowAsterisks && (strpos($cardNumber, '*') !== false)) || 
				(($cardNumber > 0) && self::doLuhn($cardNumber))));
	}
		 	 	
	/**
     * Get a credit card type
     *
     * @param int Credit card number
     * @return string Credit card type
     */
    public static function getType($cardNumber)
    {
    	if (!self::isValid($cardNumber, false)) {
			return false;
		}
		
		// Get card number length
		$len = strlen($cardNumber);
		
		if ((($len == 16) || ($len == 13)) && (substr($cardNumber, 0, 1) == 4)) {
			return "VISA";  // Visa test no : 4111 1111 1111 1111
		}
	
		$firstDig = substr($cardNumber, 0, 1);
		$secondDig = substr($cardNumber, 1, 1);
		$first4Digs = substr($cardNumber, 0, 4);
	
		if (($len == 16) && ($firstDig == 5) && (($secondDig >= 1) && ($secondDig <= 5))) {
			return "MC"; // Mastercard test no : 5500 0000 0000 0004
		}
	
		if (($len == 15) && ($firstDig == 3) && (($secondDig == 4) || ($secondDig == 7))) {
			return "AMEX"; // AMEX test no : 340000000000009
		}
	
		if (($len == 14) && ($firstDig == 3) &&	(($secondDig == 0) || ($secondDig == 6) || ($secondDig == 8))) {
			return "DC"; // Diners club test no : 30000000000004
		}
		
	    if (($len == 16) && ($first4Digs == "6011")) {
			return "DISCOVER"; // Discover test no : 6011000000000004
		}
		
	    if (($len == 16) && ($first4Digs == "5610")) {
			return "AUSBANK"; // Aus Bank test no : 5610591000000009
		}
		
	    if (($len == 15) && (($first4Digs == "2014") || ($first4Digs == "2149"))) {
			return "ENROUTE"; // Enroute test no : 201400000000009
		}
	
		if (($len == 16) && (($first4Digs == "3088") || ($first4Digs == "3096") || ($first4Digs == "3112") || ($first4Digs == "3158") || ($first4Digs == "3337") || ($first4Digs == "3528"))) {
			return "JCB";
		}
	
		if (($len == 16 || $len == 17 || $len == 18 || $len == 19) && (($first4Digs == "4903") || ($first4Digs == "4911") || ($first4Digs == "4936") || ($first4Digs == "5641") || ($first4Digs == "6333") || ($first4Digs == "6759") || ($first4Digs == "6334") || ($first4Digs == "6767"))) {
	        return "MAESTRO"; // Switch test no : 675911111111111128
	    }
    }
    
    /**
     * Run the Luhn algorithm over to do a basic validity check
     * 
     * @param int Credit card numbet
     * @return True on pass, false on fail	 	      
	 */
    public static function doLuhn($cardNumber) {
    	
    	$sum = 0; $mul = 1; $l = strlen($cardNumber);
		for ($i = 0; $i < $l; $i++) {
			$digit = (int)substr($cardNumber, $l-$i-1, 1);
			$tproduct = $digit*$mul;
			if ($tproduct >= 10) {
				$sum += ($tproduct % 10) + 1;
			} else {
				$sum += $tproduct;
			}
			if ($mul == 1) {
				$mul++;
			} else {
				$mul = $mul - 1;
			}
		}
	    return (($sum % 10) === 0);
    }
}
?>