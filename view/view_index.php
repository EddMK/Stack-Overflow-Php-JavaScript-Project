<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Stuck Overflow</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="menu">
			<?php if(empty($user)){    ?>
				<a href="user/login">Log In</a>
				<a href="user/signup">Sign Up</a>		
			<?php } else{    ?>
				<p><?= $user->userName ?></p>
				<a href="user/logout">Log Out</a>
				<a href="post/ask">Ask a question</a>
			<?php }    ?>
        </div>
		<div class="menu_questions">
			 <ul class="ul_menu_questions">
				  <li><a class="current" href="post/index/newest">Newest</a></li>
				  <li><a href="post/index/votes">Votes</a></li>
				  <li><a href="post/index/unanswered">Unanswered</a></li>
			</ul> 
		</div>
        <div class="Questions">
			<p>Questions :</p>
			<ul>
				<?php foreach ($posts as $post){ ?>				
						<li><a href="post/show/<?= $post->get_postid()?>"> <?= $post->title?> </a></li>
				<?php } ?>
			</ul>
        </div>
    </body>
</html>