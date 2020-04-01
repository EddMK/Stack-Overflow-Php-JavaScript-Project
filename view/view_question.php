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
        <div class ="mainQuestion">
			<div class = "Post">
				<div class ="rightSide">
					<h1 class="titleQuestion"><?= $question->title ?></h1>
					<p class="asked">
						Asked <?= $question->get_ago() ?> by <?= $question->get_author_by_authorId()->userName ?>
						<?php if($user){ ?>
							<?php if($question->authorId == $user->get_id()) {   ?>
								<a href="post/edit/<?= $question->get_postid() ?>/"><i class="fas fa-edit"></i></a>
								<?php if(empty($reponses)){   ?>
									<a href="post/confirm_delete/<?= $question->get_postid() ?>/"><i class="fas fa-trash-alt"></i></a>
								<?php }  ?>
							<?php }  ?>
						<?php }  ?>
					</p>
					<p class="body"><?= $question->body ?> </p>
				</div>
				<div class ="vote">
					<?php if($user){    ?>
						<form action="vote/index/<?= $question->get_postid() ?>" method="POST">
							<?php if($question->getLastVote($authorId,$question->get_postid()) == 1){ ?>	
									<button name="Genre" type="submit" value=2 >
										<i class="fas fa-thumbs-up" style="font-size : 30px"></i>
									</button>
							<?php }else{ ?>
									<button name="Genre" type="submit" value=1 >
										<i class="far fa-thumbs-up" style="font-size : 30px"></i>
									</button>
							<?php } ?>
							
							<?php if($question->get_score() == NULL){ ?>
								<p class="score">0 votes</p>
							<?php }else{ ?>
								<p class="score"><?= $question->get_score()?> votes</p>
							<?php } ?>
							
							<?php if($question->getLastVote($authorId,$question->get_postid()) == -1){ ?>
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
				</div>
			</div>
			<div class="Reponses">
				<h1> <?php echo count($reponses) ?> Réponse(s)</h1>		
				<?php if($answerAccepted !== ""){ ?>
					<div class = "Post">
						<div class ="rightSide">
							<p class="body"><?= $answerAccepted->body ?></p>
							<p class="asked">
								Asked <?= $answerAccepted->get_ago() ?> by <?= $answerAccepted->get_author_by_authorId()->userName ?>
								<?php if($user){    ?>
									<?php if($answerAccepted->authorId == $user->get_id()) {   ?>
										<a href="post/edit/<?= $answerAccepted->get_answerid() ?>/<?= $answerAccepted->get_answerid() ?> "><i class="fas fa-edit"></i></a>
										<a href="post/confirm_delete/<?= $answerAccepted->get_answerid() ?>/"><i class="fas fa-trash-alt"></i></a>
									<?php }  ?>
									<?php if($question->authorId == $user->get_id()) {   ?>
										<form action="post/accept/<?= $question->get_postid() ?>/<?= $answerAccepted->get_answerid() ?>" method="POST">
											<button name="decliner" type="submit">
												<i class="fas fa-times"  style="font-size : 15px"></i>
											</button>
										</form>
									<?php }  ?>
								<?php }  ?>
							</p>
						</div>
						<div class="vote">
									<?php if($user){    ?>
										<form action="vote/index/<?= $answerAccepted->get_answerid() ?>/<?= $answerAccepted->parentId ?> " method="POST">
											<?php if($answerAccepted->getLastVote($authorId,$answerAccepted->get_answerid()) == 1){ ?>	
													<button name="Genre" type="submit" value=2 >
														<i class="fas fa-thumbs-up" style="font-size : 30px"></i>
													</button>
											<?php }else{ ?>
													<button name="Genre" type="submit" value=1 >
														<i class="far fa-thumbs-up" style="font-size : 30px"></i>
													</button>
											<?php } ?>
											
											<?php if($answerAccepted->get_score_answer() == NULL){ ?>
												<p class="score">0 votes</p>
											<?php }else{ ?>
												<p class="score"><?= $answerAccepted->get_score_answer()?> votes</p>
											<?php } ?>
											
											<?php if($answerAccepted->getLastVote($authorId,$answerAccepted->get_answerid()) == -1){ ?>
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
							</div>	
					</div>			
				<?php } ?>				
				<?php if($reponses !== null){ ?>
					<?php foreach ($reponses as $reponse){ ?>
						<div class = "Post">
								<div class ="rightSide">
									<p class="body"><?= $reponse->body ?></p>
									<p class="asked">
										Asked <?= $reponse->get_ago() ?> by <?= $reponse->get_author_by_authorId()->userName ?>
											<?php if($user){    ?>
												<?php if($reponse->authorId == $user->get_id()) {   ?>
													<a href="post/edit/<?= $reponse->get_answerid() ?>/<?= $reponse->get_answerid() ?> "><i class="fas fa-edit"></i></a>
													<a href="post/confirm_delete/<?= $reponse->get_answerid() ?>/"><i class="fas fa-trash-alt"></i></a>
												<?php }  ?>
												<?php if($question->authorId == $user->get_id()) {   ?>
													<form action="post/accept/<?= $question->get_postid() ?>/<?= $reponse->get_answerid() ?>" method="POST">
														<button name="accepter" type="submit">
															<i class="far fa-check-circle" style="font-size : 15px" ></i>
														</button>
													</form>
												<?php }  ?>
											<?php }  ?>
									</p>
								</div>
								<?php if($user){    ?>
									<div class="vote">	
										<form action="vote/index/<?= $reponse->get_answerid() ?>/<?= $reponse->parentId ?> " method="POST">
											<?php if($reponse->getLastVote($authorId,$reponse->get_answerid()) == 1){ ?>	
													<button name="Genre" type="submit" value=2 >
														<i class="fas fa-thumbs-up" style="font-size : 30px"></i>
													</button>
											<?php }else{ ?>
													<button name="Genre" type="submit" value=1 >
														<i class="far fa-thumbs-up" style="font-size : 30px"></i>
													</button>
											<?php } ?>
											
											<?php if($reponse->get_score_answer() == NULL){ ?>
												<p class="score">0 votes</p>
											<?php }else{ ?>
												<p class="score"><?= $reponse->get_score_answer()?> votes</p>
											<?php } ?>
											
											<?php if($reponse->getLastVote($authorId,$reponse->get_answerid()) == -1){ ?>
													<button name="Genre" type="submit" value=2>
														<i class="fas fa-thumbs-down" style="font-size : 30px"></i>
													</button>							
											<?php }else{ ?>
												<button name="Genre" type="submit" value=-1>
													<i class="far fa-thumbs-down" style="font-size : 30px; padding : 0"></i>		
												</button>							
											<?php } ?>
										</form>
									</div>
								<?php }  ?>	
							</div>
						<?php } ?>
					<?php } ?>
			</div>
			<div class = "Repondre">
				<?php if($user){ ?>			
						<form id="repondre_form" action="post/show/<?= $question->get_postid() ?>" method="post">
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
		</div>
    </body>
</html>