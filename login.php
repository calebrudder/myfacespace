<!DOCTYPE html>
<html>

<head>
    <title>MyFaceSpace</title>
    <link rel="stylesheet" href="myFaceSpace.css">
</head>

<body>

<?php 
    require("database.php");
    $mysqli = ConnectToDatabase();
    session_start();

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        
        if (isset($_SESSION['message'])){
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
?>

        <form method="POST" action="login.php">
        <div>
            Username: <input type="text" name="username" autofocus>
        </div>
        <div>
            Password: <input type="password" name="password">
        </div>
        <input type="submit" value="Login">
        </form>
    
        <p>If you don't have an account, you can 
            <a href="create_account.php">Create one</a>for free!</p>

<?php 
    }
    else { 
        // POST - authentication
        echo "<p>$_POST[username] - $_POST[password]</p>";
        
        //deter sql injection
        $username = trim($mysqli->real_escape_string($_POST['username']));
        $password = trim($mysqli->real_escape_string($_POST['password']));
        // SELECT the password from the database based on the submitted username.
        $sql = "select password from account where username= '$username'";
        $results = $mysqli->query($sql);

        $submittedPassHash = password_hash($password, PASSWORD_BCRYPT);
        
        while($row = $results->fetch_assoc()) {
        $passHash = $row['password'];
        }

    if (password_verify($_POST['password'], $passHash)) {
        echo "<p>Correct</p>";
        
        $_SESSION["login"] = true;
        $_SESSION["username"] = trim($mysqli->real_escape_string($_POST['username']));
        
        // Redirect
        header("Location: index.php");
    }
        else {
            $_SESSION['message'] = 'Incorrect username or password. Please try again.';
            header("Location: login.php");
    }
}
    
?>
</body>

</html>
