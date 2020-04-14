<?php

require_once 'model/Post.php';
require_once 'model/User.php';
require_once 'model/Vote.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerVote extends Controller {

	public function index() {
        $user = $this->get_user_or_false();
		if($user && isset($_GET["param1"])){
			$voteAncien = Vote::get_vote_by_userid_and_postid($user->get_id(),$_GET["param1"]);
			var_dump($voteAncien);
			
			if($voteAncien == false && $_POST['Genre']!= 2){
				//rajoute le vote ADD
				$vote = new Vote($user->get_id(),$_GET["param1"],$_POST['Genre']);
				$vote->addVote();
			}else{
				if($voteAncien->upDown == $_POST['Genre']){
				}else if ($_POST['Genre'] == 2){
					//DELETE
					$voteAncien->deleteVote();
				}else{
					//UPDATE
					$voteAncien->updateVote($_POST['Genre']);
				}
			}
			
			if(empty($_GET["param2"])){
				$this->redirect("post","show",$_GET["param1"],$_POST['Genre']);
			}else{
				$this->redirect("post","show",$_GET["param2"],$_POST['Genre']);
			}
		}else{
			(new View("error"))->show(array());
		}
		
    }

	
}
