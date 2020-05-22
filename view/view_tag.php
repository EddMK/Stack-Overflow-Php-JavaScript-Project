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
		<div class="mainTag">
			<table>
			  <thead>
				<tr>
				  <th>Tag name</th>
				  <?php if($user && $user->role=="admin"){ ?>
					<th style="width:50%">Actions</th>
				  <?php } ?>
				</tr>
			  </thead>
			  <tbody>
				<?php foreach($tags as $tag){ ?>
					<tr>
						<td><?= $tag->tagName ?>  (<?= $tag->get_numbers_of_posts()?> posts)</td>
						<?php if($user && $user->role=="admin"){ ?>
							<td>
								<form action="tag/edit/<?= $tag->get_tagId() ?>" method="post">
									<input type="text" id="edit" name="edit" value="<?= $tag->tagName ?>" >
									<button type="submit"><i class="fas fa-edit"></i></button>
								</form>
								<a href="tag/confirm_delete/<?= $tag->get_tagId() ?>"><i class="fas fa-trash-alt"></i></a>
							</td>
						<?php  }  ?>
					</tr>
				<?php } ?>
			  </tbody>
			</table>
			<?php if($user == true && $user->is_admin()==true){ ?>
				<form action="tag/add" method="post" >
					<input type="text" id="add" name="add">
					<button type="submit"><i class="fas fa-plus-circle"></i></button>
				</form>
			<?php } ?>
			<?php if (count($errors) != 0){ ?>
                <div class='errors'>
                    <p>Errors :</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
			<?php } ?>
		</div>
    </body>
</html>