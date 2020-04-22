<?php

require_once "lib/parsedown-1.7.3/Parsedown.php";
require_once "framework/Model.php";
require_once "User.php";
require_once "Vote.php";
require_once "Comment.php";

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
	
	public static function get_searchs($search, $page) {
        $requete = "";
		if($page == null){
			$requete = " SELECT * FROM `post` WHERE Title LIKE :search OR Body LIKE :search OR
			AuthorId = ANY (SELECT UserId FROM `user` WHERE UserName LIKE :search  OR FullName LIKE :search  OR Email LIKE :search)";
		}
		else{
			$taille = Configuration::get("size_page");
			$pagination = ($page - 1)*$taille;
			$requete = " SELECT * FROM `post` WHERE Title LIKE :search OR Body LIKE :search OR
			AuthorId = ANY (SELECT UserId FROM `user` WHERE UserName LIKE :search  OR FullName LIKE :search  OR Email LIKE :search)
			LIMIT ".$taille." OFFSET ".$pagination ;
		}
		$query = self::execute( $requete, array("search" => $search));
        $data = $query->fetchAll();
        $posts = [];
        foreach ($data as $row) {
            $posts[] = new Post($row['AuthorId'], $row['Title'], $row['Body'], $row['AcceptedAnswerId'], $row['ParentId']);
        }
        return $posts;
    }
	
	public static function get_questions_newest($page) {
        $taille = Configuration::get("size_page");
		$pagination = ($page - 1)*$taille;
		$requete = "select * from Post where Title IS NOT NULL and Title <>'' order by Timestamp DESC LIMIT ".$taille." OFFSET ".$pagination ;
		$query = self::execute($requete,array());
        $data = $query->fetchAll();
        $posts = [];
        foreach ($data as $row) {
            $posts[] = new Post($row['AuthorId'], $row['Title'], $row['Body'], $row['AcceptedAnswerId'], $row['ParentId']);
        }
        return $posts;
    }
	
	public static function get_questions_votes($page) {
        $taille = Configuration::get("size_page");
		$pagination = ($page - 1)*$taille;
		$query = self::execute("SELECT post.*, max_score FROM post, ( SELECT parentid, max(score) max_score
																		FROM (SELECT post.postid, ifnull(post.parentid, post.postid) parentid, ifnull(sum(vote.updown), 0) score
																				FROM post LEFT JOIN vote ON vote.postid = post.postid
																				GROUP BY post.postid) 
																		AS tbl1
																		GROUP by parentid
																	) AS q1
								WHERE post.postid = q1.parentid
								ORDER BY q1.max_score DESC, timestamp DESC LIMIT ".$taille." OFFSET ".$pagination
								, array());
        $data = $query->fetchAll();
        $posts = [];
        foreach ($data as $row) {
            $posts[] = new Post($row['AuthorId'], $row['Title'], $row['Body'], $row['AcceptedAnswerId'], $row['ParentId']);
        }
        return $posts;
    }
	
	public static function get_questions_unanswered($page) {
        $taille = Configuration::get("size_page");
		$pagination = ($page - 1)*$taille;
		$query = self::execute("select * from Post where Title IS NOT NULL and Title <>''  and AcceptedAnswerId IS NULL order by Timestamp DESC LIMIT ".$taille." OFFSET ".$pagination, array());
        $data = $query->fetchAll();
        $posts = [];
        foreach ($data as $row) {
            $posts[] = new Post($row['AuthorId'], $row['Title'], $row['Body'], $row['AcceptedAnswerId'], $row['ParentId']);
        }
        return $posts;
    }
	
	public static function get_questions_bytag($tagId,$pagination){
		$query = self::execute("select * from post where PostId in (Select PostId from posttag where TagId=:tagId )order by Timestamp DESC", array("tagId"=>$tagId));
        $data = $query->fetchAll();
        $posts = [];
        foreach ($data as $row) {
            $posts[] = new Post($row['AuthorId'], $row['Title'], $row['Body'], $row['AcceptedAnswerId'], $row['ParentId']);
        }
        return $posts;
	}
	
	public static function get_questions_active($page){
		$taille = Configuration::get("size_page");
		$pagination = ($page - 1)*$taille;
		$query = self::execute("select question.PostId, question.AuthorId, question.Title, question.Body, question.ParentId, question.Timestamp, question.AcceptedAnswerId 
									from post as question, 
										 (select post_updates.postId, max(post_updates.timestamp) as timestamp from (
											select q.postId as postId, q.timestamp from post q where q.parentId is null
											UNION
											select a.parentId as postId, a.timestamp from post a where a.parentId is not null
											UNION
											select c.postId as postId, c.timestamp from comment c 
											UNION 
											select a.parentId as postId, c.timestamp 
											from post a, comment c 
											WHERE c.postId = a.postId and a.parentId is not null
											) as post_updates
										  group by post_updates.postId) as last_post_update
									where question.postId = last_post_update.postId and question.parentId is null
									order by last_post_update.timestamp DESC LIMIT ".$taille." OFFSET ".$pagination,
		array());
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
		if($this->is_question()){
			return Post::question_postid($this->title);
		}else{
			return Post::answer_postid($this->body);
		}
	}
	
	private static function question_postid($title){
		$query = self::execute("SELECT * FROM Post where Title = :title", array("title"=>$title));
        $data = $query->fetch(); // un seul résultat au maximum
        //var_dump($data["PostId"]);
		if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["PostId"];
        }
	}
	
	private static function answer_postid($body){
		$query = self::execute("SELECT * FROM Post where Body = :body", array("body"=>$body));
        $data = $query->fetch(); // un seul résultat au maximum
        //var_dump($data["PostId"]);
		if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["PostId"];
        }
	}
	
	public function get_answers($question){
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
	
	public function number_of_answers(){
		$query = self::execute("SELECT COUNT(*) FROM post where ParentId = :parentid", array("parentid"=>$this->get_postid()));
        $data = $query->fetchColumn();
		if($data == false){
			return 0;
		}else{
			return $data;
		}
	}
	
	public function get_author_by_authorId(){
		$query = self::execute("SELECT * FROM User where UserId = :authorid", array("authorid"=>$this->authorId));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["UserName"], $data["Password"], $data["FullName"], $data["Email"], $data["Role"]);
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
		$data = $query->fetchColumn();
		if($data == false){
			return 0;
		}else{
			return $data;
		}
		

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
		$dateThis = new DateTime($this->get_timestamp());
		$diff = $dateNow->diff($dateThis);
		$valeur = array($diff->y,$diff->m, $diff->d,$diff->h,$diff->i,$diff->s);
		$cle = array("year","month","day","hour","minute","seconde");
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
	
	public function is_question(){
		if($this->title == "" || $this->title == NULL){
			return false;
		}else{
			return true;
		}
	}
	
	public function answer_is_accepted(){
		if($this->is_question()==false){
			$answerId = $this->get_postid();
			$question = self::get_post($this->parentId);
			if($question->acceptedAnswerId == $answerId){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public function get_comments(){
		$query = self::execute("select * from comment WHERE PostId=:postid order by Timestamp DESC", array("postid"=>$this->get_postid()));
        $data = $query->fetchAll();
		$comments = [];
        foreach ($data as $row) {
            $comments[] = new Comment($row['UserId'], $row['PostId'], $row['Body']);
        }
        return $comments;
	}
	
	public function get_tags(){
		$query = self::execute("Select TagName From Tag Where TagId In (Select TagId from posttag where PostId=:postid)", 
		array("postid"=>$this->get_postid()));
        $data = $query->fetchAll();
		$tags = [];
        foreach ($data as $row) {
            $tags[] = new Tag($row['TagName']);
        }
        return $tags;
	}
	
	public static function total_questions(){
		$query = self::execute("SELECT COUNT(*) FROM post where Title IS NOT NULL and Title <>'' ", array());
        $data = $query->fetchColumn();
		if($data == false){
			return 0;
		}else{
			return $data;
		}
	}
	
	public function addTag($tagId){
		self::execute("INSERT INTO posttag(PostId,TagId) VALUES(:postid,:tagid)", 
		array("postid"=>$this->get_postid(), "tagid"=>$tagId));
		return $this;
	}
	
	public function tagNotChoosed(){
		$query = self::execute("Select * from Tag where TagId NOT IN (Select TagId from posttag where PostId=:postid)", 
		array("postid"=>$this->get_postid()));
        $data = $query->fetchAll();
		$tags = [];
        foreach ($data as $row) {
            $tags[] = new Tag($row['TagName']);
        }
        return $tags;
	}
	
}
