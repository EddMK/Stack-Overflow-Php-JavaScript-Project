<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
		<script src="https://kit.fontawesome.com/9f16cf7640.js" crossorigin="anonymous"></script>
    </head>
    <body>
		<?php include('menu.html'); ?>
        <div class="mainEdit">			
            <form id="message_form" action="comment/edit/<?= $id ?>" method="post">
				Body
				<textarea id="body" name="body" rows='3'> <?= $body ?></textarea><br>
				<input type="submit" name="modifier" value="modifier">
            </form>          
        </div>
		<?php if (count($errors) != 0){ ?>
                <div class="errorsEdit">
                    <p>Errors :</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
		<?php } ?>
    </body>
</html>