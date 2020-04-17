<!DOCTYPE html>
<html>

<head>
    <title>MyFaceSpace: Find Friends</title>
    <link rel="stylesheet" href="myFaceSpace.css">
</head>
<body>
    <header class="wrapper">
        <nav>
            <a href="index.php">Home</a>
            <a href="add_friends.php">Find Friends</a>
            <a href="edit_account.php">Edit Account</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

<?php
    session_start();
    require("nicetime.php");
    require("database.php");
    $mysqli = ConnectToDatabase();
   
    if (!isset($_SESSION['username'])) {
        // Redirect back to login screen
        header("Location: login.php");
    }else{
        $username = $_SESSION['username'];
        
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
?>
            <form method='POST' action='add_friends.php'>
                <input type="text" name="search" placeholder="Search for friends">
                <button type="submit">Search</button>
            </form>
<?php
        }else{
                //get what the user is searching and prevent sql injection
            $searchFor = trim($mysqli->real_escape_string($_POST['search']));
            //create and run sql to find friends
            $sql = "SELECT * FROM account
                    WHERE name LIKE '%$searchFor%'
                    ORDER BY name;";
            $isFriend = "SELECT username1 
                        FROM friend 
                        WHERE username2 = '$username' UNION
                        SELECT username2 
                        FROM friend 
                        WHERE username1 = '$username';";
            $friendsList = $mysqli->query($isFriend);
            
            while($friend = $friendsList->fetch_assoc()){
                $friendname = $friend['username1'];
                $friends["$friendname"] = "1";
            }
            $results = $mysqli->query($sql);
            //display form again
?>
            <form method='POST' action='add_friends.php'>
                <input type="text" name="search" placeholder="Search for friends">
                <button type="submit">Search</button>
            </form>
<?php
            //loop through and display people to add
            while($row = $results->fetch_assoc()){
                $friendusername = $row['username'];
                $name = $row['name'];
                $status = $row['status'];
                $updateTime = NiceTime($row['updatetime']);
?>
<?php
                if($friendusername != $username){
?>
                <table width =100%>
                    <tr>
                        <td width=25%><img src="images/<?=$friendusername?>_thumb.jpg"></td>
                        <td width=25%><?=$name?><br><?=$updateTime?></td>
                        <td width=25%><?=$status?></td>
<?php
                        if(!isset($friends["$friendusername"])){
?>
                        <td width=25%><a href="add_friend_db.php?user=<?=$friendusername?>">Add as Friend</a></td>
<?php
                        }
?>
                    </tr>
                </table>

<?php            }
            }
        }
    }
?>
</body>

</html>
