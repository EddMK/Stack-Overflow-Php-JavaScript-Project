<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
        <title>Error</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
		<script src="https://kit.fontawesome.com/9f16cf7640.js" crossorigin="anonymous"></script>
	</head>
	<body>
		<?php include('menu.html'); ?>
		<div class = "mainDelete">
			<i class="fas fa-times"  style="font-size:70px" ></i>
            <h1>Error</h1>    
			<p><?= $error ?></p>
        </div>
	</body>
</html>