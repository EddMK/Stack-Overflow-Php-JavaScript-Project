<?php

require_once "lib/parsedown-1.7.3/Parsedown.php";
require_once "framework/Model.php";
require_once "User.php";
require_once "Vote.php";

class Post extends Model {

    public $authorId;
    public $title;
    public $body;
    public $acceptedAnswerId;
	public $parentId;

    public function __construct($authorId, $title, $body, $acceptedAnswerId, $parentId) {
        $this->authorId = $authorId;
        $this->body = $body;
        $this->title = $title;
        $this->acceptedAnswerId = $acceptedAnswerId;
		$this->parentId = $parentId;
    }
	
	public function validate_question(){
		$errors = array();
		if(!(isset($this->title) && is_string($this->title) && strlen($this->title) > 0)){
            $errors[] = "Title must be filled";	
        }
		if(strlen(trim($this->title)) == 0){
			$errors[] = "Title contains only spaces";
		}
		if (!(isset($this->body) && is_string($this->body) && strlen($this->body) > 0)){
            $errors[] = "Body is required.";
		}
		if(strlen(trim($this->body)) == 0){
			$errors[] = "Body contains only spaces";
		}
		return $errors;
	}
	
	public function validate_answer(){
		$errors = array();
		if (!(isset($this->body) && is_string($this->body) && strlen($this->body) > 0)){
            $errors[] = "Body is required.";
		}
		if(strlen(trim($this->body)) == 0){
			$errors[] = "Body contains only spaces";
		}
		return $errors;
	}
	
	
	public function addPost(){
		//var_dump($this);
		self::execute("INSERT INTO post(AuthorId,Title,Body,AcceptedAnswerId,ParentId) VALUES(:authorId,:title,:body,:acceptedAnswerId,:parentId)", 
		array("authorId"=>$this->authorId, "title"=>$this->title, "body"=>$this->body, "acceptedAnswerId"=>$this->acceptedAnswerId, "parentId"=>$this->parentId));
		return $this;
	}
	
/*	
	public function markdown($texte){
		$Parsedown = new Parsedown();
		$Parsedown->setSafeMode(true);
		$return = $Parsedown->text($texte);
		return $return;
	}
*/
	
	public static function get_questions($user) {
        $query = self::execute("select * from Post where AuthorId = :userId and Title IS NOT NULL order by Timestamp DESC", array("userId" => $user->get_id()));
        $data = $query->fetchAll();
        $posts = [];
        foreach ($data as $row) {
            $posts[] = new Post($row['AuthorId'], $row['Title'], $row['Body'], $row['AcceptedAnswerId'], $row['ParentId']);
        }
        return $posts;
    }
	
	// TRIER QUESTIONS
	
	public static function get_questions_newest() {
        $query = self::execute("select * from Post where Title IS NOT NULL and Title <>'' order by Timestamp DESC", array());
        $data = $query->fetchAll();
        $posts = [];
        foreach ($data as $row) {
            $posts[] = new Post($row['AuthorId'], $row['Title'], $row['Body'], $row['AcceptedAnswerId'], $row['ParentId']);
        }
        return $posts;
    }
	
	public static function get_questions_votes() {
        $query = self::execute("SELECT post.*, max_score

FROM post, (

    SELECT parentid, max(score) max_score

    FROM (

        SELECT post.postid, ifnull(post.parentid, post.postid) parentid, ifnull(sum(vote.updown), 0) score

        FROM post LEFT JOIN vote ON vote.postid = post.postid

        GROUP BY post.postid

    ) AS tbl1

    GROUP by parentid

) AS q1

WHERE post.postid = q1.parentid

ORDER BY q1.max_score DESC, timestamp DESC", array());
        $data = $query->fetchAll();
        $posts = [];
        foreach ($data as $row) {
            $posts[] = new Post($row['AuthorId'], $row['Title'], $row['Body'], $row['AcceptedAnswerId'], $row['ParentId']);
        }
        return $posts;
    }
	
	public static function get_questions_unanswered () {
        $query = self::execute("select * from Post where Title IS NOT NULL and Title <>''  and AcceptedAnswerId IS NULL order by Timestamp DESC", array());
        $data = $query->fetchAll();
        $posts = [];
        foreach ($data as $row) {
            $posts[] = new Post($row['AuthorId'], $row['Title'], $row['Body'], $row['AcceptedAnswerId'], $row['ParentId']);
        }
        return $posts;
    }
	
	// FIN 
	
	
	public static function get_post($postid){
		$post = "";
		$query = self::execute("SELECT * FROM Post where PostId = :postid", array("postid"=>$postid));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            $post = "";
        } else {
            $post = new Post($data['AuthorId'], $data['Title'], $data['Body'], $data['AcceptedAnswerId'], $data['ParentId']);
        }
		return $post;
	}
	
	public static function addAccepterAnswer($questionId,$answerId){
		self::execute("UPDATE Post SET AcceptedAnswerId=:answerId WHERE PostId=:questionId", 
		array("answerId"=>$answerId, "questionId"=>$questionId));
	}
	
	public static function editPost($title,$body,$postid){
		$date = new DateTime();
		$return = $date->format('Y-m-d H:i:s');
		var_dump($return);
		self::execute("UPDATE Post SET Title=:title, Body=:body, Timestamp =:return WHERE PostId=:postid", 
		array("title"=>$title, "body"=>$body , "return"=>$return, "postid"=>$postid));
	}
	
	public static function deletePost($postid){
		var_dump(Vote::get_vote_by_postid($postid));
		if(Vote::get_vote_by_postid($postid) != false){
			Vote::delete_vote_by_postid($postid);
		}
		self::execute("DELETE FROM Post WHERE PostId = :postId", 
		array("postId"=>$postid));
	}
	
	public function get_postid(){
		$query = self::execute("SELECT * FROM Post where Title = :title", array("title"=>$this->title));
        $data = $query->fetch(); // un seul résultat au maximum
        //var_dump($data["PostId"]);
		if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["PostId"];
        }
	}
	
	public function get_answerid(){
		$query = self::execute("SELECT * FROM Post where Body = :body", array("body"=>$this->body));
        $data = $query->fetch(); // un seul résultat au maximum
        //var_dump($data["PostId"]);
		if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["PostId"];
        }
	}
	
	public function get_answers($question){//Attention à trier les réponses !!!
		if($question->acceptedAnswerId == NULL){
			$query = self::execute("SELECT post.*, max_score 
									FROM post, ( SELECT postid, max(score) max_score 
												FROM ( SELECT post.postid, ifnull(post.parentid, post.postid) parentid, ifnull(sum(vote.updown), 0) score 
														FROM post LEFT JOIN vote ON vote.postid = post.postid 
														WHERE post.ParentId = :postid 
														GROUP BY post.postid ) 
												AS tbl1 GROUP by postid ) AS q1 
									WHERE post.postid = q1.postid 
									ORDER BY q1.max_score DESC, timestamp DESC", 
			array("postid" => $question->get_postid()));
        }else{
			$query = self::execute("SELECT post.*, max_score 
									FROM post, ( SELECT postid, max(score) max_score 
												FROM ( SELECT post.postid, ifnull(post.parentid, post.postid) parentid, ifnull(sum(vote.updown), 0) score 
														FROM post LEFT JOIN vote ON vote.postid = post.postid 
														WHERE post.ParentId = :postid  and post.PostId<>:accepterAnswerId 
														GROUP BY post.postid )
												AS tbl1 GROUP by postid ) AS q1 
									WHERE post.postid = q1.postid 
									ORDER BY q1.max_score DESC, timestamp DESC", 
			array("postid" => $question->get_postid(),"accepterAnswerId" => $question->acceptedAnswerId));
		}		
		$data = $query->fetchAll();
        $posts = [];
        foreach ($data as $row) {
            $posts[] = new Post($row['AuthorId'], $row['Title'], $row['Body'], $row['AcceptedAnswerId'], $row['ParentId']);
        }
        return $posts;
	}
	
	
	
	public function get_author_by_authorId(){
		$query = self::execute("SELECT * FROM User where UserId = :authorid", array("authorid"=>$this->authorId));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["UserName"], $data["Password"], $data["FullName"], $data["Email"]);
        }
	}
	
	public function getLastVote($userId,$postId){
		$query = self::execute("SELECT * FROM Vote where UserId =:userId and PostId =:postId", array("userId"=>$userId, "postId"=>$postId));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["UpDown"];
        }
	}
	
	public function get_score(){
		$query = self::execute("SELECT SUM(UpDown) FROM Vote WHERE PostId=:postid", array("postid"=>$this->get_postid()));
		return $query->fetchColumn();
	}
	
	public function get_score_answer(){
		$query = self::execute("SELECT SUM(UpDown) FROM Vote WHERE PostId=:postid", array("postid"=>$this->get_answerid()));
		return $query->fetchColumn();
	}
	
	public function get_timestamp(){
		$query = self::execute("SELECT * FROM Post where Body = :body", array("body"=>$this->body));
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
		//var_dump($dateNow);
		$dateThis = new DateTime($this->get_timestamp());
		//var_dump($dateThis);
		$diff = $dateNow->diff($dateThis);
		//var_dump($diff);
		$valeur = array($diff->y,$diff->m, $diff->d,$diff->h,$diff->i,$diff->s);
		$cle = array("year","month","day","hour","minute","seconde");
		$i = 0;
		while($i<count($valeur) && $valeur[$i]==0){
			$i ++;
		}
		//var_dump($i);
		if($i == 6){
			return '0 secondes';
		}else{
			return $valeur[$i].' '.$cle[$i].' ago';
		}
	}
	
	public function is_question(){
		if($this->title == "" || $this->title == NULL){
			return false;
		}else{
			return true;
		}
	}
	
	
}
