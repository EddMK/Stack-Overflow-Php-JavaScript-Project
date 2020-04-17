<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerUser extends Controller {

    //si l'utilisateur est conecté, redirige vers son profil.
    //sinon, produit la vue d'accueil.
    public function index() {
        /*if ($this->user_logged()) {
            $this->redirect("member", "profile");
        } else {
            (new View("index"))->show();
        }*/
		
		$this->redirect("post", "index");
    }

    //gestion de la connexion d'un utilisateur
    public function login() {
        $userName = '';
        $password = '';
        $errors = [];
        if (isset($_POST['userName']) && isset($_POST['password'])) { //note : pourraient contenir des chaînes vides
            $userName = $_POST['userName'];
            $password = $_POST['password'];

            $errors = User::validate_login($userName, $password);
            if (empty($errors)) {
                $this->log_user(User::get_user_by_username($userName));
            }
        }
        (new View("login"))->show(array("userName" => $userName, "password" => $password, "errors" => $errors));
    }

    //gestion de l'inscription d'un utilisateur
    public function signup() {
        $userName = '';
        $password = '';
        $password_confirm = '';
		$fullName = '';
		$email = '';
        $errors = [];

        if (isset($_POST['userName']) && isset($_POST['password']) && isset($_POST['password_confirm']) 
			&& isset($_POST['fullName']) && isset($_POST['email'])) {
            $userName = trim($_POST['userName']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
			$fullName = $_POST['fullName'];
			$email = $_POST['email'];

            $user = new User($userName, Tools::my_hash($password),$fullName,$email,"user");
            $errors = User::validate_unicity($userName);
            $errors = array_merge($errors, $user->validate());
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));

            if (count($errors) == 0) { 
                $user->addUser();  
                $this->log_user($user);
            }
        }
		
        (new View("signup"))->show(array("userName" => $userName, "password" => $password, 
                                         "password_confirm" => $password_confirm, "fullName" => $fullName ,
										 "email" => $email,"errors" => $errors));
		
    }

}
