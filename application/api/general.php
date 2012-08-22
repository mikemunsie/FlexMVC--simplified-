<?php namespace API;

class General extends \API{

/**
 * Login Check
 */	
	public static function twitter_status(){
		$userid = "munstrocity";
		$url = "http://twitter.com/statuses/user_timeline/$userid.xml?count=1";
		$xml = simplexml_load_file($url);
		if(!$xml) return \API::send_return();
    	foreach($xml->status as $status) $text = $status->text;
    	$text = (array)$text;
    	\API::$results = $text[0];
       	\API::$success = true;
       	return \API::send_return();
	}
}