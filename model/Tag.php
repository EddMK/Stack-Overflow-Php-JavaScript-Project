<?php

require_once "lib/parsedown-1.7.3/Parsedown.php";
require_once "framework/Model.php";
require_once "User.php";
require_once "Vote.php";
require_once "Post.php";

class Tag extends Model {

    public $tagId;
    public $tagName;
    

    public function __construct($tagId, $tagName){
        $this->tagId = $tagId;
        $this->tagName = $tagName;
    }
	
	public function get_numbers_of_posts(){
		$number = Tag::numbers_of_posts($this->tagId);
		return $number;
	}
	
	private static function numbers_of_posts($tagId){
		$query = self::execute("SELECT COUNT(*) FROM posttag where TagId =:tagid", array("tagid"=>$tagId));
        $data = $query->fetchColumn();
		if($data == false){
			return 0;
		}else{
			return $data;
		}
	}
	
	public static function get_tags(){
		$query = self::execute("select * from Tag", array());
        $data = $query->fetchAll();
        $tags = [];
        foreach ($data as $row) {
            $tags[] = new Tag($row['TagId'], $row['TagName']);
        }
        return $tags;
	}
}
