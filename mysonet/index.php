<!DOCTYPE html>
<html>
<head>
    <title>Login - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">MySoNet.Online</div>
    <div class="container">
        <h1>Login</h1>

        <form class="login-form" method="POST" action="login.php">
            <label for="pseudo">Pseudo:</label><br>
            <input type="text" id="pseudo" name="pseudo"><br>
            <label for="pwd">Mot de passe:</label><br>
            <input type="password" id="pwd" name="pwd"><br>
            <input type="submit" name="submit" value="Se connecter">
        </form>
    </div>
</body>
</html>