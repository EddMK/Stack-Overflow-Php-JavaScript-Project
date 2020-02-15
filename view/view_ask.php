<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="main">
            <form id="message_form" action="post/ask" method="post">
                Title
				<input id="title" name="title" type="text"><br>
				Body
				<textarea id="body" name="body" rows='3'></textarea><br>
				<input id="post" type="submit" value="Post">
            </form>          
        </div>
		<a href="post/index">Back</a>
		<div class='errors'>
			<?php if ($errors==""){ ?>               
					<p>Well added</p>         
			<?php }else{ ?>
					<?php echo $errors ?> 			
			<?php }?>
		</div>
    </body>
</html>