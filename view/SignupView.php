<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Signout View</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="menu">
        <!-- Mettre autres fichiers pour menu -->
    </div>
    <div class="main">
        <form method="POST" action="LoginView.php">
            <table>
                <tr>
                    <td><label>Username:</label></td>
                    <td><input id="username" type="text" name="username" value="<?php echo $username; ?>"></td>
                </tr>
                <tr>
                    <td><label>Password:</label></td>
                    <td><input id="password" type="password" name="password" value="<?php echo $password; ?>"></td>
                </tr>
                <tr>
                    <td><label>Confirm Password:</label></td>
                    <td><input id="password" type="password" name="password" value="<?php echo $password; ?>"></td>
                </tr>
                <tr>
                    <td><label>Fullname:</label></td>
                    <td><input id="fullname" type="text" name="fullname" value="<?php echo $fullname; ?>"></td>
                </tr>
                <tr>
                    <td><label>Email:</label></td>
                    <td><input id="email" type="text" name="email" value="<?php echo $email; ?>"></td>
                </tr>
            </table>
            <input type="submit" value="Log In">
        </form>
        <?php
        ?>
    </div>
</body>
</html>