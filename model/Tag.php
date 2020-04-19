<?php

require_once "lib/parsedown-1.7.3/Parsedown.php";
require_once "framework/Model.php";
require_once "User.php";
require_once "Vote.php";
require_once "Post.php";
require_once "Comment.php";

class Tag extends Model {

    public $tagName;


    public function __construct($tagName){
        $this->tagName = $tagName;
    }
	
	public static function addTag($name){
		self::execute("INSERT INTO tag(TagName) VALUES(:tagName))", 
		array("tagName" => $name));
	}
	

	public function validate(){
		$errors = array();
		if(!(isset($this->tagName) && is_string($this->tagName) && strlen($this->tagName) > 0)){
            $errors[] = "tagName must be filled";	
        }
		if(strlen(trim($this->tagName)) == 0){
			$errors[] = "tagName contains only spaces";
		}
		//verifier l'unicité
		if(Tag::exist($this->tagName)==true){
			$errors[] = "tagName must be unique";
		}	
		return $errors;
	}
	
	private static function exist($tagName){
		$query = self::execute("SELECT * FROM Tag where TagName = :tagName", array("tagName"=>$tagName));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
	}
	
	public function get_numbers_of_posts(){
		$number = Tag::numbers_of_posts($this->get_tagId());
		return $number;
	}
	
	public function get_tagId(){
		$query = self::execute("SELECT * FROM Tag where TagName = :tagName", array("tagName"=>$this->tagName));
        $data = $query->fetch(); // un seul résultat au maximum
        //var_dump($data["PostId"]);
		if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["TagId"];
        }
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
	
	public static function editTag($tagName,$tagId){
		self::execute("UPDATE Tag SET TagName=:tagName WHERE TagId=:tagId", 
		array("tagName"=>$tagName, "tagId"=>$tagId));
	}
	
	public static function get_tags(){
		$query = self::execute("select * from Tag", array());
        $data = $query->fetchAll();
        $tags = [];
        foreach ($data as $row) {
            $tags[] = new Tag($row['TagName']);
        }
        return $tags;
	}
	
	public static function get_tag($tagId){
		$tag = "";
		$query = self::execute("SELECT * FROM tag where TagId = :tagId", array("tagId"=>$tagId));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            $tag = "";
        } else {
            $tag = new Tag($data['TagName']);
        }
		return $tag;
	}
	
	public function delete_tag(){
		self::execute("DELETE FROM posttag WHERE TagId = :tagId", 
		array("tagId"=>$this->get_tagId()));
		self::execute("DELETE FROM Tag WHERE TagId = :tagId", 
		array("tagId"=>$this->get_tagId()));
	}
}
