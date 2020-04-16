<?php

require_once "lib/parsedown-1.7.3/Parsedown.php";
require_once "framework/Model.php";
require_once "User.php";
require_once "Vote.php";
require_once "Post.php";

class Comment extends Model {

    public $userId;
    public $postId;
    public $body;
    //public $timeStamp;

    public function __construct($userId, $postId, $body){
        $this->userId = $userId;
        $this->postId = $postId;
        $this->body = $body;
        //$this->timeStamp = $timeStamp;
    }
	
	public function validate(){
		$errors = array();
		if (!(isset($this->body) && is_string($this->body) && strlen($this->body) > 0)){
            $errors[] = "Body is required.";
		}
		if(strlen(trim($this->body)) == 0){
			$errors[] = "Body contains only spaces";
		}
		return $errors;
	}
	
	public function addComment(){
		//var_dump($this);
		self::execute("INSERT INTO comment(UserId,PostId,Body) VALUES(:userId,:postId,:body)", 
		array("userId"=>$this->userId, "postId"=>$this->postId, "body"=>$this->body));
		return $this;
	}
	
	
}
