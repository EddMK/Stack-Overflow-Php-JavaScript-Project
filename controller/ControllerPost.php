<?php

require_once 'model/Post.php';
require_once 'model/User.php';
require_once 'model/User.php';

require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerPost extends Controller {

	public function index() {
        //(new View("ask"))->show(array("title" => $title, "body" => $body));
    }

	public function ask(){
		$title="";
		$body="";
		$errors="";
		
        if (isset($_POST['body']) && isset($_POST['title'])) {
            $body = $_POST['body'];
			$title = $_POST['title'];
			$user = $this->get_user_or_redirect();
			$authorId = User::get_id_by_userName($user->userName);
            $post = new Post($authorId,$title,$body ,NULL,NULL);
            $errors = $post->validate();
            if($errors==""){
                $post->addPost(); 				
            }
        }
	
		(new View("ask"))->show(array("title" => $title, "body" => $body, "errors" => $errors));
	}
	
	public function show(){
		$user = $this->get_user_or_redirect();
		$authorId = User::get_id_by_userName($user->userName);
		if(isset($_GET["param1"])){
			$reponse="";
			$question = Post::get_post($_GET["param1"]);
			if(isset($_POST['answer'])){
				$reponse=$_POST['answer'];
				$post = new Post($authorId,NULL,$reponse,NULL,$question->get_postid());
				$post->addPost();
			}		
			$reponses = $question->get_answers($question);
			var_dump($reponses);
			$error = "";
			(new View("question"))->show(array("question" => $question,"reponses" => $reponses,"authorId" => $authorId, "error" => $error));
		}
		else{		
			$posts=[];			
			$posts = Post::get_questions($user); 
			(new View("show"))->show(array("posts" => $posts));
		}
	}

}
