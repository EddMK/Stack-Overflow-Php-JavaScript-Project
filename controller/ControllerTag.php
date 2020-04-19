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
		$user = $this->get_user_or_false();
		if($user->role === 'admin'){	
			if(isset($_POST['add'])){
				$errors =array();
				$name = $_POST['add'];
				$tag = new Tag($name);
				$errors = $tag->validate();
				if(count($errors) == 0){
					Tag::addTag($tag->tagName);
				}else{
					
				}
			}
		}
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
					$this->redirect("tag","index");
				}
			}
		}else{
		}
	}
	
	public function confirm_delete(){
	}
	
	
	
}
