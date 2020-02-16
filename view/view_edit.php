<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="main">
            <form id="message_form" action="post/edit/<?= $postid ?>" method="post">
                <?php if($title !== "" && $title !== NULL){    ?>
					Title
					<input id="title" name="title" type="text" value="<?= $title ?>"><br>
				<?php } ?>
				Body
				<textarea id="body" name="body" rows='3'><?= $body ?> </textarea><br>
				<input type="submit" name="modifier" value="modifier">
            </form>          
        </div>
    </body>
</html>