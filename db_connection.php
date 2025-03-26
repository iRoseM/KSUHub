<?php

error_reporting(E_ALL); 
ini_set('log_errors','1'); 
ini_set('display_errors','1'); 

// Database connection file

$servername = "localhost"; 
$username = "root";        
$password = "root";            // Your MySQL password (leave empty for default XAMPP)
$database = "ksuhubdb";   

// Create connection
$conn = new mysqli($servername, $username, $password, $database, 8889);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
