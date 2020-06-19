<?php

require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/Post.php';
require_once 'model/User.php';
require_once 'model/Tag.php';



class ControllerPost extends Controller {

	public function index() {
		$user= $this->get_user_or_false();
		$constante = Configuration::get("size_page");
		$numberQuestions =Post::total_questions();//servais a rien => count(post[])
		$totalPages = $numberQuestions  / $constante;
		if(is_float($totalPages)){
			$totalPages =(int) ($totalPages+1);
		}
		$currentPage;
		$tagName = "";
		$menu="";
		$search="";
		$posts = array();
		if(isset($_POST['search'])){
			$menu = "search";
			$search = $_POST['search'];
			if(!(strlen(trim($search)) == 0)){
				$this->redirect("post","index",$menu,1,$search);
			}
		}else{
			if(isset($_GET["param1"])){
				$menu = $_GET["param1"];		
			}
			else{
				$menu = "newest";
			}
			if(isset($_GET["param2"])){
				$currentPage = $_GET["param2"];		
			}
			else{
				$currentPage = 1;
			}
			if($menu == "newest"){
				$posts = Post::get_questions_newest($currentPage);
			}else if($menu == "votes"){
				$posts = Post::get_questions_votes($currentPage);
			}else if($menu == "unanswered"){
				$posts = Post::get_questions_unanswered($currentPage);
			}else if($menu == "active"){
				$posts = Post::get_questions_active($currentPage);
			}else if($menu == "search"){
				$search = $_GET["param3"];
				$Search = "%".$_GET["param3"]."%";
				$posts = Post::get_searchs($Search,$currentPage);
				$numberQuestions = count(Post::get_searchs($Search, null));
				$totalPages = $numberQuestions  / $constante;
				if(is_float($totalPages)){
					$totalPages =(int) ($totalPages+1);
				}
			}
		}
		(new View("index"))->show(array("posts" => $posts,"user" => $user, "search" => $search, "menu" => $menu,
		"tagName" =>$tagName, "totalPages"=>$totalPages, "currentPage" => $currentPage ));		
    }

	public function ask(){
		$title="";
		$body="";
		$errors = [];
		$checked = false;
		$user = $this->get_user_or_false();
		$tags = Tag::get_tags();
		$constante = Configuration::get("max_tags");
		if($user){
			$choix= array();
			if (isset($_POST['body']) && isset($_POST['title'])){
				$body = $_POST['body'];
				$title = $_POST['title'];
				if(isset($_POST['choix'])){
					$choix=$_POST['choix'];
				}
				$authorId = User::get_id_by_userName($user->userName);
				$post = new Post($authorId,$title,$body ,NULL,NULL);			
				
				$errors = $post->validate_question();
				if(count($choix)>$constante){
					$errorTag = "the number of tags must not be more than 5";//change 5
					array_push($errors,$errorTag);
				}
				$checked = true;
				if(count($errors) == 0){
					$post->addPost();	
				}
			}		
			if(($checked == true) && (count($errors) == 0)){
				$postid = $post->get_postid();
				if(!empty($choix)){
					foreach($choix as $key => $val){
						$post->addTag($val);
						}
				}
				$this->redirect("post","show",$postid);				
			}else{
				(new View("ask"))->show(array("title" => $title, "body" => $body,"user" => $user, "errors" => $errors, "tags"=>$tags));
			}
		}else{
			(new View("error"))->show(array());
		}
		
		
	}
	
	public function show(){
		$user = "";
		$id = "";
		$question = "";
		$answerAccepted ="";
		$constante = Configuration::get("max_tags");
		$numberTagsTotal = count(Tag::get_tags());
		if(isset($_GET["param1"])){
			$id = $_GET["param1"];
			$user = $this->get_user_or_false();
			$question = Post::get_post($id);
			if($question->is_question()){
				$reponse="";
				$authorId ="";
				$begin = array($question);
				if($question->acceptedAnswerId !== NULL){
					$answerAccepted = Post::get_post($question->acceptedAnswerId);
					array_push($begin,$answerAccepted);
				}
				$errors =array();
				if(isset($_POST['answer'])){
					$reponse=$_POST['answer'];
					$post = new Post($user->get_id(),"",$reponse,NULL,$question->get_postid());
					$errors = $post->validate_answer();
					if(count($errors) == 0){
						$post->addPost(); 	
						$this->redirect("post","show",$id);	
						
					}
				}		
				$reponses = $question->get_answers($question);
				$posts = array_merge($begin,$reponses);
				(new View("question"))->show(array("question" => $question,"user" => $user, "errors" => $errors , "posts" =>$posts, "id" =>$id
				, "constante" => $constante, "numberTagsTotal" => $numberTagsTotal ));	
			}else{
				(new View("error"))->show(array());
			}
		}else{
			(new View("error"))->show(array());
		}
	}
	
	
	public function accept(){
		$questionId="";
		$answerId="";
		if(isset($_GET["param1"])){
			$questionId=$_GET["param1"];
			if(isset($_POST['decliner'])){
				$answerId=NULL;
				Post::addAccepterAnswer($questionId,$answerId);
			}
			if(isset($_GET["param2"])){
				$answerId=$_GET["param2"];
				if(isset($_POST['accepter'])){
					Post::addAccepterAnswer($questionId,$answerId);
				}	
			}else{
				(new View("error"))->show(array());
			}
			$this->redirect("post","show",$questionId);	
		}else{
			(new View("error"))->show(array());
		}
	}
	
	public function edit(){
		if(isset($_GET["param1"])){
			$user = $this->get_user_or_redirect();
			$postid = $_GET["param1"];
			$post = Post::get_post($postid);
			$is_question = $post->is_question();
			$title = "";
			$body = "";
			$errors= array();
			if(isset($_POST['modifier'])){		
				$body = $_POST['body'];
				if(isset($_POST['title'])){	
					$title =$_POST['title'];
				}
				$post = new Post($postid,$title,$body ,NULL,NULL);
				if($is_question == false){	
					$errors = $post->validate_answer();
					if(count($errors) == 0){
						Post::editPost($title,$body,$postid);
						$post = Post::get_post($postid);
						$this->redirect("post","show", $post->parentId);
					}else{
						(new View("edit"))->show(array("title" => $title, "body" => $body, "postid" => $postid,"user" =>$user, "errors" =>$errors, "is_question" =>$is_question));					
					}				
				}else{
					$is_question = true;
					$errors = $post->validate_question();
					if(count($errors) == 0){
						Post::editPost($title,$body,$postid);
						$this->redirect("post","show",$postid);
					}
					else{
						(new View("edit"))->show(array("title" => $title, "body" => $body, "postid" => $postid,"user" =>$user, "errors" =>$errors, "is_question" =>$is_question));
					}
				}
			}else{	
				$post = Post::get_post($_GET["param1"]);
				$is_question = $post->is_question();
				$title = $post->title;
				$body = $post->body;	
				(new View("edit"))->show(array("title" => $title, "body" => $body, "postid" => $postid,"user" =>$user,"errors" =>$errors, "is_question" =>$is_question));
			}
		}else{
			(new View("error"))->show(array());
		}
	}
	
	
	public function confirm_delete(){
		$controller = 1;
		$user = $this->get_user_or_false();
		if(isset($_GET["param1"]) && $user){		
			$id = $_GET["param1"];
			$post = Post::get_post($id);
			if(isset($_POST['annuler'])){		
				if($post->title =="" || $post->title == NULL){//reponse OK
					$this->redirect("post","show", $post->parentId);
				}else{//question
					$this->redirect("post","index");
				}
			}
			if(isset($_POST['supprimer'])){
				if($post->title =="" || $post->title == NULL){//reponse OK
					if($post->answer_is_accepted()){
						Post::addAccepterAnswer($post->parentId,NULL);
					}
					Post::deletePost($id);
					$this->redirect("post","show", $post->parentId);
				}else{//question
					if($post->number_of_answers()!=0){
						if($post->acceptedAnswerId !== NULL){
							$acceptedAnswer = Post::get_post($post->acceptedAnswerId);
							Post::deletePost($acceptedAnswer->get_postid());
						}
						foreach($post->get_answers($post) as $answer){
							Post::deletePost($answer->get_postid());
						}
					}
					if(count($post->get_comments())!= 0){
						foreach ($post->get_comments() as $comment){
							$comment->delete_comment();
						}
					}
					Post::deletePost($id);
					$this->redirect("post","index");
				}
			}		
			(new View("delete"))->show(array("id" => $id,"user" =>$user,"controller" => $controller));
		}else{
			(new View("error"))->show(array());
		}
	}
	
	
	public function posts(){
		$user=$this->get_user_or_false();
		$search='';
		$posts = array();
		$error = "URL Error";
		$menu;
		if(isset($_GET['param1']) && $_GET['param1']=='tag'){
			$tag = $_GET['param1'];
			$menu = $tag;
			if(isset($_GET['param2']) && isset($_GET['param3'])){
				$tagId = $_GET['param3'];
				$currentPage = $_GET['param2'];
				$tag = Tag::get_tag($tagId);			
				$numberQuestions = count(Post::get_questions_bytag($tagId,0));
				$totalPages = $numberQuestions  / 5;
				if(is_float($totalPages)){
					$totalPages =(int) ($totalPages+1);
				}
				$posts = Post::get_questions_bytag($tagId,$currentPage);
				(new View("index"))->show(array("posts" => $posts,"user" => $user, "search" => $search, "menu" => $menu,"tag" =>$tag, "totalPages"=>$totalPages, "currentPage" => $currentPage));
			}else{
				(new View("error"))->show(array("user" => $user,"error" => $error));
			}
		}else{
			(new View("error"))->show(array("user" => $user,"error" => $error));
		}	
	}
	
	public function takeoff_tag(){
		$tagId;
		$postId;
		$user=$this->get_user_or_false();
		if(isset($_GET['param1']) && isset($_GET['param2'])){
			$tagId = $_GET['param1'];
			$postId = $_GET['param2'];
			Tag::takeoff($tagId,$postId);
			$this->redirect("post","show", $postId);
		}else{
			(new View("error"))->show(array("user" => $user,"error" => "URL Error"));
		}
	}
	
	public function addTag(){
		$tagId;
		$postId;
		$user=$this->get_user_or_false();
		if(isset($_GET['param1'])){
			$postId = $_GET['param1'];
			$post = Post::get_post($postId);
			if(isset($_POST['tag'])){
				$tagId= $_POST['tag'];
				$post->addTag($tagId);
				$this->redirect("post","show", $postId);
			}
		}else{
			(new View("error"))->show(array("user" => $user,"error" => "URL Error"));
		}
	}
	
	public function statistique(){
		$user = $this->get_user_or_false();
		(new View("stat"))->show(array("user" => $user));
	}
	
	public function graph(){
		$tableau=array();
		if(isset($_POST['dateLimit'])){
			$nbr = Configuration::get("N");
			$date = $_POST['dateLimit'];
			$tableau = Post::totalActions($date,$nbr);
			$newTableau = array();			
			$label = array();
			$datas = array();
			foreach($tableau as $elem){
				$label[] = $elem['UserName'];
				$datas[] = $elem['totalactions'];
			}
			$newTableau["users"] = $label;
			$newTableau["values"] = $datas;
			echo json_encode($newTableau);
		}
	}
	//moment type question
	public function actions(){
		$tableau=array();
		if(isset($_POST['dateLimit'],$_POST['pseudo_choisi'])){//pseudo_choisi
			$date = $_POST['dateLimit'];
			$pseudo = $_POST['pseudo_choisi'];
			$tableau= Post::getActions($date,$pseudo);
			$newTableau = array();
			//$dateNow =  date_create();			
			foreach($tableau as $elem){
				$a=array();
				$a["ago"] = $this::get_ago($elem["moment"]);
				$a["moment"] = $elem["moment"];//refaire les dates
				$a["titre"]=$elem["titre"];
				$a["type"]=$elem["type"];
				$newTableau[]=$a;
			}
			echo json_encode($newTableau);
		}
	}
	
	public static function get_ago($date){
		$dateNow =  date_create();
		$dateThis = new DateTime($date);
		$diff = $dateNow->diff($dateThis);
		$valeur = array($diff->y,$diff->m, $diff->d,$diff->h,$diff->i,$diff->s);
		$cle = array("year","month","day","hour","minute","seconde");
		$i = 0;
		while($i<count($valeur) && $valeur[$i]==0){
			$i ++;
		}
		if($i == 6){
			return '0 secondes';
		}else{
			return $valeur[$i].' '.$cle[$i].' ago';
		}
	}
	
	
	public function newest(){
		if(isset($_POST['page'])){
			$page = $_POST['page'];
			$posts = Post::get_questions_newest($page);
			$newTableau = array();
			foreach($posts as $post){
				$a=array();
				$a["postid"] = $post->get_postid();
				$a["titre"]= $post->title;
				$Parsedown = new Parsedown();
				$a["body"]= $Parsedown->text($post->body);
				$a["ago"]= $post->get_ago();
				$a["fullname"]= $post->get_author_by_authorId()->fullName;
				$a["score"]= $post->get_score();
				$a["answers"]= $post->number_of_answers();
				$b = array();
				foreach ($post->get_tags() as $tag){ 
					$c = array();
					$c["tagid"] = $tag->get_tagId();
					$c["tagname"] = $tag->tagName;	
					$b[]=$c;
				}
				$a["tags"] = $b;
				$newTableau[]=$a;
			}
			echo json_encode($newTableau);
		}
	}
	
	public function votes(){
		if(isset($_POST['page'])){
			$page = $_POST['page'];
			$posts = Post::get_questions_votes($page);
			$newTableau = array();
			foreach($posts as $post){
				$a=array();
				$a["postid"] = $post->get_postid();
				$a["titre"]= $post->title;
				$Parsedown = new Parsedown();
				$a["body"]= $Parsedown->text($post->body);
				$a["ago"]= $post->get_ago();
				$a["fullname"]= $post->get_author_by_authorId()->fullName;
				$a["score"]= $post->get_score();
				$a["answers"]= $post->number_of_answers();
				$b = array();
				foreach ($post->get_tags() as $tag){ 
					$c = array();
					$c["tagid"] = $tag->get_tagId();
					$c["tagname"] = $tag->tagName;	
					$b[]=$c;
				}
				$a["tags"] = $b;
				$newTableau[]=$a;
			}
			echo json_encode($newTableau);
		}
	}
	
	public function unanswered(){
		if(isset($_POST['page'])){
			$page = $_POST['page'];
			$posts = Post::get_questions_unanswered($page);
			$newTableau = array();
			foreach($posts as $post){
				$a=array();
				$a["postid"] = $post->get_postid();
				$a["titre"]= $post->title;
				$Parsedown = new Parsedown();
				$a["body"]= $Parsedown->text($post->body);
				$a["ago"]= $post->get_ago();
				$a["fullname"]= $post->get_author_by_authorId()->fullName;
				$a["score"]= $post->get_score();
				$a["answers"]= $post->number_of_answers();
				$b = array();
				foreach ($post->get_tags() as $tag){ 
					$c = array();
					$c["tagid"] = $tag->get_tagId();
					$c["tagname"] = $tag->tagName;	
					$b[]=$c;
				}
				$a["tags"] = $b;
				$newTableau[]=$a;
			}
			echo json_encode($newTableau);
		}
	}
	
	public function active(){
		if(isset($_POST['page'])){
			$page = $_POST['page'];
			$posts = Post::get_questions_active($page);
			$newTableau = array();
			foreach($posts as $post){
				$a=array();
				$a["postid"] = $post->get_postid();
				$a["titre"]= $post->title;
				$Parsedown = new Parsedown();
				$a["body"]= $Parsedown->text($post->body);
				$a["ago"]= $post->get_ago();
				$a["fullname"]= $post->get_author_by_authorId()->fullName;
				$a["score"]= $post->get_score();
				$a["answers"]= $post->number_of_answers();
				$b = array();
				foreach ($post->get_tags() as $tag){ 
					$c = array();
					$c["tagid"] = $tag->get_tagId();
					$c["tagname"] = $tag->tagName;	
					$b[]=$c;
				}
				$a["tags"] = $b;
				$newTableau[]=$a;
			}
			echo json_encode($newTableau);
		}
	}
	
	
	
	public function number(){
		$numberQuestions =Post::total_questions();
		echo $numberQuestions;
	}
	
	public function search(){
		if(isset($_POST['page'],$_POST['search'])){
			$page = $_POST['page'];
			$search = "%".$_POST['search']."%";
			$posts = Post:: get_searchs($search, $page);
			$newTableau = array();
			foreach($posts as $post){
				$a=array();
				$a["postid"] = $post->get_postid();
				$a["titre"]= $post->title;
				$Parsedown = new Parsedown();
				$a["body"]= $Parsedown->text($post->body);
				$a["ago"]= $post->get_ago();
				$a["fullname"]= $post->get_author_by_authorId()->fullName;
				$a["score"]= $post->get_score();
				$a["answers"]= $post->number_of_answers();
				$b = array();
				foreach ($post->get_tags() as $tag){ 
					$c = array();
					$c["tagid"] = $tag->get_tagId();
					$c["tagname"] = $tag->tagName;	
					$b[]=$c;
				}
				$a["tags"] = $b;
				$newTableau[]=$a;
			}
			echo json_encode($newTableau);
		}
		
	}
	
	public function numbersearchs(){
		if(isset($_POST['search'])){
			$search = "%".$_POST['search']."%";
			$numberQuestions = count(Post::get_searchs($search, null));
			
			echo $numberQuestions;
		}
	}
	
	public function tag(){
		if(isset($_POST['page'],$_POST['type'])){
			$page = $_POST['page'];
			$tagId = $_POST['type'];
			$posts = Post:: get_questions_bytag($tagId,$page);
			$newTableau = array();
			foreach($posts as $post){
				$a=array();
				$a["postid"] = $post->get_postid();
				$a["titre"]= $post->title;
				$Parsedown = new Parsedown();
				$a["body"]= $Parsedown->text($post->body);
				$a["ago"]= $post->get_ago();
				$a["fullname"]= $post->get_author_by_authorId()->fullName;
				$a["score"]= $post->get_score();
				$a["answers"]= $post->number_of_answers();
				$b = array();
				foreach ($post->get_tags() as $tag){ 
					$c = array();
					$c["tagid"] = $tag->get_tagId();
					$c["tagname"] = $tag->tagName;	
					$b[]=$c;
				}
				$a["tags"] = $b;
				$newTableau[]=$a;
			}
			echo json_encode($newTableau);
		}
	}
	
	
	public function numbertags(){
		if(isset($_POST['tagid'])){
			$tagId = $_POST['tagid'];
			$posts = Post:: get_questions_bytag($tagId,0);
			$number = count($posts);
			echo $number ;
		}
	}
	
	public function getsizepage(){
		$constante = Configuration::get("size_page");
		echo $constante;
	}
	
	
}
