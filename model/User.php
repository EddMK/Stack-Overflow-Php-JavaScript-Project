<?php

require_once "framework/Model.php";
require_once "Post.php";

class User extends Model {

    public $userName;
    public $hashed_password;
    public $fullName;
	public $email;
	public $role;

    public function __construct($userName ,$hashed_password, $fullName, $email, $role) {
		$this->userName = $userName;
        $this->hashed_password = $hashed_password;
        $this->fullName = $fullName;
        $this->email = $email;
		$this->role = User::role_of_user($this->get_id());
    }
	
	public static function init(){
		$query = self::execute("SELECT * FROM User", array());
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["UserName"], $data["Password"], $data["FullName"], $data["Email"], $data["Role"]);
        }
	}
	
	
	
	public static function validate_unicity($userName){
        $errors = [];
        $user = self::get_user_by_username($userName);
        if ($user) {
            $errors[] = "This user already exists.";
        } 
        return $errors;
    }
	
	public static function get_user_by_username($userName) {
        $query = self::execute("SELECT * FROM User where UserName = :userName", array("userName"=>$userName));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["UserName"], $data["Password"], $data["FullName"], $data["Email"], $data["Role"]);
        }
    }
	
	
	public static function get_id_by_userName($userName) {
        $query = self::execute("SELECT * FROM User where UserName = :userName", array("userName"=>$userName));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["UserId"];
        }
    }
	
	public function get_user_by_id($userid){
		$query = self::execute("SELECT * FROM User where UserId = :userid", array("userid"=>$userid));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["UserName"], $data["Password"], $data["FullName"], $data["Email"], $data["Role"]);
        }
	}
	
	public function get_id() {
        $query = self::execute("SELECT * FROM User where UserName = :userName", array("userName"=>$this->userName));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["UserId"];
        }
    }
	
	//renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    //ne s'occupe que de la validation "métier" des champs obligatoires (le pseudo)
    //les autres champs (mot de passe, description et image) sont gérés par d'autres
    //méthodes.
    public function validate(){
        $errors = array();
        if (!(isset($this->userName) && is_string($this->userName) && strlen($this->userName) > 0)) {
            $errors[] = "User Name is required.";
        } if (!(isset($this->userName) && is_string($this->userName) && strlen($this->userName) >= 3)) {
            $errors[] = "User Name length must be more than 3.";
        } /*if (!(isset($this->userName) && is_string($this->userName) && preg_match("/[a-zA-Z]*$/", $this->userName))) {
            $errors[] = "User Name must start by a letter and must contain only letters.";
        }*/
		
		if (!(isset($this->fullName) && is_string($this->fullName) && strlen($this->fullName) > 0)) {
            $errors[] = "Full Name is required.";
        } if (!(isset($this->fullName) && is_string($this->fullName) && strlen($this->fullName) >= 3)) {
            $errors[] = "Full Name length must be more than 3.";
        } if (!(isset($this->fullName) && is_string($this->fullName) && preg_match("/^[A-Z][a-zA-Z\s-]*$/", $this->fullName))) {
            $errors[] = "Full Name must start by a uppercase letter and must contain only letters.";
        }
		
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			$errors[] = "Invalid email format"; 
		}
        return $errors;
    }
	
	public static function validate_passwords($password, $password_confirm){
        $errors = User::validate_password($password);
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }
	
	private static function validate_password($password){
        $errors = [];
        if (strlen($password) < 8 ) {
            $errors[] = "Password length must be more than 8.";
        } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }
	
	
	public function addUser(){
		//var_dump($this);
        self::execute("INSERT INTO user(UserName,Password,FullName,Email) VALUES(:userName,:password,:fullName,:email)", 
		array("userName"=>$this->userName, "password"=>$this->hashed_password, "fullName"=>$this->fullName, "email"=>$this->email));
		return $this;
    }
	
	
	////////////
	////////////
	////////////
	////////////
	////////////
	////////////
	////////////
	
	
	
	//renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    public static function validate_login($userName, $password) {
        $errors = [];
        $user = User::get_user_by_username($userName);
        if ($user) {
            if (!self::check_password($password, $user->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else { 
            $errors[] = "Can't find a member with the pseudo '$pseudo'. Please sign up.";
        }
        return $errors;
    }
	
	private static function check_password($clear_password, $hash) {
        return $hash === Tools::my_hash($clear_password);
    }
	
	
	public function is_admin(){
		$role = User::role_of_user($this->get_id());
		//var_dump($role);
		if($role == 'admin'){
			return true;
		}else{
			return false;
		}
	}
	
	private static function role_of_user($id){
		$query = self::execute("SELECT * FROM User where UserId = :userId", array("userId"=>$id));
        $data = $query->fetch(); // un seul résultat au maximum
        //var_dump($data["Role"]);
		if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data["Role"];
        }
	}
	
	
	
	
	
	
	
}
?>