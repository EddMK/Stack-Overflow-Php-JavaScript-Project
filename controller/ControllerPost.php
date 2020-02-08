<?php

require_once 'model/Post.php';
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerPost extends Controller {
/*
    //accueil du controlleur.
    //gère l'affichage des messages et le post
    public function index() {
        $user = $this->get_user_or_redirect();
        $recipient = $this->get_recipient($user);
        $errors = [];
        if (isset($_POST['body'])) {
            $errors = $this->post($user, $recipient);
        }

        $messages = $recipient->get_messages();
        (new View("messages"))->show(array("recipient" => $recipient, "user" => $user, "messages" => $messages, "errors" => $errors));
    }

    //méthode outil pour poster un  message. renvoie un tableau d'erreurs 
    //eventuellement vide
    private function post($user, $recipient) {
        $errors = [];
        if (isset($_POST['body'])) {
            $body = $_POST['body'];
            $private = isset($_POST['private']) ? TRUE : FALSE;
            $message = new Message($user, $recipient, $body, $private);
            $errors = $message->validate();
            if(empty($errors)){
                $user->write_message($message);                
            }
        }
        return $errors;    
    }
*/	

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
                $post->addQuestion(); 				
            }
        }
	
		(new View("ask"))->show(array("title" => $title, "body" => $body, "errors" => $errors));
	}

}
