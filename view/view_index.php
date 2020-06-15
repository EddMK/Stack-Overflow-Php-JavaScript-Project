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
		<?php include('menu.html'); ?>
		<div class="main">
			<div class="menu" >
				<div class="sort_questions">
					 <ul class="ul_menu_questions">
						  <li><a class="<?php if($menu == "newest"){ echo "current" ; }  ?>" href="post/index/newest">Newest</a></li>
						  <li><a class="<?php  if($menu == "votes"){ echo "current"; } ?>"  href="post/index/votes">Votes</a></li>
						  <li><a class="<?php  if($menu == "unanswered"){ echo "current"; }  ?>"  href="post/index/unanswered">Unanswered</a></li>
						  <li><a class="<?php  if($menu == "active"){ echo "current"; }  ?>"  href="post/index/active">Active</a></li>

						  <?php if($menu == "tag"){ ?>
							<li><a class="current">Questions tagged[<?= $tag->tagName ?>]</li>
						  <?php }?>
					</ul> 
				</div>
				<div class="search">
					<form action="post/index" method="post" class="search">
						<input type="text" id="search" name="search" value="<?= $search ?>"  placeholder="Search...">
						<button type="submit" ><i class="fas fa-search" ></i></button>
					</form>
				</div>
			</div>
			<div class="Questions">
				<ul class ="questions">
					<?php foreach ($posts as $post){ ?>	
							<li>
								<p><a href="post/show/<?= $post->get_postid()?>"  > <?= $post->title ?> </a> </p>
								<p>
									<?php 
										$Parsedown = new Parsedown();
										echo $Parsedown->text($post->body);
									?>
								</p>
								<p><b>
									Asked <?= $post->get_ago()?> by <?= $post->get_author_by_authorId()->fullName ?>
									(<?= $post->get_score()?> vote(s),<?= $post->number_of_answers() ?> answer(s))
									<?php foreach ($post->get_tags() as $tag){ ?>
										<a href="post/posts/tag/1/<?= $tag->get_tagId()?>" ><?= $tag->tagName?></a>
									<?php } ?>
								</b></p>
							</li>
					<?php } ?>
				</ul>
			</div>
			<div class="pagination">
				  <?php for($i = 1; $i<=$totalPages;$i ++){ ?>
					  <?php if($menu == "search"){?>
						<?php $_POST['search']=$search ?>
						<a href="post/index/<?= $menu ?>/<?= $i ?>/<?= $search ?>"    class="<?php if($currentPage == $i){ echo "current" ; }  ?>"       ><?= $i ?></a>
					  <?php }elseif($menu == "tag"){?>
						<a href="post/posts/tag/<?= $i ?>/<?= $tag->get_tagId()?>"    class="<?php if($currentPage == $i){ echo "current" ; }  ?>"       ><?= $i ?> </a>
					  <?php }else{ ?>
						<a href="post/index/<?= $menu ?>/<?= $i ?>"    class="<?php if($currentPage == $i){ echo "current" ; }  ?>"    ><?= $i ?></a>
					  <?php } ?>
				  <?php } ?>
			</div>
		</div>
    </body>
</html>