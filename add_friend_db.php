<!DOCTYPE html>
<html>
<head>
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
        $sql="INSERT INTO friend VALUES ('$username', '$friend');";
        $mysqli->query($sql);
        header("Location: index.php");
    }
?>    
</body>
</html>