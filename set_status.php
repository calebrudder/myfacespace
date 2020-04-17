<!DOCTYPE html>
<html>

<head>
    <title>MyFaceSpace: Set Status</title>
</head>

<body>
    <?php
    session_start();
    require("database.php");
    $mysqli = ConnectToDatabase();
    //check for authentecation 
    if(!isset($_SESSION["username"])){
         header("Location: login.php");
    }else{
        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            $username = $_SESSION['username'];
            $status = trim($mysqli->real_escape_string($_POST['status']));
            //make sure the user has typed more than just a space or just hit submit
            if(strlen($status) > 0){
                //sql to update the user's status
                $sql = "UPDATE account SET status='$status',
                    updatetime=NOW() WHERE username='$username';";
                $mysqli->query($sql);
                header("Location: index.php");
            }
            else{
                 header("Location: index.php");
            }
        }else{
            header("Location: index.php");
        }
    }
?>
</body>

</html>
