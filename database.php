<?php

// Connect to the database and return the MySQLi object
function ConnectToDatabase()
{ 

	
	$mysqli = new mysqli("localhost", $db_username, $db_password, $db_database);
 
	
	if ($mysqli->connect_errno)
		die("Failed to connect to MySQL: ($mysqli->connect_errno) $mysqli->connect_error");
		
	return $mysqli;
}

?>	
