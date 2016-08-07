<?php

namespace Yee\Managers\Controller;

use Yee\Yee;


abstract class Controller    
{
    protected $app;
   
    function __construct()
    {
    	$this->app = Yee::getInstance();
    }

    public static function __callStatic($name, $arguments)
    {
        $calledClass = get_called_class();
        $obj         = new $calledClass;
        $name        = preg_replace('/^___/','',$name);
        call_user_func_array(array($obj, $name), $arguments);
    }

    /**
     * @return Yee
     */
    protected function getYee()
    {
        return $this->app;
    }

    /**
     * Returns the logic name of the current route that are currently processed by Yee
     *
     * @return string
     */
    protected function getName()
    {
        return $this->app->router()->getCurrentRoute()->getName();
    }
}