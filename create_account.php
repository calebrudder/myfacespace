<?php session_start(); ?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>MyFaceSpace: Create Account</title>	
	<link rel="stylesheet" href="myFaceSpace.css">
	<style>
		.field {
			display: inline-block;
			width: 100px;			
			margin-bottom: 7px;
		}		
	</style>
</head>
<body>

<?php

// Replace YOUR_USERNAME with your username!
$images_upload_dir_full = "/home/crudder/public_html/myfacespace/images";
$images_upload_url = "/~crudder/myfacespace/images";

// See if the form should be displayed
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
?>
  <h2>Create Account</h2>

  <form action="create_account.php" method="POST" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="MAX_FILE_SIZE" value="5000000">

    <label for="name">Name</label><input type="text" id="name" name="name" required autofocus ><br>
	<label for="username">Username</span><input type="text" id="username" name="username" required><br>
	<label for="password">Password</span><input type="password" id="password" name="password" required><br>
	<label for="imgfile">Profile Images</span><input type="file" id="imgfile" name="imgfile" required><br>
  <br>
  <input type="submit" value="Create Account">
  </form>

</body>
</html>
<?php
}
else   // POST
{
	// To use functions that upload and resize images
	require("image_util.php");   
	
	// For connecting to database
	require("database.php");	

    // Connect and select database
	$mysqli = ConnectToDatabase();

    // Get submitted data and verify it wasn't left blank
    $name = trim($mysqli->real_escape_string($_POST['name']));
	$username = trim($mysqli->real_escape_string($_POST['username']));
	$password = trim($mysqli->real_escape_string($_POST['password']));

    if ($name == '')
		ShowError("Please go back and enter your name.");

    // Don't allow short usernames or usernames with non-alphanumeric characters
	if (strlen($username) < 3 || !ctype_alnum($username))
        ShowError("Please go back and enter a username that is at least 3 characters and only 
			composed of letters and numbers.");

    if ($password == '')
        ShowError("Please go back and enter a password.");

    // This is the directory the uploaded images will be placed in.
    // It must have privileges sufficient for the web server to write to it	
	if (!is_writeable($images_upload_dir_full))
		  ShowError("The directory $images_upload_dir_full is not writeable.\n");

	$image_filename = "$images_upload_dir_full/$username.jpg";

	// Save the uploaded image to the given filename
	$error_msg = UploadSingleImage($image_filename);
	if ($error_msg != "")
		ShowError($error_msg);

	// Save uploaded image with a maximum width or height of 300 pixels
	CreateThumbnailImage($image_filename, $image_filename, 300);

	// Create a small thumbnail of the image to be used later
	$image_thumbnail = $username . "_thumb.jpg";
	CreateThumbnailImage($image_filename, "$images_upload_dir_full/$image_thumbnail", 60);

	// Get the hash of the password for inserting into the database
	$password_hash = password_hash($password, PASSWORD_BCRYPT);

	// Insert record into the database
	$sql = "INSERT INTO account VALUES ('$username', '$password_hash', '$name', '', NULL)";
	if ($mysqli->query($sql))
	{
?>
                <nav>
                    <a href="index.php">Home</a>
                    <a href="add_friends.php">Find Friends</a>
                    <a href="logout.php">Logout</a>
                </nav>
<?php
		print "<h1>Account Created</h1>\n" .
		  "<p><img src='$images_upload_url/$username.jpg' style='float:left; " .
		  "margin: 0pt 10pt 10px 10px;'>" .
		  "<h2>$name</h2></p>\n".
		  "<p><a href='edit_account.php'>Edit Account</a></p>\n";

		// Set session variable for use in other pages
		$_SESSION['username'] = $username;
        
	}
	elseif ($mysqli->errno == 1062)
		ShowError("Sorry, but the username <b>$username</b> already exists. Please go back and 
			choose another.");
	else
		ShowError("Error ($mysqli->errno) $mysqli->error<br>SQL = $sql\n");	
		
}  // end POST


function ShowError($error)
{
?>
  <h1>Unable to create account</h1>
  <p><?= $error ?></p>
      <p><a href="javascript:history.back()">Go back</a></p>
</body>
</html>
<?php
  exit;
}
?>