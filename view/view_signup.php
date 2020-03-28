<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Sign Up</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="header">
			<a href="post/index" class="logo">Stuck Overflow</a>
			<div class = "headerRight">
				<a href="user/login"><i class="fas fa-sign-in-alt"  style="font-size:40px;color:black"></i></a>
				<a href="user/signup"><i class="fas fa-user-plus"  style="font-size:40px;color:black;padding-left:20px"></i></a>
			</div>
        </div>
        <div class="mainlogin">
            <h1 class="title">Sign up</h1>
            <form id="signupForm" action="user/signup" method="post">
                <table>
					<tr>
                        <td>Username:</td>
                        <td><input id="userName" name="userName" type="text" size="16" value="<?= $userName ?>"></td>
                        <td class="errors" id="errPseudo"></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input id="password" name="password" type="password" size="16" value="<?= $password ?>"></td>
                        <td class="errors" id="errPassword"></td>
                    </tr>
                    <tr>
                        <td>Confirm Password:</td>
                        <td><input id="password_confirm" name="password_confirm" size="16" type="password" value="<?= $password_confirm ?>"></td>
                        <td class="errors" id="errPasswordConfirm"></td>
                    </tr>
					<tr>
                        <td>Full Name:</td>
                        <td><input id="fullName" name="fullName" type="text" value="<?= $fullName ?>"></td>
                        <td class="errors" id="errFullName"></td>
                    </tr>
					<tr>
                        <td>Email:</td>
                        <td><input id="email" name="email" size="16" type="email" value="<?= $email ?>"></td>
                        <td class="errors" id="errEmail"></td>
                    </tr>
					
                </table>
                <input id="btn" type="submit" value="Sign Up">
            </form>
            <?php if (count($errors) != 0): ?>
                <div class='errors'>
                    <br><br><p>Please correct the following error(s) :</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>