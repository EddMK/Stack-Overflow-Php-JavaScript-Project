<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
		<script src="https://kit.fontawesome.com/9f16cf7640.js" crossorigin="anonymous"></script>
		<script type="text/javascript" src="lib/jquery-3.5.1.min.js"></script>
		<script type="text/javascript" src="lib/jquery-validation-1.19.1/jquery.validate.min.js"></script>
		<script>
				let id;
				
				$.validator.addMethod("regex", function (value, element, pattern) {
                if (pattern instanceof Array) {
                    for(p of pattern) {
                        if (!p.test(value))
                            return false;
                    }
                    return true;
                } else {
                    return pattern.test(value);
                }
				}, "Please enter a valid input.");
				
		
				$(function(){
					//$(".forms").hide();
				});
				
				
				function hide_form(a){
					$("#form"+a).remove();
					$("#input"+a).show();
					
				}
				
				
				function display_href(e,a){//rajoute
					e.preventDefault();
					
					if($("#post"+a).length)
					form = "";
					form += "<form id='form"+a+"' class='forms' method='post'>";
					form += "<table><tr><td>Comment:</td>";
					form +="<td><input id='comment"+a+"' name='comment' type='text' ></td>";
					form +="<td class='errors' id='errComment'></td></tr></table>";
					form +="<input type='submit' value='add'>";
					form +="<input type='button' value='cancel'  onclick='hide_form("+a+");' >	";
					form += "</form>";
					$("#post"+a).append(form);
					
					
					id = a;
					$("#input"+id).hide();
					$("#form"+id).show();
					$("#form"+id).validate({
						rules: {
							comment: {
								required : true,
								minlength: 1,
								regex : /^[a-zA-Z][a-zA-Z0-9]*$/,
							}
						},
						messages: {
							comment : {
								required: 'required',
								minlength: 'minimum 1 characters',
								regex: 'bad format for pseudo',
							}
						}
					});
					
					$("#form"+id).submit(function(){
						if($("#form"+id).valid()){
							var comment ;
							comment = $("#comment"+id).val();
							$.post("comment/add_comment",{postid : id, comment : comment}, function(donnees){
								$("#comment"+id).val("");
								if ($("#comments_post"+id).length == 0){
									$("#comments_post"+id).html(donnees);
								}
								$("#comments_post"+id).append(donnees);
							});
						}
						return false;
					});	
				}
		</script>
    </head>
    <body>
		<?php include('menu.html'); ?>
		<?php foreach($posts as $post){ ?>
			<div class = "Post"  id="post<?= $post->get_postid() ?>">
					<div class ="rightSide">
						<?php if($post->is_question()==true) {?>
								<h1 class="titleQuestion"><?= $post->title ?></h1>
						<?php } else{ ?>
								<p class="body">
									<?php 
										$Parsedown = new Parsedown();
										echo $Parsedown->text($post->body);
									?>
								</p>
						<?php } ?>
						<p class="asked">
							Asked <?= $post->get_ago() ?> by <?= $post->get_author_by_authorId()->userName ?> 
							<?php if($user){ ?>
								<?php if($post->authorId == $user->get_id()  || $user->role=="admin") {   ?>
									<a href="post/edit/<?= $post->get_postid() ?>/"><i class="fas fa-edit"></i></a>
									<?php if($post->is_question()==true) { ?>
										<?php if(($post->number_of_answers()==0 &&  count($post->get_comments())== 0) || $user->role=="admin" ){   ?>
											<a href="post/confirm_delete/<?= $post->get_postid() ?>/"><i class="fas fa-trash-alt"></i></a>
										<?php }  ?>
									<?php } else{?>	
										<?php if(count($post->get_comments())== 0 ){?>
											<a href="post/confirm_delete/<?= $post->get_postid() ?>/"><i class="fas fa-trash-alt"></i></a>
										<?php } ?>
										<?php if($question->authorId == $user->get_id()){ ?>
											<?php if($post->answer_is_accepted() == true){ ?>
												<form action="post/accept/<?= $id ?>/<?= $post->get_postid() ?>" method="POST">
													<button name="decliner" type="submit">
														<i class="fas fa-times"  style="font-size : 15px"></i>
													</button>
												</form>
											<?php }else{ ?>
												<form action="post/accept/<?= $id ?>/<?= $post->get_postid() ?>" method="POST">
													<button name="accepter" type="submit">
														<i class="far fa-check-circle" style="font-size : 15px" ></i>
													</button>
												</form>
											<?php } ?>	
										<?php } ?>	
									<?php } ?>
								<?php }  ?>
							<?php }  ?>
						</p>
						<?php if($post->is_question()==true ){?>
							<?php foreach ($post->get_tags() as $tag){ ?>
									<a href="post/posts/tag/1/<?= $tag->get_tagId()?>" ><?= $tag->tagName?></a>
									<?php if($user){?>  
										<?php if($post->authorId == $user->get_id()  || $user->role=="admin"){ ?>
											<a href="post/takeoff_tag/<?= $tag->get_tagId()?>/<?= $post->get_postid() ?>"><i class="fas fa-trash-alt"></i></a>
										<?php } ?>
									<?php } ?>
							<?php } ?>
							<?php if($user){?>  
								<?php if($post->authorId == $user->get_id()  || $user->role=="admin"){ ?>
									<?php if(count($post->get_tags())<$constante &&  count($post->get_tags())< $numberTagsTotal ){?>
										<form action="post/addtag/<?= $post->get_postid() ?>" method="POST">
											<select id="tag" name="tag">
												<?php foreach($post->tagNotChoosed() as $tag) { ?>
													<option value=<?= $tag->get_tagId() ?>><?= $tag->tagName ?></option>
												<?php } ?>
											</select>
											<input type="submit" value="Ajouter" />
										</form>
									<?php } ?>
								<?php } ?>
							<?php } ?>
						<?php } ?>
						<?php if($post->is_question()==true) {?>
							<p class="body">
								<?php 
									$Parsedown = new Parsedown();
									echo $Parsedown->text($post->body);
								?>
							</p>
						<?php } ?>
						<ul id="comments_post<?= $post->get_postid() ?>">
							<?php if(count($post->get_comments())!= 0){  ?>						
								<?php foreach ($post->get_comments() as $comment): ?>
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
								<?php endforeach; ?>						
							<?php } ?>
						</ul>
					</div>
					<div class ="vote">
						<?php if($user){    ?>
							<form action="vote/index/<?= $post->get_postid() ?>/<?php if($post->is_question()==false){ echo $id;} ?>" method="post">
								<?php if($post->getLastVote($user->get_id(),$post->get_postid()) == 1){ ?>	
										<button name="Genre" type="submit" value=2 >
											<i class="fas fa-thumbs-up" style="font-size : 30px"></i>
										</button>
								<?php }else{ ?>
										<button name="Genre" type="submit" value=1 >
											<i class="far fa-thumbs-up" style="font-size : 30px"></i>
										</button>
								<?php } ?>
							</form>
						<?php }  ?>	
						<p class="score"><?= $post->get_score()?> votes</p>
						<?php if($user){    ?>	
							<form action="vote/index/<?= $post->get_postid() ?>/<?php if($post->is_question()==false){ echo $id; } ?>" method="post">
								<?php if($post->getLastVote($user->get_id(),$post->get_postid()) == -1){ ?>
										<button name="Genre" type="submit" value=2>
											<i class="fas fa-thumbs-down" style="font-size : 30px"></i>
										</button>							
								<?php }else{ ?>
									<button name="Genre" type="submit" value=-1>
										<i class="far fa-thumbs-down" style="font-size : 30px; padding : 0"></i>		
									</button>							
								<?php } ?>
							</form>
						<?php }  ?>
						<?php if($post->is_question()==false){  ?>
							<?php if($post->answer_is_accepted() == true){ ?>
								<i class="fas fa-check-circle" style="font-size : 30px" ></i>
							<?php } ?>
						<?php } ?>
					</div>
					<?php if($user){ ?>	
						<div class="comment">
							<a href="comment/add/<?= $post->get_postid() ?>" id="input<?= $post->get_postid() ?>"  onclick="display_href(event, <?= $post->get_postid() ?>);" > Ajouter un commentaire </a>	
						</div>
					<?php }    ?>

<!---
					<form id="form<?= $post->get_postid() ?>" class="forms" method="post">
						<table>
								<tr>
									<td>Comment:</td>
									<td><input id="comment<?= $post->get_postid() ?>" name="comment" type="text" ></td>
									<td class="errors" id="errComment"></td>
								</tr>
						</table>
						<input type="submit" value="add">
						<input type="button" value="cancel"  onclick="hide_form(<?= $post->get_postid() ?>);" >
					</form>
-->					
					
					
					
					
					
					
					
					
					
					
				</div>
		<?php }?>
		<div class = "Repondre">
				<?php if($user){ ?>			
						<form id="repondre_form" action="post/show/<?= $id ?>" method="post">
							Votre réponse
							<textarea id="answer" name="answer" rows='3'></textarea><br>
							<input id="post" type="submit" value="Repond">
						</form>			
				<?php }    ?>
		</div>
		<div class ="Errors">
			<?php if (count($errors) != 0){ ?>
					<p>Errors :</p>
					<ul>
						<?php foreach ($errors as $error): ?>
							<li><?= $error ?></li>
						<?php endforeach; ?>
					</ul>
			<?php } ?>
		</div>
    </body>
</html>