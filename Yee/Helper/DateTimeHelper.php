<?php 
namespace Yee\Helper;

class DateTimeHelper{
	
	public static function validateDate( $date, $format = 'Y-m-d H:i:s' )
	{
		$d = \DateTime::createFromFormat($format, $date);
    	return $d && $d->format($format) == $date; 
	}
	
}