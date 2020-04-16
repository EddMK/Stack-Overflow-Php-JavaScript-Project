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
	
	public function get_author_by_authorId(){
		$query = self::execute("SELECT * FROM User where UserId = :userid", array("userid"=>$this->userId));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["UserName"], $data["Password"], $data["FullName"], $data["Email"]);
        }
	}
	
	private static function get_timestamp($comment){
		$query = self::execute("SELECT * FROM Comment where Body = :body", array("body"=>$comment->body));
        $data = $query->fetch(); // un seul résultat au maximum
		//var_dump($data["Timestamp"]);
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["Timestamp"];
        }
	}
	
	public function get_ago(){
		$dateNow =  date_create();
		$dateThis = new DateTime(Comment::get_timestamp($this));
		$diff = $dateNow->diff($dateThis);
		$valeur = array($diff->y,$diff->m, $diff->d,$diff->h,$diff->i,$diff->s);
		$cle = array("year(s)","month(s)","day(s)","hour(s)","minute(s)","seconde(s)");
		$i = 0;
		while($i<count($valeur) && $valeur[$i]==0){
			$i ++;
		}
		if($i == 6){
			return '0 secondes';
		}else{
			return $valeur[$i].' '.$cle[$i].' ago';
		}
	}
	
}
