<?php

require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/Post.php';
require_once 'model/User.php';
require_once 'model/Comment.php';
require_once 'model/Tag.php';


class ControllerTag extends Controller {

	public function index(){
		$user= $this->get_user_or_false();
		$tags = Tag::get_tags();
		$errors = null;
		(new View("tag"))->show(array("user" => $user, "tags" => $tags, "errors" => $errors));
    }
	
	public function add(){
		
	}
	
	public function edit(){
		$user = $this->get_user_or_false();
		if($user->role === 'admin'){
			if(isset($_GET['param1'])){
				$id = $_GET['param1'];
				if(isset($_POST['edit'])){
					$name = $_POST['edit'];
					$newTag = new Tag($name);
					$errors = $newTag->validate();
					var_dump($errors);
					if(count($errors) == 0){
						Tag::editTag($name,$id);
					}
					
				}
				$this->redirect("tag","index");
			}else{
				(new View("error"))->show(array());
			}
		}else{
			(new View("error"))->show(array());
		}
	}
	
	public function confirm_delete(){
		$controller = 3;
		$user = $this->get_user_or_false();
		if($user->role === 'admin'){
			if(isset($_GET['param1'])){
				$id = $_GET['param1'];
				$tag= Tag::get_tag($id);
				if(isset($_POST['annuler'])){
					$this->redirect("tag","index");
				}
				if(isset($_POST['supprimer'])){
					$tag->delete_tag();
					$this->redirect("tag","index");
				}
				(new View("delete"))->show(array("id" => $id,"user" =>$user,"controller" => $controller));
			}else{
				(new View("error"))->show(array());
			}
		}else{
			(new View("error"))->show(array());
		}
	}
	
	
	
}
