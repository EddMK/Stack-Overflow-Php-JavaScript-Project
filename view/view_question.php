<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
		<script src="https://kit.fontawesome.com/9f16cf7640.js" crossorigin="anonymous"></script>
    </head>
    <body>
		<?php include('menu.html'); ?>
		<?php foreach($posts as $post){ ?>
			<div class = "Post">
					<div class ="rightSide">
						<?php if($post->is_question()==true) {?>
								<h1 class="titleQuestion"><?= $post->title ?></h1>
						<?php } else{ ?>
								<p class="body"><?= $post->body ?> </p>
						<?php } ?>
						<p class="asked">
							Asked <?= $post->get_ago() ?> by <?= $post->get_author_by_authorId()->userName ?> 
							
							<?php if($user){ ?>	
								<?php if($post->authorId == $user->get_id()) {   ?>
									<a href="post/edit/<?= $post->get_postid() ?>/"><i class="fas fa-edit"></i></a>
									<?php if($post->is_question()==true) { ?>
										<?php if($post->number_of_answers()==0){   ?>
											<a href="post/confirm_delete/<?= $post->get_postid() ?>/"><i class="fas fa-trash-alt"></i></a>
										<?php }  ?>
									<?php } else{?>	
										<?php if(count($post->get_comments())== 0){?>
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
						<?php if($post->is_question()==true){?>
							<?php foreach ($post->get_tags() as $tag){ ?>
									<a href="post/posts/tag/1/<?= $tag->get_tagId()?>" ><?= $tag->tagName?></a>
									<a href="post/takeoff_tag/<?= $tag->get_tagId()?>/<?= $post->get_postid() ?>"><i class="fas fa-trash-alt"></i></a>
							<?php } ?>
							<?php if(count($post->get_tags())<$constante){?>
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
						<?php if($post->is_question()==true) {?>
							<p class="body"><?= $post->body ?> </p>
						<?php } ?>
						<?php if(count($post->get_comments())!= 0){  ?>
							<ul>
								<?php foreach ($post->get_comments() as $comment): ?>
									<li>
										<?= $comment->body ?> - <?= $comment->get_user_by_userid()->fullName ?>  <?= $comment->get_ago() ?>
										<?php if($user){?>
											<?php if($user->get_id() == $comment->userId){?>
												<a href="comment/confirm_delete/<?= $comment->get_commentid() ?>/" ><i class="fas fa-trash-alt"></i></a>
												<a href="comment/edit/<?= $comment->get_commentid() ?>/"><i class="fas fa-edit"></i></a>
											<?php }?>
										<?php }?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php } ?>
						
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
							<a href="comment/add/<?= $post->get_postid() ?>" > Ajouter un commentaire </a>	
						</div>
					<?php }    ?>
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