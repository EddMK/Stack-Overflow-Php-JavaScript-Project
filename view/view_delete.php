<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Delete</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="main">
            <h1>Etes vous sur ?<h1>
			<p>Voulez vous vraiment supprimer cette publication ?<p>
			<p>Ce processus est irréversible.</p>
			<form id="message_form" action="post/confirm_delete/<?= $postid ?>" method="post">
				<input type="submit" name="annuler" value="annuler">
				<input type="submit" name="supprimer" value="supprimer">
            </form>          
        </div>
    </body>
</html>