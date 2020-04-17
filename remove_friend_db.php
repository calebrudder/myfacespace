<!DOCTYPE html>
<html>
<head>
    <title>MyFaceSpace: Remove Friends</title>
</head>
<body>
<?php
    session_start();
    require("database.php");
    $mysqli = ConnectToDatabase();
    
    if (!isset($_SESSION['username'])) {
            // Redirect back to login screen
            header("Location: login.php");
    }else{
        $username = $_SESSION['username'];
        $friend = $_GET['user'];
        $sql= "DELETE FROM friend 
                WHERE (username1='$username' AND username2='$friend') OR
                        (username1='$friend' AND username2='$username');";
        $mysqli->query($sql);
        header("Location: index.php");
    }
?>    
</body>
</html>