<?php

require_once 'model/Post.php';
require_once 'model/User.php';

require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerPost extends Controller {

	public function index() {
		$user= $this->get_user_or_false();
		$menu="";
		$search="";
		
		if(isset($_POST['search'])){
			$search=$_POST['search'];
			
		}
		else{
			if(isset($_GET["param1"])){
				$menu = $_GET["param1"];		
			}
			else{
				$menu = "newest";
			}
		
			$posts=[];
			if($menu == "newest"){
				$posts = Post::get_questions_newest();
			}else if($menu == "votes"){
				$posts = Post::get_questions_votes();
			}else if($menu == "unanswered"){
				$posts = Post::get_questions_unanswered();
			}
		}
		
					
		 
		(new View("index"))->show(array("posts" => $posts,"user" => $user));		
    }

	public function ask(){
		$title="";
		$body="";
		$errors = [];
		$checked = false;
		$user = $this->get_user_or_redirect();
		
        if (isset($_POST['body']) && isset($_POST['title'])) {
            $body = $_POST['body'];
			$title = $_POST['title'];		
			$authorId = User::get_id_by_userName($user->userName);
            $post = new Post($authorId,$title,$body ,NULL,NULL);			
            
			$errors = $post->validate_question();
			$checked = true;
            if(count($errors) == 0){
                $post->addPost(); 				
            }
        }
		
		
		if(($checked == true) && (count($errors) == 0)){
			$reponses = null;
			$authorId = null;
			$answerAccepted = null;
			
			(new View("question"))->show(array("question" => $post,"reponses" => $reponses,"authorId" => $authorId,
				"user" => $user,"answerAccepted" => $answerAccepted ));
			
        }else{
			(new View("ask"))->show(array("title" => $title, "body" => $body,"user" => $user, "errors" => $errors));
		}
	}
	
	public function show(){
		$user = $this->get_user_or_false();
		//un if comme condition if(isset($_GET["param1"]))
		$question = Post::get_post($_GET["param1"]);
		$answerAccepted = "";
		if($question->acceptedAnswerId !== NULL){
			$answerAccepted = Post::get_post($question->acceptedAnswerId);
		}
		$authorId ="";
		if($user){
			$authorId = User::get_id_by_userName($user->userName);
		}$reponse="";
		if(isset($_POST['answer'])){
			$reponse=$_POST['answer'];
			$post = new Post($authorId,NULL,$reponse,NULL,$question->get_postid());
			$post->addPost();
		}			
		$reponses = $question->get_answers($question);
		(new View("question"))->show(array("question" => $question,"reponses" => $reponses,"authorId" => $authorId,
				"user" => $user,"answerAccepted" => $answerAccepted ));
	}
	
	
	public function accept(){
		$questionId="";
		$answerId="";
		if(isset($_POST['accepter'])){
            $questionId=$_GET["param1"];
			$answerId=$_GET["param2"];
			Post::addAccepterAnswer($questionId,$answerId);
        }
		if(isset($_POST['decliner'])){
            $questionId=$_GET["param1"];
			$answerId=NULL;
			Post::addAccepterAnswer($questionId,$answerId);
        }
		$this->redirect("post","show",$questionId);		
	}
	
	public function edit(){
		$postid = $_GET["param1"];
		$title = "";
		$body = "";
		if(isset($_POST['modifier'])){
			$title =$_POST['title'];
			$body = $_POST['body'];		
			Post::editPost($title,$body,$postid);
			if($title =="" || $title == NULL){
				$post = Post::get_post($postid);
				$this->redirect("post","show", $post->parentId);
			}else{
				$this->redirect("post","show",$postid);
			}
			$this->redirect("post","show",$questionId);
		}		
		if(isset($_POST['edit'])){
			$post = Post::get_post($_GET["param1"]);		
			$title = $post->title;
			$body = $post->body;	
            (new View("edit"))->show(array("title" => $title, "body" => $body, "postid" => $postid));
        }
	}
	
	
	public function confirm_delete(){
		$postid = $_GET["param1"];
		$post = Post::get_post($postid);
		var_dump($postid);
		if(isset($_POST['annuler'])){		
			if($post->title =="" || $post->title == NULL){//reponse OK
				$this->redirect("post","show", $post->parentId);
			}else{//question
				$this->redirect("post","index");
			}
		}
		if(isset($_POST['supprimer'])){
			Post::deletePost($postid);
			if($post->title =="" || $post->title == NULL){//reponse OK
				$this->redirect("post","show", $post->parentId);
			}else{//question
				$this->redirect("post","index");
			}
		}		
		(new View("delete"))->show(array("postid" => $postid));
	}
}
