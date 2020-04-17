<!DOCTYPE html>
<html>

<head>
    <title>MyFaceSpace</title>
    <link rel="stylesheet" href="myFaceSpace.css">
</head>
<body>
    <?php 
        require("nicetime.php");
        require("database.php");
        $mysqli = ConnectToDatabase();
        //set current time zone
        date_default_timezone_set('America/Chicago');
    
        session_start();
        // Verify that the person has authenticated
        if (!isset($_SESSION['username'])) {
            // Redirect back to login screen
            header("Location: login.php");
        }else{
    ?>

    <header class="wrapper">
        <nav>
            <a href="index.php">Home</a>
            <a href="add_friends.php">Find Friends</a>
            <a href="edit_account.php">Edit Account</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    
    <?php
            //select the username
            $username = trim($mysqli->real_escape_string($_SESSION['username']));
            //create a query for the user's information
            $sql = "SELECT name, status, updatetime FROM account WHERE username= '$username'";
            $results = $mysqli->query($sql);
            //create query for friends of user
            $friendsql = "SELECT a.username, a.name, a.status, a.updatetime FROM account a, friend f
                            WHERE (f.username1='$username' AND f.username2 = a.username) OR
                                    (f.username2='$username' AND f.username1 = a.username) 
                                        ORDER BY updatetime DESC;";
            $friendstatus = $mysqli->query($friendsql);
            
            //loop through and assign user info to variables
            while($row = $results->fetch_assoc()) {
                $name = $row['name'];
                $status = $row['status'];
                $updatetime = $row['updatetime'];
            }
      
            //display the user's information at the top of the page
    ?>
    <div class="userProfile">
            <img src="images/<?=$username?>.jpg" alt="<?=$username?>'s profile picture" class="profilePic">
        <div class="userInfo">
            
            <h1><?=$name?></h1>
            <h2><?=$status?></h2>
            <p class="statusTime"><?=NiceTime($updatetime)?></p>
            <form method="post" action="set_status.php">
                <input type="text" name="status" placeholder="Update status" id="status">
                <button type="submit" id="shareBtn">Share</button>
            </form>
            
        </div>
    </div>
    
    <!-Display the statuses of all users friends-->
    
    <div class="friendStatuses">
        <hr>
    <?php
        if($friendstatus->num_rows === 0) {
            echo "You don't have any friends! <a href='add_friends.php'>Add some!</a>";
        }else{
        //loop through and display friend's statuses
          while($status = $friendstatus->fetch_assoc()) {
                $friendUsername = $status['username'];
                $friendName = $status['name'];
                $friendStatus = $status['status'];
                $friendUpdatetime = NiceTime($status['updatetime']);
    ?>
                <table width=100%>
                    <tr>
                        <td width=25%><img class="thumbPic" src="images/<?=$friendUsername?>_thumb.jpg"
                                 alt="<?=$friendUsername?>'s profile image"></td>
                        <td width=25%><?=$friendName?><br><?=$friendUpdatetime?></td>
                        <td width=25%><?=$friendStatus?></td>
                        <td width=25%><a id="delete" href="remove_friend_db.php?user=<?=$friendUsername?>"
                            onclick="return confirm('Are you sure?')">Remove Friend</a></td>
                    </tr>                                                              
                </table>
                 <hr>
    <?php
            }
    ?>
    </div>
    
    <?php  
        }
    }
    ?>

</body>

</html>
