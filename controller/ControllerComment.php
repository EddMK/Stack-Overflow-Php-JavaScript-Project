<?php

require_once 'model/Post.php';
require_once 'model/User.php';

require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerComment extends Controller {

	public function index() {
				
    }
	
	public function add(){
		$user= $this->get_user_or_false();
		if($user && isset($_GET["param1"])){
			$postid = $_GET["param1"];
			$post = Post::get_post($postid);
			
			$errors =array();
			if(isset($_POST['add'])){
				$body=$_POST['add'];
				$comment = new Comment($user->get_id(),$postid,$body);
				$errors = $comment->validate();
				if(count($errors) == 0){
					$comment->addComment(); 
					$this->redirect("post","show",$postid);	
				}
			}
			(new View("comment"))->show(array("user" => $user,"post" => $post,"postid" => $postid,"errors" => $errors));
		}else{
			(new View("error"))->show(array());
		}
	}
}
