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
		<?php include('menu.html'); ?>
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