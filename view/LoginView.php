<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Login View</title>
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
                    <td>Username:</td>
                    <td><input id="username" type="text" name="username" value="<?php echo $username; ?>"></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input id="password" type="password" name="password" value="<?php echo $password; ?>"></td>
                </tr>
            </table>
            <input type="submit" value="Log In">
        </form>
        <?php
        ?>
    </div>
</body>

</html>