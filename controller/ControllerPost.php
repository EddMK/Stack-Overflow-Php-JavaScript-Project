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
			$reponses = array();
			$authorId = $user->get_id();
			$answerAccepted = "";
			
			(new View("question"))->show(array("question" => $post,"reponses" => $reponses,"authorId" => $authorId,
				"user" => $user,"answerAccepted" => $answerAccepted ));
				
			//$this->show();
			
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
		
		$errors =array();
		if(isset($_POST['answer'])){
			
			$reponse=$_POST['answer'];
			$post = new Post($authorId,NULL,$reponse,NULL,$question->get_postid());
			$errors = $post->validate_answer();
			if(count($errors) == 0){
                $post->addPost(); 				
            }
		}		
		
		$reponses = $question->get_answers($question);
		(new View("question"))->show(array("question" => $question,"reponses" => $reponses,"authorId" => $authorId,
				"user" => $user,"answerAccepted" => $answerAccepted, "errors" => $errors ));
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
		$user = $this->get_user_or_redirect();
		$postid = $_GET["param1"];
		$post = Post::get_post($_GET["param1"]);
		$is_question = $post->is_question();
		$title = "";
		$body = "";
		$errors= array();
		if(isset($_POST['modifier'])){		
			$body = $_POST['body'];
			$title =$_POST['title'];
			$post = new Post($postid,$title,$body ,NULL,NULL);
			if($is_question == false){	
				$errors = $post->validate_answer();
				if(count($errors) == 0){
					Post::editPost($title,$body,$postid);
					$post = Post::get_post($postid);
					$this->redirect("post","show", $post->parentId);
				}else{
					(new View("edit"))->show(array("title" => $title, "body" => $body, "postid" => $postid,"user" =>$user, "errors" =>$errors, "is_question" =>$is_question));					
				}				
			}else{
				$is_question = true;
				$errors = $post->validate_question();
				if(count($errors) == 0){
					Post::editPost($title,$body,$postid);
					$this->redirect("post","show",$postid);
				}
				else{
					(new View("edit"))->show(array("title" => $title, "body" => $body, "postid" => $postid,"user" =>$user, "errors" =>$errors, "is_question" =>$is_question));
				}
			}
		}else{	
			$post = Post::get_post($_GET["param1"]);
			$is_question = $post->is_question();
			$title = $post->title;
			$body = $post->body;	
            (new View("edit"))->show(array("title" => $title, "body" => $body, "postid" => $postid,"user" =>$user,"errors" =>$errors, "is_question" =>$is_question));
        }
	}
	
	
	public function confirm_delete(){
		$user = $this->get_user_or_redirect();
		$postid = $_GET["param1"];
		$post = Post::get_post($postid);
		// var_dump($postid);
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
		(new View("delete"))->show(array("postid" => $postid,"user" =>$user));
	}
}
