<?php

namespace Yee\Helper;

class Encryption {
	
	protected $key;
	
	protected $data;
	
	/**
	 * 
	 * @param string $key
	 * @param string $data
	 */
	public function __construct($key, $data) {
		$this->setKey($key);
		$this->setData($data);
	}
	
	/**
	 * 
	 * Sets the key for encryption/decryption
	 * @param string $key
	 */
	public function setKey($key) {
		$this->key = $key;
		return $this;
	}
	
	/**
	 * 
	 * Sets the data to be encryption/decryption
	 * @param string $key
	 */
	public function setData($data) {
		$this->data = $data;
		return $this;
	}
	
	/**
	 * 
	 * Encrypts the data
	 */
	public function encrypt() {
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$crypttext = mcrypt_encrypt(MCRYPT_BLOWFISH, $this->key, $this->data, MCRYPT_MODE_ECB, $iv);
		return $crypttext;
	}

	/**
	 * 
	 * Encrypts and encodes (via base 64) the data 
	 */
	public function encrypt64Encoded() {
		return base64_encode($this->encrypt());
	}
	
	/**
	 * 
	 * Decrypts the data
	 */
	public function decrypt() {
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypttext = mcrypt_decrypt(MCRYPT_BLOWFISH, $this->key, $this->data, MCRYPT_MODE_ECB, $iv);
		return trim($decrypttext);
	}
	
	/**
	 * 
	 * Decrypts data which are encrypted and encoded via base 64
	 */
	public function decrypt64Encoded() {
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decrypttext = mcrypt_decrypt(MCRYPT_BLOWFISH, $this->key, base64_decode($this->data), MCRYPT_MODE_ECB, $iv);
		return trim($decrypttext);
	}
	
	
	public function decryptPassword()
	{
	    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, $this->data, MCRYPT_MODE_ECB, $iv);
	    return trim($decrypttext);
	}
	
	public function encryptPassword()
	{
	    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $this->data, MCRYPT_MODE_ECB, $iv);
	    return $crypttext;
	}
	
	public function encryptAPIValues($key, $data) {
	    $iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    return base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv ) );
	} 
	
	public function decryptAPIValues($key, $data)
	{
	    $data = base64_decode($data);
	    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv);
	    return trim($decrypttext);
	}
}
?>