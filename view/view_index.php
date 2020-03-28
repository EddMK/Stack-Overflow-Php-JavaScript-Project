<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Stuck Overflow</title>
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
		<div id="main">
			<div class="menu" >
				<div class="sort_questions">
					 <ul class="ul_menu_questions">
						  <li><a class="current" href="post/index/newest">Newest</a></li>
						  <li><a href="post/index/votes">Votes</a></li>
						  <li><a href="post/index/unanswered">Unanswered</a></li>
					</ul> 
				</div>
				<div class="search">
					<form action="#">
						<input type="text" id="search" name="search" class="Search" placeholder="Search...">
					</form>
				</div>
			</div>
			<div class="Questions">
				<ul class ="questions">
					<?php foreach ($posts as $post){ ?>				
							<li>
								<p><a href="post/show/<?= $post->get_postid()?>"  > <?= $post->title?> </a> </p>
								<p><?= $post->body?> </p>
								<p>Asked <?= $post->get_ago()?> by </p>
							</li>
					<?php } ?>
				</ul>
			</div>
		</div>
    </body>
</html>