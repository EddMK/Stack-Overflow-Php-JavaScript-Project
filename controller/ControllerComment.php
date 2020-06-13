<?php

require_once 'model/Post.php';
require_once 'model/User.php';
require_once 'model/Comment.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerComment extends Controller {

	public function index() {
		$this->redirect("post","index");
    }
	
	
	public function add_comment(){
		$user= $this->get_user_or_false();
		if(isset($_POST["comment"],$_POST["postid"]) && $user){
			$comment = $_POST["comment"];
			$postid = $_POST["postid"];
			$comment = new Comment($user->get_id(),$postid,$comment);
			$comment->addComment();
		}
		?>
		<li>
			<?php 
				$Parsedown = new Parsedown();
				echo $Parsedown->text($comment->body);
			?>
			- <?= $comment->get_user_by_userid()->fullName ?>  <?= $comment->get_ago() ?>
			<?php if($user){?>
				<?php if($user->get_id() == $comment->userId || $user->is_admin()){?>
					<a href="comment/confirm_delete/<?= $comment->get_commentid() ?>/" ><i class="fas fa-trash-alt"></i></a>
					<a href="comment/edit/<?= $comment->get_commentid() ?>/"><i class="fas fa-edit"></i></a>
				<?php }?>
			<?php }?>
		</li>
		<?php	
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
					if($post->is_question()){
						$this->redirect("post","show",$postid);
					}else{
						$this->redirect("post","show",$post->parentId);
					}		
				}
			}
			(new View("comment"))->show(array("user" => $user,"post" => $post,"postid" => $postid,"errors" => $errors));
		}else{
			(new View("error"))->show(array());
		}
	}
	
	public function confirm_delete(){
		$post = false;
		$controller = 2;
		$user= $this->get_user_or_false();
		if($user && isset($_GET["param1"])){
			$id = $_GET["param1"];
			$comment= Comment::get_comment_by_id($id);
			$post = Post::get_post($comment->postId);
			if(isset($_POST['annuler'])){	
				if($post->is_question() == false){
					$this->redirect("post","show", $post->parentId );
				}
				$this->redirect("post","show", $comment->postId);
			}
			if(isset($_POST['supprimer'])){
				$comment->delete_comment();
				if($post->is_question() == false){
					$this->redirect("post","show", $post->parentId );
				}
				$this->redirect("post","show", $comment->postId );
			}				
			(new View("delete"))->show(array("id" => $id,"user" =>$user,"controller" => $controller));
		}else{
			(new View("error"))->show(array());
		}
	}
	
	public function edit(){
		$user= $this->get_user_or_false();
		if($user && isset($_GET["param1"])){
			$id = $_GET["param1"];
			$body ="";
			$errors = array();	
			if(isset($_POST['modifier'])){
				$body = $_POST['body'];
				$postId = Comment::get_postid_by_id($id);
				$post = Post::get_post($postId);
				$comment = new Comment($user->get_id(),$postId,$body);
				$errors = $comment->validate();
				if(count($errors)==0){
					$comment->editComment($id);
					if($post->is_question()){
						$this->redirect("post","show", $postId);
					}else{
						$this->redirect("post","show", $post->parentId);
					}
					
				}else{
					(new View("editcomment"))->show(array("user"=>$user,"body"=>$body,"errors" => $errors,"id"=> $id));
				}
			}else{
				$comment = Comment::get_comment_by_id($id);
				$body = $comment->body;
				(new View("editcomment"))->show(array("user"=>$user,"body"=>$body,"errors" => $errors,"id"=> $id));
			}
		}else{
			(new View("error"))->show(array());
		}
	}
	
}
