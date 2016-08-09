<?php

namespace Yee\Managers;

use \Yee\Yee;
use \Yee\Libraries\Database\MysqliDB;

class DatabaseManager extends Yee 
{
    protected $config;
    
    /**
     * @param array $options
     */
    function __construct()
    {
    	$app = \Yee\Yee::getInstance();
    	$this->config = $app->config('database');
    	
    	foreach( $this->config as $key => $database )
    	{
    		 $app->db[$key] = new MysqliDB( $database['database.host'], $database['database.user'], $database['database.pass'], $database['database.name'], $database['database.port'] );
    	}	
    	
    	
    }



}
