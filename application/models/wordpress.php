<?php  namespace models;

class Wordpress extends \database {

	public static function get_posts_by_category($category, $filter_html=false){
		$sql = "SELECT 
					post_title,
					post_content,
					post_date,
					post_name
				FROM wp_posts 
				WHERE id IN (SELECT object_id
					 FROM wp_term_relationships
					 WHERE term_taxonomy_id = (SELECT term_taxonomy_id FROM `wp_term_taxonomy` 
											   WHERE term_id = (SELECT term_id 
															   FROM `wp_terms` 
															   WHERE name= :category
															   LIMIT 1)
											   AND taxonomy = 'category'
											   LIMIT 1))
				ORDER BY post_date DESC";
		\Database::safe_select($sql, array("category" => $category));
		if(\Database::$results){
			foreach(\Database::$results as &$result){
				$result['post_name'] =  str_replace("-","_",$result['post_name']);
				$result['post_date'] = date("F jS, Y @ G:i:s a", strtotime($result['post_date']));
				$result['post_content'] = trim(utf8_encode($result['post_content']));
				$result['post_content_full'] = $result['post_content'];
				$result['post_content'] = preg_replace("/&#?[a-z0-9]{2,8};/i","",$result['post_content']);
				$result['post_content'] = strip_tags($result['post_content']);
			}
		}
		return \Database::$results;
	}
}
		