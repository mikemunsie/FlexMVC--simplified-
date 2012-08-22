<?php namespace API;

class Posts extends \API{
	
	public static function posts_by_category(){
		if(!isset(\flexMVC::$request['category'])) return \API::send_return();
		\API::$results = \Models\Wordpress::get_posts_by_category(\flexMVC::$request['category'], true);
		\API::$success = (bool)\API::$results;
		return \API::send_return();
	}
}
