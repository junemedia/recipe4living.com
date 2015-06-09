<?php

/**
 * Crypto Object - suitable for moderate security applications
 *
 * @package BluApplication
 * @subpackage SharedLib
 */
class Crypto
{
	/**
	 * The crypto object
	 *
	 * @var object
	 */
	private $_crypto;

	/**
	 * Crypto object constructor
	 *
     * @param string Key
	 * @param string Algorithm
     * @param string Mode
	 */
	private function __construct($key, $algorithm = 'RIJNDAEL_256', $mode = 'ECB')
    {
        // set mcrypt mode and cipher  
        $this->_crypto = mcrypt_module_open($algorithm, '', $mode, '') ;  

        // Unix has better pseudo random number generator then mcrypt, so if it is available lets use it!  
        $random_seed = MCRYPT_DEV_RANDOM;  

        // if initialization vector set in constructor use it else, generate from random seed  
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->_crypto), $random_seed);

        // get the expected key size based on mode and cipher  
        $expected_key_size = mcrypt_enc_get_key_size($this->_crypto);  

        // we dont need to know the real key, we just need to be able to confirm a hashed version  
        $key = substr(md5($key), 0, $expected_key_size);  

        // initialize mcrypt library with mode/cipher, encryption key, and random initialization vector  
        mcrypt_generic_init($this->_crypto, $key, $iv);  

    }

    /**
	 * Encryptor - returns base64 encoded encrypted string
	 *
     * @param string Plaintext string
     * @return String Base64 encoded Encrypted string
	 */
    public function encrypt($plain_string)  
    {      
        return base64_encode(mcrypt_generic($this->_crypto, $plain_string));  
    }  

    /**
	 * Decryptor - returns decrypted string
	 *
     * @param string Encrypted string
     * @return String Decrypted string
	 */
    public function decrypt($encrypted_string)  
    {   
        return trim(mdecrypt_generic($this->_crypto, base64_decode($encrypted_string)));  
    }    

    /**
	 * Destructor - does GC on mcrypt 
	 */
    public function __destruct()  
    {  
        // shutdown mcrypt  
        mcrypt_generic_deinit($this->_crypto);  

        // close mcrypt cipher module  
        mcrypt_module_close($this->_crypto);  
    }

    /**
     * Returns a reference to the global Crypto object, only creating it
     * if it doesn't already exist
     * @param string Key
	 * @param string Algorithm
     * @param string Mode
     *
	 * @return Crypto A crypto object
	 */
	public static function &getInstance($key, $algorithm = 'tripledes', $mode = 'ecb')
	{
		static $instances;
		if (!isset($instances)) {
			$instances = array();
		}
		
		$args = func_get_args();
		$signature = serialize($args);
		
		if (empty($instances[$signature])) {
			$c = __CLASS__;
			$instances[$signature] = new $c($key, $algorithm, $mode);
		}
		return $instances[$signature];
	}
}
?>
