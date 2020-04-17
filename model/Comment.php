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

    public function __construct($userId, $postId, $body){
        $this->userId = $userId;
        $this->postId = $postId;
        $this->body = $body;
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
	
	public function get_user_by_userid(){
		$query = self::execute("SELECT * FROM User where UserId = :userid", array("userid"=>$this->userId));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["UserName"], $data["Password"], $data["FullName"], $data["Email"], $data["Role"]);
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
	
	public function get_commentid(){
		$id = Comment::get_id($this);
		return $id;
	}
	
	private static function get_id($comment){
		$query = self::execute("SELECT * FROM Comment where UserId =:userid and PostId =:postid and Body =:body ", 
			array("userid"=>$comment->userId, "postid"=>$comment->postId, "body"=>$comment->body));
        $data = $query->fetch();
		if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["CommentId"];
        }
	}
	
	public static function get_comment_by_id($commentId){
		$comment = "";
		$query = self::execute("SELECT * FROM Comment where CommentId = :commentid", array("commentid"=>$commentId));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            $comment = "";
        } else {
            $comment = new Comment($data['UserId'], $data['PostId'], $data['Body']);
        }
		return $comment;
	}
	
	public static function get_postid_by_id($commentId){
		$query = self::execute("SELECT PostId FROM Comment where CommentId =:commentId ", 
			array("commentId"=>$commentId));
        $data = $query->fetch();
		if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["PostId"];
        }
	}
	
	public function delete_comment(){
		self::execute("DELETE FROM Comment WHERE CommentId = :commentId", 
		array("commentId"=>$this->get_commentid()));
	}
	
	public function editComment($commentId){
		$date = new DateTime();
		$return = $date->format('Y-m-d H:i:s');
		//var_dump($this->body);
		self::execute("UPDATE Comment SET Body=:body, Timestamp =:return WHERE CommentId=:commentid", 
		array("body"=>$this->body , "return"=>$return, "commentid"=>$commentId));
		return $this;
		//var_dump($this);
	}
	
}
