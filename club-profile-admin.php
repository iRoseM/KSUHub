<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] != "clubAdmin") {
    header("Location: login.html");
    exit();
}
?>