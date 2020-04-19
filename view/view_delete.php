<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Delete</title>
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
        <div class = "mainDelete">
			<i class="fas fa-exclamation-triangle"  style="font-size:70px" ></i>
            <h1>Etes vous sur ?<h1>
			<p>Voulez vous vraiment supprimer?<p>
			<p>Ce processus est irréversible.</p>
			<?php  if($controller == 1){  ?>
				<form id="delete_form" action="post/confirm_delete/<?= $id ?>" method="post">
			<?php }elseif($controller == 2){?>
				<form id="delete_form" action="comment/confirm_delete/<?= $id ?>" method="post">
			<?php }else{ ?>
				<form id="delete_form" action="tag/confirm_delete/<?= $id ?>" method="post">
			<?php } ?>
				<input type="submit" name="annuler" value="annuler">
				<input type="submit" name="supprimer" value="supprimer">
            </form>          
        </div>
    </body>
</html>