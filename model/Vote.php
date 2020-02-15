<?php

require_once "lib/parsedown-1.7.3/Parsedown.php";
require_once "framework/Model.php";
require_once "User.php";

class Vote extends Model {

    public $userId;
    public $postId;
    public $upDown;

    public function __construct($userId, $postId, $upDown) {
        $this->userId = $userId;
        $this->postId = $postId;
        $this->upDown = $upDown;
    }
	
	public static function get_vote_by_userid_and_postid($userId,$postId) {
        $query = self::execute("SELECT * FROM Vote where UserId = :userId and PostId = :postId", array("userId"=>$userId,"postId"=>$postId));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Vote($data["UserId"], $data["PostId"], $data["UpDown"]);
        }
    }
	
	public function addVote(){
		self::execute("INSERT INTO vote(UserId,PostId,UpDown) VALUES(:userId,:postId,:upDown)", 
		array("userId"=>$this->userId, "postId"=>$this->postId, "upDown"=>$this->upDown));
		return $this;
	}
	
	public function deleteVote(){
		self::execute("DELETE FROM vote WHERE UserId = :userId and PostId = :postId", 
		array("userId"=>$this->userId, "postId"=>$this->postId));
		return $this;
	}
	
	//"UPDATE Members SET password=:password, picture_path=:picture, profile=:profile WHERE pseudo=:pseudo "
	
	public function updateVote($upDown){
		self::execute("UPDATE Vote SET UpDown=:upDown WHERE UserId=:userId and PostId=:postId", 
		array("userId"=>$this->userId, "postId"=>$this->postId, "upDown"=>$upDown));
	}
		

}
