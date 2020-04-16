<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Ask a public question</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
		<script src="https://kit.fontawesome.com/9f16cf7640.js" crossorigin="anonymous"></script>
    </head>
    <body>
		<div class="header">
			<a href="post/index" class="logo">Stuck Overflow</a>
			<div class = "headerRight">
					<div class="textHeader">
						<a href="post/ask"  style="text-decoration: none;color:black">Ask a question</a>
						<i class="fas fa-user"  style="font-size:40px;color:black;padding-left:20px"></i><?= $user->userName ?>
						<a href="user/logout"><i class="fas fa-sign-out-alt" style="font-size:40px;color:black;padding-left:20px"></i></a>
					</div>
			</div>
		</div>
		<div class = "Post">
				<?php if($post->is_question()){ ?>
					<h1 class="titleQuestion"><?= $post->title ?></h1>
				<?php }?>
				<p class="body"><?= $post->body ?></p>
		</div>
		<div class = "Repondre">
			<form id="add_form" action="comment/add/<?= $postid ?>" method="post">
				Ajouter un commentaire
				<textarea id="add" name="add" rows='3'></textarea><br>
				<input id="post" type="submit" value="Add">
			</form>
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
</html>