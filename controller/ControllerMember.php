<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerMember extends Controller {

    //page d'accueil. 
    public function index() {
        $this->profile();
    }

    //profil de l'utilisateur connecté ou donné
    public function profile() {
        $user = $this->get_user_or_redirect();
         if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $user = User::get_user_by_username($_GET["param1"]);
		 }
		 //var_dump($user);
        (new View("profile"))->show(array("user" => $user));
    }


}
