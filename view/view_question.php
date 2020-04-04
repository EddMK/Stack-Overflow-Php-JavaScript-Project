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
		<div class="header">
			<a href="post/index" class="logo">Stuck Overflow</a>
			<div class = "headerRight">
				<?php if(empty($user)){    ?>
					<a href="user/login"><i class="fas fa-sign-in-alt"  style="font-size:40px;color:black"></i></a>
					<a href="user/signup"><i class="fas fa-user-plus"  style="font-size:40px;color:black;padding-left:20px"></i></a>				
				<?php } else{    ?>
					<div class="textHeader">
						<a href="post/ask"  style="text-decoration: none;color:black">Ask a question</a>
						<i class="fas fa-user"  style="font-size:40px;color:black;padding-left:20px"></i><?= $user->userName ?>
						<a href="user/logout"><i class="fas fa-sign-out-alt" style="font-size:40px;color:black;padding-left:20px"></i></a>
					</div>
				<?php }    ?>
			</div>
		</div>
		<?php  var_dump($posts)   ?>
		
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
										<?php if($post->number_of_answers()!=0){   ?>
											<a href="post/confirm_delete/<?= $post->get_postid() ?>/"><i class="fas fa-trash-alt"></i></a>
										<?php }  ?>
									<?php } else{?>	
										<a href="post/confirm_delete/<?= $post->get_postid() ?>/"><i class="fas fa-trash-alt"></i></a>
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
						<?php if($post->is_question()==true) {?>
							<p class="body"><?= $post->body ?> </p>
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