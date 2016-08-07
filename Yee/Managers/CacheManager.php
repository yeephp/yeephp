<?php 
/**
 * Yee - a tiny PHP 5 framework -> provisional under development
 *
 * @author      Andreas Maier <info@yeephp.com>
 * @copyright   2014 Andreas Maier
 * @link        http://www.yeephp.com
 * @license     http://www.yeephp.com/license
 * @version     1.0.0
 * @package     Yee
 *
 * 
 * Usage:
 * $cache = new Cache();
 * if( true == $cache_data == $cache->jsonCacheStart( 'unique_name', 'language', 3600 ) {
 *      // all the logic to gather data to be saved in the cache
 *      $data = array();
 *      // can handle any set of data, most probably out of a database query or other...
 *      $data['variable1'] = 'somestring data'; 
 *      $data['variable2'] = array( 'var1' => 'somedata', 'var2' => 'somedata' );
 *      
 *      $cache->jsonCacheSave( 'unique_name', 'language', $data, 3600 );
 * } else {
 *     // do with your cache data whatever you need here
 *     $variable1 = $cache_data['variable1'];  // returns a string back into 
 *     $variable2 = $cache_data['variable2'];  // returns an array back into  
 * }
 * // continue using your data
 * 
 */

namespace Yee\Managers;

class Cache {
	
    protected $app;
    
    public function __construct()
    {
        $this->app = \Yee\Yee::getInstance();
    }
    
    /*
     *  Control Data Function or Init Cache start 
     */
	public function jsonCacheStart( $name, $language = '', $cache_timer=900 ) 
	{
		$cache_folder = $this->app->config('cache.path')."/json/";
		$cache_name   = md5($name.$language).".json";
	
		$ntime = strtotime( date("Y-m-d H:i:s") );
		$ftime = @filemtime( $cache_folder.$cache_name );
	
		$product = $ntime-$ftime;
	
		if((int)$cache_timer > (int)$product && $ftime!=false ) {   // 100 <17 //
			return json_decode( $this->jsonCacheUnCompress( file_get_contents( $cache_folder.$cache_name ) ), true ); // returns array in the event cache data is to be used
		} else {
			return true; // returns true in the event that the timer has expired
		}
	}
	
	/*
	 * Save compressed and json encoded Data  
	 */
	public function jsonCacheSave( $name, $language = '', $data )
	{
		$cache_folder = $this->app->config('cache.path')."json/";
		$cache_name   = md5( $name.$language ).".json";
		file_put_contents( $cache_folder.$cache_name, $this->jsonCacheCompress( json_encode($data) ) );
	}
	
	private function jsonCacheCompress( $data )
	{
	    return gzcompress( $data , 6, ZLIB_ENCODING_DEFLATE );
	    
	}
	
	private function jsonCacheUnCompress( $data )
	{
	     return gzuncompress( $data );
	}
	
	
}
