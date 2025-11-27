<?php

$host = 'localhost';
$db   = 'candidate_portal';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
   // echo "Successful";
}

?>
