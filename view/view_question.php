<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
		<div class = "Question">
			<h1>Question</h1>
			<h2>Title :</h2>
			<p><?= $question->title ?></p>
			<h2>Body :</h2>
			<?= $question->body ?>
			<h3>Written by :</h3>
			<p><?= $question->get_author_by_authorId()->userName ?></p>
			<h3>Scores :</h3>
			<?php if($question->get_score() == NULL){ ?>
				<p>0</p>
			<?php }else{ ?>
			<p><?= $question->get_score() ?></p>
			<?php } ?>
			<h3>Date :</h3>
			<p><?= $question->get_ago() ?></p>
			<?php if($user){    ?>
				<h3>Vote :</h3>
				<form action="vote/index/<?= $question->get_postid() ?>" method="POST">
						<input type="radio" name="Genre" value=1 <?php if($question->getLastVote($authorId,$question->get_postid()) == 1){ echo "checked";} ?> > +1<br>
						<input type="radio" name="Genre" value=2> 0<br>
						<input type="radio" name="Genre" value=-1 <?php if($question->getLastVote($authorId,$question->get_postid()) == -1){ echo "checked";} ?>> -1<br>
						<input type="submit" value="Envoyer">
				</form>
				<?php if($question->authorId == $user->get_id()) {   ?>
					<form action="post/edit/<?= $question->get_postid() ?>/" method="POST">
						<input type="submit" name="edit" value="edit">
					</form>
					<?php if(empty($reponses)){   ?>
						<form action="post/confirm_delete/<?= $question->get_postid() ?>/" method="POST">
							<input type="submit" name="delete" value="delete">
						</form>
					<?php }  ?>
				<?php }  ?>
			<?php }  ?>
			<p>___________________________________________________________</p>
		</div>
		<h1>Réponse(s)</h1>
		<div class = "Reponse_favorite">		
			<?php if($answerAccepted !== ""){ ?>
				<h2>Réponse acceptée</h2>
				<p><?= $answerAccepted->body ?></p>
					<h3>Written by </h3>
					<p><?= $answerAccepted->get_author_by_authorId()->userName ?></p>
					<h3>Scores :</h3>
						<?php if($answerAccepted->get_score_answer() == NULL){ ?>
							<p>0</p>
						<?php }else{ ?>
							<p><?= $answerAccepted->get_score_answer() ?></p>
						<?php } ?>
					<h3>Date :</h3>
					<p><?= $answerAccepted->get_ago() ?></p>
					<p><?php var_dump($answerAccepted->get_answerid())?></p>
					<?php if($user){    ?>
						<h3>Vote :</h3>
						<form action="vote/index/<?= $answerAccepted->get_answerid() ?>/<?= $answerAccepted->parentId ?> " method="POST">
							<input type="radio" name="Genre" value=1 <?php if($answerAccepted->getLastVote($authorId,$answerAccepted->get_answerid()) == 1){ echo "checked";} ?> > +1<br>
							<input type="radio" name="Genre" value=2> 0<br>
							<input type="radio" name="Genre" value=-1 <?php if($answerAccepted->getLastVote($authorId,$answerAccepted->get_answerid()) == -1){ echo "checked";} ?>> -1<br>
							<input type="submit" value="Envoyer">
						</form>
					<?php }  ?>
					<?php if($user){   ?>
						<?php if($answerAccepted->authorId == $user->get_id()) {   ?>
							<form action="post/accept/<?= $question->get_postid() ?>/<?= $answerAccepted->get_answerid() ?>" method="POST">
								<input type="submit" name="decliner" value="decliner">
							</form>
						<?php }  ?>					
						<?php if($answerAccepted->authorId == $user->get_id()) {   ?>
							<form action="post/edit/<?= $answerAccepted->get_answerid() ?>/" method="POST">
								<input type="submit" name="edit" value="edit">
							</form>
							<form action="post/confirm_delete/<?= $answerAccepted->get_answerid() ?>/" method="POST">
								<input type="submit" name="delete" value="delete">
							</form>
						<?php }  ?>
					<?php }  ?>
				<p>___________________________________________________________</p>
			<?php } ?>
		</div>
		<div class = "Reponses">			
			<?php foreach ($reponses as $reponse){ ?>
					<h2>Réponse</h2>
					<p><?= $reponse->body ?></p>
					<h3>Written by</h3> 
					<p><?= $reponse->get_author_by_authorId()->userName ?></p>
					<h3>Scores :</h3>
						<?php if($reponse->get_score_answer() == NULL){ ?>
							<p>0</p>
						<?php }else{ ?>
							<p><?= $reponse->get_score_answer() ?></p>
						<?php } ?>
					<h3>Date :</h3>
					<p><?= $reponse->get_ago() ?></p>
					<p><?php var_dump($reponse->get_answerid())?></p>
					<?php if($user){    ?>
						<h3>Vote :</h3>
						<form action="vote/index/<?= $reponse->get_answerid() ?>/<?= $reponse->parentId ?> " method="POST">
							<input type="radio" name="Genre" value=1 <?php if($reponse->getLastVote($authorId,$reponse->get_answerid()) == 1){ echo "checked";} ?> > +1<br>
							<input type="radio" name="Genre" value=2> 0<br>
							<input type="radio" name="Genre" value=-1 <?php if($reponse->getLastVote($authorId,$reponse->get_answerid()) == -1){ echo "checked";} ?>> -1<br>
							<input type="submit" value="Envoyer">
						</form>
						<?php if($question->authorId == $user->get_id()) {   ?>
							<form action="post/accept/<?= $question->get_postid() ?>/<?= $reponse->get_answerid() ?>" method="POST">
								<input type="submit" name="accepter" value="accepter">
							</form>
						<?php }  ?>				
						<?php if($reponse->authorId == $user->get_id()) {   ?>
							<form action="post/edit/<?= $reponse->get_answerid() ?>/<?= $reponse->get_answerid() ?> " method="POST">
								<input type="submit" name="edit" value="edit">
							</form>
							<form action="post/confirm_delete/<?= $reponse->get_answerid() ?>/" method="POST">
								<input type="submit" name="delete" value="delete">
							</form>
						<?php }  ?>			
					<?php }  ?>		
					<p>___________________________________________________________</p>
			<?php } ?>
		</div>
		<?php if($user){ ?>
			<div class = "Repondre">
				<form id="repondre_form" action="post/show/<?= $question->get_postid() ?>" method="post">
					Ta réponse
					<textarea id="answer" name="answer" rows='3'></textarea><br>
					<input id="post" type="submit" value="Repond">
				</form>
			</div>
		<?php }    ?>
		<a href="post/index">Back</a>
    </body>
</html>