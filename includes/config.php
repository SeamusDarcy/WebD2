<?php
$host = 'localhost';     
$user = 'root';          
$pass = '';              
$db   = 'library';          

// Connects to the db using mysqli build into PHP
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

//echo "Connected successfully testing";
?>
