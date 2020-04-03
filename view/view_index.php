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
		<div class="main">
			<div class="menu" >
				<div class="sort_questions">
					 <ul class="ul_menu_questions">
						  <li><a class="<?php if($menu == "newest"){ echo "current" ; }  ?>" href="post/index/newest">Newest</a></li>
						  <li><a class="<?php  if($menu == "votes"){ echo "current"; } ?>"  href="post/index/votes">Votes</a></li>
						  <li><a class="<?php  if($menu == "unanswered"){ echo "current"; }  ?>"  href="post/index/unanswered">Unanswered</a></li>
					</ul> 
				</div>
				<div class="search">
					<form action="post/index" method="post" class="search">
						<input type="text" id="search" name="search" class="search" placeholder="Search...">
					</form>
				</div>
			</div>
			<div class="Questions">
				<ul class ="questions">
					<?php foreach ($posts as $post){ ?>	
							<?php
								$title = $post->title;
								$body = $post->body;
								$body = preg_replace("/($search)/i","<b>$1</b>",$body);
								$title = preg_replace("/($search)/i","<b>$1</b>",$title);
							?>
							<li>
								<p><a href="post/show/<?= $post->get_postid()?>"  > <?= $title ?> </a> </p>
								<p><?= $body ?> </p>
								<p>
									Asked <?= $post->get_ago()?> by <?= $post->get_author_by_authorId()->fullName ?>
									(<?= $post->get_score()?> vote(s),<?= $post->number_of_answers() ?> answer(s))
								</p>
							</li>
					<?php } ?>
				</ul>
			</div>
		</div>
    </body>
</html>